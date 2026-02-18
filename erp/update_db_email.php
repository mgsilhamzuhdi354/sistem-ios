<?php
// Script untuk menambah kolom email ke tabel payroll_items
require_once __DIR__ . '/index.php'; // Bootstrap

// Cek apakah kolom sudah ada
$check = $db->query("SHOW COLUMNS FROM payroll_items LIKE 'email_sent_at'");

if (empty($check)) {
    echo "Menambahkan kolom email_sent_at dan email_status...\n";
    $sql = "ALTER TABLE payroll_items 
            ADD COLUMN email_sent_at DATETIME NULL DEFAULT NULL,
            ADD COLUMN email_status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
            ADD COLUMN email_failure_reason TEXT NULL DEFAULT NULL";
    
    if ($db->query($sql)) {
        echo "BERHASIL: Kolom berhasil ditambahkan.\n";
    } else {
        echo "GAGAL: " . $db->error . "\n";
    }
} else {
    echo "INFO: Kolom sudah ada. Tidak ada perubahan.\n";
}
