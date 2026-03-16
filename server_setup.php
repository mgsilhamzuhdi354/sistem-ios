<?php
/**
 * PT Indo Ocean - Server Setup Fix Script
 * Jalankan via: php /var/www/html/server_setup.php
 * Atau akses via browser: http://localhost/server_setup.php
 * HAPUS SETELAH SELESAI!
 */

echo "<h2>PT Indo Ocean - Server Setup Fix</h2>\n";

// 1. Write Apache VirtualHost config
$apacheConfig = '<VirtualHost *:80>
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted

        RewriteEngine On

        RewriteCond %{REQUEST_FILENAME} -f [OR]
        RewriteCond %{REQUEST_FILENAME} -d
        RewriteRule ^ - [L]

        RewriteRule ^erp/api_.*\.php$ - [L]
        RewriteRule ^erp/(.*)$ /erp/index.php?url=$1 [L,QSA]
        RewriteRule ^recruitment/(.*)$ /recruitment/public/index.php?url=$1 [L,QSA]
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
echo $wrote ? "✅ Apache config written ($wrote bytes)<br>\n" : "❌ Failed to write Apache config<br>\n";

// 2. Write root .htaccess
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

<FilesMatch "\.(env|log|sql|sh|git)$">
    Require all denied
</FilesMatch>
';

$wrote = file_put_contents('/var/www/html/.htaccess', $htaccess);
echo $wrote ? "✅ Root .htaccess written ($wrote bytes)<br>\n" : "❌ Failed to write .htaccess<br>\n";

// 3. Enable required Apache modules
echo "<br><h3>Apache Modules:</h3>\n";
$modules = ['rewrite', 'headers'];
foreach ($modules as $mod) {
    $output = shell_exec("a2enmod $mod 2>&1");
    echo "Module $mod: $output<br>\n";
}

// 4. Check if modules are actually loaded
$loadedModules = shell_exec("apache2ctl -M 2>/dev/null");
echo "<br><h3>Loaded Modules Check:</h3>\n";
echo "rewrite_module: " . (strpos($loadedModules, 'rewrite') !== false ? '✅ LOADED' : '❌ NOT LOADED') . "<br>\n";
echo "headers_module: " . (strpos($loadedModules, 'headers') !== false ? '✅ LOADED' : '❌ NOT LOADED') . "<br>\n";

// 5. Check Apache main config for AllowOverride
$mainConfig = file_get_contents('/etc/apache2/apache2.conf');
echo "<br><h3>Main Apache Config Check:</h3>\n";
if (strpos($mainConfig, 'AllowOverride None') !== false) {
    echo "⚠️ Found 'AllowOverride None' in apache2.conf - Need to fix!<br>\n";
    // Fix it: replace AllowOverride None with AllowOverride All
    $mainConfig = str_replace('AllowOverride None', 'AllowOverride All', $mainConfig);
    file_put_contents('/etc/apache2/apache2.conf', $mainConfig);
    echo "✅ Changed all 'AllowOverride None' to 'AllowOverride All' in apache2.conf<br>\n";
} else {
    echo "✅ No 'AllowOverride None' found in apache2.conf<br>\n";
}

// 6. Show current apache2.conf Directory sections
preg_match_all('/<Directory[^>]*>.*?<\/Directory>/s', $mainConfig, $matches);
echo "<br><h3>Directory Configs in apache2.conf:</h3>\n";
echo "<pre>" . htmlspecialchars(implode("\n\n", $matches[0])) . "</pre>\n";

// 7. Show sites-enabled
echo "<br><h3>Sites Enabled:</h3>\n";
$sites = shell_exec("ls -la /etc/apache2/sites-enabled/ 2>&1");
echo "<pre>$sites</pre>\n";

// 8. Show current 000-default.conf
echo "<br><h3>Current VHost Config:</h3>\n";
$vhost = file_get_contents('/etc/apache2/sites-enabled/000-default.conf');
echo "<pre>" . htmlspecialchars($vhost) . "</pre>\n";

// 9. Restart Apache
echo "<br><h3>Restarting Apache:</h3>\n";
$restart = shell_exec("service apache2 restart 2>&1");
echo "<pre>$restart</pre>\n";

// 10. Test internal URL access
echo "<br><h3>Internal Test (after restart):</h3>\n";
sleep(1); // Wait for Apache to restart

$tests = [
    '/erp/index.php' => 'ERP direct file',
    '/erp/auth/login' => 'ERP rewrite URL',
    '/db_diagnose.php' => 'Diagnose page',
    '/recruitment/public/login' => 'Recruitment login',
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
    
    $status = ($httpCode >= 200 && $httpCode < 400) ? '✅' : '❌';
    echo "$status $label ($url) → HTTP $httpCode<br>\n";
}

echo "<br><h3>Fix Permissions:</h3>\n";
shell_exec("chown -R www-data:www-data /var/www/html 2>&1");
shell_exec("chmod 644 /var/www/html/.htaccess 2>&1");
echo "✅ Permissions fixed<br>\n";

echo "<br><hr><p>⚠️ <strong>HAPUS FILE INI SETELAH SELESAI!</strong></p>\n";
