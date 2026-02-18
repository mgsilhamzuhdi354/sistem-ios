<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Application Configuration (Laragon)
 */

// Dynamic BASE_URL for multiple environments
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Detect if using Laragon Pretty URL (*.test domains)
$isLaragonPrettyUrl = (strpos($host, '.test') !== false || strpos($host, '.local') !== false);

if ($isLaragonPrettyUrl) {
    // Laragon: domain.test/recruitment/public/
    $baseURL = $protocol . '://' . $host . '/recruitment/public/';
} else {
    // Localhost: localhost/indoocean/recruitment/public/
    $baseURL = $protocol . '://' . $host . '/indoocean/recruitment/public/';
}

return [
    'appName'    => 'PT Indo Ocean Crew Services - Recruitment',
    'appVersion' => '1.0.0',
    'baseURL'    => $baseURL,
    'indexPage'  => '',
    'timezone'   => 'Asia/Jakarta',
    'language'   => 'id',
    'supportedLanguages' => ['id', 'en'],
    'CSRFProtection' => true,
    'uploadPath' => FCPATH . 'uploads/',
    'maxUploadSize' => 5 * 1024 * 1024, // 5MB
    'allowedFileTypes' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
];
