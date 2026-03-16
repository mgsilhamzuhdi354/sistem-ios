<?php
/**
 * PT Indo Ocean - Database Import Script
 * Imports erp_db_export.sql and recruitment_db_export.sql into MariaDB
 * Run: php /var/www/html/db_import.php
 * HAPUS SETELAH SELESAI!
 */

echo "=== PT Indo Ocean - Database Import ===\n\n";

// Connection settings - try multiple
$hosts = ['mariadb-1', 'mysql', '172.17.0.1', '172.17.0.2', '172.17.0.3'];
$credentials = [
    ['user' => 'root', 'pass' => 'rahasia123'],
    ['user' => 'indoocean', 'pass' => 'indoocean123'],
];

$conn = null;
foreach ($hosts as $host) {
    foreach ($credentials as $cred) {
        try {
            $c = @new mysqli($host, $cred['user'], $cred['pass'], '', 3306);
            if (!$c->connect_error) {
                $conn = $c;
                echo "[OK] Connected to $host as {$cred['user']}\n";
                break 2;
            }
        } catch (Exception $e) {
            continue;
        }
    }
}

if (!$conn) {
    die("[FAIL] Could not connect to any database host!\n");
}

// Create databases if not exist
$conn->query("CREATE DATABASE IF NOT EXISTS erp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "[OK] Database erp_db ensured\n";
$conn->query("CREATE DATABASE IF NOT EXISTS recruitment_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "[OK] Database recruitment_db ensured\n";

// Import function
function importSQL($conn, $dbName, $sqlFile) {
    if (!file_exists($sqlFile)) {
        echo "[FAIL] File not found: $sqlFile\n";
        return false;
    }
    
    $fileSize = filesize($sqlFile);
    echo "\nImporting $sqlFile ({$fileSize} bytes) into $dbName...\n";
    
    $conn->select_db($dbName);
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    $conn->query("SET NAMES utf8mb4");
    
    $sql = file_get_contents($sqlFile);
    
    // Split by statement delimiter
    $errors = 0;
    $success = 0;
    
    // Use multi_query for efficiency
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
            $success++;
        } while ($conn->more_results() && $conn->next_result());
        
        if ($conn->errno) {
            echo "[WARN] Error at statement $success: " . $conn->error . "\n";
            $errors++;
        }
    } else {
        echo "[WARN] multi_query failed: " . $conn->error . "\n";
        echo "Trying statement-by-statement...\n";
        
        // Fallback: split by semicolons and execute one by one
        $statements = explode(";\n", $sql);
        $total = count($statements);
        
        foreach ($statements as $i => $stmt) {
            $stmt = trim($stmt);
            if (empty($stmt) || $stmt === '--') continue;
            
            if (!$conn->query($stmt)) {
                $errors++;
                if ($errors <= 5) {
                    echo "[WARN] Error: " . $conn->error . "\n";
                }
            } else {
                $success++;
            }
            
            // Progress
            if (($i + 1) % 50 === 0) {
                echo "  Progress: " . ($i + 1) . "/$total statements\n";
            }
        }
    }
    
    $conn->query("SET FOREIGN_KEY_CHECKS=1");
    
    echo "[OK] Import $dbName complete: $success successful" . ($errors > 0 ? ", $errors errors" : "") . "\n";
    
    // Show tables
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "[OK] Tables in $dbName: " . $result->num_rows . "\n";
        while ($row = $result->fetch_row()) {
            echo "     - $row[0]\n";
        }
    }
    
    return true;
}

// Import both databases
$basePath = '/var/www/html/';
importSQL($conn, 'erp_db', $basePath . 'erp_db_export.sql');
importSQL($conn, 'recruitment_db', $basePath . 'recruitment_db_export.sql');

// Grant privileges
$conn->query("GRANT ALL PRIVILEGES ON erp_db.* TO 'root'@'%'");
$conn->query("GRANT ALL PRIVILEGES ON recruitment_db.* TO 'root'@'%'");
$conn->query("GRANT ALL PRIVILEGES ON erp_db.* TO 'indoocean'@'%' IDENTIFIED BY 'indoocean123'");
$conn->query("GRANT ALL PRIVILEGES ON recruitment_db.* TO 'indoocean'@'%' IDENTIFIED BY 'indoocean123'");
$conn->query("FLUSH PRIVILEGES");
echo "\n[OK] Privileges granted\n";

$conn->close();
echo "\n=== DONE! Hapus file ini setelah selesai. ===\n";
