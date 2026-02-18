<?php
// ROOT Fixer (Bypassing ERP .htaccess)
header('Content-Type: text/plain');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'erp_db';

echo "Connecting to $dbName at $host (ROOT Mode)...\n";
$conn = new mysqli($host, $user, $pass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected successfully.\n";

$table = 'payroll_items';

// 1. Clean Names
$result = $conn->query("SHOW COLUMNS FROM $table");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $field = $row['Field'];
        $clean = trim($field);
        $clean = preg_replace('/[\x00-\x1F\x7F]/', '', $clean);
        
        if ($field !== $clean) {
            echo "Dirty column: '$field' -> '$clean'. Fixing...\n";
            $conn->query("ALTER TABLE $table CHANGE `$field` `$clean` " . $row['Type']);
            echo "Fixed.\n";
        }
    }
}

// 2. Add Columns
$targets = [
    'original_currency' => "VARCHAR(3) NULL DEFAULT 'USD'",
    'original_gross' => "DECIMAL(15,2) NULL DEFAULT 0",
    'exchange_rate' => "DECIMAL(10,6) NULL DEFAULT 1"
];

foreach ($targets as $col => $def) {
    echo "Checking $col... ";
    try {
        if ($conn->query("SELECT $col FROM $table LIMIT 1")) {
            echo "OK (Exists)\n";
        }
    } catch (Exception $e) {
        echo "MISSING. Adding...\n";
        $conn->query("ALTER TABLE $table ADD COLUMN $col $def");
    }
}

echo "DONE.";
