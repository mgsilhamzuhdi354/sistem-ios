<?php
/**
 * Base Controller
 */
class BaseController {
    protected $db;
    
    // Session timeout in minutes (users inactive longer will be set offline)
    const SESSION_TIMEOUT_MINUTES = 5;
    
    public function __construct() {
        $this->db = getDB();
        
        // Heartbeat: Update user activity and set inactive users offline
        $this->updateUserActivity();
    }
    
    /**
     * Update current user's activity and set inactive users offline
     */
    protected function updateUserActivity() {
        if (!$this->db) return;
        
        // Update current user's activity if logged in
        if (isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
            $this->db->query("UPDATE users SET is_online = 1, last_activity = NOW() WHERE id = $userId");
        }
        
        // Auto-set offline for users inactive for more than SESSION_TIMEOUT_MINUTES
        // This runs periodically (every 5 requests to reduce database load)
        if (!isset($_SESSION['last_cleanup']) || (time() - $_SESSION['last_cleanup']) > 60) {
            $timeout = self::SESSION_TIMEOUT_MINUTES;
            $this->db->query("UPDATE users SET is_online = 0 WHERE is_online = 1 AND last_activity < DATE_SUB(NOW(), INTERVAL $timeout MINUTE)");
            $_SESSION['last_cleanup'] = time();
        }
    }
    
    /**
     * Check if a user is currently online (active within timeout period)
     */
    public static function isUserOnline($lastActivity) {
        if (!$lastActivity) return false;
        $lastActiveTime = strtotime($lastActivity);
        $timeoutSeconds = self::SESSION_TIMEOUT_MINUTES * 60;
        return (time() - $lastActiveTime) < $timeoutSeconds;
    }
    
    /**
     * Get human-readable "last seen" text
     */
    public static function getLastSeenText($lastActivity) {
        if (!$lastActivity) return 'Never';
        
        $diff = time() - strtotime($lastActivity);
        
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        
        return date('d M Y, H:i', strtotime($lastActivity));
    }
    
    protected function view($template, $data = []) {
        view($template, $data);
    }
    
    protected function redirect($url) {
        redirect($url);
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    protected function file($key) {
        return $_FILES[$key] ?? null;
    }
}
