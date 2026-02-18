<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing AI Interviews Controller
 * Manage interview sessions for assigned applicants
 */
class Interviews extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn() || (!isCrewing() && !isAdmin() && !isMasterAdmin())) {
            flash('error', 'Access denied');
            redirect(url('/login'));
        }
    }
    
    /**
     * Build the WHERE clause to filter applications belonging to this crewing user.
     * Uses the same pattern as Pipeline/Dashboard controllers.
     */
    private function crewingFilter() {
        return "(aa.assigned_to = ? OR a.entered_by = ? OR a.current_crewing_id = ?)";
    }
    
    /**
     * List all interview sessions for crewing user's assigned applicants
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        $status = $this->input('status');
        
        // Ensure ai_feedback column exists
        $checkCol = $this->db->query("SHOW COLUMNS FROM interview_answers LIKE 'ai_feedback'");
        if ($checkCol->num_rows == 0) {
            $this->db->query("ALTER TABLE interview_answers ADD COLUMN ai_feedback TEXT NULL AFTER completeness_score");
        }
        
        // Get interviews - crewing sees only their assigned applicants
        $query = "
            SELECT is2.*, a.id as application_id, u.full_name, u.email, u.avatar,
                   v.title as vacancy_title,
                   qb.name as question_bank_name,
                   IFNULL(is2.retry_count, 0) as retry_count,
                   (SELECT COUNT(*) FROM interview_questions WHERE question_bank_id = is2.question_bank_id) as total_questions,
                   (SELECT COUNT(*) FROM interview_answers WHERE session_id = is2.id) as answered_questions
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
            WHERE " . $this->crewingFilter() . "
        ";
        
        $params = [$userId, $userId, $userId];
        $types = 'iii';
        
        if ($status) {
            $query .= " AND is2.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        $query .= " GROUP BY is2.id ORDER BY is2.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get stats
        $statsQuery = "
            SELECT 
                COUNT(DISTINCT is2.id) as total,
                SUM(CASE WHEN is2.status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN is2.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN is2.status = 'completed' THEN 1 ELSE 0 END) as completed,
                AVG(CASE WHEN is2.status = 'completed' THEN is2.total_score ELSE NULL END) as avg_score
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE " . $this->crewingFilter() . "
        ";
        $statsStmt = $this->db->prepare($statsQuery);
        $statsStmt->bind_param('iii', $userId, $userId, $userId);
        $statsStmt->execute();
        $stats = $statsStmt->get_result()->fetch_assoc();
        
        // Get question banks for assign modal
        $questionBanks = $this->db->query("
            SELECT qb.*, 
                   (SELECT COUNT(*) FROM interview_questions WHERE question_bank_id = qb.id) as question_count
            FROM interview_question_banks qb
            WHERE qb.is_active = 1
            ORDER BY qb.name
        ")->fetch_all(MYSQLI_ASSOC);
        
        // Get assignable applicants (who don't have pending/in_progress interviews)
        $applicantsStmt = $this->db->prepare("
            SELECT a.id as application_id, u.full_name, v.title as vacancy_title
            FROM applications a
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            WHERE " . $this->crewingFilter() . "
            AND a.id NOT IN (
                SELECT application_id FROM interview_sessions 
                WHERE status IN ('pending', 'in_progress')
            )
            GROUP BY a.id
            ORDER BY u.full_name
        ");
        $applicantsStmt->bind_param('iii', $userId, $userId, $userId);
        $applicantsStmt->execute();
        $assignableApplicants = $applicantsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/interviews/index', [
            'sessions' => $sessions,
            'stats' => $stats,
            'questionBanks' => $questionBanks,
            'assignableApplicants' => $assignableApplicants,
            'filter_status' => $status,
            'pageTitle' => 'AI Interviews'
        ]);
    }
    
    /**
     * Review a completed interview session
     */
    public function review($sessionId) {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("
            SELECT is2.*, a.id as application_id, u.full_name, u.email, u.avatar,
                   v.title as vacancy_title, qb.name as question_bank_name
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            JOIN users u ON a.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
            WHERE is2.id = ? AND (" . $this->crewingFilter() . ")
        ");
        $stmt->bind_param('iiii', $sessionId, $userId, $userId, $userId);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            flash('error', 'Interview session not found');
            redirect(url('/crewing/interviews'));
        }
        
        // Get answers with questions
        $answersStmt = $this->db->prepare("
            SELECT ia.*, iq.question_text, iq.question_type, iq.expected_keywords,
                   iq.correct_answer, iq.max_score, iq.options
            FROM interview_answers ia
            JOIN interview_questions iq ON ia.question_id = iq.id
            WHERE ia.session_id = ?
            ORDER BY iq.sort_order
        ");
        $answersStmt->bind_param('i', $sessionId);
        $answersStmt->execute();
        $answers = $answersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('crewing/interviews/review', [
            'session' => $session,
            'answers' => $answers,
            'pageTitle' => 'Interview Review'
        ]);
    }
    
    /**
     * Assign interview to an applicant
     */
    public function assignInterview() {
        if (!$this->isPost()) {
            redirect(url('/crewing/interviews'));
        }
        
        validate_csrf();
        
        $applicationId = $this->input('application_id');
        $questionBankId = $this->input('question_bank_id');
        $expiryDays = $this->input('expiry_days') ?: 7;
        $userId = $_SESSION['user_id'];
        
        // Verify this application is accessible by current crewing user
        $checkStmt = $this->db->prepare("
            SELECT a.id FROM applications a
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE a.id = ? AND (" . $this->crewingFilter() . ")
        ");
        $checkStmt->bind_param('iiii', $applicationId, $userId, $userId, $userId);
        $checkStmt->execute();
        if (!$checkStmt->get_result()->fetch_assoc()) {
            flash('error', 'Application not found or not assigned to you');
            redirect(url('/crewing/interviews'));
        }
        
        // Check no existing active interview
        $existCheck = $this->db->prepare("
            SELECT id FROM interview_sessions 
            WHERE application_id = ? AND status IN ('pending', 'in_progress')
        ");
        $existCheck->bind_param('i', $applicationId);
        $existCheck->execute();
        if ($existCheck->get_result()->fetch_assoc()) {
            flash('error', 'Applicant already has an active interview');
            redirect(url('/crewing/interviews'));
        }
        
        // Create interview session
        $stmt = $this->db->prepare("
            INSERT INTO interview_sessions (application_id, question_bank_id, status, expires_at, created_at)
            VALUES (?, ?, 'pending', DATE_ADD(NOW(), INTERVAL ? DAY), NOW())
        ");
        $stmt->bind_param('iii', $applicationId, $questionBankId, $expiryDays);
        
        if ($stmt->execute()) {
            // Update application status to Interview (status_id 3)
            $statusStmt = $this->db->prepare("UPDATE applications SET status_id = 3, status_updated_at = NOW() WHERE id = ?");
            $statusStmt->bind_param('i', $applicationId);
            $statusStmt->execute();
            
            // Notify applicant
            $appStmt = $this->db->prepare("SELECT user_id FROM applications WHERE id = ?");
            $appStmt->bind_param('i', $applicationId);
            $appStmt->execute();
            $app = $appStmt->get_result()->fetch_assoc();
            
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'AI Interview Assigned', 'You have been assigned an AI interview. Please complete it within $expiryDays days.', 'info', ?, NOW())
            ");
            $actionUrl = url('/applicant/interview');
            $notifStmt->bind_param('is', $app['user_id'], $actionUrl);
            $notifStmt->execute();
            
            flash('success', 'Interview assigned successfully');
        } else {
            flash('error', 'Failed to assign interview');
        }
        
        redirect(url('/crewing/interviews'));
    }
    
    /**
     * Score/override a completed interview
     */
    public function score($sessionId) {
        if (!$this->isPost()) {
            redirect(url('/crewing/interviews/review/' . $sessionId));
        }
        
        validate_csrf();
        $userId = $_SESSION['user_id'];
        
        // Verify ownership
        $checkStmt = $this->db->prepare("
            SELECT is2.id FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE is2.id = ? AND (" . $this->crewingFilter() . ")
        ");
        $checkStmt->bind_param('iiii', $sessionId, $userId, $userId, $userId);
        $checkStmt->execute();
        if (!$checkStmt->get_result()->fetch_assoc()) {
            flash('error', 'Session not found');
            redirect(url('/crewing/interviews'));
        }
        
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
            flash('success', 'Interview scored successfully');
        } else {
            flash('error', 'Failed to save score');
        }
        
        redirect(url('/crewing/interviews/review/' . $sessionId));
    }
    
    /**
     * Reset an interview for retry
     */
    public function resetInterview($sessionId) {
        if (!$this->isPost()) {
            redirect(url('/crewing/interviews'));
        }
        
        validate_csrf();
        $userId = $_SESSION['user_id'];
        
        // Verify ownership
        $stmt = $this->db->prepare("
            SELECT is2.*, a.user_id as applicant_user_id
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            LEFT JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            WHERE is2.id = ? AND (" . $this->crewingFilter() . ")
        ");
        $stmt->bind_param('iiii', $sessionId, $userId, $userId, $userId);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            flash('error', 'Interview session not found');
            redirect(url('/crewing/interviews'));
        }
        
        // Ensure retry_count column exists
        $checkColumn = $this->db->query("SHOW COLUMNS FROM interview_sessions LIKE 'retry_count'");
        if ($checkColumn->num_rows == 0) {
            $this->db->query("ALTER TABLE interview_sessions ADD COLUMN retry_count INT DEFAULT 0");
        }
        
        // Delete old answers
        $delStmt = $this->db->prepare("DELETE FROM interview_answers WHERE session_id = ?");
        $delStmt->bind_param('i', $sessionId);
        $delStmt->execute();
        
        $newRetryCount = ($session['retry_count'] ?? 0) + 1;
        
        // Reset session
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
            $notifStmt->bind_param('is', $session['applicant_user_id'], $interviewUrl);
            $notifStmt->execute();
            
            flash('success', 'Interview has been reset. Applicant can retake.');
        } else {
            flash('error', 'Failed to reset interview');
        }
        
        redirect(url('/crewing/interviews'));
    }
    
    // ===========================
    // Question Bank Management
    // ===========================
    
    /**
     * Manage question banks and questions
     */
    public function questions() {
        $bankId = $this->input('bank_id');
        
        $banks = $this->db->query("
            SELECT qb.*, 
                   (SELECT COUNT(*) FROM interview_questions WHERE question_bank_id = qb.id) as question_count
            FROM interview_question_banks qb
            ORDER BY qb.created_at DESC
        ")->fetch_all(MYSQLI_ASSOC);
        
        $questions = [];
        $selectedBank = null;
        if ($bankId) {
            $stmt = $this->db->prepare("SELECT * FROM interview_questions WHERE question_bank_id = ? ORDER BY sort_order");
            $stmt->bind_param('i', $bankId);
            $stmt->execute();
            $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            $bankStmt = $this->db->prepare("SELECT * FROM interview_question_banks WHERE id = ?");
            $bankStmt->bind_param('i', $bankId);
            $bankStmt->execute();
            $selectedBank = $bankStmt->get_result()->fetch_assoc();
        }
        
        $this->view('crewing/interviews/questions', [
            'banks' => $banks,
            'questions' => $questions,
            'selectedBankId' => $bankId,
            'selectedBank' => $selectedBank,
            'pageTitle' => 'Question Banks'
        ]);
    }
    
    /**
     * Store a new question bank or add/edit a question
     */
    public function storeQuestion() {
        if (!$this->isPost()) { redirect(url('/crewing/interviews/questions')); }
        validate_csrf();
        
        $action = $this->input('action');
        $userId = $_SESSION['user_id'];
        
        if ($action === 'create_bank') {
            $name = trim($this->input('bank_name'));
            $description = $this->input('description');
            
            if (empty($name)) {
                flash('error', 'Question bank name is required');
                redirect(url('/crewing/interviews/questions'));
                return;
            }
            
            $stmt = $this->db->prepare("INSERT INTO interview_question_banks (name, description, is_active, created_by, created_at) VALUES (?, ?, 1, ?, NOW())");
            $stmt->bind_param('ssi', $name, $description, $userId);
            
            if ($stmt->execute()) {
                flash('success', 'Question bank created successfully');
                redirect(url('/crewing/interviews/questions?bank_id=' . $this->db->insert_id));
            } else {
                flash('error', 'Failed to create question bank');
                redirect(url('/crewing/interviews/questions'));
            }
            return;
            
        } elseif ($action === 'add_question') {
            $bankId = $this->input('question_bank_id');
            $questionText = $this->input('question_text');
            $questionType = $this->input('question_type') ?: 'essay';
            $options = $this->input('options');
            $correctAnswer = $this->input('correct_answer');
            $expectedKeywords = $this->input('expected_keywords');
            $minWordCount = $this->input('min_word_count') ?: 50;
            $timeLimit = $this->input('time_limit_seconds') ?: 180;
            $maxScore = $this->input('max_score') ?: 10;
            
            // Get next sort order
            $sortStmt = $this->db->prepare("SELECT IFNULL(MAX(sort_order),0)+1 as n FROM interview_questions WHERE question_bank_id = ?");
            $sortStmt->bind_param('i', $bankId);
            $sortStmt->execute();
            $sortOrder = $sortStmt->get_result()->fetch_assoc()['n'];
            
            $optionsJson = null;
            if ($questionType === 'multiple_choice' && $options) {
                $optionsJson = json_encode(array_values(array_filter(array_map('trim', explode("\n", $options)))));
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO interview_questions (question_bank_id, question_text, question_type, options, correct_answer, expected_keywords, min_word_count, time_limit_seconds, max_score, sort_order, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
            ");
            $stmt->bind_param('isssssiiis', $bankId, $questionText, $questionType, $optionsJson, $correctAnswer, $expectedKeywords, $minWordCount, $timeLimit, $maxScore, $sortOrder);
            
            if ($stmt->execute()) {
                flash('success', 'Question added successfully');
            } else {
                flash('error', 'Failed to add question');
            }
            redirect(url('/crewing/interviews/questions?bank_id=' . $bankId));
            return;
            
        } elseif ($action === 'edit_question') {
            $questionId = $this->input('question_id');
            $bankId = $this->input('question_bank_id');
            $questionText = $this->input('question_text');
            $questionType = $this->input('question_type') ?: 'essay';
            $options = $this->input('options');
            $correctAnswer = $this->input('correct_answer');
            $expectedKeywords = $this->input('expected_keywords');
            $minWordCount = $this->input('min_word_count') ?: 50;
            $timeLimit = $this->input('time_limit_seconds') ?: 180;
            $maxScore = $this->input('max_score') ?: 10;
            
            $optionsJson = null;
            if ($questionType === 'multiple_choice' && $options) {
                $optionsJson = json_encode(array_values(array_filter(array_map('trim', explode("\n", $options)))));
            }
            
            $stmt = $this->db->prepare("UPDATE interview_questions SET question_text=?, question_type=?, options=?, correct_answer=?, expected_keywords=?, min_word_count=?, time_limit_seconds=?, max_score=? WHERE id=?");
            $stmt->bind_param('sssssiiis', $questionText, $questionType, $optionsJson, $correctAnswer, $expectedKeywords, $minWordCount, $timeLimit, $maxScore, $questionId);
            
            if ($stmt->execute()) { flash('success', 'Question updated'); }
            else { flash('error', 'Failed to update question'); }
            redirect(url('/crewing/interviews/questions?bank_id=' . $bankId));
            return;
        }
        
        redirect(url('/crewing/interviews/questions'));
    }
    
    /**
     * Delete a question
     */
    public function deleteQuestion($questionId) {
        if (!$this->isPost()) { redirect(url('/crewing/interviews/questions')); }
        validate_csrf();
        
        $stmt = $this->db->prepare("SELECT question_bank_id FROM interview_questions WHERE id = ?");
        $stmt->bind_param('i', $questionId);
        $stmt->execute();
        $q = $stmt->get_result()->fetch_assoc();
        
        if ($q) {
            $del = $this->db->prepare("DELETE FROM interview_questions WHERE id = ?");
            $del->bind_param('i', $questionId);
            $del->execute();
            flash('success', 'Question deleted');
            redirect(url('/crewing/interviews/questions?bank_id=' . $q['question_bank_id']));
        } else {
            flash('error', 'Question not found');
            redirect(url('/crewing/interviews/questions'));
        }
    }
    
    /**
     * Delete a question bank (only if no active sessions)
     */
    public function deleteBank($bankId) {
        if (!$this->isPost()) { redirect(url('/crewing/interviews/questions')); }
        validate_csrf();
        
        $chk = $this->db->prepare("SELECT COUNT(*) as c FROM interview_sessions WHERE question_bank_id = ? AND status IN ('pending','in_progress')");
        $chk->bind_param('i', $bankId);
        $chk->execute();
        if ($chk->get_result()->fetch_assoc()['c'] > 0) {
            flash('error', 'Cannot delete: bank has active interview sessions');
            redirect(url('/crewing/interviews/questions'));
            return;
        }
        
        $this->db->query("DELETE FROM interview_questions WHERE question_bank_id = $bankId");
        $this->db->query("DELETE FROM interview_question_banks WHERE id = $bankId");
        flash('success', 'Question bank deleted');
        redirect(url('/crewing/interviews/questions'));
    }
}
