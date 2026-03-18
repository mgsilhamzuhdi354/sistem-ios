<?php
/**
 * PT Indo Ocean - ERP System
 * Client Controller
 */

namespace App\Controllers;

require_once APPPATH . 'Models/ClientModel.php';

use App\Models\ClientModel;

class Client extends BaseController
{
    private $clientModel;

    public function __construct()
    {
        parent::__construct();
        $this->clientModel = new ClientModel($this->db);
    }

    public function index()
    {
        $this->requirePermission('clients', 'view');
        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';

        // Get clients with profit stats
        $clients = $this->clientModel->getAllWithStats();

        // Calculate profit data for each client (for modern view)
        if ($uiMode === 'modern') {
            foreach ($clients as &$client) {
                $contracts = $this->clientModel->getContractsWithSalary($client['id']);

                $monthlyProfit = 0;
                $monthlyRevenue = 0;
                $totalProfit = 0;

                foreach ($contracts as $c) {
                    if (in_array($c['status'], ['active', 'onboard'])) {
                        $monthlyProfit += $c['profit_usd'] ?? 0;
                        $monthlyRevenue += $c['client_rate_usd'] ?? 0;
                    }
                    $totalProfit += $c['total_profit_usd'] ?? 0;
                }

                $client['total_revenue'] = round($monthlyRevenue, 2);
                $client['total_profit'] = round($monthlyProfit, 2);
                $client['profit_margin'] = $monthlyRevenue > 0 ? round(($monthlyProfit / $monthlyRevenue) * 100, 1) : 0;

                // Calculate real contract growth percentage (replaces random mock data)
                $client['growth_percentage'] = $this->clientModel->getContractGrowthPercentage($client['id']);
            }

            // Sort by profit DESC for modern view
            usort($clients, function ($a, $b) {
                return $b['total_profit'] <=> $a['total_profit'];
            });
        }

        $data = [
            'title' => 'Client / Principal Management',
            'clients' => $clients,
            'flash' => $this->getFlash()
        ];

        // Add real KPI data for modern view
        if ($uiMode === 'modern') {
            $data['revenueGrowth'] = $this->clientModel->getRevenueGrowth();
            $data['marginGrowth'] = $this->clientModel->getMarginGrowth();
            $data['activeContracts'] = $this->clientModel->getActiveContractCount();
            $data['contractGrowth'] = $this->clientModel->getActiveContractGrowth();
            $data['revenueTrend'] = $this->clientModel->getMonthlyRevenueTrend(6);
        }

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'clients/modern' : 'clients/index';
        return $this->view($view, $data);
    }

    public function show($id)
    {
        $this->requirePermission('clients', 'view');
        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';

        $client = $this->clientModel->getWithStats($id);
        if (!$client) {
            $this->setFlash('error', 'Client not found');
            $this->redirect('clients');
        }

        // Get all contracts including inactive for complete crew view
        $contracts = $this->clientModel->getContractsWithSalary($id);

        // Calculate crew stats and profit
        $activeCrew = 0;
        $inactiveCrew = 0;
        $totalProfit = 0;
        $totalClientRate = 0;
        $totalSalary = 0;
        $accumulatedProfit = 0;
        $accumulatedSalary = 0;
        $accumulatedClientRate = 0;

        foreach ($contracts as $c) {
            // Accumulated profit from ALL contracts (historical)
            $accumulatedProfit += $c['total_profit_usd'] ?? 0;
            $accumulatedSalary += ($c['salary_usd'] ?? 0) * ($c['months_active'] ?? 0);
            $accumulatedClientRate += ($c['client_rate_usd'] ?? 0) * ($c['months_active'] ?? 0);

            if (in_array($c['status'], ['active', 'onboard'])) {
                $activeCrew++;
                // Monthly profit only for active contracts (current)
                $totalProfit += $c['profit_usd'] ?? 0;
                $totalClientRate += $c['client_rate_usd'] ?? 0;
                $totalSalary += $c['salary_usd'] ?? 0;
            } else {
                $inactiveCrew++;
            }
        }

        $data = [
            'title' => $client['name'] . ' - Client Detail',
            'currentPage' => 'clients',
            'client' => $client,
            'vessels' => $this->clientModel->getVessels($id),
            'vesselProfitData' => $this->clientModel->getVesselsProfitByClient($id),
            'contracts' => $contracts,
            'monthlyCost' => $this->clientModel->getMonthlyCostBreakdown($id),
            'stats' => [
                'active_crew' => $activeCrew,
                'inactive_crew' => $inactiveCrew,
            ],
            'profit' => [
                'monthly_profit_usd' => round($totalProfit, 2),
                'monthly_client_rate_usd' => round($totalClientRate, 2),
                'monthly_salary_usd' => round($totalSalary, 2),
                'accumulated_profit_usd' => round($accumulatedProfit, 2),
                'accumulated_salary_usd' => round($accumulatedSalary, 2),
                'accumulated_client_rate_usd' => round($accumulatedClientRate, 2),
            ],
            'flash' => $this->getFlash()
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'clients/detail_modern' : 'clients/view';
        return $this->view($view, $data);
    }

    public function create()
    {
        $this->requirePermission('clients', 'create');
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $data = ['title' => 'Add New Client'];
        $view = $uiMode === 'modern' ? 'clients/form_modern' : 'clients/form';
        return $this->view($view, $data);
    }

    public function store()
    {
        $this->requirePermission('clients', 'create');
        if (!$this->isPost()) {
            $this->redirect('clients');
        }

        $data = [
            'name' => $this->input('name'),
            'short_name' => $this->input('short_name'),
            'country' => $this->input('country'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'website' => $this->input('website'),
            'contact_person' => $this->input('contact_person'),
            'contact_email' => $this->input('contact_email'),
            'contact_phone' => $this->input('contact_phone'),
        ];

        $id = $this->clientModel->insert($data);
        $this->setFlash('success', 'Client added successfully');
        $this->redirect('clients/' . $id);
    }

    public function edit($id)
    {
        $this->requirePermission('clients', 'edit');
        $client = $this->clientModel->find($id);
        if (!$client) {
            $this->setFlash('error', 'Client not found');
            $this->redirect('clients');
        }

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $data = [
            'title' => 'Edit ' . $client['name'],
            'client' => $client,
        ];

        $view = $uiMode === 'modern' ? 'clients/form_modern' : 'clients/form';
        return $this->view($view, $data);
    }

    public function update($id)
    {
        $this->requirePermission('clients', 'edit');
        if (!$this->isPost()) {
            $this->redirect('clients/' . $id);
        }

        $data = [
            'name' => $this->input('name'),
            'short_name' => $this->input('short_name'),
            'country' => $this->input('country'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
        ];

        $this->clientModel->update($id, $data);
        $this->setFlash('success', 'Client updated');
        $this->redirect('clients/' . $id);
    }

    /**
     * Show confirmation page before deleting a client
     */
    public function confirmDelete($id)
    {
        $this->requirePermission('clients', 'edit');
        
        $client = $this->clientModel->find($id);
        if (!$client) {
            $this->setFlash('error', 'Client tidak ditemukan');
            $this->redirect('clients');
            return;
        }

        // Count related data
        $db = $this->db;
        
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM vessels WHERE client_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $vesselCount = $stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();

        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM contracts WHERE client_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $contractCount = $stmt->get_result()->fetch_assoc()['cnt'];
        $stmt->close();

        $data = [
            'title' => 'Konfirmasi Hapus Client',
            'client' => $client,
            'vesselCount' => $vesselCount,
            'contractCount' => $contractCount,
        ];

        return $this->view('clients/confirm_delete', $data);
    }

    /**
     * Delete a client and ALL related data (cascade delete)
     * Order: payroll_items → contract_* → contracts → finance_invoices → vessels → clients
     */
    public function delete($id)
    {
        $this->requirePermission('clients', 'edit');
        
        $client = $this->clientModel->find($id);
        if (!$client) {
            $this->setFlash('error', 'Client tidak ditemukan');
            $this->redirect('clients');
            return;
        }

        $clientName = $client['name'];
        $db = $this->db;

        try {
            // Get all contract IDs for this client
            $stmt = $db->prepare("SELECT id FROM contracts WHERE client_id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $contractIds = [];
            while ($row = $result->fetch_assoc()) {
                $contractIds[] = $row['id'];
            }
            $stmt->close();

            if (!empty($contractIds)) {
                $placeholders = implode(',', array_fill(0, count($contractIds), '?'));
                $types = str_repeat('i', count($contractIds));

                // 1. Delete payroll_items for these contracts
                $stmt = $db->prepare("DELETE FROM payroll_items WHERE contract_id IN ($placeholders)");
                $stmt->bind_param($types, ...$contractIds);
                $stmt->execute();
                $stmt->close();

                // 2. Delete contract child tables
                $childTables = ['contract_approvals', 'contract_deductions', 'contract_documents', 'contract_logs', 'contract_salaries', 'contract_taxes'];
                foreach ($childTables as $table) {
                    $stmt = $db->prepare("DELETE FROM `$table` WHERE contract_id IN ($placeholders)");
                    $stmt->bind_param($types, ...$contractIds);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            // 3. Delete all contracts for this client
            $stmt = $db->prepare("DELETE FROM contracts WHERE client_id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $deletedContracts = $stmt->affected_rows;
            $stmt->close();

            // 4. Delete finance invoices for this client (table may not exist)
            $stmt = $db->prepare("DELETE FROM finance_invoices WHERE client_id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }

            // 5. Delete all vessels owned by this client
            $stmt = $db->prepare("DELETE FROM vessels WHERE client_id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $deletedVessels = $stmt->affected_rows;
            $stmt->close();

            // 6. Delete the client itself
            $stmt = $db->prepare("DELETE FROM clients WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            $msg = 'Client "' . $clientName . '" berhasil dihapus';
            if ($deletedContracts > 0 || $deletedVessels > 0) {
                $msg .= ' beserta ' . $deletedContracts . ' kontrak dan ' . $deletedVessels . ' kapal terkait';
            }
            $this->setFlash('success', $msg);
        } catch (\Exception $e) {
            $this->setFlash('error', 'Gagal menghapus client: ' . $e->getMessage());
        }

        $this->redirect('clients');
    }

    /**
     * Profit per Client Analysis
     * DEPRECATED: Now integrated into main client list (modern view)
     * Redirects to clients index
     */
    public function profit()
    {
        // Profit analytics now integrated in main client list
        $this->redirect('clients');
    }
}
