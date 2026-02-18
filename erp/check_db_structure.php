<?php
// Script Diagnosa Database
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/index.php'; // Bootstrap

echo "=== DIAGNOSA STRUKTUR TABEL PAYROLL_ITEMS ===\n";

try {
    // 1. Tampilkan semua kolom saat ini
    $columns = $db->query("SHOW COLUMNS FROM payroll_items");
    $existingCols = [];
    
    echo "Kolom yang ada saat ini:\n";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
        $existingCols[] = $col['Field'];
    }
    
    echo "\n=== PENGECEKAN KOLOM ===\n";
    $targets = ['original_currency', 'original_gross', 'exchange_rate'];
    
    foreach ($targets as $target) {
        if (in_array($target, $existingCols)) {
            echo "[OK] Kolom '$target' SUDAH ADA.\n";
        } else {
            echo "[MISSING] Kolom '$target' TIDAK DITEMUKAN. Mencoba menambahkan...\n";
            
            $sql = "";
            if ($target == 'original_currency') {
                $sql = "ALTER TABLE payroll_items ADD COLUMN original_currency VARCHAR(3) NULL DEFAULT 'USD'";
            } elseif ($target == 'original_gross') {
                $sql = "ALTER TABLE payroll_items ADD COLUMN original_gross DECIMAL(15,2) NULL DEFAULT 0";
            } elseif ($target == 'exchange_rate') {
                $sql = "ALTER TABLE payroll_items ADD COLUMN exchange_rate DECIMAL(10,6) NULL DEFAULT 1";
            }
            
            if ($db->query($sql)) {
                echo "   -> SUKSES menambahkan kolom '$target'.\n";
            } else {
                echo "   -> GAGAL menambahkan kolom '$target': " . $db->error . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== SELESAI ===\n";
