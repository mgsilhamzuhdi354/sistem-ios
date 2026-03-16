<?php
/**
 * PT Indo Ocean - ERP System
 * Database Configuration (Environment Aware - Docker & Local)
 * Supports: Docker ENV, .env file (via dotenv), or Laragon defaults
 */

// Helper function to get env var from multiple sources
if (!function_exists('getEnvVar')) {
    function getEnvVar($keys, $default = '') {
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

// =====================================================
// AUTO-DETECT: Docker (Linux) vs Laragon (Windows)
// =====================================================
$isWindows = (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

if (!$isWindows) {
    // =================================================
    // DOCKER / LINUX / NAS UGREEN
    // =================================================
    $nas_ip             = getEnvVar(['DB_HOST'], 'mariadb-1');
    $nas_user           = getEnvVar(['DB_USER', 'DB_USERNAME'], 'root');
    $nas_password       = getEnvVar(['DB_PASS', 'DB_PASSWORD'], 'rahasia123');
    $nas_port           = (int) getEnvVar(['DB_PORT'], 3306);
    $nas_db_default     = getEnvVar(['ERP_DB_NAME', 'DB_DATABASE'], 'erp_db');
    $nas_db_recruitment = getEnvVar(['RECRUITMENT_DB_NAME', 'DB_DATABASE_RECRUITMENT'], 'recruitment_db');
} else {
    // =================================================
    // WINDOWS / LARAGON / XAMPP
    // =================================================
    $nas_ip             = getEnvVar(['DB_HOST'], 'localhost');
    $nas_user           = getEnvVar(['DB_USERNAME', 'DB_USER'], 'root');
    $nas_password       = getEnvVar(['DB_PASSWORD', 'DB_PASS'], '');
    $nas_port           = (int) getEnvVar(['DB_PORT'], 3306);
    $nas_db_default     = getEnvVar(['DB_DATABASE', 'ERP_DB_NAME'], 'erp_db');
    $nas_db_recruitment = getEnvVar(['DB_DATABASE_RECRUITMENT', 'RECRUITMENT_DB_NAME'], 'recruitment_db');
}

return [
    // 1. Database Utama ERP (default)
    'default' => [
        'hostname' => $nas_ip,
        'username' => $nas_user,
        'password' => $nas_password,
        'database' => $nas_db_default,
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => true,
        'charset' => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port' => $nas_port,
    ],

    // 2. Database Recruitment (Supaya ERP bisa baca data pelamar)
    'recruitment' => [
        'hostname' => $nas_ip,
        'username' => $nas_user,
        'password' => $nas_password,
        'database' => $nas_db_recruitment,
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => true,
        'charset' => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port' => $nas_port,
    ],
];