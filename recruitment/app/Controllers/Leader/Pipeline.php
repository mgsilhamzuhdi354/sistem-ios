<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Leader Pipeline Controller
 * With Transfer Responsibility Feature
 */
class Pipeline extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if (!isLoggedIn() || (!isLeader() && !isMasterAdmin())) {
            redirect('/login');
        }
    }

    public function index()
    {
        // Get all statuses
        $statusResult = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order");
        $statuses = $statusResult ? $statusResult->fetch_all(MYSQLI_ASSOC) : [];

        // Get all crewing for filter
        $crewingStaff = $this->getAllCrewingAndPIC();

        // Get filter parameters
        $crewingFilter = isset($_GET['crewing']) ? (int) $_GET['crewing'] : 0;
        $showUnassigned = isset($_GET['unassigned']) && $_GET['unassigned'] == '1';

        // Get applications grouped by status
        $pipeline = [];
        foreach ($statuses as $status) {
            $sql = "
                SELECT a.*, 
                       u.full_name as applicant_name, u.email, u.avatar,
                       jv.title as vacancy_title,
                       d.name as department_name, d.color as dept_color,
                       aa.assigned_to as crewing_id, aa.assigned_at,
                       uc.full_name as crewing_name, uc.id as current_crewing_id,
                       cp.rank as crewing_rank, cp.company as crewing_company,
                       DATEDIFF(NOW(), COALESCE(a.status_updated_at, a.submitted_at)) as days_in_status,
                       (SELECT COUNT(*) FROM handler_transfers WHERE application_id = a.id) as transfer_count
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies jv ON a.vacancy_id = jv.id
                LEFT JOIN departments d ON jv.department_id = d.id
                LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                LEFT JOIN users uc ON aa.assigned_to = uc.id
                LEFT JOIN crewing_profiles cp ON uc.id = cp.user_id
                WHERE a.status_id = {$status['id']}
            ";

            if ($crewingFilter > 0) {
                $sql .= " AND aa.assigned_to = {$crewingFilter}";
            } elseif ($showUnassigned) {
                $sql .= " AND aa.id IS NULL";
            }

            $sql .= " ORDER BY 
                CASE a.priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'normal' THEN 3 
                    WHEN 'low' THEN 4 
                END,
                a.created_at ASC LIMIT 50";

            $result = $this->db->query($sql);
            $pipeline[$status['id']] = [
                'status' => $status,
                'applications' => $result ? $result->fetch_all(MYSQLI_ASSOC) : []
            ];
        }

        // Get stats
        $stats = $this->getStats();

        $this->view('leader/pipeline/index', [
            'pageTitle' => 'Pipeline Management',
            'pipeline' => $pipeline,
            'statuses' => $statuses,
            'crewingStaff' => $crewingStaff,
            'crewingFilter' => $crewingFilter,
            'showUnassigned' => $showUnassigned,
            'stats' => $stats
        ]);
    }

    /**
     * Get all Crewing and Crewing PIC staff with workload
     */
    private function getAllCrewingAndPIC()
    {
        $result = $this->db->query("
            SELECT u.id, u.full_name, u.email, u.role_id, r.name as role_name,
                   cp.employee_id, cp.max_applications,
                   COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_assignments
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            WHERE u.role_id = 5 
            AND u.is_active = 1
            GROUP BY u.id
            ORDER BY r.name, u.full_name
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Get pipeline stats
     */
    private function getStats()
    {
        $stats = [];

        $stats['total'] = $this->db->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];

        $stats['assigned'] = $this->db->query("
            SELECT COUNT(DISTINCT a.id) as count 
            FROM applications a
            JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
        ")->fetch_assoc()['count'];

        $stats['unassigned'] = $stats['total'] - $stats['assigned'];

        $stats['transfers_today'] = $this->db->query("
            SELECT COUNT(*) as count FROM handler_transfers WHERE DATE(created_at) = CURDATE()
        ")->fetch_assoc()['count'];

        return $stats;
    }

    /**
     * Get crewing staff list for AJAX dropdown
     */
    public function getCrewingStaffAjax()
    {
        $result = $this->db->query("
            SELECT u.id, u.full_name, u.email, u.role_id, r.name as role_name,
                   cp.employee_id, cp.max_applications,
                   COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_assignments
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
            WHERE u.role_id = 5 
            AND u.is_active = 1
            GROUP BY u.id
            ORDER BY r.name, u.full_name
        ");
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        return $this->json(['success' => true, 'data' => $data]);
    }

    public function updateStatusAjax()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $applicationId = $this->input('application_id');
        $newStatusId = $this->input('status_id');

        // If Master Admin, allow direct update
        if (isMasterAdmin()) {
            $stmt = $this->db->prepare("UPDATE applications SET status_id = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param('ii', $newStatusId, $applicationId);

            if ($stmt->execute()) {
                $this->notifyStatusChange($applicationId, $newStatusId);
                return $this->json(['success' => true, 'message' => 'Status updated successfully']);
            }
            return $this->json(['success' => false, 'message' => 'Failed to update status']);
        }

        // If Leader, create a request instead (even with pipeline_manage permission)
        if (isLeader()) {
            // Get current status
            $currStmt = $this->db->prepare("SELECT status_id FROM applications WHERE id = ?");
            $currStmt->bind_param('i', $applicationId);
            $currStmt->execute();
            $currentApp = $currStmt->get_result()->fetch_assoc();

            if (!$currentApp) {
                return $this->json(['success' => false, 'message' => 'Application not found']);
            }

            $fromStatusId = $currentApp['status_id'];

            // Check if there is already a pending request
            $checkStmt = $this->db->prepare("SELECT id FROM status_change_requests WHERE application_id = ? AND status = 'pending'");
            $checkStmt->bind_param('i', $applicationId);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                return $this->json(['success' => false, 'message' => 'There is already a pending status change request for this applicant']);
            }

            // Create Request
            $userId = $_SESSION['user_id'];
            $reqStmt = $this->db->prepare("
                INSERT INTO status_change_requests 
                (application_id, requested_by, from_status_id, to_status_id, status, created_at)
                VALUES (?, ?, ?, ?, 'pending', NOW())
            ");
            $reqStmt->bind_param('iiii', $applicationId, $userId, $fromStatusId, $newStatusId);

            if ($reqStmt->execute()) {
                // Notify Master Admin
                // Assuming Master Admin role ID is 11
                // We can find all master admins
                $admins = $this->db->query("SELECT id FROM users WHERE role_id = 11 AND is_active = 1")->fetch_all(MYSQLI_ASSOC);
                foreach ($admins as $admin) {
                    notifyUser(
                        $admin['id'],
                        'Approval Needed: Status Change',
                        'A Leader has requested to change an applicant status.',
                        'warning',
                        url('/master-admin/requests')
                    );
                }

                return $this->json(['success' => true, 'message' => 'Permintaan perubahan status dikirim ke Master Admin untuk disetujui.']);
            }

            return $this->json(['success' => false, 'message' => 'Failed to create request']);
        }

        return $this->json(['success' => false, 'message' => 'Unauthorized']);
    }

    private function notifyStatusChange($applicationId, $newStatusId)
    {
        // Get applicant for notification
        $appStmt = $this->db->prepare("SELECT user_id FROM applications WHERE id = ?");
        $appStmt->bind_param('i', $applicationId);
        $appStmt->execute();
        $app = $appStmt->get_result()->fetch_assoc();

        // Get status name
        $statusStmt = $this->db->prepare("SELECT name FROM application_statuses WHERE id = ?");
        $statusStmt->bind_param('i', $newStatusId);
        $statusStmt->execute();
        $status = $statusStmt->get_result()->fetch_assoc();

        // Notify applicant
        notifyUser(
            $app['user_id'],
            'Application Status Updated',
            'Your application status has been updated to: ' . $status['name'],
            'info',
            url('/applicant/applications')
        );

        // Auto-create interview session if status is Interview
        if ($newStatusId == 3) {
            $this->createInterviewSession($applicationId);
        }
    }

    /**
     * Transfer responsibility from one crewing to another
     */
    public function transferResponsibility()
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $applicationId = intval($this->input('application_id'));
        $fromCrewingId = intval($this->input('from_crewing_id'));
        $toCrewingId = intval($this->input('to_crewing_id'));
        $reason = trim($this->input('reason') ?: '');
        $leaderId = $_SESSION['user_id'];

        if (!$applicationId || !$toCrewingId) {
            return $this->json(['success' => false, 'message' => 'Please select target crewing staff']);
        }

        if ($fromCrewingId === $toCrewingId) {
            return $this->json(['success' => false, 'message' => 'Cannot transfer to the same person']);
        }

        // Get application and applicant info
        $stmt = $this->db->prepare("
            SELECT a.id, a.user_id, u.full_name as applicant_name, jv.title as vacancy_title
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();

        if (!$app) {
            return $this->json(['success' => false, 'message' => 'Application not found']);
        }

        // Get crewing names
        $fromCrewingName = 'Unassigned';
        $toCrewingName = '';

        if ($fromCrewingId > 0) {
            $fromResult = $this->db->query("SELECT full_name FROM users WHERE id = $fromCrewingId");
            $fromCrewingName = $fromResult ? $fromResult->fetch_assoc()['full_name'] : 'Unknown';
        }

        $toResult = $this->db->query("SELECT full_name FROM users WHERE id = $toCrewingId");
        $toCrewingName = $toResult ? $toResult->fetch_assoc()['full_name'] : 'Unknown';

        // Record transfer
        $transferStmt = $this->db->prepare("
            INSERT INTO handler_transfers (application_id, from_crewing_id, to_crewing_id, transferred_by, reason, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $transferStmt->bind_param('iiiis', $applicationId, $fromCrewingId, $toCrewingId, $leaderId, $reason);

        if (!$transferStmt->execute()) {
            return $this->json(['success' => false, 'message' => 'Failed to record transfer']);
        }

        // Update assignments
        if ($fromCrewingId > 0) {
            $this->db->query("UPDATE application_assignments SET status = 'transferred' WHERE application_id = $applicationId AND status = 'active'");
        }

        $transferNotes = "Transferred from $fromCrewingName by Leader. " . ($reason ? "Reason: $reason" : '');
        $assignStmt = $this->db->prepare("
            INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status)
            VALUES (?, ?, ?, ?, 'active')
        ");
        $assignStmt->bind_param('iiis', $applicationId, $toCrewingId, $leaderId, $transferNotes);
        $assignStmt->execute();

        // Update applications table
        $this->db->query("UPDATE applications SET current_crewing_id = $toCrewingId WHERE id = $applicationId");

        // Get leader name
        $leaderName = $this->db->query("SELECT full_name FROM users WHERE id = $leaderId")->fetch_assoc()['full_name'];

        // Notify the new crewing
        notifyUser(
            $toCrewingId,
            'Application Transferred to You',
            "Applicant \"{$app['applicant_name']}\" for \"{$app['vacancy_title']}\" has been transferred to you from $fromCrewingName by $leaderName." . ($reason ? " Reason: $reason" : ''),
            'info',
            url('/crewing/applications/' . $applicationId)
        );

        // Notify the old crewing
        if ($fromCrewingId > 0) {
            notifyUser(
                $fromCrewingId,
                'Application Transferred Away',
                "Applicant \"{$app['applicant_name']}\" for \"{$app['vacancy_title']}\" has been transferred to $toCrewingName by $leaderName." . ($reason ? " Reason: $reason" : ''),
                'warning',
                url('/crewing/applications')
            );
        }

        // Log
        logAutomation('transfer', 'handler_transfers', $applicationId, 'leader_transfer', [
            'from_crewing_id' => $fromCrewingId,
            'from_crewing_name' => $fromCrewingName,
            'to_crewing_id' => $toCrewingId,
            'to_crewing_name' => $toCrewingName,
            'transferred_by' => $leaderId,
            'reason' => $reason
        ]);

        return $this->json([
            'success' => true,
            'message' => "Successfully transferred from $fromCrewingName to $toCrewingName",
            'from_name' => $fromCrewingName,
            'to_name' => $toCrewingName
        ]);
    }

    /**
     * Get transfer history for an application
     */
    public function getTransferHistory()
    {
        $applicationId = intval($this->input('application_id'));

        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Invalid application ID']);
        }

        $stmt = $this->db->prepare("
            SELECT ht.*, 
                   uf.full_name as from_crewing_name, uf.email as from_crewing_email,
                   ut.full_name as to_crewing_name, ut.email as to_crewing_email,
                   utb.full_name as transferred_by_name,
                   DATE_FORMAT(ht.created_at, '%d %b %Y %H:%i') as transfer_date
            FROM handler_transfers ht
            LEFT JOIN users uf ON ht.from_crewing_id = uf.id
            JOIN users ut ON ht.to_crewing_id = ut.id
            JOIN users utb ON ht.transferred_by = utb.id
            WHERE ht.application_id = ?
            ORDER BY ht.created_at DESC
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $this->json([
            'success' => true,
            'history' => $history,
            'count' => count($history)
        ]);
    }

    /**
     * Get application detail
     */
    public function getApplicationDetail()
    {
        $applicationId = intval($this->input('application_id'));

        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Invalid application ID']);
        }

        $stmt = $this->db->prepare("
            SELECT a.*, u.full_name as applicant_name, u.email as applicant_email, u.avatar,
                   jv.title as vacancy_title, d.name as department_name,
                   aa.assigned_to as current_crewing_id, aa.assigned_at,
                   uc.full_name as current_crewing_name,
                   uab.full_name as assigned_by_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN departments d ON jv.department_id = d.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            LEFT JOIN users uc ON aa.assigned_to = uc.id
            LEFT JOIN users uab ON aa.assigned_by = uab.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();

        if (!$app) {
            return $this->json(['success' => false, 'message' => 'Application not found']);
        }

        $transferCount = $this->db->query("SELECT COUNT(*) as count FROM handler_transfers WHERE application_id = $applicationId")->fetch_assoc()['count'];
        $app['transfer_count'] = $transferCount;

        return $this->json(['success' => true, 'application' => $app]);
    }

    private function createInterviewSession($applicationId)
    {
        $stmt = $this->db->prepare("
            SELECT a.vacancy_id, jv.department_id 
            FROM applications a 
            JOIN job_vacancies jv ON a.vacancy_id = jv.id 
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();

        if ($app) {
            $bankStmt = $this->db->prepare("
                SELECT id FROM interview_question_banks 
                WHERE (department_id = ? OR department_id IS NULL) 
                AND is_active = 1 
                LIMIT 1
            ");
            $bankStmt->bind_param('i', $app['department_id']);
            $bankStmt->execute();
            $bank = $bankStmt->get_result()->fetch_assoc();

            if ($bank) {
                $expiryDate = date('Y-m-d H:i:s', strtotime('+7 days'));
                $insertStmt = $this->db->prepare("
                    INSERT INTO interview_sessions (application_id, question_bank_id, status, expires_at, created_at)
                    VALUES (?, ?, 'pending', ?, NOW())
                ");
                $insertStmt->bind_param('iis', $applicationId, $bank['id'], $expiryDate);
                $insertStmt->execute();
            }
        }
    }
}
