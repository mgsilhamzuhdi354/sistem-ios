<?php
/**
 * PT Indo Ocean - ERP System
 * API Controller for external systems
 */

namespace App\Controllers;

class Api extends BaseController
{
    /**
     * Track visitor from Company Profile
     */
    public function trackVisitor()
    {
        // Get JSON data from request
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
            exit;
        }

        try {
            // Get visitor information
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? null;
            $pageUrl = $data['page_url'] ?? null;
            $referrer = $data['referrer'] ?? null;

            // Parse device and browser from user agent
            $deviceType = $this->detectDevice($userAgent);
            $browser = $this->detectBrowser($userAgent);

            // Get geolocation (optional - requires external API)
            $country = $this->getCountryFromIP($ipAddress);
            $city = null;

            // Insert to database
            $stmt = $this->db->prepare("
                INSERT INTO visitor_logs 
                (ip_address, user_agent, page_url, referrer, country, city, device_type, browser, visited_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->bind_param(
                'ssssssss',
                $ipAddress,
                $userAgent,
                $pageUrl,
                $referrer,
                $country,
                $city,
                $deviceType,
                $browser
            );

            $stmt->execute();
            $stmt->close();

            // Log to integration_logs
            $this->logIntegration('company_profile', 'visitor_tracked', 'visitor', null, [
                'ip' => $ipAddress,
                'page' => $pageUrl
            ]);

            echo json_encode(['success' => true]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get integration status (public endpoint)
     */
    public function getIntegrationStatus()
    {
        $status = [
            'hris' => $this->checkHRISConnection(),
            'recruitment' => $this->checkRecruitmentConnection(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        header('Content-Type: application/json');
        echo json_encode($status);
    }

    // ===== HELPER METHODS =====

    private function detectDevice($userAgent)
    {
        if (!$userAgent)
            return 'Unknown';

        if (preg_match('/mobile/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    private function detectBrowser($userAgent)
    {
        if (!$userAgent)
            return 'Unknown';

        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } else {
            return 'Other';
        }
    }

    private function getCountryFromIP($ip)
    {
        // Basic implementation - for production use ip-api.com or similar
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Indonesia'; // localhost
        }

        // Optional: Use free IP geolocation API
        // $response = file_get_contents("http://ip-api.com/json/{$ip}");
        // $data = json_decode($response, true);
        // return $data['country'] ?? 'Unknown';

        return null;
    }

    private function logIntegration($source, $action, $entityType, $entityId, $details)
    {
        $stmt = $this->db->prepare("
            INSERT INTO integration_logs (source_system, action, entity_type, entity_id, details, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $detailsJson = json_encode($details);
        $stmt->bind_param('sssss', $source, $action, $entityType, $entityId, $detailsJson);
        $stmt->execute();
        $stmt->close();
    }

    private function checkHRISConnection()
    {
        require_once APPPATH . 'Libraries/HrisApi.php';
        $hrisApi = new \App\Libraries\HrisApi();
        return $hrisApi->testConnection();
    }

    private function checkRecruitmentConnection()
    {
        $isWindows = (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

        if (!$isWindows) {
            // Docker / Linux / NAS
            $hostsToTry = ['172.17.0.3', '172.17.0.2', '172.17.0.4', '172.17.0.5'];
            foreach ($hostsToTry as $host) {
                try {
                    $db = @new \mysqli($host, 'root', 'rahasia123', 'recruitment_db', 3306);
                    if (!$db->connect_error) {
                        $db->close();
                        return true;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            return false;
        }

        // Windows / Laragon
        $db = @new \mysqli(
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['RECRUITMENT_DB_NAME'] ?? 'recruitment_db',
            $_ENV['DB_PORT'] ?? 3306
        );

        $connected = !$db->connect_error;
        if ($connected)
            $db->close();

        return $connected;
    }
}
