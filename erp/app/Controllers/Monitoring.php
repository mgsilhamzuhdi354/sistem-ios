<?php
/**
 * PT Indo Ocean - ERP System
 * Monitoring Controller
 * Central monitoring dashboard for all systems
 */

namespace App\Controllers;

class Monitoring extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // Connect to recruitment database
        $this->recruitmentDb = new \mysqli(
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['RECRUITMENT_DB_NAME'] ?? 'recruitment_db',
            $_ENV['DB_PORT'] ?? 3306
        );
    }

    /**
     * Main monitoring dashboard
     */
    public function index()
    {
        $this->requireAuth();

        // Get overview statistics from all systems
        $stats = [
            'visitors_today' => $this->getVisitorsToday(),
            'visitors_month' => $this->getVisitorsMonth(),
            'recruitment_active' => $this->getRecruitmentActive(),
            'pending_approvals' => $this->getPendingApprovals(),
            'integration_status' => $this->getIntegrationStatus()
        ];

        return $this->view('monitoring/index', [
            'title' => 'Monitoring Dashboard',
            'currentPage' => 'monitoring',
            'stats' => $stats,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Company Profile visitor tracking
     */
    public function visitors()
    {
        $this->requireAuth();

        $period = $_GET['period'] ?? 'today';

        // Get visitor logs
        $query = "SELECT * FROM visitor_logs WHERE 1=1";

        if ($period === 'today') {
            $query .= " AND DATE(visited_at) = CURDATE()";
        } elseif ($period === 'week') {
            $query .= " AND visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $query .= " AND visited_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }

        $query .= " ORDER BY visited_at DESC LIMIT 1000";

        $result = $this->db->query($query);
        $visitors = [];
        while ($row = $result->fetch_assoc()) {
            $visitors[] = $row;
        }

        // Get statistics
        $stats = $this->getVisitorStatistics($period);

        // Check UI mode
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';
        $viewFile = $uiMode === 'modern' ? 'monitoring/visitors_modern' : 'monitoring/visitors';

        return $this->view($viewFile, [
            'title' => 'Visitor Company Profile',
            'currentPage' => 'monitoring',
            'visitors' => $visitors,
            'stats' => $stats,
            'period' => $period,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Activity log from all systems
     */
    public function activity()
    {
        $this->requireAuth();

        $system = $_GET['system'] ?? 'all';
        $limit = $_GET['limit'] ?? 100;

        $query = "SELECT * FROM integration_logs WHERE 1=1";

        if ($system !== 'all') {
            $stmt = $this->db->prepare("SELECT * FROM integration_logs WHERE source_system = ? ORDER BY created_at DESC LIMIT ?");
            $stmt->bind_param('si', $system, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $query .= " ORDER BY created_at DESC LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $limit);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        // Check UI mode
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';
        $viewFile = $uiMode === 'modern' ? 'monitoring/activity_modern' : 'monitoring/activity';

        return $this->view($viewFile, [
            'title' => 'Activity Log',
            'currentPage' => 'monitoring',
            'logs' => $logs,
            'system' => $system,
            'flash' => $this->getFlash()
        ]);
    }

    /**
     * Integration status check
     */
    public function integration()
    {
        $this->requireAuth();

        $integrations = [
            'hris' => $this->checkHRISConnection(),
            'recruitment' => $this->checkRecruitmentConnection(),
            'company_profile' => $this->checkCompanyProfileTracking(),
            'finance' => $this->checkFinanceModule()
        ];

        // Check UI mode
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';
        $viewFile = $uiMode === 'modern' ? 'monitoring/integration_modern' : 'monitoring/integration';

        return $this->view($viewFile, [
            'title' => 'Integration Status',
            'currentPage' => 'monitoring',
            'integrations' => $integrations,
            'flash' => $this->getFlash()
        ]);
    }

    // ===== PRIVATE HELPER METHODS =====

    private function getVisitorsToday()
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM visitor_logs WHERE DATE(visited_at) = CURDATE()");
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    private function getVisitorsMonth()
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM visitor_logs WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    private function getRecruitmentActive()
    {
        if (!$this->recruitmentDb)
            return 0;

        // Status IDs: 2=Document Screening, 3=Interview, 4=Medical Check
        $result = $this->recruitmentDb->query("SELECT COUNT(*) as count FROM applications WHERE status_id IN (2, 3, 4)");
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    private function getPendingApprovals()
    {
        if (!$this->recruitmentDb)
            return 0;

        // Status ID: 5=Final Review (pending approval)
        $result = $this->recruitmentDb->query("SELECT COUNT(*) as count FROM applications WHERE status_id = 5");
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    private function getIntegrationStatus()
    {
        $status = [
            'hris' => $this->checkHRISConnection(),
            'recruitment' => $this->checkRecruitmentConnection()
        ];

        $active = 0;
        foreach ($status as $s) {
            if ($s['status'] === 'connected')
                $active++;
        }

        return ['active' => $active, 'total' => count($status)];
    }

    private function getVisitorStatistics($period)
    {
        $whereClause = "";
        if ($period === 'today') {
            $whereClause = "WHERE DATE(visited_at) = CURDATE()";
        } elseif ($period === 'week') {
            $whereClause = "WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $whereClause = "WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }

        // Top pages
        $query = "SELECT page_url, COUNT(*) as count FROM visitor_logs $whereClause GROUP BY page_url ORDER BY count DESC LIMIT 5";
        $result = $this->db->query($query);
        $topPages = [];
        while ($row = $result->fetch_assoc()) {
            $topPages[] = $row;
        }

        // Top countries
        $query = "SELECT country, COUNT(*) as count FROM visitor_logs $whereClause AND country IS NOT NULL GROUP BY country ORDER BY count DESC LIMIT 5";
        $result = $this->db->query($query);
        $topCountries = [];
        while ($row = $result->fetch_assoc()) {
            $topCountries[] = $row;
        }

        return [
            'top_pages' => $topPages,
            'top_countries' => $topCountries
        ];
    }

    private function checkHRISConnection()
    {
        require_once APPPATH . 'Libraries/HrisApi.php';
        $hrisApi = new \App\Libraries\HrisApi();

        $result = $hrisApi->getEmployees(['limit' => 1]);

        return [
            'name' => 'HRIS Absensi',
            'status' => $result['success'] ? 'connected' : 'error',
            'message' => $result['success'] ? 'Connected' : ($result['error'] ?? 'Connection failed'),
            'last_check' => date('Y-m-d H:i:s')
        ];
    }

    private function checkRecruitmentConnection()
    {
        if (!$this->recruitmentDb) {
            return [
                'name' => 'Recruitment System',
                'status' => 'error',
                'message' => 'Database connection failed',
                'last_check' => date('Y-m-d H:i:s')
            ];
        }

        $result = $this->recruitmentDb->query("SELECT COUNT(*) as count FROM applications LIMIT 1");

        return [
            'name' => 'Recruitment System',
            'status' => $result ? 'connected' : 'error',
            'message' => $result ? 'Connected' : 'Query failed',
            'last_check' => date('Y-m-d H:i:s')
        ];
    }

    private function checkCompanyProfileTracking()
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM visitor_logs WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $row = $result->fetch_assoc();
        $recentVisits = $row['count'] ?? 0;

        return [
            'name' => 'Company Profile Tracking',
            'status' => $recentVisits > 0 ? 'active' : 'idle',
            'message' => "Recent visits: $recentVisits in last hour",
            'last_check' => date('Y-m-d H:i:s')
        ];
    }

    private function checkFinanceModule()
    {
        try {
            // Check if payroll tables exist and have data
            $result = $this->db->query("SELECT COUNT(*) as count FROM payroll_periods");
            if (!$result) {
                return [
                    'name' => 'Finance & Payroll',
                    'status' => 'error',
                    'message' => 'Payroll tables not found',
                    'last_check' => date('Y-m-d H:i:s')
                ];
            }
            $row = $result->fetch_assoc();
            $totalPeriods = $row['count'] ?? 0;

            // Check for recent payroll activity (generated in last 30 days)
            $recent = $this->db->query("SELECT COUNT(*) as count FROM payroll_periods WHERE processed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $recentRow = $recent->fetch_assoc();
            $recentPeriods = $recentRow['count'] ?? 0;

            // Count total payroll items
            $items = $this->db->query("SELECT COUNT(*) as count FROM payroll_items");
            $itemRow = $items->fetch_assoc();
            $totalItems = $itemRow['count'] ?? 0;

            if ($recentPeriods > 0) {
                return [
                    'name' => 'Finance & Payroll',
                    'status' => 'active',
                    'message' => "Active - {$totalPeriods} periods, {$totalItems} payslips",
                    'last_check' => date('Y-m-d H:i:s')
                ];
            } elseif ($totalPeriods > 0) {
                return [
                    'name' => 'Finance & Payroll',
                    'status' => 'connected',
                    'message' => "Connected - {$totalPeriods} periods, no recent activity",
                    'last_check' => date('Y-m-d H:i:s')
                ];
            } else {
                return [
                    'name' => 'Finance & Payroll',
                    'status' => 'idle',
                    'message' => 'No payroll data generated yet',
                    'last_check' => date('Y-m-d H:i:s')
                ];
            }
        } catch (\Exception $e) {
            return [
                'name' => 'Finance & Payroll',
                'status' => 'error',
                'message' => 'Module check failed: ' . $e->getMessage(),
                'last_check' => date('Y-m-d H:i:s')
            ];
        }
    }

    public function __destruct()
    {
        if ($this->recruitmentDb) {
            $this->recruitmentDb->close();
        }
    }
}
