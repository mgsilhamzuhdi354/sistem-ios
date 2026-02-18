<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Email Controller
 * Gmail-like email client for crewing staff
 */
class Email extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            redirect(url('/login'));
        }
    }

    /**
     * Main email page - compose + sent items
     */
    public function index()
    {
        $crewingId = $_SESSION['user_id'];

        // Get current user info (sender)
        $currentUser = $this->db->query("SELECT full_name, email FROM users WHERE id = $crewingId")->fetch_assoc();

        // Get email templates
        $templates = $this->db->query("
            SELECT * FROM email_templates WHERE is_active = 1 ORDER BY name
        ")->fetch_all(MYSQLI_ASSOC);

        // Get email logs (sent items) - last 100
        $logs = $this->db->query("
            SELECT el.*, et.name as template_name, et.slug as template_slug
            FROM email_logs el
            LEFT JOIN email_templates et ON el.template_id = et.id
            ORDER BY el.created_at DESC
            LIMIT 100
        ")->fetch_all(MYSQLI_ASSOC);

        // Get applicants assigned to this crewing (for quick select)
        $applicants = $this->db->query("
            SELECT a.id as application_id, u.id as user_id, u.full_name, u.email, u.phone,
                   jv.title as vacancy_title, s.name as status_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            LEFT JOIN application_statuses s ON a.status_id = s.id
            WHERE aa.assigned_to = $crewingId
            ORDER BY u.full_name
        ")->fetch_all(MYSQLI_ASSOC);

        // Get per-user SMTP settings for from info
        $stmtSmtp = $this->db->prepare("SELECT * FROM user_smtp_configs WHERE user_id = ? AND is_active = 1 LIMIT 1");
        $stmtSmtp->bind_param('i', $crewingId);
        $stmtSmtp->execute();
        $userSmtp = $stmtSmtp->get_result()->fetch_assoc();

        $smtpConfigured = !empty($userSmtp['smtp_host']);
        $fromEmail = !empty($userSmtp['smtp_from_email']) ? $userSmtp['smtp_from_email'] : ($currentUser['email'] ?? 'noreply@indoceancrew.com');
        $fromName = !empty($userSmtp['smtp_from_name']) ? $userSmtp['smtp_from_name'] : ($currentUser['full_name'] ?? 'PT Indo Ocean Crew Services');

        $this->view('crewing/email/index', [
            'pageTitle' => 'Email Center',
            'templates' => $templates,
            'logs' => $logs,
            'applicants' => $applicants,
            'smtpConfigured' => $smtpConfigured,
            'fromEmail' => $fromEmail,
            'fromName' => $fromName,
            'currentUser' => $currentUser
        ]);
    }

    /**
     * Send email via AJAX (supports free-form email address)
     */
    public function send()
    {
        // Suppress any PHP warnings/notices from corrupting JSON output
        ob_start();
        
        if (!$this->isPost()) {
            ob_end_clean();
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        // Can send to user_id OR free-form email
        $userId = intval($this->input('user_id') ?: 0);
        $toEmail = trim($this->input('to_email') ?: '');
        $toName = trim($this->input('to_name') ?: '');
        $templateId = intval($this->input('template_id') ?: 0);
        $customSubject = trim($this->input('custom_subject') ?: '');
        $customBody = trim($this->input('custom_body') ?: '');

        // Validate recipient
        if (!$userId && empty($toEmail)) {
            ob_end_clean();
            return $this->json(['success' => false, 'message' => 'Masukkan email penerima']);
        }

        // If user_id provided, get user info
        if ($userId > 0) {
            $user = $this->db->query("SELECT full_name, email FROM users WHERE id = $userId")->fetch_assoc();
            if ($user) {
                $toEmail = $user['email'];
                $toName = $user['full_name'];
            }
        }

        // Validate email format
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            ob_end_clean();
            return $this->json(['success' => false, 'message' => 'Format email tidak valid']);
        }

        if (empty($toName)) {
            $toName = explode('@', $toEmail)[0]; // Use email prefix as name
        }

        // Handle file attachments
        $attachments = [];
        $uploadDir = dirname(APPPATH) . '/public/uploads/email_attachments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!empty($_FILES['attachments'])) {
            $files = $_FILES['attachments'];
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                             'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                             'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            $maxSize = 10 * 1024 * 1024;

            $fileCount = is_array($files['name']) ? count($files['name']) : 1;
            
            for ($i = 0; $i < $fileCount; $i++) {
                $name = is_array($files['name']) ? $files['name'][$i] : $files['name'];
                $tmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
                $size = is_array($files['size']) ? $files['size'][$i] : $files['size'];
                $error = is_array($files['error']) ? $files['error'][$i] : $files['error'];

                if ($error !== UPLOAD_ERR_OK || empty($name)) continue;

                if ($size > $maxSize) {
                    ob_end_clean();
                    return $this->json(['success' => false, 'message' => "File '$name' terlalu besar (max 10MB)"]);
                }

                $mimeType = mime_content_type($tmpName);
                if (!in_array($mimeType, $allowedTypes)) {
                    ob_end_clean();
                    return $this->json(['success' => false, 'message' => "Tipe file '$name' tidak didukung"]);
                }

                $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $name);
                $destPath = $uploadDir . $safeName;
                
                if (move_uploaded_file($tmpName, $destPath)) {
                    $attachments[] = ['path' => $destPath, 'name' => $name];
                }
            }
        }

        try {
            require_once APPPATH . 'Libraries/Mailer.php';
            // Pass sender user ID for per-user SMTP configuration
            $mailer = new Mailer($this->db, $_SESSION['user_id']);

            if ($templateId > 0 && $userId > 0) {
                // Send using template
                $template = $this->db->query("SELECT slug FROM email_templates WHERE id = $templateId")->fetch_assoc();
                if (!$template) {
                    ob_end_clean();
                    return $this->json(['success' => false, 'message' => 'Template tidak ditemukan']);
                }
                $result = $mailer->sendTemplate($userId, $template['slug']);
            } else {
                // Send custom email
                if (empty($customSubject) || empty($customBody)) {
                    ob_end_clean();
                    return $this->json(['success' => false, 'message' => 'Subject dan isi email harus diisi']);
                }
                
                // Try to get application_id if sending to a user (for tracking in pipeline)
                $applicationId = null;
                if ($userId > 0) {
                    $appStmt = $this->db->prepare("SELECT id FROM applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
                    $appStmt->bind_param('i', $userId);
                    $appStmt->execute();
                    $appResult = $appStmt->get_result()->fetch_assoc();
                    if ($appResult) {
                        $applicationId = $appResult['id'];
                    }
                }
                
                $result = $mailer->send($toEmail, $toName, $customSubject, $customBody, $userId ?: null, null, $applicationId, $attachments);
            }

            ob_end_clean();
            $attachCount = count($attachments);
            if (isset($result['success']) && $result['success']) {
                // Track medical checkup email
                if ($templateId > 0 && $userId > 0) {
                    $tpl = $this->db->query("SELECT slug FROM email_templates WHERE id = $templateId")->fetch_assoc();
                    if ($tpl && $tpl['slug'] === 'medical_checkup_scheduled') {
                        $this->db->query("UPDATE applications SET medical_email_sent_at = NOW() WHERE user_id = $userId");
                    }
                }
                $msg = 'Email berhasil dikirim ke ' . $toEmail . '!';
                if ($attachCount > 0) $msg .= " ($attachCount lampiran)";
                return $this->json(['success' => true, 'message' => $msg]);
            } else {
                return $this->json(['success' => true, 'message' => 'Email diantrekan (' . ($result['message'] ?? 'simulation mode') . ')']);
            }
        } catch (Exception $e) {
            ob_end_clean();
            return $this->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Get template preview
     */
    public function preview()
    {
        $templateId = intval($this->input('template_id'));
        if (!$templateId) {
            return $this->json(['success' => false]);
        }
        $template = $this->db->query("SELECT * FROM email_templates WHERE id = $templateId")->fetch_assoc();
        if (!$template) {
            return $this->json(['success' => false, 'message' => 'Template tidak ditemukan']);
        }
        return $this->json(['success' => true, 'template' => $template]);
    }
    
    /**
     * Delete sent email from logs
     */
    public function delete($id = null)
    {
        if (!$this->isPost() && !$id) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }
        
        // Get ID from URL parameter or POST
        $emailId = $id ?: intval($this->input('email_id'));
        
        if (!$emailId) {
            return $this->json(['success' => false, 'message' => 'Email ID required']);
        }
        
        // Delete from email_logs
        $stmt = $this->db->prepare("DELETE FROM email_logs WHERE id = ?");
        $stmt->bind_param('i', $emailId);
        
        if ($stmt->execute()) {
            return $this->json(['success' => true, 'message' => 'Email berhasil dihapus']);
        } else {
            return $this->json(['success' => false, 'message' => 'Gagal menghapus email']);
        }
    }
}
