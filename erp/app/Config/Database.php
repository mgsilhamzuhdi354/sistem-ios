<?php
/**
 * PT Indo Ocean - ERP System
 * Database Configuration
 * 
 * PRODUCTION VERSION - Uses Domainesia credentials
 */

// FORCE PRODUCTION CREDENTIALS
// Untuk development lokal, ganti username/password ke root/'' sementara

return [
    // ERP Database (primary)
    'default' => [
        'hostname' => 'localhost',
        'username' => 'indoocea_deploy',
        'password' => 'Ilhamzuhdi90',
        'database' => 'indoocea_erp',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => false,
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
        'hostname' => 'localhost',
        'username' => 'indoocea_deploy',
        'password' => 'Ilhamzuhdi90',
        'database' => 'indoocea_recruitment',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => false,
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
