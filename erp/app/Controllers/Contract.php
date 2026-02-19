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
        // Check UI mode from session (classic or modern)
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

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
        $vesselModel = new VesselModel($this->db);
        $clientModel = new ClientModel($this->db);
        $rankModel = new RankModel($this->db);
        $currencyModel = new CurrencyModel($this->db);
        $crewModel = new CrewModel($this->db);

        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

        // Get all crews for dropdown
        $allCrews = $crewModel->getForDropdown();

        // Get recruitment crews separately (newly approved from recruitment)
        $recruitmentCrews = $this->getRecruitmentCrews();

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
        ];

        // Route to appropriate view based on UI mode
        $view = $uiMode === 'modern' ? 'contracts/create_modern' : 'contracts/form';
        return $this->view($view, $data);
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
                c.current_rank_id,
                c.approved_at,
                c.source,
                r.name as rank_name,
                DATEDIFF(NOW(), c.approved_at) as days_since_approval
            FROM crews c
            LEFT JOIN ranks r ON c.current_rank_id = r.id
            WHERE c.source = 'recruitment'
            AND c.status IN ('standby', 'available')
            AND c.id NOT IN (
                SELECT crew_id FROM contracts 
                WHERE status IN ('active', 'pending')
            )
            ORDER BY c.approved_at DESC
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
     * Store new contract with salary, tax, deductions
     */
    public function store()
    {
        if (!$this->isPost()) {
            $this->redirect('contracts');
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
            'basic_salary' => floatval(str_replace(',', '', $this->input('basic_salary', 0))),
            'overtime_allowance' => floatval(str_replace(',', '', $this->input('overtime_allowance', 0))),
            'leave_pay' => floatval(str_replace(',', '', $this->input('leave_pay', 0))),
            'bonus' => floatval(str_replace(',', '', $this->input('bonus', 0))),
            'other_allowance' => floatval(str_replace(',', '', $this->input('other_allowance', 0))),
        ];

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

        $data = [
            'title' => 'Edit Contract - ' . $contract['contract_no'],
            'contract' => $contract,
            'deductions' => $deductionModel->getByContract($id),
            'vessels' => $vesselModel->getForDropdown(),
            'clients' => $clientModel->getForDropdown(),
            'ranks' => $rankModel->getForDropdown(),
            'currencies' => $currencyModel->getForDropdown(),
            'contractTypes' => CONTRACT_TYPES,
            'taxTypes' => TAX_TYPES,
            'deductionTypes' => DEDUCTION_TYPES,
        ];

        return $this->view('contracts/form', $data);
    }

    /**
     * Update contract
     */
    public function update($id)
    {
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
            'basic_salary' => floatval(str_replace(',', '', $this->input('basic_salary', 0))),
            'overtime_allowance' => floatval(str_replace(',', '', $this->input('overtime_allowance', 0))),
            'leave_pay' => floatval(str_replace(',', '', $this->input('leave_pay', 0))),
            'bonus' => floatval(str_replace(',', '', $this->input('bonus', 0))),
        ];

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

            // Copy salary
            $salaryData = [
                'currency_id' => 1,
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

        return $this->view('contracts/renew', $data);
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

            // Log
            $logModel = new ContractLogModel($this->db);
            $logModel->log($id, 'terminated', [
                'field' => 'termination_reason',
                'new' => $this->input('termination_reason')
            ]);

            $this->setFlash('warning', 'Contract terminated');
            $this->redirect('contracts/' . $id);
        }

        $data = [
            'title' => 'Terminate Contract - ' . $contract['contract_no'],
            'contract' => $contract,
        ];

        return $this->view('contracts/terminate', $data);
    }

    /**
     * Feature 10: Get expiring contracts for alerts
     */
    public function expiring($days = null)
    {
        // Check UI mode from session
        $uiMode = $_SESSION['ui_mode'] ?? 'classic';

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
        $contract = $this->contractModel->find($id);

        if ($contract && $contract['status'] === CONTRACT_STATUS_DRAFT) {
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
     * Toggle UI Mode between classic and modern
     */
    public function toggleMode()
    {
        $mode = $this->input('mode', 'classic');
        $_SESSION['ui_mode'] = in_array($mode, ['classic', 'modern']) ? $mode : 'classic';
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
}
