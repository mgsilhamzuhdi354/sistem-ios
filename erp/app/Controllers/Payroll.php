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

        $data = [
            'title' => 'Payroll Management',
            'period' => $period,
            'items' => $items,
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
}
