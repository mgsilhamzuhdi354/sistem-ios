<?php
$db = new mysqli('localhost', 'root', '', 'recruitment_db');

echo "Adding application_id column to email_logs table...\n";

$result = $db->query("
    ALTER TABLE email_logs 
    ADD COLUMN application_id INT NULL AFTER template_id,
    ADD KEY idx_application_id (application_id)
");

if ($result) {
    echo "✓ Column application_id added successfully\n";
    
    // Show updated structure
    echo "\n=== UPDATED TABLE STRUCTURE ===\n";
    $columns = $db->query("SHOW COLUMNS FROM email_logs");
    while ($col = $columns->fetch_assoc()) {
        echo "{$col['Field']} - {$col['Type']}\n";
    }
} else {
    echo "✗ Error: " . $db->error . "\n";
}

$db->close();
