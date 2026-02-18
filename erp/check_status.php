<?php
// Check Payroll Periods Status
header('Content-Type: text/plain');

$host = 'localhost';
$user = 'root';
$pass = ''; 
$dbName = 'erp_db';

$conn = new mysqli($host, $user, $pass, $dbName);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT id, period_month, period_year, status FROM payroll_periods");

echo "Payroll Periods:\n";
echo str_pad("ID", 5) . str_pad("Month/Year", 15) . str_pad("Status", 15) . "\n";
echo str_repeat("-", 35) . "\n";

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo str_pad($row['id'], 5) . 
             str_pad($row['period_month'] . '/' . $row['period_year'], 15) . 
             str_pad($row['status'], 15) . "\n";
    }
} else {
    echo "No periods found or error: " . $conn->error;
}
$conn->close();
