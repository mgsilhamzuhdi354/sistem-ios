<?php
// Quick permission fix - upload this to NAS root (/var/www/html/)
header('Content-Type: text/plain');
echo "=== FIXING PERMISSIONS ===\n\n";

$root = __DIR__;
echo "Root: $root\n\n";

// Fix directory permissions
$cmd1 = "find $root -type d -exec chmod 755 {} \\;";
echo "Running: $cmd1\n";
exec($cmd1, $out1, $ret1);
echo "Result: " . ($ret1 === 0 ? "OK" : "Error $ret1") . "\n\n";

// Fix file permissions  
$cmd2 = "find $root -type f -exec chmod 644 {} \\;";
echo "Running: $cmd2\n";
exec($cmd2, $out2, $ret2);
echo "Result: " . ($ret2 === 0 ? "OK" : "Error $ret2") . "\n\n";

// Fix .htaccess specifically
@chmod("$root/.htaccess", 0644);
@chmod("$root/index.html", 0644);

// Check what files exist
echo "=== FILES IN ROOT ===\n";
foreach (scandir($root) as $f) {
    if ($f === '.' || $f === '..') continue;
    $perms = substr(sprintf('%o', fileperms("$root/$f")), -4);
    $type = is_dir("$root/$f") ? "[DIR]" : "[FILE]";
    echo "$type $perms $f\n";
}

echo "\n=== DONE ===\n";
echo "Now try: https://indooceancrewservice.com/\n";
echo "DELETE THIS FILE AFTER!\n";
?>
