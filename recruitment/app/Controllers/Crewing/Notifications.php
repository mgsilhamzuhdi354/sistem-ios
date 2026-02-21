<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Crewing Notifications Controller
 * API endpoints for the notification bell dropdown
 */
class Notifications extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || (!isCrewing() && !isAdmin() && !isMasterAdmin())) {
            if ($this->isAjax()) {
                http_response_code(403);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            redirect(url('/login'));
        }
        
        // Ensure is_read column exists
        $check = @$this->db->query("SHOW COLUMNS FROM notifications LIKE 'is_read'");
        if ($check && $check->num_rows == 0) {
            $this->db->query("ALTER TABLE notifications ADD COLUMN is_read TINYINT(1) DEFAULT 0 AFTER action_url");
        }
    }
    
    /**
     * Check if request is AJAX
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * API: Get notifications for the current crewing user
     * Returns JSON for the dropdown bell
     */
    public function fetch() {
        $userId = $_SESSION['user_id'];
        
        // Get unread count
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) as c FROM notifications 
            WHERE user_id = ? AND (is_read = 0 OR is_read IS NULL)
        ");
        $countStmt->bind_param('i', $userId);
        $countStmt->execute();
        $unreadCount = $countStmt->get_result()->fetch_assoc()['c'];
        
        // Get recent notifications (last 20)
        $stmt = $this->db->prepare("
            SELECT id, title, message, type, action_url, is_read, created_at
            FROM notifications 
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 20
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Format timestamps
        foreach ($notifications as &$n) {
            $n['time_ago'] = $this->timeAgo($n['created_at']);
            $n['is_read'] = (int)$n['is_read'];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'unread_count' => (int)$unreadCount,
            'notifications' => $notifications
        ]);
        exit;
    }
    
    /**
     * API: Mark a notification as read
     */
    public function markRead($id) {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    
    /**
     * API: Mark all notifications as read
     */
    public function markAllRead() {
        $userId = $_SESSION['user_id'];
        
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND (is_read = 0 OR is_read IS NULL)");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'affected' => $this->db->affected_rows]);
        exit;
    }
    
    /**
     * Helper: Human-readable time ago
     */
    private function timeAgo($datetime) {
        $now = new \DateTime();
        $past = new \DateTime($datetime);
        $diff = $now->diff($past);
        
        if ($diff->y > 0) return $diff->y . ' tahun lalu';
        if ($diff->m > 0) return $diff->m . ' bulan lalu';
        if ($diff->d > 0) {
            if ($diff->d == 1) return 'Kemarin';
            return $diff->d . ' hari lalu';
        }
        if ($diff->h > 0) return $diff->h . ' jam lalu';
        if ($diff->i > 0) return $diff->i . ' menit lalu';
        return 'Baru saja';
    }
}
