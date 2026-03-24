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

// Load Composer Autoload & Environment Variables
if (file_exists(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
    
    // Load .env file if dotenv is available
    if (class_exists('Dotenv\\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(BASEPATH);
        $dotenv->safeLoad();
    }
}

// Dynamic BASE_URL detection
// Check multiple sources for HTTPS (Cloudflare terminates SSL and forwards via HTTP)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (!$isHttps && !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $isHttps = (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
}
if (!$isHttps && !empty($_SERVER['HTTP_CF_VISITOR'])) {
    $cfVisitor = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
    $isHttps = (($cfVisitor['scheme'] ?? '') === 'https');
}
$protocol = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Get base path - handle Apache rewrite where SCRIPT_NAME may be the original URL
$isWindows = (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

if (!$isWindows) {
    // Docker/NAS: SCRIPT_NAME might show original URL, not rewritten file
    // Use SCRIPT_FILENAME to get the real PHP file being executed
    $scriptFile = $_SERVER['SCRIPT_FILENAME'] ?? '';
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '/var/www/html';
    if (!empty($scriptFile) && !empty($docRoot)) {
        $scriptName = str_replace($docRoot, '', $scriptFile);
        $scriptName = '/' . ltrim(str_replace('\\', '/', $scriptName), '/');
    }
}

$scriptDir = dirname($scriptName);
$scriptDir = str_replace('\\', '/', $scriptDir);
$basePath = rtrim($scriptDir, '/') . '/';

define('BASE_URL', $protocol . '://' . $host . $basePath);



// Error reporting (disable in production)
error_reporting(E_ALL);
// For AJAX requests, suppress display_errors to prevent PHP warnings from corrupting JSON responses
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
} else {
    ini_set('display_errors', 1);
}

// Start session with ngrok-compatible settings
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
if ($isHttps) {
    ini_set('session.cookie_secure', 1);
}
session_start();

// Load constants and helpers
require_once APPPATH . 'Config/Constants.php';
require_once APPPATH . 'Helpers/common.php';
require_once APPPATH . 'Helpers/lang_helper.php';

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

// Load routes configuration
$controllerMap = require APPPATH . 'Config/Routes.php';
$controllerName = $controllerMap[$controllerName] ?? $controllerName;
$controllerFile = APPPATH . 'Controllers/' . $controllerName . '.php';

// Check if controller exists
if (file_exists($controllerFile)) {
    require_once APPPATH . 'Controllers/BaseController.php';
    require_once $controllerFile;

    $controllerClass = 'App\\Controllers\\' . $controllerName;
    $controller = new $controllerClass();

    // Remap method names that conflict with protected BaseController methods
    if ($method === 'view') {
        if ($controllerName === 'Contract') {
            $method = 'viewContract';
        } else {
            // For all other controllers (Crew, etc.), 'view' maps to 'show'
            $method = 'show';
        }
    }

    // Check if method exists and is public
    if (method_exists($controller, $method)) {
        $ref = new ReflectionMethod($controller, $method);
        if ($ref->isPublic()) {
            echo call_user_func_array([$controller, $method], $params);
        } else {
            http_response_code(403);
            echo "Method not accessible: $method in $controllerName";
        }
    } else {
        http_response_code(404);
        echo "Method not found: $method in $controllerName";
    }
} else {
    http_response_code(404);
    echo "Controller not found: $controllerName";
}
