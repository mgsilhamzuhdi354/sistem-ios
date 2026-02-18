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
require_once APPPATH . 'Models/ActivityModel.php';

use App\Models\ContractModel;
use App\Models\VesselModel;
use App\Models\ClientModel;
use App\Models\PayrollPeriodModel;
use App\Models\ActivityModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Get UI mode from session (default: classic)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

        // Get dashboard stats
        $contractModel = new ContractModel($this->db);
        $vesselModel = new VesselModel($this->db);
        $clientModel = new ClientModel($this->db);

        // Build contract stats with expiringSoon alias
        $contractStats = $contractModel->getDashboardStats();
        $contractStats['expiringSoon'] = $contractStats['expiring_60'] ?? 0;
        $contractStats['total'] = ($contractStats['active'] ?? 0) + ($contractStats['draft'] ?? 0) + ($contractStats['pending'] ?? 0);
        $contractStats['onboard'] = $contractStats['total_crew'] ?? 0;

        $data = [
            'title' => 'Dashboard',
            'currentPage' => 'dashboard',
            'contractStats' => $contractStats,
            'vesselStats' => $vesselModel->getDashboardStats(),
            'clientCount' => $clientModel->getDashboardStats(),
            'expiringContracts' => $contractModel->getExpiring(90),
            'recentContracts' => $contractModel->getList([], 1, 5),
            'vesselsProfitData' => $vesselModel->getAllVesselsProfit(),
            'clientsData' => $clientModel->getAllWithStats(),
            'flash' => $this->getFlash(),
            'uiMode' => $uiMode
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

        // Choose view based on UI mode
        $view = $uiMode === 'modern' ? 'dashboard/modern' : 'dashboard/index';
        return $this->view($view, $data);
    }

    /**
     * Toggle UI mode (classic/modern)
     */
    public function toggleMode()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $mode = $_POST['mode'] ?? $_GET['mode'] ?? 'classic';
        $_SESSION['ui_mode'] = in_array($mode, ['classic', 'modern']) ? $mode : 'classic';

        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'mode' => $_SESSION['ui_mode']]);
            exit;
        }

        // Otherwise redirect back to dashboard
        $this->redirect('dashboard');
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

    /**
     * API: Get notifications for real-time polling
     */
    public function notifications()
    {
        $contractModel = new ContractModel($this->db);
        $activityModel = new ActivityModel($this->db);

        $notifications = [];

        // Expiring contracts (urgent: ≤7 days)
        $expiring7 = $contractModel->getExpiring(7);
        foreach ($expiring7 as $c) {
            $days = $c['days_remaining'] ?? 0;
            $notifications[] = [
                'type' => 'urgent',
                'icon' => 'error',
                'title' => 'Kontrak Segera Berakhir',
                'message' => ($c['crew_name'] ?? 'N/A') . ' - ' . ($c['vessel_name'] ?? 'N/A') . " ({$days} hari lagi)",
                'url' => BASE_URL . 'contracts/view/' . $c['id'],
                'time' => $c['sign_off_date'] ?? null
            ];
        }

        // Expiring contracts (warning: ≤30 days)
        $expiring30 = $contractModel->getExpiring(30);
        foreach ($expiring30 as $c) {
            $days = $c['days_remaining'] ?? 0;
            if ($days > 7) {
                $notifications[] = [
                    'type' => 'warning',
                    'icon' => 'warning',
                    'title' => 'Kontrak Akan Berakhir',
                    'message' => ($c['crew_name'] ?? 'N/A') . ' - ' . ($c['vessel_name'] ?? 'N/A') . " ({$days} hari lagi)",
                    'url' => BASE_URL . 'contracts/view/' . $c['id'],
                    'time' => $c['sign_off_date'] ?? null
                ];
            }
        }

        // Recent activities (info)
        $activities = $activityModel->getRecent(5);
        foreach ($activities as $act) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'info',
                'title' => ucfirst($act['action'] ?? 'Activity'),
                'message' => $act['description'] ?? '',
                'url' => null,
                'time' => $act['created_at'] ?? null
            ];
        }

        $stats = $contractModel->getDashboardStats();

        $this->json([
            'success' => true,
            'data' => $notifications,
            'counts' => [
                'total' => count($notifications),
                'urgent' => count($expiring7),
                'warning' => count(array_filter($notifications, fn($n) => $n['type'] === 'warning')),
                'expiring_60' => $stats['expiring_60'] ?? 0
            ]
        ]);
    }
}
