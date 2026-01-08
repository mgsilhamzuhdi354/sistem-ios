<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Register Controller
 */
class Register extends BaseController {
    
    public function index() {
        if (isLoggedIn()) {
            $this->redirect(url('/applicant/dashboard'));
        }
        
        $this->view('auth/register', [
            'pageTitle' => 'Create Account'
        ]);
    }
    
    public function store() {
        validate_csrf();
        
        $fullName = trim($this->input('full_name'));
        $email = trim($this->input('email'));
        $phone = trim($this->input('phone'));
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        
        $errors = [];
        
        // Validation
        if (empty($fullName)) {
            $errors['full_name'] = 'Full name is required';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        } else {
            // Check if email exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $errors['email'] = 'Email already registered';
            }
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match';
        }
        
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO users (role_id, email, password, full_name, phone, is_active, created_at)
                VALUES (3, ?, ?, ?, ?, 1, NOW())
            ");
            $stmt->bind_param('ssss', $email, $hashedPassword, $fullName, $phone);
            
            if ($stmt->execute()) {
                $userId = $this->db->insert_id;
                
                // Create empty profile
                $profileStmt = $this->db->prepare("
                    INSERT INTO applicant_profiles (user_id, created_at)
                    VALUES (?, NOW())
                ");
                $profileStmt->bind_param('i', $userId);
                $profileStmt->execute();
                
                // Auto login
                $_SESSION['user_id'] = $userId;
                $_SESSION['role_id'] = 3;
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_email'] = $email;
                
                flash('success', 'Registration successful! Welcome to Indo Ocean Crew Services.');
                $this->redirect(url('/applicant/dashboard'));
            } else {
                $errors['register'] = 'Registration failed. Please try again.';
            }
        }
        
        $_SESSION['old'] = $_POST;
        $_SESSION['errors'] = $errors;
        $this->redirect(url('/register'));
    }
}
