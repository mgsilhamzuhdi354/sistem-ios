<?php
$db = new mysqli('127.0.0.1', 'root', '', 'absensi_laravel');
$today = date('Y-m-d');

echo "Checking attendance data for: $today\n\n";

$result = $db->query("SELECT COUNT(*) as c FROM mapping_shifts WHERE tanggal = '$today'");
$count = $result->fetch_assoc();
echo "Records today: {$count['c']}\n";

$result = $db->query("SELECT COUNT(*) as c FROM mapping_shifts");
$total = $result->fetch_assoc();
echo "Total records: {$total['c']}\n\n";

echo "Latest 3 attendance records:\n";
$result = $db->query("SELECT id, user_id, tanggal, jam_absen, jam_pulang, status_absen FROM mapping_shifts ORDER BY tanggal DESC, created_at DESC LIMIT 3");
while ($row = $result->fetch_assoc()) {
    echo "  - Date: {$row['tanggal']}, User: {$row['user_id']}, In: {$row['jam_absen']}, Out: {$row['jam_pulang']}, Status: {$row['status_absen']}\n";
}
