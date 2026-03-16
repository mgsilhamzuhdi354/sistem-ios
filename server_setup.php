<?php
/**
 * PT Indo Ocean - Server Setup Fix v2
 * Fixes Apache rewrite by putting rules in VirtualHost (not Directory)
 * Run: php /var/www/html/server_setup.php
 * HAPUS SETELAH SELESAI!
 */

echo "=== PT Indo Ocean - Server Setup Fix v2 ===\n\n";

// 1. Fix apache2.conf - AllowOverride None -> All
$mainConfig = file_get_contents('/etc/apache2/apache2.conf');
if (strpos($mainConfig, 'AllowOverride None') !== false) {
    $mainConfig = str_replace('AllowOverride None', 'AllowOverride All', $mainConfig);
    file_put_contents('/etc/apache2/apache2.conf', $mainConfig);
    echo "[OK] Fixed AllowOverride None -> All in apache2.conf\n";
} else {
    echo "[OK] apache2.conf already has AllowOverride All\n";
}

// 2. Write VirtualHost config with rewrite rules OUTSIDE <Directory>
$apacheConfig = '<VirtualHost *:80>
    DocumentRoot /var/www/html
    ServerName localhost

    # Rewrite rules at VirtualHost level (NOT inside Directory!)
    RewriteEngine On

    # Allow existing files and directories
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} -f [OR]
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} -d
    RewriteRule ^ - [L]

    # ERP API files - direct access
    RewriteRule ^/erp/api_.*\.php$ - [L]

    # ERP routing
    RewriteRule ^/erp/(.*)$ /erp/index.php?url=$1 [L,QSA]

    # Recruitment routing  
    RewriteRule ^/recruitment/(.*)$ /recruitment/public/index.php?url=$1 [L,QSA]

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/html/erp>
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/html/recruitment>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
';

$wrote = file_put_contents('/etc/apache2/sites-available/000-default.conf', $apacheConfig);
echo $wrote ? "[OK] Apache VHost config written ($wrote bytes)\n" : "[FAIL] Could not write Apache config\n";

// 3. Write root .htaccess (backup)
$htaccess = 'DirectoryIndex index.php index.html

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    RewriteRule ^erp/api_.*\.php$ - [L]
    RewriteRule ^erp/(.*)$ erp/index.php?url=$1 [L,QSA]
    RewriteRule ^recruitment/(.*)$ recruitment/public/index.php?url=$1 [L,QSA]
</IfModule>
';

file_put_contents('/var/www/html/.htaccess', $htaccess);
echo "[OK] .htaccess written\n";

// 4. Enable modules
shell_exec("a2enmod rewrite headers 2>&1");
echo "[OK] Modules enabled\n";

// 5. Fix permissions
shell_exec("chown -R www-data:www-data /var/www/html 2>&1");
shell_exec("chmod 644 /var/www/html/.htaccess 2>&1");
echo "[OK] Permissions fixed\n";

// 6. Restart Apache
$restart = shell_exec("service apache2 restart 2>&1");
echo "[OK] Apache restarted: " . trim($restart) . "\n";

// 7. Wait and test
sleep(2);
echo "\n=== Internal Tests ===\n";

$tests = [
    '/erp/index.php'           => 'ERP direct file',
    '/erp/'                    => 'ERP root',
    '/erp/auth/login'          => 'ERP auth/login (rewrite)',
    '/db_diagnose.php'         => 'Diagnose page',
    '/recruitment/public/login'=> 'Recruitment login (rewrite)',
    '/index.html'              => 'Landing page',
];

foreach ($tests as $url => $label) {
    $ch = curl_init("http://localhost$url");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $ok = ($httpCode >= 200 && $httpCode < 400);
    echo ($ok ? '[OK]  ' : '[FAIL]') . " $label ($url) -> HTTP $httpCode\n";
}

echo "\n=== DONE! Hapus file ini setelah selesai. ===\n";
