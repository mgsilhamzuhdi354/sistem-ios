<?php
/**
 * PT Indo Ocean - ERP System
 * Auth Controller - Login, Logout, Password Reset, 2FA OTP
 */

namespace App\Controllers;

require_once APPPATH . 'Models/UserModel.php';
require_once APPPATH . 'Models/OtpModel.php';
require_once APPPATH . 'Libraries/Mailer.php';

use App\Models\UserModel;
use App\Models\LoginHistoryModel;
use App\Models\ActivityLogModel;
use App\Models\OtpModel;
use App\Libraries\Mailer;

class Auth extends BaseController
{
    private $userModel;
    private $loginHistoryModel;
    private $activityModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel($this->db);
        $this->loginHistoryModel = new LoginHistoryModel($this->db);
        $this->activityModel = new ActivityLogModel($this->db);
    }
    
    /**
     * Show login form
     */
    public function login()
    {
        // Already logged in? Redirect to dashboard
        if ($this->isLoggedIn()) {
            $this->redirect('');
            return;
        }
        
        $data = [
            'title' => 'Login',
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        return $this->view('auth/login', $data);
    }
    
    /**
     * Process login
     */
    public function authenticate()
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
            return;
        }
        
        // Validate CSRF
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $this->setFlash('error', 'Invalid security token. Please try again.');
            $this->redirect('auth/login');
            return;
        }
        
        $login = trim($this->input('login'));
        $password = $this->input('password');
        $remember = $this->input('remember') === 'on';
        
        // Validate input
        if (empty($login) || empty($password)) {
            $this->setFlash('error', 'Username dan password wajib diisi');
            $this->redirect('auth/login');
            return;
        }
        
        // Find user
        $user = $this->userModel->findByLogin($login);
        
        if (!$user) {
            $this->setFlash('error', 'Username atau password salah');
            $this->redirect('auth/login');
            return;
        }
        
        // Check if locked
        if ($this->userModel->isLocked($user['id'])) {
            $lockedUntil = date('H:i', strtotime($user['locked_until']));
            $this->loginHistoryModel->logAttempt($user['id'], 'failed', 'Account locked');
            $this->setFlash('error', "Akun terkunci. Coba lagi setelah {$lockedUntil}");
            $this->redirect('auth/login');
            return;
        }
        
        // Verify password
        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            $attempts = $this->userModel->incrementLoginAttempts($user['id']);
            $this->loginHistoryModel->logAttempt($user['id'], 'failed', 'Wrong password');
            
            $remaining = 5 - $attempts;
            if ($remaining > 0) {
                $this->setFlash('error', "Password salah. {$remaining} percobaan tersisa.");
            } else {
                $this->setFlash('error', 'Akun terkunci selama 15 menit karena terlalu banyak percobaan gagal.');
            }
            $this->redirect('auth/login');
            return;
        }
        
        // Check if active
        if (!$user['is_active']) {
            $this->loginHistoryModel->logAttempt($user['id'], 'failed', 'Account inactive');
            $this->setFlash('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            $this->redirect('auth/login');
            return;
        }
        
        // Password correct! Now send OTP for 2FA
        $this->userModel->resetLoginAttempts($user['id']);
        
        // Generate OTP
        $otpModel = new OtpModel($this->db);
        $otpCode = $otpModel->generate($user['id'], 'login', 5); // 5 minutes expiry
        
        // Send OTP via email
        $mailer = new Mailer();
        $emailSent = $mailer->sendOtpCode($user['email'], $otpCode, $user['full_name']);
        
        if (!$emailSent) {
            // Check if local environment
            $isLocal = ($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'production') === 'local' || 
                       ($_SERVER['HTTP_HOST'] === 'localhost') ||
                       (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
            
            if ($isLocal) {
                // LOCAL DEV: Tetap pakai OTP, tapi tampilkan kode di layar
                // (karena SMTP tidak berfungsi di localhost)
                $_SESSION['pending_login'] = [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'expires' => time() + 300
                ];
                
                $this->setFlash('warning', "⚠️ SMTP tidak aktif (Mode Local). Kode OTP Anda: <strong style='font-size:1.5em; letter-spacing:3px;'>{$otpCode}</strong>");
                $this->redirect('auth/verify-otp');
                return;
            }
            
            // PRODUCTION: Tolak login kalau OTP gagal dikirim
            $this->loginHistoryModel->logAttempt($user['id'], 'failed', 'OTP Send Failed - SMTP Error');
            $this->setFlash('error', 'Gagal mengirim kode OTP. Sistem email sedang gangguan. Hubungi IT Support.');
            $this->redirect('auth/login');
            return;
        }
        
        // Store pending user in session for OTP verification
        $_SESSION['pending_login'] = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'expires' => time() + 300 // 5 minutes
        ];
        
        $this->redirect('auth/verify-otp');
    }
    
    /**
     * Show OTP verification form (GET) or verify OTP code (POST)
     */
    public function verifyOtp()
    {
        // Handle GET request - show form
        if (!$this->isPost()) {
            // Check if pending login exists
            if (empty($_SESSION['pending_login']) || $_SESSION['pending_login']['expires'] < time()) {
                $this->setFlash('error', 'Sesi verifikasi telah berakhir. Silakan login kembali.');
                $this->redirect('auth/login');
                return;
            }
            
            $data = [
                'title' => 'Verifikasi OTP',
                'flash' => $this->getFlash(),
                'csrf_token' => $this->generateCsrfToken(),
                'email' => $this->maskEmail($_SESSION['pending_login']['email'])
            ];
            
            return $this->view('auth/verify_otp', $data);
        }
        
        // Handle POST request - verify OTP
        
        // Validate CSRF
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $this->setFlash('error', 'Token keamanan tidak valid.');
            $this->redirect('auth/verify-otp');
            return;
        }
        
        // Check pending login
        if (empty($_SESSION['pending_login']) || $_SESSION['pending_login']['expires'] < time()) {
            $this->setFlash('error', 'Sesi verifikasi telah berakhir. Silakan login kembali.');
            $this->redirect('auth/login');
            return;
        }
        
        $otpCode = trim($this->input('otp_code'));
        $userId = $_SESSION['pending_login']['user_id'];
        
        // Verify OTP
        $otpModel = new OtpModel($this->db);
        $result = $otpModel->verify($userId, $otpCode, 'login');
        
        if (!$result['success']) {
            $this->setFlash('error', $result['message']);
            $this->redirect('auth/verify-otp');
            return;
        }
        
        // OTP verified! Complete login
        $user = $this->userModel->find($userId);
        
        $this->loginHistoryModel->logAttempt($user['id'], 'success');
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Clear pending login
        unset($_SESSION['pending_login']);
        
        // Set session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
            'avatar' => $user['avatar']
        ];
        
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['ui_mode'] = 'modern';
        
        // Log activity
        $this->activityModel->log($user['id'], 'login', 'user', $user['id'], 'User logged in with 2FA');
        
        // Send login notification email
        $mailer = new Mailer();
        $mailer->sendLoginNotification($user['email'], $user['full_name'], [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'browser' => $this->getBrowserInfo(),
            'time' => date('d M Y, H:i') . ' WIB'
        ]);
        
        $this->setFlash('success', 'Selamat datang, ' . $user['full_name'] . '!');
        $this->redirect('');
    }
    
    /**
     * Resend OTP code
     */
    public function resendOtp()
    {
        if (empty($_SESSION['pending_login'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $userId = $_SESSION['pending_login']['user_id'];
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            $this->setFlash('error', 'User tidak ditemukan.');
            $this->redirect('auth/login');
            return;
        }
        
        // Generate new OTP
        $otpModel = new OtpModel($this->db);
        $otpCode = $otpModel->generate($userId, 'login', 5);
        
        // Send OTP via email
        $mailer = new Mailer();
        $emailSent = $mailer->sendOtpCode($user['email'], $otpCode, $user['full_name']);
        
        if ($emailSent) {
            // Extend session
            $_SESSION['pending_login']['expires'] = time() + 300;
            $this->setFlash('success', 'Kode OTP baru telah dikirim ke email Anda.');
        } else {
            $this->setFlash('error', 'Gagal mengirim kode OTP.');
        }
        
        $this->redirect('auth/verify-otp');
    }
    
    /**
     * Mask email for display
     */
    private function maskEmail($email)
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        
        $masked = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 4, 2)) . substr($name, -2);
        return $masked . '@' . $domain;
    }
    
    /**
     * Get browser info
     */
    private function getBrowserInfo()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        if (strpos($userAgent, 'Opera') !== false) return 'Opera';
        
        return 'Unknown Browser';
    }
    
    /**
     * DEV BYPASS - Auto login without password/OTP (LOCAL DEVELOPMENT ONLY)
     * Access via: /auth/dev-bypass
     * SECURITY: Requires BOTH APP_ENV=local AND localhost hostname
     */
    public function devBypass()
    {
        // DOUBLE CHECK: Require APP_ENV=local
        $appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'production';
        if ($appEnv !== 'local' && $appEnv !== 'development') {
            http_response_code(404);
            echo 'Page not found';
            exit;
        }
        
        // STRICT: Only allow on localhost or .test domains (Laragon)
        $host = $_SERVER['HTTP_HOST'] ?? '';
        // Remove port if present
        $hostOnly = explode(':', $host)[0];
        $isLocal = in_array($hostOnly, ['localhost', '127.0.0.1', '::1']) ||
                   str_ends_with($hostOnly, '.test');
        
        if (!$isLocal) {
            http_response_code(404);
            echo 'Page not found';
            exit;
        }
        
        // Find first active user - try known usernames
        $user = null;
        $tryUsers = ['admin', 'mgs.ilham.zuhdi', 'administrator'];
        foreach ($tryUsers as $tryLogin) {
            $user = $this->userModel->findByLogin($tryLogin);
            if ($user) break;
        }
        
        // Fallback: get any user from model
        if (!$user) {
            $allUsers = $this->userModel->getAll();
            $user = $allUsers[0] ?? null;
        }
        
        if (!$user) {
            $this->setFlash('error', 'No active users found in database.');
            $this->redirect('auth/login');
            return;
        }
        
        // Unlock account if locked
        $this->userModel->resetLoginAttempts($user['id']);
        
        // Regenerate session
        session_regenerate_id(true);
        
        // Set session directly
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
            'avatar' => $user['avatar'] ?? null
        ];
        
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['ui_mode'] = 'modern';
        
        // Log
        $this->loginHistoryModel->logAttempt($user['id'], 'success', 'Dev Bypass Login');
        $this->activityModel->log($user['id'], 'login', 'user', $user['id'], 'Dev Bypass Login (localhost)');
        
        $this->setFlash('warning', '⚠️ Dev Bypass Login as: ' . $user['full_name'] . ' (' . $user['role'] . ')');
        $this->redirect('');
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $userId = $_SESSION['user']['id'];
            $this->activityModel->log($userId, 'logout', 'user', $userId, 'User logged out');
        }
        
        // Destroy session
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        $this->setFlash('success', 'Anda telah logout.');
        $this->redirect('auth/login');
    }
    
    /**
     * Forgot password form
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Forgot Password',
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        return $this->view('auth/forgot_password', $data);
    }
    
    /**
     * Send password reset email
     */
    public function sendResetLink()
    {
        if (!$this->isPost()) {
            $this->redirect('auth/forgot-password');
            return;
        }
        
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('auth/forgot-password');
            return;
        }
        
        $email = trim($this->input('email'));
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Masukkan email yang valid.');
            $this->redirect('auth/forgot-password');
            return;
        }
        
        $user = $this->userModel->findByEmail($email);
        
        // Don't reveal if email exists or not (security)
        if ($user) {
            $token = $this->userModel->generateResetToken($user['id']);
            $resetLink = BASE_URL . 'auth/reset-password?token=' . $token;
            
            $this->activityModel->log($user['id'], 'password_reset_request', 'user', $user['id'], 'Password reset requested');
            
            // Try to send email
            $mailer = new Mailer();
            $emailSent = $mailer->sendPasswordReset($email, $token, $user['full_name']);
            
            // Development mode: If email cannot be sent, show direct link
            // Only in local/development mode for security
            $appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'production';
            if (!$emailSent && in_array($appEnv, ['local', 'development'])) {
                $this->setFlash('info', "
                    <strong>Email tidak bisa dikirim (SMTP belum dikonfigurasi)</strong><br>
                    <small>Untuk development, gunakan link berikut:</small><br>
                    <a href='{$resetLink}' class='btn btn-primary' style='margin-top:10px; display:inline-block;'>Reset Password</a>
                ");
                $this->redirect('auth/forgot-password');
                return;
            }
        }
        
        // Always show success (don't reveal if email exists)
        $this->setFlash('success', 'Jika email terdaftar, link reset password telah dikirim ke email Anda.');
        $this->redirect('auth/login');
    }
    
    /**
     * Reset password form
     */
    public function resetPassword()
    {
        $token = $this->input('token');
        
        if (empty($token)) {
            $this->setFlash('error', 'Invalid reset link.');
            $this->redirect('auth/login');
            return;
        }
        
        $user = $this->userModel->verifyResetToken($token);
        
        if (!$user) {
            $this->setFlash('error', 'Link reset tidak valid atau sudah kadaluarsa.');
            $this->redirect('auth/login');
            return;
        }
        
        $data = [
            'title' => 'Reset Password',
            'token' => $token,
            'flash' => $this->getFlash(),
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        return $this->view('auth/reset_password', $data);
    }
    
    /**
     * Process password reset
     */
    public function updatePassword()
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
            return;
        }
        
        if (!$this->validateCsrfToken($this->input('csrf_token'))) {
            $this->setFlash('error', 'Invalid security token.');
            $this->redirect('auth/login');
            return;
        }
        
        $token = $this->input('token');
        $password = $this->input('password');
        $confirmPassword = $this->input('confirm_password');
        
        // Validate
        if (strlen($password) < 8) {
            $this->setFlash('error', 'Password minimal 8 karakter.');
            $this->redirect('auth/reset-password?token=' . $token);
            return;
        }
        
        if ($password !== $confirmPassword) {
            $this->setFlash('error', 'Konfirmasi password tidak cocok.');
            $this->redirect('auth/reset-password?token=' . $token);
            return;
        }
        
        $user = $this->userModel->verifyResetToken($token);
        
        if (!$user) {
            $this->setFlash('error', 'Link reset tidak valid atau sudah kadaluarsa.');
            $this->redirect('auth/login');
            return;
        }
        
        // Update password
        $hashedPassword = $this->userModel->hashPassword($password);
        $this->userModel->update($user['id'], ['password' => $hashedPassword]);
        $this->userModel->clearResetToken($user['id']);
        
        $this->activityModel->log($user['id'], 'password_reset', 'user', $user['id'], 'Password reset successful');
        
        $this->setFlash('success', 'Password berhasil diubah. Silakan login.');
        $this->redirect('auth/login');
    }
}
