<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Interviews Controller
 */
class Interviews extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        // Allow both Admin and Master Admin access
        if (!isLoggedIn() || (!isAdmin() && !isMasterAdmin())) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $status = $this->input('status');
        
        $query = "
            SELECT is2.*, a.id as application_id, u.full_name, u.email,
                   v.title as vacancy_title, qb.name as question_bank_name
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
            WHERE 1=1
        ";
        
        if ($status) {
            $query .= " AND is2.status = '" . $this->db->real_escape_string($status) . "'";
        }
        
        $query .= " ORDER BY is2.created_at DESC";
        
        $sessions = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        
        // Get question banks with count
        $questionBanks = $this->db->query("
            SELECT qb.*, 
                   (SELECT COUNT(*) FROM interview_questions WHERE question_bank_id = qb.id) as question_count
            FROM interview_question_banks qb
            ORDER BY qb.name
        ")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/interviews/index', [
            'sessions' => $sessions,
            'questionBanks' => $questionBanks,
            'filter_status' => $status,
            'pageTitle' => 'AI Interviews'
        ]);
    }
    
    public function questions() {
        $bankId = $this->input('bank_id');
        
        // Get question banks
        $banks = $this->db->query("
            SELECT qb.*, d.name as department_name,
                   (SELECT COUNT(*) FROM interview_questions WHERE question_bank_id = qb.id) as question_count
            FROM interview_question_banks qb
            LEFT JOIN departments d ON qb.department_id = d.id
            ORDER BY qb.created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $questions = [];
        if ($bankId) {
            $stmt = $this->db->prepare("
                SELECT * FROM interview_questions 
                WHERE question_bank_id = ?
                ORDER BY sort_order
            ");
            $stmt->bind_param('i', $bankId);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/interviews/questions', [
            'banks' => $banks,
            'questions' => $questions,
            'selectedBank' => $bankId,
            'departments' => $departments,
            'pageTitle' => 'Question Bank'
        ]);
    }
    
    public function createQuestionBank() {
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/interviews/create_bank', [
            'departments' => $departments,
            'pageTitle' => 'Create Question Bank'
        ]);
    }
    
    public function storeQuestionBank() {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/interviews/questions'));
        }
        
        validate_csrf();
        
        $name = trim($this->input('name'));
        $departmentId = $this->input('department_id') ?: null;
        $description = $this->input('description');
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            INSERT INTO interview_question_banks (name, department_id, description, is_active, created_by, created_at)
            VALUES (?, ?, ?, 1, ?, NOW())
        ");
        $stmt->bind_param('sisi', $name, $departmentId, $description, $userId);
        
        if ($stmt->execute()) {
            $bankId = $this->db->insert_id;
            flash('success', 'Question bank created successfully');
            $this->redirect(url('/admin/interviews/questions/' . $bankId));
        } else {
            flash('error', 'Failed to create question bank');
            $this->redirect(url('/admin/interviews/questions/create'));
        }
    }
    
    public function editQuestionBank($bankId) {
        $stmt = $this->db->prepare("SELECT * FROM interview_question_banks WHERE id = ?");
        $stmt->bind_param('i', $bankId);
        $stmt->execute();
        $bank = $stmt->get_result()->fetch_assoc();
        
        if (!$bank) {
            flash('error', 'Question bank not found');
            $this->redirect(url('/admin/interviews/questions'));
        }
        
        // Get questions
        $questionsStmt = $this->db->prepare("
            SELECT * FROM interview_questions WHERE question_bank_id = ? ORDER BY sort_order
        ");
        $questionsStmt->bind_param('i', $bankId);
        $questionsStmt->execute();
        $questions = $questionsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $departments = $this->db->query("SELECT * FROM departments WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/interviews/edit_bank', [
            'bank' => $bank,
            'questions' => $questions,
            'departments' => $departments,
            'pageTitle' => 'Edit Question Bank'
        ]);
    }
    
    public function deleteQuestion($questionId) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/interviews/questions'));
        }
        
        validate_csrf();
        
        // Get bank ID before delete
        $stmt = $this->db->prepare("SELECT question_bank_id FROM interview_questions WHERE id = ?");
        $stmt->bind_param('i', $questionId);
        $stmt->execute();
        $question = $stmt->get_result()->fetch_assoc();
        
        if ($question) {
            $this->db->query("DELETE FROM interview_questions WHERE id = $questionId");
            flash('success', 'Question deleted');
            $this->redirect(url('/admin/interviews/questions/' . $question['question_bank_id']));
        } else {
            flash('error', 'Question not found');
            $this->redirect(url('/admin/interviews/questions'));
        }
    }

    
    public function storeQuestion() {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/interviews/questions'));
        }
        
        validate_csrf();
        
        $action = $this->input('action');
        
        if ($action === 'create_bank') {
            $name = trim($this->input('bank_name'));
            $departmentId = $this->input('department_id') ?: null;
            $description = $this->input('description');
            $userId = $_SESSION['user_id'];
            
            $stmt = $this->db->prepare("
                INSERT INTO interview_question_banks (name, department_id, description, created_by, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('sisi', $name, $departmentId, $description, $userId);
            
            if ($stmt->execute()) {
                flash('success', 'Question bank created');
            } else {
                flash('error', 'Failed to create question bank');
            }
        } elseif ($action === 'add_question') {
            $bankId = $this->input('question_bank_id');
            $questionText = $this->input('question_text');
            $questionTextId = $this->input('question_text_id');
            $questionType = $this->input('question_type');
            $options = $this->input('options');
            $correctAnswer = $this->input('correct_answer');
            $expectedKeywords = $this->input('expected_keywords');
            $minWordCount = $this->input('min_word_count') ?: 50;
            $timeLimit = $this->input('time_limit_seconds') ?: 180;
            $maxScore = $this->input('max_score') ?: 100;
            
            // Get next sort order
            $sortOrder = $this->db->query("
                SELECT MAX(sort_order) + 1 as next FROM interview_questions WHERE question_bank_id = $bankId
            ")->fetch_assoc()['next'] ?: 1;
            
            $optionsJson = $options ? json_encode(array_filter(explode("\n", $options))) : null;
            $keywordsJson = $expectedKeywords ? json_encode(array_filter(explode(",", $expectedKeywords))) : null;
            
            $stmt = $this->db->prepare("
                INSERT INTO interview_questions (
                    question_bank_id, question_text, question_text_id, question_type,
                    options, correct_answer, expected_keywords, min_word_count,
                    time_limit_seconds, max_score, sort_order, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('issssssiiis',
                $bankId, $questionText, $questionTextId, $questionType,
                $optionsJson, $correctAnswer, $keywordsJson, $minWordCount,
                $timeLimit, $maxScore, $sortOrder
            );
            
            if ($stmt->execute()) {
                flash('success', 'Question added');
            } else {
                flash('error', 'Failed to add question');
            }
            
            $this->redirect(url('/admin/interviews/questions?bank_id=' . $bankId));
            return;
        }
        
        $this->redirect(url('/admin/interviews/questions'));
    }
    
    public function review($sessionId) {
        $stmt = $this->db->prepare("
            SELECT is2.*, a.id as application_id, u.full_name, u.email,
                   v.title as vacancy_title, qb.name as question_bank_name
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
            WHERE is2.id = ?
        ");
        $stmt->bind_param('i', $sessionId);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            flash('error', 'Session not found');
            $this->redirect(url('/admin/interviews'));
        }
        
        // Get answers with questions
        $answersStmt = $this->db->prepare("
            SELECT ia.*, iq.question_text, iq.question_type, iq.expected_keywords,
                   iq.correct_answer, iq.max_score
            FROM interview_answers ia
            JOIN interview_questions iq ON ia.question_id = iq.id
            WHERE ia.session_id = ?
            ORDER BY iq.sort_order
        ");
        $answersStmt->bind_param('i', $sessionId);
        $answersStmt->execute();
        $answers = $answersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('admin/interviews/review', [
            'session' => $session,
            'answers' => $answers,
            'pageTitle' => 'Interview Review'
        ]);
    }
    
    public function score($sessionId) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/interviews/review/' . $sessionId));
        }
        
        validate_csrf();
        
        $overrideScore = $this->input('override_score');
        $notes = $this->input('admin_notes');
        $recommendation = $this->input('recommendation');
        
        $stmt = $this->db->prepare("
            UPDATE interview_sessions SET
                admin_override_score = ?,
                admin_notes = ?,
                ai_recommendation = ?
            WHERE id = ?
        ");
        $stmt->bind_param('issi', $overrideScore, $notes, $recommendation, $sessionId);
        
        if ($stmt->execute()) {
            flash('success', 'Interview scored');
        } else {
            flash('error', 'Failed to save score');
        }
        
        $this->redirect(url('/admin/interviews/review/' . $sessionId));
    }
    
    public function resetInterview($sessionId) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/interviews'));
        }
        
        validate_csrf();
        
        // Get session info
        $stmt = $this->db->prepare("
            SELECT is2.*, a.user_id 
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            WHERE is2.id = ?
        ");
        $stmt->bind_param('i', $sessionId);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            flash('error', 'Interview session not found');
            $this->redirect(url('/admin/interviews'));
        }
        
        // Ensure retry_count column exists (for MySQL < 8.0.16)
        $checkColumn = $this->db->query("SHOW COLUMNS FROM interview_sessions LIKE 'retry_count'");
        if ($checkColumn->num_rows == 0) {
            $this->db->query("ALTER TABLE interview_sessions ADD COLUMN retry_count INT DEFAULT 0");
        }
        
        // Delete all answers for this session
        $this->db->query("DELETE FROM interview_answers WHERE session_id = $sessionId");
        
        // Get current retry count
        $currentRetry = $this->db->query("SELECT retry_count FROM interview_sessions WHERE id = $sessionId")->fetch_assoc();
        $newRetryCount = ($currentRetry['retry_count'] ?? 0) + 1;
        
        // Reset session to pending with new expiry and increment retry count
        $resetStmt = $this->db->prepare("
            UPDATE interview_sessions SET
                status = 'pending',
                started_at = NULL,
                completed_at = NULL,
                expires_at = DATE_ADD(NOW(), INTERVAL 7 DAY),
                total_score = NULL,
                ai_recommendation = NULL,
                admin_override_score = NULL,
                admin_notes = NULL,
                retry_count = ?
            WHERE id = ?
        ");
        $resetStmt->bind_param('ii', $newRetryCount, $sessionId);
        
        if ($resetStmt->execute()) {
            // Notify applicant
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'Interview Reset', 'Your interview has been reset. Please complete it again within 7 days.', 'warning', ?, NOW())
            ");
            $interviewUrl = url('/applicant/interview');
            $notifStmt->bind_param('is', $session['user_id'], $interviewUrl);
            $notifStmt->execute();
            
            flash('success', 'Interview has been reset. Applicant can retake the interview.');
        } else {
            flash('error', 'Failed to reset interview');
        }
        
        $this->redirect(url('/admin/interviews/review/' . $sessionId));
    }
    
    public function assignInterview() {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/applicants'));
        }
        
        validate_csrf();
        
        $applicationId = $this->input('application_id');
        $questionBankId = $this->input('question_bank_id');
        $expiryDays = $this->input('expiry_days') ?: 7;
        
        // Create interview session
        $stmt = $this->db->prepare("
            INSERT INTO interview_sessions (application_id, question_bank_id, status, expires_at, created_at)
            VALUES (?, ?, 'pending', DATE_ADD(NOW(), INTERVAL ? DAY), NOW())
        ");
        $stmt->bind_param('iii', $applicationId, $questionBankId, $expiryDays);
        
        if ($stmt->execute()) {
            // Update application status to Interview
            $this->db->query("UPDATE applications SET status_id = 3, status_updated_at = NOW() WHERE id = $applicationId");
            
            // Notify applicant
            $app = $this->db->query("SELECT user_id FROM applications WHERE id = $applicationId")->fetch_assoc();
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'Interview Assigned', 'You have been assigned an AI interview. Please complete it within $expiryDays days.', 'info', ?, NOW())
            ");
            $actionUrl = url('/applicant/interview');
            $notifStmt->bind_param('is', $app['user_id'], $actionUrl);
            $notifStmt->execute();
            
            flash('success', 'Interview assigned successfully');
        } else {
            flash('error', 'Failed to assign interview');
        }
        
        $this->redirect(url('/admin/applicants/' . $applicationId));
    }
}
