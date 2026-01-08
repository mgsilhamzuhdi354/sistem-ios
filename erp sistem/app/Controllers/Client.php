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
        $data = [
            'title' => 'Client / Principal Management',
            'clients' => $this->clientModel->getAllWithStats(),
            'flash' => $this->getFlash()
        ];
        
        return $this->view('clients/index', $data);
    }
    
    public function show($id)
    {
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
            'client' => $client,
            'vessels' => $this->clientModel->getVessels($id),
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
        
        return $this->view('clients/view', $data);
    }
    
    public function create()
    {
        $data = ['title' => 'Add New Client'];
        return $this->view('clients/form', $data);
    }
    
    public function store()
    {
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
        $client = $this->clientModel->find($id);
        if (!$client) {
            $this->setFlash('error', 'Client not found');
            $this->redirect('clients');
        }
        
        $data = [
            'title' => 'Edit ' . $client['name'],
            'client' => $client,
        ];
        
        return $this->view('clients/form', $data);
    }
    
    public function update($id)
    {
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
}
