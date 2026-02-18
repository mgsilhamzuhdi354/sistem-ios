<?php
/**
 * PT Indo Ocean - ERP System
 * Notification Controller
 */

namespace App\Controllers;

require_once APPPATH . 'Models/SettingsModel.php';

use App\Models\NotificationModel;

class Notification extends BaseController
{
    private $notificationModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new NotificationModel($this->db);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Notifications',
            'currentPage' => 'notifications',
            'notifications' => $this->notificationModel->getForUser(null, 100),
            'flash' => $this->getFlash()
        ];
        
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'notifications/index_modern' : 'notifications/index';

        return $this->view($view, $data);
    }
    
    public function getUnread()
    {
        $notifications = $this->notificationModel->getUnread(null, 10);
        $count = $this->notificationModel->countUnread();
        
        $this->json([
            'success' => true,
            'count' => $count,
            'notifications' => $notifications
        ]);
    }
    
    public function markRead($id)
    {
        $this->notificationModel->markAsRead($id);
        
        if ($this->isAjax()) {
            $this->json(['success' => true]);
        } else {
            $this->redirect('notifications');
        }
    }
    
    public function markAllRead()
    {
        $this->notificationModel->markAllAsRead();
        
        if ($this->isAjax()) {
            $this->json(['success' => true]);
        } else {
            $this->setFlash('success', 'All notifications marked as read');
            $this->redirect('notifications');
        }
    }
    
    public function generate()
    {
        // Generate notifications for expiring contracts
        require_once APPPATH . 'Models/ContractModel.php';
        $contractModel = new \App\Models\ContractModel($this->db);
        
        $expiringContracts = $contractModel->getExpiring(30);
        $count = 0;
        
        foreach ($expiringContracts as $contract) {
            $days = $contract['days_remaining'];
            
            // Check if notification already exists for today
            $checkSql = "SELECT id FROM notifications WHERE JSON_EXTRACT(data, '$.contract_id') = ? AND DATE(created_at) = CURDATE()";
            $existing = $this->db->prepare($checkSql);
            if ($existing) {
                $existing->bind_param('i', $contract['id']);
                $existing->execute();
                $result = $existing->get_result();
                
                if ($result->num_rows == 0) {
                    $this->notificationModel->notifyContractExpiry($contract, $days);
                    $count++;
                }
                $existing->close();
            }
        }
        
        $this->setFlash('success', "Generated $count notifications");
        $this->redirect('notifications');
    }
}
