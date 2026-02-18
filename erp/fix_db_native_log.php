<?php
// Native DB Fixer with File Logging
$logFile = 'db_log.txt';
file_put_contents($logFile, "Starting fixer at " . date('Y-m-d H:i:s') . "\n");

function logMsg($msg) {
    global $logFile;
    file_put_contents($logFile, $msg . "\n", FILE_APPEND);
    echo $msg . "\n";
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'erp_db';

logMsg("Connecting to $dbName at $host...");
$conn = new mysqli($host, $user, $pass, $dbName);

if ($conn->connect_error) {
    logMsg("FATAL: Connection failed: " . $conn->connect_error);
    die();
}
logMsg("Connected successfully.");

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
    logMsg("FATAL: Error getting columns: " . $conn->error);
    die();
}

logMsg("Existing columns: " . implode(', ', $existing));

foreach ($missingCols as $col => $def) {
    if (!in_array($col, $existing)) {
        logMsg("Adding column '$col'...");
        $sql = "ALTER TABLE $table ADD COLUMN $col $def";
        if ($conn->query($sql) === TRUE) {
            logMsg(" - Success adding $col");
        } else {
            logMsg(" - Error adding '$col': " . $conn->error);
        }
    } else {
        logMsg("Column '$col' already exists.");
    }
}

$conn->close();
logMsg("Done.");
