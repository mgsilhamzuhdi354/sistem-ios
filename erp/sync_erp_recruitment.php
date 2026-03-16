<?php
/**
 * Sync ERP Admin Checklist status → Recruitment Pipeline
 * - Akbar: 6/6 checklist → status should be APPROVED
 * - Budi: ensure visible in pipeline, restore if archived
 * - All crews: sync checklist status to recruitment status
 */

echo "=== SYNC ERP → RECRUITMENT ===\n\n";

// Connect to both databases
$recruitDb = new mysqli('localhost', 'root', '', 'recruitment_db');
if ($recruitDb->connect_error) die("Cannot connect recruitment_db: " . $recruitDb->connect_error . "\n");

$erpDb = new mysqli('localhost', 'root', '', 'erp_db');
if ($erpDb->connect_error) die("Cannot connect erp_db: " . $erpDb->connect_error . "\n");

// 1. Show current state
echo "--- CURRENT STATE ---\n";
$r = $recruitDb->query("SELECT a.id, u.full_name, a.status_id, s.name as status_name, a.is_archived, a.erp_crew_id FROM applications a JOIN users u ON a.user_id = u.id LEFT JOIN application_statuses s ON a.status_id = s.id ORDER BY a.id");
while ($row = $r->fetch_assoc()) {
    echo "  AppID:{$row['id']} {$row['full_name']} status:{$row['status_id']}({$row['status_name']}) archived:{$row['is_archived']} erp_crew_id:{$row['erp_crew_id']}\n";
}

echo "\n--- ERP CREWS ---\n";
$r = $erpDb->query("SELECT c.id, c.full_name, c.status, c.email, ac.status as cl_status, 
    (CASE WHEN ac.document_check=1 THEN 1 ELSE 0 END + CASE WHEN ac.owner_interview=1 THEN 1 ELSE 0 END + CASE WHEN ac.pengantar_mcu=1 THEN 1 ELSE 0 END + CASE WHEN ac.agreement_kontrak=1 THEN 1 ELSE 0 END + CASE WHEN ac.admin_charge=1 THEN 1 ELSE 0 END + CASE WHEN ac.ok_to_board=1 THEN 1 ELSE 0 END) as passed
    FROM crews c LEFT JOIN admin_checklists ac ON ac.crew_id = c.id ORDER BY c.id");
while ($row = $r->fetch_assoc()) {
    echo "  CrewID:{$row['id']} {$row['full_name']} ({$row['email']}) crew_status:{$row['status']} checklist:{$row['cl_status']} passed:{$row['passed']}/6\n";
}

// 2. Ensure statuses 9,10,11 exist in recruitment DB
echo "\n--- ENSURE ERP STATUSES EXIST ---\n";
$statuses = [
    [9, 'Admin Review', '#6366f1', 9],
    [10, 'Processing', '#f59e0b', 10],
    [11, 'On Board', '#059669', 11],
];
foreach ($statuses as $s) {
    $check = $recruitDb->query("SELECT id FROM application_statuses WHERE id = {$s[0]}");
    if ($check && $check->num_rows == 0) {
        $stmt = $recruitDb->prepare("INSERT INTO application_statuses (id, name, color, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('issi', $s[0], $s[1], $s[2], $s[3]);
        $stmt->execute();
        echo "  Created status: {$s[1]} (ID:{$s[0]})\n";
    } else {
        echo "  Status {$s[1]} (ID:{$s[0]}) already exists\n";
    }
}

// 3. For each ERP crew with a checklist, sync status to recruitment
echo "\n--- SYNCING ---\n";
$r = $erpDb->query("SELECT c.id as crew_id, c.full_name, c.email, c.status as crew_status, 
    ac.status as cl_status,
    (CASE WHEN ac.document_check=1 THEN 1 ELSE 0 END + CASE WHEN ac.owner_interview=1 THEN 1 ELSE 0 END + CASE WHEN ac.pengantar_mcu=1 THEN 1 ELSE 0 END + CASE WHEN ac.agreement_kontrak=1 THEN 1 ELSE 0 END + CASE WHEN ac.admin_charge=1 THEN 1 ELSE 0 END + CASE WHEN ac.ok_to_board=1 THEN 1 ELSE 0 END) as passed
    FROM crews c JOIN admin_checklists ac ON ac.crew_id = c.id ORDER BY c.id");

while ($crew = $r->fetch_assoc()) {
    $crewId = $crew['crew_id'];
    $passed = intval($crew['passed']);
    $clStatus = $crew['cl_status'];
    
    // Determine correct recruitment status
    if ($clStatus === 'completed' || $passed == 6) {
        $newStatusId = 6; // Approved
        $statusName = 'Approved';
    } elseif ($clStatus === 'rejected') {
        $newStatusId = 7; // Rejected
        $statusName = 'Rejected';
    } elseif ($passed > 0) {
        $newStatusId = 10; // Processing
        $statusName = 'Processing';
    } else {
        $newStatusId = 9; // Admin Review
        $statusName = 'Admin Review';
    }
    
    // Find application by erp_crew_id
    $appResult = $recruitDb->query("SELECT id, status_id, is_archived FROM applications WHERE erp_crew_id = {$crewId}");
    if ($appResult && $appResult->num_rows > 0) {
        $app = $appResult->fetch_assoc();
        $appId = $app['id'];
        $oldStatus = $app['status_id'];
        $isArchived = $app['is_archived'];
        
        // Update status
        $stmt = $recruitDb->prepare("UPDATE applications SET status_id = ?, is_archived = 0, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('ii', $newStatusId, $appId);
        $stmt->execute();
        
        echo "  {$crew['full_name']} (Crew#{$crewId} → App#{$appId}): status {$oldStatus}→{$newStatusId}({$statusName}), archived:{$isArchived}→0, passed:{$passed}/6\n";
    } else {
        echo "  {$crew['full_name']} (Crew#{$crewId}): NO APPLICATION LINKED (erp_crew_id not found)\n";
        
        // Try to find by email
        $emailResult = $recruitDb->query("SELECT a.id, u.full_name FROM applications a JOIN users u ON a.user_id = u.id WHERE u.email = '" . $recruitDb->real_escape_string($crew['email']) . "' LIMIT 1");
        if ($emailResult && $emailResult->num_rows > 0) {
            $match = $emailResult->fetch_assoc();
            echo "    → Found by email! App#{$match['id']} ({$match['full_name']}). Linking...\n";
            $stmt = $recruitDb->prepare("UPDATE applications SET erp_crew_id = ?, status_id = ?, is_archived = 0, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param('iii', $crewId, $newStatusId, $match['id']);
            $stmt->execute();
            echo "    → Linked and synced: status→{$newStatusId}({$statusName})\n";
        }
    }
}

// 4. Unarchive ALL applications that have an active ERP crew
echo "\n--- UNARCHIVE ERP APPLICATIONS ---\n";
$recruitDb->query("UPDATE applications SET is_archived = 0 WHERE erp_crew_id IS NOT NULL AND erp_crew_id > 0 AND is_archived = 1");
echo "  Unarchived " . $recruitDb->affected_rows . " applications\n";

// 5. Final state
echo "\n--- FINAL STATE ---\n";
$r = $recruitDb->query("SELECT a.id, u.full_name, a.status_id, s.name as status_name, a.is_archived, a.erp_crew_id FROM applications a JOIN users u ON a.user_id = u.id LEFT JOIN application_statuses s ON a.status_id = s.id ORDER BY a.id");
while ($row = $r->fetch_assoc()) {
    echo "  AppID:{$row['id']} {$row['full_name']} status:{$row['status_id']}({$row['status_name']}) archived:{$row['is_archived']} erp_crew_id:{$row['erp_crew_id']}\n";
}

$recruitDb->close();
$erpDb->close();
echo "\n=== SYNC COMPLETE ===\n";
?>
