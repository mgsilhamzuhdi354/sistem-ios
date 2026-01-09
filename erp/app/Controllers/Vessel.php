<?php
/**
 * PT Indo Ocean - ERP System
 * Vessel Controller
 */

namespace App\Controllers;

require_once APPPATH . 'Models/VesselModel.php';
require_once APPPATH . 'Models/ClientModel.php';

use App\Models\VesselModel;
use App\Models\VesselTypeModel;
use App\Models\FlagStateModel;
use App\Models\ClientModel;

class Vessel extends BaseController
{
    private $vesselModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->vesselModel = new VesselModel($this->db);
    }
    
    public function index()
    {
        $data = [
            'title' => 'Vessel Management',
            'vessels' => $this->vesselModel->getAllWithDetails(),
            'flash' => $this->getFlash()
        ];
        
        return $this->view('vessels/index', $data);
    }
    
    public function show($id)
    {
        $vessel = $this->vesselModel->getWithDetails($id);
        if (!$vessel) {
            $this->setFlash('error', 'Vessel not found');
            $this->redirect('vessels');
        }
        
        $data = [
            'title' => $vessel['name'],
            'vessel' => $vessel,
            'crewList' => $this->vesselModel->getCrewList($id),
            'totalCost' => $this->vesselModel->getTotalMonthlyCost($id),
            'flash' => $this->getFlash()
        ];
        
        return $this->view('vessels/view', $data);
    }
    
    public function create()
    {
        $vesselTypeModel = new VesselTypeModel($this->db);
        $flagStateModel = new FlagStateModel($this->db);
        $clientModel = new ClientModel($this->db);
        
        $data = [
            'title' => 'Add New Vessel',
            'vesselTypes' => $vesselTypeModel->getForDropdown(),
            'flagStates' => $flagStateModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
        ];
        
        return $this->view('vessels/form', $data);
    }
    
    public function store()
    {
        if (!$this->isPost()) {
            $this->redirect('vessels');
        }
        
        $data = [
            'name' => $this->input('name'),
            'imo_number' => $this->input('imo_number'),
            'vessel_type_id' => $this->input('vessel_type_id'),
            'flag_state_id' => $this->input('flag_state_id'),
            'client_id' => $this->input('client_id'),
            'gross_tonnage' => $this->input('gross_tonnage'),
            'dwt' => $this->input('dwt'),
            'year_built' => $this->input('year_built'),
            'call_sign' => $this->input('call_sign'),
            'crew_capacity' => $this->input('crew_capacity', 25),
            'status' => 'active',
        ];
        
        $id = $this->vesselModel->insert($data);
        $this->setFlash('success', 'Vessel added successfully');
        $this->redirect('vessels/' . $id);
    }
    
    public function edit($id)
    {
        $vessel = $this->vesselModel->find($id);
        if (!$vessel) {
            $this->setFlash('error', 'Vessel not found');
            $this->redirect('vessels');
        }
        
        $vesselTypeModel = new VesselTypeModel($this->db);
        $flagStateModel = new FlagStateModel($this->db);
        $clientModel = new ClientModel($this->db);
        
        $data = [
            'title' => 'Edit ' . $vessel['name'],
            'vessel' => $vessel,
            'vesselTypes' => $vesselTypeModel->getForDropdown(),
            'flagStates' => $flagStateModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
        ];
        
        return $this->view('vessels/form', $data);
    }
    
    public function update($id)
    {
        if (!$this->isPost()) {
            $this->redirect('vessels/' . $id);
        }
        
        $data = [
            'name' => $this->input('name'),
            'imo_number' => $this->input('imo_number'),
            'vessel_type_id' => $this->input('vessel_type_id'),
            'flag_state_id' => $this->input('flag_state_id'),
            'client_id' => $this->input('client_id'),
            'status' => $this->input('status'),
        ];
        
        $this->vesselModel->update($id, $data);
        $this->setFlash('success', 'Vessel updated');
        $this->redirect('vessels/' . $id);
    }
    
    public function crewList($id)
    {
        $vessel = $this->vesselModel->getWithDetails($id);
        
        $data = [
            'title' => 'Crew List - ' . $vessel['name'],
            'vessel' => $vessel,
            'crewList' => $this->vesselModel->getCrewList($id),
        ];
        
        return $this->view('vessels/crew', $data);
    }
}
