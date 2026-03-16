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
    private $channel; // 'payslip' or 'otp'
    
    // Gmail config for OTP/login emails
    private const OTP_CONFIG = [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_secure' => 'tls',
        'smtp_user' => 'indooceancrewservice@gmail.com',
        'smtp_pass' => 'kabpgxlpzqkznnla',
        'from_email' => 'indooceancrewservice@gmail.com',
        'from_name' => 'PT Indo Ocean ERP',
        'debug' => false,
    ];
    
    /**
     * @param string $channel 'payslip' for payroll emails, 'otp' for OTP/login emails
     */
    public function __construct($channel = 'payslip')
    {
        $this->channel = $channel;
        
        // OTP/Login channel: always use Gmail
        if ($channel === 'otp') {
            $this->config = self::OTP_CONFIG;
            return;
        }
        
        // Payslip channel: DB settings → config file → environment variables → Gmail fallback
        $dbConfig = $this->loadFromDatabase();
        
        if ($dbConfig && !empty($dbConfig['smtp_pass'])) {
            $this->config = $dbConfig;
        } else {
            // Try to load from config file
            $configFile = dirname(dirname(__DIR__)) . '/config/email.php';
            
            if (file_exists($configFile)) {
                $fileConfig = include $configFile;
                if (!empty($fileConfig['smtp_pass'])) {
                    $this->config = [
                        'smtp_host' => $fileConfig['smtp_host'] ?? 'mail.indooceancrew.co.id',
                        'smtp_port' => $fileConfig['smtp_port'] ?? 465,
                        'smtp_secure' => $fileConfig['smtp_secure'] ?? 'ssl',
                        'smtp_user' => $fileConfig['smtp_user'] ?? '',
                        'smtp_pass' => $fileConfig['smtp_pass'] ?? '',
                        'from_email' => $fileConfig['from_email'] ?? 'ios@indooceancrew.co.id',
                        'from_name' => $fileConfig['from_name'] ?? 'PT Indo Ocean ERP',
                        'debug' => $fileConfig['debug'] ?? false,
                    ];
                    return;
                }
            }
            
            // Check environment variables
            $envPass = getenv('SMTP_PASS');
            if (!empty($envPass)) {
                $this->config = [
                    'smtp_host' => getenv('SMTP_HOST') ?: 'mail.indooceancrew.co.id',
                    'smtp_port' => getenv('SMTP_PORT') ?: 465,
                    'smtp_secure' => 'ssl',
                    'smtp_user' => getenv('SMTP_USER') ?: '',
                    'smtp_pass' => $envPass,
                    'from_email' => getenv('MAIL_FROM') ?: 'ios@indooceancrew.co.id',
                    'from_name' => getenv('MAIL_FROM_NAME') ?: 'PT Indo Ocean ERP',
                    'debug' => false,
                ];
                return;
            }
            
            // Fallback to Gmail (same as OTP) — guaranteed to work
            $this->config = self::OTP_CONFIG;
            error_log('[MAILER] No payslip SMTP config found, using Gmail fallback');
        }
    }
    
    /**
     * Load SMTP config from database settings table
     */
    private function loadFromDatabase()
    {
        try {
            $dbConfigAll = require APPPATH . 'Config/Database.php';
            $dbConf = $dbConfigAll['default'] ?? $dbConfigAll;
            $db = @new \mysqli(
                $dbConf['hostname'] ?? $dbConf['host'] ?? 'localhost',
                $dbConf['username'] ?? 'root',
                $dbConf['password'] ?? '',
                $dbConf['database'] ?? 'erp_db',
                $dbConf['port'] ?? 3306
            );
            
            if ($db->connect_error) {
                return null;
            }
            
            $result = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'email'");
            if (!$result || $result->num_rows === 0) {
                $db->close();
                return null;
            }
            
            $settings = [];
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            $db->close();
            
            // Only use DB config if smtp_user is set (meaning it was configured)
            if (empty($settings['smtp_user'])) {
                return null;
            }
            
            return [
                'smtp_host' => $settings['smtp_host'] ?? 'mail.indooceancrew.co.id',
                'smtp_port' => intval($settings['smtp_port'] ?? 465),
                'smtp_secure' => $settings['smtp_secure'] ?? 'ssl',
                'smtp_user' => $settings['smtp_user'],
                'smtp_pass' => $settings['smtp_pass'] ?? '',
                'from_email' => $settings['smtp_from_email'] ?? $settings['smtp_user'],
                'from_name' => $settings['smtp_from_name'] ?? 'PT Indo Ocean ERP',
                'debug' => false,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function send($to, $subject, $body, $isHtml = true)
    {
        if (empty($this->config['smtp_user']) || empty($this->config['smtp_pass'])) {
            $this->errors[] = "SMTP tidak dikonfigurasi";
            return false;
        }
        
        $result = $this->sendViaSMTP($to, $subject, $body, $isHtml);
        
        // If primary SMTP failed and it's not Gmail, retry with Gmail
        if (!$result && $this->config['smtp_host'] !== 'smtp.gmail.com') {
            error_log('[MAILER] Primary SMTP failed, retrying with Gmail...');
            $origConfig = $this->config;
            $this->config = self::OTP_CONFIG;
            $this->errors = []; // clear previous errors
            $result = $this->sendViaSMTP($to, $subject, $body, $isHtml);
            $this->config = $origConfig; // restore
        }
        
        return $result;
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
        $secure = $this->config['smtp_secure'] ?? 'tls';
        
        // Build SSL context
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_SSLv23_CLIENT
            ]
        ]);
        
        // For SSL (port 465): connect with ssl:// prefix directly
        // For TLS (port 587): connect with tcp:// then upgrade via STARTTLS
        $protocol = ($secure === 'ssl') ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}";
        
        $socket = @stream_socket_client(
            $protocol,
            $errno,
            $errstr,
            5,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if (!$socket) {
            $this->errors[] = "SMTP connect failed ({$protocol}): [{$errno}] {$errstr}";
            error_log("[MAILER] SMTP connect failed to {$protocol}: [{$errno}] {$errstr}");
            return false;
        }
        
        // Set stream timeout
        stream_set_timeout($socket, 10);
        
        // Read greeting
        $this->smtpRead($socket);
        
        // EHLO
        $this->smtpWrite($socket, "EHLO localhost\r\n");
        $this->smtpRead($socket);
        
        // STARTTLS only for TLS mode (port 587), skip for SSL (port 465)
        if ($secure !== 'ssl') {
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
        }
        
        // Try AUTH LOGIN first
        $this->smtpWrite($socket, "AUTH LOGIN\r\n");
        $response = $this->smtpRead($socket);
        
        $authSuccess = false;
        
        if (strpos($response, '334') !== false) {
            // Send username (base64)
            $this->smtpWrite($socket, base64_encode($user) . "\r\n");
            $this->smtpRead($socket);
            
            // Send password (base64)
            $this->smtpWrite($socket, base64_encode($pass) . "\r\n");
            $response = $this->smtpRead($socket);
            
            if (strpos($response, '235') !== false) {
                $authSuccess = true;
            } else {
                error_log("[MAILER] AUTH LOGIN failed, server said: " . trim($response));
            }
        }
        
        // Fallback to AUTH PLAIN if AUTH LOGIN failed
        if (!$authSuccess) {
            $authPlain = base64_encode("\0" . $user . "\0" . $pass);
            $this->smtpWrite($socket, "AUTH PLAIN {$authPlain}\r\n");
            $response = $this->smtpRead($socket);
            
            if (strpos($response, '235') !== false) {
                $authSuccess = true;
            } else {
                error_log("[MAILER] AUTH PLAIN also failed, server said: " . trim($response));
            }
        }
        
        if (!$authSuccess) {
            $this->errors[] = "Authentication failed - check username/password. Server: " . trim($response);
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
    
    /**
     * Send email with file attachment via SMTP
     */
    public function sendWithAttachment($to, $subject, $body, $attachmentPath, $attachmentName = 'attachment.html')
    {
        if (!file_exists($attachmentPath)) {
            $this->errors[] = "Attachment file not found: {$attachmentPath}";
            return $this->send($to, $subject, $body); // Fallback to send without attachment
        }
        
        $host = $this->config['smtp_host'];
        $port = $this->config['smtp_port'];
        $user = $this->config['smtp_user'];
        $pass = $this->config['smtp_pass'];
        $from = $this->config['from_email'];
        $fromName = $this->config['from_name'];
        $secure = $this->config['smtp_secure'] ?? 'tls';
        
        if (empty($user) || empty($pass)) {
            $this->errors[] = "SMTP credentials not configured";
            return false;
        }
        
        // Build SSL context
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_SSLv23_CLIENT
            ]
        ]);
        
        $protocol = ($secure === 'ssl') ? "ssl://{$host}:{$port}" : "tcp://{$host}:{$port}";
        
        $socket = @stream_socket_client($protocol, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
        
        if (!$socket) {
            $this->errors[] = "SMTP connect failed ({$protocol}): [{$errno}] {$errstr}";
            return false;
        }
        
        stream_set_timeout($socket, 10);
        $this->smtpRead($socket);
        $this->smtpWrite($socket, "EHLO localhost\r\n");
        $this->smtpRead($socket);
        
        if ($secure !== 'ssl') {
            $this->smtpWrite($socket, "STARTTLS\r\n");
            $response = $this->smtpRead($socket);
            if (strpos($response, '220') === false) {
                fclose($socket);
                return false;
            }
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($socket);
                return false;
            }
            $this->smtpWrite($socket, "EHLO localhost\r\n");
            $this->smtpRead($socket);
        }
        
        // AUTH LOGIN
        $this->smtpWrite($socket, "AUTH LOGIN\r\n");
        $response = $this->smtpRead($socket);
        $authSuccess = false;
        
        if (strpos($response, '334') !== false) {
            $this->smtpWrite($socket, base64_encode($user) . "\r\n");
            $this->smtpRead($socket);
            $this->smtpWrite($socket, base64_encode($pass) . "\r\n");
            $response = $this->smtpRead($socket);
            if (strpos($response, '235') !== false) {
                $authSuccess = true;
            }
        }
        
        if (!$authSuccess) {
            $this->errors[] = "Authentication failed";
            fclose($socket);
            return false;
        }
        
        $this->smtpWrite($socket, "MAIL FROM:<{$from}>\r\n");
        $this->smtpRead($socket);
        $this->smtpWrite($socket, "RCPT TO:<{$to}>\r\n");
        $this->smtpRead($socket);
        $this->smtpWrite($socket, "DATA\r\n");
        $this->smtpRead($socket);
        
        // Build MIME multipart message with attachment
        $boundary = 'BOUNDARY_' . uniqid(time());
        $fileContent = file_get_contents($attachmentPath);
        $fileBase64 = chunk_split(base64_encode($fileContent));
        
        // Detect MIME type from file extension
        $ext = strtolower(pathinfo($attachmentName, PATHINFO_EXTENSION));
        $mimeTypes = ['pdf' => 'application/pdf', 'html' => 'text/html', 'csv' => 'text/csv', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        $attachmentMime = $mimeTypes[$ext] ?? 'application/octet-stream';
        
        $message = "From: {$fromName} <{$from}>\r\n";
        $message .= "To: {$to}\r\n";
        $message .= "Subject: {$subject}\r\n";
        $message .= "MIME-Version: 1.0\r\n";
        $message .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
        $message .= "\r\n";
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n";
        $message .= "\r\n";
        $message .= $body . "\r\n";
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: {$attachmentMime}; name=\"{$attachmentName}\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"{$attachmentName}\"\r\n";
        $message .= "\r\n";
        $message .= $fileBase64;
        $message .= "--{$boundary}--\r\n";
        $message .= ".\r\n";
        
        $this->smtpWrite($socket, $message);
        $response = $this->smtpRead($socket);
        
        $this->smtpWrite($socket, "QUIT\r\n");
        fclose($socket);
        
        if (strpos($response, '250') !== false) {
            return true;
        }
        
        $this->errors[] = "Failed to send email with attachment: {$response}";
        return false;
    }
    
    /**
     * Send email with attachment, with Gmail auto-retry on failure
     */
    public function sendWithAttachmentRetry($to, $subject, $body, $attachmentPath, $attachmentName = 'attachment.pdf')
    {
        $result = $this->sendWithAttachment($to, $subject, $body, $attachmentPath, $attachmentName);
        
        // If primary SMTP failed and it's not Gmail, retry with Gmail
        if (!$result && $this->config['smtp_host'] !== 'smtp.gmail.com') {
            error_log('[MAILER] Primary SMTP with attachment failed, retrying with Gmail...');
            $origConfig = $this->config;
            $this->config = self::OTP_CONFIG;
            $this->errors = [];
            $result = $this->sendWithAttachment($to, $subject, $body, $attachmentPath, $attachmentName);
            $this->config = $origConfig;
        }
        
        return $result;
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
     * Send payslip email
     */
    public function sendPayslip($email, $crewName, $payrollPeriod, $payrollItem, $attachmentPath = null)
    {
        $subject = "Slip Gaji / Payslip: {$payrollPeriod['period_name']} - PT Indo Ocean";
        
        $body = $this->getTemplate('payslip', [
            'name' => $crewName,
            'period' => $payrollPeriod,
            'item' => $payrollItem
        ]);
        
        if ($attachmentPath && file_exists($attachmentPath)) {
            $monthName = $payrollPeriod['period_name'] ?? 'Payslip';
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $crewName);
            $ext = strtolower(pathinfo($attachmentPath, PATHINFO_EXTENSION)) ?: 'pdf';
            $attachmentName = "Payslip_{$safeName}_{$monthName}.{$ext}";
            return $this->sendWithAttachmentRetry($email, $subject, $body, $attachmentPath, $attachmentName);
        }
        
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
                
                
            case 'payslip':
                $periodName = $data['period']['period_name'] ?? '';
                
                return "
                    <html><head>{$baseStyle}</head><body>
                    <div class='container'>
                        <div class='header'>
                            <h1>PT Indo Oceancrew Services</h1>
                            <p>Slip Gaji / Payslip</p>
                        </div>
                        <div class='content'>
                            <p>Yth. <strong>{$data['name']}</strong>,</p>
                            <p>Terlampir adalah slip gaji Anda untuk periode <strong>{$periodName}</strong>.</p>
                            <p>Silakan buka file PDF terlampir untuk melihat rincian slip gaji Anda.</p>
                            <p style='margin-top:20px;'>Terima kasih atas kerja keras dan dedikasi Anda.</p>
                            <p style='color:#666; font-size:12px; margin-top:20px;'>Email ini dikirim secara otomatis oleh sistem ERP. Jika ada pertanyaan, silakan hubungi HRD.</p>
                        </div>
                        <div class='footer'>
                            PT. Indo Oceancrew Services<br>
                            Menara Cakrawala lt 15 no 1506, Jl. M.H. Thamrin No.9
                        </div>
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
