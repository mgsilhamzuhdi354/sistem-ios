<?php
require_once APPPATH . 'Controllers/BaseController.php';

/**
 * Requests Controller - Handles both status change and job claim requests
 */
class Requests extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        if (!isLoggedIn() || !isMasterAdmin()) {
            redirect(url('/login'));
        }
    }

    public function index()
    {
        // Get pending job claim requests
        $claimResult = @$this->db->query("
            SELECT jcr.*, jcr.id as request_id, 'claim' as request_type,
                   a.id as app_id, u.full_name as applicant_name, jv.title as vacancy_title,
                   ur.full_name as requester_name
            FROM job_claim_requests jcr
            JOIN applications a ON jcr.application_id = a.id
            JOIN users u ON a.user_id = u.id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            JOIN users ur ON jcr.requested_by = ur.id
            WHERE jcr.status = 'pending'
            ORDER BY jcr.created_at DESC
        ");
        $pendingClaims = $claimResult ? $claimResult->fetch_all(MYSQLI_ASSOC) : [];

        // Get pending status change requests
        $statusResult = @$this->db->query("
            SELECT r.*, r.id as request_id, 'status' as request_type,
                   a.id as app_id, u.full_name as applicant_name, jv.title as vacancy_title,
                   ur.full_name as requester_name,
                   fs.name as from_status_name, fs.color as from_status_color,
                   ts.name as to_status_name, ts.color as to_status_color
            FROM status_change_requests r
            JOIN applications a ON r.application_id = a.id
            JOIN users u ON a.user_id = u.id
            LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
            JOIN users ur ON r.requested_by = ur.id
            JOIN application_statuses fs ON r.from_status_id = fs.id
            JOIN application_statuses ts ON r.to_status_id = ts.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
        ");
        $pendingStatus = $statusResult ? $statusResult->fetch_all(MYSQLI_ASSOC) : [];

        $this->view('master_admin/requests/index', [
            'pageTitle' => 'Approval Requests',
            'pendingClaims' => $pendingClaims,
            'pendingStatus' => $pendingStatus,
            'pendingCount' => count($pendingClaims) + count($pendingStatus)
        ]);
    }

    /**
     * Approve job claim request
     */
    public function approveClaim($id)
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $notes = trim($this->input('notes') ?: '');
            $adminId = $_SESSION['user_id'];

            // Get claim request
            $stmt = $this->db->prepare("SELECT * FROM job_claim_requests WHERE id = ? AND status = 'pending'");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $request = $stmt->get_result()->fetch_assoc();

            if (!$request) {
                return $this->json(['success' => false, 'message' => 'Request not found']);
            }

            // Start transaction
            $this->db->begin_transaction();

            // Update claim status
            $stmt = $this->db->prepare("UPDATE job_claim_requests SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), review_notes = ? WHERE id = ?");
            $stmt->bind_param('isi', $adminId, $notes, $id);
            $stmt->execute();

            // Mark other pending claims for same application as rejected
            $this->db->query("UPDATE job_claim_requests SET status = 'rejected', review_notes = 'Another request was approved' WHERE application_id = {$request['application_id']} AND id != $id AND status = 'pending'");

            // Create assignment
            $assignStmt = $this->db->prepare("INSERT INTO application_assignments (application_id, assigned_to, assigned_by, notes, status, assigned_at) VALUES (?, ?, ?, 'Approved claim request', 'active', NOW())");
            $assignStmt->bind_param('iii', $request['application_id'], $request['requested_by'], $adminId);
            $assignStmt->execute();

            // Update application
            // Use prepared statement instead of raw query
            $updateAppStmt = $this->db->prepare("UPDATE applications SET current_crewing_id = ? WHERE id = ?");
            $updateAppStmt->bind_param('ii', $request['requested_by'], $request['application_id']);
            $updateAppStmt->execute();

            // Commit transaction
            $this->db->commit();

            // AUTO-SYNC TO ERP: automatically push to ERP and set Processing status
            $this->autoPushToErp($request['application_id']);

            // Notify requester
            notifyUser(
                $request['requested_by'],
                'Claim Request Approved!',
                'Lamaran sudah di-approve dan otomatis dikirim ke ERP untuk proses Admin Checklist.',
                'success',
                url('/crewing/pipeline?view=my')
            );

            return $this->json(['success' => true, 'message' => 'Claim approved! Kandidat otomatis dikirim ke ERP.']);

        } catch (Exception $e) {
            $this->db->rollback();
            return $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject job claim request
     */
    public function rejectClaim($id)
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $notes = trim($this->input('notes') ?: 'Request ditolak');
        $adminId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("UPDATE job_claim_requests SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), review_notes = ? WHERE id = ?");
        $stmt->bind_param('isi', $adminId, $notes, $id);

        if ($stmt->execute()) {
            // Get requester
            $reqStmt = $this->db->prepare("SELECT requested_by FROM job_claim_requests WHERE id = ?");
            $reqStmt->bind_param('i', $id);
            $reqStmt->execute();
            $req = $reqStmt->get_result()->fetch_assoc();

            if ($req) {
                notifyUser(
                    $req['requested_by'],
                    'Claim Request Rejected',
                    "Your claim request was rejected. Reason: $notes",
                    'warning',
                    url('/crewing/pipeline')
                );
            }

            return $this->json(['success' => true, 'message' => 'Request rejected']);
        }

        return $this->json(['success' => false, 'message' => 'Failed to reject']);
    }

    /**
     * Approve status change request
     */
    public function approve($id)
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $notes = trim($this->input('notes') ?: '');
        $adminId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT * FROM status_change_requests WHERE id = ? AND status = 'pending'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();

        if (!$request) {
            return $this->json(['success' => false, 'message' => 'Request not found']);
        }

        // Update request
        $stmt = $this->db->prepare("UPDATE status_change_requests SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), review_notes = ? WHERE id = ?");
        $stmt->bind_param('isi', $adminId, $notes, $id);
        $stmt->execute();

        // Apply status change
        $stmt = $this->db->prepare("UPDATE applications SET status_id = ?, status_updated_at = NOW(), updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('ii', $request['to_status_id'], $request['application_id']);
        $stmt->execute();

        // AUTO-PUSH TO ERP: When approved status is 6 (Hired)
        if ($request['to_status_id'] == 6) {
            $this->autoPushToErp($request['application_id']);
        }

        notifyUser(
            $request['requested_by'],
            'Status Change Approved',
            'Your status change request has been approved.',
            'success',
            url('/crewing/pipeline')
        );

        return $this->json(['success' => true, 'message' => 'Status change approved']);
    }

    /**
     * Auto-push approved applicant to ERP crew database
     */
    private function autoPushToErp($applicationId) {
        try {
            require_once APPPATH . 'Libraries/ErpSync.php';
            
            $stmt = $this->db->prepare("
                SELECT a.*, u.full_name, u.email, u.phone, u.avatar,
                       ap.gender, ap.date_of_birth, ap.place_of_birth,
                       ap.nationality, ap.address as profile_address,
                       ap.city, ap.postal_code, ap.profile_photo,
                       ap.emergency_name, ap.emergency_phone, ap.emergency_relation,
                       ap.total_sea_service_months,
                       cp.employee_id,
                       jv.title as job_title
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                LEFT JOIN crewing_profiles cp ON u.id = cp.user_id
                LEFT JOIN job_vacancies jv ON a.vacancy_id = jv.id
                WHERE a.id = ?
            ");
            $stmt->bind_param('i', $applicationId);
            $stmt->execute();
            $app = $stmt->get_result()->fetch_assoc();
            
            if (!$app || !empty($app['sent_to_erp_at'])) return;
            
            $rawGender = strtolower(trim($app['gender'] ?? ''));
            $gender = in_array($rawGender, ['female', 'perempuan', 'p', 'f', 'w']) ? 'female' : 'male';
            
            $crewData = [
                'full_name' => $app['full_name'],
                'email' => $app['email'] ?? '',
                'phone' => $app['phone'] ?? '',
                'employee_id' => $app['employee_id'] ?: ('IO' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),
                'candidate_id' => $app['user_id'],
                'status' => 'pending_approval',
                'notes' => 'Auto-synced from recruitment: ' . ($app['job_title'] ?? ''),
                'gender' => $gender,
                'birth_date' => $app['date_of_birth'] ?? null,
                'birth_place' => $app['place_of_birth'] ?? '',
                'nationality' => $app['nationality'] ?? 'Indonesian',
                'address' => $app['profile_address'] ?? '',
                'city' => $app['city'] ?? '',
                'postal_code' => $app['postal_code'] ?? '',
                'emergency_name' => $app['emergency_name'] ?? '',
                'emergency_phone' => $app['emergency_phone'] ?? '',
                'emergency_relation' => $app['emergency_relation'] ?? '',
                'total_sea_time_months' => intval($app['total_sea_service_months'] ?? 0),
            ];
            
            $erpSync = new \ErpSync($this->db);
            $existingCrewId = $erpSync->getCrewByCandidateId($app['user_id']);
            $crewId = $existingCrewId;
            if ($existingCrewId) {
                $erpSync->updateCrew($existingCrewId, $crewData);
            } else {
                $crewId = $erpSync->createCrew($crewData);
            }
            
            // Sync photo
            $photoPath = $app['profile_photo'] ?: ($app['avatar'] ?? '');
            if ($photoPath) {
                $erpPhotoPath = $erpSync->syncPhoto($photoPath, $crewId);
                if ($erpPhotoPath) $erpSync->updateCrew($crewId, ['photo' => $erpPhotoPath]);
            }
            
            // Sync documents
            $docStmt = $this->db->prepare("
                SELECT d.*, dt.name as type_name, dt.name_id as type_name_id
                FROM documents d LEFT JOIN document_types dt ON d.document_type_id = dt.id
                WHERE d.user_id = ?
            ");
            $docStmt->bind_param('i', $app['user_id']);
            $docStmt->execute();
            $documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            if (!empty($documents)) {
                $erpSync->syncDocuments($crewId, $documents, $_SESSION['user_id']);
            }
            
            // Mark as sent to ERP + set Processing status (8)
            $updateStmt = $this->db->prepare("
                UPDATE applications 
                SET sent_to_erp_at = NOW(), erp_crew_id = ?, status_id = 8, status_updated_at = NOW() 
                WHERE id = ?
            ");
            $updateStmt->bind_param('ii', $crewId, $applicationId);
            $updateStmt->execute();
            
        } catch (\Throwable $e) {
            error_log("Auto-push to ERP failed for application #$applicationId: " . $e->getMessage());
        }
    }

    /**
     * Reject status change request
     */
    public function reject($id)
    {
        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $notes = trim($this->input('notes') ?: 'Rejected');
        $adminId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("UPDATE status_change_requests SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), review_notes = ? WHERE id = ?");
        $stmt->bind_param('isi', $adminId, $notes, $id);
        $stmt->execute();

        $reqStmt = $this->db->prepare("SELECT requested_by FROM status_change_requests WHERE id = ?");
        $reqStmt->bind_param('i', $id);
        $reqStmt->execute();
        $req = $reqStmt->get_result()->fetch_assoc();

        if ($req) {
            notifyUser(
                $req['requested_by'],
                'Status Change Rejected',
                "Your request was rejected. Reason: $notes",
                'warning',
                url('/crewing/pipeline')
            );
        }

        return $this->json(['success' => true, 'message' => 'Request rejected']);
    }
}
