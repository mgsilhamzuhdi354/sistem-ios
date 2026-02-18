<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$db = new mysqli('127.0.0.1', 'root', '', 'absensi_laravel');

$tables = ['daily_attendance_codes', 'laporan_kerjas', 'mapping_shifts', 'lemburs'];

foreach ($tables as $t) {
    echo "\n=== Table: $t ===\n";
    
    // Get count
    $count = $db->query("SELECT COUNT(*) as c FROM `$t`")->fetch_assoc();
    echo "Total records: {$count['c']}\n";
    
    // Get sample
    $result = $db->query("SELECT * FROM `$t` LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "Columns: " . implode(', ', array_keys($row)) . "\n";
    }
}

echo "\n\n=== Checking daily_attendance_codes (likely the attendance table) ===\n";
$result = $db->query("SELECT * FROM daily_attendance_codes ORDER BY tanggal DESC LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}
