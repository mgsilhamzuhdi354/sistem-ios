<?php
/**
 * PT Indo Ocean - ERP System
 * Dashboard Controller
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

class Dashboard extends BaseController
{
    public function index()
    {
        // Get dashboard stats
        $contractModel = new ContractModel($this->db);
        $vesselModel = new VesselModel($this->db);
        $clientModel = new ClientModel($this->db);
        
        $data = [
            'title' => 'Dashboard',
            'contractStats' => $contractModel->getDashboardStats(),
            'vesselStats' => $vesselModel->getDashboardStats(),
            'clientCount' => $clientModel->getDashboardStats(),
            'expiringContracts' => $contractModel->getExpiring(60),
            'recentContracts' => $contractModel->getList([], 1, 5),
            'flash' => $this->getFlash()
        ];
        
        // Get monthly payroll total from active contract salaries (accurate calculation per contract)
        $sql = "SELECT cs.total_monthly, cs.exchange_rate, cur.code as currency_code
                FROM contracts c
                JOIN contract_salaries cs ON c.id = cs.contract_id
                LEFT JOIN currencies cur ON cs.currency_id = cur.id
                WHERE c.status IN ('active', 'onboard')";
        
        $result = $this->db->query($sql);
        $monthlyPayrollUsd = 0;
        $defaultRates = ['USD' => 1.0, 'IDR' => 0.000063, 'SGD' => 0.74, 'EUR' => 1.05];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $amount = $row['total_monthly'] ?? 0;
                $currency = $row['currency_code'] ?? null;
                $exchangeRate = $row['exchange_rate'] ?? 0;
                
                // Auto-detect currency: if NULL or USD but amount > 1M, assume IDR
                if (!$currency || ($currency === 'USD' && $amount > 1000000)) {
                    $currency = 'IDR';
                }
                
                // Calculate USD for this contract using ITS exchange rate
                if ($currency === 'USD') {
                    $monthlyPayrollUsd += $amount;
                } elseif ($exchangeRate > 0) {
                    $monthlyPayrollUsd += $amount / $exchangeRate;
                } else {
                    $monthlyPayrollUsd += $amount * ($defaultRates[$currency] ?? 0.000063);
                }
            }
        }
        $data['monthlyPayroll'] = round($monthlyPayrollUsd, 2);
        
        return $this->view('dashboard/index', $data);
    }
    
    public function stats()
    {
        // API endpoint for dashboard stats
        $contractModel = new ContractModel($this->db);
        
        $this->json([
            'success' => true,
            'data' => [
                'contracts' => $contractModel->getDashboardStats(),
                'expiring' => $contractModel->getExpiring(7)
            ]
        ]);
    }
}
