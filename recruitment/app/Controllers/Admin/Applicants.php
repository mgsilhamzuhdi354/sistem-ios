<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Applicants Controller
 */
class Applicants extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || (!isAdmin() && !isMasterAdmin())) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $status = $this->input('status');
        $department = $this->input('department');
        $vacancy = $this->input('vacancy');
        $search = $this->input('search');
        
        $query = "
            SELECT a.*, u.full_name, u.email, u.phone, u.avatar,
                   v.title as vacancy_title, d.name as department_name,
                   s.name as status_name, s.color as status_color,
                   ap.profile_completion
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE 1=1
        ";
        
        if ($status) {
            $query .= " AND a.status_id = " . intval($status);
        }
        if ($department) {
            $query .= " AND v.department_id = " . intval($department);
        }
        if ($vacancy) {
            $query .= " AND a.vacancy_id = " . intval($vacancy);
        }
        if ($search) {
            $searchEsc = $this->db->real_escape_string($search);
            $query .= " AND (u.full_name LIKE '%$searchEsc%' OR u.email LIKE '%$searchEsc%')";
        }
        
        $query .= " ORDER BY a.submitted_at DESC";
        
        $applicants = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        
        $statuses = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        $vacancies = $this->db->query("SELECT id, title FROM job_vacancies WHERE status = 'published'")->fetch_all(MYSQLI_ASSOC);
        
        // Get status counts
        $statusCounts = $this->db->query("
            SELECT s.id, s.name, s.color, COUNT(a.id) as count
            FROM application_statuses s
            LEFT JOIN applications a ON s.id = a.status_id
            GROUP BY s.id
            ORDER BY s.sort_order
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/applicants/index', [
            'applicants' => $applicants,
            'statuses' => $statuses,
            'statusCounts' => $statusCounts,
            'departments' => $departments,
            'vacancies' => $vacancies,
            'filters' => compact('status', 'department', 'vacancy', 'search'),
            'pageTitle' => 'Applicants'
        ]);
    }
    
    public function detail($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.full_name, u.email, u.phone, u.avatar, u.created_at as user_created,
                   v.title as vacancy_title, v.description as vacancy_description,
                   v.salary_min, v.salary_max, v.requirements,
                   d.name as department_name, s.name as status_name, s.color as status_color,
                   ap.*
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $application = $stmt->get_result()->fetch_assoc();
        
        if (!$application) {
            flash('error', 'Application not found');
            $this->redirect(url('/admin/applicants'));
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
        $history = $historyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
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
        
        // Get medical checkups
        $medicalStmt = $this->db->prepare("SELECT * FROM medical_checkups WHERE application_id = ?");
        $medicalStmt->bind_param('i', $id);
        $medicalStmt->execute();
        $medicals = $medicalStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get all statuses for dropdown
        $statuses = $this->db->query("SELECT * FROM application_statuses ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/applicants/detail', [
            'application' => $application,
            'documents' => $documents,
            'history' => $history,
            'interviews' => $interviews,
            'medicals' => $medicals,
            'statuses' => $statuses,
            'pageTitle' => 'Applicant Detail'
        ]);
    }
    
    public function pipeline() {
        // Get pipeline data (Kanban style)
        $statuses = $this->db->query("
            SELECT * FROM application_statuses ORDER BY sort_order
        ")->fetch_all(MYSQLI_ASSOC);
        
        $pipeline = [];
        foreach ($statuses as $status) {
            $stmt = $this->db->prepare("
                SELECT a.*, u.full_name, u.avatar, v.title as vacancy_title,
                       DATEDIFF(NOW(), COALESCE(a.status_updated_at, a.submitted_at)) as days_in_status
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                WHERE a.status_id = ?
                ORDER BY COALESCE(a.status_updated_at, a.submitted_at)
            ");
            $stmt->bind_param('i', $status['id']);
            $stmt->execute();
            
            // Store applications directly by status ID for easier access in view
            $pipeline[$status['id']] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        $this->view('admin/applicants/pipeline', [
            'pipeline' => $pipeline,
            'statuses' => $statuses,
            'pageTitle' => 'Recruitment Pipeline'
        ]);
    }
    
    public function updateStatus($id) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/applicants/' . $id));
        }
        
        validate_csrf();
        
        $newStatusId = $this->input('status_id');
        $notes = $this->input('notes') ?: '';
        $rejectionReason = $this->input('rejection_reason');
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        // Also check if request expects JSON
        if (!$isAjax && isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            $isAjax = true;
        }
        
        // Get current status
        $stmt = $this->db->prepare("SELECT status_id, user_id FROM applications WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        
        if (!$app) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Application not found']);
                exit;
            }
            flash('error', 'Application not found');
            $this->redirect(url('/admin/applicants'));
        }
        
        $oldStatusId = $app['status_id'];
        
        // Update application
        $updateStmt = $this->db->prepare("
            UPDATE applications SET 
                status_id = ?, 
                admin_notes = CONCAT(IFNULL(admin_notes, ''), ?),
                rejection_reason = ?,
                reviewed_at = NOW(),
                reviewed_by = ?,
                status_updated_at = NOW()
            WHERE id = ?
        ");
        $adminId = $_SESSION['user_id'];
        $notesWithNewline = $notes ? "\n" . $notes : '';
        $updateStmt->bind_param('issii', $newStatusId, $notesWithNewline, $rejectionReason, $adminId, $id);
        
        if ($updateStmt->execute()) {
            // Add to history
            $historyStmt = $this->db->prepare("
                INSERT INTO application_status_history (application_id, from_status_id, to_status_id, notes, changed_by, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $historyStmt->bind_param('iiisi', $id, $oldStatusId, $newStatusId, $notes, $adminId);
            $historyStmt->execute();
            
            // Auto-assign interview if moving to Interview status (status_id = 3)
            if ($newStatusId == 3) {
                // Check if interview session already exists
                $checkInterview = $this->db->prepare("SELECT id FROM interview_sessions WHERE application_id = ?");
                $checkInterview->bind_param('i', $id);
                $checkInterview->execute();
                $existingInterview = $checkInterview->get_result()->fetch_assoc();
                
                if (!$existingInterview) {
                    // Get first active question bank
                    $bankResult = $this->db->query("SELECT id FROM interview_question_banks WHERE is_active = 1 ORDER BY id LIMIT 1");
                    $bank = $bankResult->fetch_assoc();
                    
                    if ($bank) {
                        $expiryDays = 7;
                        $interviewStmt = $this->db->prepare("
                            INSERT INTO interview_sessions (application_id, question_bank_id, status, expires_at, created_at)
                            VALUES (?, ?, 'pending', DATE_ADD(NOW(), INTERVAL ? DAY), NOW())
                        ");
                        $interviewStmt->bind_param('iii', $id, $bank['id'], $expiryDays);
                        $interviewStmt->execute();
                        
                        // Notify applicant about interview
                        $interviewNotif = $this->db->prepare("
                            INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                            VALUES (?, 'AI Interview Assigned', 'You have been assigned an AI interview. Please complete it within 7 days.', 'success', ?, NOW())
                        ");
                        $interviewUrl = url('/applicant/interview');
                        $interviewNotif->bind_param('is', $app['user_id'], $interviewUrl);
                        $interviewNotif->execute();
                    }
                }
            }
            
            // Create notification for applicant
            $statusName = $this->db->query("SELECT name FROM application_statuses WHERE id = $newStatusId")->fetch_assoc()['name'];
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'Application Status Updated', ?, 'info', ?, NOW())
            ");
            $message = "Your application status has been updated to: $statusName";
            $actionUrl = url('/applicant/applications/' . $id);
            $notifStmt->bind_param('iss', $app['user_id'], $message, $actionUrl);
            $notifStmt->execute();
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Status updated', 'new_status' => $statusName]);
                exit;
            }
            
            flash('success', 'Status updated successfully');
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
                exit;
            }
            flash('error', 'Failed to update status');
        }
        
        $this->redirect(url('/admin/applicants/' . $id));
    }
}
