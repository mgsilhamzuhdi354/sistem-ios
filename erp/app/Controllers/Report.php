<?php
/**
 * PT Indo Ocean - ERP System
 * Report Controller (Feature 14)
 */

namespace App\Controllers;

require_once APPPATH . 'Models/ContractModel.php';
require_once APPPATH . 'Models/VesselModel.php';
require_once APPPATH . 'Models/ClientModel.php';
require_once APPPATH . 'Models/PayrollModel.php';

use App\Models\ContractModel;
use App\Models\VesselModel;
use App\Models\ClientModel;
use App\Models\PayrollPeriodModel;
use App\Models\PayrollItemModel;
use App\Models\ContractLogModel;

class Report extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Reports Center',
        ];
        
        return $this->view('reports/index', $data);
    }
    
    /**
     * Active contracts report
     */
    public function activeContracts()
    {
        $contractModel = new ContractModel($this->db);
        
        $data = [
            'title' => 'Active Contracts Report',
            'contracts' => $contractModel->getList(['status' => 'active'], 1, 1000),
        ];
        
        return $this->view('reports/contracts', $data);
    }
    
    /**
     * Expiring contracts report
     */
    public function expiringContracts()
    {
        $days = $this->input('days', 60);
        $contractModel = new ContractModel($this->db);
        
        $data = [
            'title' => 'Expiring Contracts Report',
            'contracts' => $contractModel->getExpiring($days),
            'days' => $days,
        ];
        
        return $this->view('reports/expiring', $data);
    }
    
    /**
     * Contracts by vessel report
     */
    public function contractsByVessel()
    {
        $vesselModel = new VesselModel($this->db);
        $vessels = $vesselModel->getAllWithDetails();
        
        foreach ($vessels as &$vessel) {
            $vessel['crew_list'] = $vesselModel->getCrewList($vessel['id']);
            $vessel['monthly_cost'] = $vesselModel->getTotalMonthlyCost($vessel['id']);
        }
        
        $data = [
            'title' => 'Contracts by Vessel Report',
            'vessels' => $vessels,
        ];
        
        return $this->view('reports/by_vessel', $data);
    }
    
    /**
     * Contracts by client report
     */
    public function contractsByClient()
    {
        $clientModel = new ClientModel($this->db);
        $clients = $clientModel->getAllWithStats();
        
        foreach ($clients as &$client) {
            $client['contracts'] = $clientModel->getContracts($client['id']);
        }
        
        $data = [
            'title' => 'Contracts by Client Report',
            'clients' => $clients,
        ];
        
        return $this->view('reports/by_client', $data);
    }
    
    /**
     * Payroll summary report
     */
    public function payrollSummary()
    {
        $year = $this->input('year', date('Y'));
        $periodModel = new PayrollPeriodModel($this->db);
        
        $periods = $periodModel->query(
            "SELECT * FROM payroll_periods WHERE period_year = ? ORDER BY period_month",
            [$year], 'i'
        );
        
        $data = [
            'title' => 'Payroll Summary Report - ' . $year,
            'periods' => $periods,
            'year' => $year,
        ];
        
        return $this->view('reports/payroll_summary', $data);
    }
    
    /**
     * Tax report (PPh 21)
     */
    public function taxReport()
    {
        $month = $this->input('month', date('n'));
        $year = $this->input('year', date('Y'));
        
        $itemModel = new PayrollItemModel($this->db);
        $periodModel = new PayrollPeriodModel($this->db);
        
        $period = $periodModel->query(
            "SELECT * FROM payroll_periods WHERE period_month = ? AND period_year = ?",
            [$month, $year], 'ii'
        );
        
        $items = [];
        if (!empty($period)) {
            $items = $itemModel->getByPeriod($period[0]['id']);
        }
        
        $data = [
            'title' => 'Tax Report (PPh 21) - ' . date('F Y', strtotime("$year-$month-01")),
            'items' => $items,
            'month' => $month,
            'year' => $year,
            'totalTax' => array_sum(array_column($items, 'tax_amount')),
        ];
        
        return $this->view('reports/tax', $data);
    }
    
    /**
     * Audit log report (Feature 15)
     */
    public function auditLog()
    {
        $contractId = $this->input('contract_id');
        $logModel = new ContractLogModel($this->db);
        
        if ($contractId) {
            $logs = $logModel->getByContract($contractId, 100);
        } else {
            $logs = $logModel->query(
                "SELECT cl.*, c.contract_no FROM contract_logs cl 
                 LEFT JOIN contracts c ON cl.contract_id = c.id 
                 ORDER BY cl.created_at DESC LIMIT 100"
            );
        }
        
        $data = [
            'title' => 'Audit Log Report',
            'logs' => $logs,
            'contractId' => $contractId,
        ];
        
        return $this->view('reports/audit', $data);
    }
    
    /**
     * Export report to CSV
     */
    public function export($type)
    {
        $contractModel = new ContractModel($this->db);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        switch ($type) {
            case 'active':
                fputcsv($output, ['Contract No', 'Crew Name', 'Rank', 'Vessel', 'Client', 'Sign On', 'Sign Off', 'Status']);
                $contracts = $contractModel->getList(['status' => 'active'], 1, 1000);
                foreach ($contracts as $c) {
                    fputcsv($output, [
                        $c['contract_no'], $c['crew_name'], $c['rank_name'] ?? '',
                        $c['vessel_name'] ?? '', $c['client_name'] ?? '',
                        $c['sign_on_date'], $c['sign_off_date'], $c['status']
                    ]);
                }
                break;
                
            case 'expiring':
                fputcsv($output, ['Contract No', 'Crew Name', 'Rank', 'Vessel', 'Sign Off', 'Days Remaining']);
                $contracts = $contractModel->getExpiring(60);
                foreach ($contracts as $c) {
                    fputcsv($output, [
                        $c['contract_no'], $c['crew_name'], $c['rank_name'] ?? '',
                        $c['vessel_name'] ?? '', $c['sign_off_date'], $c['days_remaining']
                    ]);
                }
                break;
        }
        
        fclose($output);
        exit;
    }
}
