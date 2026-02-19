<?php
/**
 * PT Indo Ocean - ERP System
 * Recruitment Hub Controller
 * Integrates with Recruitment system
 */

namespace App\Controllers;

class RecruitmentHub extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // recruitmentDb is already connected by BaseController via connectDatabase()
        // which includes Docker/NAS IP fallback logic
        if (!$this->recruitmentDb || (property_exists($this->recruitmentDb, 'connect_error') && $this->recruitmentDb->connect_error)) {
            error_log("RecruitmentHub: Recruitment DB not available via BaseController");
        }
    }

    /**
     * Recruitment pipeline view
     */
    public function pipeline()
    {
        $this->requireAuth();

        // Get pipeline statistics and candidates from recruitment DB
        $stats = [];
        $candidates = [];

        if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
            // Count by status
            $query = "
                SELECT 
                    s.name as status,
                    COUNT(*) as count
                FROM applications a
                JOIN application_statuses s ON a.status_id = s.id
                WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY a.status_id, s.name
            ";
            $result = $this->recruitmentDb->query($query);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $stats[$row['status']] = $row['count'];
                }
            }

            // Get candidates list
            $candidatesQuery = "
                SELECT 
                    a.id,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.avatar,
                    v.title as vacancy_title,
                    d.name as department_name,
                    s.name as status_name,
                    s.color as status_color,
                    a.submitted_at,
                    a.interview_score,
                    a.overall_score
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                JOIN application_statuses s ON a.status_id = s.id
                ORDER BY a.submitted_at DESC
                LIMIT 100
            ";
            $candidatesResult = $this->recruitmentDb->query($candidatesQuery);

            if ($candidatesResult) {
                while ($row = $candidatesResult->fetch_assoc()) {
                    $candidates[] = $row;
                }
            }
        }

        $data = [
            'title' => 'Recruitment Pipeline',
            'currentPage' => 'recruitment-pipeline',
            'stats' => $stats,
            'candidates' => $candidates,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'recruitment/pipeline_modern' : 'recruitment/pipeline';

        return $this->view($view, $data);
    }

    /**
     * Approval center - show pending approvals from crews table
     */
    public function approval()
    {
        $this->requireAuth();

        $pendingApprovals = [];
        $stats = [
            'pending_count' => 0,
            'total_processed' => 0,
            'approved_count' => 0,
            'rejected_count' => 0
        ];

        // 1. Count pending approvals from crews table (status = pending_approval)
        $countResult = $this->db->query("SELECT COUNT(*) as count FROM crews WHERE status = 'pending_approval'");
        if ($countResult) {
            $stats['pending_count'] = $countResult->fetch_assoc()['count'];
        }

        // 2. Count approved in last 30 days
        $approvedResult = $this->db->query("
            SELECT COUNT(*) as count FROM crews 
            WHERE source = 'recruitment' AND status = 'available' 
            AND approved_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        if ($approvedResult) {
            $stats['approved_count'] = $approvedResult->fetch_assoc()['count'];
        }

        // 3. Count rejected in last 30 days
        $rejectedResult = $this->db->query("
            SELECT COUNT(*) as count FROM crews 
            WHERE source = 'recruitment' AND status = 'rejected' 
            AND updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        if ($rejectedResult) {
            $stats['rejected_count'] = $rejectedResult->fetch_assoc()['count'];
        }

        $stats['total_processed'] = $stats['approved_count'] + $stats['rejected_count'];

        // Get pending approvals from crews table with their documents count
        $query = "
            SELECT 
                c.id,
                c.employee_id,
                c.full_name as applicant_name,
                c.email,
                c.phone,
                c.gender,
                c.birth_date,
                c.birth_place,
                c.nationality,
                c.address,
                c.city,
                c.province,
                c.postal_code,
                c.emergency_name,
                c.emergency_phone,
                c.emergency_relation,
                c.total_sea_time_months,
                c.photo,
                c.notes,
                c.candidate_id,
                c.current_rank_id,
                c.created_at,
                r.name as rank_name,
                (SELECT COUNT(*) FROM crew_documents cd WHERE cd.crew_id = c.id) as doc_count
            FROM crews c
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE c.status = 'pending_approval'
            ORDER BY c.created_at DESC
        ";
        $result = $this->db->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Check data completeness
                $required = ['full_name', 'email', 'phone', 'gender', 'birth_date', 'nationality'];
                $filled = 0;
                foreach ($required as $field) {
                    if (!empty($row[$field])) $filled++;
                }
                $row['completeness'] = round(($filled / count($required)) * 100);
                $row['is_complete'] = $filled === count($required);
                $pendingApprovals[] = $row;
            }
        }

        $data = [
            'title' => 'Approval Center',
            'currentPage' => 'recruitment-approval',
            'pendingApprovals' => $pendingApprovals,
            'stats' => $stats,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'recruitment/approval_modern' : 'recruitment/approval';

        return $this->view($view, $data);
    }
    /**
     * Approve a candidate - change status from pending_approval to available
     */
    public function approve($crewId = null)
    {
        $this->requireAuth();
        
        if (!$crewId) {
            $this->setFlash('error', 'ID kandidat tidak valid');
            $this->redirect('recruitment/approval');
            return;
        }

        $approvalNotes = $_POST['approval_notes'] ?? '';

        try {
            $this->db->begin_transaction();

            $userId = $_SESSION['user_id'] ?? 1;

            // Update crew status with approval tracking
            $stmt = $this->db->prepare("
                UPDATE crews SET 
                    status = 'available', 
                    approved_at = NOW(), 
                    approved_by = ?,
                    notes = CASE WHEN ? != '' THEN CONCAT(IFNULL(notes,''), '\n[APPROVED] ', ?) ELSE notes END,
                    updated_at = NOW()
                WHERE id = ? AND status = 'pending_approval'
            ");
            $stmt->bind_param('issi', $userId, $approvalNotes, $approvalNotes, $crewId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception('Kandidat tidak ditemukan atau sudah diproses');
            }
            $stmt->close();

            // Get crew name for notification
            $nameStmt = $this->db->prepare("SELECT full_name, current_rank_id FROM crews WHERE id = ?");
            $nameStmt->bind_param('i', $crewId);
            $nameStmt->execute();
            $crew = $nameStmt->get_result()->fetch_assoc();
            $nameStmt->close();

            // Get rank name
            $rankName = 'Unknown';
            if ($crew['current_rank_id']) {
                $rankStmt = $this->db->prepare("SELECT name FROM ranks WHERE id = ?");
                $rankStmt->bind_param('i', $crew['current_rank_id']);
                $rankStmt->execute();
                $rankRow = $rankStmt->get_result()->fetch_assoc();
                if ($rankRow) $rankName = $rankRow['name'];
                $rankStmt->close();
            }

            // Create notification
            $this->createNotification(
                'candidate_approved',
                "Kandidat Disetujui: {$crew['full_name']}",
                "Pengajuan untuk {$crew['full_name']} - {$rankName} sudah di-approve. Silahkan lakukan pembuatan kontrak.",
                "contracts/create?crew_id={$crewId}"
            );

            // Log activity
            $this->logActivity('approve_candidate', "Approved candidate: {$crew['full_name']} (Crew ID: {$crewId})");

            $this->db->commit();

            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => "✅ Kandidat {$crew['full_name']} berhasil disetujui!"]);
                return;
            }

            $this->setFlash('success', "✅ Kandidat {$crew['full_name']} berhasil disetujui!");
            $this->redirect('recruitment/approval');
        } catch (\Exception $e) {
            $this->db->rollback();
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal approve: ' . $e->getMessage()]);
                return;
            }
            $this->setFlash('error', 'Gagal approve: ' . $e->getMessage());
            $this->redirect('recruitment/approval');
        }
    }

    /**
     * Reject a candidate
     */
    public function reject($crewId = null)
    {
        $this->requireAuth();
        
        if (!$crewId) {
            $this->setFlash('error', 'ID kandidat tidak valid');
            $this->redirect('recruitment/approval');
            return;
        }

        $reason = $_POST['rejection_reason'] ?? '';

        try {
            $this->db->begin_transaction();

            $userId = $_SESSION['user_id'] ?? 1;

            // Update crew status with rejection tracking
            $stmt = $this->db->prepare("
                UPDATE crews SET 
                    status = 'rejected', 
                    rejected_at = NOW(),
                    rejected_by = ?,
                    rejection_reason = ?,
                    notes = CONCAT(IFNULL(notes,''), '\n[REJECTED] ', ?),
                    updated_at = NOW()
                WHERE id = ? AND status = 'pending_approval'
            ");
            $stmt->bind_param('issi', $userId, $reason, $reason, $crewId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception('Kandidat tidak ditemukan atau sudah diproses');
            }
            $stmt->close();

            // Get crew info
            $nameStmt = $this->db->prepare("SELECT full_name, candidate_id FROM crews WHERE id = ?");
            $nameStmt->bind_param('i', $crewId);
            $nameStmt->execute();
            $crew = $nameStmt->get_result()->fetch_assoc();
            $nameStmt->close();

            // Optionally update recruitment DB status
            if ($this->recruitmentDb && $crew['candidate_id']) {
                $updateStmt = $this->recruitmentDb->prepare("
                    UPDATE applications SET status_id = 7, updated_at = NOW() 
                    WHERE user_id = ? AND status_id IN (5, 6)
                ");
                if ($updateStmt) {
                    $updateStmt->bind_param('i', $crew['candidate_id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }

            // Create notification
            $this->createNotification(
                'candidate_rejected',
                "Kandidat Ditolak: {$crew['full_name']}",
                "Pengajuan untuk {$crew['full_name']} telah ditolak." . ($reason ? " Alasan: {$reason}" : ''),
                null
            );

            // Log activity
            $this->logActivity('reject_candidate', "Rejected candidate: {$crew['full_name']} (Crew ID: {$crewId}). Reason: {$reason}");

            $this->db->commit();

            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => "Kandidat {$crew['full_name']} telah ditolak."]);
                return;
            }

            $this->setFlash('success', "Kandidat {$crew['full_name']} telah ditolak.");
            $this->redirect('recruitment/approval');
        } catch (\Exception $e) {
            $this->db->rollback();
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal reject: ' . $e->getMessage()]);
                return;
            }
            $this->setFlash('error', 'Gagal reject: ' . $e->getMessage());
            $this->redirect('recruitment/approval');
        }
    }

    /**
     * Show candidate detail page (from recruitment DB)
     */
    public function candidate($applicationId = null)
    {
        $this->requireAuth();

        if (!$applicationId || !$this->recruitmentDb || $this->recruitmentDb->connect_error) {
            $this->setFlash('error', 'ID kandidat tidak valid atau koneksi recruitment DB gagal');
            $this->redirect('recruitment/pipeline');
            return;
        }

        // Get candidate data from recruitment DB
        $stmt = $this->recruitmentDb->prepare("
            SELECT 
                a.id,
                a.user_id,
                a.vacancy_id,
                a.status_id,
                a.submitted_at,
                a.interview_score,
                a.overall_score,
                a.sent_to_erp_at,
                u.full_name,
                u.email,
                u.phone,
                ap.date_of_birth,
                ap.address,
                ap.city,
                ap.nationality as country,
                ap.passport_no,
                ap.gender,
                ap.nationality,
                u.avatar,
                ap.emergency_name,
                ap.emergency_phone,
                u.is_synced_to_erp,
                v.title as vacancy_title,
                d.name as department_name,
                s.name as status_name,
                s.color as status_color
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $candidate = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$candidate) {
            $this->setFlash('error', 'Kandidat tidak ditemukan');
            $this->redirect('recruitment/pipeline');
            return;
        }

        // Get documents
        $documents = [];
        $docStmt = $this->recruitmentDb->prepare("
            SELECT ad.*, dt.name as type_name
            FROM applicant_documents ad
            LEFT JOIN document_types dt ON ad.document_type_id = dt.id
            WHERE ad.user_id = ?
            ORDER BY ad.created_at DESC
        ");
        if ($docStmt) {
            $docStmt->bind_param('i', $candidate['user_id']);
            $docStmt->execute();
            $docResult = $docStmt->get_result();
            while ($doc = $docResult->fetch_assoc()) {
                $documents[] = $doc;
            }
            $docStmt->close();
        }
        $candidate['documents'] = $documents;

        // Get interviews
        $interviews = [];
        $intStmt = $this->recruitmentDb->prepare("
            SELECT i.*, qb.name as question_bank_name
            FROM interviews i
            LEFT JOIN interview_question_banks qb ON i.question_bank_id = qb.id
            WHERE i.application_id = ?
            ORDER BY i.created_at DESC
        ");
        if ($intStmt) {
            $intStmt->bind_param('i', $applicationId);
            $intStmt->execute();
            $intResult = $intStmt->get_result();
            while ($interview = $intResult->fetch_assoc()) {
                $interviews[] = $interview;
            }
            $intStmt->close();
        }
        $candidate['interviews'] = $interviews;

        // Get medical checkups
        $medicals = [];
        $medStmt = $this->recruitmentDb->prepare("
            SELECT * FROM medical_checkups
            WHERE application_id = ?
            ORDER BY created_at DESC
        ");
        if ($medStmt) {
            $medStmt->bind_param('i', $applicationId);
            $medStmt->execute();
            $medResult = $medStmt->get_result();
            while ($medical = $medResult->fetch_assoc()) {
                $medicals[] = $medical;
            }
            $medStmt->close();
        }
        $candidate['medical_checkups'] = $medicals;

        $data = [
            'title' => 'Detail Kandidat - ' . ($candidate['full_name'] ?? ''),
            'currentPage' => 'recruitment-pipeline',
            'candidate' => $candidate,
            'flash' => $this->getFlash()
        ];

        return $this->view('recruitment/candidate_detail', $data);
    }

    /**
     * Get candidate detail as JSON (for AJAX modal)
     */
    public function candidateDetail($crewId = null)
    {
        $this->requireAuth();

        if (!$crewId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID not valid']);
            return;
        }

        // Get crew data
        $stmt = $this->db->prepare("
            SELECT c.*, r.name as rank_name 
            FROM crews c 
            LEFT JOIN ranks r ON c.current_rank_id = r.id 
            WHERE c.id = ?
        ");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $crew = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$crew) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Crew not found']);
            return;
        }

        // Get documents
        $docStmt = $this->db->prepare("
            SELECT * FROM crew_documents WHERE crew_id = ? ORDER BY created_at DESC
        ");
        $docStmt->bind_param('i', $crewId);
        $docStmt->execute();
        $documents = [];
        $docResult = $docStmt->get_result();
        while ($doc = $docResult->fetch_assoc()) {
            $documents[] = $doc;
        }
        $docStmt->close();

        header('Content-Type: application/json');
        echo json_encode([
            'crew' => $crew,
            'documents' => $documents
        ]);
    }

    /**
     * Create notification in ERP system
     */
    private function createNotification($type, $title, $message, $actionUrl = null)
    {
        try {
            // Check if notifications table exists
            $check = $this->db->query("SHOW TABLES LIKE 'notifications'");
            if ($check->num_rows > 0) {
                // Map custom types to display-friendly types
                $typeMap = [
                    'candidate_approved' => 'success',
                    'candidate_rejected' => 'warning',
                    'contract_created' => 'info',
                    'crew_onboarded' => 'success',
                ];
                $dbType = $typeMap[$type] ?? $type;

                $stmt = $this->db->prepare("
                    INSERT INTO notifications (type, title, message, link, is_read, created_at) 
                    VALUES (?, ?, ?, ?, 0, NOW())
                ");
                $stmt->bind_param('ssss', $dbType, $title, $message, $actionUrl);
                $stmt->execute();
                $stmt->close();
            }
        } catch (\Exception $e) {
            error_log("Notification error: " . $e->getMessage());
        }
    }

    /**
     * Log activity for audit
     */
    private function logActivity($action, $description)
    {
        try {
            // Ensure activity_logs table exists
            $this->db->query("
                CREATE TABLE IF NOT EXISTS activity_logs (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NULL,
                    action VARCHAR(100) NOT NULL,
                    description TEXT,
                    entity_type VARCHAR(50) NULL,
                    entity_id INT NULL,
                    ip_address VARCHAR(45) NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $userId = $_SESSION['user_id'] ?? null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, action, description, ip_address, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('isss', $userId, $action, $description, $ipAddress);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Auto-onboarding - approved crew ready to onboard
     */
    public function onboarding()
    {
        $this->requireAuth();

        $approvedCrew = [];

        if ($this->recruitmentDb) {
            // First check if recruitment_sync table exists in ERP DB
            $result = $this->db->query("SHOW TABLES LIKE 'recruitment_sync'");
            if ($result->num_rows == 0) {
                // Table doesn't exist, create it
                $this->createRecruitmentSyncTable();
            }

            // Get approved crew from recruitment DB (status_id 6 = Approved)
            $query = "
                SELECT 
                    a.id as application_id,
                    u.full_name as applicant_name,
                    v.title as position_applied,
                    u.email,
                    u.phone,
                    a.submitted_at as applied_date
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                WHERE a.status_id = 6
                ORDER BY a.submitted_at DESC
            ";

            $result = $this->recruitmentDb->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Check if already synced in ERP DB
                    $stmt = $this->db->prepare("SELECT sync_status FROM recruitment_sync WHERE recruitment_applicant_id = ?");
                    $stmt->bind_param('i', $row['application_id']);
                    $stmt->execute();
                    $syncResult = $stmt->get_result();
                    $syncData = $syncResult->fetch_assoc();
                    $stmt->close();

                    // Only add if not synced or pending
                    if (!$syncData || $syncData['sync_status'] === 'pending') {
                        $approvedCrew[] = $row;
                    }
                }
            }
        }

        $data = [
            'title' => 'Auto-Onboarding',
            'currentPage' => 'recruitment-onboarding',
            'approvedCrew' => $approvedCrew,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'recruitment/onboarding_modern' : 'recruitment/onboarding';

        return $this->view($view, $data);
    }

    /**
     * Process onboarding - create crew from approved candidate with complete data mapping
     */
    public function processOnboard($applicationId)
    {
        $this->requireAuth();

        if (!$this->recruitmentDb) {
            $this->setFlash('error', 'Koneksi ke database recruitment gagal');
            $this->redirect('recruitment/onboarding');
        }

        // Get complete application and user data from recruitment DB
        $stmt = $this->recruitmentDb->prepare("
            SELECT 
                a.*, 
                u.full_name, 
                u.email, 
                u.phone, 
                ap.date_of_birth,
                ap.address,
                ap.city,
                ap.nationality as country,
                ap.passport_no,
                ap.gender,
                ap.nationality,
                ap.emergency_name as emergency_contact_name,
                ap.emergency_phone as emergency_contact_phone,
                v.title as position_applied
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $application = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$application) {
            $this->setFlash('error', 'Data aplikasi tidak ditemukan');
            $this->redirect('recruitment/onboarding');
        }

        // Check if already synced
        $checkStmt = $this->db->prepare("SELECT crew_id FROM recruitment_sync WHERE recruitment_applicant_id = ?");
        $checkStmt->bind_param('i', $applicationId);
        $checkStmt->execute();
        $existing = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($existing) {
            $this->setFlash('warning', 'Kandidat ini sudah di-import sebelumnya');
            $this->redirect('recruitment/onboarding');
        }

        // Create crew in ERP with complete data
        try {
            $this->db->begin_transaction();

            // Generate employee_id (format: CRW-YYYY-XXXX)
            $year = date('Y');
            $countStmt = $this->db->prepare("SELECT COUNT(*) as count FROM crews WHERE employee_id LIKE ?");
            $pattern = "CRW-{$year}-%";
            $countStmt->bind_param('s', $pattern);
            $countStmt->execute();
            $count = $countStmt->get_result()->fetch_assoc()['count'];
            $countStmt->close();
            $employeeId = sprintf("CRW-%s-%04d", $year, $count + 1);

            // Insert crew with complete data mapping
            $stmt = $this->db->prepare("
                INSERT INTO crews (
                    employee_id, full_name, email, phone, 
                    date_of_birth, passport_no, gender, nationality,
                    address, city, country,
                    emergency_contact_name, emergency_contact_phone,
                    current_rank_id, status, 
                    source, candidate_id, approved_at,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'standby', 'recruitment', ?, NOW(), NOW())
            ");

            // Map position to rank (default to 1 for now, can be enhanced later)
            $rankId = 1;

            $stmt->bind_param(
                'sssssssssssssii',
                $employeeId,
                $application['full_name'],
                $application['email'],
                $application['phone'],
                $application['date_of_birth'],
                $application['passport_no'],
                $application['gender'],
                $application['nationality'],
                $application['address'],
                $application['city'],
                $application['country'],
                $application['emergency_contact_name'],
                $application['emergency_contact_phone'],
                $rankId,
                $applicationId
            );
            $stmt->execute();
            $crewId = $this->db->insert_id;
            $stmt->close();

            // Log sync with complete tracking
            $stmt = $this->db->prepare("
                INSERT INTO recruitment_sync 
                (recruitment_applicant_id, crew_id, sync_status, synced_at, created_at)
                VALUES (?, ?, 'onboarded', NOW(), NOW())
            ");
            $stmt->bind_param('ii', $applicationId, $crewId);
            $stmt->execute();
            $stmt->close();

            // Update candidate in recruitment DB to mark as synced
            $updateStmt = $this->recruitmentDb->prepare("
                UPDATE users SET is_synced_to_erp = 1 WHERE id = ?
            ");
            $updateStmt->bind_param('i', $application['user_id']);
            $updateStmt->execute();
            $updateStmt->close();

            $this->db->commit();

            $this->setFlash('success', "✅ Berhasil import {$application['full_name']} sebagai crew dengan ID: {$employeeId}");
            $this->redirect('crews');
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Onboarding error: " . $e->getMessage());
            $this->setFlash('error', 'Gagal onboard crew: ' . $e->getMessage());
            $this->redirect('recruitment/onboarding');
        }
    }

    /**
     * Create recruitment_sync table if not exists
     */
    private function createRecruitmentSyncTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS recruitment_sync (
                id INT PRIMARY KEY AUTO_INCREMENT,
                recruitment_applicant_id INT NOT NULL,
                crew_id INT,
                contract_id INT,
                sync_status ENUM('pending', 'synced', 'onboarded', 'failed') DEFAULT 'pending',
                synced_at TIMESTAMP NULL,
                error_message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $this->db->query($sql);
    }

    public function __destruct()
    {
        if ($this->recruitmentDb) {
            $this->recruitmentDb->close();
        }
    }
}
