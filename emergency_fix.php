<?php
/**
 * EMERGENCY FIX SCRIPT - PT Indo Ocean
 * Upload ke NAS shared folder, akses via browser
 * Hapus setelah selesai!
 */

echo "<html><head><title>Emergency Fix</title><style>
body{font-family:monospace;background:#1a1a2e;color:#0f0;padding:20px;font-size:14px;}
h2{color:#ff0;} .ok{color:#0f0;} .err{color:#f00;} .warn{color:#ff0;}
pre{background:#000;padding:10px;border:1px solid #333;overflow-x:auto;}
</style></head><body>";

echo "<h1>🔧 Emergency Fix - PT Indo Ocean</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP OS: " . PHP_OS . " | PHP_OS_FAMILY: " . PHP_OS_FAMILY . "</p>";
echo "<p>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "</p>";

// ===================================================
// 1. CHECK DIRECTORY STRUCTURE
// ===================================================
echo "<h2>1. Directory Structure Check</h2>";
$docRoot = $_SERVER['DOCUMENT_ROOT'] ?: '/var/www/html';
echo "<p>Document Root: <b>$docRoot</b></p>";

echo "<pre>";
echo "=== Contents of $docRoot ===\n";
if (is_dir($docRoot)) {
    $items = scandir($docRoot);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $docRoot . '/' . $item;
        $type = is_dir($path) ? '[DIR]' : '[FILE]';
        $size = is_file($path) ? filesize($path) : '';
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path))['name'] ?? fileowner($path) : fileowner($path);
        echo sprintf("%-7s %-4s %-8s %8s  %s\n", $type, $perms, $owner, $size, $item);
    }
} else {
    echo "ERROR: $docRoot does not exist!\n";
}
echo "</pre>";

// Check critical files
$criticalFiles = [
    'index.html' => 'Landing page with redirect to /erp/',
    '.htaccess' => 'Apache rewrite rules',
    'erp/index.php' => 'ERP entry point',
    'erp/.htaccess' => 'ERP rewrite rules',
    'erp/app/Config/Database.php' => 'ERP database config',
    'erp/app/Controllers/BaseController.php' => 'ERP base controller',
    'recruitment/public/index.php' => 'Recruitment entry point',
    'recruitment/app/Config/Database.php' => 'Recruitment database config',
];

echo "<h2>2. Critical Files Check</h2><pre>";
$allOk = true;
foreach ($criticalFiles as $file => $desc) {
    $fullPath = $docRoot . '/' . $file;
    if (file_exists($fullPath)) {
        echo "<span class='ok'>✅ EXISTS</span>  $file - $desc\n";
    } else {
        echo "<span class='err'>❌ MISSING</span> $file - $desc\n";
        $allOk = false;
    }
}
echo "</pre>";

// Check if files are in a subfolder
echo "<h2>3. Subfolder Check</h2><pre>";
$possibleSubfolders = ['PT_indoocean', 'PT_indoocean/PT_indoocean', 'html', 'www'];
foreach ($possibleSubfolders as $sub) {
    $subPath = $docRoot . '/' . $sub;
    if (is_dir($subPath)) {
        echo "<span class='warn'>⚠️ Found subfolder: $sub/</span>\n";
        $subItems = scandir($subPath);
        foreach ($subItems as $si) {
            if ($si === '.' || $si === '..') continue;
            $type = is_dir($subPath . '/' . $si) ? '[DIR]' : '[FILE]';
            echo "   $type $si\n";
        }
        // Check if ERP is inside
        if (is_dir($subPath . '/erp')) {
            echo "<span class='err'>   ❌ ERP folder found inside $sub/ — FILES ARE IN WRONG LOCATION!</span>\n";
        }
    }
}
echo "</pre>";

// ===================================================
// 4. CREATE MISSING FILES
// ===================================================
echo "<h2>4. Auto-Fix Missing Files</h2><pre>";

// Create index.html if missing
if (!file_exists($docRoot . '/index.html')) {
    $indexHtml = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Indo Ocean Crew Services</title>
    <meta http-equiv="refresh" content="0;url=/erp/">
</head>
<body>
    <p>Redirecting to <a href="/erp/">ERP System</a>...</p>
</body>
</html>';
    if (@file_put_contents($docRoot . '/index.html', $indexHtml)) {
        echo "<span class='ok'>✅ Created index.html</span>\n";
    } else {
        echo "<span class='err'>❌ Cannot create index.html (permission denied)</span>\n";
    }
}

// Create .htaccess if missing
if (!file_exists($docRoot . '/.htaccess')) {
    $htaccess = 'Options -Indexes +FollowSymLinks
DirectoryIndex index.php index.html

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Redirect root to ERP
    RewriteRule ^$ erp/ [L,R=302]

    # Handle recruitment routing
    RewriteCond %{REQUEST_URI} ^/recruitment
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^recruitment/(.*)$ recruitment/public/index.php?url=$1 [L,QSA]

    # Handle ERP routing
    RewriteCond %{REQUEST_URI} ^/erp
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^erp/(.*)$ erp/index.php?url=$1 [L,QSA]
</IfModule>

# Security: deny access to sensitive files
<FilesMatch "\.(env|log|sql|sh|git)$">
    Require all denied
</FilesMatch>';
    if (@file_put_contents($docRoot . '/.htaccess', $htaccess)) {
        echo "<span class='ok'>✅ Created .htaccess</span>\n";
    } else {
        echo "<span class='err'>❌ Cannot create .htaccess</span>\n";
    }
}

echo "</pre>";

// ===================================================
// 5. FIX PERMISSIONS
// ===================================================
echo "<h2>5. Permission Fix</h2><pre>";
$user = function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] ?? 'unknown' : 'unknown';
echo "Running as user: $user (UID: " . (function_exists('posix_geteuid') ? posix_geteuid() : 'unknown') . ")\n";

// Try chmod
$testFile = $docRoot . '/.permission_test';
if (@file_put_contents($testFile, 'test')) {
    @unlink($testFile);
    echo "<span class='ok'>✅ Write permission OK in $docRoot</span>\n";
} else {
    echo "<span class='err'>❌ No write permission in $docRoot</span>\n";
}

// Try to fix with exec if available
if (function_exists('exec')) {
    @exec('chown -R www-data:www-data ' . escapeshellarg($docRoot) . ' 2>&1', $output1, $rc1);
    echo "chown result (rc=$rc1): " . implode("\n", $output1) . "\n";
    
    @exec('chmod -R 755 ' . escapeshellarg($docRoot) . ' 2>&1', $output2, $rc2);
    echo "chmod result (rc=$rc2): " . implode("\n", $output2) . "\n";
    
    if ($rc1 === 0 && $rc2 === 0) {
        echo "<span class='ok'>✅ Permissions fixed!</span>\n";
    }
} else {
    echo "<span class='warn'>⚠️ exec() disabled, cannot fix permissions via PHP</span>\n";
}
echo "</pre>";

// ===================================================
// 6. DATABASE CONNECTION TEST
// ===================================================
echo "<h2>6. Database Connection Test</h2><pre>";

$hostsToTry = ['172.17.0.2', '172.17.0.3', '172.17.0.4', '172.17.0.5', 'mysql', 'mariadb', 'mariadb-1', 'localhost', '127.0.0.1'];
$dbUser = 'root';
$dbPass = 'rahasia123';
$dbName = 'erp_db';
$dbPort = 3306;

$connectedHost = null;

foreach ($hostsToTry as $host) {
    try {
        $startTime = microtime(true);
        $conn = @new mysqli($host, $dbUser, $dbPass, $dbName, $dbPort);
        $elapsed = round((microtime(true) - $startTime) * 1000);
        
        if (!$conn->connect_error) {
            echo "<span class='ok'>✅ CONNECTED to $host ({$elapsed}ms)</span>\n";
            echo "   Server: " . $conn->server_info . "\n";
            
            // Check databases
            $result = $conn->query("SHOW DATABASES");
            if ($result) {
                echo "   Databases: ";
                $dbs = [];
                while ($row = $result->fetch_row()) {
                    $dbs[] = $row[0];
                }
                echo implode(', ', $dbs) . "\n";
            }
            
            $connectedHost = $host;
            $conn->close();
            break;
        } else {
            echo "<span class='err'>❌ $host: {$conn->connect_error} ({$elapsed}ms)</span>\n";
        }
    } catch (Exception $e) {
        $elapsed = round((microtime(true) - $startTime) * 1000);
        $msg = $e->getMessage();
        // Shorten common messages
        if (strpos($msg, 'No such file') !== false) $msg = 'No socket (expected for remote)';
        if (strpos($msg, 'Connection refused') !== false) $msg = 'Connection refused';
        if (strpos($msg, 'timed out') !== false) $msg = 'Timeout';
        echo "<span class='warn'>⚠️ $host: $msg ({$elapsed}ms)</span>\n";
    }
}

if ($connectedHost) {
    echo "\n<span class='ok'>🎉 DATABASE HOST = $connectedHost</span>\n";
    echo "<span class='ok'>Use this IP in Database.php!</span>\n";
    
    // Check recruitment_db too
    try {
        $conn2 = @new mysqli($connectedHost, $dbUser, $dbPass, 'recruitment_db', $dbPort);
        if (!$conn2->connect_error) {
            echo "<span class='ok'>✅ recruitment_db also accessible on $connectedHost</span>\n";
            $conn2->close();
        }
    } catch (Exception $e) {
        echo "<span class='warn'>⚠️ recruitment_db: " . $e->getMessage() . "</span>\n";
    }
} else {
    echo "\n<span class='err'>❌ CANNOT CONNECT TO ANY DATABASE HOST!</span>\n";
    echo "<span class='err'>Check if MariaDB container is running!</span>\n";
}

echo "</pre>";

// ===================================================
// 7. CHECK DATABASE.PHP CONFIG
// ===================================================
echo "<h2>7. Current Database.php Analysis</h2><pre>";
$dbConfigPath = $docRoot . '/erp/app/Config/Database.php';
if (file_exists($dbConfigPath)) {
    $content = file_get_contents($dbConfigPath);
    
    // Check if it has the hardcoded values
    if (strpos($content, "172.17.0.3") !== false && strpos($content, "HARDCODED") !== false) {
        echo "<span class='ok'>✅ Database.php has HARDCODED Docker values (latest version)</span>\n";
    } elseif (strpos($content, "172.17.0.3") !== false) {
        echo "<span class='warn'>⚠️ Database.php has 172.17.0.3 but might not be hardcoded version</span>\n";
    } else {
        echo "<span class='err'>❌ Database.php does NOT have 172.17.0.3 — OLD VERSION!</span>\n";
    }
    
    // Check what host it would use
    if (strpos($content, 'isWindows') !== false) {
        echo "<span class='ok'>✅ Has OS detection (isWindows check)</span>\n";
    }
    
    // Show first few important lines
    echo "\nKey content:\n";
    $lines = explode("\n", $content);
    foreach ($lines as $num => $line) {
        $lineNum = $num + 1;
        $trimmed = trim($line);
        if (strpos($trimmed, 'isWindows') !== false || 
            strpos($trimmed, 'defaultHost') !== false || 
            strpos($trimmed, 'nas_ip') !== false ||
            strpos($trimmed, 'HARDCODED') !== false ||
            strpos($trimmed, '172.17') !== false ||
            strpos($trimmed, 'getEnvVar') !== false && strpos($trimmed, 'DB_HOST') !== false) {
            echo "  L$lineNum: $trimmed\n";
        }
    }
} else {
    echo "<span class='err'>❌ Database.php NOT FOUND at $dbConfigPath</span>\n";
}
echo "</pre>";

// ===================================================
// 8. CHECK BASECONTROLLER.PHP
// ===================================================
echo "<h2>8. BaseController.php Analysis</h2><pre>";
$bcPath = $docRoot . '/erp/app/Controllers/BaseController.php';
if (file_exists($bcPath)) {
    $bcContent = file_get_contents($bcPath);
    
    if (strpos($bcContent, 'hostsToTry') !== false) {
        echo "<span class='ok'>✅ BaseController.php has FALLBACK logic (latest version)</span>\n";
    } else {
        echo "<span class='err'>❌ BaseController.php is OLD VERSION — no fallback!</span>\n";
        echo "   This means it only tries one host and fails.\n";
    }
    
    // Find the mysqli construct line
    preg_match('/new\s+\\\\?mysqli\s*\(/', $bcContent, $matches, PREG_OFFSET_CAPTURE);
    if ($matches) {
        $pos = $matches[0][1];
        $lineNum = substr_count(substr($bcContent, 0, $pos), "\n") + 1;
        echo "   mysqli construct at line: $lineNum\n";
    }
} else {
    echo "<span class='err'>❌ BaseController.php NOT FOUND!</span>\n";
}
echo "</pre>";

// ===================================================
// 9. ENVIRONMENT VARIABLES
// ===================================================
echo "<h2>9. Environment Variables</h2><pre>";
$envVars = ['DB_HOST', 'DB_USERNAME', 'DB_USER', 'DB_PASSWORD', 'DB_PASS', 'DB_DATABASE', 'DB_PORT', 'MYSQL_ROOT_PASSWORD'];
foreach ($envVars as $var) {
    $val = getenv($var);
    $envVal = $_ENV[$var] ?? null;
    $srvVal = $_SERVER[$var] ?? null;
    
    if ($val !== false || $envVal || $srvVal) {
        $display = $val ?: $envVal ?: $srvVal;
        if (strpos(strtolower($var), 'pass') !== false) {
            $display = substr($display, 0, 3) . '***';
        }
        echo "<span class='ok'>✅ $var = $display</span>\n";
    } else {
        echo "<span class='warn'>   $var = (not set)</span>\n";
    }
}
echo "</pre>";

// ===================================================
// 10. SUMMARY & RECOMMENDATIONS
// ===================================================
echo "<h2>10. Summary & What To Do</h2><pre>";
if ($connectedHost && $allOk) {
    echo "<span class='ok'>🎉 EVERYTHING LOOKS GOOD!</span>\n";
    echo "Database: $connectedHost ✅\n";
    echo "Files: All present ✅\n";
    echo "\nTry accessing: /erp/ now!\n";
} else {
    if (!$allOk) {
        echo "<span class='err'>❌ Missing critical files in /var/www/html/</span>\n";
        echo "   Make sure to upload ALL project files directly to the shared folder\n";
        echo "   The folder should contain: erp/, recruitment/, index.html, .htaccess\n";
        echo "   NOT nested inside PT_indoocean/ subfolder!\n\n";
    }
    if (!$connectedHost) {
        echo "<span class='err'>❌ Cannot connect to database</span>\n";
        echo "   Check if MariaDB container is running\n\n";
    }
    if ($connectedHost && !$allOk) {
        echo "<span class='warn'>Database OK but files missing. Upload files first!</span>\n";
    }
}
echo "</pre>";

echo "<p style='color:#f00;font-size:16px;margin-top:30px;'>⚠️ HAPUS FILE INI SETELAH SELESAI! (emergency_fix.php)</p>";
echo "</body></html>";
?>
