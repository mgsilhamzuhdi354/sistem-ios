<?php
/**
 * Standalone Payslip API - bypasses routing
 * Usage: api_payslip.php?action=get&id=2
 */

// Bootstrap
define('BASEPATH', __DIR__ . '/');
define('APPPATH', BASEPATH . 'app/');
define('WRITEPATH', BASEPATH . 'writable/');
define('FCPATH', BASEPATH);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptDir, '/') . '/';
define('BASE_URL', $protocol . '://' . $host . $basePath);

error_reporting(E_ALL);
ini_set('display_errors', 0);

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
if ($isHttps) ini_set('session.cookie_secure', 1);
session_start();

// Must be logged in
if (empty($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Load config
require_once APPPATH . 'Config/Constants.php';
if (file_exists(APPPATH . 'Helpers/common.php')) require_once APPPATH . 'Helpers/common.php';

// DB Connection
$dbConfigAll = require APPPATH . 'Config/Database.php';
$dbConfig = $dbConfigAll['default'] ?? $dbConfigAll;
$db = new mysqli(
    $dbConfig['hostname'] ?? $dbConfig['host'] ?? 'localhost',
    $dbConfig['username'] ?? 'root',
    $dbConfig['password'] ?? '',
    $dbConfig['database'] ?? 'erp_db',
    $dbConfig['port'] ?? 3306
);
if ($db->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $db->connect_error]);
    exit;
}
$db->set_charset('utf8mb4');

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($action === 'get' && $id > 0) {
    // Get payslip data with all JOINs
    $sql = "SELECT pi.*, 
                   c.contract_no, c.crew_id, c.crew_name AS contract_crew_name,
                   cr.email, cr.full_name,
                   COALESCE(pi.crew_name, c.crew_name, cr.full_name) AS crew_name,
                   COALESCE(pi.rank_name, r.name) AS rank_name,
                   COALESCE(pi.vessel_name, v.name) AS vessel_name,
                   COALESCE(pi.bank_holder, cr.bank_holder, c.crew_name) AS bank_holder,
                   COALESCE(pi.bank_account, cr.bank_account) AS bank_account,
                   COALESCE(pi.bank_name, cr.bank_name) AS bank_name,
                   COALESCE(pi.original_currency, cur.code, 'IDR') AS original_currency,
                   COALESCE(NULLIF(pi.original_basic, 0), cs.basic_salary, 0) AS original_basic,
                   COALESCE(NULLIF(pi.original_overtime, 0), cs.overtime_allowance, 0) AS original_overtime,
                   COALESCE(NULLIF(cs.exchange_rate, 0), IF(pi.exchange_rate > 0 AND pi.exchange_rate < 1, ROUND(1/pi.exchange_rate), pi.exchange_rate), 1) AS exchange_rate,
                   cs.leave_pay AS contract_leave_pay,
                   cs.bonus AS contract_bonus,
                   cs.other_allowance AS contract_other_allowance,
                   cs.total_monthly AS contract_total_monthly
            FROM payroll_items pi
            LEFT JOIN contracts c ON pi.contract_id = c.id
            LEFT JOIN crews cr ON c.crew_id = cr.id
            LEFT JOIN ranks r ON c.rank_id = r.id
            LEFT JOIN vessels v ON c.vessel_id = v.id
            LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
            LEFT JOIN currencies cur ON cs.currency_id = cur.id
            WHERE pi.id = ?";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Item not found']);
        exit;
    }
    
    // Get period
    $periodId = $item['payroll_period_id'];
    $pstmt = $db->prepare("SELECT * FROM payroll_periods WHERE id = ?");
    $pstmt->bind_param('i', $periodId);
    $pstmt->execute();
    $period = $pstmt->get_result()->fetch_assoc();
    $pstmt->close();
    
    echo json_encode(['success' => true, 'item' => $item, 'period' => $period]);

} elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = intval($_POST['item_id'] ?? 0);
    if (!$itemId) {
        echo json_encode(['success' => false, 'message' => 'Missing item_id']);
        exit;
    }
    
    $fields = [
        'original_basic' => floatval($_POST['original_basic'] ?? 0),
        'original_overtime' => floatval($_POST['original_overtime'] ?? 0),
        'reimbursement' => floatval($_POST['reimbursement'] ?? 0),
        'loans' => floatval($_POST['loans'] ?? 0),
        'exchange_rate' => floatval($_POST['exchange_rate'] ?? 0),
        'admin_bank_fee' => floatval($_POST['admin_bank_fee'] ?? 0),
        'insurance' => floatval($_POST['insurance'] ?? 0),
        'other_deductions' => floatval($_POST['other_deductions'] ?? 0),
        'tax_rate' => floatval($_POST['tax_rate'] ?? 0),
    ];
    
    $setParts = [];
    $types = '';
    $values = [];
    foreach ($fields as $col => $val) {
        $setParts[] = "$col = ?";
        $types .= 'd';
        $values[] = $val;
    }
    $types .= 'i';
    $values[] = $itemId;
    
    $sql = "UPDATE payroll_items SET " . implode(', ', $setParts) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => $result, 'message' => $result ? 'Saved' : 'Failed']);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action. Use ?action=get&id=X or POST action=update']);
}

$db->close();
