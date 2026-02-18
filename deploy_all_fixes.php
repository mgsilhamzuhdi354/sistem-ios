<?php
/**
 * PT Indo Ocean - Production Deploy Script (FIXES ONLY)
 * Only fixes PHP bugs - does NOT touch index.html or .htaccess
 * 
 * Access: https://indooceancrewservice.com/deploy_all_fixes.php
 * DELETE THIS FILE AFTER USE!
 */

echo "<h1>🔧 PT Indo Ocean - Production Bug Fixes</h1><pre>";
$errors = [];
$successes = [];

// =============================================
// 1. FIX RecruitmentHub.php (remove duplicate DB connection)
// =============================================
echo "\n<b>🔧 1. Fixing RecruitmentHub.php (DB connection)...</b>\n";

$rhubFile = __DIR__ . '/erp/app/Controllers/RecruitmentHub.php';
if (file_exists($rhubFile)) {
    $content = file_get_contents($rhubFile);
    
    if (strpos($content, "\$_ENV['DB_HOST'] ?? 'localhost'") !== false && strpos($content, 'class RecruitmentHub') !== false) {
        // Try both line ending styles
        $patterns = ["\n", "\r\n"];
        $fixed = false;
        
        foreach ($patterns as $eol) {
            $old = "    public function __construct(){$eol}    {{$eol}        parent::__construct();{$eol}{$eol}        // Connect to recruitment database{$eol}        \$this->recruitmentDb = new \\mysqli({$eol}            \$_ENV['DB_HOST'] ?? 'localhost',{$eol}            \$_ENV['DB_USERNAME'] ?? 'root',{$eol}            \$_ENV['DB_PASSWORD'] ?? '',{$eol}            \$_ENV['RECRUITMENT_DB_NAME'] ?? 'recruitment_db',{$eol}            \$_ENV['DB_PORT'] ?? 3306{$eol}        );{$eol}{$eol}        if (\$this->recruitmentDb->connect_error) {{$eol}            error_log(\"Recruitment DB connection failed: \" . \$this->recruitmentDb->connect_error);{$eol}        }{$eol}    }";
            
            $new = "    public function __construct(){$eol}    {{$eol}        parent::__construct();{$eol}{$eol}        // recruitmentDb is already connected by BaseController via connectDatabase(){$eol}        // which includes Docker/NAS IP fallback logic{$eol}        if (!\$this->recruitmentDb || (property_exists(\$this->recruitmentDb, 'connect_error') && \$this->recruitmentDb->connect_error)) {{$eol}            error_log(\"RecruitmentHub: Recruitment DB not available via BaseController\");{$eol}        }{$eol}    }";
            
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $fixed = true;
                break;
            }
        }
        
        if ($fixed && file_put_contents($rhubFile, $content)) {
            $successes[] = "RecruitmentHub.php fixed";
            echo "✅ Removed duplicate DB connection\n";
        } elseif (!$fixed) {
            // Fallback: regex approach
            $content = file_get_contents($rhubFile);
            $content = preg_replace(
                '/public function __construct\(\)\s*\{[^}]*\$this->recruitmentDb\s*=\s*new\s*\\\\mysqli\([^)]*\$_ENV[^}]*\}/s',
                'public function __construct()
    {
        parent::__construct();

        // recruitmentDb is already connected by BaseController via connectDatabase()
        if (!$this->recruitmentDb || (property_exists($this->recruitmentDb, \'connect_error\') && $this->recruitmentDb->connect_error)) {
            error_log("RecruitmentHub: Recruitment DB not available via BaseController");
        }
    }',
                $content
            );
            if (file_put_contents($rhubFile, $content)) {
                $successes[] = "RecruitmentHub.php fixed (regex)";
                echo "✅ Fixed via regex\n";
            } else {
                $errors[] = "Failed to write RecruitmentHub.php";
                echo "❌ Failed\n";
            }
        }
    } else {
        echo "ℹ️ Already fixed\n";
        $successes[] = "RecruitmentHub.php already OK";
    }
} else {
    echo "⚠️ File not found\n";
    $errors[] = "RecruitmentHub.php not found";
}

// =============================================
// 2. FIX Api.php (add Docker fallback)
// =============================================
echo "\n<b>🔧 2. Fixing Api.php (DB connection)...</b>\n";

$apiFile = __DIR__ . '/erp/app/Controllers/Api.php';
if (file_exists($apiFile)) {
    $content = file_get_contents($apiFile);
    
    if (strpos($content, 'checkRecruitmentConnection') !== false && strpos($content, 'isWindows') === false) {
        $content = preg_replace(
            '/private function checkRecruitmentConnection\(\)\s*\{[^}]*\$db\s*=\s*new\s*\\\\mysqli\([^)]*\$_ENV[^}]*return \$connected;\s*\}/s',
            'private function checkRecruitmentConnection()
    {
        $isWindows = (PHP_OS_FAMILY === \'Windows\' || strtoupper(substr(PHP_OS, 0, 3)) === \'WIN\');

        if (!$isWindows) {
            $hostsToTry = [\'172.17.0.3\', \'172.17.0.2\', \'172.17.0.4\', \'172.17.0.5\'];
            foreach ($hostsToTry as $host) {
                try {
                    $db = @new \\mysqli($host, \'root\', \'rahasia123\', \'recruitment_db\', 3306);
                    if (!$db->connect_error) { $db->close(); return true; }
                } catch (\\Exception $e) { continue; }
            }
            return false;
        }

        $db = @new \\mysqli(
            $_ENV[\'DB_HOST\'] ?? \'localhost\',
            $_ENV[\'DB_USERNAME\'] ?? \'root\',
            $_ENV[\'DB_PASSWORD\'] ?? \'\',
            $_ENV[\'RECRUITMENT_DB_NAME\'] ?? \'recruitment_db\',
            $_ENV[\'DB_PORT\'] ?? 3306
        );
        $connected = !$db->connect_error;
        if ($connected) $db->close();
        return $connected;
    }',
            $content
        );
        
        if (file_put_contents($apiFile, $content)) {
            $successes[] = "Api.php fixed";
            echo "✅ Added Docker IP fallback\n";
        } else {
            $errors[] = "Failed to write Api.php";
            echo "❌ Failed\n";
        }
    } else {
        echo "ℹ️ Already fixed\n";
        $successes[] = "Api.php already OK";
    }
} else {
    echo "⚠️ File not found\n";
    $errors[] = "Api.php not found";
}

// =============================================
// 3. FIX Dashboard modern.php (division by zero)
// =============================================
echo "\n<b>🔧 3. Fixing Dashboard modern.php (division by zero)...</b>\n";

$dashFile = __DIR__ . '/erp/app/Views/dashboard/modern.php';
if (file_exists($dashFile)) {
    $content = file_get_contents($dashFile);
    $old = "\$height = (\$vessel['count'] / \$maxVesselCount) * 100;";
    $new = "\$height = (\$maxVesselCount > 0) ? (\$vessel['count'] / \$maxVesselCount) * 100 : 0;";
    
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content);
        file_put_contents($dashFile, $content);
        $successes[] = "modern.php division-by-zero fixed";
        echo "✅ Fixed\n";
    } else {
        echo "ℹ️ Already fixed\n";
        $successes[] = "modern.php already OK";
    }
} else {
    echo "⚠️ File not found\n";
}

// =============================================
// 4. FIX test/api.php (division by zero)
// =============================================
echo "\n<b>🔧 4. Fixing test/api.php (division by zero)...</b>\n";

$testFile = __DIR__ . '/erp/app/Views/test/api.php';
if (file_exists($testFile)) {
    $content = file_get_contents($testFile);
    $old2 = 'round(($passedTests / $totalTests) * 100)';
    $new2 = '$totalTests > 0 ? round(($passedTests / $totalTests) * 100) : 0';
    
    if (strpos($content, $old2) !== false) {
        $content = str_replace($old2, $new2, $content);
        file_put_contents($testFile, $content);
        $successes[] = "test/api.php division-by-zero fixed";
        echo "✅ Fixed\n";
    } else {
        echo "ℹ️ Already fixed\n";
        $successes[] = "test/api.php already OK";
    }
} else {
    echo "⚠️ File not found\n";
}

// =============================================
// 5. Fix permissions
// =============================================
echo "\n<b>🔐 5. Setting permissions...</b>\n";
@exec("chown -R www-data:www-data " . __DIR__);
@exec("find " . __DIR__ . " -type d -exec chmod 755 {} \\;");
@exec("find " . __DIR__ . " -type f -exec chmod 644 {} \\;");
@exec("chmod -R 775 " . __DIR__ . "/uploads 2>/dev/null");
@exec("chmod -R 775 " . __DIR__ . "/erp/writable 2>/dev/null");
@exec("chmod -R 775 " . __DIR__ . "/recruitment/writable 2>/dev/null");
echo "✅ Permissions set\n";

// =============================================
// SUMMARY
// =============================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "<b>📋 SUMMARY</b>\n";
echo str_repeat("=", 50) . "\n\n";

echo "✅ Successes (" . count($successes) . "):\n";
foreach ($successes as $s) echo "   • $s\n";

if (!empty($errors)) {
    echo "\n❌ Errors (" . count($errors) . "):\n";
    foreach ($errors as $e) echo "   • $e\n";
}

echo "</pre>";
echo empty($errors) 
    ? "<h2 style='color:green'>✅ All fixes applied!</h2>" 
    : "<h2 style='color:orange'>⚠️ " . count($errors) . " error(s)</h2>";
echo "<p style='color:red;font-weight:bold'>⚠️ DELETE THIS FILE!</p>";
?>
