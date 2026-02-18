<?php
/**
 * Script Otomatisasi Email Payroll
 * Jalankan via Task Scheduler: php c:\xampp\htdocs\PT_indoocean\erp\cron_payroll_email.php
 */

require_once __DIR__ . '/index.php'; // Bootstrap

echo "Starting Payroll Email Job...\n";

$periodModel = new \App\Models\PayrollPeriodModel($db);
$itemModel = new \App\Models\PayrollItemModel($db);
$crewModel = new \App\Models\CrewModel($db);
$mailer = new \App\Libraries\Mailer();

// Cari periode yang statusnya 'completed' tapi belum semua email terkirim
// Logika: Ambil periode bulan ini atau bulan lalu yang statusnya completed
// Untuk demo ini, kita ambil periode terakhir yang completed
$periods = $db->query("SELECT * FROM payroll_periods WHERE status = 'completed' ORDER BY id DESC LIMIT 1");

if (empty($periods)) {
    echo "No completed payroll period found.\n";
    exit;
}

$period = $periods[0];
echo "check Period: {$period['period_month']}/{$period['period_year']}... \n";

// Ambil item yang status emailnya 'pending' atau 'failed' (untuk retry)
$items = $db->query("SELECT * FROM payroll_items WHERE payroll_period_id = ? AND email_status != 'sent'", [$period['id']]);

if (empty($items)) {
    echo "All emails already sent for this period.\n";
    exit;
}

$count = 0;
foreach ($items as $item) {
    $contract = $db->query("SELECT crew_id FROM contracts WHERE id = ?", [$item['contract_id']], 'i');
    if (empty($contract)) continue;
    
    $crewId = $contract[0]['crew_id'];
    $crew = $crewModel->getWithDetails($crewId);
    
    if (empty($crew['email'])) {
        echo "Skip {$item['crew_name']}: No Email\n";
        continue;
    }
    
    echo "Sending to {$crew['email']}... ";
    
    if ($mailer->sendPayslip($crew['email'], $crew['full_name'], $period, $item)) {
        $itemModel->update($item['id'], [
            'email_status' => 'sent',
            'email_sent_at' => date('Y-m-d H:i:s'),
            'email_failure_reason' => null
        ]);
        echo "OK\n";
        $count++;
    } else {
        $itemModel->update($item['id'], [
            'email_status' => 'failed',
            'email_failure_reason' => implode(', ', $mailer->getErrors())
        ]);
        echo "FAILED\n";
    }
    
    // Antigravity: Jeda 2 detik untuk menghindari rate limit SMTP
    sleep(2);
}

echo "Done. Sent {$count} emails.\n";
