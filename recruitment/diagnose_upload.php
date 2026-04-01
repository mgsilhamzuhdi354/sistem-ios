<?php
/**
 * Diagnostic script for document upload issues on NAS
 * Run via: php /var/www/html/recruitment/diagnose_upload.php
 * Or browser: http://domain/recruitment/diagnose_upload.php
 */

echo "<pre>\n";
echo "=== DOCUMENT UPLOAD DIAGNOSTIC v2 ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Check FCPATH
echo "--- 1. FCPATH CHECK ---\n";
$publicDir = __DIR__ . '/public/';
echo "Expected FCPATH: $publicDir\n";
echo "Exists: " . (is_dir($publicDir) ? 'YES' : 'NO') . "\n";
echo "Writable: " . (is_writable($publicDir) ? 'YES' : 'NO') . "\n\n";

// 2. Check upload directories
echo "--- 2. UPLOAD DIRECTORIES ---\n";
$uploadBase = $publicDir . 'uploads/';
$uploadDocs = $publicDir . 'uploads/documents/';
echo "uploads/ exists: " . (is_dir($uploadBase) ? 'YES' : 'NO') . "\n";
echo "uploads/ writable: " . (is_writable($uploadBase) ? 'YES' : 'NO') . "\n";
echo "uploads/documents/ exists: " . (is_dir($uploadDocs) ? 'YES' : 'NO') . "\n";
echo "uploads/documents/ writable: " . (is_writable($uploadDocs) ? 'YES' : 'NO') . "\n";

// Test write
$testFile = $uploadDocs . 'test_write_' . time() . '.txt';
$writeResult = @file_put_contents($testFile, 'test');
echo "Write test: " . ($writeResult !== false ? 'SUCCESS' : 'FAILED') . "\n";
if ($writeResult !== false) @unlink($testFile);

echo "\n";

// 3. Check PHP upload settings
echo "--- 3. PHP UPLOAD SETTINGS ---\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads: " . ini_get('file_uploads') . "\n";
echo "upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) . "\n";
echo "tmp dir writable: " . (is_writable(ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) ? 'YES' : 'NO') . "\n";

$uploadMax = ini_get('upload_max_filesize');
$uploadMaxBytes = (int)$uploadMax;
if (stripos($uploadMax, 'M') !== false) $uploadMaxBytes = (int)$uploadMax * 1024 * 1024;
if (stripos($uploadMax, 'K') !== false) $uploadMaxBytes = (int)$uploadMax * 1024;
echo "\n⚠️  upload_max_filesize = $uploadMax = $uploadMaxBytes bytes\n";
if ($uploadMaxBytes < 5 * 1024 * 1024) {
    echo "❌ TERLALU KECIL! File > {$uploadMax} akan ditolak PHP secara otomatis!\n";
    echo "   FIX: edit php.ini atau buat .user.ini di document root\n";
}

// Check .user.ini
echo "\n--- 3b. .user.ini CHECK ---\n";
$userIniLocations = [
    $publicDir . '.user.ini',
    __DIR__ . '/.user.ini',
];
foreach ($userIniLocations as $path) {
    echo "  $path: " . (file_exists($path) ? 'EXISTS (' . file_get_contents($path) . ')' : 'NOT FOUND') . "\n";
}
echo "  user_ini.cache_ttl: " . ini_get('user_ini.cache_ttl') . " seconds\n";

echo "\n";

// 4. Check error log configuration
echo "--- 4. ERROR LOG ---\n";
echo "error_log: " . (ini_get('error_log') ?: '(empty - goes to stderr/syslog)') . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";

echo "\n";

// 5. Check database
echo "--- 5. DATABASE CHECK ---\n";

// Load config - try multiple paths
$dbConfig = null;
$configPaths = [
    __DIR__ . '/app/Config/Database.php',
    __DIR__ . '/app/config.php', 
    __DIR__ . '/config.php',
];
foreach ($configPaths as $path) {
    if (file_exists($path)) {
        echo "Using config: $path\n";
        $dbConfig = require $path;
        break;
    }
}

if ($dbConfig && isset($dbConfig['default'])) {
    $cfg = $dbConfig['default'];
    echo "Host: {$cfg['hostname']}, DB: {$cfg['database']}, User: {$cfg['username']}\n";
    
    $db = @new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port'] ?? 3306);
    if ($db->connect_error) {
        echo "DB connection FAILED: " . $db->connect_error . "\n";
        
        // Try fallback hosts
        $fallbackHosts = ['mysql', 'mariadb-1', '127.0.0.1', 'localhost'];
        foreach ($fallbackHosts as $host) {
            $db = @new mysqli($host, $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port'] ?? 3306);
            if (!$db->connect_error) {
                echo "Connected via fallback host: $host\n";
                break;
            }
        }
    }
    
    if (!$db->connect_error) {
        echo "DB connection: OK\n";
        
        // Check documents table
        echo "\n--- documents table ---\n";
        $r = $db->query("DESCRIBE documents");
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                echo "  {$row['Field']} ({$row['Type']})\n";
            }
        } else {
            echo "  ❌ TABLE NOT FOUND: " . $db->error . "\n";
        }
        
        // Check document_types
        echo "\n--- document_types ---\n";
        $r = $db->query("SELECT * FROM document_types ORDER BY id");
        if ($r && $r->num_rows > 0) {
            while ($row = $r->fetch_assoc()) {
                echo "  ID:{$row['id']} | {$row['name']}\n";
            }
        } else {
            echo "  ❌ TABLE EMPTY OR NOT FOUND: " . $db->error . "\n";
        }
        
        // Check user for app 1
        echo "\n--- Application ID 1 ---\n";
        $r = $db->query("SELECT a.user_id, a.id, u.full_name FROM applications a JOIN users u ON a.user_id = u.id WHERE a.id = 1");
        if ($r && $row = $r->fetch_assoc()) {
            $uid = $row['user_id'];
            echo "  User ID: $uid ({$row['full_name']})\n";
            
            $r2 = $db->query("SELECT COUNT(*) as cnt FROM applicant_profiles WHERE user_id = $uid");
            $cnt = $r2 ? $r2->fetch_assoc()['cnt'] : 0;
            echo "  Profile exists: " . ($cnt > 0 ? 'YES' : '❌ NO - This causes data loss on update!') . "\n";
            
            $r3 = $db->query("SELECT COUNT(*) as cnt FROM documents WHERE user_id = $uid");
            echo "  Documents: " . ($r3 ? $r3->fetch_assoc()['cnt'] : 0) . "\n";
            
            $userUploadDir = $uploadDocs . $uid . '/';
            echo "  Upload dir: " . (is_dir($userUploadDir) ? 'EXISTS' : 'NOT FOUND') . "\n";
        } else {
            echo "  Application 1 not found\n";
        }
        
        // Check applicant_profiles columns
        echo "\n--- applicant_profiles columns ---\n";
        $r = $db->query("DESCRIBE applicant_profiles");
        if ($r) {
            $cols = [];
            while ($row = $r->fetch_assoc()) $cols[] = $row['Field'];
            echo "  Columns: " . implode(', ', $cols) . "\n";
            
            // Check for missing columns that the code needs
            $required = ['ktp_number', 'place_of_birth', 'blood_type', 'postal_code', 
                        'seaman_book_no', 'seaman_book_expiry', 'passport_expiry',
                        'height_cm', 'weight_kg', 'shoe_size', 'overall_size',
                        'total_sea_service_months', 'last_rank', 'last_vessel_name', 'last_vessel_type', 'last_sign_off'];
            $missing = array_diff($required, $cols);
            if (!empty($missing)) {
                echo "\n  ❌ MISSING COLUMNS: " . implode(', ', $missing) . "\n";
                echo "  This will cause profile UPDATE to fail silently!\n";
            } else {
                echo "\n  ✅ All required columns exist\n";
            }
        } else {
            echo "  ❌ TABLE NOT FOUND: " . $db->error . "\n";
        }
        
        $db->close();
    }
} else {
    echo "❌ Config not loaded\n";
}

echo "\n";

// 6. Process info
echo "--- 6. PROCESS INFO ---\n";
echo "Current user: " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : get_current_user()) . "\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "OS: " . PHP_OS . "\n";
echo "SAPI: " . php_sapi_name() . "\n";

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "</pre>\n";
