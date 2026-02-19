<?php
require_once APPPATH . 'Controllers/BaseController.php';
require_once APPPATH . 'Libraries/ErpSync.php';

/**
 * ERP Integration Controller
 * Handles sending approved applicants to ERP system
 */
class ErpIntegration extends BaseController {
    
    public function __construct() {
        parent::__construct();
        if (!isLoggedIn() || !isCrewingOrAdmin()) {
            redirect(url('/login'));
        }
    }
    
    /**
     * Send applicant to ERP
     */
    public function sendToErp() {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request method']);
        }
        
        try {
            $applicationId = intval($this->input('application_id'));
            $rankId = intval($this->input('rank_id'));
            $joinDate = trim($this->input('join_date'));
            $notes = trim($this->input('notes'));
            
            // Validate inputs
            if (empty($applicationId)) {
                throw new Exception('Application ID is required');
            }
            
            if (empty($rankId)) {
                throw new Exception('Please select a rank');
            }
            
            // Get application details with FULL profile data
            $stmt = $this->db->prepare("
                SELECT 
                    a.*, 
                    u.full_name, u.email, u.phone, u.avatar,
                    jv.title as job_title,
                    cp.employee_id,
                    ap.gender, ap.date_of_birth, ap.place_of_birth,
                    ap.nationality, ap.address as profile_address,
                    ap.city, ap.postal_code,
                    ap.seaman_book_no, ap.seaman_book_expiry,
                    ap.passport_no, ap.passport_expiry,
                    ap.height_cm, ap.weight_kg, ap.blood_type,
                    ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
                    ap.total_sea_service_months, ap.last_vessel_name,
                    ap.last_vessel_type, ap.last_rank, ap.last_sign_off,
                    ap.profile_photo
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
                LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                WHERE a.id = ?
            ");
            $stmt->bind_param('i', $applicationId);
            $stmt->execute();
            $application = $stmt->get_result()->fetch_assoc();
            
            if (!$application) {
                throw new Exception('Application not found');
            }
            
            // Check if approved
            if ($application['status_id'] != 6) {
                throw new Exception('Only approved applications can be sent to ERP');
            }
            
            // Check if already sent
            if (!empty($application['sent_to_erp_at'])) {
                throw new Exception('This applicant has already been sent to ERP on ' . date('d M Y H:i', strtotime($application['sent_to_erp_at'])));
            }
            
            // Get all documents for this applicant
            $docStmt = $this->db->prepare("
                SELECT d.*, dt.name as type_name, dt.name_id as type_name_id
                FROM documents d
                LEFT JOIN document_types dt ON d.document_type_id = dt.id
                WHERE d.user_id = ?
            ");
            $docStmt->bind_param('i', $application['user_id']);
            $docStmt->execute();
            $documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // Prepare COMPLETE crew data
            $crewData = [
                // Basic info
                'full_name' => $application['full_name'],
                'email' => $application['email'],
                'phone' => $application['phone'],
                'employee_id' => $application['employee_id'] ?: ('IO' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),
                'candidate_id' => $application['user_id'],
                'rank_id' => $rankId,
                'status' => 'pending_approval',
                'join_date' => $joinDate ?: null,
                'notes' => $notes ?: "Recruited for: " . $application['job_title'],
                
                // Personal profile
                'gender' => $application['gender'] ?? 'male',
                'birth_date' => $application['date_of_birth'] ?? null,
                'birth_place' => $application['place_of_birth'] ?? '',
                'nationality' => $application['nationality'] ?? 'Indonesian',
                'address' => $application['profile_address'] ?: ($application['address'] ?? ''),
                'city' => $application['city'] ?? '',
                'postal_code' => $application['postal_code'] ?? '',
                
                // Emergency contact
                'emergency_name' => $application['emergency_name'] ?? '',
                'emergency_phone' => $application['emergency_phone'] ?? '',
                'emergency_relation' => $application['emergency_relation'] ?? '',
                
                // Sea experience
                'total_sea_time_months' => intval($application['total_sea_service_months'] ?? 0),
            ];
            
            // Send to ERP
            $erpSync = new ErpSync($this->db);
            
            // Check if already exists
            $existingCrewId = $erpSync->getCrewByCandidateId($application['user_id']);
            if ($existingCrewId) {
                // Update existing crew with full data
                $erpSync->updateCrew($existingCrewId, array_merge($crewData, [
                    'current_rank_id' => $rankId,
                ]));
                $crewId = $existingCrewId;
            } else {
                // Create new crew with full data
                $crewId = $erpSync->createCrew($crewData);
            }
            
            // Sync profile photo
            $photoPath = $application['profile_photo'] ?: $application['avatar'];
            if ($photoPath) {
                $erpPhotoPath = $erpSync->syncPhoto($photoPath, $crewId);
                if ($erpPhotoPath) {
                    $erpSync->updateCrew($crewId, ['photo' => $erpPhotoPath]);
                }
            }
            
            // Sync all documents (with file copying)
            $docsSynced = 0;
            if (!empty($documents)) {
                $docsSynced = $erpSync->syncDocuments($crewId, $documents, $_SESSION['user_id']);
            }
            
            // Update application
            $updateStmt = $this->db->prepare("
                UPDATE applications 
                SET sent_to_erp_at = NOW(), erp_crew_id = ? 
                WHERE id = ?
            ");
            $updateStmt->bind_param('ii', $crewId, $applicationId);
            $updateStmt->execute();
            
            $message = 'Successfully sent to ERP!';
            if ($docsSynced > 0) {
                $message .= " ($docsSynced documents transferred)";
            }
            
            return $this->json([
                'success' => true,
                'message' => $message,
                'crew_id' => $crewId,
                'documents_synced' => $docsSynced
            ]);
            
        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    
    /**
     * Check ERP status of an application
     */
    public function checkStatus() {
        $applicationId = intval($this->input('application_id'));
        
        if (empty($applicationId)) {
            return $this->json(['success' => false, 'message' => 'Application ID required']);
        }
        
        $stmt = $this->db->prepare("SELECT sent_to_erp_at, erp_crew_id FROM applications WHERE id = ?");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            return $this->json(['success' => false, 'message' => 'Application not found']);
        }
        
        return $this->json([
            'success' => true,
            'sent' => !empty($result['sent_to_erp_at']),
            'sent_at' => $result['sent_to_erp_at'],
            'crew_id' => $result['erp_crew_id']
        ]);
    }
    
    /**
     * Get ranks for dropdown
     */
    public function getRanks() {
        try {
            $erpSync = new ErpSync($this->db);
            $ranks = $erpSync->getRanks();
            
            return $this->json([
                'success' => true,
                'ranks' => $ranks
            ]);
        } catch (Exception $e) {
            error_log("ERP getRanks error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
