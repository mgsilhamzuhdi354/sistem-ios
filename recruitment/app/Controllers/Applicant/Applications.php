<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Applicant Applications Controller
 */
class Applications extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn()) {
            flash('error', 'Please login to continue');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT a.*, v.title as vacancy_title, v.salary_min, v.salary_max,
                   v.contract_duration_months, d.name as department_name,
                   s.name as status_name, s.color as status_color,
                   vt.name as vessel_type
            FROM applications a
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('applicant/applications/index', [
            'applications' => $applications,
            'pageTitle' => 'My Applications'
        ]);
    }
    
    public function detail($id) {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT a.*, v.title as vacancy_title, v.description as vacancy_description,
                   v.requirements, v.salary_min, v.salary_max, v.contract_duration_months,
                   d.name as department_name, s.name as status_name, s.color as status_color,
                   vt.name as vessel_type
            FROM applications a
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            LEFT JOIN vessel_types vt ON v.vessel_type_id = vt.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.id = ? AND a.user_id = ?
        ");
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $application = $stmt->get_result()->fetch_assoc();
        
        if (!$application) {
            flash('error', 'Application not found');
            $this->redirect(url('/applicant/applications'));
        }
        
        // Get status history - group by status to avoid duplicates
        $historyStmt = $this->db->prepare("
            SELECT ash.*, 
                   fs.name as from_status, ts.name as to_status,
                   ts.color as to_color,
                   u.full_name as changed_by_name
            FROM application_status_history ash
            LEFT JOIN application_statuses fs ON ash.from_status_id = fs.id
            JOIN application_statuses ts ON ash.to_status_id = ts.id
            LEFT JOIN users u ON ash.changed_by = u.id
            WHERE ash.application_id = ?
            AND ash.id IN (
                SELECT MAX(id) FROM application_status_history 
                WHERE application_id = ? 
                GROUP BY to_status_id
            )
            ORDER BY ash.created_at DESC
        ");
        $historyStmt->bind_param('ii', $id, $id);
        $historyStmt->execute();
        $history = $historyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get interview session if exists
        $interviewStmt = $this->db->prepare("
            SELECT * FROM interview_sessions 
            WHERE application_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $interviewStmt->bind_param('i', $id);
        $interviewStmt->execute();
        $interview = $interviewStmt->get_result()->fetch_assoc();
        
        // Get medical checkup if exists
        $medicalStmt = $this->db->prepare("
            SELECT * FROM medical_checkups 
            WHERE application_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $medicalStmt->bind_param('i', $id);
        $medicalStmt->execute();
        $medical = $medicalStmt->get_result()->fetch_assoc();
        
        $this->view('applicant/applications/detail', [
            'application' => $application,
            'history' => $history,
            'interview' => $interview,
            'medical' => $medical,
            'pageTitle' => 'Application Detail'
        ]);
    }
    
    public function apply($vacancyId) {
        if (!$this->isPost()) {
            $this->redirect(url('/jobs/' . $vacancyId));
        }
        
        validate_csrf();
        
        $userId = $_SESSION['user_id'];
        
        // Check if vacancy exists and is open
        $stmt = $this->db->prepare("
            SELECT * FROM job_vacancies 
            WHERE id = ? AND status = 'published'
        ");
        $stmt->bind_param('i', $vacancyId);
        $stmt->execute();
        $vacancy = $stmt->get_result()->fetch_assoc();
        
        if (!$vacancy) {
            flash('error', 'Job vacancy not found or closed');
            $this->redirect(url('/jobs'));
        }
        
        // Check if already applied
        $checkStmt = $this->db->prepare("
            SELECT id FROM applications WHERE user_id = ? AND vacancy_id = ?
        ");
        $checkStmt->bind_param('ii', $userId, $vacancyId);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            flash('error', 'You have already applied to this position');
            $this->redirect(url('/jobs/' . $vacancyId));
        }
        
        // Create application
        $coverLetter = $this->input('cover_letter');
        $expectedSalary = $this->input('expected_salary');
        $availableDate = $this->input('available_date');
        
        $stmt = $this->db->prepare("
            INSERT INTO applications (user_id, vacancy_id, status_id, cover_letter, expected_salary, available_date, submitted_at, status_updated_at)
            VALUES (?, ?, 1, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param('iisds', $userId, $vacancyId, $coverLetter, $expectedSalary, $availableDate);
        
        if ($stmt->execute()) {
            $applicationId = $this->db->insert_id;
            
            // Update vacancy application count
            $this->db->query("UPDATE job_vacancies SET applications_count = applications_count + 1 WHERE id = " . $vacancyId);
            
            // Create status history
            $historyStmt = $this->db->prepare("
                INSERT INTO application_status_history (application_id, to_status_id, notes, created_at)
                VALUES (?, 1, 'Application submitted', NOW())
            ");
            $historyStmt->bind_param('i', $applicationId);
            $historyStmt->execute();
            
            // Create notification
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'Application Submitted', 'Your application for " . $this->db->real_escape_string($vacancy['title']) . " has been received.', 'success', ?, NOW())
            ");
            $actionUrl = url('/applicant/applications/' . $applicationId);
            $notifStmt->bind_param('is', $userId, $actionUrl);
            $notifStmt->execute();
            
            flash('success', 'Application submitted successfully!');
            $this->redirect(url('/applicant/applications/' . $applicationId));
        } else {
            flash('error', 'Failed to submit application. Please try again.');
            $this->redirect(url('/jobs/' . $vacancyId));
        }
    }
}
