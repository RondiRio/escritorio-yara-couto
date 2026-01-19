<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\User;
use Models\ActivityLog;

/**
 * UserController - Gerencia usuários administrativos
 */
class UserController extends Controller
{
    protected $middlewares = ['AuthMiddleware', ['RoleMiddleware', 'admin']];

    private $userModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Lista todos os usuários
     */
    public function index()
    {
        // Paginação
        $page = $this->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Filtros
        $search = $this->get('search', '');
        $role = $this->get('role', '');
        $status = $this->get('status', '');

        // Query base
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(name LIKE :search OR email LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($role)) {
            $where[] = "role = :role";
            $params['role'] = $role;
        }

        if (!empty($status)) {
            $where[] = "status = :status";
            $params['status'] = $status;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Busca usuários
        $sql = "SELECT id, name, email, role, status, last_login, created_at
                FROM users
                {$whereClause}
                ORDER BY created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $users = $this->userModel->query($sql, $params);

        // Total para paginação
        $countSql = "SELECT COUNT(*) as total FROM users {$whereClause}";
        $total = $this->userModel->query($countSql, $params)[0]['total'] ?? 0;
        $totalPages = ceil($total / $perPage);

        // Estatísticas
        $stats = [
            'total' => $this->userModel->count(),
            'admins' => $this->userModel->countByRole('admin'),
            'editors' => $this->userModel->countByRole('editor'),
            'authors' => $this->userModel->countByRole('author'),
            'active' => $this->userModel->where('status', 'active')->count(),
            'inactive' => $this->userModel->where('status', 'inactive')->count()
        ];

        $this->view('admin/users/index', [
            'pageTitle' => 'Gerenciar Usuários',
            'users' => $users,
            'stats' => $stats,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'search' => $search,
            'role_filter' => $role,
            'status_filter' => $status
        ]);
    }

    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        $this->view('admin/users/create', [
            'pageTitle' => 'Novo Usuário'
        ]);
    }

    /**
     * Salva novo usuário
     */
    public function store()
    {
        if (!$this->isPost()) {
            redirect('/admin/usuarios');
        }

        // Obtém dados
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'password' => $this->post('password'),
            'password_confirm' => $this->post('password_confirm'),
            'role' => $this->post('role', 'author'),
            'status' => $this->post('status', 'active')
        ];

        // Validações
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Validação de confirmação de senha
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'][] = 'As senhas não coincidem';
        }

        // Verifica se email já existe
        if ($this->userModel->findByEmail($data['email'])) {
            $errors['email'][] = 'Este e-mail já está em uso';
        }

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect('/admin/usuarios/novo');
        }

        // Remove confirmação
        unset($data['password_confirm']);

        // Cria usuário
        $userId = $this->userModel->createUser($data);

        if ($userId) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'created',
                'entity_type' => 'user',
                'entity_id' => $userId,
                'description' => "Usuário criado: {$data['name']} ({$data['email']})",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Usuário criado com sucesso!');
            redirect('/admin/usuarios');
        } else {
            flash('error', 'Erro ao criar usuário');
            redirect('/admin/usuarios/novo');
        }
    }

    /**
     * Exibe formulário de edição
     */
    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/usuarios');
        }

        // Não pode editar próprio usuário por aqui (usar perfil)
        if ($user['id'] == auth_id()) {
            flash('info', 'Para editar seu perfil, use a opção "Meu Perfil"');
            redirect('/admin/perfil');
        }

        $this->view('admin/users/edit', [
            'pageTitle' => 'Editar Usuário',
            'user' => $user
        ]);
    }

    /**
     * Atualiza usuário
     */
    public function update($id)
    {
        if (!$this->isPost()) {
            redirect('/admin/usuarios');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/usuarios');
        }

        // Não pode editar próprio usuário
        if ($user['id'] == auth_id()) {
            flash('error', 'Use "Meu Perfil" para editar seus dados');
            redirect('/admin/perfil');
        }

        // Obtém dados
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'role' => $this->post('role'),
            'status' => $this->post('status')
        ];

        // Validações
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email'
        ]);

        // Verifica se email já existe (exceto o próprio)
        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser && $existingUser['id'] != $id) {
            $errors['email'][] = 'Este e-mail já está em uso';
        }

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            redirect("/admin/usuarios/{$id}/editar");
        }

        // Atualiza
        if ($this->userModel->update($id, $data)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'updated',
                'entity_type' => 'user',
                'entity_id' => $id,
                'description' => "Usuário atualizado: {$data['name']}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Usuário atualizado com sucesso!');
        } else {
            flash('error', 'Erro ao atualizar usuário');
        }

        redirect('/admin/usuarios');
    }

    /**
     * Altera senha de um usuário
     */
    public function changePassword($id)
    {
        if (!$this->isPost()) {
            redirect('/admin/usuarios');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/usuarios');
        }

        $newPassword = $this->post('new_password');
        $confirmPassword = $this->post('confirm_password');

        // Validações
        if (empty($newPassword) || empty($confirmPassword)) {
            flash('error', 'Preencha todos os campos');
            redirect("/admin/usuarios/{$id}/editar");
        }

        if ($newPassword !== $confirmPassword) {
            flash('error', 'As senhas não coincidem');
            redirect("/admin/usuarios/{$id}/editar");
        }

        if (strlen($newPassword) < 6) {
            flash('error', 'A senha deve ter no mínimo 6 caracteres');
            redirect("/admin/usuarios/{$id}/editar");
        }

        // Altera senha
        if ($this->userModel->changePassword($id, $newPassword)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'password_changed',
                'entity_type' => 'user',
                'entity_id' => $id,
                'description' => "Senha alterada para o usuário: {$user['name']}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Senha alterada com sucesso!');
        } else {
            flash('error', 'Erro ao alterar senha');
        }

        redirect('/admin/usuarios');
    }

    /**
     * Altera status do usuário (ativo/inativo)
     */
    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Não pode desativar próprio usuário
        if ($user['id'] == auth_id()) {
            return $this->json(['error' => 'Você não pode desativar sua própria conta'], 403);
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';

        if ($this->userModel->update($id, ['status' => $newStatus])) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'status_changed',
                'entity_type' => 'user',
                'entity_id' => $id,
                'description' => "Status do usuário {$user['name']} alterado para: {$newStatus}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Status alterado com sucesso',
                'status' => $newStatus
            ]);
        }

        return $this->json(['error' => 'Erro ao alterar status'], 500);
    }

    /**
     * Deleta usuário
     */
    public function destroy($id)
    {
        if (!$this->isPost()) {
            redirect('/admin/usuarios');
        }

        $user = $this->userModel->find($id);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            redirect('/admin/usuarios');
        }

        // Não pode deletar próprio usuário
        if ($user['id'] == auth_id()) {
            flash('error', 'Você não pode deletar sua própria conta');
            redirect('/admin/usuarios');
        }

        if ($this->userModel->delete($id)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'deleted',
                'entity_type' => 'user',
                'entity_id' => $id,
                'description' => "Usuário deletado: {$user['name']} ({$user['email']})",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Usuário deletado com sucesso!');
        } else {
            flash('error', 'Erro ao deletar usuário');
        }

        redirect('/admin/usuarios');
    }
}
