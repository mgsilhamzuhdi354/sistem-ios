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
            'currentPage' => 'reports',
            'flash' => $this->getFlash()
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/index_modern' : 'reports/index';

        return $this->view($view, $data);
    }

    /**
     * Active contracts report
     */
    public function activeContracts()
    {
        $contractModel = new ContractModel($this->db);

        $data = [
            'title' => 'Active Contracts Report',
            'currentPage' => 'reports',
            'contracts' => $contractModel->getList(['status' => 'active'], 1, 1000),
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/contracts_modern' : 'reports/contracts';

        return $this->view($view, $data);
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
            'currentPage' => 'reports',
            'contracts' => $contractModel->getExpiring($days),
            'days' => $days,
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/expiring_modern' : 'reports/expiring';

        return $this->view($view, $data);
    }

    /**
     * Contracts by vessel report
     */
    public function byVessel()
    {
        $vesselModel = new VesselModel($this->db);
        $vessels = $vesselModel->getAllWithDetails();

        foreach ($vessels as &$vessel) {
            $vessel['crew_list'] = $vesselModel->getCrewList($vessel['id']);
            $vessel['monthly_cost'] = $vesselModel->getTotalMonthlyCost($vessel['id']);
        }

        $data = [
            'title' => 'Contracts by Vessel Report',
            'currentPage' => 'reports',
            'vessels' => $vessels,
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/by_vessel_modern' : 'reports/by_vessel';

        return $this->view($view, $data);
    }

    /**
     * Contracts by client report
     */
    public function byClient()
    {
        $clientModel = new ClientModel($this->db);
        $clients = $clientModel->getAllWithStats();

        foreach ($clients as &$client) {
            $client['contracts'] = $clientModel->getContracts($client['id']);
        }

        $data = [
            'title' => 'Contracts by Client Report',
            'currentPage' => 'reports',
            'clients' => $clients,
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/by_client_modern' : 'reports/by_client';

        return $this->view($view, $data);
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
            [$year],
            'i'
        );

        $data = [
            'title' => 'Payroll Summary Report - ' . $year,
            'currentPage' => 'reports',
            'periods' => $periods,
            'year' => $year,
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/payroll_summary_modern' : 'reports/payroll_summary';

        return $this->view($view, $data);
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
            [$month, $year],
            'ii'
        );

        $items = [];
        if (!empty($period)) {
            $items = $itemModel->getByPeriod($period[0]['id']);
        }

        $data = [
            'title' => 'Tax Report (PPh 21) - ' . date('F Y', strtotime("$year-$month-01")),
            'currentPage' => 'reports',
            'items' => $items,
            'month' => $month,
            'year' => $year,
            'totalTax' => array_sum(array_column($items, 'tax_amount')),
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/tax_modern' : 'reports/tax';

        return $this->view($view, $data);
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
            'currentPage' => 'reports',
            'logs' => $logs,
            'contractId' => $contractId,
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/audit_modern' : 'reports/audit';

        return $this->view($view, $data);
    }

    /**
     * Employee report (from HRIS integration)
     */
    public function employees()
    {
        require_once APPPATH . 'Libraries/HrisApi.php';
        $hrisApi = new \App\Libraries\HrisApi();

        // Fetch all employees from HRIS
        $result = $hrisApi->getEmployees(['status' => 'aktif']);
        $employees = $result['data'] ?? [];

        // Calculate statistics
        $totalEmployees = count($employees);
        $byDepartment = [];

        foreach ($employees as $employee) {
            $dept = $employee['department'] ?? 'Unknown';
            if (!isset($byDepartment[$dept])) {
                $byDepartment[$dept] = 0;
            }
            $byDepartment[$dept]++;
        }

        $data = [
            'title' => 'Employee Report',
            'employees' => $employees,
            'totalEmployees' => $totalEmployees,
            'byDepartment' => $byDepartment,
            'success' => $result['success'],
            'error' => $result['error'] ?? null,
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'reports/employees_modern' : 'reports/employees';

        return $this->view($view, $data);
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
                        $c['contract_no'],
                        $c['crew_name'],
                        $c['rank_name'] ?? '',
                        $c['vessel_name'] ?? '',
                        $c['client_name'] ?? '',
                        $c['sign_on_date'],
                        $c['sign_off_date'],
                        $c['status']
                    ]);
                }
                break;

            case 'expiring':
                fputcsv($output, ['Contract No', 'Crew Name', 'Rank', 'Vessel', 'Sign Off', 'Days Remaining']);
                $contracts = $contractModel->getExpiring(60);
                foreach ($contracts as $c) {
                    fputcsv($output, [
                        $c['contract_no'],
                        $c['crew_name'],
                        $c['rank_name'] ?? '',
                        $c['vessel_name'] ?? '',
                        $c['sign_off_date'],
                        $c['days_remaining']
                    ]);
                }
                break;
        }

        fclose($output);
        exit;
    }
}
