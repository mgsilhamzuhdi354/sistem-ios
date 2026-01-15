<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Transfer Controller
 * 
 * Controller untuk handle transfer crew ke ERP System
 */

namespace App\Controllers\MasterAdmin;

use App\Controllers\BaseController;
use App\Services\CrewTransferService;

class Transfer extends BaseController
{
    protected $transferService;
    
    public function __construct()
    {
        $this->transferService = new CrewTransferService();
    }
    
    /**
     * Display list of transferable applicants
     */
    public function index()
    {
        $data = [
            'title' => 'Transfer ke ERP',
            'applicants' => $this->transferService->getTransferableApplicants(),
        ];
        
        return view('master_admin/transfer/index', $data);
    }
    
    /**
     * Transfer single applicant to ERP
     */
    public function transfer($applicantId)
    {
        // Check if already exists in ERP
        $existing = $this->transferService->checkExistingCrew($applicantId);
        
        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data sudah ada di ERP dengan Employee ID: ' . $existing['employee_id']
            ]);
        }
        
        // Perform transfer
        $result = $this->transferService->transferToERP($applicantId);
        
        return $this->response->setJSON($result);
    }
    
    /**
     * Bulk transfer multiple applicants
     */
    public function bulkTransfer()
    {
        $applicantIds = $this->request->getJSON()->applicant_ids ?? [];
        
        if (empty($applicantIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada applicant yang dipilih'
            ]);
        }
        
        $results = [
            'success' => [],
            'failed' => []
        ];
        
        foreach ($applicantIds as $id) {
            $result = $this->transferService->transferToERP($id);
            
            if ($result['success']) {
                $results['success'][] = [
                    'applicant_id' => $id,
                    'crew_id' => $result['crew_id'],
                    'employee_id' => $result['employee_id']
                ];
            } else {
                $results['failed'][] = [
                    'applicant_id' => $id,
                    'message' => $result['message']
                ];
            }
        }
        
        return $this->response->setJSON([
            'success' => count($results['failed']) === 0,
            'message' => count($results['success']) . ' berhasil, ' . count($results['failed']) . ' gagal',
            'details' => $results
        ]);
    }
    
    /**
     * Preview applicant data before transfer
     */
    public function preview($applicantId)
    {
        $db = \Config\Database::connect();
        
        $applicant = $db->table('applicants')
            ->where('id', $applicantId)
            ->get()
            ->getRowArray();
        
        if (!$applicant) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Applicant tidak ditemukan'
            ]);
        }
        
        $documents = $db->table('applicant_documents')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getResultArray();
        
        $experiences = $db->table('applicant_experiences')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getResultArray();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'applicant' => $applicant,
                'documents' => $documents,
                'experiences' => $experiences
            ]
        ]);
    }
}
