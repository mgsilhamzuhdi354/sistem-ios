<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Master Admin Pipeline Controller
 * Full pipeline control with assignment management
 */
class Pipeline extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $filterCrewingId = $this->input('crewing');
        $filterUnassigned = $this->input('unassigned') === '1';
        
        // Get all statuses
        $statusResult = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order");
        $statuses = $statusResult ? $statusResult->fetch_all(MYSQLI_ASSOC) : [];
        
        // Get pipeline data for each status
        $pipeline = [];
        foreach ($statuses as $status) {
            $query = "
                SELECT a.*, u.full_name as applicant_name, u.email as applicant_email, u.avatar,
                       jv.title as vacancy_title, d.name as department_name, d.color as dept_color,
                       aa.id as assignment_id, aa.assigned_to as crewing_id, 
                       aa.assigned_at, aa.notes as assignment_notes,
                       uc.full_name as crewing_name,
                       DATEDIFF(NOW(), COALESCE(a.status_updated_at, a.submitted_at)) as days_in_status
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies jv ON a.vacancy_id = jv.id
                LEFT JOIN departments d ON jv.department_id = d.id
                LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                LEFT JOIN users uc ON aa.assigned_to = uc.id
                WHERE a.status_id = ?
            ";
            
            $params = [$status['id']];
            $types = 'i';
            
            // Apply filters
            if ($filterCrewingId) {
                $query .= " AND aa.assigned_to = ?";
                $params[] = intval($filterCrewingId);
                $types .= 'i';
            } elseif ($filterUnassigned) {
                $query .= " AND aa.id IS NULL";
            }
            
            $query .= " ORDER BY 
                CASE a.priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'normal' THEN 3 
                    WHEN 'low' THEN 4 
                END,
                a.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $pipeline[$status['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get crewing staff for dropdown
        $crewingStaff = $this->getAllCrewingAndPIC();
        
        // Get stats
        $stats = $this->getStats();
        
        $this->view('master_admin/pipeline/index', [
            'pageTitle' => 'Recruitment Pipeline',
            'statuses' => $statuses,
            'pipeline' => $pipeline,
            'crewingStaff' => $crewingStaff,
            'stats' => $stats,
            'filterCrewingId' => $filterCrewingId,
            'filterUnassigned' => $filterUnassigned
        ]);
    }
    
    /**
     * Get all Crewing and Crewing PIC staff with workload
     */
    private function getAllCrewingAndPIC() {
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
    private function getStats() {
        $stats = [];
        
        // Total applications
        $stats['total'] = $this->db->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
        
        // Assigned applications
        $stats['assigned'] = $this->db->query("
            SELECT COUNT(DISTINCT a.id) as count 
            FROM applications a
            JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
        ")->fetch_assoc()['count'];
        
        // Unassigned applications
        $stats['unassigned'] = $stats['total'] - $stats['assigned'];
        
        // Applications by crewing
        $crewingStats = $this->db->query("
            SELECT u.id, u.full_name, COUNT(aa.id) as count
            FROM users u
            LEFT JOIN application_assignments aa ON u.id = aa.assigned_to AND aa.status = 'active'
            WHERE u.role_id = 5 AND u.is_active = 1
            GROUP BY u.id
            ORDER BY count DESC
            LIMIT 5
        ");
        $stats['by_crewing'] = $crewingStats ? $crewingStats->fetch_all(MYSQLI_ASSOC) : [];
        
        return $stats;
    }
    
    /**
     * Assign an application to a crewing staff
     */
    public function assignApplication() {
        if (!$this->isPost()) {
            redirect(url('/master-admin/pipeline'));
        }
        
        $applicationId = intval($this->input('application_id'));
        $assignTo = intval($this->input('assign_to'));
        $notes = trim($this->input('notes') ?: '');
        $adminId = $_SESSION['user_id'];
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if (!$applicationId || !$assignTo) {
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => 'Invalid request']);
            }
            flash('error', 'Invalid request');
            redirect(url('/master-admin/pipeline'));
        }
        
        // Get application info
        $stmt = $this->db->prepare("SELECT id, user_id FROM applications WHERE id = ?");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => 'Application not found']);
            }
            flash('error', 'Application not found');
            redirect(url('/master-admin/pipeline'));
        }
        
        // Get current assignment if exists
        $currentStmt = $this->db->prepare("SELECT id, assigned_to FROM application_assignments WHERE application_id = ? AND status = 'active'");
        $currentStmt->bind_param('i', $applicationId);
        $currentStmt->execute();
        $currentAssignment = $currentStmt->get_result()->fetch_assoc();
        
        // Mark current as transferred
        if ($currentAssignment) {
            $this->db->query("UPDATE application_assignments SET status = 'transferred' WHERE id = " . $currentAssignment['id']);
        }
        
        // Create new assignment
        $assignNotes = $notes ?: 'Assigned by Master Admin';
        $stmt = $this->db->prepare("
            INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status)
            VALUES (?, ?, ?, ?, 'active')
        ");
        $stmt->bind_param('iiis', $applicationId, $assignTo, $adminId, $assignNotes);
        
        if ($stmt->execute()) {
            // Update application
            $updateStmt = $this->db->prepare("UPDATE applications SET current_crewing_id = ? WHERE id = ?");
            $updateStmt->bind_param('ii', $assignTo, $applicationId);
            $updateStmt->execute();
            
            // Log automation
            logAutomation('assignment', 'applications', $applicationId, 'master_admin_assign', [
                'assigned_to' => $assignTo,
                'assigned_by' => $adminId,
                'notes' => $notes,
                'previous_crewing' => $currentAssignment ? $currentAssignment['assigned_to'] : null
            ]);
            
            // Notify the assigned crewing
            $crewingName = $this->db->query("SELECT full_name FROM users WHERE id = $assignTo")->fetch_assoc()['full_name'];
            notifyUser($assignTo, 'Application Assigned to You', 
                'An application has been assigned to you by Master Admin. ' . ($notes ? "Notes: $notes" : ''),
                'info', url('/crewing/applications/' . $applicationId));
            
            if ($isAjax) {
                return $this->json([
                    'success' => true, 
                    'message' => 'Application assigned successfully',
                    'crewing_name' => $crewingName
                ]);
            }
            
            flash('success', 'Application assigned to ' . $crewingName);
        } else {
            if ($isAjax) {
                return $this->json(['success' => false, 'message' => 'Failed to assign application']);
            }
            flash('error', 'Failed to assign application');
        }
        
        redirect(url('/master-admin/pipeline'));
    }
    
    /**
     * Bulk assign multiple applications
     */
    public function bulkAssign() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationIds = $this->input('application_ids');
        $assignTo = intval($this->input('assign_to'));
        $notes = trim($this->input('notes') ?: '');
        $adminId = $_SESSION['user_id'];
        
        if (empty($applicationIds) || !$assignTo) {
            return $this->json(['success' => false, 'message' => 'Please select applications and a crewing staff']);
        }
        
        // Parse application IDs
        if (is_string($applicationIds)) {
            $applicationIds = json_decode($applicationIds, true) ?: explode(',', $applicationIds);
        }
        
        $successCount = 0;
        $failCount = 0;
        
        foreach ($applicationIds as $appId) {
            $appId = intval($appId);
            if (!$appId) continue;
            
            // Mark current as transferred
            $this->db->query("UPDATE application_assignments SET status = 'transferred' WHERE application_id = $appId AND status = 'active'");
            
            // Create new assignment
            $assignNotes = $notes ?: 'Bulk assigned by Master Admin';
            $stmt = $this->db->prepare("
                INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status)
                VALUES (?, ?, ?, ?, 'active')
            ");
            $stmt->bind_param('iiis', $appId, $assignTo, $adminId, $assignNotes);
            
            if ($stmt->execute()) {
                // Update application
                $this->db->query("UPDATE applications SET current_crewing_id = $assignTo WHERE id = $appId");
                $successCount++;
            } else {
                $failCount++;
            }
        }
        
        // Log automation
        logAutomation('assignment', 'applications', 0, 'master_admin_bulk_assign', [
            'assigned_to' => $assignTo,
            'assigned_by' => $adminId,
            'count' => $successCount,
            'notes' => $notes
        ]);
        
        // Send single notification for bulk assign
        notifyUser($assignTo, 'Applications Bulk Assigned', 
            "$successCount applications have been assigned to you by Master Admin.",
            'info', url('/crewing/applications'));
        
        return $this->json([
            'success' => $successCount > 0,
            'message' => "$successCount applications assigned successfully" . ($failCount > 0 ? ", $failCount failed" : ''),
            'success_count' => $successCount,
            'fail_count' => $failCount
        ]);
    }
    
    /**
     * Get crewing staff list for AJAX dropdown
     */
    public function getCrewingStaffAjax() {
        $crewingStaff = $this->getAllCrewingAndPIC();
        return $this->json(['success' => true, 'data' => $crewingStaff]);
    }
    
    public function updateStatus() {
        if (!$this->isPost()) {
            redirect(url('/master-admin/pipeline'));
        }
        
        $applicationId = intval($this->input('application_id'));
        $newStatusId = intval($this->input('status_id'));
        $adminId = $_SESSION['user_id'];
        
        // Get current status
        $stmt = $this->db->prepare("SELECT status_id, user_id FROM applications WHERE id = ?");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            flash('error', 'Application not found');
            redirect(url('/master-admin/pipeline'));
        }
        
        $oldStatusId = $app['status_id'];
        
        // Update status
        $updateStmt = $this->db->prepare("UPDATE applications SET status_id = ?, status_updated_at = NOW(), reviewed_by = ? WHERE id = ?");
        $updateStmt->bind_param('iii', $newStatusId, $adminId, $applicationId);
        
        if ($updateStmt->execute()) {
            // Add history
            $historyStmt = $this->db->prepare("INSERT INTO application_status_history (application_id, from_status_id, to_status_id, notes, changed_by, created_at) VALUES (?, ?, ?, 'Updated by Master Admin', ?, NOW())");
            $historyStmt->bind_param('iiii', $applicationId, $oldStatusId, $newStatusId, $adminId);
            $historyStmt->execute();
            
            flash('success', 'Status updated successfully');
        } else {
            flash('error', 'Failed to update status');
        }
        
        redirect(url('/master-admin/pipeline'));
    }
    
    /**
     * Transfer responsibility from one crewing to another
     * Includes detailed history tracking
     */
    public function transferResponsibility() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationId = intval($this->input('application_id'));
        $fromCrewingId = intval($this->input('from_crewing_id'));
        $toCrewingId = intval($this->input('to_crewing_id'));
        $reason = trim($this->input('reason') ?: '');
        $adminId = $_SESSION['user_id'];
        
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
        
        // Get crewing names for notification
        $fromCrewingName = 'Unassigned';
        $toCrewingName = '';
        
        if ($fromCrewingId > 0) {
            $fromResult = $this->db->query("SELECT full_name FROM users WHERE id = $fromCrewingId");
            $fromCrewingName = $fromResult ? $fromResult->fetch_assoc()['full_name'] : 'Unknown';
        }
        
        $toResult = $this->db->query("SELECT full_name FROM users WHERE id = $toCrewingId");
        $toCrewingName = $toResult ? $toResult->fetch_assoc()['full_name'] : 'Unknown';
        
        // Record transfer in handler_transfers table
        $transferStmt = $this->db->prepare("
            INSERT INTO handler_transfers (application_id, from_crewing_id, to_crewing_id, transferred_by, reason, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $transferStmt->bind_param('iiiis', $applicationId, $fromCrewingId, $toCrewingId, $adminId, $reason);
        
        if (!$transferStmt->execute()) {
            return $this->json(['success' => false, 'message' => 'Failed to record transfer']);
        }
        
        // Update application_assignments - mark old as transferred
        if ($fromCrewingId > 0) {
            $this->db->query("UPDATE application_assignments SET status = 'transferred' WHERE application_id = $applicationId AND status = 'active'");
        }
        
        // Create new assignment
        $transferNotes = "Transferred from $fromCrewingName. " . ($reason ? "Reason: $reason" : '');
        $assignStmt = $this->db->prepare("
            INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status)
            VALUES (?, ?, ?, ?, 'active')
        ");
        $assignStmt->bind_param('iiis', $applicationId, $toCrewingId, $adminId, $transferNotes);
        $assignStmt->execute();
        
        // Update applications table
        $this->db->query("UPDATE applications SET current_crewing_id = $toCrewingId WHERE id = $applicationId");
        
        // Get admin name for notification
        $adminName = $this->db->query("SELECT full_name FROM users WHERE id = $adminId")->fetch_assoc()['full_name'];
        
        // Notify the new crewing
        notifyUser($toCrewingId, 'Application Transferred to You', 
            "Applicant \"{$app['applicant_name']}\" for \"{$app['vacancy_title']}\" has been transferred to you from $fromCrewingName by $adminName." . ($reason ? " Reason: $reason" : ''),
            'info', url('/crewing/applications/' . $applicationId));
        
        // Notify the old crewing (if exists)
        if ($fromCrewingId > 0) {
            notifyUser($fromCrewingId, 'Application Transferred Away', 
                "Applicant \"{$app['applicant_name']}\" for \"{$app['vacancy_title']}\" has been transferred to $toCrewingName by $adminName." . ($reason ? " Reason: $reason" : ''),
                'warning', url('/crewing/applications'));
        }
        
        // Log automation
        logAutomation('transfer', 'handler_transfers', $applicationId, 'responsibility_transfer', [
            'from_crewing_id' => $fromCrewingId,
            'from_crewing_name' => $fromCrewingName,
            'to_crewing_id' => $toCrewingId,
            'to_crewing_name' => $toCrewingName,
            'transferred_by' => $adminId,
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
     * Get transfer history for an application (AJAX)
     */
    public function getTransferHistory() {
        $applicationId = intval($this->input('application_id'));
        
        if (!$applicationId) {
            return $this->json(['success' => false, 'message' => 'Invalid application ID']);
        }
        
        // Get transfer history
        $historyResult = $this->db->query("
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
        $historyResult->bind_param('i', $applicationId);
        
        // Use prepared statement properly
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
     * Get application detail with current assignment info (AJAX)
     */
    public function getApplicationDetail() {
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
        
        // Get transfer count
        $transferCount = $this->db->query("SELECT COUNT(*) as count FROM handler_transfers WHERE application_id = $applicationId")->fetch_assoc()['count'];
        $app['transfer_count'] = $transferCount;
        
        return $this->json(['success' => true, 'application' => $app]);
    }
}
