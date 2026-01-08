<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Pipeline Controller (Kanban View)
 */
class Pipeline extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $crewingId = $_SESSION['user_id'];
        $filterCrewingId = $this->input('crewing');
        $view = $this->input('view', 'my'); // 'my', 'team', 'all'
        
        // Get all statuses
        $statuses = $this->db->query("
            SELECT * FROM application_statuses ORDER BY sort_order
        ")->fetch_all(MYSQLI_ASSOC);
        
        $pipeline = [];
        
        foreach ($statuses as $status) {
            $query = "
                SELECT a.*, u.full_name, u.avatar, v.title as vacancy_title,
                       d.name as department_name, d.color as dept_color,
                       DATEDIFF(NOW(), COALESCE(a.status_updated_at, a.submitted_at)) as days_in_status,
                       aa.assigned_to as crewing_id,
                       assigned_user.full_name as crewing_name
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                LEFT JOIN users assigned_user ON aa.assigned_to = assigned_user.id
                WHERE a.status_id = ?
            ";
            
            $params = [$status['id']];
            $types = 'i';
            
            // Apply view filter
            if ($view === 'my') {
                $query .= " AND aa.assigned_to = ?";
                $params[] = $crewingId;
                $types .= 'i';
            } elseif ($view === 'team' && $filterCrewingId) {
                $query .= " AND aa.assigned_to = ?";
                $params[] = intval($filterCrewingId);
                $types .= 'i';
            }
            
            $query .= " ORDER BY 
                CASE a.priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'normal' THEN 3 
                    WHEN 'low' THEN 4 
                END,
                COALESCE(a.status_updated_at, a.submitted_at)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            
            $pipeline[$status['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        // Get crewing staff for filter
        $crewingStaff = getAllCrewingStaff();
        
        $this->view('crewing/pipeline/index', [
            'pipeline' => $pipeline,
            'statuses' => $statuses,
            'crewingStaff' => $crewingStaff,
            'currentView' => $view,
            'filterCrewingId' => $filterCrewingId,
            'pageTitle' => 'Recruitment Pipeline'
        ]);
    }
    
    public function updateStatusAjax() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        $applicationId = intval($this->input('application_id'));
        $newStatusId = intval($this->input('status_id'));
        $reason = $this->input('reason', '');
        $crewingId = $_SESSION['user_id'];
        
        // Get current status
        $stmt = $this->db->prepare("SELECT status_id, user_id FROM applications WHERE id = ?");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            return $this->json(['success' => false, 'message' => 'Application not found']);
        }
        
        $oldStatusId = $app['status_id'];
        
        // Leader, Crewing PIC, Master Admin can update directly
        if (isLeaderOrAbove() || isCrewingPIC()) {
            // Direct update
            $updateStmt = $this->db->prepare("
                UPDATE applications SET status_id = ?, status_updated_at = NOW(), reviewed_by = ?
                WHERE id = ?
            ");
            $updateStmt->bind_param('iii', $newStatusId, $crewingId, $applicationId);
            
            if ($updateStmt->execute()) {
                // Add history
                $historyStmt = $this->db->prepare("
                    INSERT INTO application_status_history (application_id, from_status_id, to_status_id, notes, changed_by, created_at)
                    VALUES (?, ?, ?, 'Status updated via pipeline', ?, NOW())
                ");
                $historyStmt->bind_param('iiii', $applicationId, $oldStatusId, $newStatusId, $crewingId);
                $historyStmt->execute();
                
                // Auto-assign interview if moving to Interview status
                if ($newStatusId == 3 && $oldStatusId != 3) {
                    $this->autoAssignInterviewAjax($applicationId, $app['user_id']);
                }
                
                // Notify applicant
                if (getSetting('auto_notify_status_change', 'true') === 'true') {
                    $statusName = $this->db->query("SELECT name FROM application_statuses WHERE id = $newStatusId")->fetch_assoc()['name'];
                    notifyUser($app['user_id'], 'Application Status Updated',
                        'Your application status has been updated to: ' . $statusName,
                        'info', url('/applicant/applications/' . $applicationId));
                }
                
                return $this->json([
                    'success' => true, 
                    'message' => 'Status updated',
                    'new_status_id' => $newStatusId
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Failed to update status']);
            }
        } else {
            // Crewing staff must REQUEST approval
            $result = createPipelineRequest($applicationId, $oldStatusId, $newStatusId, $reason);
            
            if ($result) {
                return $this->json([
                    'success' => true, 
                    'message' => 'Request submitted! Waiting for approval from PIC/Leader.',
                    'request_created' => true
                ]);
            } else {
                return $this->json(['success' => false, 'message' => 'Failed to create request. Please try again.']);
            }
        }
    }
    
    private function autoAssignInterviewAjax($applicationId, $userId) {
        $checkInterview = $this->db->prepare("SELECT id FROM interview_sessions WHERE application_id = ?");
        $checkInterview->bind_param('i', $applicationId);
        $checkInterview->execute();
        
        if ($checkInterview->get_result()->fetch_assoc()) {
            return;
        }
        
        $bankResult = $this->db->query("SELECT id FROM interview_question_banks WHERE is_active = 1 ORDER BY id LIMIT 1");
        $bank = $bankResult->fetch_assoc();
        
        if ($bank) {
            $expiryDays = intval(getSetting('interview_expiry_days', '7'));
            $interviewStmt = $this->db->prepare("
                INSERT INTO interview_sessions (application_id, question_bank_id, status, expires_at, created_at)
                VALUES (?, ?, 'pending', DATE_ADD(NOW(), INTERVAL ? DAY), NOW())
            ");
            $interviewStmt->bind_param('iii', $applicationId, $bank['id'], $expiryDays);
            $interviewStmt->execute();
            
            notifyUser($userId, 'AI Interview Assigned',
                'You have been assigned an AI interview. Please complete it within ' . $expiryDays . ' days.',
                'success', url('/applicant/interview'));
        }
    }
    
    /**
     * Handle form-based status change request
     */
    public function requestStatus() {
        if (!$this->isPost()) {
            redirect(url('/crewing/pipeline'));
        }
        
        $applicationId = intval($this->input('application_id'));
        $toStatusId = intval($this->input('to_status_id'));
        $reason = trim($this->input('reason'));
        $userId = $_SESSION['user_id'];
        
        if (empty($reason)) {
            flash('error', 'Please provide a reason for the status change request');
            redirect(url('/crewing/pipeline'));
        }
        
        // Get current status
        $stmt = $this->db->prepare("SELECT status_id, user_id FROM applications WHERE id = ?");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            flash('error', 'Application not found');
            redirect(url('/crewing/pipeline'));
        }
        
        $fromStatusId = $app['status_id'];
        
        // Get assigned crewing (if any)
        $assignStmt = $this->db->prepare("SELECT assigned_to FROM application_assignments WHERE application_id = ? AND status = 'active' LIMIT 1");
        $assignStmt->bind_param('i', $applicationId);
        $assignStmt->execute();
        $assignment = $assignStmt->get_result()->fetch_assoc();
        $assignedTo = $assignment ? $assignment['assigned_to'] : $userId;
        
        // Create the request
        $requestStmt = $this->db->prepare("
            INSERT INTO pipeline_requests (application_id, requested_by, assigned_to, from_status_id, to_status_id, reason, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $requestStmt->bind_param('iiiiis', $applicationId, $userId, $assignedTo, $fromStatusId, $toStatusId, $reason);
        
        if ($requestStmt->execute()) {
            flash('success', 'Status change request has been submitted for approval');
        } else {
            flash('error', 'Failed to submit request. Please try again.');
        }
        
        redirect(url('/crewing/pipeline'));
    }
}
