<?php
/**
 * Fix Permissions Script for Docker Container
 * Upload to root of project, access via browser, then DELETE this file.
 * URL: http://your-domain.com/fix_permissions.php
 */
header('Content-Type: text/plain; charset=utf-8');
echo "=== PT Indo Ocean - Permission & Access Fixer ===\n\n";

$baseDir = __DIR__;

// 1. Check current user
echo "--- System Info ---\n";
echo "PHP User  : " . get_current_user() . " (". posix_getuid() . ")\n";
echo "Base Dir  : $baseDir\n";
echo "Server    : " . php_sapi_name() . "\n\n";

// 2. Check .htaccess exists
echo "--- File Check ---\n";
$filesToCheck = [
    '.htaccess',
    'index.html',
    'erp/index.php',
    'erp/.htaccess',
    'erp/app/Config/Database.php',
    'recruitment/public/index.php',
    'recruitment/.htaccess',
    'recruitment/app/Config/Database.php',
];

foreach ($filesToCheck as $f) {
    $path = $baseDir . '/' . $f;
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $owner = posix_getpwuid(fileowner($path));
        $ownerName = $owner ? $owner['name'] : fileowner($path);
        echo "[OK]  $f  (perms: $perms, owner: $ownerName)\n";
    } else {
        echo "[MISSING] $f\n";
    }
}

// 3. Check .htaccess content
echo "\n--- .htaccess Content ---\n";
if (file_exists($baseDir . '/.htaccess')) {
    echo file_get_contents($baseDir . '/.htaccess');
} else {
    echo ".htaccess file NOT FOUND!\n";
    echo "Creating default .htaccess...\n";
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
</FilesMatch>
';
    file_put_contents($baseDir . '/.htaccess', $htaccess);
    echo "[CREATED] .htaccess\n";
}

// 4. Check ERP .htaccess
echo "\n--- ERP .htaccess ---\n";
if (file_exists($baseDir . '/erp/.htaccess')) {
    echo file_get_contents($baseDir . '/erp/.htaccess');
} else {
    echo "ERP .htaccess NOT FOUND! Creating...\n";
    $erpHtaccess = 'Options -Indexes +FollowSymLinks
DirectoryIndex index.php

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /erp/

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [L,QSA]
</IfModule>

# PHP Settings
php_value upload_max_filesize 50M
php_value post_max_size 55M
php_value max_execution_time 300
php_value max_input_time 300
';
    file_put_contents($baseDir . '/erp/.htaccess', $erpHtaccess);
    echo "[CREATED] erp/.htaccess\n";
}

// 5. Test Database Connection
echo "\n--- Database Connection Test ---\n";
// Auto-detect Docker
$isDocker = file_exists('/.dockerenv') || 
            (file_exists('/var/www/html') && !file_exists('/var/run/mysqld/mysqld.sock'));
echo "Is Docker: " . ($isDocker ? 'YES' : 'NO') . "\n";

$host = $isDocker ? '172.17.0.3' : 'localhost';
$user = 'root';
$pass = $isDocker ? 'rahasia123' : '';
$db   = 'erp_db';

echo "Host: $host\n";
echo "User: $user\n";
echo "Pass: " . ($pass ? '********' : '(empty)') . "\n";
echo "DB  : $db\n";

try {
    $mysqli = new mysqli($host, $user, $pass, $db, 3306);
    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }
    echo "[SUCCESS] Database connected! Server: " . $mysqli->server_info . "\n";
    $mysqli->close();
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    
    // Try alternative hosts
    $altHosts = ['mariadb-1', 'mariadb', 'mysql', 'host.docker.internal', '172.17.0.2', '172.17.0.4'];
    echo "\nTrying alternative hosts...\n";
    foreach ($altHosts as $altHost) {
        try {
            $mysqli = @new mysqli($altHost, $user, $pass, $db, 3306);
            if (!$mysqli->connect_error) {
                echo "[FOUND!] Host '$altHost' works! Server: " . $mysqli->server_info . "\n";
                echo "\n>>> UPDATE Database.php: change defaultHost to '$altHost' <<<\n";
                $mysqli->close();
                break;
            }
        } catch (Exception $e2) {
            echo "  - $altHost: FAILED (" . $e2->getMessage() . ")\n";
        }
    }
}

// 6. Check Apache modules
echo "\n--- Apache Modules ---\n";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $important = ['mod_rewrite', 'mod_headers', 'mod_ssl', 'mod_dir'];
    foreach ($important as $mod) {
        echo (in_array($mod, $modules) ? "[OK]" : "[MISSING]") . " $mod\n";
    }
} else {
    echo "Cannot check (not running under Apache handler)\n";
}

echo "\n=== Done ===\n";
echo "SECURITY: Delete this file after use!\n";
?>
