<?php
/**
 * PT Indo Ocean - ERP System
 * Payroll Controller (Feature 13)
 */

namespace App\Controllers;

require_once APPPATH . 'Models/PayrollModel.php';

use App\Models\PayrollPeriodModel;
use App\Models\PayrollItemModel;

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
        $month = (int)$this->input('month', date('n'));
        $year = (int)$this->input('year', date('Y'));
        
        $period = $this->periodModel->getOrCreate($month, $year);
        $items = $this->itemModel->getByPeriod($period['id']);
        
        $data = [
            'title' => 'Payroll Management',
            'period' => $period,
            'items' => $items,
            'summary' => $this->itemModel->getSummaryByVessel($period['id']),
            'month' => $month,
            'year' => $year,
            'flash' => $this->getFlash()
        ];
        
        return $this->view('payroll/index', $data);
    }
    
    public function process()
    {
        if (!$this->isPost()) {
            $this->redirect('payroll');
        }
        
        $month = (int)$this->input('month', date('n'));
        $year = (int)$this->input('year', date('Y'));
        
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
            'period' => $period,
            'items' => $this->itemModel->getByPeriod($periodId),
            'summary' => $this->itemModel->getSummaryByVessel($periodId),
        ];
        
        return $this->view('payroll/view', $data);
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
}
