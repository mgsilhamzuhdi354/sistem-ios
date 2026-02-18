<?php
// Debug page to verify nginx routing
header('Content-Type: text/plain');
echo "=== Recruitment Debug Info ===\n\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'NOT SET') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "\n";
echo "DB_HOST env: " . (getenv('DB_HOST') ?: 'NOT SET') . "\n";
echo "\nThis file is: " . __FILE__ . "\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
