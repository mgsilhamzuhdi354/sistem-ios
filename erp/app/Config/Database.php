<?php
/**
 * PT Indo Ocean - ERP System
 * Database Configuration (KHUSUS NAS DOCKER)
 */

$nas_ip = 'localhost';  // <-- Adjusted for Localhost/Laragon
$nas_user = 'root';
$nas_password = '';    // <-- Empty password for default Laragon/XAMPP

return [
    // 1. Database Utama ERP (default)
    'default' => [
        'hostname' => $nas_ip,
        'username' => $nas_user,
        'password' => $nas_password,
        'database' => 'erp_db',      // Sambung ke database erp_db
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

    // 2. Database Recruitment (Supaya ERP bisa baca data pelamar)
    'recruitment' => [
        'hostname' => $nas_ip,
        'username' => $nas_user,
        'password' => $nas_password,
        'database' => 'recruitment_db', // Sambung ke database recruitment_db
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