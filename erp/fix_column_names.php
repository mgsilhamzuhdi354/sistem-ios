<?php
// Fix Column Names (Trim spaces/hidden chars)
require_once __DIR__ . '/index.php';

echo "Cleaning column names...\n";

$table = 'payroll_items';
$columns = $db->query("SHOW COLUMNS FROM $table");

foreach ($columns as $col) {
    $field = $col['Field'];
    $clean = trim($field); // Remove spaces
    $clean = preg_replace('/[\x00-\x1F\x7F]/', '', $clean); // Remove control chars
    
    if ($field !== $clean) {
        echo "Found dirty column: '$field' -> '$clean'\n";
        $db->query("ALTER TABLE $table CHANGE `$field` `$clean` " . $col['Type']);
        echo "Fixed.\n";
    }
}

// Force re-add columns if missing (Brute force fix)
$targets = [
    'original_currency' => "VARCHAR(3) NULL DEFAULT 'USD'",
    'original_gross' => "DECIMAL(15,2) NULL DEFAULT 0",
    'exchange_rate' => "DECIMAL(10,6) NULL DEFAULT 1"
];

foreach ($targets as $name => $def) {
    // Try to select the column
    try {
        $db->query("SELECT $name FROM $table LIMIT 1");
        echo "Column $name is accessible.\n";
    } catch (Exception $e) {
        echo "Column $name NOT accessible. Adding...\n";
        try {
            $db->query("ALTER TABLE $table ADD COLUMN $name $def");
            echo "Added $name.\n";
        } catch (Exception $e2) {
            echo "Error adding $name: " . $e2->getMessage() . "\n";
        }
    }
}

echo "Done.\n";
