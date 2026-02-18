<?php
/**
 * Test ERP Integration - simulates the full controller flow
 */
define('FCPATH', __DIR__ . '/public/');
define('APPPATH', __DIR__ . '/app/');

echo "=== Testing ErpSync Library ===\n\n";

// 1. Test loading the class
echo "1. Loading ErpSync class... ";
try {
    require_once APPPATH . 'Libraries/ErpSync.php';
    echo "OK\n";
} catch (\Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Test DB config loading
echo "2. Loading Database config... ";
try {
    $dbConfig = require APPPATH . 'Config/Database.php';
    echo "OK (ERP host={$dbConfig['erp']['hostname']}, db={$dbConfig['erp']['database']})\n";
} catch (\Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Test recruitment DB connection
echo "3. Connecting to Recruitment DB... ";
$recruitDb = new mysqli(
    $dbConfig['default']['hostname'],
    $dbConfig['default']['username'], 
    $dbConfig['default']['password'],
    $dbConfig['default']['database'],
    $dbConfig['default']['port']
);
if ($recruitDb->connect_error) { echo "FAIL: " . $recruitDb->connect_error . "\n"; exit(1); }
echo "OK\n";

// 4. Test ErpSync instantiation (this connects to ERP DB)
echo "4. Creating ErpSync instance... ";
try {
    $erpSync = new ErpSync($recruitDb);
    echo "OK\n";
} catch (\Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// 5. Test getRanks
echo "5. Getting ranks from ERP... ";
try {
    $ranks = $erpSync->getRanks();
    echo "OK (" . count($ranks) . " ranks found)\n";
    foreach ($ranks as $r) {
        echo "   - [{$r['id']}] {$r['name']} ({$r['category']})\n";
    }
} catch (\Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// 6. Test JSON output (simulates what controller returns)
echo "\n6. Simulated JSON response:\n";
$json = json_encode(['success' => true, 'ranks' => $ranks], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo $json . "\n";

echo "\n=== ALL TESTS PASSED ===\n";
