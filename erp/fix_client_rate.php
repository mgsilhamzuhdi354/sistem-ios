<?php
/**
 * One-time fix: Reset invalid client_rate for contract CTR-2026-0003
 * Run once then delete this file.
 */
require_once __DIR__ . '/app/Config/App.php';
require_once __DIR__ . '/app/Config/Database.php';

$db = getDBConnection();

// Show current data
$result = $db->query("
    SELECT cs.id, cs.contract_id, cs.total_monthly, cs.client_rate, c.contract_no, c.crew_name 
    FROM contract_salaries cs 
    JOIN contracts c ON cs.contract_id = c.id 
    WHERE c.contract_no = 'CTR-2026-0003'
");

echo "<h3>Before Fix:</h3><pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";

// Fix: set client_rate to NULL (no client rate set)
$stmt = $db->prepare("
    UPDATE contract_salaries cs
    JOIN contracts c ON cs.contract_id = c.id
    SET cs.client_rate = NULL
    WHERE c.contract_no = 'CTR-2026-0003'
");
$stmt->execute();

echo "<p><b>Fixed!</b> Rows affected: " . $stmt->affected_rows . "</p>";

// Verify
$result2 = $db->query("
    SELECT cs.id, cs.contract_id, cs.total_monthly, cs.client_rate, c.contract_no, c.crew_name 
    FROM contract_salaries cs 
    JOIN contracts c ON cs.contract_id = c.id 
    WHERE c.contract_no = 'CTR-2026-0003'
");

echo "<h3>After Fix:</h3><pre>";
while ($row = $result2->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";
echo "<p style='color:green;'><b>Done! Hapus file ini setelah selesai.</b></p>";
