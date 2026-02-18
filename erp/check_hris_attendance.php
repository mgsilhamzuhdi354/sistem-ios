<?php
require __DIR__ . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Database connection
$host = $_ENV['HRIS_DB_HOST'] ?? '127.0.0.1';
$username = $_ENV['HRIS_DB_USERNAME'] ?? 'root';
$password = $_ENV['HRIS_DB_PASSWORD'] ?? '';
$database = $_ENV['HRIS_DB_DATABASE'] ?? 'absensi_laravel';

echo "Connecting to: $database @ $host\n";
$db = new mysqli($host, $username, $password, $database);

if ($db->connect_error) {
    die("Connection Error: " . $db->connect_error . "\n");
}

echo "✓ Connected successfully!\n\n";

// Show tables
echo "Tables in database:\n";
$result = $db->query('SHOW TABLES');
$tables = [];
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
    echo "  - {$row[0]}\n";
}

// Check for attendance-related tables
echo "\n";
$attendanceTables = ['absensi', 'absensis', 'attendance', 'attendances', 'presensi'];
foreach ($attendanceTables as $table) {
    if (in_array($table, $tables)) {
        echo "✓ Found attendance table: $table\n";
        $count = $db->query("SELECT COUNT(*) as c FROM `$table`")->fetch_assoc();
        echo "  Records: {$count['c']}\n";
        
        // Sample data
        $sample = $db->query("SELECT * FROM `$table` ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
        if ($sample) {
            echo "  Sample columns: " . implode(', ', array_keys($sample)) . "\n";
        }
    }
}

$db->close();
