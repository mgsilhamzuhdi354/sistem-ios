<?php
/**
 * PT Indo Ocean - ERP System
 * Database Configuration
 * 
 * Automatically detects environment (local vs production)
 */

// Detect environment
$isProduction = (
    isset($_SERVER['HTTP_HOST']) && 
    strpos($_SERVER['HTTP_HOST'], 'localhost') === false &&
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false
);

// Production credentials (Domainesia)
$prodCredentials = [
    'hostname' => 'localhost',
    'username' => 'indoocea_deploy',
    'password' => 'Ilhamzuhdi90',
    'erp_database' => 'indoocea_erp',
    'recruitment_database' => 'indoocea_recruitment',
];

// Local credentials (XAMPP)
$localCredentials = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'erp_database' => 'erp_db',
    'recruitment_database' => 'recruitment_db',
];

// Use appropriate credentials
$cred = $isProduction ? $prodCredentials : $localCredentials;

return [
    // ERP Database (primary)
    'default' => [
        'hostname' => $cred['hostname'],
        'username' => $cred['username'],
        'password' => $cred['password'],
        'database' => $cred['erp_database'],
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => !$isProduction,
        'charset' => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port' => 3306,
    ],
    
    // Recruitment Database (for crew data)
    'recruitment' => [
        'hostname' => $cred['hostname'],
        'username' => $cred['username'],
        'password' => $cred['password'],
        'database' => $cred['recruitment_database'],
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => !$isProduction,
        'charset' => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port' => 3306,
    ],
];
