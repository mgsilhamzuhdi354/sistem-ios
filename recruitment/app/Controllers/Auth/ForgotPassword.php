<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Forgot Password Controller
 */
class ForgotPassword extends BaseController {
    
    public function index() {
        $this->view('auth/forgot-password', [
            'pageTitle' => 'Forgot Password'
        ]);
    }
    
    public function send() {
        validate_csrf();
        
        $email = trim($this->input('email'));
        
        if (empty($email)) {
            flash('error', 'Email is required');
            $this->redirect(url('/forgot-password'));
        }
        
        $stmt = $this->db->prepare("SELECT id, full_name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            
            // Delete old tokens
            $this->db->query("DELETE FROM password_resets WHERE email = '" . $this->db->real_escape_string($email) . "'");
            
            // Insert new token
            $stmt = $this->db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param('ss', $email, $token);
            $stmt->execute();
            
            // Build reset link
            $resetLink = url('/reset-password/' . $token);
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $fullResetLink = $protocol . '://' . $host . $resetLink;
            
            // Build email body
            $userName = $user['full_name'] ?? 'User';
            $emailBody = "
                <h2>Reset Password</h2>
                <p>Halo <strong>{$userName}</strong>,</p>
                <p>Kami menerima permintaan untuk mereset password akun Anda di PT Indo Ocean Crew Services.</p>
                <p>Klik tombol di bawah ini untuk mereset password Anda:</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='{$fullResetLink}' style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;'>Reset Password</a>
                </p>
                <p>Atau copy link ini ke browser Anda:</p>
                <p style='word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 5px; font-size: 13px;'>{$fullResetLink}</p>
                <p><strong>Link ini berlaku selama 1 jam.</strong></p>
                <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 12px; color: #999;'>Email ini dikirim otomatis oleh sistem PT Indo Ocean Crew Services.</p>
            ";
            
            // Try sending via Mailer
            $emailSent = false;
            try {
                require_once APPPATH . 'Libraries/Mailer.php';
                $mailer = new \Mailer($this->db);
                $result = $mailer->send($email, $userName, 'Reset Password - PT Indo Ocean', $emailBody);
                $emailSent = $result['success'] ?? false;
            } catch (\Exception $e) {
                error_log("[ForgotPassword] Mailer error: " . $e->getMessage());
                $emailSent = false;
            }
            
            if ($emailSent) {
                flash('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
            } else {
                // Fallback: show link directly (for environments without SMTP)
                flash('success', 'Link reset password: <a href="' . $fullResetLink . '" target="_blank">Klik di sini untuk reset</a>');
            }
        } else {
            // Don't reveal if email exists
            flash('success', 'Jika email tersebut terdaftar di sistem kami, Anda akan menerima link reset password.');
        }
        
        $this->redirect(url('/forgot-password'));
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
            flash('error', 'Invalid or expired reset link');
            $this->redirect(url('/forgot-password'));
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
            flash('error', 'Invalid or expired reset link');
            $this->redirect(url('/forgot-password'));
        }
        
        $errors = [];
        
        if (empty($password) || strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param('ss', $hashedPassword, $reset['email']);
            $stmt->execute();
            
            // Delete used token
            $this->db->query("DELETE FROM password_resets WHERE email = '" . $this->db->real_escape_string($reset['email']) . "'");
            
            flash('success', 'Password has been reset. You can now login.');
            $this->redirect(url('/login'));
        }
        
        $_SESSION['errors'] = $errors;
        $this->redirect(url('/reset-password/' . $token));
    }
}
