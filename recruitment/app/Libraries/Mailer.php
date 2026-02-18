<?php
/**
 * Mailer Library
 * Handles email sending with template support
 * Supports both SMTP and simulation mode
 */

// Load PHPMailer via Composer
$autoloadPath = dirname(APPPATH) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}
class Mailer {
    
    private $db;
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $smtpEncryption;
    private $fromEmail;
    private $fromName;
    private $simulationMode = true; // Set false when SMTP configured
    private $senderUserId; // User ID of the person sending the email
    
    public function __construct($db, $senderUserId = null) {
        $this->db = $db;
        $this->senderUserId = $senderUserId;
        $this->loadSettings($senderUserId);
    }
    
    /**
     * Load SMTP settings from database
     * Tries user-specific config first, then falls back to global settings
     */
    private function loadSettings($userId = null) {
        // Try loading user-specific SMTP config first
        if ($userId) {
            $stmt = $this->db->prepare("
                SELECT * FROM user_smtp_configs 
                WHERE user_id = ? AND is_active = 1
                LIMIT 1
            ");
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $userConfig = $stmt->get_result()->fetch_assoc();
            
            if ($userConfig) {
                // Use user's own SMTP settings
                $this->smtpHost = $userConfig['smtp_host'];
                $this->smtpPort = $userConfig['smtp_port'];
                $this->smtpUsername = $userConfig['smtp_username'];
                $this->smtpPassword = $userConfig['smtp_password']; // Use password directly (decryptSmtpPassword function doesn't exist)
                $this->smtpEncryption = $userConfig['smtp_encryption'];
                $this->fromEmail = $userConfig['smtp_from_email'];
                $this->fromName = $userConfig['smtp_from_name'];
                $this->simulationMode = false; // User has configured SMTP
                return; // Exit early, user config loaded
            }
        }
        
        // Fallback to global settings (original code)
        $settings = [
            'smtp_host' => '',
            'smtp_port' => '465',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'ssl',
            'smtp_from_email' => 'recruitment@indoceancrew.co.id',
            'smtp_from_name' => 'PT Indo Ocean Crew Services'
        ];
        
        $result = $this->db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%'");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        
        $this->smtpHost = $settings['smtp_host'];
        $this->smtpPort = $settings['smtp_port'];
        $this->smtpUsername = $settings['smtp_username'];
        $this->smtpPassword = $settings['smtp_password'];
        $this->smtpEncryption = $settings['smtp_encryption'];
        $this->fromEmail = $settings['smtp_from_email'];
        $this->fromName = $settings['smtp_from_name'];
        
        // Enable real SMTP if credentials are set
        $this->simulationMode = empty($this->smtpHost) || empty($this->smtpUsername) || empty($this->smtpPassword);
    }
    
    /**
     * Send email using template
     */
    public function sendTemplate($userId, $templateSlug, $additionalData = []) {
        // Get template
        $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE slug = ? AND is_active = 1");
        $stmt->bind_param('s', $templateSlug);
        $stmt->execute();
        $template = $stmt->get_result()->fetch_assoc();
        
        if (!$template) {
            return ['success' => false, 'message' => 'Email template not found'];
        }
        
        // Get user data
        $stmt = $this->db->prepare("
            SELECT u.*, ap.date_of_birth, ap.nationality
            FROM users u
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            WHERE u.id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Prepare variables for parsing
        $variables = array_merge([
            'name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '-',
            'date' => date('d M Y'),
            'time' => date('H:i'),
            'year' => date('Y')
        ], $additionalData);
        
        // Parse template
        $subject = $this->parseVariables($template['subject'], $variables);
        $body = $this->parseVariables($template['body'], $variables);
        
        // Send email
        return $this->send($user['email'], $user['full_name'], $subject, $body, $userId, $template['id']);
    }
    
    /**
     * Send email directly (with optional attachments)
     * @param array $attachments Array of ['path' => '/path/to/file', 'name' => 'filename.pdf']
     */
    public function send($toEmail, $toName, $subject, $body, $userId = null, $templateId = null, $applicationId = null, $attachments = []) {
        // Log the email
        $logId = $this->logEmail($templateId, $toEmail, $toName, $subject, $body, $applicationId);
        
        if ($this->simulationMode) {
            // Simulation mode - just mark as sent
            $this->updateEmailLog($logId, 'sent', null);
            $attachInfo = count($attachments) > 0 ? ' (' . count($attachments) . ' attachment(s))' : '';
            return [
                'success' => true, 
                'message' => 'Email logged (simulation mode)' . $attachInfo,
                'log_id' => $logId
            ];
        }
        
        // Real SMTP sending using PHPMailer or mail()
        try {
            // Try using PHPMailer if available
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return $this->sendWithPHPMailer($toEmail, $toName, $subject, $body, $logId, $attachments);
            }
            
            // Fallback to PHP mail() with MIME for attachments
            $fullBody = $this->wrapInTemplate($body);
            
            if (empty($attachments)) {
                // Simple send without attachments
                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=UTF-8',
                    'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
                    'Reply-To: ' . $this->fromEmail
                ];
                $sent = mail($toEmail, $subject, $fullBody, implode("\r\n", $headers));
            } else {
                // Multipart MIME with attachments
                $boundary = md5(time());
                $headers = [
                    'MIME-Version: 1.0',
                    'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
                    'Reply-To: ' . $this->fromEmail,
                    'Content-Type: multipart/mixed; boundary="' . $boundary . '"'
                ];
                
                $message = "--$boundary\r\n";
                $message .= "Content-Type: text/html; charset=UTF-8\r\n";
                $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $message .= $fullBody . "\r\n";
                
                foreach ($attachments as $att) {
                    if (file_exists($att['path'])) {
                        $fileContent = chunk_split(base64_encode(file_get_contents($att['path'])));
                        $mimeType = mime_content_type($att['path']);
                        $fileName = $att['name'] ?? basename($att['path']);
                        $message .= "--$boundary\r\n";
                        $message .= "Content-Type: $mimeType; name=\"$fileName\"\r\n";
                        $message .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n";
                        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                        $message .= $fileContent . "\r\n";
                    }
                }
                $message .= "--$boundary--";
                
                $sent = mail($toEmail, $subject, $message, implode("\r\n", $headers));
            }
            
            if ($sent) {
                $this->updateEmailLog($logId, 'sent', null);
                return ['success' => true, 'message' => 'Email sent successfully', 'log_id' => $logId];
            } else {
                $this->updateEmailLog($logId, 'failed', 'PHP mail() failed');
                return ['success' => false, 'message' => 'Failed to send email'];
            }
        } catch (Exception $e) {
            $this->updateEmailLog($logId, 'failed', $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
 * Send with PHPMailer (if installed) - with attachment support
 */
private function sendWithPHPMailer($toEmail, $toName, $subject, $body, $logId, $attachments = []) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = $this->smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $this->smtpUsername;
        $mail->Password = $this->smtpPassword;
        $mail->Timeout = 10; // Short timeout to prevent hanging
        $mail->SMTPKeepAlive = false; // Don't keep connection alive
        
        // Set encryption based on setting
        if ($this->smtpEncryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port = intval($this->smtpPort);
        
        // Allow self-signed certificates (needed for many hosting providers)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        
        $mail->setFrom($this->fromEmail, $this->fromName);
        $mail->addAddress($toEmail, $toName);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $this->wrapInTemplate($body);
        
        // Add attachments
        foreach ($attachments as $att) {
            if (file_exists($att['path'])) {
                $mail->addAttachment($att['path'], $att['name'] ?? basename($att['path']));
            }
        }
        
        $mail->send();
        
        $this->updateEmailLog($logId, 'sent', null);
        return ['success' => true, 'message' => 'Email sent successfully', 'log_id' => $logId];
        
    } catch (Exception $e) {
        $errorMsg = $mail->ErrorInfo;
        
        // Make error messages more user-friendly
        if (strpos($errorMsg, 'Could not connect') !== false) {
            $errorMsg = 'Cannot connect to SMTP server. Please check host and port settings.';
        } elseif (strpos($errorMsg, 'SMTP Error: Could not authenticate') !== false) {
            $errorMsg = 'SMTP authentication failed. Please check username and password.';
        } elseif (strpos($errorMsg, 'timed out') !== false || strpos($errorMsg, 'Timeout') !== false) {
            $errorMsg = 'Connection timeout. SMTP server not responding.';
        }
        
        $this->updateEmailLog($logId, 'failed', $errorMsg);
        return ['success' => false, 'message' => $errorMsg];
    }
}
    
    /**
     * Parse template variables
     */
    private function parseVariables($text, $variables) {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }
    
    /**
     * Wrap email body in HTML template
     */
    private function wrapInTemplate($body) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e3a5f; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #fff; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; background: #f5f5f5; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>PT Indo Ocean Crew Services</h2>
                </div>
                <div class="content">
                    ' . $body . '
                </div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' PT Indo Ocean Crew Services. All rights reserved.</p>
                    <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Log email to database (matches actual email_logs table)
     */
    private function logEmail($templateId, $toEmail, $toName, $subject, $body, $applicationId = null) {
        $stmt = $this->db->prepare("
            INSERT INTO email_logs (template_id, application_id, to_email, to_name, subject, body, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param('iissss', $templateId, $applicationId, $toEmail, $toName, $subject, $body);
        $stmt->execute();
        return $this->db->insert_id;
    }
    
    /**
     * Update email log status
     */
    private function updateEmailLog($logId, $status, $errorMessage) {
        $sentAt = $status === 'sent' ? date('Y-m-d H:i:s') : null;
        $stmt = $this->db->prepare("UPDATE email_logs SET status = ?, error_message = ?, sent_at = ? WHERE id = ?");
        $stmt->bind_param('sssi', $status, $errorMessage, $sentAt, $logId);
        $stmt->execute();
    }
    
    /**
     * Get email templates
     */
    public function getTemplates() {
        $result = $this->db->query("SELECT * FROM email_templates ORDER BY id");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Get email logs
     */
    public function getLogs($limit = 50) {
        $result = $this->db->query("
            SELECT el.*, et.name as template_name
            FROM email_logs el
            LEFT JOIN email_templates et ON el.template_id = et.id
            ORDER BY el.created_at DESC
            LIMIT $limit
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
