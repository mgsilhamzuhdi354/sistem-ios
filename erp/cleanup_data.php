<?php
/**
 * PT Indo Ocean - Database Cleanup Script
 * DELETE THIS FILE AFTER USE!
 */

// Replicate the same bootstrap as index.php
define('BASEPATH', __DIR__ . '/');
define('APPPATH', BASEPATH . 'app/');

// Load Composer autoload & .env (same as index.php)
if (file_exists(BASEPATH . 'vendor/autoload.php')) {
    require_once BASEPATH . 'vendor/autoload.php';
    if (class_exists('Dotenv\\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(BASEPATH);
        $dotenv->safeLoad();
    }
}

// Now load database config (will have correct env vars)
$dbConfig = require APPPATH . 'Config/Database.php';
$erpCfg = $dbConfig['default'];
$recCfg = $dbConfig['recruitment'];

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🧹 Database Cleanup</h1><pre>";
echo "Host: {$erpCfg['hostname']}, User: {$erpCfg['username']}, DB: {$erpCfg['database']}, Port: {$erpCfg['port']}\n\n";

// Connect to ERP
$erpDb = new mysqli($erpCfg['hostname'], $erpCfg['username'], $erpCfg['password'], $erpCfg['database'], $erpCfg['port']);
if ($erpDb->connect_error) {
    die("❌ ERP DB failed: " . $erpDb->connect_error);
}
echo "✅ Connected to ERP\n\n";

$erpDb->query("SET FOREIGN_KEY_CHECKS = 0");

$erpTables = [
    'payroll_items', 'payroll_periods',
    'contract_deductions', 'contract_salaries', 'contract_taxes',
    'contract_approvals', 'contract_logs', 'contract_documents', 'contracts',
    'crew_skills', 'crew_experiences', 'crew_documents', 'crews',
    'admin_checklists',
    'finance_invoice_items', 'finance_invoices', 'finance_payments',
    'finance_journal_entries', 'finance_journal_items',
    'activity_logs', 'notifications'
];

foreach ($erpTables as $table) {
    $r = @$erpDb->query("TRUNCATE TABLE `$table`");
    if ($r) { echo "  ✅ $table\n"; }
    else {
        $r2 = @$erpDb->query("DELETE FROM `$table`");
        echo ($r2 ? "  ✅ $table (delete)\n" : "  ⚠️ $table (skip)\n");
    }
}

$erpDb->query("SET FOREIGN_KEY_CHECKS = 1");
echo "\n--- ERP Verify ---\n";
foreach (['crews','contracts','payroll_items','admin_checklists'] as $t) {
    $r = @$erpDb->query("SELECT COUNT(*) as c FROM `$t`");
    echo "  $t: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
}
$erpDb->close();

// Recruitment
echo "\n━━━━━━━━━━━━━━━━━━\n\n";
$recDb = @new mysqli($recCfg['hostname'], $recCfg['username'], $recCfg['password'], $recCfg['database'], $recCfg['port']);
if ($recDb->connect_error) {
    echo "⚠️ Recruitment DB: " . $recDb->connect_error . "\n";
} else {
    echo "✅ Connected to Recruitment: {$recCfg['database']}\n\n";
    $recDb->query("SET FOREIGN_KEY_CHECKS = 0");
    @$recDb->query("DELETE FROM applicant_profiles WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM documents WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM notifications WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    
    foreach (['application_assignments','application_status_history','pipeline_requests','status_change_requests','job_claim_requests','medical_checkups','interview_answers','interview_sessions','archived_applications','applicant_documents','email_logs','applications'] as $t) {
        $r = @$recDb->query("TRUNCATE TABLE `$t`");
        if ($r) { echo "  ✅ $t\n"; }
        else {
            $r2 = @$recDb->query("DELETE FROM `$t`");
            echo ($r2 ? "  ✅ $t (delete)\n" : "  ⚠️ $t (skip)\n");
        }
    }
    
    @$recDb->query("DELETE FROM users WHERE role_id = 3");
    echo "  ✅ Deleted " . $recDb->affected_rows . " applicant users\n";
    $recDb->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n--- Recruitment Verify ---\n";
    $r = @$recDb->query("SELECT COUNT(*) as c FROM applications"); echo "  applications: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
    $r = @$recDb->query("SELECT COUNT(*) as c FROM users WHERE role_id=3"); echo "  applicants: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
    $recDb->close();
}

echo "\n</pre><h2 style='color:green'>🎉 Done!</h2>";
echo "<p><b>⚠️ HAPUS: <code>rm /var/www/html/erp/cleanup_data.php</code></b></p>";
echo "<p><a href='/erp/contracts'>Contracts</a> | <a href='/erp/crews'>Crews</a> | <a href='/erp/payroll'>Payroll</a> | <a href='/recruitment/public/crewing/pipeline'>Pipeline</a></p>";
