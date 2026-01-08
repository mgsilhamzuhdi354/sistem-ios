<?php
/**
 * Debug script to check contract exchange rates
 */
$db = new mysqli('localhost', 'root', '', 'erp_db');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$sql = "SELECT c.crew_name, c.status, cs.total_monthly, cs.exchange_rate, cs.currency_id, cur.code as currency_code
        FROM contracts c 
        LEFT JOIN contract_salaries cs ON c.id = cs.contract_id 
        LEFT JOIN currencies cur ON cs.currency_id = cur.id 
        WHERE c.status IN ('active', 'onboard')
        ORDER BY c.crew_name";

$result = $db->query($sql);

echo "<h2>Active/Onboard Contracts - Exchange Rate Data</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Crew</th><th>Status</th><th>Total Monthly</th><th>Exchange Rate</th><th>Currency</th><th>USD Calc</th></tr>";

$totalUsd = 0;
while ($row = $result->fetch_assoc()) {
    $amount = $row['total_monthly'] ?? 0;
    $rate = $row['exchange_rate'] ?? 0;
    $currency = $row['currency_code'] ?? 'NULL';
    
    // Auto-detect currency like in the system
    if ($currency === 'NULL' || ($currency === 'USD' && $amount > 1000000)) {
        $currency = 'IDR (auto)';
    }
    
    // Calculate USD
    if ($currency === 'USD') {
        $usd = $amount;
    } elseif ($rate > 0) {
        $usd = $amount / $rate;
    } else {
        $usd = $amount * 0.000063;
    }
    $totalUsd += $usd;
    
    echo "<tr>";
    echo "<td>{$row['crew_name']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "<td>" . number_format($amount, 0) . "</td>";
    echo "<td>" . ($rate > 0 ? number_format($rate, 2) : 'NULL (default 0.000063)') . "</td>";
    echo "<td>{$currency}</td>";
    echo "<td>$" . number_format($usd, 2) . "</td>";
    echo "</tr>";
}
echo "<tr style='background:#ffd700'><td colspan='5'><strong>TOTAL USD</strong></td><td><strong>$" . number_format($totalUsd, 2) . "</strong></td></tr>";
echo "</table>";

// Check exchange_rates table
echo "<h2>Exchange Rates Table</h2>";
$ratesResult = $db->query("SELECT * FROM exchange_rates ORDER BY effective_date DESC LIMIT 10");
if ($ratesResult && $ratesResult->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Currency</th><th>Rate to USD</th><th>Effective Date</th></tr>";
    while ($rate = $ratesResult->fetch_assoc()) {
        echo "<tr><td>{$rate['currency_code']}</td><td>{$rate['rate_to_usd']}</td><td>{$rate['effective_date']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p><strong>No exchange rates in database - using default rate 0.000063 for IDR</strong></p>";
}

$db->close();
