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

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

        $filters = [
            'search' => $this->input('search'),
            'status' => $this->input('status'),
            'rank_id' => $this->input('rank_id')
        ];

        $page = (int) ($this->input('page') ?? 1);

        $data = [
            'title' => 'Crew Database',
            'crews' => $this->crewModel->getList($filters, $page, 20),
            'total' => $this->crewModel->countCrews($filters),
            'filters' => $filters,
            'page' => $page,
            'flash' => $this->getFlash()
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'crews/modern' : 'crews/index';
        return $this->view($view, $data);
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

        // Check UI mode preference
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';
        $view = $uiMode === 'modern' ? 'crews/view_modern' : 'crews/view';

        return $this->view($view, $data);
    }

    /**
     * Display crew list in modern UI
     */
    public function modern()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'view');

        $filters = [
            'search' => $this->input('search'),
            'status' => $this->input('status'),
            'rank_id' => $this->input('rank_id')
        ];

        $page = (int) ($this->input('page') ?? 1);

        $data = [
            'title' => 'Crew Database',
            'crews' => $this->crewModel->getList($filters, $page, 20),
            'total' => $this->crewModel->countCrews($filters),
            'filters' => $filters,
            'page' => $page,
            'flash' => $this->getFlash()
        ];

        return $this->view('crews/modern', $data);
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
     * Crew Performance Tracking
     */
    public function performance()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'view');

        $crewId = $this->input('crew_id');
        $month = $this->input('month', date('m'));
        $year = $this->input('year', date('Y'));

        // Get all crews for filter dropdown
        $crews = $this->crewModel->getList(['status' => 'onboard'], 1, 100);

        // Get performance data
        $performanceData = [];

        if ($crewId) {
            // Get specific crew performance
            $sql = "SELECT c.id, c.full_name, c.employee_id, r.name as rank_name,
                    v.name as vessel_name
                    FROM crews c
                    LEFT JOIN ranks r ON c.current_rank_id = r.id
                    LEFT JOIN contracts con ON c.id = con.crew_id AND con.status = 'active'
                    LEFT JOIN vessels v ON con.vessel_id = v.id
                    WHERE c.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $crewId);
            $stmt->execute();
            $crew = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($crew) {
                $performanceData[] = [
                    'crew' => $crew,
                    'score' => rand(70, 100), // Placeholder - implement actual KPI calculation
                    'attendance' => rand(90, 100),
                    'skills' => rand(75, 95),
                    'discipline' => rand(80, 100)
                ];
            }
        } else {
            // Get all onboard crew performance summary
            $sql = "SELECT c.id, c.full_name, c.employee_id, r.name as rank_name,
                    v.name as vessel_name
                    FROM crews c
                    LEFT JOIN ranks r ON c.current_rank_id = r.id
                    LEFT JOIN contracts con ON c.id = con.crew_id AND con.status = 'active'
                    LEFT JOIN vessels v ON con.vessel_id = v.id
                    WHERE c.status = 'onboard'
                    ORDER BY c.full_name";
            $result = $this->db->query($sql);

            while ($row = $result->fetch_assoc()) {
                $performanceData[] = [
                    'crew' => $row,
                    'score' => rand(70, 100),
                    'attendance' => rand(90, 100),
                    'skills' => rand(75, 95),
                    'discipline' => rand(80, 100)
                ];
            }
        }

        $data = [
            'title' => 'Performa Crew',
            'currentPage' => 'crew-performance',
            'crews' => $crews,
            'selectedCrew' => $crewId,
            'month' => $month,
            'year' => $year,
            'performanceData' => $performanceData,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'crews/performance_modern' : 'crews/performance';

        return $this->view($view, $data);
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
            'years_experience' => (int) $this->input('years_experience'),
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

    /**
     * Skill Matrix - Display competency matrix
     */
    public function skillMatrix()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'view');

        require_once APPPATH . 'Models/CrewSkillModel.php';
        $skillModel = new CrewSkillModel($this->db);

        $data = [
            'title' => 'Crew Skill Matrix',
            'skillMatrix' => $skillModel->getSkillMatrix(),
            'statistics' => $skillModel->getStatistics(),
            'uniqueSkills' => $skillModel->getUniqueSkillNames(),
            'flash' => $this->getFlash()
        ];

        // Check UI mode preference
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';
        $view = $uiMode === 'modern' ? 'crews/skill_matrix_modern' : 'crews/skill_matrix';

        return $this->view($view, $data);
    }

    /**
     * Add skill to crew member (AJAX)
     */
    public function addSkill($crewId)
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'edit');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        require_once APPPATH . 'Models/CrewSkillModel.php';
        $skillModel = new CrewSkillModel($this->db);

        // Validate crew exists
        $crew = $this->crewModel->find($crewId);
        if (!$crew) {
            echo json_encode(['success' => false, 'message' => 'Crew not found']);
            return;
        }

        $skillName = trim($this->input('skill_name'));
        $skillLevel = $this->input('skill_level');
        $certificateId = trim($this->input('certificate_id'));
        $notes = trim($this->input('notes'));

        // Validation
        if (empty($skillName)) {
            echo json_encode(['success' => false, 'message' => 'Skill name is required']);
            return;
        }

        $validLevels = ['basic', 'intermediate', 'advanced', 'expert'];
        if (!in_array($skillLevel, $validLevels)) {
            echo json_encode(['success' => false, 'message' => 'Invalid skill level']);
            return;
        }

        // Check if skill already exists for this crew
        if ($skillModel->hasSkill($crewId, $skillName)) {
            echo json_encode(['success' => false, 'message' => 'This crew member already has this skill. Please edit instead.']);
            return;
        }

        // Insert skill
        $skillData = [
            'crew_id' => $crewId,
            'skill_name' => $skillName,
            'skill_level' => $skillLevel,
            'certificate_id' => $certificateId ?: null,
            'notes' => $notes ?: null
        ];

        $skillId = $skillModel->insert($skillData);

        if ($skillId) {
            // Log activity
            $this->activityModel->log(
                $_SESSION['user']['id'],
                'crew_skill_added',
                "Added skill '{$skillName}' to crew {$crew['full_name']}"
            );

            echo json_encode([
                'success' => true,
                'message' => 'Skill added successfully',
                'skill_id' => $skillId
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add skill']);
        }
    }

    /**
     * Update skill (AJAX)
     */
    public function updateSkill($skillId)
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'edit');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        require_once APPPATH . 'Models/CrewSkillModel.php';
        $skillModel = new CrewSkillModel($this->db);

        // Validate skill exists
        $skill = $skillModel->find($skillId);
        if (!$skill) {
            echo json_encode(['success' => false, 'message' => 'Skill not found']);
            return;
        }

        $skillLevel = $this->input('skill_level');
        $certificateId = trim($this->input('certificate_id'));
        $notes = trim($this->input('notes'));

        $validLevels = ['basic', 'intermediate', 'advanced', 'expert'];
        if (!in_array($skillLevel, $validLevels)) {
            echo json_encode(['success' => false, 'message' => 'Invalid skill level']);
            return;
        }

        // Update skill
        $updateData = [
            'skill_level' => $skillLevel,
            'certificate_id' => $certificateId ?: null,
            'notes' => $notes ?: null
        ];

        $success = $skillModel->update($skillId, $updateData);

        if ($success) {
            // Log activity
            $crew = $this->crewModel->find($skill['crew_id']);
            $this->activityModel->log(
                $_SESSION['user']['id'],
                'crew_skill_updated',
                "Updated skill '{$skill['skill_name']}' for crew {$crew['full_name']}"
            );

            echo json_encode(['success' => true, 'message' => 'Skill updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update skill']);
        }
    }

    /**
     * Delete skill (AJAX)
     */
    public function deleteSkill($skillId)
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'delete');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        require_once APPPATH . 'Models/CrewSkillModel.php';
        $skillModel = new CrewSkillModel($this->db);

        // Validate skill exists
        $skill = $skillModel->find($skillId);
        if (!$skill) {
            echo json_encode(['success' => false, 'message' => 'Skill not found']);
            return;
        }

        $success = $skillModel->delete($skillId);

        if ($success) {
            // Log activity
            $crew = $this->crewModel->find($skill['crew_id']);
            $this->activityModel->log(
                $_SESSION['user']['id'],
                'crew_skill_deleted',
                "Deleted skill '{$skill['skill_name']}' from crew {$crew['full_name']}"
            );

            echo json_encode(['success' => true, 'message' => 'Skill deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete skill']);
        }
    }
}
