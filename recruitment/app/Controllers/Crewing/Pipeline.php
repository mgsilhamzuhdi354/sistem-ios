<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Pipeline Controller - Simplified with Claim Request
 */
class Pipeline extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            redirect(url('/login'));
        }
    }
    
    /**
     * Check if a column exists in a table
     */
    private function checkColumnExists($table, $column)
    {
        // Whitelist allowed table/column names to prevent injection
        $allowedTables = ['applications', 'email_logs', 'users'];
        $allowedColumns = ['is_archived', 'application_id', 'archived_at', 'archived_by'];
        if (!in_array($table, $allowedTables) || !in_array($column, $allowedColumns)) {
            return false;
        }
        $result = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return $result && $result->num_rows > 0;
    }

    public function index()
    {
        $crewingId = $_SESSION['user_id'];
        $view = $this->input('view', 'available'); // 'available', 'my'

        // Check if archive column exists
        $hasArchive = $this->checkColumnExists('applications', 'is_archived');

        // Check if email_logs table has application_id column for email tracking
        $hasEmailLogs = false;
        try {
            $hasEmailLogs = $this->checkColumnExists('email_logs', 'application_id');
        } catch (Throwable $e) {}
        $emailCountSql = $hasEmailLogs ? ",\n                           (SELECT COUNT(*) FROM email_logs el WHERE el.application_id = a.id AND el.status = 'sent') as email_sent_count,\n                           (SELECT MAX(el.sent_at) FROM email_logs el WHERE el.application_id = a.id AND el.status = 'sent') as last_email_sent_at" : ",\n                           0 as email_sent_count, NULL as last_email_sent_at";

        // Get all statuses
        $statusResult = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order");
        $statuses = $statusResult ? $statusResult->fetch_all(MYSQLI_ASSOC) : [];

        // Get pipeline data
        $pipeline = [];
        foreach ($statuses as $status) {
            if ($view === 'my') {
                // My assigned applications
                $archiveFilter = $hasArchive ? 'AND a.is_archived = 0' : '';
                $query = "
                    SELECT a.id, a.user_id, a.vacancy_id, a.status_id, a.created_at, a.medical_email_sent_at,
                           a.sent_to_erp_at, a.erp_crew_id,
                           u.full_name as applicant_name, u.email as applicant_email, u.avatar as applicant_avatar,
                           jv.title as vacancy_title,
                           aa.assigned_to as crewing_id,
                           uc.full_name as crewing_name
                           {$emailCountSql}
                    FROM applications a
                    JOIN users u ON a.user_id = u.id
                    LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
                    LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                    LEFT JOIN users uc ON aa.assigned_to = uc.id
                    WHERE a.status_id = ? AND (aa.assigned_to = ? OR a.entered_by = ? OR a.current_crewing_id = ?) {$archiveFilter}
                    ORDER BY a.created_at DESC
                ";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('iiii', $status['id'], $crewingId, $crewingId, $crewingId);
            } else {
                // Available applications (unassigned or all)
                $archiveFilter = $hasArchive ? 'AND a.is_archived = 0' : '';
                $query = "
                    SELECT a.id, a.user_id, a.vacancy_id, a.status_id, a.created_at, a.medical_email_sent_at,
                           a.sent_to_erp_at, a.erp_crew_id,
                           u.full_name as applicant_name, u.email as applicant_email, u.avatar as applicant_avatar,
                           jv.title as vacancy_title,
                           aa.assigned_to as crewing_id,
                           uc.full_name as crewing_name,
                           (SELECT COUNT(*) FROM job_claim_requests jcr WHERE jcr.application_id = a.id AND jcr.requested_by = ? AND jcr.status = 'pending') as my_pending_request
                           {$emailCountSql}
                    FROM applications a
                    JOIN users u ON a.user_id = u.id
                    LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
                    LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                    LEFT JOIN users uc ON aa.assigned_to = uc.id
                    WHERE a.status_id = ? AND aa.id IS NULL {$archiveFilter}
                    ORDER BY a.created_at DESC
                ";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('ii', $crewingId, $status['id']);
            }

            $stmt->execute();
            $pipeline[$status['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        // Get my pending claim requests
        $pendingStmt = $this->db->prepare("
            SELECT jcr.*, a.id as app_id, u.full_name as applicant_name, jv.title as vacancy_title
            FROM job_claim_requests jcr
            JOIN applications a ON jcr.application_id = a.id
            JOIN users u ON a.user_id = u.id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            WHERE jcr.requested_by = ? AND jcr.status = 'pending'
            ORDER BY jcr.created_at DESC
        ");
        $pendingStmt->bind_param('i', $crewingId);
        $pendingStmt->execute();
        $myPendingRequests = $pendingStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Set default UI mode to modern for new users
        if (!isset($_SESSION['ui_mode'])) {
            $_SESSION['ui_mode'] = 'modern';
        }

        // --- New Candidate Alerts: candidates who chose this crewing as preferred recruiter ---
        $newCandidateAlerts = [];
        try {
            // Auto-create is_new_alert column if it doesn't exist
            $colCheck = $this->db->query("SHOW COLUMNS FROM applications LIKE 'is_new_alert'");
            if ($colCheck && $colCheck->num_rows == 0) {
                $this->db->query("ALTER TABLE applications ADD COLUMN is_new_alert TINYINT(1) DEFAULT 1");
            }

            $alertStmt = $this->db->prepare("
                SELECT a.id, a.created_at, a.recruiter_assignment_type,
                       u.full_name as applicant_name, u.avatar as applicant_avatar,
                       jv.title as vacancy_title
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
                WHERE a.preferred_recruiter_id = ?
                  AND a.recruiter_assignment_type = 'preferred'
                  AND a.is_new_alert = 1
                ORDER BY a.created_at DESC
            ");
            if ($alertStmt) {
                $alertStmt->bind_param('i', $crewingId);
                $alertStmt->execute();
                $newCandidateAlerts = $alertStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Throwable $e) {
            // Silently fail - alerts are non-critical
        }
        
        $this->view('crewing/pipeline/index', [
            'pageTitle' => 'Pipeline',
            'statuses' => $statuses,
            'pipeline' => $pipeline,
            'currentView' => $view,
            'myPendingRequests' => $myPendingRequests,
            'newCandidateAlerts' => $newCandidateAlerts,
            'uiMode' => $_SESSION['ui_mode']
        ]);
    }

    /**
     * Dismiss new candidate alert (AJAX)
     */
    public function dismissAlert()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $applicationId = intval($this->input('application_id'));
        $crewingId = $_SESSION['user_id'];

        if ($applicationId > 0) {
            // Dismiss single alert
            $stmt = $this->db->prepare("UPDATE applications SET is_new_alert = 0 WHERE id = ? AND preferred_recruiter_id = ?");
            $stmt->bind_param('ii', $applicationId, $crewingId);
            $stmt->execute();
        } else {
            // Dismiss all alerts for this crewing
            $stmt = $this->db->prepare("UPDATE applications SET is_new_alert = 0 WHERE preferred_recruiter_id = ? AND is_new_alert = 1");
            $stmt->bind_param('i', $crewingId);
            $stmt->execute();
        }

        return $this->json(['success' => true, 'message' => 'Alert dismissed']);
    }

    /**
     * Get applicant detail for modal popup (AJAX)
     */
    public function getDetail()
    {
        $applicationId = intval($this->input('application_id'));
        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Invalid application ID']);
        }

        $stmt = $this->db->prepare("
            SELECT a.id, a.user_id, a.status_id, a.created_at,
                   u.full_name, u.email, u.phone, u.avatar,
                   ap.date_of_birth, ap.place_of_birth, ap.gender, ap.nationality,
                   ap.address, ap.city, ap.blood_type,
                   ap.height_cm, ap.weight_kg,
                   ap.seaman_book_no, ap.passport_no,
                   ap.total_sea_service_months, ap.last_rank, ap.last_vessel_name, ap.last_vessel_type,
                   ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
                   jv.title as vacancy_title,
                   s.name as status_name, s.color as status_color,
                   aa.assigned_to as crewing_id,
                   uc.full_name as crewing_name, aa.assigned_at
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            LEFT JOIN users uc ON aa.assigned_to = uc.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();

        if (!$app) {
            return $this->json(['success' => false, 'message' => 'Applicant not found']);
        }

        // Get documents
        $docStmt = $this->db->prepare("
            SELECT d.id, dt.name as type_name, d.document_number, d.expiry_date, d.file_path
            FROM documents d
            JOIN document_types dt ON d.document_type_id = dt.id
            WHERE d.user_id = ?
            ORDER BY dt.id
        ");
        $docStmt->bind_param('i', $app['user_id']);
        $docStmt->execute();
        $app['documents'] = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $this->json(['success' => true, 'data' => $app]);
    }

    /**
     * Request to claim an applicant
     */
    public function requestClaim()
    {
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
                notifyUser(
                    $admin['id'],
                    'New Job Claim Request',
                    'A crewing staff has requested to claim an application. Please review.',
                    'info',
                    url('/master-admin/requests')
                );
            }

            return $this->json(['success' => true, 'message' => 'Request berhasil dikirim! Menunggu approval dari Master Admin.']);
        }

        return $this->json(['success' => false, 'message' => 'Gagal membuat request']);
    }
    public function requestStatusChange()
    {
        try {
            if (!$this->isPost()) {
                return $this->json(['success' => false, 'message' => 'Invalid request']);
            }

            $applicationId = intval($this->input('application_id'));
            $newStatusId = intval($this->input('status_id'));
            $crewingId = $_SESSION['user_id'];

            // --- AUTO-FIX DATABASE SCHEMA IF NEEDED ---
            try {
                $checkCols = $this->db->query("SHOW COLUMNS FROM status_change_requests LIKE 'current_status_id'");
                if ($checkCols && $checkCols->num_rows > 0) {
                    // Wrong schema detected, fixing it...
                    $this->db->query("DROP TABLE IF EXISTS status_change_requests");
                    $this->db->query("
                        CREATE TABLE status_change_requests (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            application_id INT NOT NULL,
                            requested_by INT NOT NULL,
                            from_status_id INT NOT NULL,
                            to_status_id INT NOT NULL,
                            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                            review_notes TEXT NULL,
                            reviewed_by INT NULL,
                            reviewed_at DATETIME NULL,
                            created_at DATETIME NOT NULL,
                            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            FOREIGN KEY (application_id) REFERENCES applications(id),
                            FOREIGN KEY (requested_by) REFERENCES users(id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ");
                }
            } catch (Throwable $dbErr) {
                // Ignore DB fix error, try to proceed or fail at insert
            }
            // -------------------------------------------

            // Validation
            if (!$applicationId || !$newStatusId) {
                return $this->json(['success' => false, 'message' => 'Invalid parameters']);
            }

            // Get current app
            $stmt = $this->db->prepare("SELECT status_id FROM applications WHERE id = ?");
            $stmt->bind_param('i', $applicationId);
            $stmt->execute();
            $app = $stmt->get_result()->fetch_assoc();

            if (!$app) {
                return $this->json(['success' => false, 'message' => 'Application not found']);
            }

            $fromStatusId = $app['status_id'];
            if ($fromStatusId == $newStatusId) {
                return $this->json(['success' => false, 'message' => 'Status is already the same']);
            }

            // Check for existing pending request
            $checkStmt = $this->db->prepare("SELECT id FROM status_change_requests WHERE application_id = ? AND status = 'pending'");
            $checkStmt->bind_param('i', $applicationId);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                return $this->json(['success' => false, 'message' => 'There is already a pending status change request for this application']);
            }

            // Create Request
            $stmt = $this->db->prepare("
                INSERT INTO status_change_requests (application_id, requested_by, from_status_id, to_status_id, status, created_at)
                VALUES (?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->bind_param('iiii', $applicationId, $crewingId, $fromStatusId, $newStatusId);

            if ($stmt->execute()) {
                // Notify Master Admin
                $admins = $this->db->query("SELECT id FROM users WHERE role_id = 11 AND is_active = 1")->fetch_all(MYSQLI_ASSOC);
                foreach ($admins as $admin) {
                    notifyUser(
                        $admin['id'],
                        'Approval Needed: Status Change',
                        'A Crewing staff has requested to change an applicant status.',
                        'warning',
                        url('/master-admin/requests')
                    );
                }

                return $this->json(['success' => true]);
            }

            return $this->json(['success' => false, 'message' => 'Failed to create request']);
        } catch (Throwable $e) {
            return $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Archive an application
     */
    public function archive()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationId = intval($this->input('application_id'));
        $userId = $_SESSION['user_id'];
        
        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Application ID required']);
        }
        
        $stmt = $this->db->prepare("
            UPDATE applications 
            SET is_archived = 1, archived_at = NOW(), archived_by = ?
            WHERE id = ?
        ");
        $stmt->bind_param('ii', $userId, $applicationId);
        
        if ($stmt->execute()) {
            return $this->json(['success' => true, 'message' => 'Aplikasi berhasil diarsipkan']);
        }
        
        return $this->json(['success' => false, 'message' => 'Gagal mengarsipkan aplikasi']);
    }
    
    /**
     * Get archived applications
     */
    public function getArchivedApplications()
    {
        $crewingId = $_SESSION['user_id'];
        
        // Get all archived applications
        $query = "
            SELECT a.id, a.user_id, a.vacancy_id, a.status_id, a.created_at, a.archived_at,
                   a.medical_email_sent_at, a.sent_to_erp_at, a.erp_crew_id,
                   u.full_name as applicant_name, u.email as applicant_email, u.avatar as applicant_avatar,
                   jv.title as vacancy_title,
                   s.name as status_name, s.color as status_color,
                   ua.full_name as archived_by_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN users ua ON a.archived_by = ua.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE a.is_archived = 1 
              AND (a.archived_by = ? OR aa.assigned_to = ?)
            ORDER BY a.archived_at DESC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ii', $crewingId, $crewingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $archived = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        return $this->json(['success' => true, 'data' => $archived]);
    }
    
    /**
     * Restore application from archive
     */
    public function restore()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationId = intval($this->input('application_id'));
        
        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Application ID required']);
        }
        
        $stmt = $this->db->prepare("
            UPDATE applications 
            SET is_archived = 0, archived_at = NULL, archived_by = NULL
            WHERE id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        
        if ($stmt->execute()) {
            return $this->json(['success' => true, 'message' => 'Aplikasi berhasil dikembalikan']);
        }
        
        return $this->json(['success' => false, 'message' => 'Gagal mengembalikan aplikasi']);
    }
    
    /**
     * Permanently delete application from archive
     */
    public function permanentDelete()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationId = intval($this->input('application_id'));
        
        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Application ID required']);
        }
        
        // First verify it's archived
        $checkStmt = $this->db->prepare("SELECT is_archived FROM applications WHERE id = ?");
        $checkStmt->bind_param('i', $applicationId);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        
        if (!$result || $result['is_archived'] != 1) {
            return $this->json(['success' => false, 'message' => 'Hanya bisa menghapus aplikasi yang sudah diarsipkan']);
        }
        
        // Delete related records first
        $delStmt = $this->db->prepare("DELETE FROM application_assignments WHERE application_id = ?");
        $delStmt->bind_param('i', $applicationId);
        $delStmt->execute();
        
        $delStmt = $this->db->prepare("DELETE FROM pipeline_requests WHERE application_id = ?");
        $delStmt->bind_param('i', $applicationId);
        $delStmt->execute();
        
        $delStmt = $this->db->prepare("DELETE FROM applicant_documents WHERE application_id = ?");
        $delStmt->bind_param('i', $applicationId);
        @$delStmt->execute(); // @ suppress if table doesn't exist
        
        // Delete the application
        $stmt = $this->db->prepare("DELETE FROM applications WHERE id = ?");
        $stmt->bind_param('i', $applicationId);
        
        if ($stmt->execute()) {
            return $this->json(['success' => true, 'message' => 'Aplikasi berhasil dihapus permanen']);
        }
        
        return $this->json(['success' => false, 'message' => 'Gagal menghapus aplikasi']);
    }
}
