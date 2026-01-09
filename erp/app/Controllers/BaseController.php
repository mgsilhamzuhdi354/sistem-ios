<?php
/**
 * PT Indo Ocean - ERP System
 * Base Controller with Authentication & Authorization
 */

namespace App\Controllers;

require_once APPPATH . 'Config/Constants.php';

class BaseController
{
    protected $db;
    protected $recruitmentDb;
    protected $session;
    protected $request;
    
    // Session timeout in seconds (30 minutes)
    const SESSION_TIMEOUT = 1800;
    
    public function __construct()
    {
        // Load databases
        $dbConfig = require APPPATH . 'Config/Database.php';
        $this->db = $this->connectDatabase($dbConfig['default']);
        $this->recruitmentDb = $this->connectDatabase($dbConfig['recruitment']);
        
        // Start session with secure settings
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            // Use 'Lax' for compatibility with ngrok (Strict blocks cross-origin redirects)
            ini_set('session.cookie_samesite', 'Lax');
            // Set secure cookie only for HTTPS
            if ($isHttps) {
                ini_set('session.cookie_secure', 1);
            }
            session_start();
        }
        
        // Check session timeout
        $this->checkSessionTimeout();
        
        $this->session = $_SESSION;
        
        // Set security headers
        $this->setSecurityHeaders();
        
        // Require authentication for all pages except Auth controller
        // Child controllers can set $this->skipAuth = true before calling parent::__construct()
        if (!$this->shouldSkipAuth()) {
            $this->requireAuth();
        }
    }
    
    /**
     * Check if authentication should be skipped (for login pages)
     */
    protected function shouldSkipAuth()
    {
        // Get current controller from URL
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Skip auth for ALL auth controller pages (login, authenticate, logout, register, etc.)
        // This covers /auth/, /auth/login, /auth/authenticate, /auth/verify-otp, etc.
        if (strpos($uri, '/auth/') !== false || preg_match('/\/auth\/?$/', $uri)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Set security headers
     */
    protected function setSecurityHeaders()
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
    
    /**
     * Check session timeout
     */
    protected function checkSessionTimeout()
    {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT) {
                // Session expired
                $this->destroySession();
                return;
            }
        }
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Destroy session
     */
    protected function destroySession()
    {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }
    
    protected $dbConfig;
    
    protected function connectDatabase($config)
    {
        // Store config for reconnection
        $this->dbConfig = $config;
        
        // Enable auto-reconnect options
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        $conn = new \mysqli(
            $config['hostname'],
            $config['username'],
            $config['password'],
            $config['database'],
            $config['port']
        );
        
        if ($conn->connect_error) {
            die('Database connection failed: ' . $conn->connect_error);
        }
        
        $conn->set_charset($config['charset']);
        
        // Set connection options to prevent "gone away" errors
        $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        
        return $conn;
    }
    
    /**
     * Ensure database connection is alive, reconnect if needed
     */
    protected function ensureConnection()
    {
        if (!$this->db || !$this->db->ping()) {
            $dbConfig = require APPPATH . 'Config/Database.php';
            $this->db = $this->connectDatabase($dbConfig['default']);
        }
        return $this->db;
    }
    
    protected function view($view, $data = [])
    {
        extract($data);
        $viewPath = APPPATH . 'Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            ob_start();
            include $viewPath;
            return ob_get_clean();
        }
        
        return "View not found: $view";
    }
    
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url)
    {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    protected function input($key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    protected function getCurrentUser()
    {
        return $_SESSION['user'] ?? null;
    }
    
    protected function isLoggedIn()
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }
    
    /**
     * Require authentication - redirect to login if not logged in
     */
    protected function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Silakan login terlebih dahulu');
            $this->redirect('auth/login');
        }
    }
    
    /**
     * Require permission for module/action
     */
    protected function requirePermission($module, $action = 'view')
    {
        if (!$this->checkPermission($module, $action)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Access denied'], 403);
            }
            $this->setFlash('error', 'Anda tidak memiliki akses ke halaman ini');
            $this->redirect('');
        }
    }
    
    /**
     * Check if current user has permission
     */
    protected function checkPermission($module, $action = 'view')
    {
        $user = $this->getCurrentUser();
        if (!$user) return false;
        
        // Super admin has all permissions
        if ($user['role'] === 'super_admin') {
            return true;
        }
        
        // Check permission from database
        $sql = "SELECT * FROM role_permissions WHERE role = ? AND module = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $user['role'], $module);
        $stmt->execute();
        $result = $stmt->get_result();
        $permission = $result->fetch_assoc();
        
        if (!$permission) return false;
        
        switch ($action) {
            case 'view': return (bool)$permission['can_view'];
            case 'create': return (bool)$permission['can_create'];
            case 'edit': return (bool)$permission['can_edit'];
            case 'delete': return (bool)$permission['can_delete'];
            default: return false;
        }
    }
    
    /**
     * Check if user has specific role
     */
    protected function hasRole($role)
    {
        $user = $this->getCurrentUser();
        if (!$user) return false;
        
        if (is_array($role)) {
            return in_array($user['role'], $role);
        }
        
        return $user['role'] === $role;
    }
    
    protected function setFlash($type, $message)
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
    
    protected function getFlash()
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
    
    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken($token)
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
