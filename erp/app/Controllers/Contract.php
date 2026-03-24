<?php
/**
 * PT Indo Ocean - ERP System
 * Contract Controller - Handles all 15 features
 */

namespace App\Controllers;

require_once APPPATH . 'Models/ContractModel.php';
require_once APPPATH . 'Models/VesselModel.php';
require_once APPPATH . 'Models/ClientModel.php';
require_once APPPATH . 'Models/RankModel.php';
require_once APPPATH . 'Models/CrewModel.php';
require_once APPPATH . 'Models/SettingsModel.php';

use App\Models\ContractModel;
use App\Models\ContractSalaryModel;
use App\Models\ContractTaxModel;
use App\Models\ContractDeductionModel;
use App\Models\ContractApprovalModel;
use App\Models\ContractLogModel;
use App\Models\VesselModel;
use App\Models\VesselTypeModel;
use App\Models\FlagStateModel;
use App\Models\RankModel;
use App\Models\CurrencyModel;
use App\Models\ClientModel;
use App\Models\CrewModel;

class Contract extends BaseController
{
    private $contractModel;

    public function __construct()
    {
        parent::__construct();
        $this->contractModel = new ContractModel($this->db);
    }

    /**
     * Feature 1: Contract List with filters (with dual-mode support)
     */
    public function index()
    {
        $this->requirePermission('contracts', 'view');
        // Check UI mode from session (classic or modern)
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';

        $page = (int) $this->input('page', 1);
        $filters = [
            'status' => $this->input('status'),
            'vessel_id' => $this->input('vessel_id'),
            'client_id' => $this->input('client_id'),
            'search' => $this->input('search'),
        ];

        $vesselModel = new VesselModel($this->db);
        $clientModel = new ClientModel($this->db);

        $data = [
            'title' => 'Contract Management',
            'contracts' => $this->contractModel->getList($filters, $page),
            'total' => $this->contractModel->countList($filters),
            'page' => $page,
            'perPage' => ITEMS_PER_PAGE,
            'filters' => $filters,
            'vessels' => $vesselModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
            'statuses' => CONTRACT_STATUSES,
            'flash' => $this->getFlash(),
            'uiMode' => $uiMode
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'contracts/modern' : 'contracts/index';
        return $this->view($view, $data);
    }

    /**
     * Feature 1-7: Create Contract Form (with recruitment crew integration)
     */
    public function create()
    {
        $this->requirePermission('contracts', 'create');
        $vesselModel = new VesselModel($this->db);
        $clientModel = new ClientModel($this->db);
        $rankModel = new RankModel($this->db);
        $currencyModel = new CurrencyModel($this->db);
        $crewModel = new CrewModel($this->db);

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';

        // Get all crews for dropdown
        $allCrews = $crewModel->getForDropdown();

        // Get recruitment crews separately (newly approved from recruitment)
        $recruitmentCrews = $this->getRecruitmentCrews();

        // Auto-select crew if crew_id is passed via URL (from recruitment pipeline)
        $preselectedCrewId = $_GET['crew_id'] ?? null;

        $data = [
            'title' => 'Create New Contract',
            'contractNo' => $this->contractModel->generateContractNumber(),
            'vessels' => $vesselModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
            'crews' => $allCrews,
            'recruitmentCrews' => $recruitmentCrews,
            'ranks' => $rankModel->getForDropdown(),
            'currencies' => $currencyModel->getForDropdown(),
            'contractTypes' => CONTRACT_TYPES,
            'taxTypes' => TAX_TYPES,
            'deductionTypes' => DEDUCTION_TYPES,
            'preselectedCrewId' => $preselectedCrewId,
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'contracts/create_modern' : 'contracts/form';
        return $this->view($view, $data);
    }

    /**
     * Server-side salary sanity check.
     * Prevents saving contracts with unreasonable salary amounts for the selected currency.
     * Returns error message string if invalid, or null if OK.
     */
    private function validateSalaryCurrency(float $basicSalary, int $currencyId): ?string
    {
        // Max reasonable monthly salary thresholds per currency (for maritime crew)
        $maxThresholds = [
            1 => ['code' => 'USD', 'max' => 25000],    // US Dollar
            2 => ['code' => 'IDR', 'max' => 100000000], // Indonesian Rupiah
            3 => ['code' => 'SGD', 'max' => 30000],    // Singapore Dollar
            4 => ['code' => 'EUR', 'max' => 25000],    // Euro
            15 => ['code' => 'MYR', 'max' => 50000],   // Malaysian Ringgit
        ];

        // Fallback: lookup currency code from DB if not in static map
        $curInfo = $maxThresholds[$currencyId] ?? null;
        if (!$curInfo) {
            $stmt = $this->db->prepare("SELECT code FROM currencies WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $currencyId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            $curCode = $row['code'] ?? 'UNKNOWN';
            $curInfo = ['code' => $curCode, 'max' => 50000]; // Default threshold
        }

        // Check 1: Salary exceeds reasonable max for this currency
        if ($basicSalary > $curInfo['max']) {
            return "PERINGATAN: Gaji pokok {$curInfo['code']} " . number_format($basicSalary, 0, ',', '.')
                . " melebihi batas wajar ({$curInfo['code']} " . number_format($curInfo['max'], 0, ',', '.') . '/bulan).'
                . " Pastikan mata uang dan nominal sudah benar.";
        }

        // Check 2: Non-IDR currency with suspiciously large amount (looks like IDR was entered)
        if ($curInfo['code'] !== 'IDR' && $basicSalary >= 1000000) {
            return "PERINGATAN: Nominal {$curInfo['code']} " . number_format($basicSalary, 0, ',', '.')
                . " terlihat sangat besar. Mungkin seharusnya dalam IDR?";
        }

        return null; // All good
    }

    /**
     * Helper: Get recruitment crews ready for contract
     */
    private function getRecruitmentCrews()
    {
        // Get crews from recruitment source that are ready for contract
        // (standby/available status, no active contract)
        $query = "
            SELECT 
                c.id, 
                c.employee_id,
                c.full_name, 
                c.email, 
                c.phone,
                c.gender,
                c.birth_date,
                c.nationality,
                c.current_rank_id,
                COALESCE(c.approved_at, c.created_at) as approved_at,
                c.source,
                r.name as rank_name,
                DATEDIFF(NOW(), COALESCE(c.approved_at, c.created_at)) as days_since_approval
            FROM crews c
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE c.source = 'recruitment'
            AND c.status IN ('standby', 'available', 'ready_operational', 'pending_checklist', 'contracted')
            AND c.id NOT IN (
                SELECT crew_id FROM contracts
                WHERE status IN ('active', 'onboard')
            )
            ORDER BY COALESCE(c.approved_at, c.created_at) DESC
            LIMIT 50
        ";

        $result = $this->db->query($query);
        $crews = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $crews[] = $row;
            }
        }

        return $crews;
    }

    /**
     * AJAX: Get full crew detail for contract auto-fill
     */
    public function crewDetail($id)
    {
        $id = (int) $id;
        $query = "
            SELECT 
                c.id, c.employee_id, c.full_name, c.nickname,
                c.gender, c.birth_date, c.birth_place,
                c.nationality, c.religion, c.marital_status,
                c.email, c.phone, c.whatsapp,
                c.address, c.city, c.province, c.postal_code,
                c.bank_name, c.bank_account, c.bank_holder,
                c.emergency_name, c.emergency_relation, c.emergency_phone,
                c.current_rank_id, c.years_experience, c.total_sea_time_months,
                c.photo, c.source,
                r.name as rank_name
            FROM crews c
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE c.id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $crew = $result->fetch_assoc();
        $stmt->close();

        if (!$crew) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Crew not found']);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $crew]);
    }

    /**
     * Store new contract with salary, tax, deductions
     */
    public function store()
    {
        $this->requirePermission('contracts', 'create');
        if (!$this->isPost()) {
            $this->redirect('contracts');
        }

        $crewId = $this->input('crew_id');

        // Validate crew_id exists in crews table
        if (!empty($crewId)) {
            $crewCheck = $this->db->prepare("SELECT id, full_name FROM crews WHERE id = ? LIMIT 1");
            $crewCheck->bind_param('i', $crewId);
            $crewCheck->execute();
            $crewResult = $crewCheck->get_result();
            if (!$crewResult || $crewResult->num_rows === 0) {
                $this->setFlash('error', 'Crew ID tidak ditemukan. Pilih crew dari dropdown.');
                $this->redirect('contracts/create');
                return;
            }
        } else {
            $this->setFlash('error', 'Crew harus dipilih.');
            $this->redirect('contracts/create');
            return;
        }

        // Prevent duplicate: check if crew already has an active/pending/draft contract
        if (!empty($crewId)) {
            $stmt = $this->db->prepare(
                "SELECT id, contract_no, status FROM contracts WHERE crew_id = ? AND status IN ('active', 'pending_approval', 'draft', 'onboard') LIMIT 1"
            );
            $stmt->bind_param('i', $crewId);
            $stmt->execute();
            $dupCheck = $stmt->get_result();
            if ($dupCheck && $dupCheck->num_rows > 0) {
                $existing = $dupCheck->fetch_assoc();
                $this->setFlash('error', 'Crew sudah memiliki kontrak aktif: ' . $existing['contract_no'] . ' (Status: ' . ucfirst(str_replace('_', ' ', $existing['status'])) . ')');
                $this->redirect('contracts/create');
                return;
            }
        }

        // Contract data
        $contractData = [
            'contract_no' => $this->input('contract_no'),
            'crew_id' => $this->input('crew_id'),
            'crew_name' => $this->input('crew_name'),
            'vessel_id' => $this->input('vessel_id'),
            'client_id' => $this->input('client_id'),
            'rank_id' => $this->input('rank_id'),
            'contract_type' => $this->input('contract_type'),
            'status' => $this->input('submit_approval') ? CONTRACT_STATUS_PENDING : CONTRACT_STATUS_DRAFT,
            'sign_on_date' => $this->input('sign_on_date') ?: null,
            'sign_off_date' => $this->input('sign_off_date') ?: null,
            'duration_months' => $this->input('duration_months'),
            'embarkation_port' => $this->input('embarkation_port'),
            'disembarkation_port' => $this->input('disembarkation_port'),
            'notes' => $this->input('notes'),
            'created_by' => $this->getCurrentUser()['id'] ?? null,
        ];

        // Salary data (Feature 4)
        $exchangeRate = str_replace([',', '.'], '', $this->input('exchange_rate', ''));
        $clientRate = str_replace([',', '.'], '', $this->input('client_rate', ''));
        $salaryData = [
            'currency_id' => $this->input('currency_id', 1),
            'exchange_rate' => !empty($exchangeRate) ? floatval($exchangeRate) : null,
            'client_rate' => !empty($clientRate) ? floatval($clientRate) : null,
            'basic_salary' => floatval(str_replace([',', '.'], '', $this->input('basic_salary', 0))),
            'overtime_allowance' => floatval(str_replace([',', '.'], '', $this->input('overtime_allowance', 0))),
            'leave_pay' => floatval(str_replace([',', '.'], '', $this->input('leave_pay', 0))),
            'bonus' => floatval(str_replace([',', '.'], '', $this->input('bonus', 0))),
            'other_allowance' => floatval(str_replace([',', '.'], '', $this->input('other_allowance', 0))),
        ];

        // Validate vessel-client match: vessel must belong to the selected client
        $vesselId = (int) $contractData['vessel_id'];
        $clientId = (int) $contractData['client_id'];
        if ($vesselId > 0 && $clientId > 0) {
            $vesselCheck = $this->db->prepare("SELECT id, name, client_id FROM vessels WHERE id = ? LIMIT 1");
            $vesselCheck->bind_param('i', $vesselId);
            $vesselCheck->execute();
            $vesselRow = $vesselCheck->get_result()->fetch_assoc();
            $vesselCheck->close();
            if ($vesselRow && (int)$vesselRow['client_id'] !== $clientId) {
                // Get the actual owner name for a clear error message
                $ownerStmt = $this->db->prepare("SELECT name FROM clients WHERE id = ? LIMIT 1");
                $ownerStmt->bind_param('i', $vesselRow['client_id']);
                $ownerStmt->execute();
                $ownerRow = $ownerStmt->get_result()->fetch_assoc();
                $ownerStmt->close();
                $ownerName = $ownerRow['name'] ?? 'Unknown';
                $this->setFlash('error', 'Vessel "' . $vesselRow['name'] . '" milik client "' . $ownerName . '", tidak bisa digunakan untuk client lain. Pilih vessel yang sesuai.');
                $this->redirect('contracts/create');
                return;
            }
        }

        // Server-side salary sanity check (prevents currency/amount mismatch)
        $salaryWarning = $this->validateSalaryCurrency(
            $salaryData['basic_salary'],
            (int) $salaryData['currency_id']
        );
        if ($salaryWarning && empty($this->input('force_salary_override'))) {
            $this->setFlash('error', $salaryWarning . ' Jika yakin benar, centang "Override" dan submit ulang.');
            $this->redirect('contracts/create');
            return;
        }

        // Tax data (Feature 5)
        $taxData = [
            'tax_type' => $this->input('tax_type', TAX_TYPE_PPH21),
            'npwp_number' => $this->input('npwp_number'),
            'tax_rate' => TAX_RATES[$this->input('tax_type', TAX_TYPE_PPH21)] ?? 5,
            'effective_from' => $this->input('sign_on_date'),
        ];

        try {
            $contractId = $this->contractModel->createWithDetails($contractData, $salaryData, $taxData);

            // Save deductions (Feature 6)
            if (!empty($_POST['deduction_type'])) {
                $deductionModel = new ContractDeductionModel($this->db);
                foreach ($_POST['deduction_type'] as $i => $type) {
                    $amount = $_POST['deduction_amount'][$i] ?? 0;
                    // Clean amount - remove commas and dots that are thousand separators
                    $amount = str_replace([',', '.'], '', $amount);
                    $amount = floatval($amount);

                    if (!empty($type) && $amount > 0) {
                        $deductionModel->insert([
                            'contract_id' => $contractId,
                            'deduction_type' => $type,
                            'description' => $_POST['deduction_desc'][$i] ?? '',
                            'amount' => $amount,
                            'is_recurring' => ($_POST['deduction_recurring'][$i] ?? 'monthly') === 'monthly' ? 1 : 0,
                        ]);
                    }
                }
            }

            // Create approval workflow (Feature 9)
            if ($contractData['status'] === CONTRACT_STATUS_PENDING) {
                $approvalModel = new ContractApprovalModel($this->db);
                $approvalModel->insert([
                    'contract_id' => $contractId,
                    'approval_level' => APPROVAL_CREWING,
                    'status' => 'pending'
                ]);
            }

            // Log creation (Feature 15)
            $logModel = new ContractLogModel($this->db);
            $logModel->log(
                $contractId,
                'created',
                [],
                $this->getCurrentUser()['id'] ?? null,
                $this->getCurrentUser()['name'] ?? 'System'
            );

            $this->setFlash('success', 'Contract created successfully');

            // Sync crew status to 'contracted' when contract is created
            $syncCrewId = (int) $crewId;
            if ($syncCrewId > 0) {
                $crewUpdStmt = $this->db->prepare("UPDATE crews SET status = 'contracted', updated_at = NOW() WHERE id = ?");
                if ($crewUpdStmt) {
                    $crewUpdStmt->bind_param('i', $syncCrewId);
                    $crewUpdStmt->execute();
                    $crewUpdStmt->close();
                }

                // Sync recruitment pipeline status to 'Processing' (status_id=5)
                $this->syncRecruitmentPipelineStatus($syncCrewId, 5);
            }
            
            // Send notification: New Contract Created (Crew Sign-On)
            try {
                $notifModel = new \App\Models\NotificationModel($this->db);
                $crewName = $contractData['crew_name'] ?? 'Unknown';
                $contractNo = $contractData['contract_no'] ?? '';
                $signOn = $contractData['sign_on_date'] ? date('d M Y', strtotime($contractData['sign_on_date'])) : '-';
                $signOff = $contractData['sign_off_date'] ? date('d M Y', strtotime($contractData['sign_off_date'])) : '-';
                $duration = $contractData['duration_months'] ?? '-';
                // Get vessel & rank names
                $vesselName = '-'; $rankName = '-'; $clientName = '-';
                $infoQ = $this->db->query("SELECT v.name as vessel_name, r.name as rank_name, cl.name as client_name FROM contracts c LEFT JOIN vessels v ON c.vessel_id=v.id LEFT JOIN ranks r ON c.rank_id=r.id LEFT JOIN clients cl ON c.client_id=cl.id WHERE c.id=" . intval($contractId) . " LIMIT 1");
                if ($infoQ && $info = $infoQ->fetch_assoc()) { $vesselName = $info['vessel_name'] ?? '-'; $rankName = $info['rank_name'] ?? '-'; $clientName = $info['client_name'] ?? '-'; }
                $msg = "📢 *KONTRAK BARU DIBUAT*\n━━━━━━━━━━━━━━━━━━\n\n"
                     . "👤 *Crew:* {$crewName}\n🎖️ *Rank:* {$rankName}\n🚢 *Vessel:* {$vesselName}\n🏢 *Client:* {$clientName}\n"
                     . "📋 *No. Kontrak:* {$contractNo}\n📅 *Sign On:* {$signOn}\n📅 *Sign Off:* {$signOff}\n⏱️ *Durasi:* {$duration} bulan\n\n"
                     . "⏰ " . date('d M Y, H:i') . "\n━━━━━━━━━━━━━━━━━━\n— _IndoOcean ERP_ 🌊";
                $notifModel->notify('info', 'Kontrak Baru Dibuat', $msg, 'contracts/' . $contractId);
            } catch (\Exception $e) {
                error_log('Contract notification failed: ' . $e->getMessage());
            }
            
            $this->redirect('contracts/' . $contractId);

        } catch (\Exception $e) {
            $this->setFlash('error', 'Failed to create contract: ' . $e->getMessage());
            $this->redirect('contracts/create');
        }
    }

    /**
     * View contract detail (accessed via /contracts/view/{id})
     */
    public function viewContract($id)
    {
        $this->requirePermission('contracts', 'view');
        $contract = $this->contractModel->getWithDetails($id);
        if (!$contract) {
            $this->setFlash('error', 'Contract not found');
            $this->redirect('contracts');
        }

        $deductionModel = new ContractDeductionModel($this->db);
        $approvalModel = new ContractApprovalModel($this->db);
        $logModel = new ContractLogModel($this->db);

        // Get documents
        $documents = $this->db->query("SELECT * FROM contract_documents WHERE contract_id = $id ORDER BY created_at DESC");
        $docList = [];
        if ($documents) {
            while ($row = $documents->fetch_assoc()) {
                $docList[] = $row;
            }
        }

        $data = [
            'title' => 'Contract Detail - ' . $contract['contract_no'],
            'contract' => $contract,
            'deductions' => $deductionModel->getByContract($id),
            'approvals' => $approvalModel->getByContract($id),
            'logs' => $logModel->getByContract($id, 20),
            'documents' => $docList,
            'daysRemaining' => $this->calculateDaysRemaining($contract['sign_off_date']),
            'flash' => $this->getFlash()
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'contracts/view_modern' : 'contracts/view';
        return $this->view($view, $data);
    }

    /**
     * Alias: /contracts/{id} also shows the contract
     */
    public function show($id)
    {
        return $this->viewContract($id);
    }

    /**
     * Edit contract form
     */
    public function edit($id)
    {
        $this->requirePermission('contracts', 'edit');
        $contract = $this->contractModel->getWithDetails($id);
        if (!$contract) {
            $this->setFlash('error', 'Contract not found');
            $this->redirect('contracts');
        }

        $vesselModel = new VesselModel($this->db);
        $clientModel = new ClientModel($this->db);
        $rankModel = new RankModel($this->db);
        $currencyModel = new CurrencyModel($this->db);
        $deductionModel = new ContractDeductionModel($this->db);
        $crewModel = new CrewModel($this->db);

        $data = [
            'title' => 'Edit Contract - ' . $contract['contract_no'],
            'contract' => $contract,
            'deductions' => $deductionModel->getByContract($id),
            'vessels' => $vesselModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
            'ranks' => $rankModel->getForDropdown(),
            'currencies' => $currencyModel->getForDropdown(),
            'crews' => $crewModel->getForDropdown(),
            'contractTypes' => CONTRACT_TYPES,
            'taxTypes' => TAX_TYPES,
            'deductionTypes' => DEDUCTION_TYPES,
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'contracts/edit_modern' : 'contracts/form';
        return $this->view($view, $data);
    }

    /**
     * Update contract
     */
    public function update($id)
    {
        $this->requirePermission('contracts', 'edit');
        if (!$this->isPost()) {
            $this->redirect('contracts/' . $id);
        }

        $oldContract = $this->contractModel->find($id);

        // Update contract
        $contractData = [
            'vessel_id' => $this->input('vessel_id'),
            'client_id' => $this->input('client_id'),
            'rank_id' => $this->input('rank_id'),
            'contract_type' => $this->input('contract_type'),
            'sign_on_date' => $this->input('sign_on_date') ?: null,
            'sign_off_date' => $this->input('sign_off_date') ?: null,
            'duration_months' => $this->input('duration_months'),
            'notes' => $this->input('notes'),
            'updated_by' => $this->getCurrentUser()['id'] ?? null,
        ];

        // Validate vessel-client match: vessel must belong to the selected client
        $vesselId = (int) $contractData['vessel_id'];
        $clientId = (int) $contractData['client_id'];
        if ($vesselId > 0 && $clientId > 0) {
            $vesselCheck = $this->db->prepare("SELECT id, name, client_id FROM vessels WHERE id = ? LIMIT 1");
            $vesselCheck->bind_param('i', $vesselId);
            $vesselCheck->execute();
            $vesselRow = $vesselCheck->get_result()->fetch_assoc();
            $vesselCheck->close();
            if ($vesselRow && (int)$vesselRow['client_id'] !== $clientId) {
                $ownerStmt = $this->db->prepare("SELECT name FROM clients WHERE id = ? LIMIT 1");
                $ownerStmt->bind_param('i', $vesselRow['client_id']);
                $ownerStmt->execute();
                $ownerRow = $ownerStmt->get_result()->fetch_assoc();
                $ownerStmt->close();
                $ownerName = $ownerRow['name'] ?? 'Unknown';
                $this->setFlash('error', 'Vessel "' . $vesselRow['name'] . '" milik client "' . $ownerName . '", tidak bisa digunakan untuk client lain. Pilih vessel yang sesuai.');
                $this->redirect('contracts/edit/' . $id);
                return;
            }
        }

        $this->contractModel->update($id, $contractData);

        // Update salary
        $salaryModel = new ContractSalaryModel($this->db);
        $existingSalary = $salaryModel->getByContract($id);
        $exchangeRate = str_replace([',', '.'], '', $this->input('exchange_rate', ''));
        $clientRate = str_replace([',', '.'], '', $this->input('client_rate', ''));
        $salaryData = [
            'contract_id' => $id,
            'currency_id' => $this->input('currency_id', 1),
            'exchange_rate' => !empty($exchangeRate) ? floatval($exchangeRate) : null,
            'client_rate' => !empty($clientRate) ? floatval($clientRate) : null,
            'basic_salary' => floatval(str_replace([',', '.'], '', $this->input('basic_salary', 0))),
            'overtime_allowance' => floatval(str_replace([',', '.'], '', $this->input('overtime_allowance', 0))),
            'leave_pay' => floatval(str_replace([',', '.'], '', $this->input('leave_pay', 0))),
            'bonus' => floatval(str_replace([',', '.'], '', $this->input('bonus', 0))),
        ];

        // Server-side salary sanity check (prevents currency/amount mismatch)
        $salaryWarning = $this->validateSalaryCurrency(
            $salaryData['basic_salary'],
            (int) $salaryData['currency_id']
        );
        if ($salaryWarning && empty($this->input('force_salary_override'))) {
            $this->setFlash('error', $salaryWarning . ' Jika yakin benar, centang "Override" dan submit ulang.');
            $this->redirect('contracts/edit/' . $id);
            return;
        }

        if ($existingSalary) {
            $salaryModel->update($existingSalary['id'], $salaryData);
        } else {
            $salaryModel->insert($salaryData);
        }

        // Update deductions (Feature 6) - delete old and insert new
        $deductionModel = new ContractDeductionModel($this->db);
        // Delete existing deductions for this contract
        $this->db->query("DELETE FROM contract_deductions WHERE contract_id = " . intval($id));

        // Insert new deductions from form
        if (!empty($_POST['deduction_type'])) {
            foreach ($_POST['deduction_type'] as $i => $type) {
                $amount = $_POST['deduction_amount'][$i] ?? 0;
                // Clean amount - remove commas and dots that are thousand separators
                $amount = str_replace([',', '.'], '', $amount);
                $amount = floatval($amount);

                if (!empty($type) && $amount > 0) {
                    $deductionModel->insert([
                        'contract_id' => $id,
                        'deduction_type' => $type,
                        'description' => $_POST['deduction_desc'][$i] ?? '',
                        'amount' => $amount,
                        'is_recurring' => ($_POST['deduction_recurring'][$i] ?? 'monthly') === 'monthly' ? 1 : 0,
                    ]);
                }
            }
        }

        // Log update (Feature 15)
        $logModel = new ContractLogModel($this->db);
        $logModel->log(
            $id,
            'updated',
            [],
            $this->getCurrentUser()['id'] ?? null,
            $this->getCurrentUser()['name'] ?? 'System'
        );

        $this->setFlash('success', 'Contract updated successfully');
        $this->redirect('contracts/' . $id);
    }

    /**
     * Feature 9: Approve contract
     */
    public function approve($id)
    {
        $this->requirePermission('contracts', 'approve');
        if (!$this->isPost()) {
            $this->redirect('contracts/' . $id);
        }

        $approvalModel = new ContractApprovalModel($this->db);
        $pending = $approvalModel->getPending($id);

        if ($pending) {
            $approvalModel->update($pending['id'], [
                'status' => 'approved',
                'approver_id' => $this->getCurrentUser()['id'] ?? null,
                'approver_name' => $this->getCurrentUser()['name'] ?? 'Admin',
                'approved_at' => date('Y-m-d H:i:s'),
                'notes' => $this->input('notes')
            ]);

            // Check if all approvals done, then activate contract
            $allApprovals = $approvalModel->getByContract($id);
            $allApproved = true;
            foreach ($allApprovals as $a) {
                if ($a['status'] !== 'approved') {
                    $allApproved = false;
                    break;
                }
            }

            if ($allApproved) {
                $this->contractModel->update($id, ['status' => CONTRACT_STATUS_ACTIVE]);
                
                // Sync crew status to 'onboard' (contract fully approved)
                $contract = $this->contractModel->find($id);
                if (!empty($contract['crew_id'])) {
                    $crewStmt = $this->db->prepare("UPDATE crews SET status = 'onboard', updated_at = NOW() WHERE id = ?");
                    if ($crewStmt) {
                        $crewStmt->bind_param('i', $contract['crew_id']);
                        $crewStmt->execute();
                        $crewStmt->close();
                    }

                    // Sync recruitment pipeline status to 'On Board' (status_id=11)
                    $this->syncRecruitmentPipelineStatus((int)$contract['crew_id'], 11);
                }
                
                // Notify: Crew ON Board
                try {
                    $notifModel = new \App\Models\NotificationModel($this->db);
                    $crewName = $contract['crew_name'] ?? 'Unknown';
                    $contractNo = $contract['contract_no'] ?? '';
                    $vesselName = '-'; $rankName = '-';
                    $infoQ = $this->db->query("SELECT v.name as vessel_name, r.name as rank_name FROM contracts c LEFT JOIN vessels v ON c.vessel_id=v.id LEFT JOIN ranks r ON c.rank_id=r.id WHERE c.id=" . intval($id) . " LIMIT 1");
                    if ($infoQ && $info = $infoQ->fetch_assoc()) { $vesselName = $info['vessel_name'] ?? '-'; $rankName = $info['rank_name'] ?? '-'; }
                    $signOn = !empty($contract['sign_on_date']) ? date('d M Y', strtotime($contract['sign_on_date'])) : date('d M Y');
                    $msg = "✅ *CREW ON BOARD*\n━━━━━━━━━━━━━━━━━━\n\n"
                         . "👤 *Crew:* {$crewName}\n🎖️ *Rank:* {$rankName}\n🚢 *Vessel:* {$vesselName}\n"
                         . "📋 *Kontrak:* {$contractNo}\n📅 *Tanggal Aktif:* {$signOn}\n📊 *Status:* ACTIVE ✅\n\n"
                         . "Kontrak telah disetujui semua pihak.\nCrew sudah ON BOARD.\n\n"
                         . "⏰ " . date('d M Y, H:i') . "\n━━━━━━━━━━━━━━━━━━\n— _IndoOcean ERP_ 🌊";
                    $notifModel->notify('success', 'Crew ON Board', $msg, 'contracts/' . $id);
                } catch (\Exception $e) {
                    error_log('Approve notification failed: ' . $e->getMessage());
                }
            } else {
                // Create next level approval
                $nextLevel = $pending['approval_level'] === APPROVAL_CREWING ? APPROVAL_HR : APPROVAL_DIRECTOR;
                $approvalModel->insert([
                    'contract_id' => $id,
                    'approval_level' => $nextLevel,
                    'status' => 'pending'
                ]);
            }

            // Log (Feature 15)
            $logModel = new ContractLogModel($this->db);
            $logModel->log($id, 'approved', [
                'field' => 'approval_level',
                'new' => $pending['approval_level']
            ]);
        }

        $this->setFlash('success', 'Contract approved');
        $this->redirect('contracts/' . $id);
    }

    /**
     * Feature 9: Reject contract
     */
    public function reject($id)
    {
        $this->requirePermission('contracts', 'approve');
        if (!$this->isPost()) {
            $this->redirect('contracts/' . $id);
        }

        $approvalModel = new ContractApprovalModel($this->db);
        $pending = $approvalModel->getPending($id);

        if ($pending) {
            $approvalModel->update($pending['id'], [
                'status' => 'rejected',
                'approver_id' => $this->getCurrentUser()['id'] ?? null,
                'approver_name' => $this->getCurrentUser()['name'] ?? 'Admin',
                'approved_at' => date('Y-m-d H:i:s'),
                'rejection_reason' => $this->input('reason')
            ]);

            $this->contractModel->update($id, ['status' => CONTRACT_STATUS_DRAFT]);

            // Log
            $logModel = new ContractLogModel($this->db);
            $logModel->log($id, 'rejected', [
                'field' => 'rejection_reason',
                'new' => $this->input('reason')
            ]);
        }

        $this->setFlash('warning', 'Contract rejected');
        
        // Notify: Contract Rejected
        try {
            $contract = $this->contractModel->find($id);
            $notifModel = new \App\Models\NotificationModel($this->db);
            $crewName = $contract['crew_name'] ?? 'Unknown';
            $contractNo = $contract['contract_no'] ?? '';
            $reason = $this->input('reason') ?? '-';
            $approverName = $this->getCurrentUser()['name'] ?? 'Admin';
            $rankName = '-';
            $infoQ = $this->db->query("SELECT r.name as rank_name FROM contracts c LEFT JOIN ranks r ON c.rank_id=r.id WHERE c.id=" . intval($id) . " LIMIT 1");
            if ($infoQ && $info = $infoQ->fetch_assoc()) { $rankName = $info['rank_name'] ?? '-'; }
            $msg = "\xe2\x9a\xa0\xef\xb8\x8f *KONTRAK DITOLAK*\n\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\n\n"
                 . "\xf0\x9f\x91\xa4 *Crew:* {$crewName}\n\xf0\x9f\x8e\x96\xef\xb8\x8f *Rank:* {$rankName}\n\xf0\x9f\x93\x8b *Kontrak:* {$contractNo}\n"
                 . "\xe2\x9d\x8c *Status:* REJECTED\n\n"
                 . "\xf0\x9f\x93\x9d *Alasan Penolakan:*\n{$reason}\n\n"
                 . "\xf0\x9f\x91\xa8\xe2\x80\x8d\xf0\x9f\x92\xbc *Ditolak oleh:* {$approverName}\n"
                 . "\xf0\x9f\x93\x85 *Tanggal:* " . date('d M Y') . "\n\n"
                 . "\xe2\x8f\xb0 " . date('d M Y, H:i') . "\n\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\n\xe2\x80\x94 _IndoOcean ERP_ \xf0\x9f\x8c\x8a";
            $notifModel->notify('warning', 'Kontrak Ditolak', $msg, 'contracts/' . $id);
        } catch (\Exception $e) {
            error_log('Reject notification failed: ' . $e->getMessage());
        }
        
        $this->redirect('contracts/' . $id);
    }

    /**
     * Feature 11: Renew contract
     */
    public function renew($id)
    {
        $oldContract = $this->contractModel->getWithDetails($id);
        if (!$oldContract) {
            $this->setFlash('error', 'Contract not found');
            $this->redirect('contracts');
        }

        if ($this->isPost()) {
            // Create new contract based on old one
            $newContractData = [
                'contract_no' => $this->contractModel->generateContractNumber(),
                'crew_id' => $oldContract['crew_id'],
                'crew_name' => $oldContract['crew_name'],
                'vessel_id' => $this->input('vessel_id', $oldContract['vessel_id']),
                'client_id' => $this->input('client_id', $oldContract['client_id']),
                'rank_id' => $this->input('rank_id', $oldContract['rank_id']),
                'contract_type' => $oldContract['contract_type'],
                'status' => CONTRACT_STATUS_DRAFT,
                'sign_on_date' => $this->input('sign_on_date'),
                'sign_off_date' => $this->input('sign_off_date'),
                'duration_months' => $this->input('duration_months'),
                'is_renewal' => 1,
                'previous_contract_id' => $id,
                'created_by' => $this->getCurrentUser()['id'] ?? null,
            ];

            // Copy salary (preserve original currency, don't hardcode!)
            $salaryData = [
                'currency_id' => $oldContract['currency_id'] ?? 1,
                'exchange_rate' => $oldContract['exchange_rate'] ?? null,
                'basic_salary' => $oldContract['basic_salary'] ?? 0,
                'overtime_allowance' => $oldContract['overtime_allowance'] ?? 0,
                'leave_pay' => $oldContract['leave_pay'] ?? 0,
                'bonus' => $oldContract['bonus'] ?? 0,
            ];

            // Copy tax
            $taxData = [
                'tax_type' => $oldContract['tax_type'] ?? TAX_TYPE_PPH21,
                'npwp_number' => $oldContract['npwp_number'],
                'tax_rate' => $oldContract['tax_rate'] ?? 5,
            ];

            $newContractId = $this->contractModel->createWithDetails($newContractData, $salaryData, $taxData);

            // Mark old contract as completed
            $this->contractModel->update($id, ['status' => CONTRACT_STATUS_COMPLETED]);

            // Log
            $logModel = new ContractLogModel($this->db);
            $logModel->log($id, 'renewed', ['field' => 'new_contract_id', 'new' => $newContractId]);
            $logModel->log($newContractId, 'created_from_renewal', ['field' => 'previous_contract_id', 'new' => $id]);

            $this->setFlash('success', 'Contract renewed successfully');
            
            // Notify: Contract Renewed
            try {
                $notifModel = new \App\Models\NotificationModel($this->db);
                $crewName = $oldContract['crew_name'] ?? 'Unknown';
                $vesselName = $oldContract['vessel_name'] ?? '-';
                $rankName = $oldContract['rank_name'] ?? '-';
                $oldSignOff = !empty($oldContract['sign_off_date']) ? date('d M Y', strtotime($oldContract['sign_off_date'])) : '-';
                $newSignOn = $this->input('sign_on_date') ? date('d M Y', strtotime($this->input('sign_on_date'))) : '-';
                $newSignOff = $this->input('sign_off_date') ? date('d M Y', strtotime($this->input('sign_off_date'))) : '-';
                $duration = $this->input('duration_months') ?? '-';
                $msg = "\xf0\x9f\x94\x84 *KONTRAK DIPERPANJANG*\n\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\n\n"
                     . "\xf0\x9f\x91\xa4 *Crew:* {$crewName}\n\xf0\x9f\x8e\x96\xef\xb8\x8f *Rank:* {$rankName}\n\xf0\x9f\x9a\xa2 *Vessel:* {$vesselName}\n\n"
                     . "\xf0\x9f\x93\x8b *Kontrak Lama:* {$oldContract['contract_no']}\n   \xe2\x94\x94 Sign Off: {$oldSignOff}\n\n"
                     . "\xf0\x9f\x93\x8b *Kontrak Baru:* {$newContractData['contract_no']}\n   \xe2\x94\x9c Sign On: {$newSignOn}\n   \xe2\x94\x9c Sign Off: {$newSignOff}\n   \xe2\x94\x94 Durasi: {$duration} bulan\n\n"
                     . "\xe2\x8f\xb0 " . date('d M Y, H:i') . "\n\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\n\xe2\x80\x94 _IndoOcean ERP_ \xf0\x9f\x8c\x8a";
                $notifModel->notify('info', 'Kontrak Diperpanjang', $msg, 'contracts/' . $newContractId);
            } catch (\Exception $e) {
                error_log('Renew notification failed: ' . $e->getMessage());
            }
            
            $this->redirect('contracts/' . $newContractId);
        }

        // Show renewal form
        $vesselModel = new VesselModel($this->db);
        $clientModel = new ClientModel($this->db);
        $rankModel = new RankModel($this->db);

        $data = [
            'title' => 'Renew Contract - ' . $oldContract['contract_no'],
            'contract' => $oldContract,
            'newContractNo' => $this->contractModel->generateContractNumber(),
            'vessels' => $vesselModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
            'ranks' => $rankModel->getForDropdown(),
        ];

        $uiMode = $_SESSION['ui_mode'] ?? 'modern';
        $view = $uiMode === 'modern' ? 'contracts/renew_modern' : 'contracts/renew';
        return $this->view($view, $data);
    }

    /**
     * Feature 12: Terminate contract
     */
    public function terminate($id)
    {
        $contract = $this->contractModel->find($id);
        if (!$contract) {
            $this->setFlash('error', 'Contract not found');
            $this->redirect('contracts');
        }

        if ($this->isPost()) {
            $this->contractModel->update($id, [
                'status' => CONTRACT_STATUS_TERMINATED,
                'actual_sign_off_date' => $this->input('actual_sign_off_date', date('Y-m-d')),
                'termination_reason' => $this->input('termination_reason'),
                'updated_by' => $this->getCurrentUser()['id'] ?? null,
            ]);

            // Update crew status back to available
            if (!empty($contract['crew_id'])) {
                $crewStmt = $this->db->prepare("UPDATE crews SET status = 'available', updated_at = NOW() WHERE id = ?");
                $crewStmt->bind_param('i', $contract['crew_id']);
                $crewStmt->execute();
                $crewStmt->close();
                
                // Notify: Contract Activated (Crew ON-Board)
                try {
                    $notifModel = new \App\Models\NotificationModel($this->db);
                    $crewName = $contract['crew_name'] ?? 'Unknown';
                    $contractNo = $contract['contract_no'] ?? '';
                    $notifModel->notify(
                        'success',
                        'Crew ON Board ✅',
                        "Crew: {$crewName}\nKontrak {$contractNo} telah disetujui & aktif.\nStatus crew: Onboard",
                        'contracts/' . $id
                    );
                } catch (\Exception $e) {
                    error_log('Approve notification failed: ' . $e->getMessage());
                }
            }

            // Cascade: cleanup pending payroll_items for terminated contract
            $affectedPeriods = $this->db->query("SELECT DISTINCT payroll_period_id FROM payroll_items WHERE contract_id = " . intval($id) . " AND status IN ('pending', 'draft')");
            $this->db->query("DELETE FROM payroll_items WHERE contract_id = " . intval($id) . " AND status IN ('pending', 'draft')");
            if ($affectedPeriods) {
                require_once APPPATH . 'Models/PayrollModel.php';
                $periodModel = new \App\Models\PayrollPeriodModel($this->db);
                while ($row = $affectedPeriods->fetch_assoc()) {
                    $periodModel->updateTotals($row['payroll_period_id']);
                }
            }

            // Log
            $logModel = new ContractLogModel($this->db);
            $logModel->log($id, 'terminated', [
                'field' => 'termination_reason',
                'new' => $this->input('termination_reason')
            ]);

            $this->setFlash('warning', 'Contract terminated');
            
            // Notify: Contract Terminated (Crew OFF / Sign-Off)
            try {
                $notifModel = new \App\Models\NotificationModel($this->db);
                $crewName = $contract['crew_name'] ?? 'Unknown';
                $contractNo = $contract['contract_no'] ?? '';
                $reason = $this->input('termination_reason') ?? '-';
                $signOffDate = $this->input('actual_sign_off_date', date('Y-m-d'));
                $signOffFormatted = date('d M Y', strtotime($signOffDate));
                $vesselName = '-'; $rankName = '-'; $portName = '-';
                $infoQ = $this->db->query("SELECT v.name as vessel_name, r.name as rank_name, c.disembarkation_port FROM contracts c LEFT JOIN vessels v ON c.vessel_id=v.id LEFT JOIN ranks r ON c.rank_id=r.id WHERE c.id=" . intval($id) . " LIMIT 1");
                if ($infoQ && $info = $infoQ->fetch_assoc()) { $vesselName = $info['vessel_name'] ?? '-'; $rankName = $info['rank_name'] ?? '-'; $portName = $info['disembarkation_port'] ?? '-'; }
                $msg = "\xe2\x9b\x94 *CREW OFF BOARD*\n\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\n\n"
                     . "\xf0\x9f\x91\xa4 *Crew:* {$crewName}\n\xf0\x9f\x8e\x96\xef\xb8\x8f *Rank:* {$rankName}\n\xf0\x9f\x9a\xa2 *Vessel:* {$vesselName}\n"
                     . "\xf0\x9f\x93\x8b *Kontrak:* {$contractNo}\n\n"
                     . "\xe2\x9d\x8c *Status:* TERMINATED\n"
                     . "\xf0\x9f\x93\x85 *Tanggal Sign Off:* {$signOffFormatted}\n"
                     . "\xf0\x9f\x8f\x97\xef\xb8\x8f *Pelabuhan:* {$portName}\n\n"
                     . "\xf0\x9f\x93\x9d *Alasan:*\n{$reason}\n\n"
                     . "\xe2\x8f\xb0 " . date('d M Y, H:i') . "\n\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\xe2\x94\x81\n\xe2\x80\x94 _IndoOcean ERP_ \xf0\x9f\x8c\x8a";
                $notifModel->notify('danger', 'Crew OFF Board', $msg, 'contracts/' . $id);
            } catch (\Exception $e) {
                error_log('Terminate notification failed: ' . $e->getMessage());
            }
            
            $this->redirect('contracts/' . $id);
        }

        $data = [
            'title' => 'Terminate Contract - ' . $contract['contract_no'],
            'contract' => $contract,
        ];

        return $this->view('contracts/terminate', $data);
    }

    /**
     * Feature: Suspend contract (OFF) - removes from all calculations
     */
    public function suspend($id)
    {
        $this->requirePermission('contracts', 'edit');
        $contract = $this->contractModel->find($id);

        if (!$contract) {
            $this->setFlash('error', 'Contract not found');
            $this->redirect('contracts');
            return;
        }

        // Only active/onboard contracts can be suspended
        if (!in_array($contract['status'], ['active', 'onboard'])) {
            $this->setFlash('error', 'Hanya kontrak Active/Onboard yang bisa di-suspend.');
            $this->redirect('contracts/' . $id);
            return;
        }

        if ($this->isPost()) {
            $this->db->begin_transaction();

            try {
                $previousStatus = $contract['status'];
                $suspendReason = $this->input('suspend_reason', '');

                // 1. Update contract status to suspended, save previous status in notes for restore
                $notesPrefix = "[SUSPENDED from:{$previousStatus}] ";
                $existingNotes = $contract['notes'] ?? '';
                $this->contractModel->update($id, [
                    'status' => CONTRACT_STATUS_SUSPENDED,
                    'notes' => $notesPrefix . ($suspendReason ? $suspendReason . ' | ' : '') . $existingNotes,
                    'updated_by' => $this->getCurrentUser()['id'] ?? null,
                ]);

                // 2. Update crew status to standby
                if (!empty($contract['crew_id'])) {
                    $crewStmt = $this->db->prepare("UPDATE crews SET status = 'standby', updated_at = NOW() WHERE id = ?");
                    $crewStmt->bind_param('i', $contract['crew_id']);
                    $crewStmt->execute();
                    $crewStmt->close();
                }

                // 3. Cleanup pending payroll items for this contract
                $affectedPeriods = $this->db->query(
                    "SELECT DISTINCT payroll_period_id FROM payroll_items WHERE contract_id = " . intval($id) . " AND status IN ('pending', 'draft')"
                );
                $this->db->query(
                    "DELETE FROM payroll_items WHERE contract_id = " . intval($id) . " AND status IN ('pending', 'draft')"
                );
                if ($affectedPeriods) {
                    require_once APPPATH . 'Models/PayrollModel.php';
                    $periodModel = new \App\Models\PayrollPeriodModel($this->db);
                    while ($row = $affectedPeriods->fetch_assoc()) {
                        $periodModel->updateTotals($row['payroll_period_id']);
                    }
                }

                // 4. Log
                $logModel = new ContractLogModel($this->db);
                $logModel->log($id, 'suspended', [
                    'field' => 'status',
                    'old' => $previousStatus,
                    'new' => 'suspended'
                ], $this->getCurrentUser()['id'] ?? null, $this->getCurrentUser()['name'] ?? 'System');

                $this->db->commit();

                // 5. Notify
                try {
                    $notifModel = new \App\Models\NotificationModel($this->db);
                    $crewName = $contract['crew_name'] ?? 'Unknown';
                    $contractNo = $contract['contract_no'] ?? '';
                    $vesselName = '-'; $rankName = '-';
                    $infoQ = $this->db->query("SELECT v.name as vessel_name, r.name as rank_name FROM contracts c LEFT JOIN vessels v ON c.vessel_id=v.id LEFT JOIN ranks r ON c.rank_id=r.id WHERE c.id=" . intval($id) . " LIMIT 1");
                    if ($infoQ && $info = $infoQ->fetch_assoc()) { $vesselName = $info['vessel_name'] ?? '-'; $rankName = $info['rank_name'] ?? '-'; }
                    $reasonText = $suspendReason ?: 'Tidak ada alasan';
                    $msg = "⏸️ *KONTRAK DI-SUSPEND (OFF)*\n━━━━━━━━━━━━━━━━━━\n\n"
                         . "👤 *Crew:* {$crewName}\n🎖️ *Rank:* {$rankName}\n🚢 *Vessel:* {$vesselName}\n"
                         . "📋 *Kontrak:* {$contractNo}\n\n"
                         . "⚠️ *Status:* SUSPENDED\n"
                         . "📝 *Alasan:* {$reasonText}\n\n"
                         . "ℹ️ Semua perhitungan payroll, margin, pendapatan otomatis dikurangi.\n"
                         . "Kontrak bisa di-reactivate kapan saja.\n\n"
                         . "⏰ " . date('d M Y, H:i') . "\n━━━━━━━━━━━━━━━━━━\n— _IndoOcean ERP_ 🌊";
                    $notifModel->notify('warning', 'Kontrak Di-Suspend', $msg, 'contracts/' . $id);
                } catch (\Exception $e) {
                    error_log('Suspend notification failed: ' . $e->getMessage());
                }

                $this->setFlash('success', 'Kontrak berhasil di-suspend. Semua perhitungan otomatis disesuaikan.');
                $this->redirect('contracts/' . $id);

            } catch (\Exception $e) {
                $this->db->rollback();
                $this->setFlash('error', 'Gagal suspend kontrak: ' . $e->getMessage());
                $this->redirect('contracts/' . $id);
            }
            return;
        }

        // Show confirm page (GET request) - redirect to detail with flash
        $data = [
            'title' => 'Suspend Contract - ' . $contract['contract_no'],
            'contract' => $contract,
        ];
        return $this->view('contracts/suspend_modern', $data);
    }

    /**
     * Feature: Reactivate suspended contract (ON) - restores to all calculations
     */
    public function reactivate($id)
    {
        $this->requirePermission('contracts', 'edit');
        $contract = $this->contractModel->find($id);

        if (!$contract) {
            $this->setFlash('error', 'Contract not found');
            $this->redirect('contracts');
            return;
        }

        // Only suspended contracts can be reactivated
        if ($contract['status'] !== CONTRACT_STATUS_SUSPENDED) {
            $this->setFlash('error', 'Hanya kontrak Suspended yang bisa di-reactivate.');
            $this->redirect('contracts/' . $id);
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('contracts/' . $id);
            return;
        }

        $this->db->begin_transaction();

        try {
            // 1. Restore previous status from notes
            $restoredStatus = 'active'; // default
            $notes = $contract['notes'] ?? '';
            if (preg_match('/\[SUSPENDED from:(\w+)\]/', $notes, $matches)) {
                $restoredStatus = $matches[1];
                // Clean the suspend marker from notes
                $notes = trim(preg_replace('/\[SUSPENDED from:\w+\]\s*/', '', $notes));
                // Also clean the suspend reason part if present
                $notes = preg_replace('/^[^|]*\|\s*/', '', $notes);
            }

            // Ensure restored status is valid
            if (!in_array($restoredStatus, ['active', 'onboard'])) {
                $restoredStatus = 'active';
            }

            // 2. Update contract status
            $this->contractModel->update($id, [
                'status' => $restoredStatus,
                'notes' => $notes,
                'updated_by' => $this->getCurrentUser()['id'] ?? null,
            ]);

            // 3. Update crew status to contracted
            if (!empty($contract['crew_id'])) {
                $crewStmt = $this->db->prepare("UPDATE crews SET status = 'contracted', updated_at = NOW() WHERE id = ?");
                $crewStmt->bind_param('i', $contract['crew_id']);
                $crewStmt->execute();
                $crewStmt->close();
            }

            // 4. Auto-regenerate payroll for current period if exists
            try {
                require_once APPPATH . 'Models/PayrollModel.php';
                $periodModel = new \App\Models\PayrollPeriodModel($this->db);
                $currentMonth = date('n');
                $currentYear = date('Y');
                $currentPeriod = $periodModel->getOrCreate($currentMonth, $currentYear);
                if ($currentPeriod && $currentPeriod['status'] !== 'locked') {
                    $itemModel = new \App\Models\PayrollItemModel($this->db);
                    $itemModel->generateForPeriod($currentPeriod['id']);
                }
            } catch (\Exception $e) {
                error_log('Reactivate payroll regen failed: ' . $e->getMessage());
            }

            // 5. Log
            $logModel = new ContractLogModel($this->db);
            $logModel->log($id, 'reactivated', [
                'field' => 'status',
                'old' => 'suspended',
                'new' => $restoredStatus
            ], $this->getCurrentUser()['id'] ?? null, $this->getCurrentUser()['name'] ?? 'System');

            $this->db->commit();

            // 6. Notify
            try {
                $notifModel = new \App\Models\NotificationModel($this->db);
                $crewName = $contract['crew_name'] ?? 'Unknown';
                $contractNo = $contract['contract_no'] ?? '';
                $vesselName = '-'; $rankName = '-';
                $infoQ = $this->db->query("SELECT v.name as vessel_name, r.name as rank_name FROM contracts c LEFT JOIN vessels v ON c.vessel_id=v.id LEFT JOIN ranks r ON c.rank_id=r.id WHERE c.id=" . intval($id) . " LIMIT 1");
                if ($infoQ && $info = $infoQ->fetch_assoc()) { $vesselName = $info['vessel_name'] ?? '-'; $rankName = $info['rank_name'] ?? '-'; }
                $msg = "▶️ *KONTRAK DI-REACTIVATE (ON)*\n━━━━━━━━━━━━━━━━━━\n\n"
                     . "👤 *Crew:* {$crewName}\n🎖️ *Rank:* {$rankName}\n🚢 *Vessel:* {$vesselName}\n"
                     . "📋 *Kontrak:* {$contractNo}\n\n"
                     . "✅ *Status:* " . strtoupper($restoredStatus) . "\n\n"
                     . "ℹ️ Semua perhitungan payroll, margin, pendapatan otomatis kembali.\n"
                     . "Kontrak sudah aktif kembali.\n\n"
                     . "⏰ " . date('d M Y, H:i') . "\n━━━━━━━━━━━━━━━━━━\n— _IndoOcean ERP_ 🌊";
                $notifModel->notify('success', 'Kontrak Di-Reactivate', $msg, 'contracts/' . $id);
            } catch (\Exception $e) {
                error_log('Reactivate notification failed: ' . $e->getMessage());
            }

            $this->setFlash('success', 'Kontrak berhasil di-reactivate! Semua perhitungan otomatis kembali normal.');
            $this->redirect('contracts/' . $id);

        } catch (\Exception $e) {
            $this->db->rollback();
            $this->setFlash('error', 'Gagal reactivate kontrak: ' . $e->getMessage());
            $this->redirect('contracts/' . $id);
        }
    }

    /**
     * Feature 10: Get expiring contracts for alerts
     */
    public function expiring($days = null)
    {
        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'modern';

        $days = $days ?? (int) $this->input('days', 60);
        $contracts = $this->contractModel->getExpiring($days);

        // Categorize by urgency
        $critical = [];
        $warning = [];
        $upcoming = [];

        foreach ($contracts as $contract) {
            $daysLeft = $contract['days_remaining'] ?? 999;

            if ($daysLeft <= 7) {
                $critical[] = $contract;
            } elseif ($daysLeft <= 30) {
                $warning[] = $contract;
            } else {
                $upcoming[] = $contract;
            }
        }

        $data = [
            'title' => 'Expiring Contracts',
            'contracts' => $contracts,
            'critical_count' => count($critical),
            'warning_count' => count($warning),
            'upcoming_count' => count($upcoming),
            'days' => $days,
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'contracts/expiring_modern' : 'contracts/expiring';
        return $this->view($view, $data);
    }

    /**
     * Delete contract (soft delete or hard delete for draft only)
     */
    public function delete($id)
    {
        $this->requirePermission('contracts', 'delete');
        $contract = $this->contractModel->find($id);

        if ($contract && $contract['status'] === CONTRACT_STATUS_DRAFT) {
            // Cascade: cleanup related payroll_items before deleting contract
            $affectedPeriods = $this->db->query("SELECT DISTINCT payroll_period_id FROM payroll_items WHERE contract_id = " . intval($id));
            $this->db->query("DELETE FROM payroll_items WHERE contract_id = " . intval($id));
            // Recalculate period totals for affected periods
            if ($affectedPeriods) {
                require_once APPPATH . 'Models/PayrollModel.php';
                $periodModel = new \App\Models\PayrollPeriodModel($this->db);
                while ($row = $affectedPeriods->fetch_assoc()) {
                    $periodModel->updateTotals($row['payroll_period_id']);
                }
            }

            $this->contractModel->delete($id);
            $this->setFlash('success', 'Contract deleted');
        } else {
            $this->setFlash('error', 'Only draft contracts can be deleted');
        }

        $this->redirect('contracts');
    }

    // Helper methods
    private function calculateDaysRemaining($signOffDate)
    {
        if (!$signOffDate)
            return null;
        $diff = strtotime($signOffDate) - time();
        return floor($diff / 86400);
    }

    /**
     * Toggle UI Mode - LOCKED to modern
     */
    public function toggleMode()
    {
        $_SESSION['ui_mode'] = 'modern';
        return $this->redirect('contracts');
    }

    /**
     * Feature 8: Export contract as PDF
     */
    public function exportPdf($id)
    {
        $contract = $this->contractModel->getWithDetails($id);
        if (!$contract) {
            $this->setFlash('error', 'Contract not found');
            $this->redirect('contracts');
        }

        $deductionModel = new ContractDeductionModel($this->db);
        $deductions = $deductionModel->getByContract($id);

        require_once APPPATH . 'Libraries/PDFGenerator.php';
        $pdf = new \App\Libraries\PDFGenerator();
        $pdf->generateContract($contract, $deductions);
        $pdf->output('Contract_' . $contract['contract_no'] . '.pdf', 'I');
    }

    /**
     * Upload document for contract
     */
    public function uploadDoc($contractId)
    {
        if (!$this->isPost()) {
            $this->redirect('contracts/' . $contractId);
        }

        if (empty($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Please select a file to upload');
            $this->redirect('contracts/' . $contractId);
        }

        $file = $_FILES['document'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validate extension
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xls', 'xlsx'];
        if (!in_array($ext, $allowed)) {
            $this->setFlash('error', 'Invalid file type. Allowed: ' . implode(', ', $allowed));
            $this->redirect('contracts/' . $contractId);
        }

        // Validate size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $this->setFlash('error', 'File too large. Max size: 10MB');
            $this->redirect('contracts/' . $contractId);
        }

        $uploadPath = FCPATH . 'uploads/contracts/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $newFilename = 'contract_' . $contractId . '_' . time() . '.' . $ext;
        $filePath = $uploadPath . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $stmt = $this->db->prepare("
                INSERT INTO contract_documents (contract_id, document_type, language, file_name, file_path, file_size, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $docType = $_POST['document_type'] ?? 'contract';
            $language = $_POST['language'] ?? 'id';
            $relPath = 'uploads/contracts/' . $newFilename;
            $stmt->bind_param('issssi', $contractId, $docType, $language, $file['name'], $relPath, $file['size']);
            $stmt->execute();

            $this->setFlash('success', 'Document uploaded successfully');
        } else {
            $this->setFlash('error', 'Failed to upload file');
        }

        $this->redirect('contracts/' . $contractId);
    }

    /**
     * Download contract document
     */
    public function downloadDoc($docId)
    {
        $result = $this->db->query("SELECT * FROM contract_documents WHERE id = " . intval($docId));
        $doc = $result ? $result->fetch_assoc() : null;

        if (!$doc) {
            $this->setFlash('error', 'Document not found');
            $this->redirect('contracts');
        }

        $filePath = FCPATH . $doc['file_path'];
        if (!file_exists($filePath)) {
            $this->setFlash('error', 'File not found on server');
            $this->redirect('contracts/' . $doc['contract_id']);
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $doc['file_name'] . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * Crew Movement Monitoring Timeline
     * Shows all contract events: onboard, sign-off, renew, terminate
     */
    public function timeline()
    {
        $this->requireAuth();
        $this->requirePermission('contracts', 'view');

        $filterCrew = $this->input('crew');
        $filterVessel = $this->input('vessel');
        $filterType = $this->input('type');
        $filterFrom = $this->input('from', date('Y-m-d', strtotime('-90 days')));
        $filterTo = $this->input('to', date('Y-m-d'));

        // Build timeline from contract_logs + contracts data
        $sql = "
            SELECT 
                cl.id as log_id,
                cl.contract_id,
                cl.action,
                cl.created_at as event_date,
                cl.user_name,
                c.contract_no,
                c.crew_name,
                c.crew_id,
                c.status as contract_status,
                c.sign_on_date,
                c.sign_off_date,
                c.actual_sign_off_date,
                c.is_renewal,
                c.previous_contract_id,
                c.termination_reason,
                v.name as vessel_name,
                r.name as rank_name
            FROM contract_logs cl
            JOIN contracts c ON cl.contract_id = c.id
            LEFT JOIN vessels v ON c.vessel_id = v.id
            LEFT JOIN ranks r ON c.rank_id = r.id
            WHERE cl.created_at BETWEEN ? AND ?
        ";
        
        $params = [$filterFrom . ' 00:00:00', $filterTo . ' 23:59:59'];
        $types = 'ss';

        if ($filterCrew) {
            $sql .= " AND c.crew_name LIKE ?";
            $params[] = '%' . $filterCrew . '%';
            $types .= 's';
        }
        if ($filterVessel) {
            $sql .= " AND v.id = ?";
            $params[] = (int)$filterVessel;
            $types .= 'i';
        }
        if ($filterType) {
            $sql .= " AND cl.action = ?";
            $params[] = $filterType;
            $types .= 's';
        }

        $sql .= " ORDER BY cl.created_at DESC LIMIT 100";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $stmt->close();

        // Get stats
        $statsQuery = $this->db->query("
            SELECT 
                SUM(CASE WHEN status IN ('active','onboard') THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'terminated' THEN 1 ELSE 0 END) as terminated_count,
                SUM(CASE WHEN is_renewal = 1 THEN 1 ELSE 0 END) as renewed_count
            FROM contracts
        ");
        $stats = $statsQuery ? $statsQuery->fetch_assoc() : [];

        // Vessels for filter dropdown
        $vesselModel = new VesselModel($this->db);

        $data = [
            'title' => 'Crew Movement Monitoring',
            'events' => $events,
            'stats' => $stats,
            'vessels' => $vesselModel->getForDropdown(),
            'filters' => [
                'crew' => $filterCrew,
                'vessel' => $filterVessel,
                'type' => $filterType,
                'from' => $filterFrom,
                'to' => $filterTo,
            ],
            'flash' => $this->getFlash()
        ];

        return $this->view('contracts/timeline_modern', $data);
    }

    /**
     * Sync recruitment pipeline status when contract status changes.
     * Maps crew_id → recruitment application via recruitment_sync table,
     * then updates recruitment DB applications.status_id.
     * 
     * Status IDs in recruitment DB:
     *   5 = Processing (contract created/pending)
     *   6 = Approved (checklist completed)
     *   9 = Admin Review
     *   11 = On Board (contract approved & active)
     */
    private function syncRecruitmentPipelineStatus(int $crewId, int $statusId): void
    {
        try {
            // Look up the recruitment application ID from sync table
            $syncStmt = $this->db->prepare("SELECT recruitment_applicant_id FROM recruitment_sync WHERE crew_id = ? LIMIT 1");
            if (!$syncStmt) return;
            
            $syncStmt->bind_param('i', $crewId);
            $syncStmt->execute();
            $syncRow = $syncStmt->get_result()->fetch_assoc();
            $syncStmt->close();

            if (!$syncRow || empty($syncRow['recruitment_applicant_id'])) {
                return; // Not from recruitment — nothing to sync
            }

            $applicationId = (int) $syncRow['recruitment_applicant_id'];

            // Update sync table status
            $syncStatus = $statusId == 6 ? 'onboard' : 'processing';
            $updSync = $this->db->prepare("UPDATE recruitment_sync SET sync_status = ?, synced_at = NOW() WHERE crew_id = ?");
            if ($updSync) {
                $updSync->bind_param('si', $syncStatus, $crewId);
                $updSync->execute();
                $updSync->close();
            }

            // Update recruitment DB if connected
            if (!empty($this->recruitmentDb) && !$this->recruitmentDb->connect_error) {
                $updApp = $this->recruitmentDb->prepare("
                    UPDATE applications 
                    SET status_id = ?, status_updated_at = NOW(), updated_at = NOW() 
                    WHERE id = ?
                ");
                if ($updApp) {
                    $updApp->bind_param('ii', $statusId, $applicationId);
                    $updApp->execute();
                    $updApp->close();
                }
            }

            error_log("Pipeline sync: crew_id={$crewId}, app_id={$applicationId}, status_id={$statusId}");
        } catch (\Throwable $e) {
            error_log("syncRecruitmentPipelineStatus error: " . $e->getMessage());
        }
    }
}
