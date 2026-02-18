<?php

namespace App\Controllers;

use App\Libraries\AnalyticsApi;

/**
 * Analytics Controller - Monitoring Center
 * Display visitor tracking and recruitment analytics
 */
class Analytics extends BaseController
{
    protected $analyticsApi;

    public function __construct()
    {
        // Initialize parent controller (required for CI4)
        parent::__construct();
        $this->analyticsApi = new AnalyticsApi();
    }

    /**
     * Main monitoring center dashboard
     */
    public function index()
    {
        // Get date range (default: last 30 days) - using BaseController's input() method
        $startDate = $this->input('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = $this->input('end_date', date('Y-m-d'));

        // Fetch visitor stats
        $visitorStats = $this->analyticsApi->getVisitorStats([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        // Fetch recruitment funnel
        $funnelData = $this->analyticsApi->getRecruitmentFunnel([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        // Fetch recent visitors
        $recentVisitors = $this->analyticsApi->getRecentVisitors(25);

        $data = [
            'visitor_stats' => $visitorStats['data'] ?? [],
            'funnel_data' => $funnelData['data'] ?? [],
            'recent_visitors' => $recentVisitors['data'] ?? [],
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        return $this->view('analytics/index', $data);
    }

    /**
     * Detailed visitor analytics
     */
    public function visitors()
    {
        $startDate = $this->input('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = $this->input('end_date', date('Y-m-d'));

        $visitorStats = $this->analyticsApi->getVisitorStats([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $data = [
            'visitor_stats' => $visitorStats['data'] ?? [],
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        return $this->view('analytics/visitors', $data);
    }

    /**
     * Detailed recruitment analytics
     */
    public function recruitment()
    {
        $startDate = $this->input('start_date', date('Y-m-d', strtotime('-30 days')));
        $endDate = $this->input('end_date', date('Y-m-d'));

        $funnelData = $this->analyticsApi->getRecruitmentFunnel([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $timelineData = $this->analyticsApi->getRecruitmentTimeline(30);

        $data = [
            'funnel_data' => $funnelData['data'] ?? [],
            'timeline_data' => $timelineData['data'] ?? [],
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        return $this->view('analytics/recruitment', $data);
    }
}
