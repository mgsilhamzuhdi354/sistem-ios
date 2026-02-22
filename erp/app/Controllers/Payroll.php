<?php
/**
 * PT Indo Ocean - ERP System
 * Payroll Controller (Feature 13)
 */

namespace App\Controllers;

require_once APPPATH . 'Models/PayrollModel.php';
require_once APPPATH . 'Models/SettingsModel.php';

use App\Models\PayrollPeriodModel;
use App\Models\PayrollItemModel;
use App\Models\SettingsModel;

class Payroll extends BaseController
{
    private $periodModel;
    private $itemModel;

    public function __construct()
    {
        parent::__construct();
        $this->periodModel = new PayrollPeriodModel($this->db);
        $this->itemModel = new PayrollItemModel($this->db);
    }

    public function index()
    {
        $this->requirePermission('payroll', 'view');
        $month = (int) $this->input('month', date('n'));
        $year = (int) $this->input('year', date('Y'));

        $period = $this->periodModel->getOrCreate($month, $year);
        $items = $this->itemModel->getByPeriod($period['id']);

        // Preload full payslip data for all items (avoids separate API call)
        // Uses pi.* which safely selects only columns that exist
        $payslipDataMap = [];
        foreach ($items as $item) {
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
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $item['id']);
            $stmt->execute();
            $fullItem = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($fullItem) {
                // Map display values for JS (prefer contract data over payroll_items)
                $fullItem['crew_name'] = $fullItem['display_crew_name'];
                $fullItem['rank_name'] = $fullItem['display_rank_name'];
                $fullItem['vessel_name'] = $fullItem['display_vessel_name'];
                $fullItem['bank_holder'] = $fullItem['display_bank_holder'];
                $fullItem['bank_account'] = $fullItem['display_bank_account'];
                $fullItem['bank_name'] = $fullItem['display_bank_name'];
                $fullItem['original_currency'] = $fullItem['display_original_currency'];
                $fullItem['original_basic'] = $fullItem['contract_basic_salary'];
                $fullItem['original_overtime'] = $fullItem['contract_overtime'];
                // Exchange rate = kurs (1 original_currency = X IDR)
                $origCur = strtoupper($fullItem['original_currency'] ?? 'IDR');
                $cRate = floatval($fullItem['contract_exchange_rate']);
                $pRate = floatval($fullItem['exchange_rate'] ?? 0);
                if ($cRate > 0) {
                    $fullItem['exchange_rate'] = $cRate;
                } elseif ($pRate > 1) {
                    $fullItem['exchange_rate'] = $pRate;
                } else {
                    // Default IDR kurs per currency
                    $defaultKurs = ['IDR' => 1, 'USD' => 15900, 'MYR' => 3500, 'SGD' => 11800, 'EUR' => 17000];
                    $fullItem['exchange_rate'] = $defaultKurs[$origCur] ?? 1;
                }
                $payslipDataMap[$item['id']] = $fullItem;
            }
        }

        $data = [
            'title' => 'Payroll Management',
            'period' => $period,
            'items' => $items,
            'payslipDataMap' => $payslipDataMap,
            'summary' => $this->itemModel->getSummaryByVessel($period['id']),
            'month' => $month,
            'year' => $year,
            'payroll_day' => (new SettingsModel($this->db))->get('payroll_day', '15'),
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'payroll/index_modern' : 'payroll/index';
        
        return $this->view($view, $data);
    }

    /**
     * Display history of all payroll periods
     */
    public function history()
    {
        // Get all periods, ordered by most recent first
        $sql = "SELECT * FROM payroll_periods ORDER BY period_year DESC, period_month DESC";
        $rawPeriods = $this->db->query($sql);

        // Get summary for each period
        $periods = [];
        foreach ($rawPeriods as $period) {
            $items = $this->itemModel->getByPeriod($period['id']);
            $period['total_items'] = count($items);

            // Calculate total
            $totalNet = 0;
            foreach ($items as $item) {
                $totalNet += $item['net_salary'];
            }
            $period['total_amount'] = $totalNet;
            $periods[] = $period;
        }

        $data = [
            'title' => 'Payroll History',
            'currentPage' => 'payroll-history',
            'periods' => $periods,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'payroll/history_modern' : 'payroll/history';

        return $this->view($view, $data);
    }
    public function process()
    {
        $this->requirePermission('payroll', 'create');
        if (!$this->isPost()) {
            $this->redirect('payroll');
        }

        $month = (int) $this->input('month', date('n'));
        $year = (int) $this->input('year', date('Y'));

        $period = $this->periodModel->getOrCreate($month, $year);

        // Generate payroll items from active contracts
        $this->itemModel->generateForPeriod($period['id']);

        // Update period status
        $this->periodModel->update($period['id'], [
            'status' => PAYROLL_PROCESSING,
            'processed_by' => $this->getCurrentUser()['id'] ?? null,
            'processed_at' => date('Y-m-d H:i:s')
        ]);

        $this->setFlash('success', 'Payroll generated successfully');
        $this->redirect('payroll?month=' . $month . '&year=' . $year);
    }

    public function complete()
    {
        $this->requirePermission('payroll', 'approve');
        if (!$this->isPost()) {
            $this->redirect('payroll');
        }

        $periodId = $this->input('period_id');

        $this->periodModel->update($periodId, [
            'status' => PAYROLL_COMPLETED
        ]);

        // Mark all items as paid
        $this->db->query("UPDATE payroll_items SET status = 'paid', payment_date = CURDATE() WHERE payroll_period_id = $periodId");

        $this->setFlash('success', 'Payroll marked as completed');
        $this->redirect('payroll');
    }

    public function show($periodId)
    {
        $period = $this->periodModel->find($periodId);
        if (!$period) {
            $this->redirect('payroll');
        }

        $data = [
            'title' => 'Payroll Detail - ' . date('F Y', strtotime($period['start_date'])),
            'currentPage' => 'crew-payroll',
            'period' => $period,
            'items' => $this->itemModel->getByPeriod($periodId),
            'summary' => $this->itemModel->getSummaryByVessel($periodId),
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'payroll/view_modern' : 'payroll/view';

        return $this->view($view, $data);
    }

    public function export($periodId)
    {
        $period = $this->periodModel->find($periodId);
        $items = $this->itemModel->getByPeriod($periodId);

        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="payroll_' . $period['period_year'] . '_' . str_pad($period['period_month'], 2, '0', STR_PAD_LEFT) . '.csv"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, ['Crew Name', 'Rank', 'Vessel', 'Basic Salary', 'Overtime', 'Leave Pay', 'Bonus', 'Gross', 'Deductions', 'Tax', 'Net Salary', 'Currency']);

        foreach ($items as $item) {
            fputcsv($output, [
                $item['crew_name'],
                $item['rank_name'],
                $item['vessel_name'],
                $item['basic_salary'],
                $item['overtime'],
                $item['leave_pay'],
                $item['bonus'],
                $item['gross_salary'],
                $item['total_deductions'],
                $item['tax_amount'],
                $item['net_salary'],
                $item['currency_code']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Send payslips via email for a period
     */
    public function sendEmails()
    {
        $periodId = $this->input('period_id');
        $period = $this->periodModel->find($periodId);

        if (!$period) {
            $this->setFlash('error', 'Period not found');
            $this->redirect('payroll');
        }

        // Get all items in this period
        $items = $this->itemModel->getByPeriod($periodId);
        $crewModel = new \App\Models\CrewModel($this->db);
        $mailer = new \App\Libraries\Mailer();

        $countSent = 0;
        $countFailed = 0;

        foreach ($items as $item) {
            // Find crew email
            $contractStmt = $this->db->prepare("SELECT crew_id FROM contracts WHERE id = ?");
            $contractStmt->bind_param('i', $item['contract_id']);
            $contractStmt->execute();
            $contractResult = $contractStmt->get_result()->fetch_assoc();
            $contractStmt->close();
            if (empty($contractResult))
                continue;

            $crewId = $contractResult['crew_id'];
            $crew = $crewModel->getWithDetails($crewId);

            if (empty($crew['email'])) {
                $this->itemModel->update($item['id'], [
                    'email_status' => 'failed',
                    'email_failure_reason' => 'No email address found'
                ]);
                $countFailed++;
                continue;
            }

            // Send email
            if ($mailer->sendPayslip($crew['email'], $crew['full_name'], $period, $item)) {
                $this->itemModel->update($item['id'], [
                    'email_status' => 'sent',
                    'email_sent_at' => date('Y-m-d H:i:s'),
                    'email_failure_reason' => null
                ]);
                $countSent++;
            } else {
                $this->itemModel->update($item['id'], [
                    'email_status' => 'failed',
                    'email_failure_reason' => implode(', ', $mailer->getErrors())
                ]);
                $countFailed++;
            }
        }

        $this->setFlash('success', "Email sent: {$countSent} success, {$countFailed} failed.");
        $this->redirect("payroll/show/{$periodId}");
    }

    /**
     * View payslip for a specific payroll item
     */
    public function payslip($itemId)
    {
        $this->requirePermission('payroll', 'view');
        
        // Get payroll item
        $sql = "SELECT pi.*, c.contract_no, c.crew_id
                FROM payroll_items pi
                LEFT JOIN contracts c ON pi.contract_id = c.id
                WHERE pi.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$item) {
            $this->setFlash('error', 'Payroll item not found');
            $this->redirect('payroll');
            return;
        }
        
        // Get period
        $period = $this->periodModel->find($item['payroll_period_id']);
        
        // Get crew bank details
        $crew = ['bank_holder' => '', 'bank_account' => '', 'bank_name' => ''];
        if (!empty($item['crew_id'])) {
            $crewStmt = $this->db->prepare("SELECT full_name, bank_holder, bank_account, bank_name, email FROM crews WHERE id = ?");
            $crewStmt->bind_param('i', $item['crew_id']);
            $crewStmt->execute();
            $crewData = $crewStmt->get_result()->fetch_assoc();
            $crewStmt->close();
            if ($crewData) $crew = $crewData;
        }
        
        $settingsModel = new SettingsModel($this->db);
        $payroll_day = $settingsModel->get('payroll_day', '15');
        
        $data = [
            'item' => $item,
            'period' => $period,
            'crew' => $crew,
            'payroll_day' => $payroll_day
        ];
        
        return $this->view('payroll/payslip_pdf', $data);
    }

    /**
     * API: Get crew email and info by payroll item ID
     */
    public function apiCrewEmail($itemId)
    {
        $this->requireAuth();
        
        // Get payroll item + crew info
        $sql = "SELECT pi.id, pi.crew_name, pi.rank_name, pi.vessel_name, pi.net_salary, pi.currency_code,
                       c.crew_id, cr.email, cr.full_name, cr.bank_name
                FROM payroll_items pi
                LEFT JOIN contracts c ON pi.contract_id = c.id
                LEFT JOIN crews cr ON c.crew_id = cr.id
                WHERE pi.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        header('Content-Type: application/json');
        if ($result) {
            echo json_encode([
                'success' => true,
                'crew_name' => $result['full_name'] ?? $result['crew_name'],
                'email' => $result['email'] ?? '',
                'rank' => $result['rank_name'],
                'vessel' => $result['vessel_name'],
                'net_salary' => $result['net_salary'],
                'currency' => $result['currency_code'],
                'item_id' => $result['id']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
        exit;
    }

    /**
     * API: Send payslip email for a single crew member
     */
    public function apiSendPayslip()
    {
        $this->requireAuth();
        
        $itemId = (int) $this->input('item_id');
        $email = trim($this->input('email', ''));
        
        if (!$itemId || !$email) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Item ID dan email diperlukan']);
            exit;
        }
        
        // Get payroll item
        $sql = "SELECT pi.*, c.crew_id
                FROM payroll_items pi
                LEFT JOIN contracts c ON pi.contract_id = c.id
                WHERE pi.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$item) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Payroll item tidak ditemukan']);
            exit;
        }
        
        $period = $this->periodModel->find($item['payroll_period_id']);
        
        // Try to send email
        try {
            if (class_exists('\\App\\Libraries\\Mailer')) {
                $mailer = new \App\Libraries\Mailer();
                $sent = $mailer->sendPayslip($email, $item['crew_name'], $period, $item);
                
                if ($sent) {
                    $this->itemModel->update($itemId, [
                        'email_status' => 'sent',
                        'email_sent_at' => date('Y-m-d H:i:s'),
                        'email_failure_reason' => null
                    ]);
                    
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Slip gaji berhasil dikirim ke ' . $email]);
                    exit;
                }
            }
            
            // Fallback: use PHP mail()
            $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $periodText = ($monthNames[$period['period_month']] ?? '') . ' ' . $period['period_year'];
            $subject = "Slip Gaji - {$item['crew_name']} - {$periodText}";
            $body = "Slip gaji Anda untuk periode {$periodText} telah tersedia.\n\n";
            $body .= "Nama: {$item['crew_name']}\n";
            $body .= "Kapal: {$item['vessel_name']}\n";
            $body .= "Gaji Bersih: {$item['currency_code']} " . number_format($item['net_salary'], 0, ',', '.') . "\n\n";
            $body .= "Silakan login ke ERP untuk melihat detail slip gaji.\n";
            $body .= "Link: " . BASE_URL . "payroll/payslip/{$itemId}\n\n";
            $body .= "PT. Indo Oceancrew Services";
            
            $headers = "From: PT Indo Oceancrew Services <ios@indooceancrew.co.id>\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            mail($email, $subject, $body, $headers);
            
            $this->itemModel->update($itemId, [
                'email_status' => 'sent',
                'email_sent_at' => date('Y-m-d H:i:s')
            ]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Slip gaji berhasil dikirim ke ' . $email]);
            exit;
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * API: Get full payslip data for editable modal
     */
    public function apiGetPayslipData($itemId)
    {
        $this->requireAuth();
        
        $sql = "SELECT pi.*, 
                       c.contract_no, c.crew_id, c.crew_name AS contract_crew_name,
                       cr.email, cr.full_name,
                       COALESCE(pi.crew_name, c.crew_name, cr.full_name) AS crew_name,
                       COALESCE(pi.rank_name, r.name) AS rank_name,
                       COALESCE(pi.vessel_name, v.name) AS vessel_name,
                       COALESCE(cr.bank_holder, c.crew_name) AS bank_holder,
                       cr.bank_account AS bank_account,
                       cr.bank_name AS bank_name,
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
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$item) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit;
        }
        
        $period = $this->periodModel->find($item['payroll_period_id']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'item' => $item,
            'period' => $period
        ]);
        exit;
    }

    /**
     * API: Update payslip values (from editable modal)
     */
    public function apiUpdatePayslip()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $itemId = (int) $this->input('item_id');
        if (!$itemId) {
            echo json_encode(['success' => false, 'message' => 'Item ID diperlukan']);
            exit;
        }
        
        // Auto-fix exchange_rate column if too small
        try {
            $colInfo = $this->db->query("SHOW COLUMNS FROM payroll_items WHERE Field = 'exchange_rate'");
            if ($colInfo && $row = $colInfo->fetch_assoc()) {
                if (preg_match('/decimal\((\d+),(\d+)\)/i', $row['Type'], $m)) {
                    if ((intval($m[1]) - intval($m[2])) < 6) {
                        $this->db->query("ALTER TABLE payroll_items MODIFY COLUMN exchange_rate DECIMAL(15,4) DEFAULT 0");
                    }
                }
            }
            // Ensure status columns exist
            $migrateCols = [
                'confirmed_at' => 'DATETIME NULL',
                'email_sent_at' => 'DATETIME NULL',
                'email_status' => "VARCHAR(20) DEFAULT 'pending'",
                'status' => "VARCHAR(20) DEFAULT 'pending'",
            ];
            $existing = [];
            $colCheck = $this->db->query("SHOW COLUMNS FROM payroll_items");
            if ($colCheck) {
                while ($r = $colCheck->fetch_assoc()) $existing[] = $r['Field'];
            }
            foreach ($migrateCols as $col => $def) {
                if (!in_array($col, $existing)) {
                    $this->db->query("ALTER TABLE payroll_items ADD COLUMN $col $def");
                }
            }
        } catch (\Exception $e) {}
        
        // Collect all editable fields
        $updateData = [];
        $editableFields = [
            'basic_salary', 'overtime', 'overtime_allowance', 'leave_pay', 'bonus', 'other_allowance',
            'insurance', 'medical', 'advance', 'other_deductions',
            'admin_bank_fee', 'reimbursement', 'loans',
            'tax_rate', 'tax_amount',
            'original_basic', 'original_overtime', 'original_leave_pay',
            'exchange_rate', 'original_currency',
            'gross_salary', 'net_salary', 'total_deductions'
        ];
        
        foreach ($editableFields as $field) {
            $val = $this->input($field);
            if ($val !== null && $val !== '') {
                if ($field === 'original_currency') {
                    $updateData[$field] = trim($val);
                } else {
                    $updateData[$field] = (float) $val;
                }
            }
        }
        
        // Handle status
        $status = $this->input('status');
        if ($status) {
            $updateData['status'] = $status;
            if ($status === 'confirmed') {
                $updateData['confirmed_at'] = date('Y-m-d H:i:s');
            }
        }
        
        // If computed values not provided, recalculate
        $existingItem = $this->itemModel->find($itemId);
        if (!$existingItem) {
            echo json_encode(['success' => false, 'message' => 'Item tidak ditemukan']);
            exit;
        }
        
        // Merge with existing to compute new totals if not provided
        $merged = array_merge($existingItem, $updateData);
        
        if (!isset($updateData['gross_salary'])) {
            $grossSalary = (float)$merged['basic_salary'] + (float)($merged['overtime'] ?? 0) + (float)($merged['leave_pay'] ?? 0) + (float)($merged['bonus'] ?? 0) + (float)($merged['other_allowance'] ?? 0);
            $totalDeductions = (float)($merged['insurance'] ?? 0) + (float)($merged['medical'] ?? 0) + (float)($merged['advance'] ?? 0) + (float)($merged['other_deductions'] ?? 0) + (float)($merged['admin_bank_fee'] ?? 0);
            $taxRate = (float)($merged['tax_rate'] ?? 2.5);
            $taxAmount = ($grossSalary - $totalDeductions) * ($taxRate / 100);
            $netSalary = $grossSalary - $totalDeductions - $taxAmount;
            
            $updateData['gross_salary'] = round($grossSalary, 2);
            $updateData['total_deductions'] = round($totalDeductions, 2);
            $updateData['tax_amount'] = round($taxAmount, 2);
            $updateData['net_salary'] = round($netSalary, 2);
        }
        
        $this->itemModel->update($itemId, $updateData);
        
        // Update period totals
        $this->periodModel->updateTotals($existingItem['payroll_period_id']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Slip gaji berhasil disimpan',
            'data' => [
                'gross_salary' => $updateData['gross_salary'] ?? 0,
                'total_deductions' => $updateData['total_deductions'] ?? 0,
                'tax_amount' => $updateData['tax_amount'] ?? 0,
                'net_salary' => $updateData['net_salary'] ?? 0
            ]
        ]);
        exit;
    }

    /**
     * API: Send payslip via email
     */
    public function sendPayslipEmail()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $itemId = (int) $this->input('item_id');
        $email = trim($this->input('email') ?? '');
        
        if (!$itemId || !$email) {
            echo json_encode(['success' => false, 'message' => 'Item ID dan email diperlukan']);
            exit;
        }
        
        // Get payroll item
        $stmt = $this->db->prepare("SELECT pi.*, c.crew_id FROM payroll_items pi LEFT JOIN contracts c ON pi.contract_id = c.id WHERE pi.id = ?");
        $stmt->bind_param('i', $itemId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$item) {
            echo json_encode(['success' => false, 'message' => 'Payroll item tidak ditemukan']);
            exit;
        }
        
        // Get period
        $period = $this->periodModel->find($item['payroll_period_id']);
        
        // Try to send email
        try {
            $mailerSent = false;
            $mailerFile = APPPATH . 'Libraries/Mailer.php';
            if (file_exists($mailerFile)) {
                require_once $mailerFile;
                if (class_exists('\\App\\Libraries\\Mailer')) {
                    $mailer = new \App\Libraries\Mailer();
                    $mailerSent = $mailer->sendPayslip($email, $item['crew_name'], $period, $item);
                }
            }
            
            if (!$mailerSent) {
                // Fallback: use PHP mail()
                $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                $periodText = ($monthNames[$period['period_month']] ?? '') . ' ' . $period['period_year'];
                $subject = "Slip Gaji - {$item['crew_name']} - {$periodText}";
                $body = "Slip gaji Anda untuk periode {$periodText} telah tersedia.\n\n";
                $body .= "Nama: {$item['crew_name']}\n";
                $body .= "Kapal: {$item['vessel_name']}\n";
                $body .= "Gaji Bersih: Rp " . number_format($item['net_salary'] ?? 0, 0, ',', '.') . "\n\n";
                $body .= "Silakan login ke ERP untuk melihat detail slip gaji.\n";
                $body .= "Link: " . BASE_URL . "payroll/payslip/{$itemId}\n\n";
                $body .= "PT. Indo Oceancrew Services";
                
                $headers = "From: PT Indo Oceancrew Services <ios@indooceancrew.co.id>\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                
                mail($email, $subject, $body, $headers);
            }
            
            // Update status to paid + email_sent_at
            $updateData = [
                'status' => 'paid',
                'email_sent_at' => date('Y-m-d H:i:s'),
                'email_status' => 'sent'
            ];
            $this->itemModel->update($itemId, $updateData);
            
            echo json_encode(['success' => true, 'message' => 'Slip gaji berhasil dikirim ke ' . $email]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * API: Update payday setting
     */
    public function updatePayday()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $day = (int) $this->input('payroll_day');
        if ($day < 1 || $day > 28) {
            echo json_encode(['success' => false, 'message' => 'Tanggal harus antara 1 - 28']);
            exit;
        }
        
        $settingsModel = new SettingsModel($this->db);
        $settingsModel->set('payroll_day', (string)$day);
        
        echo json_encode(['success' => true, 'message' => 'Tanggal gajian berhasil diubah ke tanggal ' . $day]);
        exit;
    }
}

