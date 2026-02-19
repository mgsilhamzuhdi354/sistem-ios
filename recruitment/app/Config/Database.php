<?php
/**
 * PT Indo Ocean - RECRUITMENT System
 * Database Configuration (Environment Aware - Docker & Local)
 */

// Helper function to get env var from multiple sources
if (!function_exists('getRecruitmentEnvVar')) {
    function getRecruitmentEnvVar($keys, $default = '') {
        if (!is_array($keys)) $keys = [$keys];
        
        foreach ($keys as $key) {
            if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
            $val = getenv($key);
            if ($val !== false && $val !== '') return $val;
            if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];
        }
        return $default;
    }
}

// Auto-detect: Windows = Laragon, Linux = Docker NAS
$isWindows = (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

if (!$isWindows) {
    // DOCKER / LINUX / NAS - HARDCODED
    $nas_ip             = '172.17.0.3';
    $nas_user           = 'root';
    $nas_password       = 'rahasia123';
    $nas_port           = 3306;
    $nas_db_recruitment = 'recruitment_db';
    $nas_db_erp         = 'erp_db';
} else {
    // WINDOWS / LARAGON (baca dari .env)
    $nas_ip             = getRecruitmentEnvVar(['DB_HOST'], 'localhost');
    $nas_user           = getRecruitmentEnvVar(['DB_USERNAME', 'DB_USER'], 'root');
    $nas_password       = getRecruitmentEnvVar(['DB_PASSWORD', 'DB_PASS'], '');
    $nas_port           = (int) getRecruitmentEnvVar(['DB_PORT'], 3306);
    $nas_db_recruitment = getRecruitmentEnvVar(['DB_DATABASE_RECRUITMENT', 'RECRUITMENT_DB_NAME', 'DB_DATABASE'], 'recruitment_db');
    $nas_db_erp         = getRecruitmentEnvVar(['DB_DATABASE', 'ERP_DB_NAME'], 'erp_db');
}

return [
    // 1. Database Utama Recruitment (default)
    'default' => [
        'hostname' => $nas_ip,
        'username' => $nas_user,
        'password' => $nas_password,
        'database' => $nas_db_recruitment,
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => $nas_port,
    ],
    
    // 2. Database ERP (Supaya Recruitment bisa kirim data crew)
    'erp' => [
        'hostname' => $nas_ip,
        'username' => $nas_user,
        'password' => $nas_password,
        'database' => $nas_db_erp,
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => $nas_port,
    ],
];
