<?php
// Fix Payroll Database Schema
require_once __DIR__ . '/index.php';

echo "Checking payroll_items table columns...\n";

$columns = [];
$query = $db->query("SHOW COLUMNS FROM payroll_items");
foreach ($query as $row) {
    $columns[] = $row['Field'];
}

// Columns to check and add
$missingCols = [
    'original_currency' => "VARCHAR(3) NULL DEFAULT 'USD' AFTER currency_code",
    'original_gross' => "DECIMAL(15,2) NULL DEFAULT 0 AFTER original_currency",
    'exchange_rate' => "DECIMAL(10,6) NULL DEFAULT 1 AFTER original_gross"
];

foreach ($missingCols as $col => $def) {
    if (!in_array($col, $columns)) {
        echo "Adding missing column: $col...\n";
        $sql = "ALTER TABLE payroll_items ADD COLUMN $col $def";
        try {
            $db->query($sql);
            echo "Success.\n";
        } catch (\Exception $e) {
            echo "Failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Column $col already exists.\n";
    }
}

echo "Database fix completed.\n";
