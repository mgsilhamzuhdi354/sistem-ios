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
        
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
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
            
            // In production, send email here
            // For now, just show the reset link
            $resetLink = url('/reset-password/' . $token);
            flash('success', 'Password reset link has been sent to your email. (Dev mode: ' . $resetLink . ')');
        } else {
            // Don't reveal if email exists
            flash('success', 'If that email exists in our system, you will receive a password reset link.');
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
