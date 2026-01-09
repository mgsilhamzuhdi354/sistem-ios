<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Application Configuration
 */

// Detect environment
$isProduction = (
    isset($_SERVER['HTTP_HOST']) && 
    strpos($_SERVER['HTTP_HOST'], 'localhost') === false &&
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false
);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Production: /recruitment/ | Local: /PT_indoocean/recruitment/public/
$basePath = $isProduction ? '/recruitment/' : '/PT_indoocean/recruitment/public/';

return [
    'appName'    => 'PT Indo Ocean Crew Services - Recruitment',
    'appVersion' => '1.0.0',
    'baseURL'    => $protocol . '://' . $host . $basePath,
    'indexPage'  => '',
    'timezone'   => 'Asia/Jakarta',
    'language'   => 'id',
    'supportedLanguages' => ['id', 'en'],
    'CSRFProtection' => true,
    'uploadPath' => FCPATH . 'uploads/',
    'maxUploadSize' => 5 * 1024 * 1024, // 5MB
    'allowedFileTypes' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
];
