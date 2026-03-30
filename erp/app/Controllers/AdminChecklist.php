<?php
/**
 * PT Indo Ocean - ERP System
 * Admin Checklist Controller (Stage 2 of Recruitment Flow)
 * Manages document check, owner interview, MCU, agreement, admin charge, OK to board
 */

namespace App\Controllers;

class AdminChecklist extends BaseController
{
    /**
     * Ensure admin_checklists table exists
     */
    private function ensureTable()
    {
        // Ensure crews status ENUM includes new values
        try {
            $this->db->query("
                ALTER TABLE crews MODIFY COLUMN status 
                ENUM('available','onboard','standby','terminated','pending_approval','rejected','contracted','pending_checklist','ready_operational','on_board') 
                COLLATE utf8mb4_unicode_ci DEFAULT 'available'
            ");
        } catch (\Exception $e) {
            // Already has the values or other non-critical error
        }

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

        $result2 = $this->db->query("SHOW TABLES LIKE 'crew_operationals'");
        if ($result2 && $result2->num_rows == 0) {
            $this->db->query("
                CREATE TABLE crew_operationals (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    crew_id INT UNSIGNED NOT NULL,
                    checklist_id INT UNSIGNED NOT NULL,
                    hotel_name VARCHAR(255) NULL,
                    hotel_checkout_date DATE NULL,
                    transport_to_airport TEXT NULL,
                    ticket_booking_code VARCHAR(100) NULL,
                    airport_depart VARCHAR(255) NULL,
                    airport_arrival VARCHAR(255) NULL,
                    notes TEXT NULL,
                    status ENUM('pending','completed') DEFAULT 'pending',
                    completed_at DATETIME NULL,
                    created_by INT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_crew_operational (crew_id),
                    KEY idx_operational_status (status)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
    }

    /**
     * List all candidates in admin checklist (Stage 2)
     */
    public function index()
    {
        $this->requireAuth();
        $this->ensureTable();

        // Get all crews that are in admin checklist stage (NOT yet moved to operational/on_board)
        $query = "
            SELECT 
                c.id as crew_id,
                c.employee_id,
                c.full_name,
                c.email,
                c.phone,
                c.photo,
                c.status as crew_status,
                c.created_at as crew_created_at,
                r.name as rank_name,
                ac.id as checklist_id,
                ac.document_check,
                ac.owner_interview,
                ac.pengantar_mcu,
                ac.agreement_kontrak,
                ac.admin_charge,
                ac.ok_to_board,
                ac.status as checklist_status,
                ac.created_at as checklist_created_at,
                (SELECT COUNT(*) FROM crew_documents cd WHERE cd.crew_id = c.id) as doc_count
            FROM crews c
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            LEFT JOIN admin_checklists ac ON ac.crew_id = c.id
            WHERE (
                c.status IN ('pending_checklist', 'pending_approval')
                OR (ac.status = 'in_progress' AND c.status NOT IN ('ready_operational', 'on_board', 'onboard', 'contracted'))
            )
            AND (ac.status IS NULL OR ac.status != 'completed')
            ORDER BY c.created_at DESC
        ";
        $result = $this->db->query($query);
        $candidates = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Calculate progress
                $items = ['document_check', 'owner_interview', 'pengantar_mcu', 'agreement_kontrak', 'admin_charge', 'ok_to_board'];
                $completed = 0;
                foreach ($items as $item) {
                    if (($row[$item] ?? 0) == 1) $completed++;
                }
                $row['progress'] = $completed;
                $row['progress_total'] = 6;
                $row['progress_percent'] = round(($completed / 6) * 100);

                // Auto-create checklist if not exists
                if (!$row['checklist_id'] && in_array($row['crew_status'], ['pending_checklist', 'pending_approval'])) {
                    $stmt = $this->db->prepare("INSERT IGNORE INTO admin_checklists (crew_id) VALUES (?)");
                    $stmt->bind_param('i', $row['crew_id']);
                    $stmt->execute();
                    $row['checklist_id'] = $this->db->insert_id;
                    $stmt->close();
                }

                $candidates[] = $row;
            }
        }

        // Stats
        $stats = [
            'total' => count($candidates),
            'in_progress' => count(array_filter($candidates, fn($c) => ($c['checklist_status'] ?? 'in_progress') === 'in_progress')),
            'completed' => 0,
            'rejected' => 0
        ];

        // Count completed
        $completedResult = $this->db->query("SELECT COUNT(*) as c FROM admin_checklists WHERE status = 'completed'");
        if ($completedResult) $stats['completed'] = $completedResult->fetch_assoc()['c'];

        // Count rejected
        $rejectedResult = $this->db->query("SELECT COUNT(*) as c FROM admin_checklists WHERE status = 'rejected'");
        if ($rejectedResult) $stats['rejected'] = $rejectedResult->fetch_assoc()['c'];

        // Get rejected list for archive
        $rejectedQuery = "
            SELECT c.id as crew_id, c.employee_id, c.full_name, c.email, c.photo,
                   r.name as rank_name, ac.rejected_reason, ac.updated_at as rejected_at
            FROM admin_checklists ac
            JOIN crews c ON ac.crew_id = c.id
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE ac.status = 'rejected'
            ORDER BY ac.updated_at DESC
        ";
        $rejectedList = [];
        $rejResult = $this->db->query($rejectedQuery);
        if ($rejResult) {
            while ($row = $rejResult->fetch_assoc()) {
                $rejectedList[] = $row;
            }
        }

        // Get recruiter/PIC info from recruitment DB
        $crewIds = array_column($candidates, 'crew_id');
        $recruiterMap = $this->getRecruiterInfo($crewIds);

        $data = [
            'title' => 'Admin Checklist',
            'currentPage' => 'admin-checklist',
            'candidates' => $candidates,
            'rejectedList' => $rejectedList,
            'stats' => $stats,
            'recruiterMap' => $recruiterMap,
            'flash' => $this->getFlash()
        ];

        return $this->view('admin_checklist/index_modern', $data);
    }

    /**
     * View checklist detail for a specific crew
     */
    public function detail($crewId = null)
    {
        $this->requireAuth();
        $this->ensureTable();

        if (!$crewId) {
            $this->setFlash('error', 'ID crew tidak valid');
            $this->redirect('AdminChecklist');
            return;
        }

        // Get crew data
        $stmt = $this->db->prepare("
            SELECT c.*, r.name as rank_name,
                   (SELECT COUNT(*) FROM crew_documents cd WHERE cd.crew_id = c.id) as doc_count
            FROM crews c
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE c.id = ?
        ");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $crew = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$crew) {
            $this->setFlash('error', 'Data crew tidak ditemukan');
            $this->redirect('AdminChecklist');
            return;
        }

        // Get or create checklist
        $clStmt = $this->db->prepare("SELECT * FROM admin_checklists WHERE crew_id = ?");
        $clStmt->bind_param('i', $crewId);
        $clStmt->execute();
        $checklist = $clStmt->get_result()->fetch_assoc();
        $clStmt->close();

        if (!$checklist) {
            $insertStmt = $this->db->prepare("INSERT INTO admin_checklists (crew_id) VALUES (?)");
            $insertStmt->bind_param('i', $crewId);
            $insertStmt->execute();
            $checklistId = $this->db->insert_id;
            $insertStmt->close();

            $clStmt2 = $this->db->prepare("SELECT * FROM admin_checklists WHERE id = ?");
            $clStmt2->bind_param('i', $checklistId);
            $clStmt2->execute();
            $checklist = $clStmt2->get_result()->fetch_assoc();
            $clStmt2->close();
        }

        // Get crew documents
        $documents = [];
        $docStmt = $this->db->prepare("
            SELECT cd.*, dt.name as type_name 
            FROM crew_documents cd 
            LEFT JOIN document_types dt ON cd.document_type = dt.code
            WHERE cd.crew_id = ? 
            ORDER BY cd.created_at DESC
        ");
        if ($docStmt) {
            $docStmt->bind_param('i', $crewId);
            $docStmt->execute();
            $docResult = $docStmt->get_result();
            while ($doc = $docResult->fetch_assoc()) {
                $documents[] = $doc;
            }
            $docStmt->close();
        }

        // Calculate progress
        $items = ['document_check', 'owner_interview', 'pengantar_mcu', 'agreement_kontrak', 'admin_charge', 'ok_to_board'];
        $completed = 0;
        foreach ($items as $item) {
            if (($checklist[$item] ?? 0) == 1) $completed++;
        }

        // Get recruiter/PIC info from recruitment DB
        $recruiterMap = $this->getRecruiterInfo([$crewId]);
        $recruiterInfo = $recruiterMap[$crewId] ?? null;

        $data = [
            'title' => 'Admin Checklist - ' . $crew['full_name'],
            'currentPage' => 'admin-checklist',
            'crew' => $crew,
            'checklist' => $checklist,
            'documents' => $documents,
            'progress' => $completed,
            'progress_total' => 6,
            'recruiterInfo' => $recruiterInfo,
            'flash' => $this->getFlash()
        ];

        return $this->view('admin_checklist/view_modern', $data);
    }

    /**
     * Update a checklist item (AJAX)
     */
    public function updateItem()
    {
        $this->requireAuth();
        $this->ensureTable();

        if (!$this->isPost()) {
            return $this->json(['success' => false, 'message' => 'Invalid request']);
        }

        $checklistId = intval($this->input('checklist_id'));
        $item = $this->input('item');
        $value = intval($this->input('value')); // 0=pending, 1=passed, 2=rejected
        $notes = trim($this->input('notes', ''));

        // Validate item name
        $validItems = ['document_check', 'owner_interview', 'pengantar_mcu', 'agreement_kontrak', 'admin_charge', 'ok_to_board'];
        if (!in_array($item, $validItems)) {
            return $this->json(['success' => false, 'message' => 'Item tidak valid']);
        }

        if (!$checklistId) {
            return $this->json(['success' => false, 'message' => 'Checklist ID tidak valid']);
        }

        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 1;

        // ── SEQUENTIAL ENFORCEMENT ────────────────────────────────
        // When setting an item to PASSED (value=1), enforce sequential order:
        //   1. Document Check → 2. Owner Interview → 3. Pengantar MCU →
        //   4. Agreement Kontrak → 5. Admin Charge → 6. OK to Board
        // Each requires all previous items to be passed first.
        // Also perform real data validation for certain items.
        if ($value == 1) {
            // Get current checklist + crew_id
            $clCheck = $this->db->prepare("SELECT * FROM admin_checklists WHERE id = ?");
            if (!$clCheck) {
                return $this->json(['success' => false, 'message' => 'DB error: ' . $this->db->error]);
            }
            $clCheck->bind_param('i', $checklistId);
            $clCheck->execute();
            $currentCl = $clCheck->get_result()->fetch_assoc();
            $clCheck->close();

            if (!$currentCl) {
                return $this->json(['success' => false, 'message' => 'Checklist tidak ditemukan']);
            }

            $crewIdForCheck = (int)$currentCl['crew_id'];

            // Define sequential order and their display names
            $sequence = [
                'document_check'    => 'Document Check',
                'owner_interview'   => 'Owner Interview',
                'pengantar_mcu'     => 'Pengantar MCU',
                'agreement_kontrak' => 'Agreement Kontrak',
                'admin_charge'      => 'Admin Charge',
                'ok_to_board'       => 'OK to Board',
            ];
            $sequenceKeys = array_keys($sequence);
            $currentIndex = array_search($item, $sequenceKeys);

            // Check all PRECEDING items are passed (value == 1)
            for ($i = 0; $i < $currentIndex; $i++) {
                $prevItem = $sequenceKeys[$i];
                if (($currentCl[$prevItem] ?? 0) != 1) {
                    $prevLabel = $sequence[$prevItem];
                    $currentLabel = $sequence[$item];
                    return $this->json([
                        'success' => false,
                        'message' => "⚠️ Tidak bisa mencentang \"{$currentLabel}\" — \"{$prevLabel}\" harus diselesaikan terlebih dahulu.",
                        'blocked_by' => $prevItem,
                        'blocked_by_label' => $prevLabel
                    ]);
                }
            }

            // ── REAL DATA VALIDATION per item ────────────────────
            // 1) Document Check: verify crew actually has uploaded documents
            if ($item === 'document_check') {
                $docCountStmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM crew_documents WHERE crew_id = ?");
                if ($docCountStmt) {
                    $docCountStmt->bind_param('i', $crewIdForCheck);
                    $docCountStmt->execute();
                    $docCount = $docCountStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
                    $docCountStmt->close();
                    if ($docCount == 0) {
                        return $this->json([
                            'success' => false,
                            'message' => '⚠️ Tidak bisa pass Document Check — belum ada dokumen yang di-upload untuk crew ini. Silakan upload dokumen terlebih dahulu.',
                            'blocked_by' => 'no_documents'
                        ]);
                    }
                }
            }

            // 4) Agreement Kontrak: crew agrees to contract terms
            // Note: No contract validation needed here — contract is created AFTER operational stage
            // This step is for the crew member to agree to the employment terms

            // 6) OK to Board: verify all previous 5 items are passed (final gate)
            if ($item === 'ok_to_board') {
                $allPrev = true;
                for ($i = 0; $i < 5; $i++) {
                    if (($currentCl[$sequenceKeys[$i]] ?? 0) != 1) {
                        $allPrev = false;
                        break;
                    }
                }
                if (!$allPrev) {
                    return $this->json([
                        'success' => false,
                        'message' => '⚠️ Tidak bisa mencentang OK to Board — semua 5 item sebelumnya harus sudah PASSED.',
                        'blocked_by' => 'incomplete_checklist'
                    ]);
                }
            }
        }
        // ── END SEQUENTIAL ENFORCEMENT ────────────────────────────

        // Update the item
        $notesCol = $item . '_notes';
        $atCol = $item . '_at';

        $stmt = $this->db->prepare("
            UPDATE admin_checklists 
            SET `{$item}` = ?, 
                `{$notesCol}` = ?,
                `{$atCol}` = NOW(),
                checked_by = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        if (!$stmt) {
            return $this->json(['success' => false, 'message' => 'DB prepare error: ' . $this->db->error]);
        }
        $stmt->bind_param('isii', $value, $notes, $userId, $checklistId);
        $stmt->execute();
        $stmt->close();

        // If owner_interview is rejected (value=2), reject the whole checklist
        if ($item === 'owner_interview' && $value == 2) {
            $this->rejectChecklist($checklistId, 'Ditolak pada Owner Interview: ' . $notes);
            // Sync rejection to recruitment DB
            $crewStmt = $this->db->prepare("SELECT crew_id FROM admin_checklists WHERE id = ?");
            $crewStmt->bind_param('i', $checklistId);
            $crewStmt->execute();
            $crewRow = $crewStmt->get_result()->fetch_assoc();
            $crewStmt->close();
            if ($crewRow) {
                $this->syncToRecruitment($crewRow['crew_id'], 'rejected');
            }
            return $this->json([
                'success' => true, 
                'message' => 'Owner Interview: REJECTED. Kandidat dipindahkan ke arsip.',
                'rejected' => true
            ]);
        }

        // Check if all items are completed (value=1)
        $clStmt = $this->db->prepare("SELECT * FROM admin_checklists WHERE id = ?");
        $clStmt->bind_param('i', $checklistId);
        $clStmt->execute();
        $cl = $clStmt->get_result()->fetch_assoc();
        $clStmt->close();

        $allDone = true;
        $passedCount = 0;
        foreach ($validItems as $vi) {
            if (($cl[$vi] ?? 0) == 1) {
                $passedCount++;
            } else {
                $allDone = false;
            }
        }

        // Sync progress to recruitment DB in real-time
        if ($cl) {
            $this->syncChecklistProgressToRecruitment($cl['crew_id'], $passedCount, $allDone);
        }

        // AUTO-COMPLETE: When all 6 items pass, auto-complete the checklist
        if ($allDone && $cl) {
            try {
                // Mark checklist as completed
                $compStmt = $this->db->prepare("UPDATE admin_checklists SET status = 'completed', completed_at = NOW(), checked_by = ? WHERE id = ? AND status = 'in_progress'");
                $compStmt->bind_param('ii', $userId, $checklistId);
                $compStmt->execute();
                $compStmt->close();

                // Update crew status to ready_operational
                $crewUpd = $this->db->prepare("UPDATE crews SET status = 'ready_operational', updated_at = NOW() WHERE id = ? AND status IN ('pending_checklist', 'pending_approval')");
                $crewUpd->bind_param('i', $cl['crew_id']);
                $crewUpd->execute();
                $crewUpd->close();

                // Create operational entry
                $opStmt = $this->db->prepare("INSERT IGNORE INTO crew_operationals (crew_id, checklist_id, created_by) VALUES (?, ?, ?)");
                $opStmt->bind_param('iii', $cl['crew_id'], $checklistId, $userId);
                $opStmt->execute();
                $opStmt->close();

                // Sync Approved status to recruitment DB
                $this->syncToRecruitment($cl['crew_id'], 'approved');

                // Get crew name for notification
                $nameResult = $this->db->query("SELECT full_name FROM crews WHERE id = " . intval($cl['crew_id']));
                $crewName = $nameResult ? ($nameResult->fetch_assoc()['full_name'] ?? 'Crew') : 'Crew';

                // Create notification (safe - method may not exist)
                try {
                    if (method_exists($this, 'createNotification')) {
                        $this->createNotification(
                            'checklist_completed',
                            "Admin Checklist Selesai: {$crewName}",
                            "Semua item checklist untuk {$crewName} sudah di-pass. Status: Ready Operational.",
                            "AdminChecklist/detail/{$cl['crew_id']}"
                        );
                    }
                } catch (\Throwable $e) {
                    error_log("createNotification not available: " . $e->getMessage());
                }
            } catch (\Exception $e) {
                error_log("Auto-complete checklist error: " . $e->getMessage());
            }
        }

        $itemLabels = [
            'document_check' => 'Document Check',
            'owner_interview' => 'Owner Interview',
            'pengantar_mcu' => 'Pengantar MCU',
            'agreement_kontrak' => 'Agreement Kontrak',
            'admin_charge' => 'Admin Charge',
            'ok_to_board' => 'OK to Board'
        ];

        $message = $itemLabels[$item] . ': ' . ($value == 1 ? '✅ Passed' : ($value == 2 ? '❌ Rejected' : '⏳ Reset'));
        if ($allDone) {
            $message .= ' | 🎉 Semua checklist selesai! Status → Ready Operational & Approved.';
        }

        return $this->json([
            'success' => true,
            'message' => $message,
            'all_done' => $allDone,
            'passed_count' => $passedCount,
            'auto_completed' => $allDone
        ]);
    }

    /**
     * Complete checklist - move to operational (Stage 3)
     */
    public function complete($crewId = null)
    {
        $this->requireAuth();
        $this->ensureTable();

        if (!$crewId) {
            $this->setFlash('error', 'ID crew tidak valid');
            $this->redirect('AdminChecklist');
            return;
        }

        // Verify all items are completed
        $stmt = $this->db->prepare("SELECT * FROM admin_checklists WHERE crew_id = ?");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $cl = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$cl) {
            $this->setFlash('error', 'Checklist tidak ditemukan');
            $this->redirect('AdminChecklist');
            return;
        }

        $items = ['document_check', 'owner_interview', 'pengantar_mcu', 'agreement_kontrak', 'admin_charge', 'ok_to_board'];
        foreach ($items as $item) {
            if (($cl[$item] ?? 0) != 1) {
                $this->setFlash('error', 'Semua item checklist harus di-pass terlebih dahulu');
                $this->redirect('AdminChecklist/detail/' . $crewId);
                return;
            }
        }

        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 1;

        $this->db->begin_transaction();
        try {
            // Mark checklist as completed
            $updateStmt = $this->db->prepare("
                UPDATE admin_checklists SET status = 'completed', completed_at = NOW(), checked_by = ? WHERE id = ?
            ");
            $updateStmt->bind_param('ii', $userId, $cl['id']);
            $updateStmt->execute();
            $updateStmt->close();

            // Update crew status
            $crewStmt = $this->db->prepare("UPDATE crews SET status = 'ready_operational', updated_at = NOW() WHERE id = ?");
            $crewStmt->bind_param('i', $crewId);
            $crewStmt->execute();
            $crewStmt->close();

            // Create operational entry
            $opStmt = $this->db->prepare("INSERT IGNORE INTO crew_operationals (crew_id, checklist_id, created_by) VALUES (?, ?, ?)");
            $opStmt->bind_param('iii', $crewId, $cl['id'], $userId);
            $opStmt->execute();
            $opStmt->close();

            // Sync Approved status to recruitment DB
            $this->syncToRecruitment($crewId, 'approved');

            $this->db->commit();

            $this->setFlash('success', '✅ Admin Checklist selesai! Status recruitment → Approved.');
            $this->redirect('AdminChecklist/detail/' . $crewId);
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->setFlash('error', 'Gagal menyelesaikan checklist: ' . $e->getMessage());
            $this->redirect('AdminChecklist/detail/' . $crewId);
        }
    }

    /**
     * Mark candidate as On Board - final stage
     */
    public function onBoard($crewId = null)
    {
        $this->requireAuth();
        
        if (!$crewId) {
            $this->setFlash('error', 'ID crew tidak valid');
            $this->redirect('AdminChecklist');
            return;
        }

        // Verify checklist is completed first
        $stmt = $this->db->prepare("SELECT * FROM admin_checklists WHERE crew_id = ?");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $cl = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$cl || $cl['status'] !== 'completed') {
            $this->setFlash('error', 'Checklist harus diselesaikan terlebih dahulu');
            $this->redirect('AdminChecklist/detail/' . $crewId);
            return;
        }

        // Update crew status to on_board
        $crewStmt = $this->db->prepare("UPDATE crews SET status = 'on_board', updated_at = NOW() WHERE id = ?");
        $crewStmt->bind_param('i', $crewId);
        $crewStmt->execute();
        $crewStmt->close();

        // Sync On Board status to recruitment
        $this->syncToRecruitment($crewId, 'on_board');

        $this->setFlash('success', '🚢 Kandidat sudah On Board! Status recruitment di-update.');
        $this->redirect('AdminChecklist/detail/' . $crewId);
    }

    /**
     * Reject a candidate from admin checklist
     */
    public function reject($crewId = null)
    {
        $this->requireAuth();
        $this->ensureTable();

        if (!$crewId) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'ID crew tidak valid']);
            }
            $this->setFlash('error', 'ID crew tidak valid');
            $this->redirect('AdminChecklist');
            return;
        }

        $reason = trim($this->input('reason', ''));

        $stmt = $this->db->prepare("SELECT id FROM admin_checklists WHERE crew_id = ?");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $cl = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$cl) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Checklist tidak ditemukan']);
            }
            $this->setFlash('error', 'Checklist tidak ditemukan');
            $this->redirect('AdminChecklist');
            return;
        }

        $this->rejectChecklist($cl['id'], $reason);

        // Auto-sync to recruitment DB
        $this->syncToRecruitment($crewId, 'rejected');

        if ($this->isAjax()) {
            return $this->json(['success' => true, 'message' => 'Kandidat berhasil di-reject']);
        }

        $this->setFlash('success', 'Kandidat berhasil di-reject dan dipindahkan ke arsip.');
        $this->redirect('AdminChecklist');
    }

    /**
     * Restore a rejected candidate from archive back to Admin Checklist (in_progress)
     */
    public function restoreFromArchive($crewId = null)
    {
        $this->requireAuth();
        header('Content-Type: application/json');

        if (!$crewId) {
            echo json_encode(['success' => false, 'message' => 'ID crew tidak valid']);
            return;
        }

        $reason = $_POST['reason'] ?? 'Dikembalikan ke Admin Checklist';
        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 1;

        try {
            $this->db->begin_transaction();

            // 1. Reset admin_checklist status to in_progress
            $clStmt = $this->db->prepare("
                UPDATE admin_checklists SET 
                    status = 'in_progress', 
                    rejected_reason = NULL,
                    checked_by = ?,
                    updated_at = NOW() 
                WHERE crew_id = ?
            ");
            $clStmt->bind_param('ii', $userId, $crewId);
            $clStmt->execute();
            $clStmt->close();

            // 2. Reset crew status to pending_checklist
            $crewStmt = $this->db->prepare("
                UPDATE crews SET status = 'pending_checklist', updated_at = NOW() WHERE id = ?
            ");
            $crewStmt->bind_param('i', $crewId);
            $crewStmt->execute();
            $crewStmt->close();

            // 3. Get crew name
            $nameResult = $this->db->query("SELECT full_name FROM crews WHERE id = " . intval($crewId));
            $crewName = $nameResult ? ($nameResult->fetch_assoc()['full_name'] ?? 'Crew') : 'Crew';

            $this->db->commit();

            // 4. Sync to recruitment DB (Processing = 10)
            if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
                $syncStmt = $this->recruitmentDb->prepare("
                    UPDATE applications SET status_id = 10, status_updated_at = NOW(), updated_at = NOW() WHERE erp_crew_id = ?
                ");
                if ($syncStmt) {
                    $syncStmt->bind_param('i', $crewId);
                    $syncStmt->execute();
                    $syncStmt->close();
                }
            }

            error_log("Restored candidate from archive: {$crewName} (Crew ID: {$crewId}). Reason: {$reason}");

            echo json_encode([
                'success' => true,
                'message' => "✅ {$crewName} berhasil dikembalikan ke Admin Checklist.",
                'redirect_url' => BASE_URL . "AdminChecklist/detail/{$crewId}"
            ]);
        } catch (\Exception $e) {
            $this->db->rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Internal: reject checklist and update all related data
     */
    private function rejectChecklist($checklistId, $reason = '')
    {
        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 1;

        $this->db->begin_transaction();
        try {
            // Update checklist status
            $stmt = $this->db->prepare("
                UPDATE admin_checklists SET status = 'rejected', rejected_reason = ?, checked_by = ?, updated_at = NOW() WHERE id = ?
            ");
            $stmt->bind_param('sii', $reason, $userId, $checklistId);
            $stmt->execute();
            $stmt->close();

            // Get crew_id and application_id
            $clStmt = $this->db->prepare("SELECT crew_id, application_id FROM admin_checklists WHERE id = ?");
            $clStmt->bind_param('i', $checklistId);
            $clStmt->execute();
            $cl = $clStmt->get_result()->fetch_assoc();
            $clStmt->close();

            if ($cl) {
                // Update crew status
                $crewStmt = $this->db->prepare("
                    UPDATE crews SET status = 'rejected', rejection_reason = ?, rejected_at = NOW(), rejected_by = ?, updated_at = NOW() WHERE id = ?
                ");
                $crewStmt->bind_param('sii', $reason, $userId, $cl['crew_id']);
                $crewStmt->execute();
                $crewStmt->close();

                // Update recruitment DB
                if ($this->recruitmentDb && !$this->recruitmentDb->connect_error && $cl['application_id']) {
                    try {
                        $appStmt = $this->recruitmentDb->prepare("
                            UPDATE applications SET status_id = (
                                SELECT id FROM application_statuses WHERE LOWER(name) = 'rejected' LIMIT 1
                            ), rejection_reason = ?, updated_at = NOW() WHERE id = ?
                        ");
                        if ($appStmt) {
                            $appStmt->bind_param('si', $reason, $cl['application_id']);
                            $appStmt->execute();
                            $appStmt->close();
                        }
                    } catch (\Exception $e) {
                        error_log("Recruitment DB reject update warning: " . $e->getMessage());
                    }
                }
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Reject checklist error: " . $e->getMessage());
        }
    }

    /**
     * Sync status back to recruitment_db
     */
    private function syncToRecruitment($crewId, $action = 'approved')
    {
        try {
            if (!$this->recruitmentDb || $this->recruitmentDb->connect_error) return;

            // Map action to recruitment status_id
            $statusMap = [
                'admin_review' => 9,  // Admin Review (just imported to ERP)
                'processing' => 10,   // Processing (Admin Checklist in progress)
                'approved' => 6,      // Approved (all checklist items passed)
                'rejected' => 7,      // Rejected
                'on_board' => 11,     // On Board (final stage)
            ];
            $newStatusId = $statusMap[$action] ?? null;
            if (!$newStatusId) return;

            // Find application by erp_crew_id
            $stmt = $this->recruitmentDb->prepare("UPDATE applications SET status_id = ?, status_updated_at = NOW(), updated_at = NOW() WHERE erp_crew_id = ?");
            if ($stmt) {
                $stmt->bind_param('ii', $newStatusId, $crewId);
                $stmt->execute();
                $stmt->close();
            }

            // If rejected, also archive in recruitment
            if ($action === 'rejected') {
                // Try to set is_archived if column exists
                try {
                    $stmt2 = $this->recruitmentDb->prepare("UPDATE applications SET is_archived = 1 WHERE erp_crew_id = ?");
                    if ($stmt2) {
                        $stmt2->bind_param('i', $crewId);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                } catch (\Exception $e) {
                    // Column might not exist, ignore
                }
            }
        } catch (\Throwable $e) {
            // Log but don't fail
            error_log('syncToRecruitment error: ' . $e->getMessage());
        }
    }

    /**
     * Sync checklist progress to recruitment DB in real-time
     * Called every time an admin checklist item is updated
     */
    private function syncChecklistProgressToRecruitment($crewId, $passedCount, $allDone = false)
    {
        try {
            if (!$this->recruitmentDb || $this->recruitmentDb->connect_error) return;

            // Ensure checklist_progress column exists
            try {
                $colCheck = $this->recruitmentDb->query("SHOW COLUMNS FROM applications LIKE 'checklist_progress'");
                if ($colCheck && $colCheck->num_rows == 0) {
                    $this->recruitmentDb->query("ALTER TABLE applications ADD COLUMN checklist_progress TINYINT DEFAULT 0 AFTER erp_crew_id");
                    $this->recruitmentDb->query("ALTER TABLE applications ADD COLUMN checklist_updated_at DATETIME NULL AFTER checklist_progress");
                }
            } catch (\Exception $e) { /* ignore */ }

            // Update progress and auto-change status
            if ($allDone) {
                // All 6 items passed → move to Approved (6)
                $stmt = $this->recruitmentDb->prepare("
                    UPDATE applications 
                    SET checklist_progress = ?, checklist_updated_at = NOW(), status_id = 6, status_updated_at = NOW(), updated_at = NOW()
                    WHERE erp_crew_id = ?
                ");
            } else {
                // Still in progress → ensure status is Processing (10)
                $stmt = $this->recruitmentDb->prepare("
                    UPDATE applications 
                    SET checklist_progress = ?, checklist_updated_at = NOW(), updated_at = NOW(),
                        status_id = CASE WHEN status_id IN (9, 10) THEN 10 ELSE status_id END
                    WHERE erp_crew_id = ?
                ");
            }
            if ($stmt) {
                $stmt->bind_param('ii', $passedCount, $crewId);
                $stmt->execute();
                $stmt->close();
            }
        } catch (\Throwable $e) {
            error_log('syncChecklistProgress error: ' . $e->getMessage());
        }
    }

    /**
     * Get PIC/recruiter info from recruitment DB for a crew member
     */
    private function getRecruiterInfo($crewIds)
    {
        $recruiterMap = [];
        try {
            if (!$this->recruitmentDb || $this->recruitmentDb->connect_error) return $recruiterMap;
            if (empty($crewIds)) return $recruiterMap;

            $idList = implode(',', array_map('intval', $crewIds));
            $result = $this->recruitmentDb->query("
                SELECT 
                    a.erp_crew_id,
                    u_crewing.full_name as recruiter_name,
                    u_crewing.email as recruiter_email,
                    aa.assigned_at
                FROM applications a
                JOIN application_assignments aa ON a.id = aa.application_id AND aa.status = 'active'
                JOIN users u_crewing ON aa.assigned_to = u_crewing.id
                WHERE a.erp_crew_id IN ($idList)
            ");

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $recruiterMap[$row['erp_crew_id']] = $row;
                }
            }

            // Fallback: try current_crewing_id if no assignment found
            $missingIds = array_diff(array_map('intval', $crewIds), array_keys($recruiterMap));
            if (!empty($missingIds)) {
                $missingList = implode(',', $missingIds);
                $result2 = $this->recruitmentDb->query("
                    SELECT 
                        a.erp_crew_id,
                        u.full_name as recruiter_name,
                        u.email as recruiter_email
                    FROM applications a
                    JOIN users u ON a.current_crewing_id = u.id
                    WHERE a.erp_crew_id IN ($missingList)
                      AND a.current_crewing_id IS NOT NULL
                ");
                if ($result2) {
                    while ($row = $result2->fetch_assoc()) {
                        if (!isset($recruiterMap[$row['erp_crew_id']])) {
                            $recruiterMap[$row['erp_crew_id']] = $row;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            error_log('getRecruiterInfo error: ' . $e->getMessage());
        }
        return $recruiterMap;
    }

    /**
     * AJAX: Get real data for a sub-item inside book modal (Level 2)
     * URL: /AdminChecklist/getSubItemData/{crewId}/{category}/{subItem}
     */
    public function getSubItemData($crewId = null, $category = null, $subItem = null)
    {
        $this->requireAuth();

        if (!$crewId || !$category || !$subItem) {
            return $this->json(['success' => false, 'message' => 'Parameter tidak lengkap']);
        }

        $crewId = intval($crewId);
        $result = ['success' => true, 'title' => '', 'fields' => [], 'link' => null, 'empty_message' => null];

        try {
            switch ($category) {
                case 'document_check':
                    $result = $this->getDocumentData($crewId, $subItem);
                    break;
                case 'owner_interview':
                    $result = $this->getInterviewData($crewId, $subItem);
                    break;
                case 'pengantar_mcu':
                    $result = $this->getMcuData($crewId, $subItem);
                    break;
                case 'agreement_kontrak':
                    $result = $this->getContractData($crewId, $subItem);
                    break;
                case 'admin_charge':
                    $result = $this->getAdminChargeData($crewId, $subItem);
                    break;
                case 'ok_to_board':
                    $result = $this->getOkToBoardData($crewId, $subItem);
                    break;
                default:
                    $result = ['success' => false, 'message' => 'Kategori tidak dikenal'];
            }
        } catch (\Exception $e) {
            error_log("getSubItemData error: " . $e->getMessage());
            $result = ['success' => false, 'message' => 'Gagal mengambil data'];
        }

        return $this->json($result);
    }

    // ── DOCUMENT CHECK sub-items ──────────────────────
    private function getDocumentData($crewId, $subItem)
    {
        $typeMap = [
            'passport' => 'PASSPORT',
            'seaman_book' => 'SEAMAN_BOOK',
            'coc' => 'COC',
            'stcw_bst' => 'BST',
            'ktp' => 'KTP',
            'medical_cert' => 'MEDICAL',
            'yellow_fever' => 'YELLOW_FEVER',
            'covid_vax' => 'COVID_VAX',
        ];

        $labelMap = [
            'passport' => 'Passport',
            'seaman_book' => 'Seaman Book',
            'coc' => 'Certificate of Competency',
            'stcw_bst' => 'STCW / BST',
            'ktp' => 'KTP / Identitas',
            'medical_cert' => 'Medical Certificate',
            'yellow_fever' => 'Yellow Fever Vaccination',
            'covid_vax' => 'COVID-19 Vaccination',
        ];

        $docType = $typeMap[$subItem] ?? null;
        if (!$docType) return ['success' => false, 'message' => 'Sub-item tidak valid'];

        $stmt = $this->db->prepare("
            SELECT cd.*, dt.name as type_name 
            FROM crew_documents cd 
            LEFT JOIN document_types dt ON cd.document_type = dt.code
            WHERE cd.crew_id = ? AND cd.document_type = ?
            ORDER BY cd.created_at DESC LIMIT 1
        ");
        $stmt->bind_param('is', $crewId, $docType);
        $stmt->execute();
        $doc = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$doc) {
            return [
                'success' => true,
                'title' => $labelMap[$subItem] ?? $subItem,
                'fields' => [],
                'empty_message' => 'Dokumen belum di-upload',
                'link' => ['url' => BASE_URL . 'crews/view/' . $crewId, 'label' => 'Upload Dokumen →']
            ];
        }

        $fields = [
            ['icon' => 'badge', 'label' => 'Nama Dokumen', 'value' => $doc['document_name'] ?: '-'],
            ['icon' => 'pin', 'label' => 'Nomor Dokumen', 'value' => $doc['document_number'] ?: '-'],
            ['icon' => 'event', 'label' => 'Tanggal Terbit', 'value' => $doc['issue_date'] ? date('d M Y', strtotime($doc['issue_date'])) : '-'],
            ['icon' => 'event_busy', 'label' => 'Tanggal Expired', 'value' => $doc['expiry_date'] ? date('d M Y', strtotime($doc['expiry_date'])) : '-'],
            ['icon' => 'location_city', 'label' => 'Penerbit', 'value' => $doc['issuing_authority'] ?: '-'],
            ['icon' => 'place', 'label' => 'Tempat Terbit', 'value' => $doc['issuing_place'] ?: '-'],
        ];

        $statusLabel = match($doc['status'] ?? 'pending') {
            'valid' => '✅ Valid',
            'expiring_soon' => '⚠️ Segera Expired',
            'expired' => '❌ Expired',
            default => '⏳ Pending'
        };
        $fields[] = ['icon' => 'verified', 'label' => 'Status', 'value' => $statusLabel];

        if (!empty($doc['file_path'])) {
            $fields[] = ['icon' => 'attach_file', 'label' => 'File', 'value' => '📎 Tersedia', 'link' => BASE_URL . $doc['file_path']];
        }

        return [
            'success' => true,
            'title' => $labelMap[$subItem] ?? $doc['type_name'] ?? $subItem,
            'fields' => $fields,
            'empty_message' => null
        ];
    }

    // ── OWNER INTERVIEW sub-items ─────────────────────
    private function getInterviewData($crewId, $subItem)
    {
        switch ($subItem) {
            case 'profil_crew':
                $stmt = $this->db->prepare("
                    SELECT c.*, r.name as rank_name FROM crews c 
                    LEFT JOIN ranks r ON c.current_rank_id = r.id WHERE c.id = ?
                ");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $crew = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$crew) return ['success' => true, 'title' => 'Data Profil', 'fields' => [], 'empty_message' => 'Data crew tidak ditemukan'];

                $age = $crew['birth_date'] ? (date_diff(date_create($crew['birth_date']), date_create('today'))->y . ' tahun') : '-';
                return [
                    'success' => true,
                    'title' => 'Data Profil Crew',
                    'fields' => [
                        ['icon' => 'person', 'label' => 'Nama', 'value' => $crew['full_name']],
                        ['icon' => 'military_tech', 'label' => 'Rank', 'value' => $crew['rank_name'] ?: '-'],
                        ['icon' => 'cake', 'label' => 'Umur', 'value' => $age],
                        ['icon' => 'wc', 'label' => 'Gender', 'value' => ucfirst($crew['gender'] ?? '-')],
                        ['icon' => 'flag', 'label' => 'Nationality', 'value' => $crew['nationality'] ?: '-'],
                        ['icon' => 'email', 'label' => 'Email', 'value' => $crew['email'] ?: '-'],
                        ['icon' => 'phone', 'label' => 'Telepon', 'value' => $crew['phone'] ?: '-'],
                        ['icon' => 'location_on', 'label' => 'Alamat', 'value' => ($crew['address'] ?: '-') . ($crew['city'] ? ', ' . $crew['city'] : '')],
                    ]
                ];

            case 'pengalaman_kerja':
                $stmt = $this->db->prepare("
                    SELECT * FROM crew_experiences WHERE crew_id = ? ORDER BY start_date DESC
                ");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $result = $stmt->get_result();
                $fields = [];
                $idx = 1;
                while ($exp = $result->fetch_assoc()) {
                    $period = ($exp['start_date'] ? date('M Y', strtotime($exp['start_date'])) : '?') 
                            . ' — ' . ($exp['end_date'] ? date('M Y', strtotime($exp['end_date'])) : 'sekarang');
                    $fields[] = ['icon' => 'directions_boat', 'label' => "#{$idx}: {$exp['vessel_name']}", 'value' => "{$exp['rank_position']} • {$exp['company_name']} ({$period})"];
                    $idx++;
                }
                $stmt->close();

                return [
                    'success' => true,
                    'title' => 'Pengalaman Kerja',
                    'fields' => $fields,
                    'empty_message' => empty($fields) ? 'Belum ada data pengalaman kerja' : null,
                    'link' => empty($fields) ? ['url' => BASE_URL . 'crews/view/' . $crewId, 'label' => 'Tambah Pengalaman →'] : null
                ];

            case 'skills':
                $stmt = $this->db->prepare("SELECT * FROM crew_skills WHERE crew_id = ? ORDER BY skill_name");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $result = $stmt->get_result();
                $fields = [];
                while ($skill = $result->fetch_assoc()) {
                    $levelBadge = match($skill['skill_level']) {
                        'expert' => '🟣 Expert',
                        'advanced' => '🔵 Advanced',
                        'intermediate' => '🟢 Intermediate',
                        default => '⚪ Basic'
                    };
                    $fields[] = ['icon' => 'psychology', 'label' => $skill['skill_name'], 'value' => $levelBadge . ($skill['certificate_id'] ? " (Cert: {$skill['certificate_id']})" : '')];
                }
                $stmt->close();

                return [
                    'success' => true,
                    'title' => 'Skills & Sertifikat',
                    'fields' => $fields,
                    'empty_message' => empty($fields) ? 'Belum ada data skills' : null
                ];

            case 'catatan_interview':
                $stmt = $this->db->prepare("SELECT owner_interview, owner_interview_notes, owner_interview_at FROM admin_checklists WHERE crew_id = ?");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $cl = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $status = match(intval($cl['owner_interview'] ?? 0)) {
                    1 => '✅ PASSED',
                    2 => '❌ REJECTED',
                    default => '⏳ PENDING'
                };

                return [
                    'success' => true,
                    'title' => 'Catatan Interview',
                    'fields' => [
                        ['icon' => 'how_to_reg', 'label' => 'Status', 'value' => $status],
                        ['icon' => 'schedule', 'label' => 'Waktu', 'value' => $cl['owner_interview_at'] ? date('d M Y H:i', strtotime($cl['owner_interview_at'])) : 'Belum dilakukan'],
                        ['icon' => 'notes', 'label' => 'Catatan', 'value' => $cl['owner_interview_notes'] ?: 'Tidak ada catatan'],
                    ]
                ];
            
            default:
                return ['success' => false, 'message' => 'Sub-item tidak valid'];
        }
    }

    // ── PENGANTAR MCU sub-items ───────────────────────
    private function getMcuData($crewId, $subItem)
    {
        $docMap = [
            'medical_cert' => ['type' => 'MEDICAL', 'label' => 'Medical Certificate'],
            'yellow_fever' => ['type' => 'YELLOW_FEVER', 'label' => 'Yellow Fever Vaccination'],
            'covid_vax' => ['type' => 'COVID_VAX', 'label' => 'COVID-19 Vaccination'],
        ];

        if ($subItem === 'catatan_mcu') {
            $stmt = $this->db->prepare("SELECT pengantar_mcu, pengantar_mcu_notes, pengantar_mcu_at FROM admin_checklists WHERE crew_id = ?");
            $stmt->bind_param('i', $crewId);
            $stmt->execute();
            $cl = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            return [
                'success' => true,
                'title' => 'Catatan MCU',
                'fields' => [
                    ['icon' => 'how_to_reg', 'label' => 'Status', 'value' => ($cl['pengantar_mcu'] ?? 0) == 1 ? '✅ PASSED' : '⏳ PENDING'],
                    ['icon' => 'schedule', 'label' => 'Waktu', 'value' => $cl['pengantar_mcu_at'] ? date('d M Y H:i', strtotime($cl['pengantar_mcu_at'])) : 'Belum dilakukan'],
                    ['icon' => 'notes', 'label' => 'Catatan', 'value' => $cl['pengantar_mcu_notes'] ?: 'Tidak ada catatan'],
                ]
            ];
        }

        $info = $docMap[$subItem] ?? null;
        if (!$info) return ['success' => false, 'message' => 'Sub-item tidak valid'];

        // Reuse document fetcher
        return $this->getDocumentData($crewId, $subItem === 'medical_cert' ? 'medical_cert' : ($subItem === 'yellow_fever' ? 'yellow_fever' : 'covid_vax'));
    }

    // ── AGREEMENT KONTRAK sub-items ───────────────────
    private function getContractData($crewId, $subItem)
    {
        // Fetch latest contract for this crew
        $stmt = $this->db->prepare("
            SELECT ct.*, v.name as vessel_name, cl.name as client_name, r.name as rank_name
            FROM contracts ct
            LEFT JOIN vessels v ON ct.vessel_id = v.id
            LEFT JOIN clients cl ON ct.client_id = cl.id
            LEFT JOIN ranks r ON ct.rank_id = r.id
            WHERE ct.crew_id = ?
            ORDER BY ct.created_at DESC LIMIT 1
        ");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $contract = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$contract) {
            return [
                'success' => true,
                'title' => 'Agreement Kontrak',
                'fields' => [],
                'empty_message' => 'Belum ada kontrak untuk crew ini',
                'link' => ['url' => BASE_URL . 'Contract/create?crew_id=' . $crewId, 'label' => 'Buat Kontrak →']
            ];
        }

        switch ($subItem) {
            case 'durasi_kontrak':
                $signOn = $contract['sign_on_date'] ? date('d M Y', strtotime($contract['sign_on_date'])) : '-';
                $signOff = $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-';
                return [
                    'success' => true,
                    'title' => 'Durasi Kontrak',
                    'fields' => [
                        ['icon' => 'pin', 'label' => 'No. Kontrak', 'value' => $contract['contract_no']],
                        ['icon' => 'login', 'label' => 'Sign On', 'value' => $signOn],
                        ['icon' => 'logout', 'label' => 'Sign Off', 'value' => $signOff],
                        ['icon' => 'date_range', 'label' => 'Durasi', 'value' => ($contract['duration_months'] ?? 0) . ' bulan'],
                        ['icon' => 'category', 'label' => 'Tipe Kontrak', 'value' => ucfirst($contract['contract_type'] ?? '-')],
                        ['icon' => 'info', 'label' => 'Status', 'value' => strtoupper($contract['status'] ?? '-')],
                    ]
                ];

            case 'gaji_tunjangan':
                $salStmt = $this->db->prepare("
                    SELECT cs.*, cur.code as currency_code, cur.symbol as currency_symbol 
                    FROM contract_salaries cs 
                    LEFT JOIN currencies cur ON cs.currency_id = cur.id
                    WHERE cs.contract_id = ?
                ");
                $salStmt->bind_param('i', $contract['id']);
                $salStmt->execute();
                $salary = $salStmt->get_result()->fetch_assoc();
                $salStmt->close();

                if (!$salary) {
                    return [
                        'success' => true,
                        'title' => 'Gaji & Tunjangan',
                        'fields' => [],
                        'empty_message' => 'Data gaji belum diisi di kontrak',
                        'link' => ['url' => BASE_URL . 'Contract/edit/' . $contract['id'], 'label' => 'Edit Kontrak →']
                    ];
                }

                $sym = $salary['currency_symbol'] ?? '$';
                $fmt = function($v) use ($sym) { return $sym . ' ' . number_format($v ?? 0, 0, ',', '.'); };
                return [
                    'success' => true,
                    'title' => 'Gaji & Tunjangan',
                    'fields' => [
                        ['icon' => 'account_balance_wallet', 'label' => 'Basic Salary', 'value' => $fmt($salary['basic_salary'])],
                        ['icon' => 'schedule', 'label' => 'Overtime Allowance', 'value' => $fmt($salary['overtime_allowance'])],
                        ['icon' => 'beach_access', 'label' => 'Leave Pay', 'value' => $fmt($salary['leave_pay'])],
                        ['icon' => 'card_giftcard', 'label' => 'Bonus', 'value' => $fmt($salary['bonus'])],
                        ['icon' => 'add_circle', 'label' => 'Other Allowance', 'value' => $fmt($salary['other_allowance'])],
                        ['icon' => 'payments', 'label' => 'Total Monthly', 'value' => $fmt($salary['total_monthly']), 'highlight' => true],
                    ]
                ];

            case 'penempatan':
                return [
                    'success' => true,
                    'title' => 'Penempatan',
                    'fields' => [
                        ['icon' => 'directions_boat', 'label' => 'Vessel', 'value' => $contract['vessel_name'] ?: '-'],
                        ['icon' => 'business', 'label' => 'Client', 'value' => $contract['client_name'] ?: '-'],
                        ['icon' => 'military_tech', 'label' => 'Rank', 'value' => $contract['rank_name'] ?: '-'],
                        ['icon' => 'flight_takeoff', 'label' => 'Embarkation Port', 'value' => $contract['embarkation_port'] ?: '-'],
                        ['icon' => 'flight_land', 'label' => 'Disembarkation Port', 'value' => $contract['disembarkation_port'] ?: '-'],
                    ]
                ];

            case 'terms_conditions':
                return [
                    'success' => true,
                    'title' => 'Terms & Conditions',
                    'fields' => [
                        ['icon' => 'category', 'label' => 'Tipe Kontrak', 'value' => ucfirst($contract['contract_type'] ?? '-')],
                        ['icon' => 'info', 'label' => 'Status', 'value' => strtoupper($contract['status'] ?? '-')],
                        ['icon' => 'autorenew', 'label' => 'Renewal', 'value' => ($contract['is_renewal'] ?? 0) ? 'Ya (perpanjangan)' : 'Tidak (baru)'],
                        ['icon' => 'notes', 'label' => 'Catatan', 'value' => $contract['notes'] ?: 'Tidak ada catatan khusus'],
                    ]
                ];

            case 'tanda_tangan':
                // Check approvals
                $approvals = [];
                $apStmt = $this->db->prepare("
                    SELECT ca.*, u.name as approver_name 
                    FROM contract_approvals ca 
                    LEFT JOIN users u ON ca.approved_by = u.id
                    WHERE ca.contract_id = ? ORDER BY ca.created_at DESC
                ");
                if ($apStmt) {
                    $apStmt->bind_param('i', $contract['id']);
                    $apStmt->execute();
                    $apResult = $apStmt->get_result();
                    while ($ap = $apResult->fetch_assoc()) $approvals[] = $ap;
                    $apStmt->close();
                }

                $fields = [
                    ['icon' => 'pin', 'label' => 'No. Kontrak', 'value' => $contract['contract_no']],
                    ['icon' => 'info', 'label' => 'Status', 'value' => strtoupper($contract['status'] ?? '-')],
                    ['icon' => 'event', 'label' => 'Dibuat', 'value' => $contract['created_at'] ? date('d M Y H:i', strtotime($contract['created_at'])) : '-'],
                ];

                foreach ($approvals as $i => $ap) {
                    $fields[] = ['icon' => 'draw', 'label' => 'Approval #' . ($i+1), 'value' => ($ap['approver_name'] ?? 'Unknown') . ' — ' . strtoupper($ap['status'] ?? '') . ' (' . date('d M Y', strtotime($ap['created_at'])) . ')'];
                }

                if (empty($approvals)) {
                    $fields[] = ['icon' => 'draw', 'label' => 'Approval', 'value' => 'Belum ada approval'];
                }

                return ['success' => true, 'title' => 'Tanda Tangan & Approval', 'fields' => $fields];

            default:
                return ['success' => false, 'message' => 'Sub-item tidak valid'];
        }
    }

    // ── ADMIN CHARGE sub-items ────────────────────────
    private function getAdminChargeData($crewId, $subItem)
    {
        // Find invoice linked to this crew's contract
        $contractStmt = $this->db->prepare("SELECT id FROM contracts WHERE crew_id = ? ORDER BY created_at DESC LIMIT 1");
        $contractStmt->bind_param('i', $crewId);
        $contractStmt->execute();
        $contractRow = $contractStmt->get_result()->fetch_assoc();
        $contractStmt->close();
        $contractId = $contractRow['id'] ?? 0;

        switch ($subItem) {
            case 'biaya_admin':
                $stmt = $this->db->prepare("
                    SELECT fi.*, cl.name as client_name 
                    FROM finance_invoices fi 
                    LEFT JOIN clients cl ON fi.client_id = cl.id
                    WHERE fi.vessel_id IN (SELECT vessel_id FROM contracts WHERE crew_id = ?)
                    ORDER BY fi.created_at DESC LIMIT 5
                ");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $result = $stmt->get_result();
                $fields = [];
                while ($inv = $result->fetch_assoc()) {
                    $statusBadge = match($inv['status']) {
                        'paid' => '✅ Paid',
                        'partial' => '🟡 Partial',
                        'sent', 'unpaid' => '📤 Unpaid',
                        'overdue' => '🔴 Overdue',
                        default => '📋 ' . ucfirst($inv['status'] ?? 'draft')
                    };
                    $fields[] = [
                        'icon' => 'receipt',
                        'label' => $inv['invoice_no'],
                        'value' => number_format($inv['total'] ?? 0, 0, ',', '.') . ' ' . ($inv['currency_code'] ?? 'IDR') . ' — ' . $statusBadge
                    ];
                }
                $stmt->close();

                return [
                    'success' => true,
                    'title' => 'Biaya Admin',
                    'fields' => $fields,
                    'empty_message' => empty($fields) ? 'Belum ada invoice terkait' : null,
                    'link' => empty($fields) ? ['url' => BASE_URL . 'finance/invoices', 'label' => 'Buat Invoice →'] : null
                ];

            case 'pembayaran':
                $stmt = $this->db->prepare("
                    SELECT fp.* FROM finance_payments fp
                    WHERE fp.reference_id IN (
                        SELECT fi.id FROM finance_invoices fi 
                        WHERE fi.vessel_id IN (SELECT vessel_id FROM contracts WHERE crew_id = ?)
                    ) ORDER BY fp.payment_date DESC LIMIT 5
                ");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $result = $stmt->get_result();
                $fields = [];
                while ($pay = $result->fetch_assoc()) {
                    $method = match($pay['payment_method'] ?? '') {
                        'bank_transfer' => '🏦 Bank Transfer',
                        'cash' => '💵 Cash',
                        'check' => '📝 Check',
                        default => ucfirst($pay['payment_method'] ?? '-')
                    };
                    $fields[] = [
                        'icon' => 'payments',
                        'label' => $pay['payment_no'] ?? 'Payment',
                        'value' => number_format($pay['amount'] ?? 0, 0, ',', '.') . ' — ' . $method . ' (' . date('d M Y', strtotime($pay['payment_date'])) . ')'
                    ];
                }
                $stmt->close();

                return [
                    'success' => true,
                    'title' => 'Pembayaran',
                    'fields' => $fields,
                    'empty_message' => empty($fields) ? 'Belum ada pembayaran tercatat' : null
                ];

            case 'kwitansi_invoice':
                $stmt = $this->db->prepare("
                    SELECT fi.id, fi.invoice_no, fi.invoice_date, fi.subtotal, fi.tax_amount, fi.total, fi.status
                    FROM finance_invoices fi
                    WHERE fi.vessel_id IN (SELECT vessel_id FROM contracts WHERE crew_id = ?)
                    ORDER BY fi.created_at DESC LIMIT 1
                ");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $inv = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$inv) {
                    return ['success' => true, 'title' => 'Kwitansi / Invoice', 'fields' => [], 'empty_message' => 'Belum ada invoice'];
                }

                // Get items
                $itemStmt = $this->db->prepare("SELECT * FROM finance_invoice_items WHERE invoice_id = ? ORDER BY sort_order");
                $itemStmt->bind_param('i', $inv['id']);
                $itemStmt->execute();
                $itemResult = $itemStmt->get_result();
                $fields = [
                    ['icon' => 'pin', 'label' => 'Invoice No', 'value' => $inv['invoice_no']],
                    ['icon' => 'event', 'label' => 'Tanggal', 'value' => date('d M Y', strtotime($inv['invoice_date']))],
                ];
                while ($item = $itemResult->fetch_assoc()) {
                    $fields[] = ['icon' => 'shopping_cart', 'label' => $item['description'], 'value' => number_format($item['amount'] ?? 0, 0, ',', '.')];
                }
                $fields[] = ['icon' => 'calculate', 'label' => 'Subtotal', 'value' => number_format($inv['subtotal'] ?? 0, 0, ',', '.')];
                $fields[] = ['icon' => 'percent', 'label' => 'Pajak', 'value' => number_format($inv['tax_amount'] ?? 0, 0, ',', '.')];
                $fields[] = ['icon' => 'payments', 'label' => 'Total', 'value' => number_format($inv['total'] ?? 0, 0, ',', '.'), 'highlight' => true];
                $itemStmt->close();

                return ['success' => true, 'title' => 'Detail Invoice', 'fields' => $fields];

            case 'potongan':
                if (!$contractId) {
                    return ['success' => true, 'title' => 'Potongan Gaji', 'fields' => [], 'empty_message' => 'Belum ada kontrak'];
                }
                $stmt = $this->db->prepare("SELECT * FROM contract_deductions WHERE contract_id = ? AND is_active = 1 ORDER BY created_at DESC");
                $stmt->bind_param('i', $contractId);
                $stmt->execute();
                $result = $stmt->get_result();
                $fields = [];
                while ($ded = $result->fetch_assoc()) {
                    $typeLabel = match($ded['deduction_type'] ?? '') {
                        'insurance' => '🛡️ Asuransi',
                        'medical' => '🏥 Medical',
                        'training' => '📚 Training',
                        'advance' => '💰 Advance',
                        'loan' => '🏦 Pinjaman',
                        default => '📋 ' . ucfirst($ded['deduction_type'] ?? 'other')
                    };
                    $recurring = ($ded['is_recurring'] ?? 0) ? " (recurring {$ded['recurring_months']}x)" : ' (one-time)';
                    $fields[] = [
                        'icon' => 'remove_circle',
                        'label' => $ded['description'] ?: $typeLabel,
                        'value' => number_format($ded['amount'] ?? 0, 0, ',', '.') . $recurring
                    ];
                }
                $stmt->close();

                return [
                    'success' => true,
                    'title' => 'Potongan Gaji',
                    'fields' => $fields,
                    'empty_message' => empty($fields) ? 'Tidak ada potongan aktif' : null
                ];

            default:
                return ['success' => false, 'message' => 'Sub-item tidak valid'];
        }
    }

    // ── OK TO BOARD sub-items ─────────────────────────
    private function getOkToBoardData($crewId, $subItem)
    {
        switch ($subItem) {
            case 'dokumen_lengkap':
                // Count mandatory doc types vs uploaded
                $mandatoryStmt = $this->db->query("SELECT code, name FROM document_types WHERE is_mandatory = 1 AND is_active = 1");
                $mandatoryDocs = [];
                while ($row = $mandatoryStmt->fetch_assoc()) $mandatoryDocs[$row['code']] = $row['name'];

                $uploadStmt = $this->db->prepare("SELECT document_type, status FROM crew_documents WHERE crew_id = ?");
                $uploadStmt->bind_param('i', $crewId);
                $uploadStmt->execute();
                $uploadResult = $uploadStmt->get_result();
                $uploaded = [];
                while ($row = $uploadResult->fetch_assoc()) $uploaded[$row['document_type']] = $row['status'];
                $uploadStmt->close();

                $fields = [];
                foreach ($mandatoryDocs as $code => $name) {
                    if (isset($uploaded[$code])) {
                        $st = $uploaded[$code] === 'valid' ? '✅' : ($uploaded[$code] === 'expired' ? '❌' : '⚠️');
                        $fields[] = ['icon' => 'check_circle', 'label' => $name, 'value' => $st . ' ' . ucfirst($uploaded[$code])];
                    } else {
                        $fields[] = ['icon' => 'cancel', 'label' => $name, 'value' => '❌ Belum upload'];
                    }
                }

                $done = count(array_intersect_key($uploaded, $mandatoryDocs));
                $total = count($mandatoryDocs);

                return [
                    'success' => true,
                    'title' => "Dokumen Lengkap ({$done}/{$total})",
                    'fields' => $fields,
                    'empty_message' => $done == 0 ? 'Belum ada dokumen yang di-upload' : null
                ];

            case 'interview_approved':
                $stmt = $this->db->prepare("SELECT owner_interview, owner_interview_notes, owner_interview_at FROM admin_checklists WHERE crew_id = ?");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $cl = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                $status = match(intval($cl['owner_interview'] ?? 0)) { 1 => '✅ PASSED', 2 => '❌ REJECTED', default => '⏳ PENDING' };
                return [
                    'success' => true,
                    'title' => 'Interview',
                    'fields' => [
                        ['icon' => 'how_to_reg', 'label' => 'Status', 'value' => $status],
                        ['icon' => 'schedule', 'label' => 'Waktu', 'value' => $cl['owner_interview_at'] ? date('d M Y H:i', strtotime($cl['owner_interview_at'])) : '-'],
                        ['icon' => 'notes', 'label' => 'Catatan', 'value' => $cl['owner_interview_notes'] ?: '-'],
                    ]
                ];

            case 'medical_fit':
                $stmt = $this->db->prepare("SELECT pengantar_mcu, pengantar_mcu_notes, pengantar_mcu_at FROM admin_checklists WHERE crew_id = ?");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $cl = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                // Also check medical cert
                $medStmt = $this->db->prepare("SELECT expiry_date, status FROM crew_documents WHERE crew_id = ? AND document_type = 'MEDICAL' LIMIT 1");
                $medStmt->bind_param('i', $crewId);
                $medStmt->execute();
                $med = $medStmt->get_result()->fetch_assoc();
                $medStmt->close();

                return [
                    'success' => true,
                    'title' => 'Medical Fitness',
                    'fields' => [
                        ['icon' => 'health_and_safety', 'label' => 'Status MCU', 'value' => ($cl['pengantar_mcu'] ?? 0) == 1 ? '✅ PASSED' : '⏳ PENDING'],
                        ['icon' => 'event_busy', 'label' => 'Medical Cert Expiry', 'value' => $med ? date('d M Y', strtotime($med['expiry_date'])) . ' (' . ucfirst($med['status']) . ')' : 'Tidak ada'],
                        ['icon' => 'notes', 'label' => 'Catatan', 'value' => $cl['pengantar_mcu_notes'] ?: '-'],
                    ]
                ];

            case 'kontrak_signed':
                $stmt = $this->db->prepare("
                    SELECT ct.contract_no, ct.status, ct.sign_on_date, v.name as vessel_name 
                    FROM contracts ct LEFT JOIN vessels v ON ct.vessel_id = v.id
                    WHERE ct.crew_id = ? ORDER BY ct.created_at DESC LIMIT 1
                ");
                $stmt->bind_param('i', $crewId);
                $stmt->execute();
                $ct = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$ct) {
                    return ['success' => true, 'title' => 'Kontrak', 'fields' => [], 'empty_message' => 'Belum ada kontrak',
                        'link' => ['url' => BASE_URL . 'Contract/create?crew_id=' . $crewId, 'label' => 'Buat Kontrak →']];
                }

                return [
                    'success' => true,
                    'title' => 'Kontrak',
                    'fields' => [
                        ['icon' => 'pin', 'label' => 'No. Kontrak', 'value' => $ct['contract_no']],
                        ['icon' => 'info', 'label' => 'Status', 'value' => strtoupper($ct['status'] ?? '-')],
                        ['icon' => 'directions_boat', 'label' => 'Vessel', 'value' => $ct['vessel_name'] ?: '-'],
                        ['icon' => 'login', 'label' => 'Sign On', 'value' => $ct['sign_on_date'] ? date('d M Y', strtotime($ct['sign_on_date'])) : '-'],
                    ]
                ];

            case 'ready_deploy':
                // Aggregate all statuses
                $clStmt = $this->db->prepare("SELECT * FROM admin_checklists WHERE crew_id = ?");
                $clStmt->bind_param('i', $crewId);
                $clStmt->execute();
                $cl = $clStmt->get_result()->fetch_assoc();
                $clStmt->close();

                $ctStmt = $this->db->prepare("SELECT status FROM contracts WHERE crew_id = ? ORDER BY created_at DESC LIMIT 1");
                $ctStmt->bind_param('i', $crewId);
                $ctStmt->execute();
                $ct = $ctStmt->get_result()->fetch_assoc();
                $ctStmt->close();

                $items = [
                    ['label' => 'Document Check', 'ok' => ($cl['document_check'] ?? 0) == 1],
                    ['label' => 'Owner Interview', 'ok' => ($cl['owner_interview'] ?? 0) == 1],
                    ['label' => 'Pengantar MCU', 'ok' => ($cl['pengantar_mcu'] ?? 0) == 1],
                    ['label' => 'Agreement Kontrak', 'ok' => ($cl['agreement_kontrak'] ?? 0) == 1],
                    ['label' => 'Admin Charge', 'ok' => ($cl['admin_charge'] ?? 0) == 1],
                    ['label' => 'OK to Board', 'ok' => ($cl['ok_to_board'] ?? 0) == 1],
                    ['label' => 'Kontrak Aktif', 'ok' => in_array($ct['status'] ?? '', ['active', 'onboard', 'pending_approval'])],
                ];

                $fields = [];
                $allOk = true;
                foreach ($items as $it) {
                    $fields[] = ['icon' => $it['ok'] ? 'check_circle' : 'cancel', 'label' => $it['label'], 'value' => $it['ok'] ? '✅ Ready' : '❌ Not Ready'];
                    if (!$it['ok']) $allOk = false;
                }

                $fields[] = ['icon' => $allOk ? 'rocket_launch' : 'pending', 'label' => 'Overall', 'value' => $allOk ? '🚀 SIAP DIBERANGKATKAN' : '⏳ Belum lengkap', 'highlight' => true];

                return ['success' => true, 'title' => 'Readiness Check', 'fields' => $fields];

            default:
                return ['success' => false, 'message' => 'Sub-item tidak valid'];
        }
    }

    /**
     * Clean all test data from ERP and Recruitment databases
     * Access via: /erp/admin-checklist/cleanup-test-data
     * Uses the app's own DB connection (guaranteed to work)
     */
    public function cleanupTestData()
    {
        $this->requireAuth();
        header('Content-Type: text/html; charset=utf-8');
        echo "<h1>🧹 Database Cleanup</h1><pre>";

        // ERP cleanup
        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        $erpTables = [
            'payroll_items', 'payroll_periods',
            'contract_deductions', 'contract_salaries', 'contract_taxes',
            'contract_approvals', 'contract_logs', 'contract_documents', 'contracts',
            'crew_skills', 'crew_experiences', 'crew_documents', 'crews',
            'admin_checklists', 'crew_operationals',
            'finance_invoice_items', 'finance_invoices', 'finance_payments',
            'finance_journal_entries', 'finance_journal_items',
            'activity_logs', 'notifications', 'recruitment_sync'
        ];

        foreach ($erpTables as $table) {
            $r = @$this->db->query("TRUNCATE TABLE `$table`");
            if ($r) { echo "  ✅ ERP: $table\n"; }
            else {
                $r2 = @$this->db->query("DELETE FROM `$table`");
                echo ($r2 ? "  ✅ ERP: $table (delete)\n" : "  ⚠️ ERP: $table — " . $this->db->error . "\n");
            }
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        echo "\n--- ERP Verify ---\n";
        foreach (['crews','contracts','payroll_items','admin_checklists'] as $t) {
            $r = @$this->db->query("SELECT COUNT(*) as c FROM `$t`");
            echo "  $t: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
        }

        // Recruitment cleanup
        echo "\n━━━━━━━━━━━━━━━━━━\n\n";
        if ($this->recruitmentDb && !$this->recruitmentDb->connect_error) {
            echo "✅ Connected to Recruitment DB\n\n";
            $this->recruitmentDb->query("SET FOREIGN_KEY_CHECKS = 0");

            @$this->recruitmentDb->query("DELETE FROM applicant_profiles WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
            @$this->recruitmentDb->query("DELETE FROM documents WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");
            @$this->recruitmentDb->query("DELETE FROM notifications WHERE user_id IN (SELECT id FROM users WHERE role_id = 3)");

            $recTables = [
                'application_assignments', 'application_status_history',
                'pipeline_requests', 'status_change_requests', 'job_claim_requests',
                'medical_checkups', 'interview_answers', 'interview_sessions',
                'archived_applications', 'applicant_documents', 'email_logs',
                'applications'
            ];

            foreach ($recTables as $table) {
                $r = @$this->recruitmentDb->query("TRUNCATE TABLE `$table`");
                if ($r) { echo "  ✅ REC: $table\n"; }
                else {
                    $r2 = @$this->recruitmentDb->query("DELETE FROM `$table`");
                    echo ($r2 ? "  ✅ REC: $table (delete)\n" : "  ⚠️ REC: $table\n");
                }
            }

            @$this->recruitmentDb->query("DELETE FROM users WHERE role_id = 3");
            echo "  ✅ Deleted " . $this->recruitmentDb->affected_rows . " applicant users\n";

            $this->recruitmentDb->query("SET FOREIGN_KEY_CHECKS = 1");

            echo "\n--- Recruitment Verify ---\n";
            $r = @$this->recruitmentDb->query("SELECT COUNT(*) as c FROM applications");
            echo "  applications: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
            $r = @$this->recruitmentDb->query("SELECT COUNT(*) as c FROM users WHERE role_id = 3");
            echo "  applicants: " . ($r ? $r->fetch_assoc()['c'] : '?') . "\n";
        } else {
            echo "⚠️ Recruitment DB not connected\n";
        }

        echo "\n</pre><h2 style='color:green'>🎉 Cleanup Selesai!</h2>";
        echo "<p><a href='" . BASE_URL . "contracts'>→ Contracts</a> | ";
        echo "<a href='" . BASE_URL . "crews'>→ Crews</a> | ";
        echo "<a href='" . BASE_URL . "payroll'>→ Payroll</a> | ";
        echo "<a href='" . BASE_URL . "recruitment/pipeline'>→ Pipeline</a></p>";
        exit;
    }
}
