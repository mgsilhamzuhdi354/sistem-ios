<?php
/**
 * PT Indo Ocean - ERP System
 * Operational Controller (Stage 3 of Recruitment Flow)
 * Manages hotel, transport, ticket, airport details for crew deployment
 */

namespace App\Controllers;

class Operational extends BaseController
{
    /**
     * Ensure operational tables exist
     */
    private function ensureTable()
    {
        $result = $this->db->query("SHOW TABLES LIKE 'crew_operationals'");
        if ($result && $result->num_rows == 0) {
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
     * List all crew in operational stage
     */
    public function index()
    {
        $this->requireAuth();
        $this->ensureTable();

        $query = "
            SELECT 
                co.id,
                co.crew_id,
                co.hotel_name,
                co.hotel_checkout_date,
                co.transport_to_airport,
                co.ticket_booking_code,
                co.airport_depart,
                co.airport_arrival,
                co.status as op_status,
                co.completed_at,
                co.created_at,
                c.employee_id,
                c.full_name,
                c.email,
                c.phone,
                c.photo,
                c.status as crew_status,
                r.name as rank_name
            FROM crew_operationals co
            JOIN crews c ON co.crew_id = c.id
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            ORDER BY co.created_at DESC
        ";
        $result = $this->db->query($query);
        $operationals = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Calculate completeness
                $fields = ['hotel_name', 'hotel_checkout_date', 'transport_to_airport', 'ticket_booking_code', 'airport_depart', 'airport_arrival'];
                $filled = 0;
                foreach ($fields as $f) {
                    if (!empty($row[$f])) $filled++;
                }
                $row['filled'] = $filled;
                $row['filled_total'] = count($fields);
                $row['filled_percent'] = round(($filled / count($fields)) * 100);
                $operationals[] = $row;
            }
        }

        $stats = [
            'total' => count($operationals),
            'pending' => count(array_filter($operationals, fn($o) => $o['op_status'] === 'pending')),
            'completed' => count(array_filter($operationals, fn($o) => $o['op_status'] === 'completed')),
        ];

        $data = [
            'title' => 'Operational',
            'currentPage' => 'operational',
            'operationals' => $operationals,
            'stats' => $stats,
            'flash' => $this->getFlash()
        ];

        return $this->view('operational/index_modern', $data);
    }

    /**
     * View/Edit operational detail for a specific crew
     */
    public function detail($crewId = null)
    {
        $this->requireAuth();
        $this->ensureTable();

        if (!$crewId) {
            $this->setFlash('error', 'ID crew tidak valid');
            $this->redirect('Operational');
            return;
        }

        // Get operational data
        $stmt = $this->db->prepare("
            SELECT co.*, c.employee_id, c.full_name, c.email, c.phone, c.photo,
                   c.gender, c.birth_date, c.nationality, c.address, c.city,
                   r.name as rank_name
            FROM crew_operationals co
            JOIN crews c ON co.crew_id = c.id
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE co.crew_id = ?
        ");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $operational = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$operational) {
            $this->setFlash('error', 'Data operational tidak ditemukan. Pastikan Admin Checklist sudah selesai.');
            $this->redirect('Operational');
            return;
        }

        $data = [
            'title' => 'Operational - ' . $operational['full_name'],
            'currentPage' => 'operational',
            'op' => $operational,
            'flash' => $this->getFlash()
        ];

        return $this->view('operational/view_modern', $data);
    }

    /**
     * Update operational data
     */
    public function update($crewId = null)
    {
        $this->requireAuth();
        $this->ensureTable();

        if (!$crewId || !$this->isPost()) {
            $this->setFlash('error', 'Request tidak valid');
            $this->redirect('Operational');
            return;
        }

        $hotelName = trim($this->input('hotel_name', ''));
        $hotelCheckout = trim($this->input('hotel_checkout_date', ''));
        $transport = trim($this->input('transport_to_airport', ''));
        $ticketCode = trim($this->input('ticket_booking_code', ''));
        $airportDepart = trim($this->input('airport_depart', ''));
        $airportArrival = trim($this->input('airport_arrival', ''));
        $notes = trim($this->input('notes', ''));

        $stmt = $this->db->prepare("
            UPDATE crew_operationals SET 
                hotel_name = ?,
                hotel_checkout_date = ?,
                transport_to_airport = ?,
                ticket_booking_code = ?,
                airport_depart = ?,
                airport_arrival = ?,
                notes = ?,
                updated_at = NOW()
            WHERE crew_id = ?
        ");

        $checkoutDate = $hotelCheckout ?: null;
        $stmt->bind_param('sssssssi', $hotelName, $checkoutDate, $transport, $ticketCode, $airportDepart, $airportArrival, $notes, $crewId);

        if ($stmt->execute()) {
            // Check if user also wants to complete (Selesai → On Board)
            $action = trim($this->input('_action', ''));
            if ($action === 'complete') {
                $stmt->close();
                // Validate required fields from the form data we just saved
                if (empty($hotelName) || empty($airportDepart) || empty($airportArrival)) {
                    $this->setFlash('error', 'Isi minimal: Hotel, Airport Depart, dan Airport Arrival');
                    $this->redirect('Operational/detail/' . $crewId);
                    return;
                }
                // Run complete logic
                $this->doComplete($crewId);
                return;
            }
            $this->setFlash('success', '✅ Data operational berhasil disimpan!');
        } else {
            $this->setFlash('error', 'Gagal menyimpan data operational');
        }
        $stmt->close();

        $this->redirect('Operational/detail/' . $crewId);
    }

    /**
     * Internal: complete operational after data is saved
     */
    private function doComplete($crewId)
    {
        $this->db->begin_transaction();
        try {
            $updateStmt = $this->db->prepare("
                UPDATE crew_operationals SET status = 'completed', completed_at = NOW() WHERE crew_id = ?
            ");
            $updateStmt->bind_param('i', $crewId);
            $updateStmt->execute();
            $updateStmt->close();

            // Update crew status to on_board
            $crewStmt = $this->db->prepare("UPDATE crews SET status = 'on_board', updated_at = NOW() WHERE id = ?");
            $crewStmt->bind_param('i', $crewId);
            $crewStmt->execute();
            $crewStmt->close();

            // AUTO-CREATE CONTRACT if none exists
            $contractCreated = $this->autoCreateContract($crewId);

            $this->db->commit();

            // Sync On Board status to recruitment DB
            $this->syncToRecruitmentDb($crewId, 'on_board');

            $msg = '✅ Operational selesai! Crew sudah On Board.';
            if ($contractCreated) {
                $msg .= ' 📄 Draft kontrak otomatis dibuat.';
            }
            $this->setFlash('success', $msg);
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->setFlash('error', 'Gagal: ' . $e->getMessage());
        }

        $this->redirect('Operational');
    }

    /**
     * Auto-create a draft contract for a crew member
     * Called when Operational stage completes
     */
    private function autoCreateContract($crewId)
    {
        // Check if crew already has a contract
        $checkStmt = $this->db->prepare("SELECT id FROM contracts WHERE crew_id = ? LIMIT 1");
        $checkStmt->bind_param('i', $crewId);
        $checkStmt->execute();
        $existing = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($existing) {
            return false; // Already has contract
        }

        // Get crew data
        $crewStmt = $this->db->prepare("
            SELECT c.*, r.name as rank_name 
            FROM crews c 
            LEFT JOIN ranks r ON c.current_rank_id = r.id 
            WHERE c.id = ?
        ");
        $crewStmt->bind_param('i', $crewId);
        $crewStmt->execute();
        $crew = $crewStmt->get_result()->fetch_assoc();
        $crewStmt->close();

        if (!$crew) return false;

        // Generate contract number
        $year = date('Y');
        $countResult = $this->db->query("SELECT COUNT(*) as c FROM contracts WHERE contract_number LIKE 'CTR-{$year}-%'");
        $count = $countResult ? $countResult->fetch_assoc()['c'] : 0;
        $contractNumber = sprintf("CTR-%s-%04d", $year, $count + 1);

        // Get first available vessel (if any)
        $vesselResult = $this->db->query("SELECT id FROM vessels WHERE status = 'active' LIMIT 1");
        $vesselId = $vesselResult ? ($vesselResult->fetch_assoc()['id'] ?? null) : null;

        // Get first available client (if any)
        $clientResult = $this->db->query("SELECT id FROM clients LIMIT 1");
        $clientId = $clientResult ? ($clientResult->fetch_assoc()['id'] ?? null) : null;

        // Get default currency (IDR = 2, or first available)
        $currResult = $this->db->query("SELECT id FROM currencies WHERE code = 'IDR' LIMIT 1");
        $currencyId = $currResult ? ($currResult->fetch_assoc()['id'] ?? 1) : 1;

        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 1;
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+12 months'));
        $rankId = $crew['current_rank_id'] ?? 1;

        // Insert draft contract
        $insertStmt = $this->db->prepare("
            INSERT INTO contracts (
                contract_number, crew_id, vessel_id, client_id,
                rank_id, contract_type, start_date, end_date,
                currency_id, status, created_by, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, 'fixed_term', ?, ?, ?, 'draft', ?, NOW(), NOW())
        ");
        $insertStmt->bind_param('siiiissii',
            $contractNumber, $crewId, $vesselId, $clientId,
            $rankId, $startDate, $endDate, $currencyId, $userId
        );
        $insertStmt->execute();
        $contractId = $this->db->insert_id;
        $insertStmt->close();

        if ($contractId) {
            // Insert default salary entry (0 — to be filled by admin)
            $salStmt = $this->db->prepare("
                INSERT INTO contract_salaries (contract_id, component, amount, currency_id)
                VALUES (?, 'basic_salary', 0, ?)
            ");
            $salStmt->bind_param('ii', $contractId, $currencyId);
            $salStmt->execute();
            $salStmt->close();

            // Log
            @$this->db->query("INSERT INTO contract_logs (contract_id, action, description, created_by, created_at) 
                VALUES ({$contractId}, 'created', 'Auto-created from Operational completion for {$crew['full_name']}', {$userId}, NOW())");

            // Update crew status to contracted
            $updCrew = $this->db->prepare("UPDATE crews SET status = 'contracted', updated_at = NOW() WHERE id = ?");
            $updCrew->bind_param('i', $crewId);
            $updCrew->execute();
            $updCrew->close();
        }

        return $contractId > 0;
    }

    /**
     * Mark operational as completed
     */
    public function complete($crewId = null)
    {
        $this->requireAuth();
        $this->ensureTable();

        if (!$crewId) {
            $this->setFlash('error', 'ID crew tidak valid');
            $this->redirect('Operational');
            return;
        }

        // Verify minimum fields filled
        $stmt = $this->db->prepare("SELECT * FROM crew_operationals WHERE crew_id = ?");
        $stmt->bind_param('i', $crewId);
        $stmt->execute();
        $op = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$op) {
            $this->setFlash('error', 'Data operational tidak ditemukan');
            $this->redirect('Operational');
            return;
        }

        $required = ['hotel_name', 'airport_depart', 'airport_arrival'];
        foreach ($required as $field) {
            if (empty($op[$field])) {
                $this->setFlash('error', 'Isi minimal: Hotel, Airport Depart, dan Airport Arrival');
                $this->redirect('Operational/detail/' . $crewId);
                return;
            }
        }

        $this->db->begin_transaction();
        try {
            $updateStmt = $this->db->prepare("
                UPDATE crew_operationals SET status = 'completed', completed_at = NOW() WHERE crew_id = ?
            ");
            $updateStmt->bind_param('i', $crewId);
            $updateStmt->execute();
            $updateStmt->close();

            // Update crew status to on_board
            $crewStmt = $this->db->prepare("UPDATE crews SET status = 'on_board', updated_at = NOW() WHERE id = ?");
            $crewStmt->bind_param('i', $crewId);
            $crewStmt->execute();
            $crewStmt->close();

            $this->db->commit();

            // Sync On Board status to recruitment DB
            $this->syncToRecruitmentDb($crewId, 'on_board');

            $this->setFlash('success', '✅ Operational selesai! Crew sudah On Board.');
        } catch (\Exception $e) {
            $this->db->rollback();
            $this->setFlash('error', 'Gagal: ' . $e->getMessage());
        }

        $this->redirect('Operational');
    }

    /**
     * Return crew from Operational back to Admin Checklist (Stage 2)
     * Resets operational status and crew status to pending_checklist
     */
    public function returnToChecklist($crewId = null)
    {
        $this->requireAuth();
        header('Content-Type: application/json');

        if (!$crewId) {
            echo json_encode(['success' => false, 'message' => 'ID crew tidak valid']);
            return;
        }

        $reason = $_POST['reason'] ?? 'Dikembalikan ke Admin Checklist untuk diproses ulang';
        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 1;

        try {
            $this->db->begin_transaction();

            // 1. Reset crew status to pending_checklist
            $stmt = $this->db->prepare("UPDATE crews SET status = 'pending_checklist', updated_at = NOW() WHERE id = ?");
            $stmt->bind_param('i', $crewId);
            $stmt->execute();
            $stmt->close();

            // 2. Reset admin_checklist status to in_progress (NOT completed)
            $clStmt = $this->db->prepare("
                UPDATE admin_checklists SET status = 'in_progress', completed_at = NULL, updated_at = NOW() WHERE crew_id = ?
            ");
            $clStmt->bind_param('i', $crewId);
            $clStmt->execute();
            $clStmt->close();

            // 3. DELETE operational record so it disappears from Operational list
            $opStmt = $this->db->prepare("DELETE FROM crew_operationals WHERE crew_id = ?");
            $opStmt->bind_param('i', $crewId);
            $opStmt->execute();
            $opStmt->close();

            // 4. Add notes to crew
            $notesStmt = $this->db->prepare("
                UPDATE crews SET notes = CONCAT(IFNULL(notes,''), '\n[RETURNED] ', ?) WHERE id = ?
            ");
            $notesStmt->bind_param('si', $reason, $crewId);
            $notesStmt->execute();
            $notesStmt->close();

            $this->db->commit();

            // 5. Sync to recruitment DB (Processing status)
            $this->syncToRecruitmentDb($crewId, 'processing');

            // 6. Get crew name for message
            $nameResult = $this->db->query("SELECT full_name FROM crews WHERE id = " . intval($crewId));
            $crewName = $nameResult ? ($nameResult->fetch_assoc()['full_name'] ?? 'Crew') : 'Crew';

            error_log("Returned crew to Admin Checklist: {$crewName} (ID: {$crewId}). Reason: {$reason}");

            echo json_encode([
                'success' => true,
                'message' => "✅ {$crewName} berhasil dikembalikan ke Admin Checklist.",
                'redirect_url' => BASE_URL . 'AdminChecklist/detail/' . $crewId
            ]);
        } catch (\Exception $e) {
            $this->db->rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Sync status to recruitment DB (centralized helper)
     */
    private function syncToRecruitmentDb($crewId, $action)
    {
        try {
            if (!$this->recruitmentDb || $this->recruitmentDb->connect_error) {
                // Fallback to direct connection
                $recruitDb = new \mysqli('localhost', 'root', '', 'recruitment_db');
                if ($recruitDb->connect_error) return;
                $useDirectConn = true;
            } else {
                $recruitDb = $this->recruitmentDb;
                $useDirectConn = false;
            }

            $statusMap = [
                'rejected'   => 7,   // Rejected
                'approved'   => 6,   // Approved
                'processing' => 10,  // Processing (checklist in progress)
                'on_board'   => 11,  // On Board
            ];
            $newStatusId = $statusMap[$action] ?? null;
            if (!$newStatusId) {
                if ($useDirectConn) $recruitDb->close();
                return;
            }

            $stmt = $recruitDb->prepare("UPDATE applications SET status_id = ?, status_updated_at = NOW(), updated_at = NOW() WHERE erp_crew_id = ?");
            if ($stmt) {
                $stmt->bind_param('ii', $newStatusId, $crewId);
                $stmt->execute();
                $stmt->close();
            }
            if ($useDirectConn) $recruitDb->close();
        } catch (\Throwable $e) {
            error_log('Operational syncToRecruitmentDb error: ' . $e->getMessage());
        }
    }
}
