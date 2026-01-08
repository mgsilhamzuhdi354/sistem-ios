<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Database Configuration
 * 
 * PRODUCTION VERSION - Uses Domainesia credentials
 */

// FORCE PRODUCTION CREDENTIALS
// Untuk development lokal, ganti username/password ke root/'' sementara

return [
    'default' => [
        'hostname' => 'localhost',
        'username' => 'indoocea_deploy',
        'password' => 'Ilhamzuhdi90',
        'database' => 'indoocea_recruitment',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => false,
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
