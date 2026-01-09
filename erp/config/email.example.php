<?php
/**
 * Email Configuration for PT Indo Ocean ERP
 * 
 * CARA PENGGUNAAN:
 * 1. Copy file ini ke: config/email.php
 * 2. Ubah nilai-nilai di bawah sesuai dengan akun email Anda
 * 3. Untuk Gmail: Gunakan App Password (bukan password biasa)
 *    - Buka https://myaccount.google.com/apppasswords
 *    - Buat App Password baru untuk "Mail" + "Windows Computer"
 *    - Gunakan 16-character password yang dihasilkan
 */

return [
    // =====================================================
    // SMTP SERVER SETTINGS
    // =====================================================
    
    // Untuk Gmail:
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls', // tls atau ssl
    
    // Untuk Outlook/Hotmail:
    // 'smtp_host' => 'smtp-mail.outlook.com',
    // 'smtp_port' => 587,
    
    // Untuk Yahoo:
    // 'smtp_host' => 'smtp.mail.yahoo.com', 
    // 'smtp_port' => 465,
    // 'smtp_secure' => 'ssl',
    
    // =====================================================
    // CREDENTIALS - GANTI DENGAN DATA ANDA
    // =====================================================
    
    'smtp_user' => 'your-email@gmail.com',      // <<< GANTI dengan email Anda
    'smtp_pass' => 'your-app-password-here',    // <<< GANTI dengan App Password
    
    // =====================================================
    // SENDER INFORMATION
    // =====================================================
    
    'from_email' => 'noreply@ptindoocean.com',  // Email pengirim yang ditampilkan
    'from_name'  => 'PT Indo Ocean ERP',         // Nama pengirim yang ditampilkan
    
    // =====================================================
    // ADDITIONAL SETTINGS
    // =====================================================
    
    'debug' => false,  // Set true untuk melihat debug log saat development
];
