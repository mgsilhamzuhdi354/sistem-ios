<?php
/**
 * PT Indo Ocean - Root Entry Point
 * Serves the portal page or redirects to ERP
 */

// If accessing root, show the portal landing page
$portalFile = __DIR__ . '/index.html';
if (file_exists($portalFile)) {
    readfile($portalFile);
    exit;
}

// Fallback: redirect to ERP
header('Location: /erp/');
exit;
