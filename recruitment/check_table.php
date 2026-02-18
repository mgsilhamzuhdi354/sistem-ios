<?php
$db = new mysqli('localhost', 'root', '', 'recruitment_db');

echo "=== CHECKING document_types TABLE STRUCTURE ===\n\n";
$r = $db->query("DESCRIBE document_types");
echo "Columns:\n";
while ($col = $r->fetch_assoc()) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}

$db->close();
