<?php
$db = new mysqli('localhost', 'root', '', 'recruitment_db');
echo "=== Checking tables ===\n";
$r = $db->query("SHOW TABLES LIKE '%document%'");
while ($row = $r->fetch_array()) {
    echo "Table: " . $row[0] . "\n";
}

echo "\n=== Checking if documents table exists ===\n";
$r = $db->query("SHOW TABLES LIKE 'documents'");
if ($r->num_rows > 0) {
    echo "documents table EXISTS\n";
    $r = $db->query("DESCRIBE documents");
    while ($row = $r->fetch_assoc()) {
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }
    $count = $db->query("SELECT COUNT(*) as cnt FROM documents")->fetch_assoc();
    echo "  Total documents: {$count['cnt']}\n";
} else {
    echo "documents table DOES NOT EXIST!\n";
}

echo "\n=== Checking form action route ===\n";
echo "Looking for /crewing/manual-entry/submit route...\n";
$db->close();
