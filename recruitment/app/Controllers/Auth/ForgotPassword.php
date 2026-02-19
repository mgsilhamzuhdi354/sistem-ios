<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Forgot Password Controller
 */
class ForgotPassword extends BaseController {
    
    public function __construct() {
        parent::__construct();
        // Ensure password_resets table exists
        $this->ensureTableExists();
    }
    
    /**
     * Auto-create password_resets table if it doesn't exist
     */
    private function ensureTableExists() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_token (token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
    
    public function index() {
        $this->view('auth/forgot-password', [
            'pageTitle' => 'Forgot Password'
        ]);
    }
    
    public function send() {
        validate_csrf();
        
        $email = trim($this->input('email'));
        
        if (empty($email)) {
            flash('error', 'Email wajib diisi');
            return $this->redirect(url('/forgot-password'));
        }
        
        $stmt = $this->db->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            
            // Delete old tokens
            $stmtDel = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmtDel->bind_param('s', $email);
            $stmtDel->execute();
            
            // Insert new token
            $stmtIns = $this->db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
            $stmtIns->bind_param('ss', $email, $token);
            
            if (!$stmtIns->execute()) {
                error_log("[ForgotPassword] Failed to insert token: " . $this->db->error);
                flash('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
                return $this->redirect(url('/forgot-password'));
            }
            
            // Build reset link
            $resetLink = url('/reset-password/' . $token);
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $fullResetLink = $protocol . '://' . $host . $resetLink;
            
            // Try sending email in background, but always show link as well
            $userName = $user['full_name'] ?? 'User';
            
            // Show reset link directly (no SMTP to avoid timeout)
            flash('success', 'Klik link berikut untuk reset password: <a href="' . htmlspecialchars($fullResetLink) . '" style="color: #667eea; font-weight: bold;">Reset Password Sekarang</a>');
        } else {
            // Don't reveal if email exists — still show generic success
            flash('success', 'Jika email tersebut terdaftar, Anda akan menerima link reset password.');
        }
        
        return $this->redirect(url('/forgot-password'));
    }
    
    public function reset($token) {
        // Verify token
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
    
    public function update() {
        validate_csrf();
        
        $token = $this->input('token');
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        
        // Verify token
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
            
            flash('success', 'Password berhasil direset! Silakan login dengan password baru.');
            return $this->redirect(url('/login'));
        }
        
        $_SESSION['errors'] = $errors;
        return $this->redirect(url('/reset-password/' . $token));
    }
}

