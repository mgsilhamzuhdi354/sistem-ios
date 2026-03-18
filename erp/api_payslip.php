<?php
/**
 * Standalone Payslip API - bypasses routing
 * Usage: api_payslip.php?action=get&id=2
 *        POST api_payslip.php?action=update
 *        POST api_payslip.php?action=send_email
 */

// Bootstrap
define('BASEPATH', __DIR__ . '/');
define('APPPATH', BASEPATH . 'app/');
define('WRITEPATH', BASEPATH . 'writable/');
define('FCPATH', BASEPATH);

// Load Composer Autoload (for DOMPDF)
if (file_exists(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
}

// Ensure payslips directory exists
$payslipsDir = WRITEPATH . 'payslips/';
if (!is_dir($payslipsDir)) {
    @mkdir($payslipsDir, 0755, true);
}

$isHttpsProto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (!$isHttpsProto && !empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $isHttpsProto = (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
}
if (!$isHttpsProto && !empty($_SERVER['HTTP_CF_VISITOR'])) {
    $cfv = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
    $isHttpsProto = (($cfv['scheme'] ?? '') === 'https');
}
$protocol = $isHttpsProto ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptDir, '/') . '/';
define('BASE_URL', $protocol . '://' . $host . $basePath);

error_reporting(E_ALL);
ini_set('display_errors', 0);

$isHttps = $isHttpsProto;
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_path', '/');
if ($isHttps) ini_set('session.cookie_secure', 1);
session_start();

// Must be logged in - Auth stores in $_SESSION['user']['id']
if (empty($_SESSION['user']['id'])) {
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

// Auto-migrate: ensure required columns exist and correct types
$migrateCols = [
    'confirmed_at' => 'DATETIME NULL',
    'email_sent_at' => 'DATETIME NULL',
    'email_status' => "VARCHAR(20) DEFAULT 'pending'",
    'overtime_allowance' => 'DECIMAL(12,2) DEFAULT 0',
    'pdf_path' => 'VARCHAR(255) NULL',
];
$existing = [];
$colCheck = $db->query("SHOW COLUMNS FROM payroll_items");
if ($colCheck) {
    while ($r = $colCheck->fetch_assoc()) {
        $existing[] = $r['Field'];
        // Fix exchange_rate column if it's too small for kurs values like 15900
        if ($r['Field'] === 'exchange_rate' && strpos(strtolower($r['Type']), 'decimal') !== false) {
            if (preg_match('/decimal\((\d+),(\d+)\)/i', $r['Type'], $m)) {
                $precision = intval($m[1]);
                $scale = intval($m[2]);
                // If integer digits (precision-scale) < 6, column can't hold 15900
                if (($precision - $scale) < 6) {
                    $db->query("ALTER TABLE payroll_items MODIFY COLUMN exchange_rate DECIMAL(15,4) DEFAULT 0");
                }
            }
        }
        // Fix status column: ENUM doesn't have 'confirmed', change to VARCHAR
        if ($r['Field'] === 'status' && stripos($r['Type'], 'enum') !== false) {
            if (stripos($r['Type'], 'confirmed') === false) {
                $db->query("ALTER TABLE payroll_items MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending'");
            }
        }
    }
}
foreach ($migrateCols as $col => $def) {
    if (!in_array($col, $existing)) {
        $db->query("ALTER TABLE payroll_items ADD COLUMN $col $def");
    }
}

header('Content-Type: application/json');

// Helper: get actual columns for a table
function getTableColumns($db, $table) {
    $result = $db->query("SHOW COLUMNS FROM $table");
    $cols = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cols[] = $row['Field'];
        }
    }
    return $cols;
}

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($action === 'get' && $id > 0) {
    // Get payslip data with all JOINs (using pi.* which is safe)
    $sql = "SELECT pi.*, 
                   c.contract_no, c.crew_id, c.crew_name AS contract_crew_name,
                   cr.email, cr.full_name,
                   COALESCE(pi.crew_name, c.crew_name, cr.full_name) AS display_crew_name,
                   COALESCE(pi.rank_name, r.name) AS display_rank_name,
                   COALESCE(pi.vessel_name, v.name) AS display_vessel_name,
                   COALESCE(cr.bank_holder, c.crew_name) AS display_bank_holder,
                   cr.bank_account AS display_bank_account,
                   cr.bank_name AS display_bank_name,
                   COALESCE(cur.code, 'IDR') AS display_original_currency,
                   COALESCE(cs.basic_salary, 0) AS contract_basic_salary,
                   COALESCE(cs.overtime_allowance, 0) AS contract_overtime,
                   COALESCE(cs.exchange_rate, 0) AS contract_exchange_rate,
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
    
    // Map display values
    $item['crew_name'] = $item['display_crew_name'];
    $item['rank_name'] = $item['display_rank_name'];
    $item['vessel_name'] = $item['display_vessel_name'];
    $item['bank_holder'] = $item['display_bank_holder'];
    $item['bank_account'] = $item['display_bank_account'];
    $item['bank_name'] = $item['display_bank_name'];
    $item['original_currency'] = $item['display_original_currency'];
    $item['original_basic'] = $item['contract_basic_salary'];
    $item['original_overtime'] = $item['contract_overtime'];
    
    // Exchange rate logic
    $cRate = floatval($item['contract_exchange_rate']);
    $pRate = floatval($item['exchange_rate'] ?? 0);
    if ($cRate > 0) {
        $item['exchange_rate'] = $cRate;
    } elseif ($pRate > 0 && $pRate < 1) {
        $item['exchange_rate'] = round(1 / $pRate);
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
    
    // All possible fields to update
    $allFields = [
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
    
    // Also save computed salary values if provided
    $computedFields = ['basic_salary', 'overtime_allowance', 'gross_salary', 'tax_amount', 'net_salary', 'total_deductions'];
    foreach ($computedFields as $cf) {
        if (isset($_POST[$cf])) {
            $allFields[$cf] = floatval($_POST[$cf]);
        }
    }
    
    // Handle status + confirmed_at
    if (!empty($_POST['status'])) {
        $allFields['status'] = $_POST['status'];
        if ($_POST['status'] === 'confirmed') {
            $allFields['confirmed_at'] = date('Y-m-d H:i:s');
        }
    }
    
    // Filter to only columns that exist in DB
    $dbCols = getTableColumns($db, 'payroll_items');
    $fields = [];
    foreach ($allFields as $col => $val) {
        if (in_array($col, $dbCols)) {
            $fields[$col] = $val;
        }
    }
    
    if (empty($fields)) {
        echo json_encode(['success' => false, 'message' => 'No valid columns to update']);
        exit;
    }
    
    $setParts = [];
    $types = '';
    $values = [];
    $stringCols = ['status', 'confirmed_at', 'email_status', 'email_sent_at'];
    foreach ($fields as $col => $val) {
        $setParts[] = "$col = ?";
        $types .= in_array($col, $stringCols) ? 's' : 'd';
        $values[] = $val;
    }
    $types .= 'i';
    $values[] = $itemId;
    
    $sql = "UPDATE payroll_items SET " . implode(', ', $setParts) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $db->error, 'sql' => $sql]);
        exit;
    }
    $stmt->bind_param($types, ...$values);
    $result = $stmt->execute();
    $execError = $stmt->error;
    $stmt->close();
    
    if ($result) {
        // === AUTO-GENERATE PDF ON SAVE ===
        $pdfUrl = null;
        $pdfRelPath = null;
        try {
            // Fetch full payslip data for PDF rendering
            $fullSql = "SELECT pi.*, 
                           c.contract_no, c.crew_id, c.crew_name AS contract_crew_name,
                           cr.email, cr.full_name, cr.bank_holder, cr.bank_account, cr.bank_name,
                           COALESCE(pi.crew_name, c.crew_name, cr.full_name) AS display_crew_name,
                           COALESCE(pi.rank_name, r.name) AS display_rank_name,
                           COALESCE(pi.vessel_name, v.name) AS display_vessel_name,
                           COALESCE(cur.code, 'IDR') AS original_currency_code,
                           COALESCE(cs.basic_salary, 0) AS contract_basic_salary,
                           COALESCE(cs.overtime_allowance, 0) AS contract_overtime,
                           COALESCE(cs.exchange_rate, 0) AS contract_exchange_rate
                    FROM payroll_items pi
                    LEFT JOIN contracts c ON pi.contract_id = c.id
                    LEFT JOIN crews cr ON c.crew_id = cr.id
                    LEFT JOIN ranks r ON c.rank_id = r.id
                    LEFT JOIN vessels v ON c.vessel_id = v.id
                    LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                    LEFT JOIN currencies cur ON cs.currency_id = cur.id
                    WHERE pi.id = ?";
            $fstmt = $db->prepare($fullSql);
            $fstmt->bind_param('i', $itemId);
            $fstmt->execute();
            $fullItem = $fstmt->get_result()->fetch_assoc();
            $fstmt->close();
            
            if ($fullItem) {
                // Map display values
                $fullItem['crew_name'] = $fullItem['display_crew_name'];
                $fullItem['rank_name'] = $fullItem['display_rank_name'];
                $fullItem['vessel_name'] = $fullItem['display_vessel_name'];
                if (empty($fullItem['original_currency']) || $fullItem['original_currency'] === 'IDR') {
                    $fullItem['original_currency'] = $fullItem['original_currency_code'] ?? 'IDR';
                }
                
                // Crew data for bank info
                $crew = [
                    'bank_holder' => $fullItem['bank_holder'] ?? $fullItem['crew_name'],
                    'bank_account' => $fullItem['bank_account'] ?? '-',
                    'bank_name' => $fullItem['bank_name'] ?? '-'
                ];
                
                // Get period
                $pstmtPdf = $db->prepare("SELECT * FROM payroll_periods WHERE id = ?");
                $pstmtPdf->bind_param('i', $fullItem['payroll_period_id']);
                $pstmtPdf->execute();
                $period = $pstmtPdf->get_result()->fetch_assoc();
                $pstmtPdf->close();
                
                // Get payroll day setting
                $pdResult = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'payroll_day' LIMIT 1");
                $payroll_day = 1;
                if ($pdResult && $pdRow = $pdResult->fetch_assoc()) {
                    $payroll_day = intval($pdRow['setting_value']);
                }
                
                // Render the DOMPDF-compatible payslip template
                $item = $fullItem;
                ob_start();
                include APPPATH . 'Views/payroll/payslip_pdf_dompdf.php';
                $html = ob_get_clean();
                
                // Convert logo to base64 for embeddable PDF
                $logoPath = FCPATH . 'assets/images/logo.png';
                if (file_exists($logoPath)) {
                    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                    $html = str_replace(
                        [BASE_URL . 'assets/images/logo.png', 'assets/images/logo.png'],
                        [$logoBase64, $logoBase64],
                        $html
                    );
                }
                
                // Generate PDF with DOMPDF
                $options = new \Dompdf\Options();
                $options->set('isRemoteEnabled', true);
                $options->set('isHtml5ParserEnabled', true);
                $options->set('defaultFont', 'Arial');
                
                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                
                // Save PDF permanently
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fullItem['crew_name'] ?? 'crew');
                $monthStr = str_pad($period['period_month'] ?? 1, 2, '0', STR_PAD_LEFT);
                $yearStr = $period['period_year'] ?? date('Y');
                $pdfFilename = "payslip_{$safeName}_{$yearStr}_{$monthStr}.pdf";
                $pdfFullPath = $payslipsDir . $pdfFilename;
                $pdfRelPath = 'writable/payslips/' . $pdfFilename;
                
                file_put_contents($pdfFullPath, $dompdf->output());
                
                // Update pdf_path in DB
                $dbCols2 = getTableColumns($db, 'payroll_items');
                if (in_array('pdf_path', $dbCols2)) {
                    $pdfStmt = $db->prepare("UPDATE payroll_items SET pdf_path = ? WHERE id = ?");
                    $pdfStmt->bind_param('si', $pdfRelPath, $itemId);
                    $pdfStmt->execute();
                    $pdfStmt->close();
                }
                
                $pdfUrl = BASE_URL . $pdfRelPath;
                error_log("[PAYSLIP] PDF auto-generated: {$pdfFullPath}");
            }
        } catch (\Exception $e) {
            error_log('[PAYSLIP] Auto PDF generation failed: ' . $e->getMessage());
            // Don't fail the save — PDF generation is a bonus
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Slip gaji berhasil disimpan & PDF dibuat', 
            'fields_updated' => count($fields),
            'pdf_url' => $pdfUrl,
            'pdf_path' => $pdfRelPath
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $execError]);
    }

} elseif ($action === 'send_email' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = intval($_POST['item_id'] ?? 0);
    $email = trim($_POST['email'] ?? '');
    
    if (!$itemId || !$email) {
        echo json_encode(['success' => false, 'message' => 'Item ID dan email diperlukan']);
        exit;
    }
    
    // Get payroll item
    $stmt = $db->prepare("SELECT pi.*, c.crew_id FROM payroll_items pi LEFT JOIN contracts c ON pi.contract_id = c.id WHERE pi.id = ?");
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (!$item) {
        echo json_encode(['success' => false, 'message' => 'Payroll item tidak ditemukan']);
        exit;
    }
    
    // Get period
    $pstmt = $db->prepare("SELECT * FROM payroll_periods WHERE id = ?");
    $pstmt->bind_param('i', $item['payroll_period_id']);
    $pstmt->execute();
    $period = $pstmt->get_result()->fetch_assoc();
    $pstmt->close();
    
    // Try to send email
    try {
        // Check if a saved PDF already exists (from auto-generate on save)
        $attachmentPath = null;
        $attachmentName = 'payslip.pdf';
        $isPermanentPdf = false;
        
        // Check for existing saved PDF
        if (!empty($item['pdf_path']) && file_exists(BASEPATH . $item['pdf_path'])) {
            $attachmentPath = BASEPATH . $item['pdf_path'];
            $isPermanentPdf = true;
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $item['crew_name'] ?? 'crew');
            $monthStr = str_pad($period['period_month'] ?? 1, 2, '0', STR_PAD_LEFT);
            $yearStr = $period['period_year'] ?? date('Y');
            $attachmentName = "Payslip_{$safeName}_{$yearStr}_{$monthStr}.pdf";
            error_log("[PAYSLIP_EMAIL] Using saved PDF: {$attachmentPath}");
        } else {
            // Fallback: generate PDF on-the-fly if no saved PDF exists
            try {
                $fullSql = "SELECT pi.*, 
                               c.contract_no, c.crew_id, c.crew_name AS contract_crew_name,
                               cr.email, cr.full_name, cr.bank_holder, cr.bank_account, cr.bank_name,
                               COALESCE(pi.crew_name, c.crew_name, cr.full_name) AS display_crew_name,
                               COALESCE(pi.rank_name, r.name) AS display_rank_name,
                               COALESCE(pi.vessel_name, v.name) AS display_vessel_name,
                               COALESCE(cur.code, 'IDR') AS original_currency,
                               COALESCE(cs.basic_salary, 0) AS contract_basic_salary,
                               COALESCE(cs.overtime_allowance, 0) AS contract_overtime,
                               COALESCE(cs.exchange_rate, 0) AS contract_exchange_rate
                        FROM payroll_items pi
                        LEFT JOIN contracts c ON pi.contract_id = c.id
                        LEFT JOIN crews cr ON c.crew_id = cr.id
                        LEFT JOIN ranks r ON c.rank_id = r.id
                        LEFT JOIN vessels v ON c.vessel_id = v.id
                        LEFT JOIN contract_salaries cs ON c.id = cs.contract_id
                        LEFT JOIN currencies cur ON cs.currency_id = cur.id
                        WHERE pi.id = ?";
                $fstmt = $db->prepare($fullSql);
                $fstmt->bind_param('i', $itemId);
                $fstmt->execute();
                $fullItem = $fstmt->get_result()->fetch_assoc();
                $fstmt->close();
                
                if ($fullItem) {
                    $fullItem['crew_name'] = $fullItem['display_crew_name'];
                    $fullItem['rank_name'] = $fullItem['display_rank_name'];
                    $fullItem['vessel_name'] = $fullItem['display_vessel_name'];
                    
                    $crew = [
                        'bank_holder' => $fullItem['bank_holder'] ?? $fullItem['crew_name'],
                        'bank_account' => $fullItem['bank_account'] ?? '-',
                        'bank_name' => $fullItem['bank_name'] ?? '-'
                    ];
                    
                    $pdResult = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'payroll_day' LIMIT 1");
                    $payroll_day = 1;
                    if ($pdResult && $pdRow = $pdResult->fetch_assoc()) {
                        $payroll_day = intval($pdRow['setting_value']);
                    }
                    
                    // Render DOMPDF template
                    $renderItem = $fullItem;
                    $item_backup = $item;
                    $item = $renderItem;
                    ob_start();
                    include APPPATH . 'Views/payroll/payslip_pdf_dompdf.php';
                    $html = ob_get_clean();
                    $item = $item_backup;
                    
                    // Convert logo to base64
                    $logoPath = FCPATH . 'assets/images/logo.png';
                    if (file_exists($logoPath)) {
                        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                        $html = str_replace(
                            [BASE_URL . 'assets/images/logo.png', 'assets/images/logo.png'],
                            [$logoBase64, $logoBase64],
                            $html
                        );
                    }
                    
                    // Generate PDF
                    $options = new \Dompdf\Options();
                    $options->set('isRemoteEnabled', true);
                    $options->set('isHtml5ParserEnabled', true);
                    $options->set('defaultFont', 'Arial');
                    
                    $dompdf = new \Dompdf\Dompdf($options);
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    
                    // Save permanently
                    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fullItem['crew_name'] ?? 'crew');
                    $monthStr = str_pad($period['period_month'] ?? 1, 2, '0', STR_PAD_LEFT);
                    $yearStr = $period['period_year'] ?? date('Y');
                    $pdfFilename = "payslip_{$safeName}_{$yearStr}_{$monthStr}.pdf";
                    $pdfFullPath = $payslipsDir . $pdfFilename;
                    
                    file_put_contents($pdfFullPath, $dompdf->output());
                    $attachmentPath = $pdfFullPath;
                    $attachmentName = "Payslip_{$safeName}_{$yearStr}_{$monthStr}.pdf";
                    $isPermanentPdf = true;
                    
                    // Save pdf_path to DB
                    $pdfRelPath = 'writable/payslips/' . $pdfFilename;
                    $dbCols3 = getTableColumns($db, 'payroll_items');
                    if (in_array('pdf_path', $dbCols3)) {
                        $pdfStmt = $db->prepare("UPDATE payroll_items SET pdf_path = ? WHERE id = ?");
                        $pdfStmt->bind_param('si', $pdfRelPath, $itemId);
                        $pdfStmt->execute();
                        $pdfStmt->close();
                    }
                }
            } catch (\Exception $e) {
                error_log("[PAYSLIP_EMAIL] Failed to generate PDF attachment: " . $e->getMessage());
            }
        }
        
        // Try Mailer class first
        $mailerSent = false;
        $mailerErrors = '';
        $mailerFile = APPPATH . 'Libraries/Mailer.php';
        if (file_exists($mailerFile)) {
            require_once $mailerFile;
            if (class_exists('App\\Libraries\\Mailer')) {
                try {
                    $mailer = new \App\Libraries\Mailer();
                    error_log("[PAYSLIP_EMAIL] Mailer loaded. Sending to {$email}, attachment=" . ($attachmentPath ?? 'none'));
                    $mailerSent = $mailer->sendPayslip($email, $item['crew_name'], $period, $item, $attachmentPath);
                    if (!$mailerSent) {
                        $mailerErrors = implode('; ', $mailer->getErrors());
                        error_log("[PAYSLIP_EMAIL] SMTP failed for {$email}: {$mailerErrors}");
                    } else {
                        error_log("[PAYSLIP_EMAIL] Sent OK to {$email}");
                    }
                } catch (\Throwable $e) {
                    $mailerErrors = 'Mailer error: ' . $e->getMessage();
                    error_log("[PAYSLIP_EMAIL] Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
                }
            } else {
                $mailerErrors = 'Mailer class not found';
                error_log("[PAYSLIP_EMAIL] class_exists failed");
            }
        } else {
            $mailerErrors = 'Mailer.php not found';
            error_log("[PAYSLIP_EMAIL] File not found: {$mailerFile}");
        }
        
        // Don't delete permanent PDF files (only delete temp ones)
        // Permanent PDFs in writable/payslips/ are kept for future use
        
        if (!$mailerSent) {
            // Fallback: use PHP mail()
            $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $periodText = ($monthNames[$period['period_month']] ?? '') . ' ' . $period['period_year'];
            $subject = "Slip Gaji - {$item['crew_name']} - {$periodText}";
            $body = "Slip gaji Anda untuk periode {$periodText} telah tersedia.\n\n";
            $body .= "Nama: {$item['crew_name']}\n";
            $body .= "Kapal: {$item['vessel_name']}\n";
            $body .= "Gaji Bersih: {$item['currency_code']} " . number_format($item['net_salary'] ?? 0, 0, ',', '.') . "\n\n";
            $body .= "Silakan login ke ERP untuk melihat detail slip gaji.\n";
            $body .= "Link: " . BASE_URL . "payroll/payslip/{$itemId}\n\n";
            $body .= "PT. Indo Oceancrew Services";
            
            $headers = "From: PT Indo Oceancrew Services <ios@indooceancrew.co.id>\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            $mailerSent = @mail($email, $subject, $body, $headers);
        }
        
        // Update status (only if column exists)
        $dbCols = getTableColumns($db, 'payroll_items');
        $updateParts = [];
        $updateTypes = '';
        $updateValues = [];
        
        if (in_array('email_status', $dbCols)) {
            $updateParts[] = "email_status = ?";
            $updateTypes .= 's';
            $updateValues[] = $mailerSent ? 'sent' : 'failed';
        }
        if ($mailerSent && in_array('email_sent_at', $dbCols)) {
            $updateParts[] = "email_sent_at = ?";
            $updateTypes .= 's';
            $updateValues[] = date('Y-m-d H:i:s');
        }
        
        if (!empty($updateParts)) {
            $updateTypes .= 'i';
            $updateValues[] = $itemId;
            $sql = "UPDATE payroll_items SET " . implode(', ', $updateParts) . " WHERE id = ?";
            $ustmt = $db->prepare($sql);
            $ustmt->bind_param($updateTypes, ...$updateValues);
            $ustmt->execute();
            $ustmt->close();
        }
        
        if ($mailerSent) {
            echo json_encode(['success' => true, 'message' => 'Slip gaji berhasil dikirim ke ' . $email]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengirim email ke ' . $email . ($mailerErrors ? ': ' . $mailerErrors : '. Periksa konfigurasi SMTP.')]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
    }

} elseif ($action === 'update_payday' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = intval($_POST['payroll_day'] ?? 0);
    if ($day < 1 || $day > 28) {
        echo json_encode(['success' => false, 'message' => 'Tanggal harus antara 1 - 28']);
        exit;
    }
    
    // Check if settings table exists and has payroll_day
    $check = $db->query("SELECT id FROM settings WHERE setting_key = 'payroll_day' LIMIT 1");
    if ($check && $check->num_rows > 0) {
        $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'payroll_day'");
        $dayStr = (string)$day;
        $stmt->bind_param('s', $dayStr);
        $result = $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('payroll_day', ?)");
        $dayStr = (string)$day;
        $stmt->bind_param('s', $dayStr);
        $result = $stmt->execute();
        $stmt->close();
    }
    
    echo json_encode(['success' => $result, 'message' => $result ? 'Tanggal gajian berhasil diubah ke tanggal ' . $day : 'Gagal menyimpan']);

} elseif ($action === 'bulk_list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Return all payroll items for a period with crew emails
    $periodId = intval($_GET['period_id'] ?? 0);
    if (!$periodId) {
        echo json_encode(['success' => false, 'message' => 'Period ID diperlukan']);
        exit;
    }
    
    $sql = "SELECT pi.id, pi.crew_name, pi.status, pi.email_sent_at, pi.email_status,
                   cr.email
            FROM payroll_items pi
            LEFT JOIN contracts c ON pi.contract_id = c.id
            LEFT JOIN crews cr ON c.crew_id = cr.id
            WHERE pi.payroll_period_id = ?
            ORDER BY pi.vessel_name, pi.crew_name";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $periodId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['success' => true, 'items' => $items]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action. Use ?action=get&id=X, POST action=update, POST action=send_email, GET action=bulk_list&period_id=X, or POST action=update_payday']);
}

$db->close();
