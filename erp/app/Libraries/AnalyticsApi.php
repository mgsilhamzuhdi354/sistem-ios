<?php

namespace App\Libraries;

/**
 * Analytics API Library
 * Fetches analytics data from recruitment system
 */
class AnalyticsApi
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://localhost/PT_indoocean/recruitment';
    }

    /**
     * Get visitor statistics
     * 
     * @param array $params Query parameters (start_date, end_date)
     * @return array|null
     */
    public function getVisitorStats($params = [])
    {
        $query = http_build_query($params);
        $url = "{$this->baseUrl}/api/analytics/visitors?" . $query;

        return $this->makeRequest($url);
    }

    /**
     * Get recruitment funnel metrics
     * 
     * @param array $params Query parameters (start_date, end_date)
     * @return array|null
     */
    public function getRecruitmentFunnel($params = [])
    {
        $query = http_build_query($params);
        $url = "{$this->baseUrl}/api/analytics/recruitment/funnel?" . $query;

        return $this->makeRequest($url);
    }

    /**
     * Get recruitment timeline data
     * 
     * @param int $days Number of days to fetch
     * @return array|null
     */
    public function getRecruitmentTimeline($days = 30)
    {
        $url = "{$this->baseUrl}/api/analytics/recruitment/timeline?days={$days}";

        return $this->makeRequest($url);
    }

    /**
     * Get recent visitor logs
     * 
     * @param int $limit Number of records to fetch
     * @return array|null
     */
    public function getRecentVisitors($limit = 20)
    {
        $url = "{$this->baseUrl}/api/analytics/recent-visitors?limit={$limit}";

        return $this->makeRequest($url);
    }

    /**
     * Make HTTP request to recruitment API
     * 
     * @param string $url Full API URL
     * @return array|null
     */
    private function makeRequest($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                error_log('Analytics API HTTP Error: ' . $httpCode . ' - URL: ' . $url);
                return null;
            }

            $result = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('Analytics API JSON Error: ' . json_last_error_msg());
                return null;
            }

            return $result;

        } catch (\Exception $e) {
            error_log('Analytics API Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Test API connection
     * 
     * @return bool
     */
    public function testConnection()
    {
        $result = $this->getVisitorStats([
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d')
        ]);

        return $result !== null && isset($result['success']);
    }
}
