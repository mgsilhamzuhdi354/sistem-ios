<?php
$db = new mysqli('localhost', 'root', '', 'recruitment_db');
$result = $db->query("SELECT aa.id, aa.application_id, aa.assigned_to, aa.assigned_at, u.full_name 
                      FROM application_assignments aa 
                      LEFT JOIN users u ON aa.assigned_to = u.id 
                      WHERE aa.status = 'active' 
                      ORDER BY aa.assigned_at DESC LIMIT 5");

echo "Recent assignments:\n\n";
while($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']} | App: {$row['application_id']} | To: {$row['full_name']} | At: {$row['assigned_at']}\n";
}

// Delete assignments from last 10 minutes
$cutoff = date('Y-m-d H:i:s', strtotime('-10 minutes'));
$deleteResult = $db->query("DELETE FROM application_assignments WHERE assigned_at > '$cutoff' AND status = 'active'");
echo "\nDeleted {$db->affected_rows} recent assignments (last 10 min)\n";
