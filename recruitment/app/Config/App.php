<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Application Configuration
 */

return [
    'appName'    => 'PT Indo Ocean Crew Services - Recruitment',
    'appVersion' => '1.0.0',
    'baseURL'    => ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/PT_indoocean/recruitment/public/',
    'indexPage'  => '',
    'timezone'   => 'Asia/Jakarta',
    'language'   => 'id',
    'supportedLanguages' => ['id', 'en'],
    'CSRFProtection' => true,
    'uploadPath' => FCPATH . 'uploads/',
    'maxUploadSize' => 5 * 1024 * 1024, // 5MB
    'allowedFileTypes' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
];
