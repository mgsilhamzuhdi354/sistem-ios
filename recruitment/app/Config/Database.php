<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
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
    'database' => 'indoocea_recruitment',
];

// Local credentials (XAMPP)
$localCredentials = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'recruitment_db',
];

// Use appropriate credentials
$cred = $isProduction ? $prodCredentials : $localCredentials;

return [
    'default' => [
        'hostname' => $cred['hostname'],
        'username' => $cred['username'],
        'password' => $cred['password'],
        'database' => $cred['database'],
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => !$isProduction,
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_unicode_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ],
];
