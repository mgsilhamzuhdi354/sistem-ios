<?php
/**
 * PT Indo Ocean - Database Cleanup Script
 * Run via browser: https://indooceancrewservice.com/erp/cleanup_data.php
 * DELETE THIS FILE AFTER USE!
 */

// Load ERP database config
require_once __DIR__ . '/app/Config/Database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h1>🧹 Database Cleanup</h1><pre>";

$results = [];

// Connect to ERP database
$erpDb = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($erpDb->connect_error) {
    die("ERP DB connection failed: " . $erpDb->connect_error);
}
echo "✅ Connected to ERP DB: " . DB_NAME . "\n";

$erpDb->query("SET FOREIGN_KEY_CHECKS = 0");

// ERP Tables to truncate
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
    $result = @$erpDb->query("TRUNCATE TABLE `$table`");
    if ($result) {
        echo "  ✅ Truncated: $table\n";
    } else {
        // Try DELETE instead
        $result2 = @$erpDb->query("DELETE FROM `$table`");
        if ($result2) {
            echo "  ✅ Deleted from: $table\n";
        } else {
            echo "  ⚠️ Skipped (not found): $table\n";
        }
    }
}

$erpDb->query("SET FOREIGN_KEY_CHECKS = 1");
echo "\n✅ ERP cleanup done!\n\n";

// Verify ERP
$checks = ['crews', 'contracts', 'payroll_items', 'admin_checklists'];
foreach ($checks as $t) {
    $r = @$erpDb->query("SELECT COUNT(*) as cnt FROM `$t`");
    $cnt = $r ? $r->fetch_assoc()['cnt'] : '?';
    echo "  $t: $cnt records\n";
}

// Connect to Recruitment database
echo "\n---\n";
$recDb = @new mysqli(DB_HOST, DB_USER, DB_PASS, defined('RECRUITMENT_DB_NAME') ? RECRUITMENT_DB_NAME : 'recruitment_db', DB_PORT);
if ($recDb->connect_error) {
    echo "⚠️ Recruitment DB not accessible: " . $recDb->connect_error . "\n";
} else {
    echo "✅ Connected to Recruitment DB\n";
    
    $recDb->query("SET FOREIGN_KEY_CHECKS = 0");
    
    // Delete applicant data
    @$recDb->query("DELETE FROM applicant_profiles WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM documents WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    @$recDb->query("DELETE FROM notifications WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
    
    $recTables = [
        'application_assignments', 'application_status_history',
        'pipeline_requests', 'status_change_requests', 'job_claim_requests',
        'medical_checkups', 'interview_answers', 'interview_sessions',
        'archived_applications', 'applications', 'applicant_documents',
        'email_logs'
    ];
    
    foreach ($recTables as $table) {
        $result = @$recDb->query("TRUNCATE TABLE `$table`");
        if ($result) {
            echo "  ✅ Truncated: $table\n";
        } else {
            $result2 = @$recDb->query("DELETE FROM `$table`");
            echo ($result2 ? "  ✅ Deleted from: $table\n" : "  ⚠️ Skipped: $table\n");
        }
    }
    
    // Delete applicant users
    $delUsers = @$recDb->query("DELETE FROM users WHERE role_id = 3");
    $affected = $recDb->affected_rows;
    echo "  ✅ Deleted $affected applicant users\n";
    
    $recDb->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "\n✅ Recruitment cleanup done!\n";
    
    // Verify
    $r = @$recDb->query("SELECT COUNT(*) as cnt FROM applications");
    echo "  applications: " . ($r ? $r->fetch_assoc()['cnt'] : '?') . " records\n";
    $r = @$recDb->query("SELECT COUNT(*) as cnt FROM users WHERE role_id = 3");
    echo "  applicant users: " . ($r ? $r->fetch_assoc()['cnt'] : '?') . " records\n";
    
    $recDb->close();
}

$erpDb->close();

echo "\n</pre>";
echo "<h2 style='color:green'>🎉 Cleanup Complete!</h2>";
echo "<p><strong>⚠️ HAPUS FILE INI SETELAH SELESAI!</strong></p>";
echo "<p><a href='/erp/contracts'>→ Cek Contracts</a> | ";
echo "<a href='/erp/crews'>→ Cek Crews</a> | ";
echo "<a href='/erp/payroll'>→ Cek Payroll</a> | ";
echo "<a href='/recruitment/public/crewing/pipeline'>→ Cek Pipeline</a></p>";
