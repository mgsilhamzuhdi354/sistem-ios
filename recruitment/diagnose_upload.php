<?php
/**
 * Diagnostic script for document upload issues on NAS
 * Run via: php /var/www/html/recruitment/diagnose_upload.php
 * Or browser: http://domain/recruitment/diagnose_upload.php
 */

echo "<pre>\n";
echo "=== DOCUMENT UPLOAD DIAGNOSTIC ===\n";
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

// Try creating test directory
if (!is_dir($uploadDocs)) {
    echo "\nAttempting to create uploads/documents/...\n";
    $result = @mkdir($uploadDocs, 0755, true);
    echo "mkdir result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    if (!$result) {
        echo "Error: " . error_get_last()['message'] . "\n";
    }
}

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

echo "\n";

// 4. Check error log configuration
echo "--- 4. ERROR LOG ---\n";
echo "error_log: " . ini_get('error_log') . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
$errLog = ini_get('error_log');
if ($errLog) {
    echo "error_log exists: " . (file_exists($errLog) ? 'YES' : 'NO') . "\n";
    echo "error_log writable: " . (is_writable($errLog) ? 'YES' : 'NO') . "\n";
}
// Try writing a test error_log
error_log("[DIAG_TEST] This is a test log entry from diagnose_upload.php");
echo "Test error_log written. Check your configured log location.\n";

echo "\n";

// 5. Check database
echo "--- 5. DATABASE CHECK ---\n";
// Load database config
$configFile = __DIR__ . '/app/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
    
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        echo "DB connection FAILED: " . $db->connect_error . "\n";
    } else {
        echo "DB connection: OK\n";
        
        // Check documents table
        echo "\n--- documents table columns ---\n";
        $r = $db->query("DESCRIBE documents");
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                echo "  {$row['Field']} - {$row['Type']} {$row['Null']} {$row['Key']}\n";
            }
        } else {
            echo "  ERROR: " . $db->error . "\n";
            echo "  Table might not exist!\n";
        }
        
        // Check document_types table
        echo "\n--- document_types entries ---\n";
        $r = $db->query("SELECT * FROM document_types ORDER BY id");
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                echo "  ID:{$row['id']} | {$row['name']} | {$row['name_id']}\n";
            }
        } else {
            echo "  ERROR: " . $db->error . "\n";
        }
        
        // Check existing documents count
        echo "\n--- documents count ---\n";
        $r = $db->query("SELECT COUNT(*) as cnt FROM documents");
        if ($r) {
            echo "  Total documents in DB: " . $r->fetch_assoc()['cnt'] . "\n";
        }
        
        // Check applicant_profiles table
        echo "\n--- applicant_profiles columns ---\n";
        $r = $db->query("DESCRIBE applicant_profiles");
        if ($r) {
            while ($row = $r->fetch_assoc()) {
                echo "  {$row['Field']} - {$row['Type']}\n";
            }
        } else {
            echo "  ERROR: " . $db->error . "\n";
        }
        
        // Check user 1's profile
        echo "\n--- User ID for app 1 ---\n";
        $r = $db->query("SELECT a.user_id, u.full_name FROM applications a JOIN users u ON a.user_id = u.id WHERE a.id = 1");
        if ($r && $row = $r->fetch_assoc()) {
            $uid = $row['user_id'];
            echo "  App ID 1 -> User ID: $uid ({$row['full_name']})\n";
            
            // Check profile exists
            $r2 = $db->query("SELECT COUNT(*) as cnt FROM applicant_profiles WHERE user_id = $uid");
            echo "  Profile exists: " . ($r2->fetch_assoc()['cnt'] > 0 ? 'YES' : 'NO') . "\n";
            
            // Check documents
            $r3 = $db->query("SELECT COUNT(*) as cnt FROM documents WHERE user_id = $uid");
            echo "  Documents count: " . $r3->fetch_assoc()['cnt'] . "\n";
            
            // Check upload dir for this user
            $userUploadDir = $uploadDocs . $uid . '/';
            echo "  Upload dir ($userUploadDir): " . (is_dir($userUploadDir) ? 'EXISTS' : 'NOT FOUND') . "\n";
            if (is_dir($userUploadDir)) {
                $files = scandir($userUploadDir);
                echo "  Files in dir: " . (count($files) - 2) . "\n";
                foreach (array_diff($files, ['.', '..']) as $f) {
                    echo "    - $f (" . filesize($userUploadDir . $f) . " bytes)\n";
                }
            }
        }
        
        $db->close();
    }
} else {
    echo "Config file not found at: $configFile\n";
    // Try alternate paths
    $altConfigs = [
        __DIR__ . '/config.php',
        __DIR__ . '/app/Config/Database.php',
    ];
    foreach ($altConfigs as $alt) {
        echo "  Checking: $alt -> " . (file_exists($alt) ? 'EXISTS' : 'not found') . "\n";
    }
}

echo "\n";

// 6. Permissions check
echo "--- 6. PROCESS INFO ---\n";
echo "Current user: " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : get_current_user()) . "\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "OS: " . PHP_OS . "\n";

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "</pre>\n";
