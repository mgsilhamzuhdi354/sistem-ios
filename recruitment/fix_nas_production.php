<?php
/**
 * Fix script for NAS production
 * Run: php /var/www/html/recruitment/fix_nas_production.php
 * 
 * Fixes:
 * 1. Add missing 'ktp_number' column to applicant_profiles
 * 2. Fix PHP upload_max_filesize by editing php.ini directly
 * 3. Create upload directories
 */

echo "=== NAS PRODUCTION FIX SCRIPT ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// ============================================
// 1. FIX DATABASE: Add missing ktp_number column
// ============================================
echo "--- 1. DATABASE FIX ---\n";

$dbConfig = require __DIR__ . '/app/Config/Database.php';
$cfg = $dbConfig['default'];

$db = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port'] ?? 3306);

if ($db->connect_error) {
    // Try fallbacks
    foreach (['mysql', 'mariadb-1', '127.0.0.1'] as $host) {
        $db = @new mysqli($host, $cfg['username'], $cfg['password'], $cfg['database'], $cfg['port'] ?? 3306);
        if (!$db->connect_error) break;
    }
}

if ($db->connect_error) {
    die("❌ DB connection failed: " . $db->connect_error . "\n");
}

echo "✅ DB connected\n";

// Check and add ktp_number
$result = $db->query("SHOW COLUMNS FROM applicant_profiles LIKE 'ktp_number'");
if ($result && $result->num_rows === 0) {
    $r = $db->query("ALTER TABLE applicant_profiles ADD COLUMN ktp_number VARCHAR(50) NULL AFTER user_id");
    echo $r ? "✅ Added 'ktp_number' column to applicant_profiles\n" : "❌ Failed to add ktp_number: {$db->error}\n";
} else {
    echo "✅ 'ktp_number' column already exists\n";
}

$db->close();

// ============================================
// 2. FIX PHP UPLOAD LIMITS
// ============================================
echo "\n--- 2. PHP UPLOAD LIMITS FIX ---\n";

// Find php.ini location
$phpIni = php_ini_loaded_file();
echo "Loaded php.ini: " . ($phpIni ?: 'NONE') . "\n";

// Also check for additional ini dirs
$scanDir = php_ini_scanned_files();
echo "Scanned ini files: " . ($scanDir ?: 'NONE') . "\n";

// Find all possible php.ini locations for FPM
$possiblePaths = [
    '/usr/local/etc/php/php.ini',
    '/usr/local/etc/php/php.ini-production',
    '/etc/php/8.1/fpm/php.ini',
    '/etc/php/8.0/fpm/php.ini',
    '/etc/php/8.2/fpm/php.ini',
    '/usr/local/etc/php/conf.d/',
];

echo "\nSearching for php.ini locations:\n";
foreach ($possiblePaths as $path) {
    if (file_exists($path) || is_dir($path)) {
        echo "  ✅ Found: $path\n";
    }
}

// Create a custom ini file in conf.d to override upload limits
$confDirs = [
    '/usr/local/etc/php/conf.d/',
    '/etc/php/8.1/fpm/conf.d/',
    '/etc/php/8.0/fpm/conf.d/',
    '/etc/php/8.2/fpm/conf.d/',
];

$iniContent = "; Upload limits for recruitment system\nupload_max_filesize = 100M\npost_max_size = 110M\nmax_execution_time = 300\nmemory_limit = 256M\nmax_input_time = 300\nmax_file_uploads = 50\n";

$iniWritten = false;
foreach ($confDirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        $iniFile = $dir . '99-recruitment-uploads.ini';
        if (file_put_contents($iniFile, $iniContent)) {
            echo "✅ Created $iniFile\n";
            $iniWritten = true;
        }
        break;
    }
}

// Also try editing the main php.ini directly
if ($phpIni && file_exists($phpIni) && is_writable($phpIni)) {
    $content = file_get_contents($phpIni);
    
    // Replace upload_max_filesize
    if (preg_match('/^upload_max_filesize\s*=\s*.+$/m', $content)) {
        $content = preg_replace('/^upload_max_filesize\s*=\s*.+$/m', 'upload_max_filesize = 100M', $content);
    }
    if (preg_match('/^post_max_size\s*=\s*.+$/m', $content)) {
        $content = preg_replace('/^post_max_size\s*=\s*.+$/m', 'post_max_size = 110M', $content);
    }
    
    file_put_contents($phpIni, $content);
    echo "✅ Updated $phpIni with new upload limits\n";
    $iniWritten = true;
}

if (!$iniWritten) {
    echo "⚠️  Could not write ini files. You need to manually edit php.ini:\n";
    echo "   upload_max_filesize = 100M\n";
    echo "   post_max_size = 110M\n";
}

// ============================================
// 3. CREATE UPLOAD DIRECTORIES
// ============================================
echo "\n--- 3. UPLOAD DIRECTORIES ---\n";

$publicDir = __DIR__ . '/public/';
$dirs = [
    $publicDir . 'uploads/',
    $publicDir . 'uploads/documents/',
    $publicDir . 'uploads/avatars/',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "✅ Created: $dir\n";
        } else {
            echo "❌ Failed to create: $dir\n";
        }
    } else {
        chmod($dir, 0777);
        echo "✅ Exists (chmod 777): $dir\n";
    }
}

// ============================================
// 4. RESTART PHP-FPM (if possible)
// ============================================
echo "\n--- 4. RESTARTING PHP ---\n";

// Try various restart methods
$restartCmds = [
    'kill -USR2 $(pgrep -f "php-fpm: master" | head -1) 2>/dev/null',
    'kill -USR2 $(pgrep php-fpm | head -1) 2>/dev/null',
    'supervisorctl restart php-fpm 2>/dev/null',
    'service php8.1-fpm reload 2>/dev/null',
    'service php-fpm reload 2>/dev/null',
];

$restarted = false;
foreach ($restartCmds as $cmd) {
    $output = [];
    $retval = 0;
    exec($cmd, $output, $retval);
    if ($retval === 0) {
        echo "✅ PHP-FPM restarted using: $cmd\n";
        $restarted = true;
        break;
    }
}

if (!$restarted) {
    echo "⚠️  Could not auto-restart PHP-FPM\n";
    echo "   Run manually: kill -USR2 \$(pgrep php-fpm | head -1)\n";
    echo "   Or wait " . ini_get('user_ini.cache_ttl') . " seconds for .user.ini cache to expire\n";
}

// ============================================
// 5. VERIFY
// ============================================
echo "\n--- 5. VERIFICATION ---\n";
echo "Current upload_max_filesize: " . ini_get('upload_max_filesize') . " (will change after php-fpm restart)\n";

echo "\n=== FIX SCRIPT COMPLETE ===\n";
echo "After php-fpm restart, run diagnostic again to verify:\n";
echo "  php " . __DIR__ . "/diagnose_upload.php\n";
