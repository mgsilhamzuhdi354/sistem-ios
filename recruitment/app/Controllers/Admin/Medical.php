<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Medical Controller
 */
class Medical extends BaseController {
    
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
            SELECT mc.*, u.full_name, u.email, v.title as vacancy_title,
                   p.full_name as processed_by_name
            FROM medical_checkups mc
            JOIN applications a ON mc.application_id = a.id
            JOIN users u ON mc.user_id = u.id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN users p ON mc.processed_by = p.id
            WHERE 1=1
        ";
        
        if ($status) {
            $query .= " AND mc.status = '" . $this->db->real_escape_string($status) . "'";
        }
        
        $query .= " ORDER BY mc.scheduled_date DESC";
        
        $checkups = $this->db->query($query)->fetch_all(MYSQLI_ASSOC);
        
        // Get stats
        $stats = [
            'scheduled' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE status = 'scheduled'")->fetch_assoc()['c'],
            'in_progress' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE status = 'in_progress'")->fetch_assoc()['c'],
            'fit' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE result = 'fit'")->fetch_assoc()['c'],
            'unfit' => $this->db->query("SELECT COUNT(*) as c FROM medical_checkups WHERE result = 'unfit'")->fetch_assoc()['c'],
        ];
        
        $this->view('admin/medical/index', [
            'checkups' => $checkups,
            'stats' => $stats,
            'filter_status' => $status,
            'pageTitle' => 'Medical Check-ups'
        ]);
    }
    
    public function schedule($applicationId) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/applicants/' . $applicationId));
        }
        
        validate_csrf();
        
        $scheduledDate = $this->input('scheduled_date');
        $scheduledTime = $this->input('scheduled_time');
        $hospitalName = $this->input('hospital_name');
        $hospitalAddress = $this->input('hospital_address');
        
        // Get user id from application
        $app = $this->db->query("SELECT user_id FROM applications WHERE id = $applicationId")->fetch_assoc();
        
        if (!$app) {
            flash('error', 'Application not found');
            $this->redirect(url('/admin/applicants'));
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO medical_checkups (
                application_id, user_id, scheduled_date, scheduled_time,
                hospital_name, hospital_address, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, 'scheduled', NOW())
        ");
        $stmt->bind_param('iissss', 
            $applicationId, $app['user_id'], $scheduledDate, $scheduledTime,
            $hospitalName, $hospitalAddress
        );
        
        if ($stmt->execute()) {
            // Update application status
            $this->db->query("UPDATE applications SET status_id = 4, status_updated_at = NOW() WHERE id = $applicationId");
            
            // Notify applicant
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'Medical Check-up Scheduled', ?, 'info', ?, NOW())
            ");
            $message = "Your medical check-up has been scheduled for $scheduledDate at $scheduledTime at $hospitalName.";
            $actionUrl = url('/applicant/applications/' . $applicationId);
            $notifStmt->bind_param('iss', $app['user_id'], $message, $actionUrl);
            $notifStmt->execute();
            
            // Send email notification
            try {
                require_once APPPATH . 'Libraries/Mailer.php';
                $mailer = new Mailer($this->db);
                $mailer->sendTemplate($app['user_id'], 'medical_checkup_scheduled', [
                    'scheduled_date' => date('d F Y', strtotime($scheduledDate)),
                    'scheduled_time' => $scheduledTime,
                    'hospital_name' => $hospitalName,
                    'hospital_address' => $hospitalAddress
                ]);
            } catch (Exception $emailErr) {
                error_log('Medical schedule email error: ' . $emailErr->getMessage());
            }
            
            flash('success', 'Medical check-up scheduled');
        } else {
            flash('error', 'Failed to schedule medical check-up');
        }
        
        $this->redirect(url('/admin/applicants/' . $applicationId));
    }
    
    public function result($checkupId) {
        if (!$this->isPost()) {
            $this->redirect(url('/admin/medical'));
        }
        
        validate_csrf();
        
        $result = $this->input('result'); // fit, unfit, conditional
        $resultNotes = $this->input('result_notes');
        $validUntil = $this->input('valid_until');
        $adminId = $_SESSION['user_id'];
        
        // Handle file upload
        $resultDocPath = null;
        $file = $this->file('result_document');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = 'mcu_' . $checkupId . '_' . time() . '.' . $extension;
            $uploadDir = FCPATH . 'uploads/medical/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                $resultDocPath = 'uploads/medical/' . $fileName;
            }
        }
        
        $stmt = $this->db->prepare("
            UPDATE medical_checkups SET
                status = 'completed',
                result = ?,
                result_notes = ?,
                result_document_path = COALESCE(?, result_document_path),
                valid_until = ?,
                processed_by = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param('ssssii', $result, $resultNotes, $resultDocPath, $validUntil, $adminId, $checkupId);
        
        if ($stmt->execute()) {
            // Get checkup info
            $checkup = $this->db->query("SELECT application_id, user_id FROM medical_checkups WHERE id = $checkupId")->fetch_assoc();
            
            // If fit, move to next stage
            if ($result === 'fit') {
                $this->db->query("UPDATE applications SET status_id = 5, status_updated_at = NOW() WHERE id = {$checkup['application_id']}");
            } elseif ($result === 'unfit') {
                $this->db->query("UPDATE applications SET status_id = 7, rejection_reason = 'Medical check-up: Unfit', status_updated_at = NOW() WHERE id = {$checkup['application_id']}");
            }
            
            // Notify applicant
            $resultText = $result === 'fit' ? 'Fit for Duty' : ($result === 'unfit' ? 'Unfit' : 'Conditional');
            $notifStmt = $this->db->prepare("
                INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
                VALUES (?, 'Medical Result', ?, ?, ?, NOW())
            ");
            $message = "Your medical check-up result: $resultText";
            $notifType = $result === 'fit' ? 'success' : ($result === 'unfit' ? 'error' : 'warning');
            $actionUrl = url('/applicant/applications/' . $checkup['application_id']);
            $notifStmt->bind_param('isss', $checkup['user_id'], $message, $notifType, $actionUrl);
            $notifStmt->execute();
            
            // Send email notification
            try {
                require_once APPPATH . 'Libraries/Mailer.php';
                $mailer = new Mailer($this->db);
                $mailer->sendTemplate($checkup['user_id'], 'medical_result', [
                    'result' => $resultText,
                    'result_notes' => $resultNotes ?? '-',
                    'valid_until' => $validUntil ? date('d F Y', strtotime($validUntil)) : '-'
                ]);
            } catch (Exception $emailErr) {
                error_log('Medical result email error: ' . $emailErr->getMessage());
            }
            
            flash('success', 'Medical result recorded');
        } else {
            flash('error', 'Failed to save result');
        }
        
        $this->redirect(url('/admin/medical'));
    }
}
