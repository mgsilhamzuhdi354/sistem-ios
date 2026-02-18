<?php
// Fallback index.php for recruitment folder
// This handles requests if .htaccess mod_rewrite is not working or not allowed

// Check if public/index.php exists
if (file_exists(__DIR__ . '/public/index.php')) {
    // If we are here, it means the rewrite rule in .htaccess didn't work
    // or the server is configured to look for index.php in the root.
    // We can simply include the public index.php
    
    // Set the current directory to public so paths work correctly
    chdir(__DIR__ . '/public');
    
    // Require the public index
    require __DIR__ . '/public/index.php';
} else {
    echo "System configuration error: public/index.php not found.";
}
