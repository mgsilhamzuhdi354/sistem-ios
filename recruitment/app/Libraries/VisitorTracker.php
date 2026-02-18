<?php

namespace App\Libraries;

use Config\Database;

/**
 * Visitor Tracker Library
 * Track visitor analytics for company profile pages
 */
class VisitorTracker
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Track page visit
     * 
     * @param string $page_visited Page URL or identifier
     * @return bool Success status
     */
    public function track($page_visited = '')
    {
        try {
            // Get visitor data
            $ip = $this->getIpAddress();
            $userAgent = $this->getUserAgent();
            $referrer = $this->getReferrer();
            $deviceType = $this->getDeviceType($userAgent);
            $sessionId = $this->getSessionId();

            // Get geolocation (basic, can be enhanced with API)
            $geo = $this->getGeolocation($ip);

            // Insert to database
            $builder = $this->db->table('visitor_logs');
            $data = [
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'referrer_url' => $referrer,
                'page_visited' => $page_visited ?: $_SERVER['REQUEST_URI'] ?? '/',
                'country' => $geo['country'] ?? null,
                'city' => $geo['city'] ?? null,
                'device_type' => $deviceType,
                'session_id' => $sessionId,
                'visited_at' => date('Y-m-d H:i:s')
            ];

            return $builder->insert($data);

        } catch (\Exception $e) {
            log_message('error', 'VisitorTracker Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get real IP address (handles proxy)
     */
    private function getIpAddress()
    {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return $_SERVER[$key];
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     */
    private function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    /**
     * Get referrer URL
     */
    private function getReferrer()
    {
        return $_SERVER['HTTP_REFERER'] ?? 'Direct';
    }

    /**
     * Detect device type from user agent
     */
    private function getDeviceType($userAgent)
    {
        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            if (preg_match('/ipad|tablet/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Get or create session ID
     */
    private function getSessionId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['visitor_id'])) {
            $_SESSION['visitor_id'] = uniqid('visitor_', true);
        }

        return $_SESSION['visitor_id'];
    }

    /**
     * Get geolocation from IP (basic implementation)
     * Can be enhanced with ipapi.co or ip-api.com API
     */
    private function getGeolocation($ip)
    {
        // Skip for local IPs
        if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
            return ['country' => 'Local', 'city' => 'Development'];
        }

        // Basic implementation - can add API call here
        // Example: $data = file_get_contents("http://ip-api.com/json/{$ip}");

        return [
            'country' => null,
            'city' => null
        ];
    }

    /**
     * Get visitor statistics
     * 
     * @param array $filters Date range, page, etc
     * @return array Statistics
     */
    public function getStatistics($filters = [])
    {
        $builder = $this->db->table('visitor_logs');

        // Apply date filter
        if (isset($filters['start_date'])) {
            $builder->where('visited_at >=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $builder->where('visited_at <=', $filters['end_date']);
        }

        // Apply page filter
        if (isset($filters['page'])) {
            $builder->where('page_visited', $filters['page']);
        }

        return [
            'total_visits' => $builder->countAllResults(false),
            'unique_visitors' => $builder->distinct()->countAllResults('ip_address'),
            'mobile_visitors' => $builder->where('device_type', 'mobile')->countAllResults(),
        ];
    }
}
