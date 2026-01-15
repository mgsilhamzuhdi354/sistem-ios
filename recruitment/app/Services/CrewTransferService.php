<?php
/**
 * PT Indo Ocean Crew Services - Recruitment System
 * Crew Transfer Service
 * 
 * Service untuk transfer data applicant yang sudah approved ke ERP System
 */

namespace App\Services;

class CrewTransferService
{
    protected $erpDb;
    protected $recruitmentDb;
    
    public function __construct()
    {
        // Connect to ERP database
        $this->erpDb = \Config\Database::connect('erp');
        // Connect to Recruitment database (default)
        $this->recruitmentDb = \Config\Database::connect();
    }
    
    /**
     * Transfer applicant to ERP as crew member
     * 
     * @param int $applicantId ID dari applicant yang akan ditransfer
     * @return array ['success' => bool, 'message' => string, 'crew_id' => int|null]
     */
    public function transferToERP(int $applicantId): array
    {
        try {
            // 1. Get applicant data
            $applicant = $this->getApplicant($applicantId);
            
            if (!$applicant) {
                return [
                    'success' => false,
                    'message' => 'Applicant tidak ditemukan',
                    'crew_id' => null
                ];
            }
            
            // Check if already transferred
            if ($applicant['status'] === 'transferred') {
                return [
                    'success' => false,
                    'message' => 'Applicant sudah pernah ditransfer ke ERP',
                    'crew_id' => null
                ];
            }
            
            // 2. Start transaction
            $this->erpDb->transStart();
            $this->recruitmentDb->transStart();
            
            // 3. Generate employee ID
            $employeeId = $this->generateEmployeeId();
            
            // 4. Insert to ERP crews table
            $crewData = [
                'employee_id' => $employeeId,
                'name' => $applicant['full_name'],
                'email' => $applicant['email'],
                'phone' => $applicant['phone'],
                'rank' => $applicant['position_applied'] ?? $applicant['rank'],
                'nationality' => $applicant['nationality'] ?? 'Indonesia',
                'date_of_birth' => $applicant['date_of_birth'] ?? null,
                'place_of_birth' => $applicant['place_of_birth'] ?? null,
                'address' => $applicant['address'] ?? null,
                'status' => 'available',
                'created_at' => date('Y-m-d H:i:s'),
                'source' => 'recruitment_system',
                'source_id' => $applicantId
            ];
            
            $this->erpDb->table('crews')->insert($crewData);
            $crewId = $this->erpDb->insertID();
            
            // 5. Transfer documents
            $this->transferDocuments($applicantId, $crewId);
            
            // 6. Transfer experiences
            $this->transferExperiences($applicantId, $crewId);
            
            // 7. Update applicant status in recruitment
            $this->recruitmentDb->table('applicants')
                ->where('id', $applicantId)
                ->update([
                    'status' => 'transferred',
                    'transferred_at' => date('Y-m-d H:i:s'),
                    'erp_crew_id' => $crewId
                ]);
            
            // 8. Commit transactions
            $this->erpDb->transComplete();
            $this->recruitmentDb->transComplete();
            
            if ($this->erpDb->transStatus() === false || $this->recruitmentDb->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return [
                'success' => true,
                'message' => "Berhasil transfer ke ERP dengan Employee ID: {$employeeId}",
                'crew_id' => $crewId,
                'employee_id' => $employeeId
            ];
            
        } catch (\Exception $e) {
            // Rollback on error
            $this->erpDb->transRollback();
            $this->recruitmentDb->transRollback();
            
            log_message('error', 'Crew transfer failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal transfer: ' . $e->getMessage(),
                'crew_id' => null
            ];
        }
    }
    
    /**
     * Get applicant data from recruitment database
     */
    protected function getApplicant(int $id): ?array
    {
        return $this->recruitmentDb->table('applicants')
            ->where('id', $id)
            ->get()
            ->getRowArray();
    }
    
    /**
     * Generate unique employee ID for ERP
     */
    protected function generateEmployeeId(): string
    {
        $year = date('Y');
        $prefix = 'EMP';
        
        // Get last employee number this year
        $lastCrew = $this->erpDb->table('crews')
            ->like('employee_id', "{$prefix}{$year}", 'after')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        
        if ($lastCrew && preg_match('/(\d{4})$/', $lastCrew['employee_id'], $matches)) {
            $lastNumber = (int)$matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Transfer applicant documents to ERP
     */
    protected function transferDocuments(int $applicantId, int $crewId): void
    {
        $documents = $this->recruitmentDb->table('applicant_documents')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getResultArray();
        
        foreach ($documents as $doc) {
            $this->erpDb->table('crew_documents')->insert([
                'crew_id' => $crewId,
                'document_type' => $doc['document_type'] ?? $doc['type'],
                'document_number' => $doc['document_number'] ?? $doc['number'],
                'issue_date' => $doc['issue_date'] ?? null,
                'expiry_date' => $doc['expiry_date'] ?? null,
                'issuing_authority' => $doc['issuing_authority'] ?? null,
                'file_path' => $doc['file_path'] ?? $doc['file'],
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
    
    /**
     * Transfer applicant experiences to ERP
     */
    protected function transferExperiences(int $applicantId, int $crewId): void
    {
        $experiences = $this->recruitmentDb->table('applicant_experiences')
            ->where('applicant_id', $applicantId)
            ->get()
            ->getResultArray();
        
        foreach ($experiences as $exp) {
            $this->erpDb->table('crew_experiences')->insert([
                'crew_id' => $crewId,
                'vessel_name' => $exp['vessel_name'] ?? $exp['ship_name'],
                'vessel_type' => $exp['vessel_type'] ?? $exp['ship_type'],
                'rank' => $exp['rank'] ?? $exp['position'],
                'company' => $exp['company'] ?? $exp['employer'],
                'start_date' => $exp['start_date'] ?? $exp['from_date'],
                'end_date' => $exp['end_date'] ?? $exp['to_date'],
                'gross_tonnage' => $exp['gross_tonnage'] ?? $exp['grt'],
                'engine_type' => $exp['engine_type'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
    
    /**
     * Get list of applicants ready for transfer
     */
    public function getTransferableApplicants(): array
    {
        return $this->recruitmentDb->table('applicants')
            ->whereIn('status', ['approved', 'hired', 'accepted'])
            ->where('status !=', 'transferred')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Check if applicant is already in ERP
     */
    public function checkExistingCrew(int $applicantId): ?array
    {
        $applicant = $this->getApplicant($applicantId);
        
        if (!$applicant) {
            return null;
        }
        
        // Check by email or phone
        return $this->erpDb->table('crews')
            ->groupStart()
                ->where('email', $applicant['email'])
                ->orWhere('phone', $applicant['phone'])
            ->groupEnd()
            ->get()
            ->getRowArray();
    }
}
