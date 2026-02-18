<?php
$db = new mysqli('localhost', 'root', '', 'recruitment_db');

echo "=== EMAIL_LOGS TABLE STRUCTURE ===\n";
$columns = $db->query("SHOW COLUMNS FROM email_logs");
while ($col = $columns->fetch_assoc()) {
    echo "{$col['Field']} - {$col['Type']}\n";
}

echo "\n=== LATEST EMAIL LOGS (3 records) ===\n";
$logs = $db->query("SELECT * FROM email_logs ORDER BY id DESC LIMIT 3");
while ($log = $logs->fetch_assoc()) {
    echo "ID: {$log['id']}\n";
    echo "Recipient: {$log['recipient_email']}\n";
    echo "Status: {$log['status']}\n";
    echo "Application ID: " . ($log['application_id'] ?? 'COLUMN NOT EXISTS') . "\n";
    echo "Created: {$log['created_at']}\n";
    echo "---\n";
}

$db->close();
