<?php
/**
 * PT Indo Ocean - ERP System
 * Database Configuration
 */

return [
    // ERP Database (primary)
    'default' => [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'erp_db',
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
        'port' => 3306,
    ],
    
    // Recruitment Database (for crew data)
    'recruitment' => [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'recruitment_db',
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
        'port' => 3306,
    ],
];
