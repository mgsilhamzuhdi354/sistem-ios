<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['application_id'] = '3';
$_POST['rank_id'] = '1';
$_POST['join_date'] = '2026-02-16';
$_POST['notes'] = 'test';
$_POST['csrf_token'] = 'skip';

// Boot the application
define('ROOTPATH', dirname(__DIR__) . '/');
define('APPPATH', ROOTPATH . 'app/');

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'crewing';
$_SESSION['logged_in'] = true;

// Load database
require_once APPPATH . 'Config/Database.php';

// Load ErpSync
require_once APPPATH . 'Libraries/ErpSync.php';

$dbConfig = require APPPATH . 'Config/Database.php';
$default = $dbConfig['default'];

echo "<h3>Testing ERP Sync</h3>";

// Connect to recruitment DB
$recruitDb = new mysqli(
    $default['hostname'] ?? 'localhost',
    $default['username'] ?? 'root',
    $default['password'] ?? '',
    $default['database'] ?? 'recruitment_db',
    $default['port'] ?? 3306
);

if ($recruitDb->connect_error) {
    die("Recruitment DB connection error: " . $recruitDb->connect_error);
}
echo "<p>✅ Recruitment DB connected</p>";

// Test ErpSync connection
try {
    $erpSync = new ErpSync($recruitDb);
    echo "<p>✅ ErpSync created (ERP DB connected)</p>";
} catch (Exception $e) {
    die("<p>❌ ErpSync error: " . $e->getMessage() . "</p>");
}

// Get application data
$stmt = $recruitDb->prepare("
    SELECT 
        a.*, 
        u.full_name, u.email, u.phone, u.avatar,
        jv.title as job_title,
        ap.gender, ap.date_of_birth, ap.place_of_birth,
        ap.nationality, ap.address as profile_address,
        ap.city, ap.postal_code,
        ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
        ap.total_sea_service_months,
        ap.profile_photo
    FROM applications a
    JOIN users u ON a.user_id = u.id
    LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
    LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
    WHERE a.id = 3
");
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if (!$app) {
    die("<p>❌ Application ID 3 not found</p>");
}
echo "<p>✅ Application found: " . htmlspecialchars($app['full_name']) . "</p>";
echo "<p>Status ID: " . $app['status_id'] . "</p>";

// Get documents
$docStmt = $recruitDb->prepare("
    SELECT d.*, dt.name as type_name
    FROM documents d
    LEFT JOIN document_types dt ON d.document_type_id = dt.id
    WHERE d.user_id = ?
");
$docStmt->bind_param('i', $app['user_id']);
$docStmt->execute();
$documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
echo "<p>✅ Documents found: " . count($documents) . "</p>";

foreach ($documents as $doc) {
    echo "<li>" . htmlspecialchars($doc['type_name'] ?? 'Unknown') . " - " . htmlspecialchars($doc['file_path'] ?? 'no file') . "</li>";
}

// Try to create crew
echo "<h3>Attempting crew creation...</h3>";
try {
    // Check if already exists
    $existingCrewId = $erpSync->getCrewByCandidateId($app['user_id']);
    if ($existingCrewId) {
        echo "<p>⚠️ Crew already exists with ID: $existingCrewId - will update instead</p>";
        $erpSync->updateCrew($existingCrewId, [
            'current_rank_id' => 1,
            'status' => 'pending_approval',
        ]);
        $crewId = $existingCrewId;
    } else {
        $crewData = [
            'full_name' => $app['full_name'],
            'email' => $app['email'],
            'phone' => $app['phone'],
            'employee_id' => 'IO' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'candidate_id' => $app['user_id'],
            'rank_id' => 1,
            'status' => 'pending_approval',
            'notes' => 'Test from debug script',
            'gender' => $app['gender'] ?? 'male',
            'birth_date' => $app['date_of_birth'] ?? null,
            'birth_place' => $app['place_of_birth'] ?? '',
            'nationality' => $app['nationality'] ?? 'Indonesian',
            'address' => $app['profile_address'] ?? '',
            'city' => $app['city'] ?? '',
            'postal_code' => $app['postal_code'] ?? '',
            'emergency_name' => $app['emergency_name'] ?? '',
            'emergency_phone' => $app['emergency_phone'] ?? '',
            'emergency_relation' => $app['emergency_relation'] ?? '',
            'total_sea_time_months' => intval($app['total_sea_service_months'] ?? 0),
        ];
        
        $crewId = $erpSync->createCrew($crewData);
        echo "<p>✅ Crew created with ID: $crewId</p>";
    }

    // Sync documents
    if (!empty($documents)) {
        $docsSynced = $erpSync->syncDocuments($crewId, $documents);
        echo "<p>✅ Documents synced: $docsSynced</p>";
    }

    // Sync photo
    $photoPath = $app['profile_photo'] ?: $app['avatar'];
    if ($photoPath) {
        $erpPhotoPath = $erpSync->syncPhoto($photoPath, $crewId);
        echo "<p>" . ($erpPhotoPath ? "✅ Photo synced: $erpPhotoPath" : "⚠️ Photo not found at: $photoPath") . "</p>";
    }

    echo "<h3>🎉 SUCCESS! All data transferred to ERP</h3>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
