<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Applicant Interview Controller
 */
class Interview extends BaseController {
    
    public function __construct() {
        parent::__construct();
        
        if (!isLoggedIn()) {
            flash('error', 'Please login to continue');
            redirect(url('/login'));
        }
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Ensure retry_count column exists
        $checkColumn = $this->db->query("SHOW COLUMNS FROM interview_sessions LIKE 'retry_count'");
        if ($checkColumn->num_rows == 0) {
            $this->db->query("ALTER TABLE interview_sessions ADD COLUMN retry_count INT DEFAULT 0");
        }
        
        // Get all interview sessions for user's applications
        $stmt = $this->db->prepare("
            SELECT is2.id, is2.application_id, is2.question_bank_id, is2.status,
                   is2.started_at, is2.completed_at, is2.expires_at, is2.total_score,
                   IFNULL(is2.retry_count, 0) as retry_count,
                   a.id as app_id, v.title as vacancy_title,
                   qb.name as question_bank_name,
                   (SELECT COUNT(*) FROM interview_questions WHERE question_bank_id = is2.question_bank_id) as total_questions
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            JOIN interview_question_banks qb ON is2.question_bank_id = qb.id
            WHERE a.user_id = ?
            ORDER BY is2.created_at DESC
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        $this->view('applicant/interview/index', [
            'sessions' => $sessions,
            'pageTitle' => 'AI Interview'
        ]);
    }
    
    public function start($sessionId) {
        $userId = $_SESSION['user_id'];
        
        // Verify session belongs to user
        $stmt = $this->db->prepare("
            SELECT is2.*, a.id as application_id, v.title as vacancy_title
            FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            WHERE is2.id = ? AND a.user_id = ?
        ");
        $stmt->bind_param('ii', $sessionId, $userId);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            flash('error', 'Interview session not found');
            $this->redirect(url('/applicant/interview'));
        }
        
        if ($session['status'] === 'completed') {
            flash('info', 'You have already completed this interview');
            $this->redirect(url('/applicant/interview'));
        }
        
        if ($session['status'] === 'expired') {
            flash('error', 'This interview session has expired');
            $this->redirect(url('/applicant/interview'));
        }
        
        // Update status to in_progress if pending
        if ($session['status'] === 'pending') {
            $this->db->query("UPDATE interview_sessions SET status = 'in_progress', started_at = NOW() WHERE id = $sessionId");
        }
        
        // Get questions
        $questionsStmt = $this->db->prepare("
            SELECT * FROM interview_questions 
            WHERE question_bank_id = ? AND is_active = 1
            ORDER BY sort_order
        ");
        $questionsStmt->bind_param('i', $session['question_bank_id']);
        $questionsStmt->execute();
        $questions = $questionsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get answered questions
        $answeredStmt = $this->db->prepare("
            SELECT question_id FROM interview_answers WHERE session_id = ?
        ");
        $answeredStmt->bind_param('i', $sessionId);
        $answeredStmt->execute();
        $answeredResult = $answeredStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $answeredIds = array_column($answeredResult, 'question_id');
        
        // Find next unanswered question
        $currentQuestion = null;
        $currentIndex = 0;
        foreach ($questions as $index => $q) {
            if (!in_array($q['id'], $answeredIds)) {
                $currentQuestion = $q;
                $currentIndex = $index;
                break;
            }
        }
        
        // All questions answered
        if (!$currentQuestion) {
            $this->completeSession($sessionId);
            flash('success', 'Interview completed! Thank you for your responses.');
            $this->redirect(url('/applicant/interview'));
        }
        
        $this->view('applicant/interview/session', [
            'session' => $session,
            'questions' => $questions,
            'currentQuestion' => $currentQuestion,
            'currentIndex' => $currentIndex,
            'answeredIds' => $answeredIds,
            'pageTitle' => 'Interview Session'
        ]);
    }
    
    public function submit() {
        if (!$this->isPost()) {
            $this->redirect(url('/applicant/interview'));
        }
        
        validate_csrf();
        
        $sessionId = $this->input('session_id');
        $questionId = $this->input('question_id');
        $answerText = $this->input('answer_text');
        $selectedOption = $this->input('selected_option');
        $timeTaken = $this->input('time_taken');
        
        $userId = $_SESSION['user_id'];
        
        // Verify session
        $stmt = $this->db->prepare("
            SELECT is2.* FROM interview_sessions is2
            JOIN applications a ON is2.application_id = a.id
            WHERE is2.id = ? AND a.user_id = ? AND is2.status = 'in_progress'
        ");
        $stmt->bind_param('ii', $sessionId, $userId);
        $stmt->execute();
        $session = $stmt->get_result()->fetch_assoc();
        
        if (!$session) {
            flash('error', 'Invalid session');
            $this->redirect(url('/applicant/interview'));
        }
        
        // Get question for AI scoring
        $qStmt = $this->db->prepare("SELECT * FROM interview_questions WHERE id = ?");
        $qStmt->bind_param('i', $questionId);
        $qStmt->execute();
        $question = $qStmt->get_result()->fetch_assoc();
        
        // Calculate AI score
        $aiScore = $this->calculateAIScore($question, $answerText, $selectedOption);
        
        // Save answer
        $insertStmt = $this->db->prepare("
            INSERT INTO interview_answers (session_id, question_id, answer_text, selected_option, 
                                           ai_score, keyword_matches, relevance_score, completeness_score,
                                           time_taken_seconds, answered_at, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $keywordJson = json_encode($aiScore['keywords']);
        $insertStmt->bind_param('iissiisii', 
            $sessionId, $questionId, $answerText, $selectedOption,
            $aiScore['score'], $keywordJson, $aiScore['relevance'], $aiScore['completeness'],
            $timeTaken
        );
        $insertStmt->execute();
        
        // Check if all questions answered
        $totalQuestions = $this->db->query("
            SELECT COUNT(*) as c FROM interview_questions 
            WHERE question_bank_id = {$session['question_bank_id']} AND is_active = 1
        ")->fetch_assoc()['c'];
        
        $answeredQuestions = $this->db->query("
            SELECT COUNT(*) as c FROM interview_answers WHERE session_id = $sessionId
        ")->fetch_assoc()['c'];
        
        if ($answeredQuestions >= $totalQuestions) {
            $this->completeSession($sessionId);
            flash('success', 'Interview completed! Thank you for your responses.');
            $this->redirect(url('/applicant/interview'));
        }
        
        $this->redirect(url('/applicant/interview/start/' . $sessionId));
    }
    
    private function calculateAIScore($question, $answerText, $selectedOption) {
        $score = 0;
        $relevance = 0;
        $completeness = 0;
        $matchedKeywords = [];
        
        if ($question['question_type'] === 'multiple_choice') {
            // For multiple choice, check correct answer
            if ($selectedOption === $question['correct_answer']) {
                $score = 100;
                $relevance = 100;
                $completeness = 100;
            } else {
                $score = 0;
                $relevance = 50;
                $completeness = 100;
            }
        } else {
            // For text/essay questions
            $expectedKeywords = json_decode($question['expected_keywords'] ?? '[]', true) ?: [];
            $answerLower = strtolower($answerText);
            
            // Check keywords
            foreach ($expectedKeywords as $keyword) {
                if (strpos($answerLower, strtolower($keyword)) !== false) {
                    $matchedKeywords[] = $keyword;
                }
            }
            
            // Calculate scores
            $keywordScore = count($expectedKeywords) > 0 
                ? (count($matchedKeywords) / count($expectedKeywords)) * 100 
                : 50;
            
            // Word count check
            $wordCount = str_word_count($answerText);
            $minWords = $question['min_word_count'] ?? 50;
            $completeness = min(100, ($wordCount / $minWords) * 100);
            
            // Simple relevance check (length-based for now)
            $relevance = min(100, ($wordCount / 20) * 100);
            
            // Overall score
            $score = round(($keywordScore * 0.5) + ($completeness * 0.3) + ($relevance * 0.2));
        }
        
        return [
            'score' => max(0, min(100, $score)),
            'relevance' => round($relevance),
            'completeness' => round($completeness),
            'keywords' => $matchedKeywords
        ];
    }
    
    private function completeSession($sessionId) {
        // Calculate total score
        $avgScore = $this->db->query("
            SELECT AVG(ai_score) as avg FROM interview_answers WHERE session_id = $sessionId
        ")->fetch_assoc()['avg'];
        
        $totalScore = round($avgScore ?? 0);
        
        // Determine recommendation
        $recommendation = 'review';
        if ($totalScore >= 80) {
            $recommendation = 'pass';
        } elseif ($totalScore < 50) {
            $recommendation = 'fail';
        }
        
        // Update session
        $this->db->query("
            UPDATE interview_sessions 
            SET status = 'completed', completed_at = NOW(), 
                total_score = $totalScore, ai_recommendation = '$recommendation'
            WHERE id = $sessionId
        ");
        
        // Get application to update
        $session = $this->db->query("SELECT application_id FROM interview_sessions WHERE id = $sessionId")->fetch_assoc();
        
        // Update application interview score
        $this->db->query("UPDATE applications SET interview_score = $totalScore WHERE id = {$session['application_id']}");
    }
}
