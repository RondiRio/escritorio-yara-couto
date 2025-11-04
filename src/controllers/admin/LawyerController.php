<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\Lawyer;
use Models\ActivityLog;

/**
 * LawyerController (Admin) - Gerencia advogados
 */
class LawyerController extends Controller
{
    private $lawyerModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->lawyerModel = new Lawyer();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Lista advogados
     */
    public function index()
    {
        $this->requireAuth();

        $lawyers = $this->lawyerModel->all('display_order ASC');

        $pageTitle = 'Gerenciar Advogados';

        $this->view('admin/lawyers/index', [
            'pageTitle' => $pageTitle,
            'lawyers' => $lawyers
        ]);
    }

    /**
     * Formulário criar advogado
     */
    public function create()
    {
        $this->requireAuth();

        $pageTitle = 'Cadastrar Advogado';

        $this->view('admin/lawyers/create', [
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * Salva advogado
     */
    public function store()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/advogados');
        }

        $data = [
            'name' => $this->post('name'),
            'oab_number' => $this->post('oab_number'),
            'oab_state' => $this->post('oab_state'),
            'bio' => $this->post('bio'),
            'specialties' => $this->post('specialties'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'whatsapp' => $this->post('whatsapp'),
            'cases_won' => $this->post('cases_won', 0),
            'status' => 'active',
            'display_order' => $this->post('display_order', 999)
        ];

        // Valida
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'oab_number' => 'required',
            'oab_state' => 'required',
            'bio' => 'required|min:50'
        ]);

        if (!empty($errors)) {
            set_old($data);
            flash('error', 'Preencha todos os campos obrigatórios');
            $this->redirect('admin/advogados/criar');
        }

        // Valida OAB
        if (!$this->lawyerModel->validateOAB($data['oab_number'], $data['oab_state'])) {
            set_old($data);
            flash('error', 'Número de OAB inválido. Verifique o formato.');
            $this->redirect('admin/advogados/criar');
        }

        // Verifica se OAB já existe
        if ($this->lawyerModel->oabExists($data['oab_number'], $data['oab_state'])) {
            set_old($data);
            flash('error', 'Este número de OAB já está cadastrado no sistema.');
            $this->redirect('admin/advogados/criar');
        }

        // Upload de foto
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $photoName = upload_file(
                $_FILES['photo'],
                __DIR__ . '/../../../public/images/advogados',
                ['jpg', 'jpeg', 'png', 'webp']
            );
            if ($photoName) {
                $data['photo'] = $photoName;
            }
        }

        // Sanitiza
        $data = $this->sanitize($data);

        // Cria
        $lawyerId = $this->lawyerModel->create($data);

        if ($lawyerId) {
            $this->activityLogModel->logCreate(
                'lawyer',
                $lawyerId,
                "Advogado cadastrado: {$data['name']} - OAB {$data['oab_number']}/{$data['oab_state']}"
            );

            flash('success', 'Advogado cadastrado com sucesso!');
            $this->redirect('admin/advogados');
        } else {
            flash('error', 'Erro ao cadastrar advogado');
            $this->redirect('admin/advogados/criar');
        }
    }

    /**
     * Formulário editar
     */
    public function edit($id)
    {
        $this->requireAuth();

        $lawyer = $this->lawyerModel->find($id);

        if (!$lawyer) {
            flash('error', 'Advogado não encontrado');
            $this->redirect('admin/advogados');
        }

        $pageTitle = 'Editar Advogado: ' . $lawyer['name'];

        $this->view('admin/lawyers/edit', [
            'pageTitle' => $pageTitle,
            'lawyer' => $lawyer
        ]);
    }

    /**
     * Atualiza advogado
     */
    public function update($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/advogados');
        }

        $lawyer = $this->lawyerModel->find($id);

        if (!$lawyer) {
            flash('error', 'Advogado não encontrado');
            $this->redirect('admin/advogados');
        }

        $data = [
            'name' => $this->post('name'),
            'oab_number' => $this->post('oab_number'),
            'oab_state' => $this->post('oab_state'),
            'bio' => $this->post('bio'),
            'specialties' => $this->post('specialties'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'whatsapp' => $this->post('whatsapp'),
            'cases_won' => $this->post('cases_won', 0),
            'display_order' => $this->post('display_order', 999)
        ];

        // Valida
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'oab_number' => 'required',
            'oab_state' => 'required',
            'bio' => 'required|min:50'
        ]);

        if (!empty($errors)) {
            flash('error', 'Preencha todos os campos obrigatórios');
            $this->redirect("admin/advogados/{$id}/editar");
        }

        // Valida OAB
        if (!$this->lawyerModel->validateOAB($data['oab_number'], $data['oab_state'])) {
            flash('error', 'Número de OAB inválido');
            $this->redirect("admin/advogados/{$id}/editar");
        }

        // Verifica OAB duplicado
        if ($this->lawyerModel->oabExists($data['oab_number'], $data['oab_state'], $id)) {
            flash('error', 'Este número de OAB já está cadastrado');
            $this->redirect("admin/advogados/{$id}/editar");
        }

        // Upload nova foto
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $photoName = upload_file(
                $_FILES['photo'],
                __DIR__ . '/../../../public/images/advogados',
                ['jpg', 'jpeg', 'png', 'webp']
            );
            if ($photoName) {
                $data['photo'] = $photoName;
                
                // Remove foto antiga
                if (!empty($lawyer['photo'])) {
                    $oldPhoto = __DIR__ . '/../../../public/images/advogados/' . $lawyer['photo'];
                    if (file_exists($oldPhoto)) {
                        unlink($oldPhoto);
                    }
                }
            }
        }

        // Sanitiza
        $data = $this->sanitize($data);

        // Atualiza
        $updated = $this->lawyerModel->update($id, $data);

        if ($updated !== false) {
            $this->activityLogModel->logUpdate(
                'lawyer',
                $id,
                "Advogado atualizado: {$data['name']}"
            );

            flash('success', 'Advogado atualizado com sucesso!');
            $this->redirect('admin/advogados');
        } else {
            flash('error', 'Erro ao atualizar advogado');
            $this->redirect("admin/advogados/{$id}/editar");
        }
    }

    /**
     * Visualiza advogado
     */
    public function show($id)
    {
        $this->requireAuth();

        $lawyer = $this->lawyerModel->find($id);

        if (!$lawyer) {
            flash('error', 'Advogado não encontrado');
            $this->redirect('admin/advogados');
        }

        $pageTitle = 'Visualizar: ' . $lawyer['name'];

        $this->view('admin/lawyers/show', [
            'pageTitle' => $pageTitle,
            'lawyer' => $lawyer
        ]);
    }

    /**
     * Deleta advogado
     */
    public function delete($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/advogados');
        }

        $lawyer = $this->lawyerModel->find($id);

        if (!$lawyer) {
            flash('error', 'Advogado não encontrado');
            $this->redirect('admin/advogados');
        }

        // Remove foto
        if (!empty($lawyer['photo'])) {
            $photoPath = __DIR__ . '/../../../public/images/advogados/' . $lawyer['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        $deleted = $this->lawyerModel->delete($id);

        if ($deleted) {
            $this->activityLogModel->logDelete(
                'lawyer',
                $id,
                "Advogado deletado: {$lawyer['name']}"
            );

            flash('success', 'Advogado deletado com sucesso!');
        } else {
            flash('error', 'Erro ao deletar advogado');
        }

        $this->redirect('admin/advogados');
    }

    /**
     * Altera status (ativo/inativo)
     */
    public function changeStatus($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método não permitido'], 405);
        }

        $status = $this->post('status');

        if (!in_array($status, ['active', 'inactive'])) {
            $this->json(['error' => 'Status inválido'], 400);
        }

        $updated = $this->lawyerModel->update($id, ['status' => $status]);

        if ($updated !== false) {
            $this->json([
                'success' => true,
                'message' => 'Status atualizado'
            ]);
        } else {
            $this->json(['error' => 'Erro ao atualizar'], 500);
        }
    }

    /**
     * Remove foto
     */
    public function removePhoto($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método não permitido'], 405);
        }

        $removed = $this->lawyerModel->removePhoto($id);

        if ($removed !== false) {
            $this->json([
                'success' => true,
                'message' => 'Foto removida'
            ]);
        } else {
            $this->json(['error' => 'Erro ao remover foto'], 500);
        }
    }

    /**
     * Valida OAB (AJAX)
     * Referência: https://cna.oab.org.br/
     */
    public function validateOAB()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método não permitido'], 405);
        }

        $oabNumber = $this->post('oab_number');
        $oabState = $this->post('oab_state');

        $isValid = $this->lawyerModel->validateOAB($oabNumber, $oabState);

        if ($isValid) {
            $exists = $this->lawyerModel->oabExists($oabNumber, $oabState);
            
            $this->json([
                'valid' => true,
                'exists' => $exists,
                'message' => $exists ? 'OAB já cadastrada' : 'OAB válida'
            ]);
        } else {
            $this->json([
                'valid' => false,
                'message' => 'Número de OAB inválido'
            ]);
        }
    }

    /**
     * Upload de foto
     */
    public function uploadPhoto()
    {
        $this->requireAuth();

        if (!isset($_FILES['photo'])) {
            $this->json(['error' => 'Nenhuma foto enviada'], 400);
        }

        $photoName = upload_file(
            $_FILES['photo'],
            __DIR__ . '/../../../public/images/advogados',
            ['jpg', 'jpeg', 'png', 'webp']
        );

        if ($photoName) {
            $this->json([
                'success' => true,
                'url' => asset('images/advogados/' . $photoName)
            ]);
        } else {
            $this->json(['error' => 'Erro ao fazer upload'], 500);
        }
    }

    /**
     * Reordena advogados
     */
    public function reorder()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método não permitido'], 405);
        }

        $order = $this->post('order', []);

        if (empty($order) || !is_array($order)) {
            $this->json(['error' => 'Ordem inválida'], 400);
        }

        foreach ($order as $index => $lawyerId) {
            $this->lawyerModel->updateDisplayOrder($lawyerId, $index + 1);
        }

        $this->json([
            'success' => true,
            'message' => 'Ordem atualizada'
        ]);
    }
}