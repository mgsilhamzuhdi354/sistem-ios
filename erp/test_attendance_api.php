<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require_once __DIR__ . '/app/Libraries/HrisApi.php';

$hrisApi = new HrisApi();

// Test get attendance with today's date
$today = date('Y-m-d');
echo "Testing attendance fetch for: $today\n\n";

$result = $hrisApi->getAttendance([
    'start_date' => $today,
    'end_date' => $today
]);

echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
echo "Data count: " . count($result['data'] ?? []) . "\n";

if (!empty($result['error'])) {
    echo "Error: {$result['error']}\n";
}

if (!empty($result['data'])) {
    echo "\nSample data (first record):\n";
    echo json_encode($result['data'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
