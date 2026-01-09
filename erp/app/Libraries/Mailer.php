<?php
/**
 * PT Indo Ocean - ERP System
 * Email Helper - Simple mailer without external dependencies
 * Note: For production, recommend using PHPMailer for better SMTP support
 * 
 * KONFIGURASI:
 * 1. Copy config/email.example.php ke config/email.php
 * 2. Edit config/email.php dengan kredensial SMTP Anda
 */

namespace App\Libraries;

class Mailer
{
    private $config;
    private $errors = [];
    
    public function __construct()
    {
        // Try to load from config file first
        $configFile = dirname(dirname(__DIR__)) . '/config/email.php';
        
        if (file_exists($configFile)) {
            $fileConfig = include $configFile;
            $this->config = [
                'smtp_host' => $fileConfig['smtp_host'] ?? 'smtp.gmail.com',
                'smtp_port' => $fileConfig['smtp_port'] ?? 587,
                'smtp_secure' => $fileConfig['smtp_secure'] ?? 'tls',
                'smtp_user' => $fileConfig['smtp_user'] ?? '',
                'smtp_pass' => $fileConfig['smtp_pass'] ?? '',
                'from_email' => $fileConfig['from_email'] ?? 'noreply@ptindoocean.com',
                'from_name' => $fileConfig['from_name'] ?? 'PT Indo Ocean ERP',
                'debug' => $fileConfig['debug'] ?? false,
            ];
        } else {
            // Fallback to environment variables
            $this->config = [
                'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
                'smtp_port' => getenv('SMTP_PORT') ?: 587,
                'smtp_secure' => 'tls',
                'smtp_user' => getenv('SMTP_USER') ?: '',
                'smtp_pass' => getenv('SMTP_PASS') ?: '',
                'from_email' => getenv('MAIL_FROM') ?: 'noreply@ptindoocean.com',
                'from_name' => getenv('MAIL_FROM_NAME') ?: 'PT Indo Ocean ERP',
                'debug' => false,
            ];
        }
    }
    
    /**
     * Send email using SMTP with authentication
     */
    public function send($to, $subject, $body, $isHtml = true)
    {
        // If SMTP credentials are configured, use SMTP
        if (!empty($this->config['smtp_user']) && !empty($this->config['smtp_pass'])) {
            return $this->sendViaSMTP($to, $subject, $body, $isHtml);
        }
        
        // Fallback to PHP mail() (will likely fail on most systems)
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = $isHtml ? "Content-type: text/html; charset=UTF-8" : "Content-type: text/plain; charset=UTF-8";
        $headers[] = "From: {$this->config['from_name']} <{$this->config['from_email']}>";
        $headers[] = "Reply-To: {$this->config['from_email']}";
        
        $result = @mail($to, $subject, $body, implode("\r\n", $headers));
        
        if (!$result) {
            $this->errors[] = "Failed to send email to {$to}";
            return false;
        }
        
        return true;
    }
    
    /**
     * Send email via SMTP socket connection
     */
    private function sendViaSMTP($to, $subject, $body, $isHtml = true)
    {
        $host = $this->config['smtp_host'];
        $port = $this->config['smtp_port'];
        $user = $this->config['smtp_user'];
        $pass = $this->config['smtp_pass'];
        $from = $this->config['from_email'];
        $fromName = $this->config['from_name'];
        
        // Connect to SMTP server with TLS
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        
        $socket = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            $this->errors[] = "Failed to connect to SMTP server: {$errstr}";
            return false;
        }
        
        // Read greeting
        $this->smtpRead($socket);
        
        // EHLO
        $this->smtpWrite($socket, "EHLO localhost\r\n");
        $this->smtpRead($socket);
        
        // STARTTLS
        $this->smtpWrite($socket, "STARTTLS\r\n");
        $response = $this->smtpRead($socket);
        
        if (strpos($response, '220') === false) {
            $this->errors[] = "STARTTLS failed";
            fclose($socket);
            return false;
        }
        
        // Enable TLS encryption
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $this->errors[] = "Failed to enable TLS";
            fclose($socket);
            return false;
        }
        
        // EHLO again after TLS
        $this->smtpWrite($socket, "EHLO localhost\r\n");
        $this->smtpRead($socket);
        
        // AUTH LOGIN
        $this->smtpWrite($socket, "AUTH LOGIN\r\n");
        $response = $this->smtpRead($socket);
        
        if (strpos($response, '334') === false) {
            $this->errors[] = "AUTH LOGIN failed";
            fclose($socket);
            return false;
        }
        
        // Send username (base64)
        $this->smtpWrite($socket, base64_encode($user) . "\r\n");
        $this->smtpRead($socket);
        
        // Send password (base64)
        $this->smtpWrite($socket, base64_encode($pass) . "\r\n");
        $response = $this->smtpRead($socket);
        
        if (strpos($response, '235') === false) {
            $this->errors[] = "Authentication failed - check username/password";
            fclose($socket);
            return false;
        }
        
        // MAIL FROM
        $this->smtpWrite($socket, "MAIL FROM:<{$from}>\r\n");
        $this->smtpRead($socket);
        
        // RCPT TO
        $this->smtpWrite($socket, "RCPT TO:<{$to}>\r\n");
        $this->smtpRead($socket);
        
        // DATA
        $this->smtpWrite($socket, "DATA\r\n");
        $this->smtpRead($socket);
        
        // Build email content
        $contentType = $isHtml ? "text/html" : "text/plain";
        $message = "From: {$fromName} <{$from}>\r\n";
        $message .= "To: {$to}\r\n";
        $message .= "Subject: {$subject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: {$contentType}; charset=UTF-8\r\n";
        $message .= "\r\n";
        $message .= $body;
        $message .= "\r\n.\r\n";
        
        $this->smtpWrite($socket, $message);
        $response = $this->smtpRead($socket);
        
        // QUIT
        $this->smtpWrite($socket, "QUIT\r\n");
        fclose($socket);
        
        if (strpos($response, '250') !== false) {
            return true;
        }
        
        $this->errors[] = "Failed to send email: {$response}";
        return false;
    }
    
    private function smtpWrite($socket, $data)
    {
        if ($this->config['debug']) {
            echo "CLIENT: " . trim($data) . "\n";
        }
        fwrite($socket, $data);
    }
    
    private function smtpRead($socket)
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        if ($this->config['debug']) {
            echo "SERVER: " . trim($response) . "\n";
        }
        return $response;
    }
    
    /**
     * Send contract expiry notification
     */
    public function sendContractExpiryNotification($contract, $daysRemaining)
    {
        $subject = "[ALERT] Contract Expiring: {$contract['contract_no']} - {$daysRemaining} days left";
        
        $body = $this->getTemplate('contract_expiry', [
            'contract' => $contract,
            'days_remaining' => $daysRemaining
        ]);
        
        // Get admin emails
        $adminEmails = $this->getAdminEmails();
        
        $success = true;
        foreach ($adminEmails as $email) {
            if (!$this->send($email, $subject, $body)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Send document expiry notification
     */
    public function sendDocumentExpiryNotification($document, $daysRemaining)
    {
        $subject = "[ALERT] Document Expiring: {$document['document_name']} - {$daysRemaining} days left";
        
        $body = $this->getTemplate('document_expiry', [
            'document' => $document,
            'days_remaining' => $daysRemaining
        ]);
        
        $adminEmails = $this->getAdminEmails();
        
        $success = true;
        foreach ($adminEmails as $email) {
            if (!$this->send($email, $subject, $body)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Send payroll completed notification
     */
    public function sendPayrollNotification($payrollPeriod, $totalAmount)
    {
        $subject = "Payroll Completed: {$payrollPeriod['period_name']}";
        
        $body = $this->getTemplate('payroll_complete', [
            'period' => $payrollPeriod,
            'total' => $totalAmount
        ]);
        
        $adminEmails = $this->getAdminEmails();
        
        $success = true;
        foreach ($adminEmails as $email) {
            if (!$this->send($email, $subject, $body)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordReset($email, $resetToken, $userName)
    {
        $subject = "Password Reset Request - PT Indo Ocean";
        
        $resetUrl = BASE_URL . "auth/reset-password?token={$resetToken}";
        
        $body = $this->getTemplate('password_reset', [
            'name' => $userName,
            'reset_url' => $resetUrl
        ]);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send OTP code for 2FA login
     */
    public function sendOtpCode($email, $otpCode, $userName)
    {
        $subject = "Kode OTP Login - PT Indo Ocean ERP";
        
        $body = $this->getTemplate('otp_code', [
            'name' => $userName,
            'otp_code' => $otpCode
        ]);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send login notification
     */
    public function sendLoginNotification($email, $userName, $loginData)
    {
        $subject = "Login Alert - PT Indo Ocean ERP";
        
        $body = $this->getTemplate('login_notification', [
            'name' => $userName,
            'ip' => $loginData['ip'] ?? 'Unknown',
            'browser' => $loginData['browser'] ?? 'Unknown',
            'time' => $loginData['time'] ?? date('d M Y, H:i'),
            'location' => $loginData['location'] ?? 'Unknown'
        ]);
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Get email template
     */
    private function getTemplate($templateName, $data = [])
    {
        $templateFile = APPPATH . "Views/emails/{$templateName}.php";
        
        if (file_exists($templateFile)) {
            extract($data);
            ob_start();
            include $templateFile;
            return ob_get_clean();
        }
        
        // Default template
        return $this->getDefaultTemplate($templateName, $data);
    }
    
    /**
     * Default templates
     */
    private function getDefaultTemplate($type, $data)
    {
        $baseStyle = "
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0A2463; color: #D4AF37; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { padding: 15px; text-align: center; font-size: 12px; color: #666; }
                .btn { display: inline-block; padding: 12px 24px; background: #D4AF37; color: #0A2463; text-decoration: none; border-radius: 4px; font-weight: bold; }
                .alert { padding: 15px; border-radius: 4px; margin: 10px 0; }
                .alert-warning { background: #FEF3C7; border-left: 4px solid #F59E0B; }
                .alert-danger { background: #FEE2E2; border-left: 4px solid #EF4444; }
            </style>
        ";
        
        switch ($type) {
            case 'contract_expiry':
                return "
                    <html><head>{$baseStyle}</head><body>
                    <div class='container'>
                        <div class='header'><h1>PT Indo Ocean</h1><p>Contract Alert</p></div>
                        <div class='content'>
                            <div class='alert alert-warning'>
                                <strong>⚠️ Contract Expiring Soon!</strong>
                            </div>
                            <p><strong>Contract:</strong> {$data['contract']['contract_no']}</p>
                            <p><strong>Crew:</strong> {$data['contract']['crew_name']}</p>
                            <p><strong>Vessel:</strong> {$data['contract']['vessel_name']}</p>
                            <p><strong>Expires:</strong> " . date('d M Y', strtotime($data['contract']['sign_off_date'])) . "</p>
                            <p><strong>Days Remaining:</strong> <span style='color:#EF4444; font-weight:bold;'>{$data['days_remaining']} days</span></p>
                            <p style='margin-top:20px;'><a href='" . BASE_URL . "contracts/{$data['contract']['id']}' class='btn'>View Contract</a></p>
                        </div>
                        <div class='footer'>PT Indo Ocean ERP System</div>
                    </div>
                    </body></html>
                ";
                
            case 'document_expiry':
                return "
                    <html><head>{$baseStyle}</head><body>
                    <div class='container'>
                        <div class='header'><h1>PT Indo Ocean</h1><p>Document Alert</p></div>
                        <div class='content'>
                            <div class='alert alert-danger'>
                                <strong>📄 Document Expiring!</strong>
                            </div>
                            <p><strong>Document:</strong> {$data['document']['document_name']}</p>
                            <p><strong>Crew:</strong> {$data['document']['crew_name']}</p>
                            <p><strong>Expires:</strong> " . date('d M Y', strtotime($data['document']['expiry_date'])) . "</p>
                            <p><strong>Days Remaining:</strong> <span style='color:#EF4444; font-weight:bold;'>{$data['days_remaining']} days</span></p>
                        </div>
                        <div class='footer'>PT Indo Ocean ERP System</div>
                    </div>
                    </body></html>
                ";
                
            case 'payroll_complete':
                return "
                    <html><head>{$baseStyle}</head><body>
                    <div class='container'>
                        <div class='header'><h1>PT Indo Ocean</h1><p>Payroll Notification</p></div>
                        <div class='content'>
                            <h2>✅ Payroll Processed</h2>
                            <p><strong>Period:</strong> {$data['period']['period_name']}</p>
                            <p><strong>Total Amount:</strong> $" . number_format($data['total'], 2) . "</p>
                            <p style='margin-top:20px;'><a href='" . BASE_URL . "payroll' class='btn'>View Payroll</a></p>
                        </div>
                        <div class='footer'>PT Indo Ocean ERP System</div>
                    </div>
                    </body></html>
                ";
                
            case 'password_reset':
                return "
                    <html><head>{$baseStyle}</head><body>
                    <div class='container'>
                        <div class='header'><h1>PT Indo Ocean</h1><p>Password Reset</p></div>
                        <div class='content'>
                            <p>Hi {$data['name']},</p>
                            <p>We received a request to reset your password. Click the button below to set a new password:</p>
                            <p style='text-align:center; margin:30px 0;'>
                                <a href='{$data['reset_url']}' class='btn'>Reset Password</a>
                            </p>
                            <p style='color:#666; font-size:12px;'>This link will expire in 1 hour. If you didn't request this, please ignore this email.</p>
                        </div>
                        <div class='footer'>PT Indo Ocean ERP System</div>
                    </div>
                    </body></html>
                ";
                
            case 'otp_code':
                return "
                    <html><head>{$baseStyle}</head><body>
                    <div class='container'>
                        <div class='header'><h1>PT Indo Ocean</h1><p>Kode Verifikasi Login</p></div>
                        <div class='content'>
                            <p>Halo {$data['name']},</p>
                            <p>Kode OTP untuk login ke sistem ERP:</p>
                            <div style='text-align:center; margin:30px 0;'>
                                <div style='display:inline-block; background:#0A2463; color:#D4AF37; font-size:32px; font-weight:bold; letter-spacing:8px; padding:20px 40px; border-radius:12px;'>
                                    {$data['otp_code']}
                                </div>
                            </div>
                            <p style='color:#666; font-size:14px;'>Kode ini berlaku selama <strong>5 menit</strong>.</p>
                            <div class='alert alert-warning'>
                                <strong>⚠️ Jangan bagikan kode ini kepada siapapun!</strong>
                            </div>
                        </div>
                        <div class='footer'>PT Indo Ocean ERP System</div>
                    </div>
                    </body></html>
                ";
                
            case 'login_notification':
                return "
                    <html><head>{$baseStyle}</head><body>
                    <div class='container'>
                        <div class='header'><h1>PT Indo Ocean</h1><p>Notifikasi Login</p></div>
                        <div class='content'>
                            <p>Halo {$data['name']},</p>
                            <p>Akun Anda baru saja <strong>login</strong> ke sistem ERP.</p>
                            <div style='background:#f8fafc; border-radius:8px; padding:20px; margin:20px 0;'>
                                <p style='margin:5px 0;'><strong>🕐 Waktu:</strong> {$data['time']}</p>
                                <p style='margin:5px 0;'><strong>🌐 IP Address:</strong> {$data['ip']}</p>
                                <p style='margin:5px 0;'><strong>💻 Browser:</strong> {$data['browser']}</p>
                            </div>
                            <div class='alert alert-warning'>
                                <strong>Bukan Anda?</strong> Segera ubah password Anda dan hubungi administrator.
                            </div>
                        </div>
                        <div class='footer'>PT Indo Ocean ERP System</div>
                    </div>
                    </body></html>
                ";
                
            default:
                return "<html><body><p>Notification from PT Indo Ocean ERP</p></body></html>";
        }
    }
    
    /**
     * Get admin email addresses
     */
    private function getAdminEmails()
    {
        // Return admin email - use the configured Gmail
        return ['indooceancrewservice@gmail.com'];
    }
    
    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
