<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Database Configuration
 * 
 * Supports: Docker, XAMPP (local), Domainesia (production)
 */

// Check if running in Docker (environment variables set)
$isDocker = !empty(getenv('DB_HOST')) && getenv('DB_HOST') !== 'localhost';

// Detect production (Domainesia)
$isProduction = (
    isset($_SERVER['HTTP_HOST']) && 
    strpos($_SERVER['HTTP_HOST'], 'localhost') === false &&
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false
);

// Docker credentials (from environment variables)
$dockerCredentials = [
    'hostname' => getenv('DB_HOST') ?: 'mysql',
    'username' => getenv('DB_USER') ?: 'indoocean',
    'password' => getenv('DB_PASS') ?: 'indoocean_secret',
    'database' => getenv('DB_NAME') ?: 'recruitment_db',
    'erp_database' => getenv('ERP_DB_NAME') ?: 'erp_db',
    'port' => 3306,
];

// Production credentials (Domainesia)
$prodCredentials = [
    'hostname' => 'localhost',
    'username' => 'indoocea_deploy',
    'password' => 'Ilhamzuhdi90',
    'database' => 'indoocea_recruitment',
    'erp_database' => 'indoocea_erp',
    'port' => 3306,
];

// Local credentials (XAMPP)
$localCredentials = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'recruitment_db',
    'erp_database' => 'erp_db',
    'port' => 3308,
];

// Select appropriate credentials
if ($isDocker) {
    $cred = $dockerCredentials;
} elseif ($isProduction) {
    $cred = $prodCredentials;
} else {
    $cred = $localCredentials;
}

return [
    // Recruitment Database (default)
    'default' => [
        'hostname' => $cred['hostname'],
        'username' => $cred['username'],
        'password' => $cred['password'],
        'database' => $cred['database'],
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => !$isProduction && !$isDocker,
        'charset' => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port' => $cred['port'],
    ],
    
    // ERP Database (for integration - transfer crew data)
    'erp' => [
        'hostname' => $cred['hostname'],
        'username' => $cred['username'],
        'password' => $cred['password'],
        'database' => $cred['erp_database'],
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => !$isProduction && !$isDocker,
        'charset' => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port' => $cred['port'],
    ],
];

