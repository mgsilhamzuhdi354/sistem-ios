<?php
// Debug script - check recruitment DB state
$db = new mysqli('localhost', 'root', '', 'recruitment_db');
if ($db->connect_error) die("Cannot connect to recruitment_db: " . $db->connect_error . "\n");

echo "=== APPLICATION STATUSES ===\n";
$r = $db->query("SELECT id, name FROM application_statuses ORDER BY id");
while ($row = $r->fetch_assoc()) echo "ID:{$row['id']} name:{$row['name']}\n";

echo "\n=== ALL APPLICATIONS (no filter) ===\n";
$r = $db->query("SELECT a.id, u.full_name, a.status_id, a.is_archived, a.erp_crew_id FROM applications a JOIN users u ON a.user_id = u.id ORDER BY a.id");
while ($row = $r->fetch_assoc()) {
    echo "AppID:{$row['id']} name:{$row['full_name']} status_id:{$row['status_id']} archived:{$row['is_archived']} erp_crew_id:{$row['erp_crew_id']}\n";
}
$db->close();

echo "\n=== ERP DB - crews & checklists ===\n";
$erpDb = new mysqli('localhost', 'root', '', 'erp_db');
if ($erpDb->connect_error) { echo "Cannot connect to erp_db: " . $erpDb->connect_error . "\n"; exit; }

$r = $erpDb->query("SELECT c.id, c.full_name, c.status, ac.status as checklist_status, ac.document_check+ac.owner_interview+ac.pengantar_mcu+ac.agreement_kontrak+ac.admin_charge+ac.ok_to_board as items_passed FROM crews c LEFT JOIN admin_checklists ac ON ac.crew_id = c.id ORDER BY c.id");
while ($row = $r->fetch_assoc()) {
    echo "CrewID:{$row['id']} name:{$row['full_name']} crew_status:{$row['status']} checklist:{$row['checklist_status']} passed:{$row['items_passed']}/6\n";
}
$erpDb->close();
?>
