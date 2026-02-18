<?php
$db = new mysqli('localhost', 'root', '', 'recruitment_db');

echo "=== RUNNING ERP TRACKING MIGRATION ===\n\n";

// Add columns
$sql = "
ALTER TABLE applications 
ADD COLUMN sent_to_erp_at TIMESTAMP NULL AFTER updated_at,
ADD COLUMN erp_crew_id INT NULL AFTER sent_to_erp_at
";

if ($db->query($sql)) {
    echo "✅ Columns added successfully\n\n";
} else {
    if (strpos($db->error, "Duplicate column") !== false) {
        echo "ℹ️  Columns already exist\n\n";
    } else {
        echo "❌ Error: " . $db->error . "\n\n";
    }
}

// Add indexes
$sql2 = "ALTER TABLE applications ADD INDEX idx_sent_to_erp (sent_to_erp_at)";
if ($db->query($sql2)) {
    echo "✅ Index idx_sent_to_erp added\n";
} else {
    if (strpos($db->error, "Duplicate key") !== false) {
        echo "ℹ️  Index idx_sent_to_erp already exists\n";
    } else {
        echo "❌ Error: " . $db->error . "\n";
    }
}

$sql3 = "ALTER TABLE applications ADD INDEX idx_erp_crew_id (erp_crew_id)";
if ($db->query($sql3)) {
    echo "✅ Index idx_erp_crew_id added\n";
} else {
    if (strpos($db->error, "Duplicate key") !== false) {
        echo "ℹ️  Index idx_erp_crew_id already exists\n";
    } else {
        echo "❌ Error: " . $db->error . "\n";
    }
}

echo "\n=== APPLICATIONS TABLE STRUCTURE ===\n\n";
$result = $db->query("DESCRIBE applications");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} | {$row['Type']}\n";
}

echo "\n✅ Migration complete!\n";
