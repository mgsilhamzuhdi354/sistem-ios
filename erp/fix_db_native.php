<?php
// Native DB Fixer
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'erp_db';

echo "Connecting to $dbName at $host...\n";
$conn = new mysqli($host, $user, $pass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Connected successfully.\n";

$table = 'payroll_items';
$missingCols = [
    'original_currency' => "VARCHAR(3) NULL DEFAULT 'USD' AFTER currency_code",
    'original_gross' => "DECIMAL(15,2) NULL DEFAULT 0 AFTER original_currency",
    'exchange_rate' => "DECIMAL(10,6) NULL DEFAULT 1 AFTER original_gross"
];

// Get existing columns
$result = $conn->query("SHOW COLUMNS FROM $table");
$existing = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $existing[] = $row['Field'];
    }
} else {
    die("Error getting columns: " . $conn->error . "\n");
}

echo "Existing columns: " . implode(', ', $existing) . "\n";

foreach ($missingCols as $col => $def) {
    if (!in_array($col, $existing)) {
        echo "Adding column '$col'...\n";
        $sql = "ALTER TABLE $table ADD COLUMN $col $def";
        if ($conn->query($sql) === TRUE) {
            echo " - Success\n";
        } else {
            echo " - Error adding '$col': " . $conn->error . "\n";
        }
    } else {
        echo "Column '$col' already exists.\n";
    }
}

$conn->close();
echo "Done.\n";
