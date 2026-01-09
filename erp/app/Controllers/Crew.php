<?php
/**
 * PT Indo Ocean - ERP System
 * Crew Controller - Manage crew members
 */

namespace App\Controllers;

require_once APPPATH . 'Models/CrewModel.php';
require_once APPPATH . 'Models/UserModel.php';

use App\Models\CrewModel;
use App\Models\CrewSkillModel;
use App\Models\CrewExperienceModel;
use App\Models\CrewDocumentModel;
use App\Models\DocumentTypeModel;
use App\Models\ActivityLogModel;

class Crew extends BaseController
{
    private $crewModel;
    private $activityModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->crewModel = new CrewModel($this->db);
        $this->activityModel = new ActivityLogModel($this->db);
    }
    
    /**
     * List all crews
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'view');
        
        $filters = [
            'search' => $this->input('search'),
            'status' => $this->input('status'),
            'rank_id' => $this->input('rank_id')
        ];
        
        $page = (int)($this->input('page') ?? 1);
        
        $data = [
            'title' => 'Crew Database',
            'crews' => $this->crewModel->getList($filters, $page, 20),
            'total' => $this->crewModel->countCrews($filters),
            'filters' => $filters,
            'page' => $page,
            'flash' => $this->getFlash()
        ];
        
        return $this->view('crews/index', $data);
    }
    
    /**
     * Show crew details
     */
    public function show($id)
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'view');
        
        $crew = $this->crewModel->getWithDetails($id);
        
        if (!$crew) {
            $this->setFlash('error', 'Crew tidak ditemukan');
            $this->redirect('crews');
            return;
        }
        
        $skillModel = new CrewSkillModel($this->db);
        $expModel = new CrewExperienceModel($this->db);
        $docModel = new CrewDocumentModel($this->db);
        
        $data = [
            'title' => $crew['full_name'],
            'crew' => $crew,
            'skills' => $skillModel->getByCrew($id),
            'experiences' => $expModel->getByCrew($id),
            'documents' => $docModel->getByCrew($id),
            'contractHistory' => $this->crewModel->getContractHistory($id),
            'flash' => $this->getFlash()
        ];
        
        return $this->view('crews/view', $data);
    }
    
    /**
     * Create crew form
     */
    public function create()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'create');
        
        $data = [
            'title' => 'Add New Crew',
            'crew' => null,
            'employeeId' => $this->crewModel->generateEmployeeId(),
            'csrf_token' => $this->generateCsrfToken(),
            'flash' => $this->getFlash()
        ];
        
        return $this->view('crews/form', $data);
    }
    
    /**
     * Store new crew
     */
    public function store()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'create');
        
        if (!$this->isPost()) {
            $this->redirect('crews');
            return;
        }
        
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $this->setFlash('error', 'Invalid security token');
            $this->redirect('crews/create');
            return;
        }
        
        $data = $this->getCrewData();
        $data['employee_id'] = $this->crewModel->generateEmployeeId();
        $data['created_by'] = $this->getCurrentUser()['id'];
        
        // Handle photo upload
        if (!empty($_FILES['photo']['name'])) {
            $photoPath = $this->uploadPhoto($_FILES['photo']);
            if ($photoPath) {
                $data['photo'] = $photoPath;
            }
        }
        
        $crewId = $this->crewModel->insert($data);
        
        if ($crewId) {
            $this->activityModel->log(
                $this->getCurrentUser()['id'],
                'create',
                'crew',
                $crewId,
                "Created crew: {$data['full_name']}"
            );
            
            $this->setFlash('success', 'Crew berhasil ditambahkan');
            $this->redirect('crews/' . $crewId);
        } else {
            $this->setFlash('error', 'Gagal menyimpan data crew');
            $this->redirect('crews/create');
        }
    }
    
    /**
     * Edit crew form
     */
    public function edit($id)
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'edit');
        
        $crew = $this->crewModel->find($id);
        
        if (!$crew) {
            $this->setFlash('error', 'Crew tidak ditemukan');
            $this->redirect('crews');
            return;
        }
        
        $data = [
            'title' => 'Edit Crew: ' . $crew['full_name'],
            'crew' => $crew,
            'employeeId' => $crew['employee_id'],
            'csrf_token' => $this->generateCsrfToken(),
            'flash' => $this->getFlash()
        ];
        
        return $this->view('crews/form', $data);
    }
    
    /**
     * Update crew
     */
    public function update($id)
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'edit');
        
        if (!$this->isPost()) {
            $this->redirect('crews');
            return;
        }
        
        $crew = $this->crewModel->find($id);
        
        if (!$crew) {
            $this->setFlash('error', 'Crew tidak ditemukan');
            $this->redirect('crews');
            return;
        }
        
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $this->setFlash('error', 'Invalid security token');
            $this->redirect('crews/edit/' . $id);
            return;
        }
        
        $data = $this->getCrewData();
        
        // Handle photo upload
        if (!empty($_FILES['photo']['name'])) {
            $photoPath = $this->uploadPhoto($_FILES['photo']);
            if ($photoPath) {
                $data['photo'] = $photoPath;
                // Delete old photo
                if ($crew['photo'] && file_exists($crew['photo'])) {
                    unlink($crew['photo']);
                }
            }
        }
        
        $this->crewModel->update($id, $data);
        
        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'update',
            'crew',
            $id,
            "Updated crew: {$data['full_name']}"
        );
        
        $this->setFlash('success', 'Crew berhasil diupdate');
        $this->redirect('crews/' . $id);
    }
    
    /**
     * Delete crew
     */
    public function delete($id)
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'delete');
        
        $crew = $this->crewModel->find($id);
        
        if (!$crew) {
            $this->setFlash('error', 'Crew tidak ditemukan');
            $this->redirect('crews');
            return;
        }
        
        $this->crewModel->delete($id);
        
        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'delete',
            'crew',
            $id,
            "Deleted crew: {$crew['full_name']}"
        );
        
        $this->setFlash('success', 'Crew berhasil dihapus');
        $this->redirect('crews');
    }
    
    /**
     * Skill Matrix view
     */
    public function skillMatrix()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'view');
        
        $skillModel = new CrewSkillModel($this->db);
        
        // Get all skills grouped
        $sql = "SELECT cs.skill_name, cs.skill_level, c.id as crew_id, c.full_name, c.status
                FROM crew_skills cs
                JOIN crews c ON cs.crew_id = c.id
                WHERE c.status IN ('available', 'onboard')
                ORDER BY cs.skill_name, c.full_name";
        
        $skills = $this->crewModel->query($sql);
        
        // Group by skill
        $skillMatrix = [];
        foreach ($skills as $skill) {
            $skillMatrix[$skill['skill_name']][] = $skill;
        }
        
        $data = [
            'title' => 'Skill Matrix',
            'skillMatrix' => $skillMatrix,
            'flash' => $this->getFlash()
        ];
        
        return $this->view('crews/skill_matrix', $data);
    }
    
    /**
     * Get crew data from POST
     */
    private function getCrewData()
    {
        return [
            'full_name' => trim($this->input('full_name')),
            'nickname' => trim($this->input('nickname')),
            'gender' => $this->input('gender'),
            'birth_date' => $this->input('birth_date') ?: null,
            'birth_place' => trim($this->input('birth_place')),
            'nationality' => trim($this->input('nationality')) ?: 'Indonesia',
            'religion' => trim($this->input('religion')),
            'marital_status' => $this->input('marital_status'),
            'email' => trim($this->input('email')),
            'phone' => trim($this->input('phone')),
            'whatsapp' => trim($this->input('whatsapp')),
            'address' => trim($this->input('address')),
            'city' => trim($this->input('city')),
            'province' => trim($this->input('province')),
            'postal_code' => trim($this->input('postal_code')),
            'emergency_name' => trim($this->input('emergency_name')),
            'emergency_relation' => trim($this->input('emergency_relation')),
            'emergency_phone' => trim($this->input('emergency_phone')),
            'bank_name' => trim($this->input('bank_name')),
            'bank_account' => trim($this->input('bank_account')),
            'bank_holder' => trim($this->input('bank_holder')),
            'current_rank_id' => $this->input('current_rank_id') ?: null,
            'years_experience' => (int)$this->input('years_experience'),
            'status' => $this->input('status') ?: 'available',
            'notes' => trim($this->input('notes'))
        ];
    }
    
    /**
     * Upload photo
     */
    private function uploadPhoto($file)
    {
        $uploadDir = 'uploads/crews/';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }
        
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            return null;
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'crew_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filepath;
        }
        
        return null;
    }
}
