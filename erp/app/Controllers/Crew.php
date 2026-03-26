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
     * Alias for index() — supports /crews/modern URL
     */
    public function modern()
    {
        return $this->index();
    }

    /**
     * List all crews
     */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('crews', 'view');

        // Auto-sync crew status from active contracts
        $this->db->query("
            UPDATE crews cr
            JOIN contracts c ON cr.id = c.crew_id AND c.status IN ('active', 'onboard')
            SET cr.status = 'onboard'
            WHERE cr.status NOT IN ('onboard', 'terminated')
        ");
        $this->db->query("
            UPDATE crews cr
            LEFT JOIN contracts c ON cr.id = c.crew_id AND c.status IN ('active', 'onboard')
            SET cr.status = 'available'
            WHERE c.id IS NULL AND cr.status = 'onboard'
        ");

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';

        $filters = [
            'search' => $this->input('search'),
            'status' => $this->input('status'),
            'rank_id' => $this->input('rank_id')
        ];

        $page = (int) ($this->input('page') ?? 1);

        // Cost summary from active contracts
        $costSummary = ['idr' => 0, 'usd' => 0, 'other' => 0, 'other_currency' => '', 'active_crew' => 0];
        $costQuery = $this->db->query("
            SELECT 
                UPPER(COALESCE(cur.code, 'IDR')) as currency,
                SUM(COALESCE(cs.total_monthly, cs.basic_salary, 0)) as total_salary,
                COUNT(DISTINCT c.crew_id) as crew_count
            FROM contracts c
            JOIN contract_salaries cs ON cs.contract_id = c.id
            LEFT JOIN currencies cur ON cs.currency_id = cur.id
            WHERE c.status = 'active'
            GROUP BY UPPER(COALESCE(cur.code, 'IDR'))
        ");
        if ($costQuery) {
            while ($row = $costQuery->fetch_assoc()) {
                $cur = strtoupper($row['currency']);
                $costSummary['active_crew'] += (int)$row['crew_count'];
                if ($cur === 'IDR') {
                    $costSummary['idr'] = (float)$row['total_salary'];
                } elseif ($cur === 'USD') {
                    $costSummary['usd'] = (float)$row['total_salary'];
                } else {
                    $costSummary['other'] += (float)$row['total_salary'];
                    $costSummary['other_currency'] = $cur;
                }
            }
        }

        $data = [
            'title' => 'Crew Database',
            'crews' => $this->crewModel->getList($filters, $page, 20),
            'total' => $this->crewModel->countCrews($filters),
            'costSummary' => $costSummary,
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

        // Active contract with salary details
        $activeContract = null;
        $acStmt = $this->db->prepare("
            SELECT c.*, v.name as vessel_name, r.name as rank_name, cl.name as client_name,
                   cs.basic_salary, cs.overtime_allowance, cs.leave_pay, cs.bonus, 
                   cs.other_allowance, cs.total_monthly, cs.exchange_rate,
                   COALESCE(cur.code, 'IDR') as currency_code
            FROM contracts c
            LEFT JOIN vessels v ON c.vessel_id = v.id
            LEFT JOIN ranks r ON c.rank_id = r.id
            LEFT JOIN clients cl ON c.client_id = cl.id
            LEFT JOIN contract_salaries cs ON cs.contract_id = c.id
            LEFT JOIN currencies cur ON cs.currency_id = cur.id
            WHERE c.crew_id = ? AND c.status IN ('active','onboard')
            ORDER BY c.sign_on_date DESC LIMIT 1
        ");
        if ($acStmt) {
            $acStmt->bind_param('i', $id);
            $acStmt->execute();
            $activeContract = $acStmt->get_result()->fetch_assoc();
            $acStmt->close();
        }

        // Recent payroll (last 3 months)
        $recentPayroll = [];
        $rpStmt = $this->db->prepare("
            SELECT pi.id, pi.crew_name, pi.rank_name, pi.vessel_name,
                   pi.basic_salary, pi.gross_salary, pi.net_salary, pi.total_deductions,
                   pi.currency_code, pi.status, pi.email_status, pi.payment_date,
                   pp.period_month, pp.period_year,
                   CONCAT(
                       ELT(pp.period_month, 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'),
                       ' ', pp.period_year
                   ) as period_name
            FROM payroll_items pi
            JOIN contracts c ON pi.contract_id = c.id
            JOIN payroll_periods pp ON pi.payroll_period_id = pp.id
            WHERE c.crew_id = ?
            ORDER BY pp.period_year DESC, pp.period_month DESC
            LIMIT 3
        ");
        if ($rpStmt) {
            $rpStmt->bind_param('i', $id);
            $rpStmt->execute();
            $rpResult = $rpStmt->get_result();
            while ($row = $rpResult->fetch_assoc()) {
                $recentPayroll[] = $row;
            }
            $rpStmt->close();
        }

        $data = [
            'title' => $crew['full_name'],
            'crew' => $crew,
            'skills' => $skillModel->getByCrew($id),
            'experiences' => $expModel->getByCrew($id),
            'documents' => $docModel->getByCrew($id),
            'contractHistory' => $this->crewModel->getContractHistory($id),
            'activeContract' => $activeContract,
            'recentPayroll' => $recentPayroll,
            'flash' => $this->getFlash()
        ];

        // Check UI mode preference
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'crews/view_modern' : 'crews/view';

        return $this->view($view, $data);
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

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'crews/form_modern' : 'crews/form';
        return $this->view($view, $data);
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

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'crews/form_modern' : 'crews/form';
        return $this->view($view, $data);
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
     * Delete crew (with cascading delete of related data)
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

        // Delete payroll_items linked through contracts first
        $sqlPayroll = "DELETE FROM payroll_items WHERE contract_id IN (SELECT id FROM contracts WHERE crew_id = ?)";
        $stmtP = $this->db->prepare($sqlPayroll);
        if ($stmtP) {
            $stmtP->bind_param('i', $id);
            $stmtP->execute();
            $stmtP->close();
        }

        // Cascade delete related records
        $tables = ['crew_skills', 'crew_experiences', 'crew_documents', 'contracts'];
        foreach ($tables as $table) {
            $sql = "DELETE FROM {$table} WHERE crew_id = ?";
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Delete the crew record
        $this->crewModel->delete($id);

        // Delete crew photo file if exists
        if ($crew['photo'] && file_exists($crew['photo'])) {
            unlink($crew['photo']);
        }

        $this->activityModel->log(
            $this->getCurrentUser()['id'],
            'delete',
            'crew',
            $id,
            "Deleted crew: {$crew['full_name']} (cascade: contracts, payroll_items, skills, documents, experiences)"
        );

        $this->setFlash('success', 'Crew dan semua data terkait berhasil dihapus');
        $this->redirect('crews');
    }

    /**
     * Get related data counts for delete confirmation (AJAX)
     */
    public function deleteInfo($id)
    {
        $this->requireAuth();

        $counts = [
            'contracts' => 0,
            'payroll_items' => 0,
            'skills' => 0,
            'documents' => 0,
            'experiences' => 0
        ];

        $tables = [
            'contracts' => 'contracts',
            'skills' => 'crew_skills',
            'documents' => 'crew_documents',
            'experiences' => 'crew_experiences'
        ];

        foreach ($tables as $key => $table) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM {$table} WHERE crew_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $counts[$key] = $result->fetch_assoc()['cnt'] ?? 0;
                $stmt->close();
            }
        }

        // Payroll items are linked through contracts
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM payroll_items WHERE contract_id IN (SELECT id FROM contracts WHERE crew_id = ?)");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $counts['payroll_items'] = $result->fetch_assoc()['cnt'] ?? 0;
            $stmt->close();
        }

        header('Content-Type: application/json');
        echo json_encode($counts);
        exit;
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

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Use server-side MIME detection (more reliable than browser)
        $detectedType = $file['type'];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $serverMime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                if ($serverMime) {
                    $detectedType = $serverMime;
                }
            }
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Accept any size - check MIME or extension
        if (!in_array($detectedType, $allowedTypes) && !in_array($extension, $allowedExtensions)) {
            error_log("Photo upload rejected: MIME={$detectedType}, ext={$extension}");
            return null;
        }

        // Max 10MB for photos
        $maxSize = 10 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            error_log("Photo upload rejected: size={$file['size']} exceeds 10MB");
            return null;
        }

        // Normalize extension
        if ($extension === 'jpeg') $extension = 'jpg';

        $filename = 'crew_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            error_log("Photo upload failed: move_uploaded_file() returned false");
            return null;
        }

        // Auto-resize if image is too large (save disk space)
        if (function_exists('imagecreatefromjpeg') && in_array($detectedType, ['image/jpeg', 'image/png', 'image/webp'])) {
            try {
                $imageInfo = @getimagesize($filepath);
                if ($imageInfo && ($imageInfo[0] > 1200 || $imageInfo[1] > 1200)) {
                    $srcWidth = $imageInfo[0];
                    $srcHeight = $imageInfo[1];
                    $maxDim = 1200;

                    // Calculate new dimensions maintaining aspect ratio
                    if ($srcWidth > $srcHeight) {
                        $newWidth = $maxDim;
                        $newHeight = (int)round($srcHeight * ($maxDim / $srcWidth));
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = (int)round($srcWidth * ($maxDim / $srcHeight));
                    }

                    $src = null;
                    switch ($detectedType) {
                        case 'image/jpeg': $src = @imagecreatefromjpeg($filepath); break;
                        case 'image/png': $src = @imagecreatefrompng($filepath); break;
                        case 'image/webp': $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($filepath) : null; break;
                    }

                    if ($src) {
                        $dst = imagecreatetruecolor($newWidth, $newHeight);
                        // Preserve transparency for PNG
                        if ($detectedType === 'image/png') {
                            imagealphablending($dst, false);
                            imagesavealpha($dst, true);
                        }
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

                        // Save resized - always save as JPEG for consistency and smaller size
                        $jpgPath = $uploadDir . 'crew_' . time() . '_' . uniqid() . '.jpg';
                        imagejpeg($dst, $jpgPath, 85);
                        imagedestroy($src);
                        imagedestroy($dst);

                        // Remove original, use resized
                        @unlink($filepath);
                        $filepath = $jpgPath;
                    }
                }
            } catch (\Exception $e) {
                error_log("Photo resize warning: " . $e->getMessage());
                // Keep original if resize fails
            }
        }

        return $filepath;
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
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
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
