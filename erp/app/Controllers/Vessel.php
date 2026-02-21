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
        $this->requirePermission('vessels', 'view');
        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

        $vessels = $this->vesselModel->getAllWithDetails();

        // Calculate statistics for modern view
        $totalCrew = 0;
        $maintenanceCount = 0;

        foreach ($vessels as &$vessel) {
            // Get crew count for each vessel
            $crewList = $this->vesselModel->getCrewList($vessel['id']);
            $vessel['crew_count'] = count($crewList);
            $totalCrew += $vessel['crew_count'];

            // Count vessels in maintenance
            if (isset($vessel['status']) && $vessel['status'] === 'maintenance') {
                $maintenanceCount++;
            }
        }

        $data = [
            'title' => 'Vessel Management',
            'vessels' => $vessels,
            'total_crew' => $totalCrew,
            'maintenance_count' => $maintenanceCount,
            'flash' => $this->getFlash()
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'vessels/modern' : 'vessels/index';
        return $this->view($view, $data);
    }

    public function show($id)
    {
        $this->requirePermission('vessels', 'view');
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
        $this->requirePermission('vessels', 'create');
        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';

        $vesselTypeModel = new VesselTypeModel($this->db);
        $flagStateModel = new FlagStateModel($this->db);
        $clientModel = new ClientModel($this->db);

        $data = [
            'title' => 'Add New Vessel',
            'vesselTypes' => $vesselTypeModel->getForDropdown(),
            'flagStates' => $flagStateModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'vessels/form_modern' : 'vessels/form';
        return $this->view($view, $data);
    }

    public function store()
    {
        $this->requirePermission('vessels', 'create');
        if (!$this->isPost()) {
            $this->redirect('vessels');
        }

        $data = [
            'name' => $this->input('name'),
            'imo_number' => $this->input('imo_number'),
            'vessel_type_id' => $this->input('vessel_type_id') ?: null,
            'flag_state_id' => $this->input('flag_state_id') ?: null,
            'client_id' => $this->input('client_id') ?: null,
            'gross_tonnage' => $this->input('gross_tonnage') ?: null,
            'dwt' => $this->input('dwt') ?: null,
            'year_built' => $this->input('year_built') ?: null,
            'call_sign' => $this->input('call_sign'),
            'crew_capacity' => $this->input('crew_capacity', 25) ?: 25,
            'status' => 'active',
        ];

        $id = $this->vesselModel->insert($data);
        $this->setFlash('success', 'Vessel added successfully');
        $this->redirect('vessels/' . $id);
    }

    public function edit($id)
    {
        $this->requirePermission('vessels', 'edit');
        $vessel = $this->vesselModel->find($id);
        if (!$vessel) {
            $this->setFlash('error', 'Vessel not found');
            $this->redirect('vessels');
        }

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

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

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'vessels/edit_modern' : 'vessels/form';
        return $this->view($view, $data);
    }

    public function update($id)
    {
        $this->requirePermission('vessels', 'edit');
        if (!$this->isPost()) {
            $this->redirect('vessels/' . $id);
        }

        $data = [
            'name' => $this->input('name'),
            'imo_number' => $this->input('imo_number'),
            'vessel_type_id' => $this->input('vessel_type_id') ?: null,
            'flag_state_id' => $this->input('flag_state_id') ?: null,
            'client_id' => $this->input('client_id') ?: null,
            'gross_tonnage' => $this->input('gross_tonnage') ?: null,
            'dwt' => $this->input('dwt') ?: null,
            'year_built' => $this->input('year_built') ?: null,
            'call_sign' => $this->input('call_sign'),
            'crew_capacity' => $this->input('crew_capacity', 25) ?: 25,
            'status' => $this->input('status'),
        ];

        // Handle photo upload
        if (isset($_FILES['vessel_photo']) && $_FILES['vessel_photo']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->handlePhotoUpload($_FILES['vessel_photo'], $id);
            if ($upload['success']) {
                $data['image_url'] = $upload['url'];
            }
        }

        $this->vesselModel->update($id, $data);
        $this->setFlash('success', 'Vessel updated successfully');
        $this->redirect('vessels');
    }

    /**
     * Handle vessel photo upload
     */
    private function handlePhotoUpload($file, $vesselId)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File too large'];
        }

        // Create upload directory if not exists
        $uploadDir = APPPATH . '../public/uploads/vessels/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'vessel_' . $vesselId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'url' => 'public/uploads/vessels/' . $filename  // Store relative path
            ];
        }

        return ['success' => false, 'error' => 'Upload failed'];
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

    /**
     * Profit per Vessel report
     */
    public function profit()
    {
        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

        $data = [
            'title' => 'Profit per Vessel',
            'currentPage' => 'vessels',
            'profitData' => $this->vesselModel->getAllVesselsProfit(),
            'flash' => $this->getFlash()
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'vessels/profit_modern' : 'vessels/profit';
        return $this->view($view, $data);
    }
}

