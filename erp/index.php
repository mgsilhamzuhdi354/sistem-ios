<?php
/**
 * PT Indo Ocean - ERP System
 * Main Entry Point
 */

// Define paths
define('BASEPATH', __DIR__ . '/');
define('APPPATH', BASEPATH . 'app/');
define('WRITEPATH', BASEPATH . 'writable/');
define('FCPATH', BASEPATH);

// Dynamic BASE_URL detection (works with localhost and production)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Detect environment
$isProduction = (
    strpos($host, 'localhost') === false &&
    strpos($host, '127.0.0.1') === false
);

// Production: /erp/ | Local: /PT_indoocean/erp/
$basePath = $isProduction ? '/erp/' : '/PT_indoocean/erp/';
define('BASE_URL', $protocol . '://' . $host . $basePath);


// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session with ngrok-compatible settings
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
if ($isHttps) {
    ini_set('session.cookie_secure', 1);
}
session_start();

// Load constants
require_once APPPATH . 'Config/Constants.php';

// Simple Router with numeric ID support
$uri = $_GET['url'] ?? '';
$uri = trim($uri, '/');
$segments = $uri ? explode('/', $uri) : [];

// Default controller and method
$controllerName = !empty($segments[0]) ? ucfirst($segments[0]) : 'Dashboard';
$method = 'index';
$params = [];

// Parse segments - if second segment is numeric, treat it as ID for 'show' method
if (isset($segments[1])) {
    if (is_numeric($segments[1])) {
        // /contracts/2 → Contract::show(2)
        $method = 'show';
        $params = [$segments[1]];
        // Check for additional method after ID (e.g., /contracts/edit/2)
    } else {
        // /contracts/create or /contracts/edit/2
        $method = $segments[1];
        $params = array_slice($segments, 2);
    }
}

// Convert kebab-case to camelCase for method names (e.g., mark-read → markRead)
if (strpos($method, '-') !== false) {
    $method = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $method))));
}

// Map controller name
$controllerMap = [
    'Dashboard' => 'Dashboard',
    'Contracts' => 'Contract',
    'Contract' => 'Contract',
    'Vessels' => 'Vessel',
    'Vessel' => 'Vessel',
    'Clients' => 'Client',
    'Client' => 'Client',
    'Payroll' => 'Payroll',
    'Reports' => 'Report',
    'Report' => 'Report',
    'Settings' => 'Settings',
    'Notifications' => 'Notification',
    'Notification' => 'Notification',
    'Api' => 'Api',
    // Authentication & User Management
    'Auth' => 'Auth',
    'Users' => 'UserManagement',
    'User' => 'UserManagement',
    // Crew & Document Management
    'Crews' => 'Crew',
    'Crew' => 'Crew',
    'Documents' => 'CrewDocument',
    'Document' => 'CrewDocument',
];

$controllerName = $controllerMap[$controllerName] ?? $controllerName;
$controllerFile = APPPATH . 'Controllers/' . $controllerName . '.php';

// Check if controller exists
if (file_exists($controllerFile)) {
    require_once APPPATH . 'Controllers/BaseController.php';
    require_once $controllerFile;
    
    $controllerClass = 'App\\Controllers\\' . $controllerName;
    $controller = new $controllerClass();
    
    // Check if method exists
    if (method_exists($controller, $method)) {
        echo call_user_func_array([$controller, $method], $params);
    } else {
        http_response_code(404);
        echo "Method not found: $method in $controllerName";
    }
} else {
    http_response_code(404);
    echo "Controller not found: $controllerName";
}
