<?php
/**
 * ONE-TIME SYNC & CLEANUP SCRIPT
 * Run on production: php sync_crew_data.php
 * 
 * This script:
 * 1. Syncs all approved applicants from recruitment to ERP crews table
 * 2. Fixes orphaned contracts (contracts with crew_id that doesn't exist)
 * 3. Shows diagnostic report
 * 
 * ⚠️ DELETE THIS FILE AFTER RUNNING
 */

// Load ERP database config  
$dbConfig = require __DIR__ . '/app/Config/Database.php';
$erpCfg = $dbConfig['default'];
$recCfg = $dbConfig['recruitment'];

// Connect to ERP database
$erpDb = new mysqli($erpCfg['hostname'], $erpCfg['username'], $erpCfg['password'], $erpCfg['database'], $erpCfg['port'] ?? 3306);
if ($erpDb->connect_error) die("ERP DB connection failed: " . $erpDb->connect_error . "\n");

// Connect to Recruitment database
$recDb = new mysqli($recCfg['hostname'], $recCfg['username'], $recCfg['password'], $recCfg['database'], $recCfg['port'] ?? 3306);
if ($recDb->connect_error) die("Recruitment DB connection failed: " . $recDb->connect_error . "\n");

echo "=== CREW DATA SYNC & CLEANUP SCRIPT ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// =====================================================
// PHASE 1: DIAGNOSTIC - Show current state
// =====================================================
echo "--- PHASE 1: DIAGNOSTIC ---\n";

$crewCount = $erpDb->query("SELECT COUNT(*) as cnt FROM crews")->fetch_assoc()['cnt'];
$contractCount = $erpDb->query("SELECT COUNT(*) as cnt FROM contracts")->fetch_assoc()['cnt'];
echo "ERP Crews:     $crewCount\n";
echo "ERP Contracts: $contractCount\n";

// Orphaned contracts
$orphans = $erpDb->query("
    SELECT c.id, c.contract_no, c.crew_id, c.crew_name, c.status
    FROM contracts c
    LEFT JOIN crews cr ON c.crew_id = cr.id
    WHERE cr.id IS NULL
");
echo "Orphaned contracts (no matching crew): " . $orphans->num_rows . "\n";

// Approved applicants in recruitment
$approvedApplicants = $recDb->query("
    SELECT a.id, a.user_id, a.status_id, a.sent_to_erp_at,
           u.full_name, u.email, u.phone,
           ap.gender, ap.date_of_birth, ap.place_of_birth,
           ap.nationality, ap.address as profile_address,
           ap.city, ap.postal_code,
           ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
           ap.total_sea_service_months, ap.profile_photo
    FROM applications a
    JOIN users u ON a.user_id = u.id
    LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
    WHERE a.status_id = 6
");
echo "Recruitment approved applicants (status_id=6): " . $approvedApplicants->num_rows . "\n";

// Count how many haven't been sent to ERP
$notSent = $recDb->query("
    SELECT COUNT(*) as cnt FROM applications 
    WHERE status_id = 6 AND (sent_to_erp_at IS NULL OR sent_to_erp_at = '')
")->fetch_assoc()['cnt'];
echo "Not yet sent to ERP: $notSent\n\n";


// =====================================================
// PHASE 2: SYNC - Push all approved applicants to ERP
// =====================================================
echo "--- PHASE 2: SYNC APPROVED APPLICANTS TO ERP ---\n";

$synced = 0;
$skipped = 0;
$errors = 0;

// Reset the result pointer
$approvedApplicants->data_seek(0);

while ($app = $approvedApplicants->fetch_assoc()) {
    $userId = $app['user_id'];
    $fullName = $app['full_name'];
    
    // Check if this candidate already exists in ERP crews table
    $existCheck = $erpDb->prepare("SELECT id FROM crews WHERE candidate_id = ? LIMIT 1");
    $existCheck->bind_param('i', $userId);
    $existCheck->execute();
    $existResult = $existCheck->get_result();
    
    if ($existResult->num_rows > 0) {
        $skipped++;
        continue; // Already in ERP
    }
    
    // Also check by name + email (in case candidate_id wasn't set)
    $nameCheck = $erpDb->prepare("SELECT id FROM crews WHERE full_name = ? AND email = ? LIMIT 1");
    $email = $app['email'] ?? '';
    $nameCheck->bind_param('ss', $fullName, $email);
    $nameCheck->execute();
    if ($nameCheck->get_result()->num_rows > 0) {
        $skipped++;
        continue; // Already in ERP by name+email
    }
    
    // Create crew record in ERP
    $employeeId = 'IO' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // Map gender
    $rawGender = strtolower(trim($app['gender'] ?? ''));
    if (in_array($rawGender, ['male', 'laki-laki', 'l', 'm'])) {
        $gender = 'male';
    } elseif (in_array($rawGender, ['female', 'perempuan', 'p', 'f', 'w'])) {
        $gender = 'female';
    } else {
        $gender = 'male';
    }
    
    $birthDate = $app['date_of_birth'] ?? null;
    $birthPlace = $app['place_of_birth'] ?? '';
    $nationality = $app['nationality'] ?? 'Indonesian';
    $address = $app['profile_address'] ?? '';
    $city = $app['city'] ?? '';
    $postalCode = $app['postal_code'] ?? '';
    $phone = $app['phone'] ?? '';
    $emergencyName = $app['emergency_name'] ?? '';
    $emergencyPhone = $app['emergency_phone'] ?? '';
    $emergencyRelation = $app['emergency_relation'] ?? '';
    $totalSeaTime = intval($app['total_sea_service_months'] ?? 0);
    $status = 'available';
    $source = 'recruitment';
    $notes = 'Synced from recruitment system';
    
    $stmt = $erpDb->prepare("
        INSERT INTO crews (
            employee_id, full_name, email, phone,
            gender, birth_date, birth_place, nationality,
            address, city, postal_code,
            emergency_name, emergency_relation, emergency_phone,
            total_sea_time_months,
            status, source, candidate_id,
            notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param(
        'sssssssssssssssisss',
        $employeeId, $fullName, $email, $phone,
        $gender, $birthDate, $birthPlace, $nationality,
        $address, $city, $postalCode,
        $emergencyName, $emergencyRelation, $emergencyPhone,
        $totalSeaTime,
        $status, $source, $userId,
        $notes
    );
    
    if ($stmt->execute()) {
        $newCrewId = $erpDb->insert_id;
        echo "  ✓ Created crew: $fullName (ID: $newCrewId, Employee: $employeeId)\n";
        
        // Mark as sent in recruitment
        $updateRec = $recDb->prepare("UPDATE applications SET sent_to_erp_at = NOW() WHERE user_id = ? AND status_id = 6");
        $updateRec->bind_param('i', $userId);
        $updateRec->execute();
        
        $synced++;
    } else {
        echo "  ✗ Failed to create crew: $fullName - " . $stmt->error . "\n";
        $errors++;
    }
}

echo "\nSync Results: $synced created, $skipped already existed, $errors errors\n\n";


// =====================================================
// PHASE 3: FIX ORPHANED CONTRACTS
// =====================================================
echo "--- PHASE 3: FIX ORPHANED CONTRACTS ---\n";

// Re-query orphans (after sync, some may now be resolved)
$orphans = $erpDb->query("
    SELECT c.id, c.contract_no, c.crew_id, c.crew_name, c.status
    FROM contracts c
    LEFT JOIN crews cr ON c.crew_id = cr.id
    WHERE cr.id IS NULL
");

$fixed = 0;
$created = 0;

if ($orphans->num_rows === 0) {
    echo "No orphaned contracts found! All contracts have valid crew records.\n";
} else {
    echo "Found " . $orphans->num_rows . " orphaned contracts. Creating crew records for them...\n\n";
    
    while ($orphan = $orphans->fetch_assoc()) {
        $crewName = $orphan['crew_name'] ?? 'Unknown Crew';
        $crewId = $orphan['crew_id'];
        
        if (empty($crewId) || $crewId == 0) {
            echo "  ⚠ Contract #{$orphan['id']} ({$orphan['contract_no']}) has NULL crew_id - skipping\n";
            continue;
        }
        
        // Check if maybe the crew exists but with different ID mapping
        $nameMatch = $erpDb->prepare("SELECT id FROM crews WHERE full_name = ? LIMIT 1");
        $nameMatch->bind_param('s', $crewName);
        $nameMatch->execute();
        $nameResult = $nameMatch->get_result();
        
        if ($nameResult->num_rows > 0) {
            // Found crew by name — update contract to point to correct crew_id
            $correctCrewId = $nameResult->fetch_assoc()['id'];
            $updateStmt = $erpDb->prepare("UPDATE contracts SET crew_id = ? WHERE id = ?");
            $updateStmt->bind_param('ii', $correctCrewId, $orphan['id']);
            $updateStmt->execute();
            echo "  ✓ Contract #{$orphan['id']} ({$orphan['contract_no']}): crew_id updated $crewId → $correctCrewId (matched by name: $crewName)\n";
            $fixed++;
        } else {
            // No matching crew found — create a basic crew record
            $empId = 'IO' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $crewStatus = 'available';
            $crewSource = 'contract_sync';
            $crewNotes = "Auto-created from orphaned contract: " . $orphan['contract_no'];
            
            $insertCrew = $erpDb->prepare("
                INSERT INTO crews (employee_id, full_name, status, source, notes, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $insertCrew->bind_param('sssss', $empId, $crewName, $crewStatus, $crewSource, $crewNotes);
            
            if ($insertCrew->execute()) {
                $newId = $erpDb->insert_id;
                // Update contract to point to the new crew
                $updateContract = $erpDb->prepare("UPDATE contracts SET crew_id = ? WHERE id = ?");
                $updateContract->bind_param('ii', $newId, $orphan['id']);
                $updateContract->execute();
                echo "  ✓ Contract #{$orphan['id']} ({$orphan['contract_no']}): created crew '$crewName' (ID: $newId) and linked\n";
                $created++;
            } else {
                echo "  ✗ Failed to create crew for contract #{$orphan['id']}: " . $insertCrew->error . "\n";
            }
        }
    }
}

echo "\nOrphan fix results: $fixed re-linked, $created new crews created\n\n";


// =====================================================
// PHASE 4: FINAL DIAGNOSTIC
// =====================================================
echo "--- PHASE 4: FINAL STATE ---\n";

$crewCountFinal = $erpDb->query("SELECT COUNT(*) as cnt FROM crews")->fetch_assoc()['cnt'];
$contractCountFinal = $erpDb->query("SELECT COUNT(*) as cnt FROM contracts")->fetch_assoc()['cnt'];
$orphanCountFinal = $erpDb->query("
    SELECT COUNT(*) as cnt FROM contracts c
    LEFT JOIN crews cr ON c.crew_id = cr.id
    WHERE cr.id IS NULL
")->fetch_assoc()['cnt'];

echo "ERP Crews:            $crewCountFinal (was: $crewCount)\n";
echo "ERP Contracts:        $contractCountFinal\n";
echo "Remaining orphans:    $orphanCountFinal\n";
echo "\n=== SYNC COMPLETE ===\n";
echo "⚠️  DELETE THIS FILE AFTER USE!\n";

$erpDb->close();
$recDb->close();
