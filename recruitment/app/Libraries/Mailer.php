<?php
/**
 * Mailer Library
 * Handles email sending with template support
 * Supports both SMTP (Gmail) and simulation mode
 */
class Mailer {
    
    private $db;
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $fromEmail;
    private $fromName;
    private $simulationMode = true; // Set false when SMTP configured
    
    public function __construct($db) {
        $this->db = $db;
        $this->loadSettings();
    }
    
    /**
     * Load SMTP settings from database
     */
    private function loadSettings() {
        $settings = [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => '587',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_from_email' => 'recruitment@indoceancrew.com',
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
        $this->fromEmail = $settings['smtp_from_email'];
        $this->fromName = $settings['smtp_from_name'];
        
        // Enable real SMTP if credentials are set
        $this->simulationMode = empty($this->smtpUsername) || empty($this->smtpPassword);
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
     * Send email directly
     */
    public function send($toEmail, $toName, $subject, $body, $userId = null, $templateId = null, $applicationId = null) {
        // Log the email
        $logId = $this->logEmail($userId, $applicationId, $templateId, $subject, $toEmail, $toName);
        
        if ($this->simulationMode) {
            // Simulation mode - just mark as sent
            $this->updateEmailLog($logId, 'sent', null);
            return [
                'success' => true, 
                'message' => 'Email logged (simulation mode - no SMTP configured)',
                'log_id' => $logId
            ];
        }
        
        // Real SMTP sending using PHPMailer or mail()
        try {
            // Try using PHPMailer if available
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return $this->sendWithPHPMailer($toEmail, $toName, $subject, $body, $logId);
            }
            
            // Fallback to PHP mail()
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
                'Reply-To: ' . $this->fromEmail
            ];
            
            $fullBody = $this->wrapInTemplate($body);
            
            if (mail($toEmail, $subject, $fullBody, implode("\r\n", $headers))) {
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
     * Send with PHPMailer (if installed)
     */
    private function sendWithPHPMailer($toEmail, $toName, $subject, $body, $logId) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->smtpPort;
            
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $this->wrapInTemplate($body);
            
            $mail->send();
            
            $this->updateEmailLog($logId, 'sent', null);
            return ['success' => true, 'message' => 'Email sent successfully', 'log_id' => $logId];
            
        } catch (Exception $e) {
            $this->updateEmailLog($logId, 'failed', $mail->ErrorInfo);
            return ['success' => false, 'message' => $mail->ErrorInfo];
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
     * Log email to database
     */
    private function logEmail($userId, $applicationId, $templateId, $subject, $toEmail, $toName) {
        $stmt = $this->db->prepare("
            INSERT INTO email_logs (user_id, application_id, template_id, subject, recipient_email, recipient_name, status)
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param('iiisss', $userId, $applicationId, $templateId, $subject, $toEmail, $toName);
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
