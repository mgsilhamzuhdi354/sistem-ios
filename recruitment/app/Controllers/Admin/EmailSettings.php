<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Admin Email Settings Controller
 * Manage SMTP config and email templates
 */
class EmailSettings extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if (!isLoggedIn() || !isAdmin()) {
            redirect(url('/login'));
        }
    }

    /**
     * Settings page
     */
    public function index()
    {
        // Get current SMTP settings
        $settings = [];
        $keys = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_from_email', 'smtp_from_name', 'email_auto_send'];
        foreach ($keys as $key) {
            $row = $this->db->query("SELECT setting_value FROM settings WHERE setting_key = '$key'")->fetch_assoc();
            $settings[$key] = $row ? $row['setting_value'] : '';
        }

        // Get templates
        $templates = $this->db->query("SELECT * FROM email_templates ORDER BY name")->fetch_all(MYSQLI_ASSOC);

        // Get recent logs
        $logs = $this->db->query("
            SELECT el.*, et.name as template_name, u.full_name as sender_name
            FROM email_logs el
            LEFT JOIN email_templates et ON el.template_id = et.id
            LEFT JOIN users u ON el.user_id = u.id
            ORDER BY el.created_at DESC
            LIMIT 100
        ")->fetch_all(MYSQLI_ASSOC);

        // Stats
        $totalSent = $this->db->query("SELECT COUNT(*) as c FROM email_logs WHERE status = 'sent'")->fetch_assoc()['c'];
        $totalPending = $this->db->query("SELECT COUNT(*) as c FROM email_logs WHERE status = 'pending'")->fetch_assoc()['c'];
        $totalFailed = $this->db->query("SELECT COUNT(*) as c FROM email_logs WHERE status = 'failed'")->fetch_assoc()['c'];

        $this->view('admin/email_settings/index', [
            'pageTitle' => 'Email Settings',
            'settings' => $settings,
            'templates' => $templates,
            'logs' => $logs,
            'totalSent' => $totalSent,
            'totalPending' => $totalPending,
            'totalFailed' => $totalFailed
        ]);
    }

    /**
     * Save SMTP settings
     */
    public function save()
    {
        if (!$this->isPost()) {
            flash('error', 'Invalid request');
            return $this->redirect(url('/admin/email-settings'));
        }

        $keys = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_from_email', 'smtp_from_name', 'email_auto_send'];

        foreach ($keys as $key) {
            $value = $this->input($key) ?: '';
            $stmt = $this->db->prepare("INSERT INTO settings (setting_key, setting_value, setting_type, description)
                VALUES (?, ?, 'string', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $desc = ucwords(str_replace('_', ' ', $key));
            $stmt->bind_param('ssss', $key, $value, $desc, $value);
            $stmt->execute();
        }

        flash('success', 'SMTP settings berhasil disimpan!');
        $this->redirect(url('/admin/email-settings'));
    }

    /**
     * Test SMTP connection
     */
    public function test()
    {
        try {
            require_once APPPATH . 'Libraries/Mailer.php';
            $mailer = new Mailer($this->db);
            $result = $mailer->send(
                $this->input('test_email') ?: 'test@test.com',
                'Test',
                'Test Email dari PT Indo Ocean Recruitment',
                '<h2>Test Email</h2><p>Ini adalah email test dari sistem recruitment. Jika Anda menerima email ini, SMTP sudah terkonfigurasi dengan benar.</p>',
                $_SESSION['user_id']
            );
            return $this->json(['success' => true, 'message' => 'Test email dikirim! Cek: ' . ($result['message'] ?? 'OK')]);
        } catch (Exception $e) {
            return $this->json(['success' => false, 'message' => 'SMTP Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle template active/inactive
     */
    public function toggleTemplate()
    {
        $id = intval($this->input('template_id'));
        $active = intval($this->input('is_active'));
        $this->db->query("UPDATE email_templates SET is_active = $active WHERE id = $id");
        return $this->json(['success' => true]);
    }
}
