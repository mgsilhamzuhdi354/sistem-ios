<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Main Entry Point
 */

// Define paths
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APPPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
define('WRITEPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR);

// ============================================
// ROLE CONSTANTS - Matches Database Structure
// ============================================
define('ROLE_ADMIN', 1);          // Admin - Job Vacancy
define('ROLE_HR_STAFF', 2);       // HR Staff
define('ROLE_APPLICANT', 3);      // Job Applicant
define('ROLE_LEADER', 4);         // Leader
define('ROLE_CREWING', 5);        // Crewing Staff
define('ROLE_MASTER_ADMIN', 11);  // Master Admin - Full Access

// ============================================
// APPLICATION STATUS CONSTANTS
// ============================================
define('STATUS_NEW', 1);           // New Application
define('STATUS_DOCUMENT_REVIEW', 2); // Document Review
define('STATUS_INTERVIEW', 3);     // Interview Stage
define('STATUS_UNDER_REVIEW', 4);  // Under Review
define('STATUS_SHORTLISTED', 5);   // Shortlisted
define('STATUS_APPROVED', 6);      // Approved / Hired
define('STATUS_REJECTED', 7);      // Rejected
define('STATUS_ARCHIVED', 8);      // Archived

// Start session
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$appConfig = require APPPATH . 'Config/App.php';
$dbConfig = require APPPATH . 'Config/Database.php';
$routes = require APPPATH . 'Config/Routes.php';

// Detect environment for URL generation
$isWindows = (PHP_OS_FAMILY === 'Windows' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLaragonPrettyUrl = (strpos($host, '.test') !== false || strpos($host, '.local') !== false);
// Direct domain: Linux/Docker OR Windows Laragon (.test) — NOT Windows localhost
$isDirectDomain = (!$isWindows || $isLaragonPrettyUrl);

// Database connection
function getDB() {
    global $dbConfig;
    static $conn = null;
    
    if ($conn === null) {
        $config = $dbConfig['default'];
        $conn = new mysqli(
            $config['hostname'],
            $config['username'],
            $config['password'],
            $config['database'],
            $config['port']
        );
        
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset($config['charset']);
    }
    
    return $conn;
}

// Simple Router
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Helper to get relative base path
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$scriptDir = str_replace('\\', '/', $scriptDir); // normalize windows paths
$basePath = rtrim($scriptDir, '/');

// Check if we are being accessed via rewritten URL (e.g. /recruitment/ instead of /recruitment/public/index.php)
if (strpos($requestUri, $basePath) !== 0) {
    // Maybe we are in the parent folder via rewrite?
    $parentDir = dirname($basePath);
    if (strpos($requestUri, $parentDir) === 0) {
       $basePath = $parentDir;
    }
}

// Ensure base path is correctly set for Windows local dev
if ($isWindows && strpos($basePath, '/PT_indoocean') === false && strpos($_SERVER['REQUEST_URI'], '/PT_indoocean') !== false) {
    // Windows localhost subfolder
    $basePath = '/PT_indoocean/recruitment/public';
}

$requestUri = str_replace($basePath, '', $requestUri);
$requestUri = $requestUri ?: '/';

// Load Language Helper
require_once APPPATH . 'Helpers/Language.php';

// Load user's language preference if logged in
if (isLoggedIn()) {
    $db = getDB();
    $stmt = $db->prepare("SELECT language FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result) {
        $_SESSION['user_language'] = $result['language'] ?? 'id';
        $_SESSION['language'] = $result['language'] ?? 'id';
    }
}

// Helper Functions
function view($template, $data = []) {
    extract($data);
    $viewPath = APPPATH . 'Views/' . $template . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        echo "View not found: " . $template;
    }
}

function redirect($url) {
    // Auto-prepend base path if URL starts with / and doesn't already include it
    global $isDirectDomain;
    $base = $isDirectDomain ? '/recruitment/public' : '/PT_indoocean/recruitment/public';
    if (strpos($url, '/') === 0 && strpos($url, $base) !== 0) {
        $url = $base . $url;
    }
    header("Location: " . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// ============================================
// PERMISSION SYSTEM - Dynamic RBAC
// ============================================

/**
 * Get user permissions from database and cache in session
 */
function getUserPermissions() {
    if (!isLoggedIn()) return [];
    
    // Return cached permissions if available
    if (isset($_SESSION['permissions'])) {
        return $_SESSION['permissions'];
    }
    
    $db = getDB();
    $roleId = $_SESSION['role_id'];
    
    // Master Admin has all permissions
    if ($roleId == ROLE_MASTER_ADMIN) {
        $result = $db->query("SELECT name FROM permissions");
        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['name'];
        }
        $_SESSION['permissions'] = $permissions;
        return $permissions;
    }
    
    // Get permissions for this role
    $stmt = $db->prepare("
        SELECT p.name 
        FROM permissions p
        JOIN role_permissions rp ON p.id = rp.permission_id
        WHERE rp.role_id = ?
    ");
    $stmt->bind_param('i', $roleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row['name'];
    }
    
    $_SESSION['permissions'] = $permissions;
    return $permissions;
}

/**
 * Check if current user has a specific permission
 */
function hasPermission($permissionName) {
    if (!isLoggedIn()) return false;
    
    // Master Admin always has permission
    if ($_SESSION['role_id'] == ROLE_MASTER_ADMIN) {
        return true;
    }
    
    $permissions = getUserPermissions();
    return in_array($permissionName, $permissions);
}

/**
 * Clear permission cache (call after role change)
 */
function clearPermissionCache() {
    unset($_SESSION['permissions']);
}

// ============================================
// ROLE CHECK FUNCTIONS (Legacy + New)
// ============================================

function isAdmin() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_ADMIN;
}

function isMasterAdmin() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_MASTER_ADMIN;
}

function isLeader() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_LEADER;
}

function isCrewingPIC() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_CREWING;
}

function isCrewing() {
    // Crewing and Crewing PIC merged to ROLE_CREWING (5)
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_CREWING;
}

function isLeaderOrAbove() {
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_LEADER]);
}

function isCrewingPICOrAbove() {
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_LEADER, ROLE_CREWING]);
}

function isMasterAdminOrLeader() {
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_LEADER]);
}

function isCrewingOrAdmin() {
    // Any staff role (not applicant)
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_ADMIN, ROLE_LEADER, ROLE_CREWING]);
}

function canApproveRequests() {
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_LEADER]);
}

function canTransferHandler() {
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_LEADER]);
}

function canManageVacancy() {
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_ADMIN]);
}

function canCreateVacancy() {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_MASTER_ADMIN;
}

function canViewVacancy() {
    return isset($_SESSION['role_id']) && in_array($_SESSION['role_id'], [ROLE_MASTER_ADMIN, ROLE_ADMIN, ROLE_CREWING]);
}

function getCrewingId() {
    if (isCrewing()) {
        return $_SESSION['user_id'];
    }
    return null;
}

function getCrewingProfile($userId = null) {
    $db = getDB();
    $userId = $userId ?: $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT cp.*, u.full_name, u.email FROM crewing_profiles cp JOIN users u ON cp.user_id = u.id WHERE cp.user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getAllCrewingStaff() {
    $db = getDB();
    $result = $db->query("
        SELECT u.id, u.full_name, u.email, cp.employee_id, cp.max_applications,
               COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_assignments
        FROM users u
        LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
        LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
        WHERE u.role_id = (SELECT id FROM roles WHERE name = 'crewing')
        AND u.is_active = 1
        GROUP BY u.id
        ORDER BY u.full_name
    ");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getCrewingWithLowestWorkload() {
    $db = getDB();
    $result = $db->query("
        SELECT u.id, u.full_name, cp.max_applications,
               COUNT(DISTINCT CASE WHEN aa.status = 'active' THEN aa.id END) as active_count
        FROM users u
        LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
        LEFT JOIN application_assignments aa ON u.id = aa.assigned_to
        WHERE u.role_id = (SELECT id FROM roles WHERE name = 'crewing')
        AND u.is_active = 1
        GROUP BY u.id
        HAVING active_count < COALESCE(cp.max_applications, 50)
        ORDER BY active_count ASC
        LIMIT 1
    ");
    return $result ? $result->fetch_assoc() : null;
}

function autoAssignApplication($applicationId, $assignedBy = null) {
    $db = getDB();
    
    // Check if auto-assign is enabled
    $setting = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'auto_assign_new_applications'")->fetch_assoc();
    if (!$setting || $setting['setting_value'] !== 'true') {
        return false;
    }
    
    // Get crewing with lowest workload
    $crewing = getCrewingWithLowestWorkload();
    if (!$crewing) {
        return false;
    }
    
    $assignedBy = $assignedBy ?: ($crewing['id']); // Self-assign if no assigner
    
    // Create assignment
    $stmt = $db->prepare("
        INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status)
        VALUES (?, ?, ?, 'Auto-assigned by system', 'active')
    ");
    $stmt->bind_param('iii', $applicationId, $crewing['id'], $assignedBy);
    
    if ($stmt->execute()) {
        // Update application with current crewing
        $updateStmt = $db->prepare("UPDATE applications SET current_crewing_id = ?, auto_assigned = 1 WHERE id = ?");
        $updateStmt->bind_param('ii', $crewing['id'], $applicationId);
        $updateStmt->execute();
        
        // Log automation
        logAutomation('assignment', 'applications', $applicationId, 'auto_assign', [
            'assigned_to' => $crewing['id'],
            'crewing_name' => $crewing['full_name']
        ]);
        
        // Notify crewing
        notifyUser($crewing['id'], 'New Application Assigned', 'A new application has been auto-assigned to you.', 'info', url('/crewing/applications'));
        
        return true;
    }
    
    return false;
}

function logAutomation($type, $targetTable, $targetId, $action, $details = []) {
    $db = getDB();
    $detailsJson = json_encode($details);
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    $stmt = $db->prepare("
        INSERT INTO automation_logs (type, target_table, target_id, action, details, created_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('ssissi', $type, $targetTable, $targetId, $action, $detailsJson, $userId);
    $stmt->execute();
}

function notifyUser($userId, $title, $message, $type = 'info', $actionUrl = null) {
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, title, message, type, action_url, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param('issss', $userId, $title, $message, $type, $actionUrl);
    return $stmt->execute();
}

function getSetting($key, $default = null) {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['setting_value'] : $default;
}

function currentUser() {
    if (!isLoggedIn()) return null;
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Leader Profile Functions
function getLeaderProfile($userId = null) {
    $db = getDB();
    $userId = $userId ?: $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT lp.*, u.full_name, u.email FROM leader_profiles lp JOIN users u ON lp.user_id = u.id WHERE lp.user_id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getAllLeaders() {
    $db = getDB();
    $result = $db->query("
        SELECT u.id, u.full_name, u.email, u.is_online, u.last_activity,
               lp.department, lp.employee_id
        FROM users u
        LEFT JOIN leader_profiles lp ON u.id = lp.user_id
        WHERE u.role_id = 4 AND u.is_active = 1
        ORDER BY u.full_name
    ");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Online Status Functions
function updateOnlineStatus() {
    if (!isLoggedIn()) return;
    $db = getDB();
    $userId = $_SESSION['user_id'];
    $stmt = $db->prepare("UPDATE users SET is_online = 1, last_activity = NOW() WHERE id = ?");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
}

function getOnlineCrewingStaff() {
    $db = getDB();
    $result = $db->query("
        SELECT u.id, u.full_name, u.email, u.last_activity,
               cp.rank, cp.company
        FROM users u
        LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
        WHERE u.role_id = 2 AND u.is_active = 1 AND u.is_online = 1
        ORDER BY u.full_name
    ");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Pipeline Request Functions
function getPendingRequests($leaderId = null) {
    $db = getDB();
    $sql = "
        SELECT pr.*, 
               a.id as app_id, u_applicant.full_name as applicant_name,
               u_crewing.full_name as crewing_name,
               fs.name as from_status_name, ts.name as to_status_name,
               jv.title as vacancy_title
        FROM pipeline_requests pr
        JOIN applications a ON pr.application_id = a.id
        JOIN users u_applicant ON a.user_id = u_applicant.id
        JOIN users u_crewing ON pr.requested_by = u_crewing.id
        JOIN application_statuses fs ON pr.from_status_id = fs.id
        JOIN application_statuses ts ON pr.to_status_id = ts.id
        LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
        WHERE pr.status = 'pending'
    ";
    if ($leaderId) {
        $sql .= " AND pr.assigned_to = ?";
    }
    $sql .= " ORDER BY pr.created_at DESC";
    
    if ($leaderId) {
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $leaderId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query($sql);
    }
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function approvePipelineRequest($requestId, $notes = null) {
    $db = getDB();
    $userId = $_SESSION['user_id'];
    
    // Get request details
    $stmt = $db->prepare("SELECT * FROM pipeline_requests WHERE id = ? AND status = 'pending'");
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    
    if (!$request) return false;
    
    // Update request
    $stmt = $db->prepare("UPDATE pipeline_requests SET status = 'approved', response_notes = ?, responded_by = ?, responded_at = NOW() WHERE id = ?");
    $stmt->bind_param('sii', $notes, $userId, $requestId);
    $stmt->execute();
    
    // Update application status
    $stmt = $db->prepare("UPDATE applications SET status_id = ?, status_updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('ii', $request['to_status_id'], $request['application_id']);
    $stmt->execute();
    
    // Notify crewing
    notifyUser($request['requested_by'], 'Request Approved', 'Your pipeline request has been approved.', 'success');
    
    return true;
}

function rejectPipelineRequest($requestId, $notes) {
    $db = getDB();
    $userId = $_SESSION['user_id'];
    
    $stmt = $db->prepare("SELECT * FROM pipeline_requests WHERE id = ? AND status = 'pending'");
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    
    if (!$request) return false;
    
    $stmt = $db->prepare("UPDATE pipeline_requests SET status = 'rejected', response_notes = ?, responded_by = ?, responded_at = NOW() WHERE id = ?");
    $stmt->bind_param('sii', $notes, $userId, $requestId);
    $stmt->execute();
    
    // Notify crewing
    notifyUser($request['requested_by'], 'Request Rejected', 'Your pipeline request was rejected. Reason: ' . $notes, 'warning');
    
    return true;
}

function transferHandler($applicationId, $fromCrewingId, $toCrewingId, $reason = null) {
    $db = getDB();
    $transferredBy = $_SESSION['user_id'];
    
    // Log transfer
    $stmt = $db->prepare("INSERT INTO handler_transfers (application_id, from_crewing_id, to_crewing_id, transferred_by, reason) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiis', $applicationId, $fromCrewingId, $toCrewingId, $transferredBy, $reason);
    $stmt->execute();
    
    // Update application
    $stmt = $db->prepare("UPDATE applications SET current_crewing_id = ? WHERE id = ?");
    $stmt->bind_param('ii', $toCrewingId, $applicationId);
    $stmt->execute();
    
    // Update assignment
    $transferStmt = $db->prepare("UPDATE application_assignments SET status = 'transferred' WHERE application_id = ? AND status = 'active'");
    $transferStmt->bind_param('i', $applicationId);
    $transferStmt->execute();
    
    $stmt = $db->prepare("INSERT INTO application_assignments (application_id, assigned_to, assigned_by, status, notes) VALUES (?, ?, ?, 'active', ?)");
    $reason = $reason ?: 'Transferred by leader';
    $stmt->bind_param('iiis', $applicationId, $toCrewingId, $transferredBy, $reason);
    $stmt->execute();
    
    // Notify the new crewing
    notifyUser($toCrewingId, 'Application Transferred', 'An application has been transferred to you.', 'info');
    
    return true;
}

// Pipeline Request Functions
function createPipelineRequest($applicationId, $fromStatus, $toStatus, $reason = null) {
    $db = getDB();
    $requestedBy = $_SESSION['user_id'];
    
    // Get leader to assign request to (first available leader)
    $leaders = getAllLeaders();
    if (empty($leaders)) return false;
    $assignedTo = $leaders[0]['id'];
    
    $stmt = $db->prepare("
        INSERT INTO pipeline_requests (application_id, requested_by, assigned_to, from_status_id, to_status_id, reason)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiiiss', $applicationId, $requestedBy, $assignedTo, $fromStatus, $toStatus, $reason);
    
    if ($stmt->execute()) {
        // Notify leader
        notifyUser($assignedTo, 'Pipeline Request', 'A crewing staff has requested a pipeline status change.', 'warning', url('/leader/requests'));
        return true;
    }
    return false;
}

// Old duplicate functions removed - now using versions at lines 283-405

// Crewing Rating Functions
function rateCrewing($crewingId, $applicationId, $rating, $comment = null) {
    $db = getDB();
    $applicantId = $_SESSION['user_id'];
    
    $stmt = $db->prepare("
        INSERT INTO crewing_ratings (crewing_id, applicant_id, application_id, rating, comment)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE rating = ?, comment = ?
    ");
    $stmt->bind_param('iiiisis', $crewingId, $applicantId, $applicationId, $rating, $comment, $rating, $comment);
    return $stmt->execute();
}

function getCrewingRating($crewingId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM crewing_ratings WHERE crewing_id = ?");
    $stmt->bind_param('i', $crewingId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function validate_csrf() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
}

function flash($key, $value = null) {
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
    } else {
        $val = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $val;
    }
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function asset($path) {
    global $isDirectDomain;
    $base = $isDirectDomain ? '/recruitment/public/assets/' : '/PT_indoocean/recruitment/public/assets/';
    return $base . ltrim($path, '/');
}

function url($path = '') {
    global $isDirectDomain;
    $base = $isDirectDomain ? '/recruitment/public' : '/PT_indoocean/recruitment/public';
    return $base . $path;
}

/**
 * Encrypt SMTP password for secure storage
 * Uses AES-128-CBC encryption with a key from environment or default
 */
function encryptSmtpPassword($password) {
    if (empty($password)) return '';
    $key = getenv('ENCRYPTION_KEY') ?: 'PT_IndoOcean_2026_Key';  // Change this in production
    $iv = substr(md5($key), 0, 16);
    return base64_encode(openssl_encrypt($password, 'AES-128-CBC', $key, 0, $iv));
}

/**
 * Decrypt SMTP password from database
 */
function decryptSmtpPassword($encrypted) {
    if (empty($encrypted)) return '';
    $key = getenv('ENCRYPTION_KEY') ?: 'PT_IndoOcean_2026_Key';  // Must match encryption key
    $iv = substr(md5($key), 0, 16);
    return openssl_decrypt(base64_decode($encrypted), 'AES-128-CBC', $key, 0, $iv);
}

// Route matcher
function matchRoute($routes, $method, $uri) {
    if (!isset($routes[$method])) return null;
    
    foreach ($routes[$method] as $pattern => $handler) {
        // Convert route pattern to regex
        $regex = preg_replace('/\(:num\)/', '(\d+)', $pattern);
        $regex = preg_replace('/\(:any\)/', '([^/]+)', $regex);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches);
            return ['handler' => $handler, 'params' => $matches];
        }
    }
    return null;
}

// Match current route
$route = matchRoute($routes, $requestMethod, $requestUri);

if ($route) {
    $handlerParts = explode('::', $route['handler']);
    $controllerName = $handlerParts[0];
    $methodPart = $handlerParts[1] ?? 'index';
    
    // Remove parameter placeholders like /$1, /$2 from method name
    $methodName = preg_replace('/\/\$\d+/', '', $methodPart);
    
    // Build controller path
    $controllerPath = APPPATH . 'Controllers/' . str_replace('/', DIRECTORY_SEPARATOR, $controllerName) . '.php';
    
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        
        // Get just the class name (last part after /)
        $classNameParts = explode('/', $controllerName);
        $className = end($classNameParts);
        
        $controller = new $className();
        
        call_user_func_array([$controller, $methodName], $route['params']);
    } else {
        // Show 404
        http_response_code(404);
        view('errors/404');
    }
} else {
    // Show 404
    http_response_code(404);
    view('errors/404');
}

