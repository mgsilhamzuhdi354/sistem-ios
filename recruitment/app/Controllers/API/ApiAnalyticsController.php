<?php
/**
 * Analytics API Controller
 * Provides analytics data for ERP monitoring center
 */
require_once dirname(__DIR__) . '/BaseController.php';

class ApiAnalyticsController extends BaseController
{
    /**
     * GET /api/analytics/visitors
     * Get visitor statistics
     */
    public function visitors()
    {
        try {
            $startDate = $this->input('start_date', date('Y-m-d', strtotime('-30 days')));
            $endDate = $this->input('end_date', date('Y-m-d'));
            $startDateTime = $startDate . ' 00:00:00';
            $endDateTime = $endDate . ' 23:59:59';

            // Check if visitor_logs table exists
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'visitor_logs'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                // Return placeholder data if table doesn't exist
                $this->json([
                    'success' => true,
                    'data' => [
                        'total_visits' => 0,
                        'unique_visitors' => 0,
                        'devices' => [],
                        'traffic_sources' => []
                    ],
                    'message' => 'Visitor tracking not yet configured'
                ]);
                return;
            }

            // Total visits
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM visitor_logs 
                WHERE visited_at >= ? AND visited_at <= ?
            ");
            $stmt->bind_param('ss', $startDateTime, $endDateTime);
            $stmt->execute();
            $result = $stmt->get_result();
            $totalVisits = $result->fetch_assoc()['total'] ?? 0;

            // Unique visitors
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT ip_address) as total FROM visitor_logs 
                WHERE visited_at >= ? AND visited_at <= ?
            ");
            $stmt->bind_param('ss', $startDateTime, $endDateTime);
            $stmt->execute();
            $result = $stmt->get_result();
            $uniqueVisitors = $result->fetch_assoc()['total'] ?? 0;

            // Device breakdown
            $stmt = $this->db->prepare("
                SELECT device_type, COUNT(*) as count 
                FROM visitor_logs 
                WHERE visited_at >= ? AND visited_at <= ?
                GROUP BY device_type
            ");
            $stmt->bind_param('ss', $startDateTime, $endDateTime);
            $stmt->execute();
            $devices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Traffic sources
            $stmt = $this->db->prepare("
                SELECT 
                    CASE 
                        WHEN referrer_url = 'Direct' OR referrer_url IS NULL OR referrer_url = '' THEN 'Direct'
                        WHEN referrer_url LIKE '%google%' THEN 'Google'
                        WHEN referrer_url LIKE '%facebook%' THEN 'Facebook'
                        WHEN referrer_url LIKE '%linkedin%' THEN 'LinkedIn'
                        ELSE 'Other'
                    END as source,
                    COUNT(*) as count
                FROM visitor_logs 
                WHERE visited_at >= ? AND visited_at <= ?
                GROUP BY source
                ORDER BY count DESC
            ");
            $stmt->bind_param('ss', $startDateTime, $endDateTime);
            $stmt->execute();
            $sources = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $this->json([
                'success' => true,
                'data' => [
                    'total_visits' => (int) $totalVisits,
                    'unique_visitors' => (int) $uniqueVisitors,
                    'devices' => $devices,
                    'traffic_sources' => $sources
                ]
            ]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/analytics/recruitment/funnel
     * Get recruitment funnel metrics
     */
    public function recruitmentFunnel()
    {
        try {
            $startDate = $this->input('start_date', date('Y-m-d', strtotime('-30 days')));
            $endDate = $this->input('end_date', date('Y-m-d'));
            $startDateTime = $startDate . ' 00:00:00';
            $endDateTime = $endDate . ' 23:59:59';

            // Page views - check if table exists
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'visitor_logs'");
            $pageViews = 0;
            if ($tableCheck && $tableCheck->num_rows > 0) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total FROM visitor_logs 
                    WHERE visited_at >= ? AND visited_at <= ?
                ");
                $stmt->bind_param('ss', $startDateTime, $endDateTime);
                $stmt->execute();
                $pageViews = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
            }

            // Total applications
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM applications 
                WHERE created_at >= ? AND created_at <= ?
            ");
            $stmt->bind_param('ss', $startDateTime, $endDateTime);
            $stmt->execute();
            $applications = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

            // Interviews completed - handle missing table gracefully
            $interviews = 0;
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'interviews'");
            if ($tableCheck && $tableCheck->num_rows > 0) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total FROM interviews 
                    WHERE status = 'completed' 
                    AND scheduled_at >= ? AND scheduled_at <= ?
                ");
                $stmt->bind_param('ss', $startDateTime, $endDateTime);
                $stmt->execute();
                $interviews = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
            }

            // Approved candidates (status_id = 5)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM applications 
                WHERE status_id = 5 
                AND created_at >= ? AND created_at <= ?
            ");
            $stmt->bind_param('ss', $startDateTime, $endDateTime);
            $stmt->execute();
            $approved = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

            // Hired (synced to ERP)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total FROM applications 
                WHERE is_synced_to_erp = 1 
                AND created_at >= ? AND created_at <= ?
            ");
            $stmt->bind_param('ss', $startDateTime, $endDateTime);
            $stmt->execute();
            $hired = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

            $this->json([
                'success' => true,
                'data' => [
                    'funnel' => [
                        'page_views' => (int) $pageViews,
                        'applications' => (int) $applications,
                        'interviews' => (int) $interviews,
                        'approved' => (int) $approved,
                        'hired' => (int) $hired
                    ],
                    'conversion_rates' => [
                        'view_to_apply' => $pageViews > 0 ? round(($applications / $pageViews) * 100, 2) : 0,
                        'apply_to_interview' => $applications > 0 ? round(($interviews / $applications) * 100, 2) : 0,
                        'interview_to_approved' => $interviews > 0 ? round(($approved / $interviews) * 100, 2) : 0,
                        'approved_to_hired' => $approved > 0 ? round(($hired / $approved) * 100, 2) : 0
                    ]
                ]
            ]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/analytics/recruitment/timeline
     */
    public function recruitmentTimeline()
    {
        try {
            $days = (int) $this->input('days', 30);
            $startDate = date('Y-m-d', strtotime("-{$days} days"));

            $stmt = $this->db->prepare("
                SELECT DATE(created_at) as date, COUNT(*) as applications
                FROM applications
                WHERE created_at >= ?
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->bind_param('s', $startDate);
            $stmt->execute();
            $timeline = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $this->json([
                'success' => true,
                'data' => $timeline
            ]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/analytics/recent-visitors
     */
    public function recentVisitors()
    {
        try {
            $limit = (int) $this->input('limit', 20);

            // Check if table exists
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'visitor_logs'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                $this->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Visitor tracking not yet configured'
                ]);
                return;
            }

            $result = $this->db->query("
                SELECT * FROM visitor_logs 
                ORDER BY visited_at DESC 
                LIMIT $limit
            ");

            $visitors = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

            $this->json([
                'success' => true,
                'data' => $visitors
            ]);

        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
