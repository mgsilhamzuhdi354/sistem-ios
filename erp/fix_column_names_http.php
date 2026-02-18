<?php
// HTTP Fix Column Names (STANDALONE - NO AUTH)
header('Content-Type: text/plain');

// Credentials from .env (Manual copy to avoid loading framework)
$host = 'localhost';
$user = 'root';
$pass = ''; // User's env has empty password
$dbName = 'erp_db';

echo "Connecting to $dbName at $host (Standalone Mode)...\n";
$conn = new mysqli($host, $user, $pass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected successfully.\n";

$table = 'payroll_items';

// 1. Clean Column Names
echo "Cleaning column names...\n";
$result = $conn->query("SHOW COLUMNS FROM $table");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $field = $row['Field'];
        $clean = trim($field);
        $clean = preg_replace('/[\x00-\x1F\x7F]/', '', $clean);
        
        if ($field !== $clean) {
            echo "Found dirty column: '$field' -> '$clean'\n";
            $conn->query("ALTER TABLE $table CHANGE `$field` `$clean` " . $row['Type']);
            echo "Fixed.\n";
        }
    }
}

// 2. Check Key Columns
echo "Checking key columns availability:\n";
$targets = [
    'original_currency' => "VARCHAR(3) NULL DEFAULT 'USD'",
    'original_gross' => "DECIMAL(15,2) NULL DEFAULT 0",
    'exchange_rate' => "DECIMAL(10,6) NULL DEFAULT 1"
];

foreach ($targets as $name => $def) {
    // Try to select the column
    try {
        if ($conn->query("SELECT $name FROM $table LIMIT 1")) {
            echo " - $name [OK]\n";
        } else {
            throw new Exception("Column not found via SELECT");
        }
    } catch (Exception $e) {
        echo " - $name [MISSING] -> Adding...\n";
        if ($conn->query("ALTER TABLE $table ADD COLUMN $name $def") === TRUE) {
            echo "   + Added successfully.\n";
        } else {
            echo "   ! Failed to add: " . $conn->error . "\n";
        }
    }
}

$conn->close();
echo "Done.\n";
