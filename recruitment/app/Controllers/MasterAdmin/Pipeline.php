<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Pipeline Controller - SIMPLIFIED VERSION
 */
class Pipeline extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
    }
    
    public function index() {
        // Get all statuses
        $statusResult = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order");
        $statuses = $statusResult ? $statusResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // If no statuses, create defaults
        if (empty($statuses)) {
            $this->db->query("INSERT INTO application_statuses (id, name, color, sort_order) VALUES 
                (1, 'Pending', '#ffc107', 1),
                (2, 'Review', '#17a2b8', 2),
                (3, 'Interview', '#6f42c1', 3),
                (4, 'Medical', '#fd7e14', 4),
                (5, 'Final Review', '#20c997', 5),
                (6, 'Hired', '#28a745', 6),
                (7, 'Rejected', '#dc3545', 7)
            ");
            $statusResult = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order");
            $statuses = $statusResult ? $statusResult->fetch_all(MYSQLI_ASSOC) : [];
        }
        
        // Get pipeline data - SIMPLIFIED query without complex joins
        $pipeline = [];
        foreach ($statuses as $status) {
            $query = "
                SELECT a.id, a.user_id, a.vacancy_id, a.status_id, a.created_at,
                       u.full_name as applicant_name, u.email as applicant_email,
                       jv.title as vacancy_title,
                       aa.assigned_to as crewing_id,
                       uc.full_name as crewing_name
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
                LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                LEFT JOIN users uc ON aa.assigned_to = uc.id
                WHERE a.status_id = ?
                ORDER BY a.created_at DESC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $status['id']);
            $stmt->execute();
            $pipeline[$status['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get crewing staff
        $crewingStaff = $this->getCrewingStaff();
        
        // Get simple stats
        $stats = [
            'total' => $this->db->query("SELECT COUNT(*) as c FROM applications")->fetch_assoc()['c'] ?? 0,
            'assigned' => $this->db->query("SELECT COUNT(DISTINCT a.id) as c FROM applications a JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'")->fetch_assoc()['c'] ?? 0,
            'unassigned' => 0,
            'by_crewing' => []
        ];
        $stats['unassigned'] = $stats['total'] - $stats['assigned'];
        
        $this->view('master_admin/pipeline/index', [
            'pageTitle' => 'Recruitment Pipeline',
            'statuses' => $statuses,
            'pipeline' => $pipeline,
            'crewingStaff' => $crewingStaff,
            'stats' => $stats,
            'filterCrewingId' => null,
            'filterUnassigned' => false
        ]);
    }
    
    private function getCrewingStaff() {
        $result = $this->db->query("
            SELECT u.id, u.full_name, u.email, u.role_id,
                   COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_assignments
            FROM users u
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            WHERE u.role_id = 5 AND u.is_active = 1
            GROUP BY u.id
            ORDER BY u.full_name
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function getCrewingStaffAjax() {
        return $this->json(['success' => true, 'data' => $this->getCrewingStaff()]);
    }
    
    public function updateStatus() {
        if (!$this->isPost()) {
            redirect(url('/master-admin/pipeline'));
        }
        
        $applicationId = intval($this->input('application_id'));
        $newStatusId = intval($this->input('status_id'));
        
        $stmt = $this->db->prepare("UPDATE applications SET status_id = ?, status_updated_at = NOW(), updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('ii', $newStatusId, $applicationId);
        
        if ($stmt->execute()) {
            flash('success', 'Status updated successfully');
            
            // AUTO-PUSH TO ERP: When status changes to 6 (Hired/Approved)
            if ($newStatusId == 6) {
                $this->autoPushToErp($applicationId);
            }
        } else {
            flash('error', 'Failed to update status');
        }
        
        redirect(url('/master-admin/pipeline'));
    }
    
    /**
     * Auto-push approved applicant to ERP system
     * Creates crew record automatically when status changes to Hired
     */
    private function autoPushToErp($applicationId) {
        try {
            require_once APPPATH . 'Libraries/ErpSync.php';
            
            // Get application data
            $stmt = $this->db->prepare("
                SELECT a.*, u.full_name, u.email, u.phone,
                       ap.gender, ap.date_of_birth, ap.place_of_birth,
                       ap.nationality, ap.address as profile_address,
                       ap.city, ap.postal_code,
                       ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
                       ap.total_sea_service_months,
                       cp.employee_id
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
                WHERE a.id = ?
            ");
            $stmt->bind_param('i', $applicationId);
            $stmt->execute();
            $app = $stmt->get_result()->fetch_assoc();
            
            if (!$app || !empty($app['sent_to_erp_at'])) {
                return; // Already sent or not found
            }
            
            // Map gender
            $rawGender = strtolower(trim($app['gender'] ?? ''));
            if (in_array($rawGender, ['male', 'laki-laki', 'l', 'm'])) {
                $gender = 'male';
            } elseif (in_array($rawGender, ['female', 'perempuan', 'p', 'f', 'w'])) {
                $gender = 'female';
            } else {
                $gender = 'male';
            }
            
            $crewData = [
                'full_name' => $app['full_name'],
                'email' => $app['email'] ?? '',
                'phone' => $app['phone'] ?? '',
                'employee_id' => $app['employee_id'] ?: ('IO' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),
                'candidate_id' => $app['user_id'],
                'rank_id' => null,
                'status' => 'available',
                'join_date' => null,
                'notes' => 'Auto-pushed from recruitment pipeline',
                'gender' => $gender,
                'birth_date' => $app['date_of_birth'] ?? null,
                'birth_place' => $app['place_of_birth'] ?? '',
                'nationality' => $app['nationality'] ?? 'Indonesian',
                'address' => $app['profile_address'] ?? '',
                'city' => $app['city'] ?? '',
                'postal_code' => $app['postal_code'] ?? '',
                'emergency_name' => $app['emergency_name'] ?? '',
                'emergency_phone' => $app['emergency_phone'] ?? '',
                'emergency_relation' => $app['emergency_relation'] ?? '',
                'total_sea_time_months' => intval($app['total_sea_service_months'] ?? 0),
            ];
            
            $erpSync = new \ErpSync($this->db);
            
            // Check if crew already exists
            $existingCrewId = $erpSync->getCrewByCandidateId($app['user_id']);
            if ($existingCrewId) {
                $erpSync->updateCrew($existingCrewId, $crewData);
            } else {
                $erpSync->createCrew($crewData);
            }
            
            // Mark as sent
            $updateStmt = $this->db->prepare("UPDATE applications SET sent_to_erp_at = NOW() WHERE id = ?");
            $updateStmt->bind_param('i', $applicationId);
            $updateStmt->execute();
            
            // Add success notification
            flash('success', 'Status updated & crew auto-pushed to ERP ✓');
            
        } catch (\Throwable $e) {
            // Log error but don't block the status update
            error_log("Auto-push to ERP failed for application #$applicationId: " . $e->getMessage());
            flash('warning', 'Status updated, but auto-push to ERP failed: ' . $e->getMessage());
        }
    }
    
    public function transferResponsibility() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationId = intval($this->input('application_id'));
        $toCrewingId = intval($this->input('to_crewing_id'));
        $adminId = $_SESSION['user_id'];
        
        if (!$applicationId || !$toCrewingId) {
            return $this->json(['success' => false, 'message' => 'Please select a handler']);
        }
        
        // Mark current as transferred
        $this->db->query("UPDATE application_assignments SET status = 'transferred' WHERE application_id = $applicationId AND status = 'active'");
        
        // Create new assignment
        $stmt = $this->db->prepare("INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status) VALUES (?, ?, ?, 'Assigned by Master Admin', 'active')");
        $stmt->bind_param('iii', $applicationId, $toCrewingId, $adminId);
        
        if ($stmt->execute()) {
            // Get crewing name
            $crewingName = $this->db->query("SELECT full_name FROM users WHERE id = $toCrewingId")->fetch_assoc()['full_name'] ?? '';
            return $this->json(['success' => true, 'message' => 'Assigned to ' . $crewingName]);
        }
        
        return $this->json(['success' => false, 'message' => 'Failed to assign']);
    }
}
