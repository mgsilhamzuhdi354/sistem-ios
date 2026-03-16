<?php
/**
 * PT Indo Ocean - ERP System
 * Recruiter Performance Controller
 * Tracks PIC points from recruitment activities
 */

namespace App\Controllers;

class RecruiterPerformance extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTables();
    }

    /**
     * Ensure performance tables exist
     */
    private function ensureTables()
    {
        // Create recruiter_points_config table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS recruiter_points_config (
                id INT PRIMARY KEY AUTO_INCREMENT,
                action_type VARCHAR(50) NOT NULL UNIQUE,
                points INT NOT NULL DEFAULT 0,
                label VARCHAR(100),
                description VARCHAR(255),
                icon VARCHAR(50) DEFAULT 'star',
                color VARCHAR(20) DEFAULT '#6366f1'
            )
        ");

        // Create recruiter_performance table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS recruiter_performance (
                id INT PRIMARY KEY AUTO_INCREMENT,
                recruiter_user_id INT NOT NULL,
                recruiter_name VARCHAR(100),
                action_type VARCHAR(50) NOT NULL,
                points INT NOT NULL DEFAULT 0,
                application_id INT NULL,
                applicant_name VARCHAR(100) NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_recruiter (recruiter_user_id),
                INDEX idx_action (action_type),
                INDEX idx_created (created_at)
            )
        ");

        // Seed default config if empty
        $check = $this->db->query("SELECT COUNT(*) as c FROM recruiter_points_config");
        if ($check && $check->fetch_assoc()['c'] == 0) {
            $defaults = [
                ['referral_apply', 10, 'Referral Apply', 'Pelamar apply menggunakan kode referral', 'card_giftcard', '#f59e0b'],
                ['applicant_apply', 5, 'Applicant Apply', 'Pelamar di-assign ke perekrut', 'person_add', '#3b82f6'],
                ['doc_screening', 3, 'Doc Screening', 'Screening dokumen selesai', 'fact_check', '#8b5cf6'],
                ['interview_done', 5, 'Interview Done', 'Interview selesai', 'record_voice_over', '#06b6d4'],
                ['medical_done', 3, 'Medical Check', 'Medical check selesai', 'health_and_safety', '#10b981'],
                ['approved', 15, 'Approved', 'Pelamar disetujui di ERP', 'verified', '#22c55e'],
                ['onboarded', 20, 'Onboarded', 'Pelamar berhasil onboard', 'emoji_events', '#eab308'],
            ];
            $stmt = $this->db->prepare("INSERT INTO recruiter_points_config (action_type, points, label, description, icon, color) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($defaults as $d) {
                $stmt->bind_param('sissss', $d[0], $d[1], $d[2], $d[3], $d[4], $d[5]);
                $stmt->execute();
            }
        }
    }

    /**
     * Dashboard - Leaderboard
     */
    public function index()
    {
        $this->requireAuth();

        $period = $_GET['period'] ?? 'all';
        $dateFilter = '';
        $periodLabel = 'Semua Waktu';

        switch ($period) {
            case 'month':
                $dateFilter = "AND rp.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
                $periodLabel = date('F Y');
                break;
            case 'quarter':
                $quarter = ceil(date('n') / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $dateFilter = "AND rp.created_at >= '{$this->getYear()}-" . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . "-01'";
                $periodLabel = "Q{$quarter} " . date('Y');
                break;
            case 'year':
                $dateFilter = "AND rp.created_at >= '{$this->getYear()}-01-01'";
                $periodLabel = date('Y');
                break;
        }

        // Get leaderboard
        $leaderboard = [];
        $result = $this->db->query("
            SELECT 
                rp.recruiter_user_id,
                rp.recruiter_name,
                SUM(rp.points) as total_points,
                COUNT(*) as total_actions,
                COUNT(DISTINCT rp.application_id) as total_applicants,
                MAX(rp.created_at) as last_activity
            FROM recruiter_performance rp
            WHERE 1=1 {$dateFilter}
            GROUP BY rp.recruiter_user_id, rp.recruiter_name
            ORDER BY total_points DESC
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $leaderboard[] = $row;
            }
        }

        // If no data yet, pull recruiter names from recruitment DB to show empty leaderboard
        if (empty($leaderboard) && $this->recruitmentDb && !$this->recruitmentDb->connect_error) {
            $recruiters = $this->recruitmentDb->query("
                SELECT u.id, u.full_name FROM users u WHERE u.role_id = 5 AND u.is_active = 1 ORDER BY u.full_name
            ");
            if ($recruiters) {
                while ($r = $recruiters->fetch_assoc()) {
                    $leaderboard[] = [
                        'recruiter_user_id' => $r['id'],
                        'recruiter_name' => $r['full_name'],
                        'total_points' => 0,
                        'total_actions' => 0,
                        'total_applicants' => 0,
                        'last_activity' => null,
                    ];
                }
            }
        }

        // Get points config
        $configs = [];
        $cfgResult = $this->db->query("SELECT * FROM recruiter_points_config ORDER BY points DESC");
        if ($cfgResult) {
            while ($row = $cfgResult->fetch_assoc()) {
                $configs[$row['action_type']] = $row;
            }
        }

        // Get recent activity
        $recentActivity = [];
        $actResult = $this->db->query("
            SELECT rp.*, rpc.icon, rpc.color, rpc.label
            FROM recruiter_performance rp
            LEFT JOIN recruiter_points_config rpc ON rp.action_type = rpc.action_type
            ORDER BY rp.created_at DESC
            LIMIT 20
        ");
        if ($actResult) {
            while ($row = $actResult->fetch_assoc()) {
                $recentActivity[] = $row;
            }
        }

        // Get breakdown by action type
        $breakdown = [];
        $bkResult = $this->db->query("
            SELECT rp.action_type, SUM(rp.points) as total_points, COUNT(*) as count
            FROM recruiter_performance rp
            WHERE 1=1 {$dateFilter}
            GROUP BY rp.action_type
            ORDER BY total_points DESC
        ");
        if ($bkResult) {
            while ($row = $bkResult->fetch_assoc()) {
                $breakdown[$row['action_type']] = $row;
            }
        }

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        $trendResult = $this->db->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(points) as total_points,
                COUNT(DISTINCT recruiter_user_id) as active_recruiters
            FROM recruiter_performance
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");
        if ($trendResult) {
            while ($row = $trendResult->fetch_assoc()) {
                $monthlyTrend[] = $row;
            }
        }

        // Total stats
        $totalRes = $this->db->query("SELECT COALESCE(SUM(rp.points),0) as tp, COUNT(DISTINCT rp.recruiter_user_id) as tr, COUNT(DISTINCT rp.application_id) as ta FROM recruiter_performance rp WHERE 1=1 {$dateFilter}");
        $totals = $totalRes ? $totalRes->fetch_assoc() : ['tp' => 0, 'tr' => 0, 'ta' => 0];

        $data = [
            'title' => 'Kinerja Perekrut',
            'currentPage' => 'recruiter-performance',
            'leaderboard' => $leaderboard,
            'configs' => $configs,
            'recentActivity' => $recentActivity,
            'breakdown' => $breakdown,
            'monthlyTrend' => $monthlyTrend,
            'period' => $period,
            'periodLabel' => $periodLabel,
            'totalPoints' => $totals['tp'],
            'totalRecruiters' => $totals['tr'],
            'totalApplicants' => $totals['ta'],
            'flash' => $this->getFlash()
        ];

        return $this->view('recruitment/performance_dashboard', $data);
    }

    /**
     * Detail for one recruiter
     */
    public function detail($recruiterId = null)
    {
        $this->requireAuth();
        if (!$recruiterId) { $this->redirect('RecruiterPerformance'); return; }

        // Get recruiter info from recruitment DB
        $recruiterName = 'Perekrut #' . $recruiterId;
        if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
            $stmt = $this->recruitmentDb->prepare("SELECT full_name FROM users WHERE id = ?");
            $stmt->bind_param('i', $recruiterId);
            $stmt->execute();
            $r = $stmt->get_result()->fetch_assoc();
            if ($r) $recruiterName = $r['full_name'];
        }

        // Get history
        $history = [];
        $stmt = $this->db->prepare("
            SELECT rp.*, rpc.icon, rpc.color, rpc.label
            FROM recruiter_performance rp
            LEFT JOIN recruiter_points_config rpc ON rp.action_type = rpc.action_type
            WHERE rp.recruiter_user_id = ?
            ORDER BY rp.created_at DESC
            LIMIT 100
        ");
        $stmt->bind_param('i', $recruiterId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }

        // Get totals by action type
        $actionTotals = [];
        $stmt2 = $this->db->prepare("
            SELECT action_type, SUM(points) as total_points, COUNT(*) as count
            FROM recruiter_performance
            WHERE recruiter_user_id = ?
            GROUP BY action_type
        ");
        $stmt2->bind_param('i', $recruiterId);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        while ($row = $result2->fetch_assoc()) {
            $actionTotals[$row['action_type']] = $row;
        }

        $totalStmt = $this->db->prepare("SELECT COALESCE(SUM(points),0) as tp FROM recruiter_performance WHERE recruiter_user_id = ?");
        $totalStmt->bind_param('i', $recruiterId);
        $totalStmt->execute();
        $totalPts = $totalStmt->get_result()->fetch_assoc()['tp'];

        $configs = [];
        $cfgResult = $this->db->query("SELECT * FROM recruiter_points_config ORDER BY points DESC");
        if ($cfgResult) { while ($row = $cfgResult->fetch_assoc()) { $configs[$row['action_type']] = $row; } }

        $data = [
            'title' => 'Detail Kinerja: ' . $recruiterName,
            'currentPage' => 'recruiter-performance',
            'recruiterId' => $recruiterId,
            'recruiterName' => $recruiterName,
            'history' => $history,
            'actionTotals' => $actionTotals,
            'totalPoints' => $totalPts,
            'configs' => $configs,
            'flash' => $this->getFlash()
        ];

        return $this->view('recruitment/performance_detail', $data);
    }

    /**
     * Settings - configure points per action
     */
    public function settings()
    {
        $this->requireAuth();

        if ($this->isPost()) {
            // Save settings
            $actions = $_POST['action_type'] ?? [];
            $points = $_POST['points'] ?? [];
            
            for ($i = 0; $i < count($actions); $i++) {
                $action = $actions[$i];
                $point = intval($points[$i] ?? 0);
                $stmt = $this->db->prepare("UPDATE recruiter_points_config SET points = ? WHERE action_type = ?");
                $stmt->bind_param('is', $point, $action);
                $stmt->execute();
            }

            if ($this->isAjax()) {
                return $this->json(['success' => true, 'message' => 'Konfigurasi poin berhasil disimpan!']);
            }
            $this->setFlash('success', 'Konfigurasi poin berhasil disimpan!');
            $this->redirect('RecruiterPerformance/settings');
            return;
        }

        $configs = [];
        $result = $this->db->query("SELECT * FROM recruiter_points_config ORDER BY id ASC");
        if ($result) { while ($row = $result->fetch_assoc()) { $configs[] = $row; } }

        $data = [
            'title' => 'Konfigurasi Poin Perekrut',
            'currentPage' => 'recruiter-performance',
            'configs' => $configs,
            'flash' => $this->getFlash()
        ];

        return $this->view('recruitment/performance_settings', $data);
    }

    /**
     * Award points to a recruiter (called from other controllers)
     */
    public static function awardPoints($db, $recruitmentDb, $recruiterId, $actionType, $applicationId = null, $applicantName = null, $description = null)
    {
        // Get points config
        $cfgStmt = $db->prepare("SELECT points FROM recruiter_points_config WHERE action_type = ?");
        $cfgStmt->bind_param('s', $actionType);
        $cfgStmt->execute();
        $cfg = $cfgStmt->get_result()->fetch_assoc();
        if (!$cfg) return false;
        $points = $cfg['points'];

        // Get recruiter name
        $recruiterName = 'Unknown';
        if ($recruitmentDb && !$recruitmentDb->connect_error) {
            $nStmt = $recruitmentDb->prepare("SELECT full_name FROM users WHERE id = ?");
            $nStmt->bind_param('i', $recruiterId);
            $nStmt->execute();
            $n = $nStmt->get_result()->fetch_assoc();
            if ($n) $recruiterName = $n['full_name'];
        }

        // Check for duplicate (same recruiter, same action, same application)
        if ($applicationId) {
            $dupStmt = $db->prepare("SELECT id FROM recruiter_performance WHERE recruiter_user_id = ? AND action_type = ? AND application_id = ?");
            $dupStmt->bind_param('isi', $recruiterId, $actionType, $applicationId);
            $dupStmt->execute();
            if ($dupStmt->get_result()->num_rows > 0) return false; // Already awarded
        }

        $stmt = $db->prepare("
            INSERT INTO recruiter_performance 
            (recruiter_user_id, recruiter_name, action_type, points, application_id, applicant_name, description, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param('ississs', $recruiterId, $recruiterName, $actionType, $points, $applicationId, $applicantName, $description);
        return $stmt->execute();
    }

    /**
     * Manual award (AJAX)
     */
    public function award()
    {
        $this->requireAuth();
        if (!$this->isPost()) return $this->json(['success' => false, 'message' => 'Invalid request']);

        $recruiterId = intval($this->input('recruiter_id'));
        $actionType = $this->input('action_type');
        $applicationId = $this->input('application_id');
        $applicantName = $this->input('applicant_name');
        $description = $this->input('description') ?: 'Manual award';

        if (!$recruiterId || !$actionType) {
            return $this->json(['success' => false, 'message' => 'Recruiter ID dan action type wajib diisi']);
        }

        $result = self::awardPoints($this->db, $this->recruitmentDb, $recruiterId, $actionType, $applicationId, $applicantName, $description);

        if ($result) {
            return $this->json(['success' => true, 'message' => 'Poin berhasil diberikan!']);
        }
        return $this->json(['success' => false, 'message' => 'Gagal atau poin sudah pernah diberikan']);
    }

    private function getYear()
    {
        return date('Y');
    }
}
