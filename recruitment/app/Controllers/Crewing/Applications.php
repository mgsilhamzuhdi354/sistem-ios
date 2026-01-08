<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Applications Controller
 */
class Applications extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $crewingId = $_SESSION['user_id'];
        $status = $this->input('status');
        $department = $this->input('department');
        $priority = $this->input('priority');
        $search = $this->input('search');
        $view = $this->input('view', 'my'); // 'my' or 'all'
        
        $query = "
            SELECT a.*, u.full_name, u.email, u.phone, u.avatar,
                   v.title as vacancy_title, d.name as department_name,
                   s.name as status_name, s.color as status_color,
                   ap.profile_completion,
                   aa.assigned_at, aa.notes as assignment_notes,
                   assigned_user.full_name as assigned_to_name,
                   assigned_user.is_online as crewing_online,
                   assigned_user.last_activity as crewing_last_activity,
                   assigner.full_name as assigned_by_name,
                   cp.rank as crewing_rank, cp.company as crewing_company
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            LEFT JOIN users assigned_user ON aa.assigned_to = assigned_user.id
            LEFT JOIN crewing_profiles cp ON assigned_user.id = cp.user_id
            LEFT JOIN users assigner ON aa.assigned_by = assigner.id
            WHERE 1=1
        ";
        
        // Filter by my assignments or all
        if ($view === 'my') {
            $query .= " AND aa.assigned_to = " . intval($crewingId);
        }
        
        if ($status) {
            $query .= " AND a.status_id = " . intval($status);
        }
        if ($department) {
            $query .= " AND v.department_id = " . intval($department);
        }
        if ($priority) {
            $query .= " AND a.priority = '" . $this->db->real_escape_string($priority) . "'";
        }
        if ($search) {
            $searchEsc = $this->db->real_escape_string($search);
            $query .= " AND (u.full_name LIKE '%$searchEsc%' OR u.email LIKE '%$searchEsc%' OR v.title LIKE '%$searchEsc%')";
        }
        
        $query .= " ORDER BY a.submitted_at DESC";
        
        $applications = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        
        // Get filter options
        $statuses = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        // Get all crewing for assignment dropdown
        $crewingStaff = getAllCrewingStaff();
        
        // Get status counts for my assignments
        $statusCounts = $this->db->query("
            SELECT s.id, s.name, s.color, COUNT(a.id) as count
            FROM application_statuses s
            LEFT JOIN applications a ON s.id = a.status_id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE aa.assigned_to = $crewingId OR (aa.assigned_to IS NULL AND '$view' = 'all')
            GROUP BY s.id
            ORDER BY s.sort_order
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/applications/index', [
            'applications' => $applications,
            'statuses' => $statuses,
            'statusCounts' => $statusCounts,
            'departments' => $departments,
            'crewingStaff' => $crewingStaff,
            'filters' => compact('status', 'department', 'priority', 'search', 'view'),
            'pageTitle' => 'My Applications'
        ]);
    }
    
    public function detail($id) {
        $crewingId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT a.*, u.full_name, u.email, u.phone, u.avatar, u.created_at as user_created,
                   v.title as vacancy_title, v.description as vacancy_description,
                   v.salary_min, v.salary_max, v.requirements,
                   d.name as department_name, s.name as status_name, s.color as status_color,
                   ap.*,
                   aa.id as assignment_id, aa.assigned_at, aa.notes as assignment_notes,
                   assigned_user.full_name as assigned_to_name,
                   assigner.full_name as assigned_by_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            LEFT JOIN users assigned_user ON aa.assigned_to = assigned_user.id
            LEFT JOIN users assigner ON aa.assigned_by = assigner.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $application = $stmt->get_result()->fetch_assoc();
        
        if (!$application) {
            flash('error', 'Application not found');
            $this->redirect(url('/crewing/applications'));
        }
        
        // Get documents
        $docStmt = $this->db->prepare("
            SELECT d.*, dt.name as type_name
            FROM documents d
            JOIN document_types dt ON d.document_type_id = dt.id
            WHERE d.user_id = ?
            ORDER BY dt.sort_order
        ");
        $docStmt->bind_param('i', $application['user_id']);
        $docStmt->execute();
        $documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get assignment history
        $assignmentHistory = $this->db->prepare("
            SELECT aa.*, 
                   assigned_user.full_name as assigned_to_name,
                   assigner.full_name as assigned_by_name
            FROM application_assignments aa
            LEFT JOIN users assigned_user ON aa.assigned_to = assigned_user.id
            LEFT JOIN users assigner ON aa.assigned_by = assigner.id
            WHERE aa.application_id = ?
            ORDER BY aa.assigned_at DESC
        ");
        $assignmentHistory->bind_param('i', $id);
        $assignmentHistory->execute();
        $assignmentHistory = $assignmentHistory->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get status history
        $historyStmt = $this->db->prepare("
            SELECT ash.*, fs.name as from_status, ts.name as to_status, ts.color as to_color,
                   u.full_name as changed_by_name
            FROM application_status_history ash
            LEFT JOIN application_statuses fs ON ash.from_status_id = fs.id
            JOIN application_statuses ts ON ash.to_status_id = ts.id
            LEFT JOIN users u ON ash.changed_by = u.id
            WHERE ash.application_id = ?
            ORDER BY ash.created_at DESC
        ");
        $historyStmt->bind_param('i', $id);
        $historyStmt->execute();
        $statusHistory = $historyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get interview sessions
        $interviewStmt = $this->db->prepare("
            SELECT is2.*, qb.name as question_bank_name
            FROM interview_sessions is2
            JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
            WHERE is2.application_id = ?
        ");
        $interviewStmt->bind_param('i', $id);
        $interviewStmt->execute();
        $interviews = $interviewStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get all statuses and crewing staff for dropdowns
        $statuses = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        $crewingStaff = getAllCrewingStaff();
        
        $this->view('crewing/applications/detail', [
            'application' => $application,
            'documents' => $documents,
            'assignmentHistory' => $assignmentHistory,
            'statusHistory' => $statusHistory,
            'interviews' => $interviews,
            'statuses' => $statuses,
            'crewingStaff' => $crewingStaff,
            'pageTitle' => 'Application Detail'
        ]);
    }
    
    public function assign($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/crewing/applications/' . $id));
        }
        
        validate_csrf();
        
        $assignTo = intval($this->input('assign_to'));
        $notes = $this->input('notes') ?: '';
        $crewingId = $_SESSION['user_id'];
        
        if (!$assignTo) {
            flash('error', 'Please select a crewing staff to assign');
            $this->redirect(url('/crewing/applications/' . $id));
        }
        
        // Get current assignment
        $currentStmt = $this->db->prepare("SELECT id FROM application_assignments WHERE application_id = ? AND status = 'active'");
        $currentStmt->bind_param('i', $id);
        $currentStmt->execute();
        $currentAssignment = $currentStmt->get_result()->fetch_assoc();
        
        // Mark current as transferred
        if ($currentAssignment) {
            $this->db->query("UPDATE application_assignments SET status = 'transferred' WHERE id = " . $currentAssignment['id']);
        }
        
        // Create new assignment
        $stmt = $this->db->prepare("
            INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status)
            VALUES (?, ?, ?, ?, 'active')
        ");
        $stmt->bind_param('iiis', $id, $assignTo, $crewingId, $notes);
        
        if ($stmt->execute()) {
            // Update application
            $updateStmt = $this->db->prepare("UPDATE applications SET current_crewing_id = ? WHERE id = ?");
            $updateStmt->bind_param('ii', $assignTo, $id);
            $updateStmt->execute();
            
            // Log automation
            logAutomation('assignment', 'applications', $id, 'manual_assign', [
                'assigned_to' => $assignTo,
                'assigned_by' => $crewingId,
                'notes' => $notes
            ]);
            
            // Notify the assigned crewing
            if ($assignTo != $crewingId) {
                notifyUser($assignTo, 'Application Assigned to You', 
                    'An application has been assigned to you by ' . $_SESSION['user_name'] . '. Notes: ' . ($notes ?: 'No notes'),
                    'info', url('/crewing/applications/' . $id));
            }
            
            flash('success', 'Application assigned successfully');
        } else {
            flash('error', 'Failed to assign application');
        }
        
        $this->redirect(url('/crewing/applications/' . $id));
    }
    
    public function updateStatus($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/crewing/applications/' . $id));
        }
        
        validate_csrf();
        
        $newStatusId = intval($this->input('status_id'));
        $notes = $this->input('notes') ?: '';
        $priority = $this->input('priority');
        $crewingId = $_SESSION['user_id'];
        
        // Get current status and user
        $stmt = $this->db->prepare("SELECT status_id, user_id FROM applications WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            flash('error', 'Application not found');
            $this->redirect(url('/crewing/applications'));
        }
        
        $oldStatusId = $app['status_id'];
        
        // Update application
        $updateQuery = "UPDATE applications SET status_id = ?, status_updated_at = NOW(), reviewed_at = NOW(), reviewed_by = ?";
        $params = [$newStatusId, $crewingId];
        $types = 'ii';
        
        if ($priority) {
            $updateQuery .= ", priority = ?";
            $params[] = $priority;
            $types .= 's';
        }
        
        if ($notes) {
            $updateQuery .= ", admin_notes = CONCAT(IFNULL(admin_notes, ''), ?)";
            $params[] = "\n[" . date('Y-m-d H:i') . " - " . $_SESSION['user_name'] . "] " . $notes;
            $types .= 's';
        }
        
        $updateQuery .= " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';
        
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->bind_param($types, ...$params);
        
        if ($updateStmt->execute()) {
            // Add to history
            $historyStmt = $this->db->prepare("
                INSERT INTO application_status_history (application_id, from_status_id, to_status_id, notes, changed_by, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $historyStmt->bind_param('iiisi', $id, $oldStatusId, $newStatusId, $notes, $crewingId);
            $historyStmt->execute();
            
            // Auto-assign interview if moving to Interview status (status_id = 3)
            if ($newStatusId == 3 && $oldStatusId != 3) {
                $this->autoAssignInterview($id, $app['user_id']);
            }
            
            // Notify applicant about status change
            if (getSetting('auto_notify_status_change', 'true') === 'true') {
                $statusName = $this->db->query("SELECT name FROM application_statuses WHERE id = $newStatusId")->fetch_assoc()['name'];
                notifyUser($app['user_id'], 'Application Status Updated',
                    'Your application status has been updated to: ' . $statusName,
                    'info', url('/applicant/applications/' . $id));
            }
            
            flash('success', 'Status updated successfully');
        } else {
            flash('error', 'Failed to update status');
        }
        
        $this->redirect(url('/crewing/applications/' . $id));
    }
    
    private function autoAssignInterview($applicationId, $userId) {
        // Check if interview session already exists
        $checkInterview = $this->db->prepare("SELECT id FROM interview_sessions WHERE application_id = ?");
        $checkInterview->bind_param('i', $applicationId);
        $checkInterview->execute();
        $existingInterview = $checkInterview->get_result()->fetch_assoc();
        
        if ($existingInterview) {
            return;
        }
        
        // Get first active question bank
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
            
            // Log automation
            logAutomation('interview', 'interview_sessions', $this->db->insert_id, 'auto_assign_interview', [
                'application_id' => $applicationId,
                'question_bank_id' => $bank['id']
            ]);
            
            // Notify applicant
            notifyUser($userId, 'AI Interview Assigned',
                'You have been assigned an AI interview. Please complete it within ' . $expiryDays . ' days.',
                'success', url('/applicant/interview'));
        }
    }
    
    public function markComplete($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/crewing/applications/' . $id));
        }
        
        validate_csrf();
        
        $crewingId = $_SESSION['user_id'];
        
        // Mark current assignment as completed
        $stmt = $this->db->prepare("
            UPDATE application_assignments 
            SET status = 'completed', completed_at = NOW() 
            WHERE application_id = ? AND assigned_to = ? AND status = 'active'
        ");
        $stmt->bind_param('ii', $id, $crewingId);
        
        if ($stmt->execute()) {
            flash('success', 'Application marked as completed');
        } else {
            flash('error', 'Failed to mark as completed');
        }
        
        $this->redirect(url('/crewing/applications'));
    }
}
