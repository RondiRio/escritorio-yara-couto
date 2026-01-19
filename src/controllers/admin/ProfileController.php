<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\User;
use Models\ActivityLog;

/**
 * ProfileController - Gerencia perfil do usuário logado
 */
class ProfileController extends Controller
{
    protected $middlewares = ['AuthMiddleware'];

    private $userModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Exibe perfil do usuário
     */
    public function index()
    {
        $userId = auth_id();
        $user = $this->userModel->find($userId);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/dashboard');
        }

        // Busca atividades recentes do usuário
        $recentActivities = $this->activityLogModel->getByUser($userId, 10);

        $this->view('admin/profile/index', [
            'pageTitle' => 'Meu Perfil',
            'user' => $user,
            'recentActivities' => $recentActivities
        ]);
    }

    /**
     * Exibe formulário de edição de perfil
     */
    public function edit()
    {
        $userId = auth_id();
        $user = $this->userModel->find($userId);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/dashboard');
        }

        $this->view('admin/profile/edit', [
            'pageTitle' => 'Editar Perfil',
            'user' => $user
        ]);
    }

    /**
     * Atualiza dados do perfil
     */
    public function update()
    {
        if (!$this->isPost()) {
            redirect('/admin/perfil');
        }

        $userId = auth_id();
        $user = $this->userModel->find($userId);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/dashboard');
        }

        // Obtém dados
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email')
        ];

        // Validações
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email'
        ]);

        // Verifica se email já existe (exceto o próprio)
        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser && $existingUser['id'] != $userId) {
            $errors['email'][] = 'Este e-mail já está em uso';
        }

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            redirect('/admin/perfil/editar');
        }

        // Atualiza
        if ($this->userModel->update($userId, $data)) {
            // Atualiza sessão com novo nome e email
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];

            // Log
            $this->activityLogModel->create([
                'user_id' => $userId,
                'action' => 'profile_updated',
                'entity_type' => 'user',
                'entity_id' => $userId,
                'description' => 'Perfil atualizado',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Perfil atualizado com sucesso!');
            redirect('/admin/perfil');
        } else {
            flash('error', 'Erro ao atualizar perfil');
            redirect('/admin/perfil/editar');
        }
    }

    /**
     * Exibe formulário de alteração de senha
     */
    public function showChangePassword()
    {
        $userId = auth_id();
        $user = $this->userModel->find($userId);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/dashboard');
        }

        $this->view('admin/profile/change-password', [
            'pageTitle' => 'Alterar Senha',
            'user' => $user
        ]);
    }

    /**
     * Altera senha do usuário
     */
    public function changePassword()
    {
        if (!$this->isPost()) {
            redirect('/admin/perfil');
        }

        $userId = auth_id();
        $user = $this->userModel->find($userId);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/dashboard');
        }

        // Obtém dados
        $currentPassword = $this->post('current_password');
        $newPassword = $this->post('new_password');
        $confirmPassword = $this->post('confirm_password');

        // Validações
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            flash('error', 'Preencha todos os campos');
            redirect('/admin/perfil/alterar-senha');
        }

        // Verifica senha atual
        if (!password_verify($currentPassword, $user['password'])) {
            flash('error', 'Senha atual incorreta');
            redirect('/admin/perfil/alterar-senha');
        }

        // Verifica se nova senha é diferente da atual
        if ($currentPassword === $newPassword) {
            flash('error', 'A nova senha deve ser diferente da senha atual');
            redirect('/admin/perfil/alterar-senha');
        }

        // Verifica confirmação
        if ($newPassword !== $confirmPassword) {
            flash('error', 'As senhas não coincidem');
            redirect('/admin/perfil/alterar-senha');
        }

        // Valida tamanho
        if (strlen($newPassword) < 6) {
            flash('error', 'A senha deve ter no mínimo 6 caracteres');
            redirect('/admin/perfil/alterar-senha');
        }

        // Altera senha
        if ($this->userModel->changePassword($userId, $newPassword)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => $userId,
                'action' => 'password_changed',
                'entity_type' => 'user',
                'entity_id' => $userId,
                'description' => 'Senha alterada pelo próprio usuário',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Senha alterada com sucesso!');
            redirect('/admin/perfil');
        } else {
            flash('error', 'Erro ao alterar senha');
            redirect('/admin/perfil/alterar-senha');
        }
    }

    /**
     * Upload de avatar (foto de perfil)
     */
    public function uploadAvatar()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $userId = auth_id();

        // Verifica se arquivo foi enviado
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['error' => 'Nenhum arquivo enviado'], 400);
        }

        $file = $_FILES['avatar'];

        // Validações
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            return $this->json(['error' => 'Tipo de arquivo não permitido. Use JPG ou PNG'], 400);
        }

        if ($file['size'] > $maxSize) {
            return $this->json(['error' => 'Arquivo muito grande. Máximo: 2MB'], 400);
        }

        // Upload
        $uploadDir = __DIR__ . '/../../../public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Nome único
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        // Move arquivo
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Atualiza no banco
            $avatarUrl = '/public/uploads/avatars/' . $fileName;

            // Remove avatar antigo se existir
            $user = $this->userModel->find($userId);
            if (!empty($user['avatar']) && file_exists(__DIR__ . '/../../../' . $user['avatar'])) {
                @unlink(__DIR__ . '/../../../' . $user['avatar']);
            }

            // Atualiza
            $this->userModel->update($userId, ['avatar' => $avatarUrl]);

            // Log
            $this->activityLogModel->create([
                'user_id' => $userId,
                'action' => 'avatar_updated',
                'entity_type' => 'user',
                'entity_id' => $userId,
                'description' => 'Avatar atualizado',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Avatar atualizado com sucesso!',
                'avatar_url' => $avatarUrl
            ]);
        }

        return $this->json(['error' => 'Erro ao fazer upload do arquivo'], 500);
    }

    /**
     * Remove avatar
     */
    public function removeAvatar()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $userId = auth_id();
        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Remove arquivo se existir
        if (!empty($user['avatar']) && file_exists(__DIR__ . '/../../../' . $user['avatar'])) {
            @unlink(__DIR__ . '/../../../' . $user['avatar']);
        }

        // Atualiza no banco
        $this->userModel->update($userId, ['avatar' => null]);

        // Log
        $this->activityLogModel->create([
            'user_id' => $userId,
            'action' => 'avatar_removed',
            'entity_type' => 'user',
            'entity_id' => $userId,
            'description' => 'Avatar removido',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Avatar removido com sucesso!'
        ]);
    }
}
