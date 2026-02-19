<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Forgot Password Controller
 * Flow: Enter email → receive OTP code → verify code → reset password
 */
class ForgotPassword extends BaseController {
    
    public function __construct() {
        parent::__construct();
        $this->ensureTableExists();
    }
    
    private function ensureTableExists() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                otp_code VARCHAR(10) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_token (token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Add otp_code column if missing (for existing tables)
        $result = $this->db->query("SHOW COLUMNS FROM password_resets LIKE 'otp_code'");
        if ($result && $result->num_rows === 0) {
            $this->db->query("ALTER TABLE password_resets ADD COLUMN otp_code VARCHAR(10) DEFAULT NULL AFTER token");
        }
    }
    
    /**
     * Show forgot password form
     */
    public function index() {
        $this->view('auth/forgot-password', [
            'pageTitle' => 'Forgot Password'
        ]);
    }
    
    /**
     * Step 1: Generate OTP and send to email
     */
    public function send() {
        validate_csrf();
        
        $email = trim($this->input('email'));
        
        if (empty($email)) {
            flash('error', 'Email wajib diisi');
            return $this->redirect(url('/forgot-password'));
        }
        
        // Check if user exists
        $stmt = $this->db->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            // Don't reveal if email exists — redirect to verify anyway
            $_SESSION['reset_email'] = $email;
            flash('info', 'Jika email terdaftar, kode verifikasi telah dikirim.');
            return $this->redirect(url('/verify-reset-code'));
        }
        
        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = bin2hex(random_bytes(32));
        
        // Delete old tokens for this email
        $stmtDel = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmtDel->bind_param('s', $email);
        $stmtDel->execute();
        
        // Insert new token + OTP
        $stmtIns = $this->db->prepare("INSERT INTO password_resets (email, token, otp_code, created_at) VALUES (?, ?, ?, NOW())");
        $stmtIns->bind_param('sss', $email, $token, $otpCode);
        
        if (!$stmtIns->execute()) {
            error_log("[ForgotPassword] DB error: " . $this->db->error);
            flash('error', 'Terjadi kesalahan. Silakan coba lagi.');
            return $this->redirect(url('/forgot-password'));
        }
        
        // Try to send OTP via email
        $userName = $user['full_name'] ?? 'User';
        $emailSent = false;
        
        try {
            require_once APPPATH . 'Libraries/Mailer.php';
            $mailer = new \Mailer($this->db);
            
            $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto;'>
                    <h2 style='color: #333; text-align: center;'>Reset Password</h2>
                    <p>Halo <strong>{$userName}</strong>,</p>
                    <p>Gunakan kode verifikasi berikut untuk mereset password Anda:</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <div style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-size: 32px; letter-spacing: 8px; padding: 20px 40px; border-radius: 12px; display: inline-block; font-weight: bold;'>{$otpCode}</div>
                    </div>
                    <p style='text-align: center; color: #888;'>Kode berlaku selama <strong>15 menit</strong></p>
                    <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999; text-align: center;'>PT Indo Ocean Crew Services</p>
                </div>
            ";
            
            $result = $mailer->send($email, $userName, 'Kode Verifikasi Reset Password - PT Indo Ocean', $emailBody);
            $emailSent = $result['success'] ?? false;
        } catch (\Exception $e) {
            error_log("[ForgotPassword] Mailer error: " . $e->getMessage());
        }
        
        // Store email in session for the verify page
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_email_sent'] = $emailSent;
        
        // If email not sent, store OTP in session as fallback (for display)
        if (!$emailSent) {
            $_SESSION['reset_otp_fallback'] = $otpCode;
        }
        
        flash('success', 'Kode verifikasi telah dikirim ke <strong>' . htmlspecialchars($email) . '</strong>');
        return $this->redirect(url('/verify-reset-code'));
    }
    
    /**
     * Step 2: Show OTP verification form
     */
    public function verifyCodeForm() {
        $email = $_SESSION['reset_email'] ?? null;
        
        if (!$email) {
            flash('error', 'Silakan masukkan email terlebih dahulu.');
            return $this->redirect(url('/forgot-password'));
        }
        
        $this->view('auth/verify-code', [
            'email' => $email,
            'emailSent' => $_SESSION['reset_email_sent'] ?? false,
            'otpFallback' => $_SESSION['reset_otp_fallback'] ?? null,
            'pageTitle' => 'Verifikasi Kode'
        ]);
    }
    
    /**
     * Step 2b: Verify OTP code
     */
    public function verifyCode() {
        validate_csrf();
        
        $email = $_SESSION['reset_email'] ?? null;
        $inputCode = trim($this->input('otp_code'));
        
        if (!$email) {
            flash('error', 'Session habis. Silakan mulai ulang.');
            return $this->redirect(url('/forgot-password'));
        }
        
        if (empty($inputCode)) {
            flash('error', 'Kode verifikasi wajib diisi.');
            return $this->redirect(url('/verify-reset-code'));
        }
        
        // Verify OTP from database (valid for 15 minutes)
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE email = ? AND otp_code = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");
        $stmt->bind_param('ss', $email, $inputCode);
        $stmt->execute();
        $reset = $stmt->get_result()->fetch_assoc();
        
        if (!$reset) {
            flash('error', 'Kode verifikasi salah atau sudah kedaluwarsa.');
            return $this->redirect(url('/verify-reset-code'));
        }
        
        // Code verified! Clear fallback OTP from session
        unset($_SESSION['reset_otp_fallback']);
        unset($_SESSION['reset_email_sent']);
        
        // Redirect to reset password with token
        return $this->redirect(url('/reset-password/' . $reset['token']));
    }
    
    /**
     * Step 2c: Resend OTP code
     */
    public function resendCode() {
        $email = $_SESSION['reset_email'] ?? null;
        
        if (!$email) {
            return $this->redirect(url('/forgot-password'));
        }
        
        // Check if user exists
        $stmt = $this->db->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            flash('error', 'Email tidak ditemukan.');
            return $this->redirect(url('/forgot-password'));
        }
        
        // Generate new OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update existing record
        $stmtUpd = $this->db->prepare("UPDATE password_resets SET otp_code = ?, created_at = NOW() WHERE email = ?");
        $stmtUpd->bind_param('ss', $otpCode, $email);
        $stmtUpd->execute();
        
        if ($stmtUpd->affected_rows === 0) {
            // No existing record, insert new
            $token = bin2hex(random_bytes(32));
            $stmtIns = $this->db->prepare("INSERT INTO password_resets (email, token, otp_code, created_at) VALUES (?, ?, ?, NOW())");
            $stmtIns->bind_param('sss', $email, $token, $otpCode);
            $stmtIns->execute();
        }
        
        // Try send email
        $emailSent = false;
        $userName = $user['full_name'] ?? 'User';
        
        try {
            require_once APPPATH . 'Libraries/Mailer.php';
            $mailer = new \Mailer($this->db);
            $emailBody = "
                <div style='text-align: center; font-family: Arial, sans-serif;'>
                    <h2>Kode Verifikasi Baru</h2>
                    <p>Halo <strong>{$userName}</strong>, berikut kode verifikasi baru Anda:</p>
                    <div style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; font-size: 32px; letter-spacing: 8px; padding: 20px 40px; border-radius: 12px; display: inline-block; font-weight: bold; margin: 20px 0;'>{$otpCode}</div>
                    <p style='color: #888;'>Berlaku 15 menit</p>
                </div>
            ";
            $result = $mailer->send($email, $userName, 'Kode Verifikasi Baru - PT Indo Ocean', $emailBody);
            $emailSent = $result['success'] ?? false;
        } catch (\Exception $e) {
            error_log("[ForgotPassword] Resend error: " . $e->getMessage());
        }
        
        $_SESSION['reset_email_sent'] = $emailSent;
        if (!$emailSent) {
            $_SESSION['reset_otp_fallback'] = $otpCode;
        }
        
        flash('success', 'Kode verifikasi baru telah dikirim.');
        return $this->redirect(url('/verify-reset-code'));
    }
    
    /**
     * Step 3: Show reset password form
     */
    public function reset($token) {
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $reset = $stmt->get_result()->fetch_assoc();
        
        if (!$reset) {
            flash('error', 'Link reset tidak valid atau sudah kedaluwarsa.');
            return $this->redirect(url('/forgot-password'));
        }
        
        $this->view('auth/reset-password', [
            'token' => $token,
            'email' => $reset['email'],
            'pageTitle' => 'Reset Password'
        ]);
    }
    
    /**
     * Step 4: Update password
     */
    public function update() {
        validate_csrf();
        
        $token = $this->input('token');
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $reset = $stmt->get_result()->fetch_assoc();
        
        if (!$reset) {
            flash('error', 'Link reset tidak valid atau sudah kedaluwarsa.');
            return $this->redirect(url('/forgot-password'));
        }
        
        $errors = [];
        
        if (empty($password) || strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter';
        }
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Password tidak cocok';
        }
        
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param('ss', $hashedPassword, $reset['email']);
            $stmt->execute();
            
            // Delete used token
            $stmtDel = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmtDel->bind_param('s', $reset['email']);
            $stmtDel->execute();
            
            // Clear session
            unset($_SESSION['reset_email'], $_SESSION['reset_email_sent'], $_SESSION['reset_otp_fallback']);
            
            flash('success', 'Password berhasil direset! Silakan login dengan password baru.');
            return $this->redirect(url('/login'));
        }
        
        $_SESSION['errors'] = $errors;
        return $this->redirect(url('/reset-password/' . $token));
    }
}
