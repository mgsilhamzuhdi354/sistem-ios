<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Pipeline Controller - Simplified with Claim Request
 */
class Pipeline extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $crewingId = $_SESSION['user_id'];
        $view = $this->input('view', 'available'); // 'available', 'my'
        
        // Get all statuses
        $statusResult = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order");
        $statuses = $statusResult ? $statusResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get pipeline data
        $pipeline = [];
        foreach ($statuses as $status) {
            if ($view === 'my') {
                // My assigned applications
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
                    WHERE a.status_id = ? AND aa.assigned_to = ?
                    ORDER BY a.created_at DESC
                ";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('ii', $status['id'], $crewingId);
            } else {
                // Available applications (unassigned or all)
                $query = "
                    SELECT a.id, a.user_id, a.vacancy_id, a.status_id, a.created_at,
                           u.full_name as applicant_name, u.email as applicant_email,
                           jv.title as vacancy_title,
                           aa.assigned_to as crewing_id,
                           uc.full_name as crewing_name,
                           (SELECT COUNT(*) FROM job_claim_requests jcr WHERE jcr.application_id = a.id AND jcr.requested_by = ? AND jcr.status = 'pending') as my_pending_request
                    FROM applications a
                    JOIN users u ON a.user_id = u.id
                    LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
                    LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                    LEFT JOIN users uc ON aa.assigned_to = uc.id
                    WHERE a.status_id = ? AND aa.id IS NULL
                    ORDER BY a.created_at DESC
                ";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('ii', $crewingId, $status['id']);
            }
            
            $stmt->execute();
            $pipeline[$status['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get my pending claim requests
        $pendingResult = $this->db->query("
            SELECT jcr.*, a.id as app_id, u.full_name as applicant_name, jv.title as vacancy_title
            FROM job_claim_requests jcr
            JOIN applications a ON jcr.application_id = a.id
            JOIN users u ON a.user_id = u.id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            WHERE jcr.requested_by = $crewingId AND jcr.status = 'pending'
            ORDER BY jcr.created_at DESC
        ");
        $myPendingRequests = $pendingResult ? $pendingResult->fetch_all(MYSQLI_ASSOC) : [];
        
        $this->view('crewing/pipeline/index', [
            'pageTitle' => 'Pipeline',
            'statuses' => $statuses,
            'pipeline' => $pipeline,
            'currentView' => $view,
            'myPendingRequests' => $myPendingRequests
        ]);
    }
    
    /**
     * Request to claim an applicant
     */
    public function requestClaim() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationId = intval($this->input('application_id'));
        $reason = trim($this->input('reason') ?: 'Ingin mengambil lamaran ini');
        $crewingId = $_SESSION['user_id'];
        
        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Invalid application']);
        }
        
        // Check if already requested
        $checkStmt = $this->db->prepare("SELECT id FROM job_claim_requests WHERE application_id = ? AND requested_by = ? AND status = 'pending'");
        $checkStmt->bind_param('ii', $applicationId, $crewingId);
        $checkStmt->execute();
        if ($checkStmt->get_result()->fetch_assoc()) {
            return $this->json(['success' => false, 'message' => 'Anda sudah memiliki request pending untuk lamaran ini']);
        }
        
        // Check if already assigned
        $assignCheck = $this->db->prepare("SELECT id FROM application_assignments WHERE application_id = ? AND status = 'active'");
        $assignCheck->bind_param('i', $applicationId);
        $assignCheck->execute();
        if ($assignCheck->get_result()->fetch_assoc()) {
            return $this->json(['success' => false, 'message' => 'Lamaran ini sudah di-assign ke orang lain']);
        }
        
        // Create claim request
        $stmt = $this->db->prepare("INSERT INTO job_claim_requests (application_id, requested_by, reason, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->bind_param('iis', $applicationId, $crewingId, $reason);
        
        if ($stmt->execute()) {
            // Notify Master Admin
            $adminResult = $this->db->query("SELECT id FROM users WHERE role_id = 11 AND is_active = 1 LIMIT 1");
            if ($admin = $adminResult->fetch_assoc()) {
                notifyUser($admin['id'], 'New Job Claim Request', 
                    'A crewing staff has requested to claim an application. Please review.',
                    'info', url('/master-admin/requests'));
            }
            
            return $this->json(['success' => true, 'message' => 'Request berhasil dikirim! Menunggu approval dari Master Admin.']);
        }
        
        return $this->json(['success' => false, 'message' => 'Gagal membuat request']);
    }
}
