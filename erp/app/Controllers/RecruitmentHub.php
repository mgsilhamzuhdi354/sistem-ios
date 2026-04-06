<?php
/**
 * PT Indo Ocean - ERP System
 * Recruitment Hub Controller
 * Integrates with Recruitment system
 */

namespace App\Controllers;

class RecruitmentHub extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        // recruitmentDb is already connected by BaseController via connectDatabase()
        // which includes Docker/NAS IP fallback logic
        if (!$this->recruitmentDb || (property_exists($this->recruitmentDb, 'connect_error') && $this->recruitmentDb->connect_error)) {
            error_log("RecruitmentHub: Recruitment DB not available via BaseController");
        }
    }

    /**
     * Recruitment pipeline view
     */
    public function pipeline()
    {
        $this->requireAuth();

        // Get pipeline statistics and candidates from recruitment DB
        $stats = [];
        $candidates = [];

        if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
            // Count by status
            $query = "
                SELECT 
                    s.name as status,
                    COUNT(*) as count
                FROM applications a
                JOIN application_statuses s ON a.status_id = s.id
                WHERE a.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY a.status_id, s.name
            ";
            $result = $this->recruitmentDb->query($query);

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $stats[$row['status']] = $row['count'];
                }
            }

            // Get candidates list — use safe query without optional columns that may not exist
            // First try with all columns, fallback to basic query
            $candidatesQuery = "
                SELECT 
                    a.id,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.avatar,
                    v.title as vacancy_title,
                    d.name as department_name,
                    s.name as status_name,
                    s.color as status_color,
                    a.submitted_at,
                    a.updated_at
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                JOIN application_statuses s ON a.status_id = s.id
                ORDER BY a.submitted_at DESC
                LIMIT 100
            ";
            $candidatesResult = $this->recruitmentDb->query($candidatesQuery);

            if ($candidatesResult) {
                while ($row = $candidatesResult->fetch_assoc()) {
                    $candidates[] = $row;
                }
            }
        }

        // === USE ERP's recruitment_sync TABLE as SOURCE OF TRUTH ===
        // This table is in our own ERP DB, so we have full control
        $syncedAppIds = [];  // application_id => crew_id mapping
        $syncResult = $this->db->query("SELECT recruitment_applicant_id, crew_id FROM recruitment_sync");
        if ($syncResult) {
            while ($row = $syncResult->fetch_assoc()) {
                $syncedAppIds[(int)$row['recruitment_applicant_id']] = (int)$row['crew_id'];
            }
        }

        // Get admin checklist progress for synced crews
        $checklistProgress = [];
        $syncedCrewIds = array_values(array_filter($syncedAppIds));
        if (!empty($syncedCrewIds)) {
            $idList = implode(',', array_map('intval', $syncedCrewIds));
            $clResult = $this->db->query("
                SELECT crew_id,
                    (IFNULL(step1_complete,0) + IFNULL(step2_complete,0) + IFNULL(step3_complete,0) + 
                     IFNULL(step4_complete,0) + IFNULL(step5_complete,0) + IFNULL(step6_complete,0)) as progress
                FROM admin_checklists 
                WHERE crew_id IN ($idList)
            ");
            if ($clResult) {
                while ($row = $clResult->fetch_assoc()) {
                    $checklistProgress[(int)$row['crew_id']] = (int)$row['progress'];
                }
            }
        }

        // Check which candidates already have contracts in ERP
        $crewIdsWithContracts = [];
        if (!empty($syncedCrewIds)) {
            $idList = implode(',', array_map('intval', $syncedCrewIds));
            $contractCheckResult = $this->db->query("
                SELECT DISTINCT crew_id FROM contracts 
                WHERE crew_id IN ($idList) 
                AND status IN ('active', 'onboard', 'pending_approval', 'draft')
            ");
            if ($contractCheckResult) {
                while ($row = $contractCheckResult->fetch_assoc()) {
                    $crewIdsWithContracts[] = (int)$row['crew_id'];
                }
            }
        }

        // Enrich each candidate with ERP data from our own tables
        foreach ($candidates as &$c) {
            $appId = (int)($c['id'] ?? 0);
            $crewId = $syncedAppIds[$appId] ?? null;
            
            // Mark as already processed if found in recruitment_sync
            $c['is_synced_to_erp'] = ($crewId !== null);
            $c['erp_crew_id'] = $crewId;
            $c['sent_to_erp_at'] = $c['is_synced_to_erp'] ? 'yes' : null;
            $c['checklist_progress'] = $crewId ? ($checklistProgress[$crewId] ?? 0) : 0;
            $c['has_contract'] = in_array($crewId, $crewIdsWithContracts);
            
            // Override status_name for synced candidates that still show as Pending
            // (recruitment DB update may have failed, but ERP sync is the source of truth)
            if ($c['is_synced_to_erp'] && in_array($c['status_name'] ?? '', ['Pending', 'Applied', 'New'])) {
                $c['status_name'] = 'Admin Review';
            }
        }
        unset($c);

        $data = [
            'title' => 'Recruitment Pipeline',
            'currentPage' => 'recruitment-pipeline',
            'stats' => $stats,
            'candidates' => $candidates,
            'flash' => $this->getFlash()
        ];

        return $this->view('recruitment/pipeline_modern', $data);
    }

    /**
     * Approval center - show pending approvals from both crews table AND recruitment DB
     */
    public function approval()
    {
        $this->requireAuth();

        $pendingApprovals = [];
        $stats = [
            'pending_count' => 0,
            'total_processed' => 0,
            'approved_count' => 0,
            'rejected_count' => 0
        ];

        // 1. Count pending approvals from crews table (status = pending_approval)
        $countResult = $this->db->query("SELECT COUNT(*) as count FROM crews WHERE status = 'pending_approval'");
        if ($countResult) {
            $stats['pending_count'] = $countResult->fetch_assoc()['count'];
        }

        // 2. Count approved in last 30 days
        $approvedResult = $this->db->query("
            SELECT COUNT(*) as count FROM crews 
            WHERE source = 'recruitment' AND status = 'available' 
            AND approved_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        if ($approvedResult) {
            $stats['approved_count'] = $approvedResult->fetch_assoc()['count'];
        }

        // 3. Count rejected in last 30 days
        $rejectedResult = $this->db->query("
            SELECT COUNT(*) as count FROM crews 
            WHERE source = 'recruitment' AND status = 'rejected' 
            AND updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        if ($rejectedResult) {
            $stats['rejected_count'] = $rejectedResult->fetch_assoc()['count'];
        }

        // Get pending approvals from crews table with their documents count
        $query = "
            SELECT 
                c.id,
                c.employee_id,
                c.full_name as applicant_name,
                c.email,
                c.phone,
                c.gender,
                c.birth_date,
                c.birth_place,
                c.nationality,
                c.address,
                c.city,
                c.province,
                c.postal_code,
                c.emergency_name,
                c.emergency_phone,
                c.emergency_relation,
                c.total_sea_time_months,
                c.photo,
                c.notes,
                c.candidate_id,
                c.current_rank_id,
                c.created_at,
                r.name as rank_name,
                (SELECT COUNT(*) FROM crew_documents cd WHERE cd.crew_id = c.id) as doc_count
            FROM crews c
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE c.status = 'pending_approval'
            ORDER BY c.created_at DESC
        ";
        $result = $this->db->query($query);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Check data completeness
                $required = ['full_name', 'email', 'phone', 'gender', 'birth_date', 'nationality'];
                $filled = 0;
                foreach ($required as $field) {
                    if (!empty($row[$field])) $filled++;
                }
                $row['completeness'] = round(($filled / count($required)) * 100);
                $row['is_complete'] = $filled === count($required);
                $row['source_type'] = 'erp'; // Mark as from ERP crews table
                $pendingApprovals[] = $row;
            }
        }

        // 4. ALSO fetch pending candidates from recruitment DB (applications with Pending status)
        // Exclude candidates already synced to ERP
        $syncedAppIds = [];
        $syncResult = $this->db->query("SELECT recruitment_applicant_id FROM recruitment_sync");
        if ($syncResult) {
            while ($syncRow = $syncResult->fetch_assoc()) {
                $syncedAppIds[] = (int)$syncRow['recruitment_applicant_id'];
            }
        }

        if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
            $excludeClause = '';
            if (!empty($syncedAppIds)) {
                $excludeClause = 'AND a.id NOT IN (' . implode(',', $syncedAppIds) . ')';
            }

            $recruitPendingQuery = "
                SELECT 
                    a.id as application_id,
                    u.full_name as applicant_name,
                    u.email,
                    u.phone,
                    ap.gender,
                    ap.date_of_birth as birth_date,
                    ap.nationality,
                    v.title as rank_name,
                    d.name as department_name,
                    s.name as status_name,
                    a.submitted_at as created_at
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                JOIN application_statuses s ON a.status_id = s.id
                WHERE LOWER(s.name) = 'pending'
                {$excludeClause}
                ORDER BY a.submitted_at DESC
            ";
            $recruitResult = $this->recruitmentDb->query($recruitPendingQuery);

            if ($recruitResult) {
                while ($row = $recruitResult->fetch_assoc()) {
                    // Check data completeness
                    $required = ['applicant_name', 'email', 'phone', 'gender', 'birth_date', 'nationality'];
                    $filled = 0;
                    foreach ($required as $field) {
                        if (!empty($row[$field])) $filled++;
                    }
                    $row['completeness'] = round(($filled / count($required)) * 100);
                    $row['is_complete'] = $filled === count($required);
                    $row['source_type'] = 'recruitment'; // Mark as from recruitment DB
                    $row['id'] = null; // No crew ID yet (not in ERP)
                    $row['doc_count'] = 0;
                    $pendingApprovals[] = $row;
                    $stats['pending_count']++; // Add to pending count
                }
            }
        }

        $stats['total_processed'] = $stats['approved_count'] + $stats['rejected_count'];

        // 5. Fetch detail lists for stats modals (approved, rejected, all processed)
        $approvedList = [];
        $rejectedList = [];

        // Approved from ERP crews
        $approvedQuery = $this->db->query("
            SELECT c.full_name, c.employee_id, c.email, r.name as rank_name, c.approved_at, c.source,
                   CONCAT(u.full_name) as approved_by_name
            FROM crews c 
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            LEFT JOIN users u ON c.approved_by = u.id
            WHERE c.source = 'recruitment' AND c.status = 'available' 
            AND c.approved_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY c.approved_at DESC
        ");
        if ($approvedQuery) {
            while ($row = $approvedQuery->fetch_assoc()) {
                $approvedList[] = $row;
            }
        }

        // Rejected from ERP crews
        $rejectedQuery = $this->db->query("
            SELECT c.full_name, c.employee_id, c.email, r.name as rank_name, c.rejected_at, c.rejection_reason, c.source
            FROM crews c 
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE c.source = 'recruitment' AND c.status = 'rejected' 
            AND c.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY c.rejected_at DESC
        ");
        if ($rejectedQuery) {
            while ($row = $rejectedQuery->fetch_assoc()) {
                $rejectedList[] = $row;
            }
        }

        $data = [
            'title' => 'Approval Center',
            'currentPage' => 'recruitment-approval',
            'pendingApprovals' => $pendingApprovals,
            'approvedList' => $approvedList,
            'rejectedList' => $rejectedList,
            'stats' => $stats,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'recruitment/approval_modern' : 'recruitment/approval';

        return $this->view($view, $data);
    }
    /**
     * Approve a candidate - change status from pending_approval to available
     */
    public function approve($crewId = null)
    {
        $this->requireAuth();
        
        if (!$crewId) {
            $this->setFlash('error', 'ID kandidat tidak valid');
            $this->redirect('recruitment/approval');
            return;
        }

        $approvalNotes = $_POST['approval_notes'] ?? '';

        try {
            $this->db->begin_transaction();

            $userId = $_SESSION['user_id'] ?? 1;

            // Update crew status with approval tracking — now goes to pending_checklist
            $stmt = $this->db->prepare("
                UPDATE crews SET 
                    status = 'pending_checklist', 
                    approved_at = NOW(), 
                    approved_by = ?,
                    notes = CASE WHEN ? != '' THEN CONCAT(IFNULL(notes,''), '\n[APPROVED] ', ?) ELSE notes END,
                    updated_at = NOW()
                WHERE id = ? AND status = 'pending_approval'
            ");
            if (!$stmt) {
                throw new \Exception('DB prepare error (crews UPDATE): ' . $this->db->error . '. Cek kolom: approved_at, approved_by');
            }
            $stmt->bind_param('issi', $userId, $approvalNotes, $approvalNotes, $crewId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception('Kandidat tidak ditemukan atau sudah diproses');
            }
            $stmt->close();

            // Auto-create admin checklist entry
            $this->ensureAdminChecklistTable();
            $clStmt = $this->db->prepare("INSERT IGNORE INTO admin_checklists (crew_id) VALUES (?)");
            if ($clStmt) {
                $clStmt->bind_param('i', $crewId);
                $clStmt->execute();
                $clStmt->close();
            } else {
                error_log('Failed to create admin checklist entry: ' . $this->db->error);
            }

            // Get crew name for notification
            $nameStmt = $this->db->prepare("SELECT full_name, current_rank_id FROM crews WHERE id = ?");
            if (!$nameStmt) {
                throw new \Exception('DB prepare error (crew name query): ' . $this->db->error);
            }
            $nameStmt->bind_param('i', $crewId);
            $nameStmt->execute();
            $crew = $nameStmt->get_result()->fetch_assoc();
            $nameStmt->close();

            // Get rank name
            $rankName = 'Unknown';
            if ($crew['current_rank_id']) {
                $rankStmt = $this->db->prepare("SELECT name FROM ranks WHERE id = ?");
                $rankStmt->bind_param('i', $crew['current_rank_id']);
                $rankStmt->execute();
                $rankRow = $rankStmt->get_result()->fetch_assoc();
                if ($rankRow) $rankName = $rankRow['name'];
                $rankStmt->close();
            }

            // Create notification — redirect to Admin Checklist
            $this->createNotification(
                'candidate_approved',
                "Kandidat Disetujui: {$crew['full_name']}",
                "Pengajuan untuk {$crew['full_name']} - {$rankName} sudah di-approve. Lanjut ke Admin Checklist.",
                "AdminChecklist/detail/{$crewId}"
            );

            // Log activity
            $this->logActivity('approve_candidate', "Approved candidate: {$crew['full_name']} (Crew ID: {$crewId})");

            $this->db->commit();

            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => "✅ Kandidat {$crew['full_name']} berhasil disetujui! Lanjut ke Admin Checklist.",
                    'redirect_url' => BASE_URL . "AdminChecklist/detail/{$crewId}"
                ]);
                return;
            }

            $this->setFlash('success', "✅ Kandidat {$crew['full_name']} berhasil disetujui! Lanjut ke Admin Checklist.");
            $this->redirect("AdminChecklist/detail/{$crewId}");
        } catch (\Exception $e) {
            $this->db->rollback();
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal approve: ' . $e->getMessage()]);
                return;
            }
            $this->setFlash('error', 'Gagal approve: ' . $e->getMessage());
            $this->redirect('recruitment/approval');
        }
    }

    /**
     * Reject a candidate
     */
    public function reject($crewId = null)
    {
        $this->requireAuth();
        
        if (!$crewId) {
            $this->setFlash('error', 'ID kandidat tidak valid');
            $this->redirect('recruitment/approval');
            return;
        }

        $reason = $_POST['rejection_reason'] ?? '';

        try {
            $this->db->begin_transaction();

            $userId = $_SESSION['user_id'] ?? 1;

            // Update crew status with rejection tracking
            $stmt = $this->db->prepare("
                UPDATE crews SET 
                    status = 'rejected', 
                    rejected_at = NOW(),
                    rejected_by = ?,
                    rejection_reason = ?,
                    notes = CONCAT(IFNULL(notes,''), '\n[REJECTED] ', ?),
                    updated_at = NOW()
                WHERE id = ? AND status IN ('pending_approval', 'pending_checklist')
            ");
            if (!$stmt) {
                // Fallback: try simpler UPDATE without optional columns
                $stmt = $this->db->prepare("
                    UPDATE crews SET status = 'rejected', updated_at = NOW()
                    WHERE id = ? AND status IN ('pending_approval', 'pending_checklist')
                ");
                if (!$stmt) {
                    throw new \Exception('DB prepare error (crews reject): ' . $this->db->error);
                }
                $stmt->bind_param('i', $crewId);
            } else {
                $stmt->bind_param('issi', $userId, $reason, $reason, $crewId);
            }
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception('Kandidat tidak ditemukan atau sudah diproses');
            }
            $stmt->close();

            // Get crew info
            $nameStmt = $this->db->prepare("SELECT full_name, candidate_id FROM crews WHERE id = ?");
            if (!$nameStmt) {
                throw new \Exception('DB prepare error (crew name): ' . $this->db->error);
            }
            $nameStmt->bind_param('i', $crewId);
            $nameStmt->execute();
            $crew = $nameStmt->get_result()->fetch_assoc();
            $nameStmt->close();

            // Optionally update recruitment DB status
            if ($this->recruitmentDb && $crew['candidate_id']) {
                $updateStmt = $this->recruitmentDb->prepare("
                    UPDATE applications SET status_id = 7, updated_at = NOW() 
                    WHERE user_id = ? AND status_id IN (5, 6)
                ");
                if ($updateStmt) {
                    $updateStmt->bind_param('i', $crew['candidate_id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }

            // Create notification
            $this->createNotification(
                'candidate_rejected',
                "Kandidat Ditolak: {$crew['full_name']}",
                "Pengajuan untuk {$crew['full_name']} telah ditolak." . ($reason ? " Alasan: {$reason}" : ''),
                null
            );

            // Log activity
            $this->logActivity('reject_candidate', "Rejected candidate: {$crew['full_name']} (Crew ID: {$crewId}). Reason: {$reason}");

            $this->db->commit();

            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => "Kandidat {$crew['full_name']} telah ditolak."]);
                return;
            }

            $this->setFlash('success', "Kandidat {$crew['full_name']} telah ditolak.");
            $this->redirect('recruitment/approval');
        } catch (\Exception $e) {
            $this->db->rollback();
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal reject: ' . $e->getMessage()]);
                return;
            }
            $this->setFlash('error', 'Gagal reject: ' . $e->getMessage());
            $this->redirect('recruitment/approval');
        }
    }

    /**
     * Reject a recruitment DB candidate directly (from pipeline popup)
     */
    public function rejectRecruitment($applicationId = null)
    {
        $this->requireAuth();
        
        // Buffer output to prevent PHP warnings from corrupting JSON response
        ob_start();
        
        header('Content-Type: application/json');

        if (!$applicationId) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'ID aplikasi tidak valid']);
            return;
        }

        if (!$this->recruitmentDb || $this->recruitmentDb->connect_error) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Koneksi ke database recruitment gagal']);
            return;
        }

        $reason = $_POST['rejection_reason'] ?? 'Ditolak via Pipeline';

        try {
            // Get name
            $nameStmt = $this->recruitmentDb->prepare("
                SELECT u.full_name FROM applications a JOIN users u ON a.user_id = u.id WHERE a.id = ?
            ");
            if (!$nameStmt) {
                throw new \Exception('DB prepare error: ' . $this->recruitmentDb->error);
            }
            $nameStmt->bind_param('i', $applicationId);
            $nameStmt->execute();
            $candidate = $nameStmt->get_result()->fetch_assoc();
            $nameStmt->close();

            if (!$candidate) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Kandidat tidak ditemukan']);
                return;
            }

            // Update to Rejected (status_id = 7)
            $stmt = $this->recruitmentDb->prepare("
                UPDATE applications SET status_id = 7, status_updated_at = NOW(), updated_at = NOW() WHERE id = ?
            ");
            if (!$stmt) {
                // Fallback without status_updated_at
                $stmt = $this->recruitmentDb->prepare("UPDATE applications SET status_id = 7, updated_at = NOW() WHERE id = ?");
            }
            if ($stmt) {
                $stmt->bind_param('i', $applicationId);
                $stmt->execute();
                $stmt->close();
            }

            $this->logActivity('reject_recruitment', "Rejected recruitment candidate: {$candidate['full_name']} (App ID: {$applicationId}). Reason: {$reason}");

            $buffered = ob_get_clean();
            if ($buffered) error_log("RejectRecruitment buffered output: " . $buffered);
            echo json_encode(['success' => true, 'message' => "Kandidat {$candidate['full_name']} telah ditolak."]);
        } catch (\Throwable $e) {
            $buffered = ob_get_clean();
            if ($buffered) error_log("RejectRecruitment buffered output on error: " . $buffered);
            error_log("RejectRecruitment error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve a recruitment DB candidate - auto-import to ERP + redirect to contract creation
     * This combines onboarding (import) + approval in one step
     */
    public function approveRecruitment($applicationId = null)
    {
        $this->requireAuth();

        // Buffer output to prevent PHP warnings from corrupting JSON response
        ob_start();

        if (!$applicationId) {
            ob_end_clean();
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID aplikasi tidak valid']);
                return;
            }
            $this->setFlash('error', 'ID aplikasi tidak valid');
            $this->redirect('recruitment/approval');
            return;
        }

        if (!$this->recruitmentDb || $this->recruitmentDb->connect_error) {
            ob_end_clean();
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Koneksi ke database recruitment gagal']);
                return;
            }
            $this->setFlash('error', 'Koneksi ke database recruitment gagal');
            $this->redirect('recruitment/approval');
            return;
        }

        $approvalNotes = $_POST['approval_notes'] ?? '';

        try {
            // Ensure recruitment_sync table exists
            $result = $this->db->query("SHOW TABLES LIKE 'recruitment_sync'");
            if ($result->num_rows == 0) {
                $this->createRecruitmentSyncTable();
            }

            // Check if already synced
            $checkStmt = $this->db->prepare("SELECT crew_id FROM recruitment_sync WHERE recruitment_applicant_id = ?");
            if (!$checkStmt) {
                throw new \Exception('DB prepare error (recruitment_sync check): ' . $this->db->error);
            }
            $checkStmt->bind_param('i', $applicationId);
            $checkStmt->execute();
            $existing = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();

            if ($existing) {
                // Already imported - update status to admin_review and redirect to admin checklist
                $crewId = $existing['crew_id'];

                // Ensure status_id=9 ('Admin Review') exists in recruitment DB
                if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
                    $statusCheck = $this->recruitmentDb->query("SELECT id FROM application_statuses WHERE id = 9");
                    if (!$statusCheck || $statusCheck->num_rows === 0) {
                        $this->recruitmentDb->query("INSERT IGNORE INTO application_statuses (id, name, color, sort_order) VALUES (9, 'Admin Review', '#3b82f6', 9)");
                    }
                }

                // Update recruitment DB: set status to admin_review (9) with progressive fallback
                if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
                    $updateSuccess = false;
                    
                    // Try 1: Full update with all optional columns
                    $updateStmt = $this->recruitmentDb->prepare(
                        "UPDATE applications SET erp_crew_id = ?, status_id = 9, sent_to_erp_at = IFNULL(sent_to_erp_at, NOW()), status_updated_at = NOW(), updated_at = NOW() WHERE id = ?"
                    );
                    if ($updateStmt) {
                        $updateStmt->bind_param('ii', $crewId, $applicationId);
                        $updateSuccess = $updateStmt->execute();
                        error_log("[APPROVE_RECRUITMENT] Synced branch - full update: affected=" . $updateStmt->affected_rows);
                        $updateStmt->close();
                    }
                    
                    // Try 2: Without status_updated_at
                    if (!$updateSuccess) {
                        error_log("[APPROVE_RECRUITMENT] Fallback 1: without status_updated_at");
                        $updateStmt = $this->recruitmentDb->prepare(
                            "UPDATE applications SET erp_crew_id = ?, status_id = 9, sent_to_erp_at = NOW(), updated_at = NOW() WHERE id = ?"
                        );
                        if ($updateStmt) {
                            $updateStmt->bind_param('ii', $crewId, $applicationId);
                            $updateSuccess = $updateStmt->execute();
                            $updateStmt->close();
                        }
                    }
                    
                    // Try 3: Without sent_to_erp_at and status_updated_at
                    if (!$updateSuccess) {
                        error_log("[APPROVE_RECRUITMENT] Fallback 2: without sent_to_erp_at");
                        $updateStmt = $this->recruitmentDb->prepare(
                            "UPDATE applications SET erp_crew_id = ?, status_id = 9, updated_at = NOW() WHERE id = ?"
                        );
                        if ($updateStmt) {
                            $updateStmt->bind_param('ii', $crewId, $applicationId);
                            $updateSuccess = $updateStmt->execute();
                            $updateStmt->close();
                        }
                    }
                    
                    // Try 4: Minimal — just status_id (most basic, should always work)
                    if (!$updateSuccess) {
                        error_log("[APPROVE_RECRUITMENT] Fallback 3: status_id only");
                        $updateStmt = $this->recruitmentDb->prepare(
                            "UPDATE applications SET status_id = 9 WHERE id = ?"
                        );
                        if ($updateStmt) {
                            $updateStmt->bind_param('i', $applicationId);
                            $updateSuccess = $updateStmt->execute();
                            $updateStmt->close();
                        }
                    }
                    
                    if (!$updateSuccess) {
                        error_log("[APPROVE_RECRUITMENT] CRITICAL: All recruitment DB update attempts failed for app_id=$applicationId. Error: " . $this->recruitmentDb->error);
                    }
                }

                // Ensure admin checklist entry exists
                $acCheck = $this->db->prepare("SELECT id FROM admin_checklists WHERE crew_id = ?");
                $acCheck->bind_param('i', $crewId);
                $acCheck->execute();
                $acRow = $acCheck->get_result()->fetch_assoc();
                $acCheck->close();

                if (!$acRow) {
                    $acInsert = $this->db->prepare("INSERT IGNORE INTO admin_checklists (crew_id) VALUES (?)");
                    $acInsert->bind_param('i', $crewId);
                    $acInsert->execute();
                    $acInsert->close();
                }

                // Make sure crew status is pending_checklist
                $crewUpd = $this->db->prepare("UPDATE crews SET status = 'pending_checklist', updated_at = NOW() WHERE id = ? AND status NOT IN ('ready_operational','on_board')");
                $crewUpd->bind_param('i', $crewId);
                $crewUpd->execute();
                $crewUpd->close();

                // === SYNC DOCUMENTS (for already-imported candidates that may be missing documents) ===
                try {
                    // Get user_id from application
                    $userStmt = $this->recruitmentDb->prepare("SELECT a.user_id FROM applications a WHERE a.id = ?");
                    if ($userStmt) {
                        $userStmt->bind_param('i', $applicationId);
                        $userStmt->execute();
                        $userRow = $userStmt->get_result()->fetch_assoc();
                        $userStmt->close();

                        if ($userRow) {
                            // Get existing ERP documents count for this crew
                            $erpDocCheck = $this->db->prepare("SELECT COUNT(*) as cnt FROM crew_documents WHERE crew_id = ?");
                            $erpDocCheck->bind_param('i', $crewId);
                            $erpDocCheck->execute();
                            $erpDocCount = $erpDocCheck->get_result()->fetch_assoc()['cnt'];
                            $erpDocCheck->close();

                            // Only sync if ERP has no documents yet
                            if ($erpDocCount == 0) {
                                $docStmt = $this->recruitmentDb->prepare("
                                    SELECT d.*, dt.name as type_name 
                                    FROM documents d
                                    LEFT JOIN document_types dt ON d.document_type_id = dt.id
                                    WHERE d.user_id = ?
                                ");
                                if ($docStmt) {
                                    $docStmt->bind_param('i', $userRow['user_id']);
                                    $docStmt->execute();
                                    $docResult = $docStmt->get_result();
                                    $syncedCount = 0;
                                    
                                    $typeMap = [
                                        'CV / Resume' => 'OTHER', 'Passport' => 'PASSPORT',
                                        'Seaman Book' => 'SEAMAN_BOOK', 'COC Certificate' => 'COC',
                                        'COP / STCW Certificates' => 'BST', 'Medical Certificate' => 'MEDICAL',
                                        'Photo' => 'OTHER', 'Other Certificates' => 'OTHER',
                                    ];

                                    $erpBaseDir = defined('FCPATH') ? FCPATH : dirname(dirname(__DIR__)) . '/';
                                    $erpDocDir = $erpBaseDir . 'uploads/crew_documents/' . $crewId . '/';
                                    if (!is_dir($erpDocDir)) mkdir($erpDocDir, 0777, true);

                                    while ($doc = $docResult->fetch_assoc()) {
                                        $docTypeCode = $typeMap[$doc['type_name'] ?? ''] ?? 'OTHER';
                                        $docName = $doc['type_name'] ?? ($doc['original_name'] ?? 'Document');
                                        $erpFilePath = null; $erpFileName = null;
                                        $fileSize = $doc['file_size'] ?? null;

                                        if (!empty($doc['file_path'])) {
                                            $recruitBase = str_replace('/erp/', '/recruitment/', $erpBaseDir);
                                            $sourcePaths = [
                                                $recruitBase . 'public/' . ltrim($doc['file_path'], '/'),
                                                $recruitBase . ltrim($doc['file_path'], '/'),
                                                dirname(dirname($erpBaseDir)) . '/recruitment/public/' . ltrim($doc['file_path'], '/'),
                                            ];
                                            foreach ($sourcePaths as $sp) {
                                                if (file_exists($sp)) {
                                                    $ext = pathinfo($sp, PATHINFO_EXTENSION);
                                                    $newFn = 'rec_' . time() . '_' . $doc['id'] . '.' . $ext;
                                                    if (copy($sp, $erpDocDir . $newFn)) {
                                                        $erpFilePath = 'uploads/crew_documents/' . $crewId . '/' . $newFn;
                                                        $erpFileName = $doc['original_name'] ?? $newFn;
                                                        $fileSize = filesize($erpDocDir . $newFn);
                                                    }
                                                    break;
                                                }
                                            }
                                        }

                                        $status = 'pending';
                                        if (!empty($doc['expiry_date'])) {
                                            $dl = (strtotime($doc['expiry_date']) - time()) / 86400;
                                            if ($dl < 0) $status = 'expired';
                                            elseif ($dl < 90) $status = 'expiring_soon';
                                            else $status = 'valid';
                                        }

                                        $ins = $this->db->prepare("INSERT INTO crew_documents (crew_id, document_type, document_name, document_number, file_path, file_name, file_size, mime_type, issue_date, expiry_date, issuing_authority, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                                        if ($ins) {
                                            $mt = $doc['file_type'] ?? 'application/octet-stream';
                                            $dn = $doc['document_number'] ?? '';
                                            $id2 = $doc['issue_date'] ?? null;
                                            $ed = $doc['expiry_date'] ?? null;
                                            $ib = $doc['issued_by'] ?? null;
                                            $ins->bind_param('isssssississs', $crewId, $docTypeCode, $docName, $dn, $erpFilePath, $erpFileName, $fileSize, $mt, $id2, $ed, $ib, $status);
                                            if ($ins->execute()) $syncedCount++;
                                            $ins->close();
                                        }
                                    }
                                    $docStmt->close();
                                    if ($syncedCount > 0) {
                                        error_log("[APPROVE_RECRUITMENT] Re-sync: $syncedCount documents synced for existing crew $crewId");
                                    }
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    error_log("[APPROVE_RECRUITMENT] Document re-sync error: " . $e->getMessage());
                }

                if ($this->isAjax()) {
                    // Log any buffered warnings then clean
                    $buffered = ob_get_clean();
                    if ($buffered) error_log("ApproveRecruitment buffered output: " . $buffered);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => '✅ Kandidat sudah di-approve! Mengarahkan ke Admin Checklist...',
                        'redirect_url' => BASE_URL . "AdminChecklist/detail/{$crewId}"
                    ]);
                    return;
                }
                ob_end_clean();
                $this->setFlash('success', 'Kandidat sudah di-approve! Silakan proses Admin Checklist.');
                $this->redirect("AdminChecklist/detail/{$crewId}");
                return;
            }

            // Get complete application data from recruitment DB
            $stmt = $this->recruitmentDb->prepare("
                SELECT 
                    a.*, 
                    u.full_name, 
                    u.email, 
                    u.phone, 
                    u.id as user_id,
                    ap.date_of_birth,
                    ap.address,
                    ap.city,
                    ap.nationality as country,
                    ap.passport_no,
                    ap.gender,
                    ap.nationality,
                    ap.emergency_name as emergency_contact_name,
                    ap.emergency_phone as emergency_contact_phone,
                    v.title as position_applied
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                WHERE a.id = ?
            ");
            if (!$stmt) {
                throw new \Exception('DB prepare error (recruitment app query): ' . $this->recruitmentDb->error);
            }
            $stmt->bind_param('i', $applicationId);
            $stmt->execute();
            $application = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$application) {
                throw new \Exception('Data aplikasi tidak ditemukan di database recruitment');
            }

            $this->db->begin_transaction();

            // Generate employee_id (format: CRW-YYYY-XXXX)
            $year = date('Y');
            $countStmt = $this->db->prepare("SELECT COUNT(*) as count FROM crews WHERE employee_id LIKE ?");
            if (!$countStmt) {
                throw new \Exception('DB prepare error (employee_id count): ' . $this->db->error);
            }
            $pattern = "CRW-{$year}-%";
            $countStmt->bind_param('s', $pattern);
            $countStmt->execute();
            $count = $countStmt->get_result()->fetch_assoc()['count'];
            $countStmt->close();
            $employeeId = sprintf("CRW-%s-%04d", $year, $count + 1);

            // Insert crew with complete data mapping - status 'pending_checklist' (goes to Admin Checklist)
            $stmt = $this->db->prepare("
                INSERT INTO crews (
                    employee_id, full_name, email, phone, 
                    birth_date, gender, nationality,
                    address, city,
                    emergency_name, emergency_phone,
                    current_rank_id, status, 
                    source, candidate_id, approved_at, approved_by,
                    notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_checklist', 'recruitment', ?, NOW(), ?, ?, NOW())
            ");
            if (!$stmt) {
                throw new \Exception('DB prepare error (crews INSERT): ' . $this->db->error . '. Pastikan tabel crews memiliki kolom: source, candidate_id, approved_at, approved_by');
            }

            $rankId = 1; // Default rank
            $userId = $_SESSION['user_id'] ?? 1;
            $notes = $approvalNotes ? "[APPROVED] {$approvalNotes}" : "[APPROVED] Auto-imported from recruitment DB";

            // Sanitize gender for ENUM('male','female')
            $gender = strtolower(trim($application['gender'] ?? ''));
            if (!in_array($gender, ['male', 'female'])) {
                $gender = null;
            }

            $stmt->bind_param(
                'sssssssssssiiis',
                $employeeId,
                $application['full_name'],
                $application['email'],
                $application['phone'],
                $application['date_of_birth'],
                $gender,
                $application['nationality'],
                $application['address'],
                $application['city'],
                $application['emergency_contact_name'],
                $application['emergency_contact_phone'],
                $rankId,
                $applicationId,
                $userId,
                $notes
            );
            $stmt->execute();
            $crewId = $this->db->insert_id;
            $stmt->close();

            // Log sync
            $syncStmt = $this->db->prepare("
                INSERT INTO recruitment_sync 
                (recruitment_applicant_id, crew_id, sync_status, synced_at, created_at)
                VALUES (?, ?, 'onboarded', NOW(), NOW())
            ");
            if (!$syncStmt) {
                throw new \Exception('DB prepare error (recruitment_sync INSERT): ' . $this->db->error);
            }
            $syncStmt->bind_param('ii', $applicationId, $crewId);
            $syncStmt->execute();
            $syncStmt->close();

            // Update recruitment DB - mark as synced, set erp_crew_id, and status to Admin Review
            try {
                // Ensure status_id=9 ('Admin Review') exists in recruitment DB
                $statusCheck = $this->recruitmentDb->query("SELECT id FROM application_statuses WHERE id = 9");
                if (!$statusCheck || $statusCheck->num_rows === 0) {
                    $this->recruitmentDb->query("INSERT IGNORE INTO application_statuses (id, name, color, sort_order) VALUES (9, 'Admin Review', '#3b82f6', 9)");
                }

                // Try to mark user as synced (optional — column may not exist)
                $updateStmt = $this->recruitmentDb->prepare("UPDATE users SET is_synced_to_erp = 1 WHERE id = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param('i', $application['user_id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }

                // Progressive fallback for application status update
                $updateSuccess = false;
                
                // Try 1: Full update with all columns
                $syncStatusStmt = $this->recruitmentDb->prepare("
                    UPDATE applications SET status_id = 9, erp_crew_id = ?, sent_to_erp_at = NOW(), status_updated_at = NOW(), updated_at = NOW() WHERE id = ?
                ");
                if ($syncStatusStmt) {
                    $syncStatusStmt->bind_param('ii', $crewId, $applicationId);
                    $updateSuccess = $syncStatusStmt->execute();
                    error_log("[APPROVE_RECRUITMENT] New branch - full update: affected=" . $syncStatusStmt->affected_rows);
                    $syncStatusStmt->close();
                }
                
                // Try 2: Without status_updated_at
                if (!$updateSuccess) {
                    error_log("[APPROVE_RECRUITMENT] New branch fallback 1");
                    $syncStatusStmt = $this->recruitmentDb->prepare("
                        UPDATE applications SET status_id = 9, erp_crew_id = ?, sent_to_erp_at = NOW(), updated_at = NOW() WHERE id = ?
                    ");
                    if ($syncStatusStmt) {
                        $syncStatusStmt->bind_param('ii', $crewId, $applicationId);
                        $updateSuccess = $syncStatusStmt->execute();
                        $syncStatusStmt->close();
                    }
                }
                
                // Try 3: Without erp_crew_id and sent_to_erp_at
                if (!$updateSuccess) {
                    error_log("[APPROVE_RECRUITMENT] New branch fallback 2");
                    $syncStatusStmt = $this->recruitmentDb->prepare("
                        UPDATE applications SET status_id = 9, updated_at = NOW() WHERE id = ?
                    ");
                    if ($syncStatusStmt) {
                        $syncStatusStmt->bind_param('i', $applicationId);
                        $updateSuccess = $syncStatusStmt->execute();
                        $syncStatusStmt->close();
                    }
                }
                
                // Try 4: Minimal — just status_id
                if (!$updateSuccess) {
                    error_log("[APPROVE_RECRUITMENT] New branch fallback 3: status_id only");
                    $syncStatusStmt = $this->recruitmentDb->prepare("UPDATE applications SET status_id = 9 WHERE id = ?");
                    if ($syncStatusStmt) {
                        $syncStatusStmt->bind_param('i', $applicationId);
                        $updateSuccess = $syncStatusStmt->execute();
                        $syncStatusStmt->close();
                    }
                }
                
                if (!$updateSuccess) {
                    error_log("[APPROVE_RECRUITMENT] CRITICAL: All new-branch update attempts failed. Error: " . $this->recruitmentDb->error);
                }
            } catch (\Exception $e) {
                error_log("Recruitment DB update warning: " . $e->getMessage());
            }

            // Auto-create admin checklist entry
            $this->ensureAdminChecklistTable();
            $clStmt = $this->db->prepare("INSERT IGNORE INTO admin_checklists (crew_id, application_id) VALUES (?, ?)");
            if ($clStmt) {
                $clStmt->bind_param('ii', $crewId, $applicationId);
                $clStmt->execute();
                $clStmt->close();
            } else {
                // Try without application_id (column may not exist)
                $clStmt2 = $this->db->prepare("INSERT IGNORE INTO admin_checklists (crew_id) VALUES (?)");
                if ($clStmt2) {
                    $clStmt2->bind_param('i', $crewId);
                    $clStmt2->execute();
                    $clStmt2->close();
                } else {
                    error_log('Failed to create admin checklist: ' . $this->db->error);
                }
            }

            // Create notification — redirect to Admin Checklist
            $this->createNotification(
                'candidate_approved',
                "Kandidat Disetujui: {$application['full_name']}",
                "Kandidat {$application['full_name']} dari recruitment telah di-import ke ERP (ID: {$employeeId}). Lanjut ke Admin Checklist.",
                "AdminChecklist/detail/{$crewId}"
            );

            // === SYNC DOCUMENTS FROM RECRUITMENT TO ERP ===
            try {
                // Fetch documents from recruitment DB
                $docStmt = $this->recruitmentDb->prepare("
                    SELECT d.*, dt.name as type_name 
                    FROM documents d
                    LEFT JOIN document_types dt ON d.document_type_id = dt.id
                    WHERE d.user_id = ?
                ");
                if ($docStmt) {
                    $docStmt->bind_param('i', $application['user_id']);
                    $docStmt->execute();
                    $docResult = $docStmt->get_result();
                    $documents = [];
                    while ($doc = $docResult->fetch_assoc()) {
                        $documents[] = $doc;
                    }
                    $docStmt->close();

                    if (!empty($documents)) {
                        $syncedCount = 0;
                        
                        // Map recruitment document types to ERP document type codes
                        $typeMap = [
                            'CV / Resume' => 'OTHER',
                            'Passport' => 'PASSPORT',
                            'Seaman Book' => 'SEAMAN_BOOK',
                            'COC Certificate' => 'COC',
                            'COP / STCW Certificates' => 'BST',
                            'Medical Certificate' => 'MEDICAL',
                            'Photo' => 'OTHER',
                            'Other Certificates' => 'OTHER',
                        ];

                        // ERP upload directory
                        $erpBaseDir = defined('FCPATH') ? FCPATH : dirname(dirname(__DIR__)) . '/';
                        $erpDocDir = $erpBaseDir . 'uploads/crew_documents/' . $crewId . '/';
                        if (!is_dir($erpDocDir)) {
                            mkdir($erpDocDir, 0777, true);
                        }

                        foreach ($documents as $doc) {
                            $docTypeCode = $typeMap[$doc['type_name'] ?? ''] ?? 'OTHER';
                            $docName = $doc['type_name'] ?? ($doc['original_name'] ?? 'Document');
                            $docNumber = $doc['document_number'] ?? '';

                            // Try to copy the actual file from recruitment to ERP
                            $erpFilePath = null;
                            $erpFileName = null;
                            $fileSize = $doc['file_size'] ?? null;

                            if (!empty($doc['file_path'])) {
                                // Build absolute source path from recruitment public dir
                                $recruitBase = str_replace('/erp/', '/recruitment/', $erpBaseDir);
                                // Also try: the path could be like uploads/documents/26/file.pdf
                                $sourcePaths = [
                                    $recruitBase . 'public/' . ltrim($doc['file_path'], '/'),
                                    $recruitBase . ltrim($doc['file_path'], '/'),
                                    dirname(dirname($erpBaseDir)) . '/recruitment/public/' . ltrim($doc['file_path'], '/'),
                                ];
                                
                                $sourceFile = null;
                                foreach ($sourcePaths as $sp) {
                                    if (file_exists($sp)) {
                                        $sourceFile = $sp;
                                        break;
                                    }
                                }

                                if ($sourceFile) {
                                    $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);
                                    $newFileName = 'rec_' . time() . '_' . $doc['id'] . '.' . $ext;
                                    $destPath = $erpDocDir . $newFileName;
                                    
                                    if (copy($sourceFile, $destPath)) {
                                        $erpFilePath = 'uploads/crew_documents/' . $crewId . '/' . $newFileName;
                                        $erpFileName = $doc['original_name'] ?? $newFileName;
                                        $fileSize = filesize($destPath);
                                    }
                                }
                            }

                            // Determine status
                            $status = 'pending';
                            if (!empty($doc['expiry_date'])) {
                                $daysLeft = (strtotime($doc['expiry_date']) - time()) / 86400;
                                if ($daysLeft < 0) $status = 'expired';
                                elseif ($daysLeft < 90) $status = 'expiring_soon';
                                else $status = 'valid';
                            }

                            // Insert into ERP crew_documents
                            $insertDoc = $this->db->prepare("
                                INSERT INTO crew_documents (
                                    crew_id, document_type, document_name, document_number,
                                    file_path, file_name, file_size, mime_type,
                                    issue_date, expiry_date, issuing_authority,
                                    status, created_at
                                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                            ");
                            if ($insertDoc) {
                                $mimeType = $doc['file_type'] ?? 'application/octet-stream';
                                $issueDate = $doc['issue_date'] ?? null;
                                $expiryDate = $doc['expiry_date'] ?? null;
                                $issuedBy = $doc['issued_by'] ?? null;

                                $insertDoc->bind_param(
                                    'isssssississs',
                                    $crewId, $docTypeCode, $docName, $docNumber,
                                    $erpFilePath, $erpFileName, $fileSize, $mimeType,
                                    $issueDate, $expiryDate, $issuedBy,
                                    $status
                                );
                                if ($insertDoc->execute()) {
                                    $syncedCount++;
                                }
                                $insertDoc->close();
                            }
                        }
                        
                        error_log("[APPROVE_RECRUITMENT] Synced $syncedCount/" . count($documents) . " documents from recruitment to ERP for crew $crewId");
                    }
                }
            } catch (\Exception $e) {
                error_log("[APPROVE_RECRUITMENT] Document sync error: " . $e->getMessage());
                // Don't fail the whole approval for document sync issues
            }

            // Auto-award recruiter performance points
            try {
                require_once APPPATH . 'Controllers/RecruiterPerformance.php';
                // Find the assigned recruiter from recruitment DB
                $assignStmt = $this->recruitmentDb->prepare("
                    SELECT aa.assigned_to FROM application_assignments aa
                    WHERE aa.application_id = ? AND aa.status = 'active'
                    ORDER BY aa.assigned_at DESC LIMIT 1
                ");
                if ($assignStmt) {
                    $assignStmt->bind_param('i', $applicationId);
                    $assignStmt->execute();
                    $assignRow = $assignStmt->get_result()->fetch_assoc();
                    if ($assignRow && $assignRow['assigned_to']) {
                        \App\Controllers\RecruiterPerformance::awardPoints(
                            $this->db, $this->recruitmentDb,
                            $assignRow['assigned_to'], 'approved',
                            $applicationId, $application['full_name'],
                            'Approved via ERP Approval Center'
                        );
                    }
                }
            } catch (\Exception $e) {
                error_log("RecruiterPerformance award error: " . $e->getMessage());
            }

            // Log activity
            $this->logActivity('approve_recruitment_candidate', "Approved & imported recruitment candidate: {$application['full_name']} (App ID: {$applicationId}, Crew ID: {$crewId})");

            $this->db->commit();

            if ($this->isAjax()) {
                // Log any buffered warnings then clean
                $buffered = ob_get_clean();
                if ($buffered) error_log("ApproveRecruitment buffered output: " . $buffered);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => "✅ Kandidat {$application['full_name']} berhasil di-import! Lanjut ke Admin Checklist.",
                    'redirect_url' => BASE_URL . "AdminChecklist/detail/{$crewId}"
                ]);
                return;
            }

            ob_end_clean();
            $this->setFlash('success', "✅ Kandidat {$application['full_name']} berhasil di-import (ID: {$employeeId})! Lanjut ke Admin Checklist.");
            $this->redirect("AdminChecklist/detail/{$crewId}");
        } catch (\Throwable $e) {
            if ($this->db->connect_error === null) {
                $this->db->rollback();
            }
            // Log any buffered output + error
            $buffered = ob_get_clean();
            if ($buffered) error_log("ApproveRecruitment buffered output on error: " . $buffered);
            error_log("ApproveRecruitment error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal approve: ' . $e->getMessage()]);
                return;
            }
            $this->setFlash('error', 'Gagal approve & import: ' . $e->getMessage());
            $this->redirect('recruitment/approval');
        }
    }

    /**
     * Show candidate detail page (from recruitment DB)
     */
    public function candidate($applicationId = null)
    {
        $this->requireAuth();

        if (!$applicationId || !$this->recruitmentDb || $this->recruitmentDb->connect_error) {
            $this->setFlash('error', 'ID kandidat tidak valid atau koneksi recruitment DB gagal');
            $this->redirect('recruitment/pipeline');
            return;
        }

        // Get candidate data from recruitment DB
        $stmt = $this->recruitmentDb->prepare("
            SELECT 
                a.id,
                a.user_id,
                a.vacancy_id,
                a.status_id,
                a.submitted_at,
                a.interview_score,
                a.overall_score,
                a.sent_to_erp_at,
                u.full_name,
                u.email,
                u.phone,
                ap.date_of_birth,
                ap.address,
                ap.city,
                ap.nationality as country,
                ap.passport_no,
                ap.gender,
                ap.nationality,
                u.avatar,
                ap.emergency_name,
                ap.emergency_phone,
                v.title as vacancy_title,
                d.name as department_name,
                s.name as status_name,
                s.color as status_color
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            LEFT JOIN departments d ON v.department_id = d.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $candidate = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$candidate) {
            $this->setFlash('error', 'Kandidat tidak ditemukan');
            $this->redirect('recruitment/pipeline');
            return;
        }

        // Get documents
        $documents = [];
        try {
            $docStmt = $this->recruitmentDb->prepare("
                SELECT ad.*, dt.name as type_name
                FROM applicant_documents ad
                LEFT JOIN document_types dt ON ad.document_type_id = dt.id
                WHERE ad.user_id = ?
                ORDER BY ad.created_at DESC
            ");
            if ($docStmt) {
                $docStmt->bind_param('i', $candidate['user_id']);
                $docStmt->execute();
                $docResult = $docStmt->get_result();
                while ($doc = $docResult->fetch_assoc()) {
                    $documents[] = $doc;
                }
                $docStmt->close();
            }
        } catch (\Exception $e) {
            // Table may not exist in local DB
        }
        $candidate['documents'] = $documents;

        // Get interviews
        $interviews = [];
        try {
            $intStmt = $this->recruitmentDb->prepare("
                SELECT i.*, qb.name as question_bank_name
                FROM interviews i
                LEFT JOIN interview_question_banks qb ON i.question_bank_id = qb.id
                WHERE i.application_id = ?
                ORDER BY i.created_at DESC
            ");
            if ($intStmt) {
                $intStmt->bind_param('i', $applicationId);
                $intStmt->execute();
                $intResult = $intStmt->get_result();
                while ($interview = $intResult->fetch_assoc()) {
                    $interviews[] = $interview;
                }
                $intStmt->close();
            }
        } catch (\Exception $e) {
            // Table may not exist in local DB
        }
        $candidate['interviews'] = $interviews;

        // Get medical checkups
        $medicals = [];
        try {
            $medStmt = $this->recruitmentDb->prepare("
                SELECT * FROM medical_checkups
                WHERE application_id = ?
                ORDER BY created_at DESC
            ");
            if ($medStmt) {
                $medStmt->bind_param('i', $applicationId);
                $medStmt->execute();
                $medResult = $medStmt->get_result();
                while ($medical = $medResult->fetch_assoc()) {
                    $medicals[] = $medical;
                }
                $medStmt->close();
            }
        } catch (\Exception $e) {
            // Table may not exist in local DB
        }
        $candidate['medical_checkups'] = $medicals;

        $data = [
            'title' => 'Detail Kandidat - ' . ($candidate['full_name'] ?? ''),
            'currentPage' => 'recruitment-pipeline',
            'candidate' => $candidate,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'recruitment/candidate_detail_modern' : 'recruitment/candidate_detail';

        return $this->view($view, $data);
    }

    /**
     * Get candidate detail as JSON (for AJAX modal)
     */
    public function candidateDetail($crewId = null)
    {
        $this->requireAuth();

        if (!$crewId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID not valid']);
            return;
        }

        // Get crew data
        $stmt = $this->db->prepare("
            SELECT c.*, r.name as rank_name 
            FROM crews c 
            LEFT JOIN ranks r ON c.current_rank_id = r.id 
            WHERE c.id = ?
        ");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $crew = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$crew) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Crew not found']);
            return;
        }

        // Get documents
        $docStmt = $this->db->prepare("
            SELECT * FROM crew_documents WHERE crew_id = ? ORDER BY created_at DESC
        ");
        $docStmt->bind_param('i', $crewId);
        $docStmt->execute();
        $documents = [];
        $docResult = $docStmt->get_result();
        while ($doc = $docResult->fetch_assoc()) {
            $documents[] = $doc;
        }
        $docStmt->close();

        header('Content-Type: application/json');
        echo json_encode([
            'crew' => $crew,
            'documents' => $documents
        ]);
    }

    /**
     * Create notification in ERP system
     */
    private function createNotification($type, $title, $message, $actionUrl = null)
    {
        try {
            // Check if notifications table exists
            $check = $this->db->query("SHOW TABLES LIKE 'notifications'");
            if ($check->num_rows > 0) {
                // Map custom types to display-friendly types
                $typeMap = [
                    'candidate_approved' => 'success',
                    'candidate_rejected' => 'warning',
                    'contract_created' => 'info',
                    'crew_onboarded' => 'success',
                ];
                $dbType = $typeMap[$type] ?? $type;

                $stmt = $this->db->prepare("
                    INSERT INTO notifications (type, title, message, link, is_read, created_at) 
                    VALUES (?, ?, ?, ?, 0, NOW())
                ");
                $stmt->bind_param('ssss', $dbType, $title, $message, $actionUrl);
                $stmt->execute();
                $stmt->close();
            }
        } catch (\Exception $e) {
            error_log("Notification error: " . $e->getMessage());
        }
    }

    /**
     * Log activity for audit
     */
    private function logActivity($action, $description)
    {
        try {
            // Ensure activity_logs table exists
            $this->db->query("
                CREATE TABLE IF NOT EXISTS activity_logs (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NULL,
                    action VARCHAR(100) NOT NULL,
                    description TEXT,
                    entity_type VARCHAR(50) NULL,
                    entity_id INT NULL,
                    ip_address VARCHAR(45) NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $userId = $_SESSION['user_id'] ?? null;
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs (user_id, action, description, ip_address, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param('isss', $userId, $action, $description, $ipAddress);
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Restore a rejected candidate back to Admin Review status
     */
    public function restoreRejected()
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $applicationId = (int) ($_POST['application_id'] ?? 0);
        if (!$applicationId) {
            echo json_encode(['success' => false, 'message' => 'Application ID required']);
            return;
        }

        if (!$this->recruitmentDb || $this->recruitmentDb->connect_error) {
            echo json_encode(['success' => false, 'message' => 'Recruitment DB not available']);
            return;
        }

        // Get application info
        $stmt = $this->recruitmentDb->prepare("
            SELECT a.id, a.status_id, a.erp_crew_id, u.full_name, s.name as status_name
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN application_statuses s ON a.status_id = s.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$app) {
            echo json_encode(['success' => false, 'message' => 'Application not found']);
            return;
        }

        if ($app['status_id'] != 7) { // 7 = Rejected
            echo json_encode(['success' => false, 'message' => 'Hanya kandidat yang ditolak yang bisa dikembalikan']);
            return;
        }

        // Determine target status: if has erp_crew_id, restore to Admin Review (9), else to Pending (1)
        $targetStatusId = !empty($app['erp_crew_id']) ? 9 : 1;
        $targetStatusName = !empty($app['erp_crew_id']) ? 'Admin Review' : 'Pending';

        // Update recruitment DB
        $stmt = $this->recruitmentDb->prepare("
            UPDATE applications 
            SET status_id = ?, status_updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param('ii', $targetStatusId, $applicationId);
        $stmt->execute();
        $stmt->close();

        // If has ERP crew, also reset the admin checklist rejection
        if (!empty($app['erp_crew_id'])) {
            $crewId = (int) $app['erp_crew_id'];
            
            // Reset crew status in ERP
            $stmt = $this->db->prepare("UPDATE crews SET status = 'pending_checklist' WHERE id = ?");
            $stmt->bind_param('i', $crewId);
            $stmt->execute();
            $stmt->close();

            // Reset any rejected items in admin checklist back to pending (0)
            $stmt = $this->db->prepare("
                UPDATE admin_checklists 
                SET owner_interview = CASE WHEN owner_interview = 2 THEN 0 ELSE owner_interview END,
                    document_check = CASE WHEN document_check = 2 THEN 0 ELSE document_check END,
                    pengantar_mcu = CASE WHEN pengantar_mcu = 2 THEN 0 ELSE pengantar_mcu END,
                    agreement_kontrak = CASE WHEN agreement_kontrak = 2 THEN 0 ELSE agreement_kontrak END,
                    admin_charge = CASE WHEN admin_charge = 2 THEN 0 ELSE admin_charge END,
                    ok_to_board = CASE WHEN ok_to_board = 2 THEN 0 ELSE ok_to_board END,
                    status = 'pending',
                    updated_at = NOW()
                WHERE crew_id = ?
            ");
            $stmt->bind_param('i', $crewId);
            $stmt->execute();
            $stmt->close();
        }

        // Log activity
        $this->logActivity('restore_rejected', "Restored rejected candidate: {$app['full_name']} (App #{$applicationId}) back to {$targetStatusName}");

        echo json_encode([
            'success' => true, 
            'message' => "{$app['full_name']} berhasil dikembalikan ke status {$targetStatusName}",
            'new_status' => $targetStatusName
        ]);
    }

    /**
     * Auto-onboarding - approved crew ready to onboard
     */
    public function onboarding()
    {
        $this->requireAuth();

        $approvedCrew = [];

        if ($this->recruitmentDb) {
            // First check if recruitment_sync table exists in ERP DB
            $result = $this->db->query("SHOW TABLES LIKE 'recruitment_sync'");
            if ($result->num_rows == 0) {
                // Table doesn't exist, create it
                $this->createRecruitmentSyncTable();
            }

            // Get approved crew from recruitment DB (by status name, not hardcoded ID)
            $query = "
                SELECT 
                    a.id as application_id,
                    u.full_name as applicant_name,
                    v.title as position_applied,
                    d.name as department_name,
                    u.email,
                    u.phone,
                    a.submitted_at as applied_date,
                    s.name as status_name
                FROM applications a
                JOIN users u ON a.user_id = u.id
                JOIN job_vacancies v ON a.vacancy_id = v.id
                LEFT JOIN departments d ON v.department_id = d.id
                JOIN application_statuses s ON a.status_id = s.id
                WHERE LOWER(s.name) = 'approved'
                ORDER BY a.submitted_at DESC
            ";

            $result = $this->recruitmentDb->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Check if already synced in ERP DB
                    $stmt = $this->db->prepare("SELECT sync_status FROM recruitment_sync WHERE recruitment_applicant_id = ?");
                    $stmt->bind_param('i', $row['application_id']);
                    $stmt->execute();
                    $syncResult = $stmt->get_result();
                    $syncData = $syncResult->fetch_assoc();
                    $stmt->close();

                    // Only add if not synced or pending
                    if (!$syncData || $syncData['sync_status'] === 'pending') {
                        $approvedCrew[] = $row;
                    }
                }
            }
        }

        $data = [
            'title' => 'Auto-Onboarding',
            'currentPage' => 'recruitment-onboarding',
            'approvedCrew' => $approvedCrew,
            'flash' => $this->getFlash()
        ];

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'recruitment/onboarding_modern' : 'recruitment/onboarding';

        return $this->view($view, $data);
    }

    /**
     * Process onboarding - create crew from approved candidate with complete data mapping
     */
    public function processOnboard($applicationId)
    {
        $this->requireAuth();

        if (!$this->recruitmentDb) {
            $this->setFlash('error', 'Koneksi ke database recruitment gagal');
            $this->redirect('recruitment/onboarding');
        }

        // Get complete application and user data from recruitment DB
        $stmt = $this->recruitmentDb->prepare("
            SELECT 
                a.*, 
                u.full_name, 
                u.email, 
                u.phone, 
                ap.date_of_birth,
                ap.address,
                ap.city,
                ap.nationality as country,
                ap.passport_no,
                ap.gender,
                ap.nationality,
                ap.emergency_name as emergency_contact_name,
                ap.emergency_phone as emergency_contact_phone,
                v.title as position_applied
            FROM applications a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN applicant_profiles ap ON u.id = ap.user_id
            JOIN job_vacancies v ON a.vacancy_id = v.id
            WHERE a.id = ?
        ");
        $stmt->bind_param('i', $applicationId);
        $stmt->execute();
        $application = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$application) {
            $this->setFlash('error', 'Data aplikasi tidak ditemukan');
            $this->redirect('recruitment/onboarding');
        }

        // Check if already synced
        $checkStmt = $this->db->prepare("SELECT crew_id FROM recruitment_sync WHERE recruitment_applicant_id = ?");
        $checkStmt->bind_param('i', $applicationId);
        $checkStmt->execute();
        $existing = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($existing) {
            $this->setFlash('warning', 'Kandidat ini sudah di-import sebelumnya');
            $this->redirect('recruitment/onboarding');
        }

        // Create crew in ERP with complete data
        try {
            $this->db->begin_transaction();

            // Generate employee_id (format: CRW-YYYY-XXXX)
            $year = date('Y');
            $countStmt = $this->db->prepare("SELECT COUNT(*) as count FROM crews WHERE employee_id LIKE ?");
            $pattern = "CRW-{$year}-%";
            $countStmt->bind_param('s', $pattern);
            $countStmt->execute();
            $count = $countStmt->get_result()->fetch_assoc()['count'];
            $countStmt->close();
            $employeeId = sprintf("CRW-%s-%04d", $year, $count + 1);

            // Insert crew with complete data mapping
            $stmt = $this->db->prepare("
                INSERT INTO crews (
                    employee_id, full_name, email, phone, 
                    birth_date, gender, nationality,
                    address, city,
                    emergency_name, emergency_phone,
                    current_rank_id, status, 
                    source, candidate_id, approved_at,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'standby', 'recruitment', ?, NOW(), NOW())
            ");

            // Map position to rank (default to 1 for now, can be enhanced later)
            $rankId = 1;

            $stmt->bind_param(
                'sssssssssssii',
                $employeeId,
                $application['full_name'],
                $application['email'],
                $application['phone'],
                $application['date_of_birth'],
                $application['gender'],
                $application['nationality'],
                $application['address'],
                $application['city'],
                $application['emergency_contact_name'],
                $application['emergency_contact_phone'],
                $rankId,
                $applicationId
            );
            $stmt->execute();
            $crewId = $this->db->insert_id;
            $stmt->close();

            // Log sync with complete tracking
            $stmt = $this->db->prepare("
                INSERT INTO recruitment_sync 
                (recruitment_applicant_id, crew_id, sync_status, synced_at, created_at)
                VALUES (?, ?, 'onboarded', NOW(), NOW())
            ");
            $stmt->bind_param('ii', $applicationId, $crewId);
            $stmt->execute();
            $stmt->close();

            // Update candidate in recruitment DB to mark as synced
            $updateStmt = $this->recruitmentDb->prepare("
                UPDATE users SET is_synced_to_erp = 1 WHERE id = ?
            ");
            $updateStmt->bind_param('i', $application['user_id']);
            $updateStmt->execute();
            $updateStmt->close();

            $this->db->commit();

            $this->setFlash('success', "✅ Berhasil import {$application['full_name']} sebagai crew dengan ID: {$employeeId}");
            $this->redirect('crews');
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Onboarding error: " . $e->getMessage());
            $this->setFlash('error', 'Gagal onboard crew: ' . $e->getMessage());
            $this->redirect('recruitment/onboarding');
        }
    }

    /**
     * Create recruitment_sync table if not exists
     */
    private function createRecruitmentSyncTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS recruitment_sync (
                id INT PRIMARY KEY AUTO_INCREMENT,
                recruitment_applicant_id INT NOT NULL,
                crew_id INT,
                contract_id INT,
                sync_status ENUM('pending', 'synced', 'onboarded', 'failed') DEFAULT 'pending',
                synced_at TIMESTAMP NULL,
                error_message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $this->db->query($sql);
    }

    /**
     * Ensure admin_checklists table exists
     */
    private function ensureAdminChecklistTable()
    {
        $result = $this->db->query("SHOW TABLES LIKE 'admin_checklists'");
        if ($result && $result->num_rows == 0) {
            $this->db->query("
                CREATE TABLE admin_checklists (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    crew_id INT UNSIGNED NOT NULL,
                    application_id INT NULL,
                    document_check TINYINT DEFAULT 0,
                    document_check_notes TEXT NULL,
                    document_check_at DATETIME NULL,
                    owner_interview TINYINT DEFAULT 0,
                    owner_interview_notes TEXT NULL,
                    owner_interview_at DATETIME NULL,
                    pengantar_mcu TINYINT DEFAULT 0,
                    pengantar_mcu_notes TEXT NULL,
                    pengantar_mcu_at DATETIME NULL,
                    agreement_kontrak TINYINT DEFAULT 0,
                    agreement_kontrak_notes TEXT NULL,
                    agreement_kontrak_at DATETIME NULL,
                    admin_charge TINYINT DEFAULT 0,
                    admin_charge_notes TEXT NULL,
                    admin_charge_at DATETIME NULL,
                    ok_to_board TINYINT DEFAULT 0,
                    ok_to_board_notes TEXT NULL,
                    ok_to_board_at DATETIME NULL,
                    status ENUM('in_progress','completed','rejected') DEFAULT 'in_progress',
                    rejected_reason TEXT NULL,
                    completed_at DATETIME NULL,
                    checked_by INT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_crew_checklist (crew_id),
                    KEY idx_checklist_status (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    public function __destruct()
    {
        if ($this->recruitmentDb) {
            $this->recruitmentDb->close();
        }
    }
}
