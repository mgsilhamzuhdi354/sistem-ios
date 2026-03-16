<?php
/**
 * PT Indo Ocean - ERP System
 * WhatsApp Service - Fonnte API Integration
 * 
 * Sends WhatsApp messages via Fonnte API (https://fonnte.com)
 * All settings (API token, target phone) are read from the `settings` table.
 */

namespace App\Libraries;

class WhatsAppService
{
    private const API_URL = 'https://api.fonnte.com/send';
    
    private $apiToken = '';
    private $errors = [];
    
    /**
     * Constructor - loads API token from settings DB
     */
    public function __construct($apiToken = null)
    {
        if ($apiToken) {
            $this->apiToken = $apiToken;
        } else {
            $this->loadSettings();
        }
    }
    
    /**
     * Load WhatsApp settings from database
     */
    private function loadSettings()
    {
        try {
            require_once APPPATH . 'Models/SettingsModel.php';
            $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
            $dbUser = $_ENV['DB_USER'] ?? 'root';
            $dbPass = $_ENV['DB_PASS'] ?? '';
            $dbName = $_ENV['DB_NAME'] ?? 'indoocean_erp';
            
            $db = new \mysqli($dbHost, $dbUser, $dbPass, $dbName);
            if ($db->connect_error) {
                $this->errors[] = 'DB connection failed: ' . $db->connect_error;
                return;
            }
            
            $settingsModel = new \App\Models\SettingsModel($db);
            $this->apiToken = $settingsModel->get('wa_api_token', '');
            $db->close();
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to load settings: ' . $e->getMessage();
        }
    }
    
    /**
     * Send WhatsApp message to a single number
     * 
     * @param string $phone Phone number (08xxx or 628xxx)
     * @param string $message Message text (supports WhatsApp formatting: *bold*, _italic_)
     * @return bool
     */
    public function send($phone, $message)
    {
        if (empty($this->apiToken)) {
            $this->errors[] = 'WhatsApp API token tidak dikonfigurasi';
            return false;
        }
        
        if (empty($phone)) {
            $this->errors[] = 'Nomor telepon kosong';
            return false;
        }
        
        $phone = $this->formatPhone($phone);
        
        $postData = [
            'target' => $phone,
            'message' => $message,
            'countryCode' => '62',
        ];
        
        return $this->doRequest($postData);
    }
    
    /**
     * Send WhatsApp message to multiple numbers
     * 
     * @param array|string $phones Array of phone numbers or comma-separated string
     * @param string $message Message text
     * @return bool
     */
    public function sendBulk($phones, $message)
    {
        if (is_string($phones)) {
            $phones = array_map('trim', explode(',', $phones));
        }
        
        $phones = array_filter($phones);
        if (empty($phones)) {
            $this->errors[] = 'Tidak ada nomor telepon yang valid';
            return false;
        }
        
        // Format all phones
        $formatted = array_map([$this, 'formatPhone'], $phones);
        $target = implode(',', $formatted);
        
        $postData = [
            'target' => $target,
            'message' => $message,
            'countryCode' => '62',
        ];
        
        return $this->doRequest($postData);
    }
    
    /**
     * Format phone number to international format (62xxx)
     */
    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading +
        if (substr($phone, 0, 1) === '+') {
            $phone = substr($phone, 1);
        }
        
        // Convert 08xx to 628xx
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with 62, prepend it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Execute HTTP POST to Fonnte API
     */
    private function doRequest($postData)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => self::API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->apiToken,
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            $this->errors[] = 'cURL Error: ' . $curlError;
            return false;
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode !== 200 || !$result) {
            $this->errors[] = 'API Error (HTTP ' . $httpCode . '): ' . ($response ?: 'No response');
            return false;
        }
        
        if (isset($result['status']) && $result['status'] === false) {
            $this->errors[] = 'Fonnte Error: ' . ($result['reason'] ?? $result['message'] ?? 'Unknown error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Get last errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Clear errors
     */
    public function clearErrors()
    {
        $this->errors = [];
    }
    
    /**
     * Build a formatted notification message for WhatsApp
     * 
     * @param string $title Notification title
     * @param string $message Notification body
     * @param string|null $link Optional link path
     * @return string Formatted WhatsApp message
     */
    public static function buildNotificationMessage($title, $message, $link = null)
    {
        $wa = "📢 *{$title}*\n\n{$message}";
        
        if ($link) {
            // Build full URL if it's a relative path
            $baseUrl = '';
            if (defined('BASE_URL')) {
                $baseUrl = BASE_URL;
            }
            $wa .= "\n\n🔗 " . $baseUrl . $link;
        }
        
        $wa .= "\n\n⏰ " . date('d M Y, H:i');
        $wa .= "\n— _IndoOcean ERP_";
        
        return $wa;
    }
}
