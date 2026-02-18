<?php
require_once APPPATH . 'Controllers/BaseController.php';
require_once APPPATH . 'Libraries/Mailer.php';

/**
 * Email Settings Controller
 * Manage email templates and view logs
 */
class EmailSettings extends BaseController {
    
    private $mailer;
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
        $this->mailer = new Mailer($this->db);
    }
    
    /**
     * Email Dashboard - Templates and Logs
     */
    public function index() {
        $templates = $this->mailer->getTemplates();
        $logs = $this->mailer->getLogs(30);
        
        // Get stats
        $stats = $this->getStats();
        
        // Get current SMTP settings
        $smtpSettings = [
            'smtp_host' => '',
            'smtp_port' => '465',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'ssl',
            'smtp_from_email' => '',
            'smtp_from_name' => 'PT Indo Ocean Crew Services'
        ];
        $result = $this->db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'smtp_%'");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $smtpSettings[$row['setting_key']] = $row['setting_value'];
            }
        }
        
        $this->view('master_admin/email_settings/index', [
            'pageTitle' => 'Email Settings',
            'templates' => $templates,
            'logs' => $logs,
            'stats' => $stats,
            'smtpSettings' => $smtpSettings
        ]);
    }
    
    /**
     * Get email statistics
     */
    private function getStats() {
        $result = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
            FROM email_logs
        ");
        return $result ? $result->fetch_assoc() : ['total' => 0, 'sent' => 0, 'failed' => 0, 'today' => 0];
    }
    
    /**
     * Edit template
     */
    public function editTemplate($id) {
        if ($this->isPost()) {
            $subject = trim($this->input('subject'));
            $body = trim($this->input('body'));
            $isActive = $this->input('is_active') ? 1 : 0;
            $isAutoSend = $this->input('is_auto_send') ? 1 : 0;
            
            $stmt = $this->db->prepare("
                UPDATE email_templates 
                SET subject = ?, body = ?, is_active = ?, is_auto_send = ?
                WHERE id = ?
            ");
            $stmt->bind_param('ssiii', $subject, $body, $isActive, $isAutoSend, $id);
            $stmt->execute();
            
            flash('success', 'Template updated successfully');
            redirect(url('/master-admin/email-settings'));
        }
        
        $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $template = $stmt->get_result()->fetch_assoc();
        
        $this->view('master_admin/email_settings/edit_template', [
            'pageTitle' => 'Edit Email Template',
            'template' => $template
        ]);
    }
    
    /**
     * Send test email
     */
    public function sendTest() {
        $templateId = intval($this->input('template_id'));
        $email = trim($this->input('email'));
        
        if (!$email) {
            return $this->json(['success' => false, 'message' => 'Email address required']);
        }
        
        // Get template
        $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->bind_param('i', $templateId);
        $stmt->execute();
        $template = $stmt->get_result()->fetch_assoc();
        
        if (!$template) {
            return $this->json(['success' => false, 'message' => 'Template not found']);
        }
        
        // Replace variables with test data
        $testData = [
            'name' => 'Test Applicant',
            'email' => $email,
            'position' => 'Test Position',
            'date' => date('d M Y'),
            'interview_link' => url('/applicant/interview'),
            'deadline' => date('d M Y', strtotime('+7 days'))
        ];
        
        $subject = '[TEST] ' . $this->parseVariables($template['subject'], $testData);
        $body = $this->parseVariables($template['body'], $testData);
        
        $result = $this->mailer->send($email, 'Test User', $subject, $body, $_SESSION['user_id'], $templateId);
        
        return $this->json($result);
    }
    
    /**
     * Parse variables in template
     */
    private function parseVariables($text, $variables) {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }
    
    /**
     * View email logs
     */
    public function logs() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        $totalResult = $this->db->query("SELECT COUNT(*) as total FROM email_logs");
        $total = $totalResult->fetch_assoc()['total'];
        
        $result = $this->db->query("
            SELECT el.*, et.name as template_name
            FROM email_logs el
            LEFT JOIN email_templates et ON el.template_id = et.id
            ORDER BY el.created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $logs = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        
        $this->view('master_admin/email_settings/logs', [
            'pageTitle' => 'Email Logs',
            'logs' => $logs,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
            'total' => $total
        ]);
    }
    
    /**
     * Save SMTP settings
     */
    public function saveSettings() {
        if (!$this->isPost()) {
            redirect(url('/master-admin/email-settings'));
        }
        
        $settings = [
            'smtp_host' => trim($this->input('smtp_host')),
            'smtp_port' => trim($this->input('smtp_port')),
            'smtp_username' => trim($this->input('smtp_username')),
            'smtp_encryption' => trim($this->input('smtp_encryption')),
            'smtp_from_email' => trim($this->input('smtp_from_email')),
            'smtp_from_name' => trim($this->input('smtp_from_name'))
        ];
        
        // Only update password if provided (don't overwrite with empty)
        $password = trim($this->input('smtp_password'));
        if (!empty($password)) {
            $settings['smtp_password'] = $password;
        }
        
        foreach ($settings as $key => $value) {
            $stmt = $this->db->prepare("
                INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->bind_param('sss', $key, $value, $value);
            $stmt->execute();
        }
        
        flash('success', 'Pengaturan SMTP berhasil disimpan!');
        redirect(url('/master-admin/email-settings'));
    }
    
    /**
     * Manual send email to applicant
     */
    public function sendToApplicant() {
        $userId = intval($this->input('user_id'));
        $templateSlug = $this->input('template');
        $additionalData = [
            'position' => $this->input('position') ?? '',
            'interview_link' => url('/applicant/interview'),
            'deadline' => date('d M Y', strtotime('+7 days'))
        ];
        
        $result = $this->mailer->sendTemplate($userId, $templateSlug, $additionalData);
        
        return $this->json($result);
    }
}
