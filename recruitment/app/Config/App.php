<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Application Configuration (Laragon)
 */

// Dynamic BASE_URL for multiple environments
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Detect environment: Linux/Docker = production, Windows = local
$isWindows = (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
$isLaragonPrettyUrl = (strpos($host, '.test') !== false || strpos($host, '.local') !== false);

if (!$isWindows || $isLaragonPrettyUrl) {
    // Production/Docker OR Laragon .test: direct domain
    $baseURL = $protocol . '://' . $host . '/recruitment/public/';
} else {
    // Windows localhost only
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
