<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Login Controller
 */
class Login extends BaseController {
    
    public function index() {
        if (isLoggedIn()) {
            if (isMasterAdmin()) {
                $this->redirect(url('/master-admin/dashboard'));
            } elseif (isAdmin()) {
                $this->redirect(url('/admin/dashboard'));
            } elseif (isLeader()) {
                $this->redirect(url('/leader/dashboard'));
            } elseif (isCrewing()) {
                $this->redirect(url('/crewing/dashboard'));
            } else {
                $this->redirect(url('/applicant/dashboard'));
            }
        }
        
        $this->view('auth/login', [
            'pageTitle' => 'Login'
        ]);
    }
    
    public function authenticate() {
        validate_csrf();
        
        $email = trim($this->input('email'));
        $password = $this->input('password');
        $remember = $this->input('remember');
        
        $errors = [];
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        }
        
        if (empty($errors)) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Update last login and online status
                $this->db->query("UPDATE users SET last_login = NOW(), is_online = 1, last_activity = NOW() WHERE id = " . $user['id']);
                
                // Clear permission cache to load fresh permissions
                clearPermissionCache();
                
                flash('success', 'Welcome back, ' . $user['full_name'] . '!');
                
                // Redirect based on role (Matches Database Structure)
                switch ($user['role_id']) {
                    case ROLE_MASTER_ADMIN: // 11 - Master Admin
                        $this->redirect(url('/master-admin/dashboard'));
                        break;
                    case ROLE_ADMIN: // 1 - Admin
                        $this->redirect(url('/admin/dashboard'));
                        break;
                    case ROLE_LEADER: // 4 - Leader
                        $this->redirect(url('/leader/dashboard'));
                        break;
                    case ROLE_CREWING: // 5 - Crewing
                        $this->redirect(url('/crewing/dashboard'));
                        break;
                    case ROLE_APPLICANT: // 3 - Applicant
                    default:
                        $this->redirect(url('/applicant/dashboard'));
                        break;
                }
            } else {
                $errors['login'] = 'Invalid email or password';
            }
        }
        
        $_SESSION['old'] = $_POST;
        $_SESSION['errors'] = $errors;
        $this->redirect(url('/login'));
    }
    
    public function logout() {
        // Set user offline before destroying session
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $this->db->query("UPDATE users SET is_online = 0, last_activity = NOW() WHERE id = $userId");
        }
        
        session_destroy();
        session_start();
        flash('success', 'You have been logged out');
        $this->redirect(url('/login'));
    }
}
