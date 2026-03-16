<?php
/**
 * PT Indo Ocean - ERP System
 * Smart Importer Controller — Multi-Sheet Intelligent Data Import
 * Reads ALL sheets, auto-creates Crew/Vessel/Client/Contract/Salary/Documents
 * Skip identical rows, update changed rows, 100% deduplication
 */

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class SmartImport extends BaseController
{
    /**
     * Upload page
     */
    public function index()
    {
        $this->requireAuth();
        $data = [
            'title'       => 'Smart Import — Intelligent Data Import',
            'currentPage' => 'smart-import',
            'flash'       => $this->getFlash()
        ];
        return $this->view('smart_import/index_modern', $data);
    }

    /**
     * Preview uploaded file — parse ALL sheets & show mapping
     */
    public function preview()
    {
        $this->requireAuth();
        if (!$this->isPost() || empty($_FILES['excel_file'])) {
            $this->setFlash('error', 'Pilih file Excel terlebih dahulu');
            $this->redirect('SmartImport');
            return;
        }

        $file = $_FILES['excel_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'xls', 'csv'])) {
            $this->setFlash('error', 'Format tidak didukung. Gunakan .xlsx, .xls, atau .csv');
            $this->redirect('SmartImport');
            return;
        }

        try {
            $spreadsheet = IOFactory::load($file['tmp_name']);

            // Save to temp for processing later
            $tmpName = 'import_' . uniqid() . '.' . $ext;
            $tmpPath = WRITEPATH . 'uploads/';
            if (!is_dir($tmpPath)) mkdir($tmpPath, 0755, true);
            move_uploaded_file($file['tmp_name'], $tmpPath . $tmpName);

            // ========== PARSE ALL SHEETS ==========
            $sheetNames = $spreadsheet->getSheetNames();
            $allSheets = [];
            $totalRows = 0;
            $totalValid = 0;
            $totalError = 0;
            $totalWarn = 0;

            $lookups = $this->loadLookups();

            foreach ($sheetNames as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $rows = $sheet->toArray(null, true, true, true);

                if (count($rows) < 2) continue; // Skip empty sheets

                // Smart header detection
                $headerInfo = $this->detectHeaders($rows);
                $headers = $headerInfo['headers'];
                $headerRowCount = $headerInfo['headerRowCount'];

                // Get data rows
                $dataRows = [];
                $idx = 0;
                foreach ($rows as $key => $row) {
                    $idx++;
                    if ($idx <= $headerRowCount) continue;
                    $hasData = false;
                    foreach ($row as $cell) {
                        $v = trim((string)($cell ?? ''));
                        if ($v !== '') { $hasData = true; break; }
                    }
                    if ($hasData) $dataRows[] = $row;
                }

                if (empty($dataRows)) continue;

                // Auto-detect column mapping
                $mapping = $this->autoDetectMapping($headers);

                // Validate rows
                $validation = $this->validateRows($dataRows, $mapping, $lookups, $headers);

                $allSheets[] = [
                    'name'       => $sheetName,
                    'headers'    => $headers,
                    'mapping'    => $mapping,
                    'dataRows'   => $dataRows,
                    'validation' => $validation,
                    'totalRows'  => count($dataRows),
                    'validRows'  => $validation['valid_count'],
                    'errorRows'  => $validation['error_count'],
                    'warnRows'   => $validation['warn_count'],
                ];

                $totalRows  += count($dataRows);
                $totalValid += $validation['valid_count'];
                $totalError += $validation['error_count'];
                $totalWarn  += $validation['warn_count'];
            }

            if (empty($allSheets)) {
                $this->setFlash('error', 'Tidak ada data yang bisa diimport dari file ini.');
                $this->redirect('SmartImport');
                return;
            }

            $_SESSION['import_file'] = $tmpPath . $tmpName;
            $_SESSION['import_sheets'] = array_map(function($s) {
                return [
                    'name' => $s['name'],
                    'mapping' => $s['mapping'],
                    'headerRowCount' => count($s['headers']),
                ];
            }, $allSheets);

            $data = [
                'title'       => 'Smart Import — Preview Data (' . count($allSheets) . ' Sheets)',
                'currentPage' => 'smart-import',
                'allSheets'   => $allSheets,
                'lookups'     => $lookups,
                'totalRows'   => $totalRows,
                'validRows'   => $totalValid,
                'errorRows'   => $totalError,
                'warnRows'    => $totalWarn,
                'fileName'    => $file['name'],
                'sheetCount'  => count($allSheets),
                'flash'       => $this->getFlash()
            ];

            return $this->view('smart_import/preview_modern', $data);
        } catch (\Exception $e) {
            $this->setFlash('error', 'Gagal membaca file: ' . $e->getMessage());
            $this->redirect('SmartImport');
        }
    }

    /**
     * Process the import — ALL SHEETS
     */
    public function process()
    {
        $this->requireAuth();
        if (!$this->isPost()) {
            $this->redirect('SmartImport');
            return;
        }

        $filePath = $_SESSION['import_file'] ?? null;

        if (!$filePath || !file_exists($filePath)) {
            $this->setFlash('error', 'File import tidak ditemukan. Upload ulang.');
            $this->redirect('SmartImport');
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetNames = $spreadsheet->getSheetNames();
            $lookups = $this->loadLookups();

            $allResults = [];
            $grandTotal = ['success' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [],
                           'created_crews' => [], 'updated_crews' => [],
                           'created_vessels' => [], 'created_clients' => [],
                           'created_contracts' => []];

            $this->db->begin_transaction();

            foreach ($sheetNames as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $rows = $sheet->toArray(null, true, true, true);

                if (count($rows) < 2) continue;

                // Header detection
                $headerInfo = $this->detectHeaders($rows);
                $headers = $headerInfo['headers'];
                $headerRowCount = $headerInfo['headerRowCount'];

                // Data rows
                $dataRows = [];
                $idx = 0;
                foreach ($rows as $key => $row) {
                    $idx++;
                    if ($idx <= $headerRowCount) continue;
                    $hasData = false;
                    foreach ($row as $cell) {
                        $v = trim((string)($cell ?? ''));
                        if ($v !== '') { $hasData = true; break; }
                    }
                    if ($hasData) $dataRows[] = $row;
                }

                if (empty($dataRows)) continue;

                $mapping = $this->autoDetectMapping($headers);

                $sheetResults = [
                    'sheet' => $sheetName,
                    'success' => 0, 'updated' => 0, 'skipped' => 0,
                    'errors' => [], 'created_crews' => [], 'updated_crews' => []
                ];

                foreach ($dataRows as $rowNum => $row) {
                    try {
                        $this->importRow($row, $mapping, $lookups, $sheetResults, $rowNum + $headerRowCount + 1, $sheetName);
                    } catch (\Exception $e) {
                        $sheetResults['errors'][] = "[{$sheetName}] Baris " . ($rowNum + $headerRowCount + 1) . ": " . $e->getMessage();
                    }
                }

                $allResults[] = $sheetResults;

                // Aggregate
                $grandTotal['success']  += $sheetResults['success'];
                $grandTotal['updated']  += $sheetResults['updated'];
                $grandTotal['skipped']  += $sheetResults['skipped'];
                $grandTotal['errors']    = array_merge($grandTotal['errors'], $sheetResults['errors']);
                $grandTotal['created_crews'] = array_merge($grandTotal['created_crews'], $sheetResults['created_crews']);
                $grandTotal['updated_crews'] = array_merge($grandTotal['updated_crews'], $sheetResults['updated_crews']);
            }

            $this->db->commit();

            // Cleanup
            @unlink($filePath);
            unset($_SESSION['import_file'], $_SESSION['import_sheets']);

            $grandTotal['created_vessels']  = array_values(array_unique($lookups['_new_vessels'] ?? []));
            $grandTotal['created_clients']  = array_values(array_unique($lookups['_new_clients'] ?? []));
            $grandTotal['created_contracts'] = $lookups['_new_contracts'] ?? 0;

            $data = [
                'title'       => 'Smart Import — Hasil Import',
                'currentPage' => 'smart-import',
                'results'     => $grandTotal,
                'sheetResults'=> $allResults,
                'totalSheets' => count($allResults),
                'flash'       => $this->getFlash()
            ];

            return $this->view('smart_import/result_modern', $data);

        } catch (\Exception $e) {
            $this->db->rollback();
            $this->setFlash('error', 'Import gagal: ' . $e->getMessage());
            $this->redirect('SmartImport');
        }
    }

    /**
     * Import a single row — UPSERT logic (create or update)
     */
    private function importRow($row, $mapping, &$lookups, &$results, $rowNum, $sheetName)
    {
        $val = function($field) use ($row, $mapping) {
            $col = $mapping[$field] ?? null;
            if (!$col) return null;
            $v = trim((string)($row[$col] ?? ''));
            return $v === '' ? null : $v;
        };

        // --- 1. Build crew name ---
        $firstName = $val('first_name') ?? '';
        $lastName  = $val('last_name') ?? '';
        $fullName  = trim($firstName . ' ' . $lastName);
        if (empty($fullName)) {
            $results['skipped']++;
            return;
        }

        // --- 2. Parse all fields ---
        $birthDate = $this->parseDate($val('birth_date'));
        $address   = $val('address');
        $phone     = $val('phone');
        $email     = $val('email');
        $emergencyPhone    = $val('emergency_phone');
        $emergencyRelation = $val('emergency_relation');
        $bankAccount = $val('bank_account');
        $bankHolder  = $val('bank_holder');
        $bankName    = $val('bank_name');
        $pic         = $val('pic');
        $note        = $val('note');
        $crewNotes   = ($pic ? "PIC: $pic" : '') . ($note ? ($pic ? "\n" : '') . $note : '') ?: null;

        $rankName      = $val('certificate');
        $companyName   = $val('company');
        $vesselName    = $val('vessel');
        $imoNumber     = $val('imo_number');
        $flag          = $val('flag');
        $portRegistry  = $val('port_of_registry');
        $status        = $val('status');
        $noteOffDate   = $val('note_off_date');
        $joinDate      = $this->parseDate($val('joint_date'));
        $finishDate    = $this->parseDate($val('finish_contract'));

        $passportNo    = $val('passport_number');
        $passportExp   = $this->parseDate($val('passport_exp'));
        $seamanNo      = $val('seaman_number');
        $seamanExp     = $this->parseDate($val('seaman_exp'));
        $mcuDate       = $this->parseDate($val('mcu'));

        $currencyCode  = strtoupper($val('currency') ?: 'IDR');
        if ($currencyCode === 'RM') $currencyCode = 'MYR';
        if ($currencyCode === 'RP') $currencyCode = 'IDR';
        $salaryPayroll = $this->parseNumber($val('salary_payroll'));
        $salaryInvoice = $this->parseNumber($val('salary_invoice'));

        // --- 3. Resolve lookups ---
        $rankId   = $this->findOrNullRank($rankName, $lookups);
        $clientId = $this->findOrCreateClient($companyName, $lookups);
        $vesselId = $this->findOrCreateVessel($vesselName, $imoNumber, $flag, $portRegistry, $companyName, $lookups);
        $crewStatus = $this->mapStatus($status);
        $currencyId = $lookups['currencies_map'][$currencyCode] ?? ($lookups['currencies_map']['IDR'] ?? 2);

        // Ensure contract.client_id matches vessel.client_id (source of truth)
        if ($vesselId) {
            $stmt = $this->db->prepare("SELECT client_id FROM vessels WHERE id = ?");
            $stmt->bind_param('i', $vesselId);
            $stmt->execute();
            $vesselRow = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($vesselRow && $vesselRow['client_id']) {
                $clientId = $vesselRow['client_id'];
            }
        }

        // --- 4. Smart duplicate check: name + birth_date ---
        $existing = null;
        if ($birthDate) {
            $stmt = $this->db->prepare("SELECT * FROM crews WHERE full_name = ? AND birth_date = ? LIMIT 1");
            $stmt->bind_param('ss', $fullName, $birthDate);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
        if (!$existing) {
            // Try by name only
            $stmt = $this->db->prepare("SELECT * FROM crews WHERE full_name = ? LIMIT 1");
            $stmt->bind_param('s', $fullName);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }

        if ($existing) {
            // === CHECK IF DATA CHANGED ===
            $changed = false;
            $crewId = $existing['id'];

            // Compare key fields
            $comparisons = [
                'birth_date'         => $birthDate,
                'address'            => $address,
                'phone'              => $phone,
                'email'              => $email,
                'emergency_phone'    => $emergencyPhone,
                'emergency_relation' => $emergencyRelation,
                'bank_account'       => $bankAccount,
                'bank_holder'        => $bankHolder,
                'bank_name'          => $bankName,
                'current_rank_id'    => $rankId,
                'status'             => $crewStatus,
            ];

            foreach ($comparisons as $field => $newVal) {
                if ($newVal !== null && (string)($existing[$field] ?? '') !== (string)$newVal) {
                    $changed = true;
                    break;
                }
            }

            if ($changed) {
                // UPDATE crew
                $sql = "UPDATE crews SET
                    birth_date = COALESCE(?, birth_date),
                    address = COALESCE(?, address),
                    phone = COALESCE(?, phone),
                    email = COALESCE(?, email),
                    emergency_phone = COALESCE(?, emergency_phone),
                    emergency_relation = COALESCE(?, emergency_relation),
                    bank_account = COALESCE(?, bank_account),
                    bank_holder = COALESCE(?, bank_holder),
                    bank_name = COALESCE(?, bank_name),
                    current_rank_id = COALESCE(?, current_rank_id),
                    status = COALESCE(?, status),
                    notes = COALESCE(?, notes),
                    updated_at = NOW()
                    WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('sssssssssissi',
                    $birthDate, $address, $phone, $email,
                    $emergencyPhone, $emergencyRelation,
                    $bankAccount, $bankHolder, $bankName,
                    $rankId, $crewStatus, $crewNotes, $crewId
                );
                $stmt->execute();
                $stmt->close();

                $results['updated']++;
                $results['updated_crews'][] = ['id' => $crewId, 'name' => $fullName];
            } else {
                // Crew data is same, check if contract needs creating
            }

            // Even if crew data is same, check if contract needs creating for this vessel
            if (!$changed) {
                // Check if contract exists for this crew+vessel
                $contractExists = false;
                if ($vesselId) {
                    $stmt = $this->db->prepare("SELECT id FROM contracts WHERE crew_id = ? AND vessel_id = ? LIMIT 1");
                    $stmt->bind_param('ii', $crewId, $vesselId);
                    $stmt->execute();
                    $contractExists = $stmt->get_result()->fetch_assoc() ? true : false;
                    $stmt->close();
                }

                if ($contractExists) {
                    $results['skipped']++;
                    return; // Truly identical — crew + contract already exist
                }
                // No contract for this vessel → fall through to create contract below
                // Count as updated since we're adding a new contract
                $results['updated']++;
                $results['updated_crews'][] = ['id' => $crewId, 'name' => $fullName . ' (kontrak baru)'];
            }

        } else {
            // === CREATE NEW CREW ===
            $employeeId = 'CRW-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

            $crewSql = "INSERT INTO crews (employee_id, full_name, birth_date, address, phone, email,
                         emergency_phone, emergency_relation, bank_account, bank_holder, bank_name,
                         current_rank_id, status, source, notes, created_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'manual', ?, NOW())";
            $crewStmt = $this->db->prepare($crewSql);
            $crewStmt->bind_param('sssssssssssiss',
                $employeeId, $fullName, $birthDate, $address, $phone, $email,
                $emergencyPhone, $emergencyRelation,
                $bankAccount, $bankHolder, $bankName,
                $rankId, $crewStatus, $crewNotes
            );
            $crewStmt->execute();
            $crewId = $this->db->insert_id;
            $crewStmt->close();

            $results['success']++;
            $results['created_crews'][] = ['id' => $crewId, 'name' => $fullName, 'rank' => $rankName];
        }

        // --- 5. Auto-create contract if vessel exists (rank optional) ---
        if ($vesselId) {
            // Check existing contract for this crew + vessel
            $stmt = $this->db->prepare("SELECT id FROM contracts WHERE crew_id = ? AND vessel_id = ? LIMIT 1");
            $stmt->bind_param('ii', $crewId, $vesselId);
            $stmt->execute();
            $existingContract = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Determine correct contract status based on crew status
            $contractStatus = 'active';
            if ($crewStatus === 'on_board' || $crewStatus === 'onboard') $contractStatus = 'active';
            if ($crewStatus === 'available') $contractStatus = 'completed';
            if ($crewStatus === 'terminated') $contractStatus = 'terminated';

            if (!$existingContract) {
                $contractNo = 'CTR-' . date('Ym') . '-' . str_pad($crewId, 4, '0', STR_PAD_LEFT) . '-V' . $vesselId;

                $cSql = "INSERT INTO contracts (contract_no, crew_id, crew_name, vessel_id, client_id, rank_id,
                         contract_type, status, sign_on_date, sign_off_date, notes, created_by, created_at)
                         VALUES (?, ?, ?, ?, ?, ?, 'fixed', ?, ?, ?, ?, ?, NOW())";
                $cStmt = $this->db->prepare($cSql);
                $userId = $this->getCurrentUser()['id'] ?? null;
                $cStmt->bind_param('sisiiissssi',
                    $contractNo, $crewId, $fullName, $vesselId, $clientId, $rankId,
                    $contractStatus, $joinDate, $finishDate, $noteOffDate, $userId
                );
                $cStmt->execute();
                $contractId = $this->db->insert_id;
                $cStmt->close();

                $lookups['_new_contracts'] = ($lookups['_new_contracts'] ?? 0) + 1;

                // --- 6. Create salary record ---
                if ($salaryPayroll > 0 || $salaryInvoice > 0) {
                    $sSql = "INSERT INTO contract_salaries (contract_id, currency_id, basic_salary, client_rate, created_at)
                             VALUES (?, ?, ?, ?, NOW())";
                    $sStmt = $this->db->prepare($sSql);
                    $sStmt->bind_param('iidd', $contractId, $currencyId, $salaryPayroll, $salaryInvoice);
                    $sStmt->execute();
                    $sStmt->close();
                }
            } else {
                $contractId = $existingContract['id'];
                // Update contract dates, status, and rank if they changed
                $uSql = "UPDATE contracts SET
                    sign_on_date = COALESCE(?, sign_on_date),
                    sign_off_date = COALESCE(?, sign_off_date),
                    status = ?,
                    rank_id = COALESCE(?, rank_id),
                    notes = COALESCE(?, notes),
                    updated_at = NOW()
                    WHERE id = ?";
                $uStmt = $this->db->prepare($uSql);
                $uStmt->bind_param('sssisi', $joinDate, $finishDate, $contractStatus, $rankId, $noteOffDate, $contractId);
                $uStmt->execute();
                $uStmt->close();

                // Update salary if changed
                if ($salaryPayroll > 0 || $salaryInvoice > 0) {
                    $stmt = $this->db->prepare("SELECT id FROM contract_salaries WHERE contract_id = ? LIMIT 1");
                    $stmt->bind_param('i', $contractId);
                    $stmt->execute();
                    $existingSalary = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    if ($existingSalary) {
                        $stmt = $this->db->prepare("UPDATE contract_salaries SET basic_salary = ?, client_rate = ?, currency_id = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->bind_param('ddii', $salaryPayroll, $salaryInvoice, $currencyId, $existingSalary['id']);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $stmt = $this->db->prepare("INSERT INTO contract_salaries (contract_id, currency_id, basic_salary, client_rate, created_at) VALUES (?, ?, ?, ?, NOW())");
                        $stmt->bind_param('iidd', $contractId, $currencyId, $salaryPayroll, $salaryInvoice);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }

        // --- 7. Sync documents ---
        $this->syncDocument($crewId, 'PASSPORT', 'Passport', $passportNo, $passportExp);
        $this->syncDocument($crewId, 'SEAMAN_BOOK', 'Seaman Book', $seamanNo, $seamanExp);
        if ($mcuDate) {
            $this->syncDocument($crewId, 'MEDICAL', 'Medical Certificate (MCU)', null, $mcuDate);
        }
    }

    // =====================
    // HELPER METHODS
    // =====================

    /**
     * Detect header rows from sheet data.
     * Row 1 = main headers (NAME, RANK, COMPANY, VESSEL, etc.)
     * Row 2 = sub-headers for composite columns (PHONE NUMBER, RELATION, NUMBER, EXP, etc.)
     * Row 3+ = data (sometimes Row 3 is empty)
     */
    private function detectHeaders($rows)
    {
        $allRows = [];
        $idx = 0;
        foreach ($rows as $row) {
            $idx++;
            $allRows[$idx] = $row;
            if ($idx >= 5) break;
        }

        $row1 = $allRows[1] ?? [];
        $row2 = $allRows[2] ?? [];

        // Build headers: use Row 1 as primary, Row 2 fills sub-columns
        $headers = [];
        $lastGroup = '';
        foreach ($row1 as $col => $val) {
            $main = strtoupper(trim((string)($val ?? '')));
            $sub  = strtoupper(trim((string)($row2[$col] ?? '')));

            if (!empty($main)) $lastGroup = $main;

            if (!empty($main) && empty($sub)) {
                // Simple header (NAME, RANK, COMPANY, VESSEL, STATUS, etc.)
                $headers[$col] = $main;
            } elseif (!empty($main) && !empty($sub) && $main !== $sub) {
                // Main + different sub (e.g. EMERGENCY CP + PHONE NUMBER)
                $headers[$col] = $main . ' — ' . $sub;
            } elseif (empty($main) && !empty($sub)) {
                // Sub-header only — belongs to previous group
                $headers[$col] = $lastGroup . ' — ' . $sub;
            } elseif (!empty($main) && $main === $sub) {
                // Same text in both rows — just use the main
                $headers[$col] = $main;
            }
        }

        // Find where data starts (skip completely empty rows after headers)
        $headerRowCount = 2;
        for ($i = 3; $i <= 4; $i++) {
            if (!isset($allRows[$i])) break;
            $hasData = false;
            foreach ($allRows[$i] as $col => $cell) {
                if ($col === 'A') continue; // Skip row number column
                $v = trim((string)($cell ?? ''));
                if (!empty($v) && $v !== '#REF!') { $hasData = true; break; }
            }
            if ($hasData) break;
            $headerRowCount = $i; // empty row 3 is still "header area"
        }

        return ['headers' => $headers, 'headerRowCount' => $headerRowCount];
    }

    private function autoDetectMapping($headers)
    {
        $mapping = [];

        // ==== STEP 1: Header text matching ====
        foreach ($headers as $col => $header) {
            $h = strtoupper(trim($header));
            if (in_array($h, ['NO', 'NO.', '#'])) continue;

            if ($h === 'NAME' && !isset($mapping['first_name']))
                $mapping['first_name'] = $col;
            elseif (($h === 'RANK' || $h === 'CERTIFICATE' || str_contains($h, 'RANK')) && !isset($mapping['certificate']))
                $mapping['certificate'] = $col;
            elseif ($h === 'COMPANY' && !isset($mapping['company']))
                $mapping['company'] = $col;
            elseif ($h === 'VESSEL' && !isset($mapping['vessel']))
                $mapping['vessel'] = $col;
            elseif (str_contains($h, 'IMO') && !isset($mapping['imo_number']))
                $mapping['imo_number'] = $col;
            elseif ($h === 'FLAG' && !isset($mapping['flag']))
                $mapping['flag'] = $col;
            elseif (str_contains($h, 'PORT') && !isset($mapping['port_of_registry']))
                $mapping['port_of_registry'] = $col;
            elseif ($h === 'STATUS' && !isset($mapping['status']))
                $mapping['status'] = $col;
            elseif (str_contains($h, 'NOTE') && str_contains($h, 'OFF') && !isset($mapping['note_off_date']))
                $mapping['note_off_date'] = $col;
            elseif ((str_contains($h, 'JOINT') || str_contains($h, 'JOIN')) && !isset($mapping['joint_date']))
                $mapping['joint_date'] = $col;
            elseif (str_contains($h, 'FINISH') && !isset($mapping['finish_contract']))
                $mapping['finish_contract'] = $col;
            elseif (str_contains($h, 'BIRTH') && !isset($mapping['birth_date']))
                $mapping['birth_date'] = $col;
            elseif (($h === 'ADDRESS' || str_contains($h, 'ALAMAT')) && !isset($mapping['address']))
                $mapping['address'] = $col;
            elseif ($h === 'PHONE NUMBER' && !isset($mapping['phone']))
                $mapping['phone'] = $col;
            elseif (str_contains($h, 'EMERGENCY') && str_contains($h, 'PHONE') && !isset($mapping['emergency_phone']))
                $mapping['emergency_phone'] = $col;
            elseif (str_contains($h, 'EMERGENCY') && str_contains($h, 'RELATION') && !isset($mapping['emergency_relation']))
                $mapping['emergency_relation'] = $col;
            elseif (str_contains($h, 'PASSPORT') && str_contains($h, 'NUMBER') && !isset($mapping['passport_number']))
                $mapping['passport_number'] = $col;
            elseif (str_contains($h, 'PASSPORT') && str_contains($h, 'EXP') && !isset($mapping['passport_exp']))
                $mapping['passport_exp'] = $col;
            elseif (str_contains($h, 'SEAMAN') && str_contains($h, 'NUMBER') && !isset($mapping['seaman_number']))
                $mapping['seaman_number'] = $col;
            elseif (str_contains($h, 'SEAMAN') && str_contains($h, 'EXP') && !isset($mapping['seaman_exp']))
                $mapping['seaman_exp'] = $col;
            elseif ($h === 'MCU' && !isset($mapping['mcu']))
                $mapping['mcu'] = $col;
            elseif ($h === 'PIC' && !isset($mapping['pic']))
                $mapping['pic'] = $col;
            elseif ((str_contains($h, 'ACCOUNT') && str_contains($h, 'NUMBER')) && !isset($mapping['bank_account']))
                $mapping['bank_account'] = $col;
            elseif ($h === 'BANK ACCOUNT' && !isset($mapping['bank_account']))
                $mapping['bank_account'] = $col;
            elseif (str_contains($h, 'NAME OF ACCOUNT') && !isset($mapping['bank_holder']))
                $mapping['bank_holder'] = $col;
            elseif (str_contains($h, 'NAME OF BANK') && !isset($mapping['bank_name']))
                $mapping['bank_name'] = $col;
            elseif ($h === 'EMAIL' && !isset($mapping['email']))
                $mapping['email'] = $col;
            elseif (str_contains($h, 'CURRENCY') && !isset($mapping['currency']))
                $mapping['currency'] = $col;
            elseif (str_contains($h, 'PAYROLL') && !isset($mapping['salary_payroll']))
                $mapping['salary_payroll'] = $col;
            elseif (str_contains($h, 'INVOICE') && !isset($mapping['salary_invoice']))
                $mapping['salary_invoice'] = $col;
            elseif ($h === 'NOTE' && !isset($mapping['note']))
                $mapping['note'] = $col;
        }

        // ==== STEP 2: Column position fallback (known DATABASE CREW IOS layout) ====
        $fallback = [
            'B'=>'first_name', 'C'=>'certificate', 'E'=>'company', 'F'=>'vessel',
            'G'=>'imo_number', 'H'=>'flag', 'I'=>'port_of_registry', 'K'=>'status',
            'L'=>'note_off_date', 'M'=>'joint_date', 'N'=>'finish_contract',
            'O'=>'birth_date', 'P'=>'address', 'Q'=>'phone',
            'R'=>'emergency_phone', 'S'=>'emergency_relation',
            'T'=>'passport_number', 'U'=>'passport_exp',
            'V'=>'seaman_number', 'W'=>'seaman_exp',
            'X'=>'mcu', 'Y'=>'pic',
            'Z'=>'bank_account', 'AA'=>'bank_holder', 'AB'=>'bank_name',
            'AC'=>'email', 'AD'=>'currency',
            'AE'=>'salary_payroll', 'AF'=>'salary_invoice', 'AG'=>'note',
        ];
        foreach ($fallback as $col => $field) {
            if (!isset($mapping[$field])) $mapping[$field] = $col;
        }

        return $mapping;
    }

    private function loadLookups()
    {
        $lookups = [];

        // Ranks
        $result = $this->db->query("SELECT id, name FROM ranks WHERE is_active = 1");
        $lookups['ranks'] = [];
        $lookups['ranks_map'] = [];
        while ($r = $result->fetch_assoc()) {
            $lookups['ranks'][] = $r;
            $lookups['ranks_map'][strtolower($r['name'])] = $r['id'];
        }

        // Vessels (with normalized name map and IMO map for dedup)
        $result = $this->db->query("SELECT id, name, imo_number FROM vessels");
        $lookups['vessels'] = [];
        $lookups['vessels_map'] = [];           // exact lowercase name -> id
        $lookups['vessels_normalized_map'] = []; // normalized (no spaces/dots) -> id
        $lookups['vessels_imo_map'] = [];        // imo_number -> id
        while ($r = $result->fetch_assoc()) {
            $lookups['vessels'][] = $r;
            $lookups['vessels_map'][strtolower($r['name'])] = $r['id'];
            // Normalized name: remove spaces, dots, commas, lowercase
            $normalized = strtolower(str_replace([' ', '.', ','], '', $r['name']));
            $lookups['vessels_normalized_map'][$normalized] = $r['id'];
            // IMO map
            if (!empty($r['imo_number']) && $r['imo_number'] !== 'KOSONG') {
                $lookups['vessels_imo_map'][$r['imo_number']] = $r['id'];
            }
        }

        // Clients
        $result = $this->db->query("SELECT id, name FROM clients WHERE is_active = 1");
        $lookups['clients'] = [];
        $lookups['clients_map'] = [];
        while ($r = $result->fetch_assoc()) {
            $lookups['clients'][] = $r;
            $lookups['clients_map'][strtolower($r['name'])] = $r['id'];
        }

        // Currencies
        $result = $this->db->query("SELECT id, code, name FROM currencies");
        $lookups['currencies'] = [];
        $lookups['currencies_map'] = [];
        while ($r = $result->fetch_assoc()) {
            $lookups['currencies'][] = $r;
            $lookups['currencies_map'][$r['code']] = $r['id'];
        }
        $lookups['currencies_map']['RM'] = $lookups['currencies_map']['MYR'] ?? 1;

        // Flag states
        $result = $this->db->query("SELECT id, code, name FROM flag_states");
        $lookups['flags'] = [];
        $lookups['flags_map'] = [];
        while ($r = $result->fetch_assoc()) {
            $lookups['flags'][] = $r;
            $lookups['flags_map'][strtolower($r['name'])] = $r['id'];
        }

        // Existing crews (for deduplication)
        $result = $this->db->query("SELECT id, full_name, birth_date FROM crews");
        $lookups['crews_map'] = [];
        while ($r = $result->fetch_assoc()) {
            $key = strtolower($r['full_name']);
            $lookups['crews_map'][$key] = $r;
        }

        // Track new entities
        $lookups['_new_vessels'] = [];
        $lookups['_new_clients'] = [];
        $lookups['_new_contracts'] = 0;

        return $lookups;
    }

    private function validateRows($dataRows, $mapping, $lookups, $headers)
    {
        $results = ['rows' => [], 'valid_count' => 0, 'error_count' => 0, 'warn_count' => 0];

        $val = function($row, $field) use ($mapping) {
            $col = $mapping[$field] ?? null;
            if (!$col) return null;
            return trim((string)($row[$col] ?? ''));
        };

        $seenNames = [];

        foreach ($dataRows as $i => $row) {
            $rowResult = ['errors' => [], 'warnings' => [], 'info' => [], 'status' => 'ok'];

            $firstName = $val($row, 'first_name');
            $lastName  = $val($row, 'last_name');
            $fullName  = trim($firstName . ' ' . $lastName);

            if (empty($fullName)) {
                $rowResult['errors'][] = 'Nama wajib diisi';
            }

            // Rank info
            $rankName = $val($row, 'certificate');
            if (!empty($rankName) && !isset($lookups['ranks_map'][strtolower($rankName)])) {
                $fuzzyFound = false;
                foreach ($lookups['ranks_map'] as $rName => $rId) {
                    if (str_contains($rName, strtolower($rankName)) || str_contains(strtolower($rankName), $rName)) {
                        $rowResult['info'][] = "Rank → $rankName (fuzzy match)";
                        $fuzzyFound = true;
                        break;
                    }
                }
                if (!$fuzzyFound) {
                    $rowResult['info'][] = "Rank '$rankName' — akan dicocokan otomatis";
                }
            }

            // Vessel info (check normalized name and IMO too)
            $vessel = $val($row, 'vessel');
            $vesselImo = $val($row, 'imo_number');
            if (!empty($vessel)) {
                $vesselKey = strtolower($vessel);
                $vesselNorm = strtolower(str_replace([' ', '.', ','], '', $vessel));
                if (isset($lookups['vessels_map'][$vesselKey])) {
                    // Exact match - ok
                } elseif (isset($lookups['vessels_normalized_map'][$vesselNorm])) {
                    $rowResult['info'][] = "🚢 Vessel '$vessel' → cocok dengan kapal yang ada (nama serupa)";
                } elseif (!empty($vesselImo) && $vesselImo !== 'KOSONG' && isset($lookups['vessels_imo_map'][$vesselImo])) {
                    $rowResult['info'][] = "🚢 Vessel '$vessel' → cocok via IMO $vesselImo";
                } else {
                    $rowResult['info'][] = "🚢 Vessel '$vessel' → baru (auto-create)";
                }
            }

            // Client info
            $client = $val($row, 'company');
            if (!empty($client) && !isset($lookups['clients_map'][strtolower($client)])) {
                $rowResult['info'][] = "🏢 Client '$client' → baru (auto-create)";
            }

            // Duplicate check
            if (!empty($fullName)) {
                $nameKey = strtolower($fullName);
                if (isset($lookups['crews_map'][$nameKey])) {
                    $rowResult['warnings'][] = "Kru '$fullName' sudah ada → akan di-update jika ada perubahan";
                } elseif (isset($seenNames[$nameKey])) {
                    $rowResult['warnings'][] = "Duplikat dalam file (muncul di baris sebelumnya)";
                }
                $seenNames[$nameKey] = true;
            }

            if (!empty($rowResult['errors'])) {
                $rowResult['status'] = 'error';
                $results['error_count']++;
            } elseif (!empty($rowResult['warnings'])) {
                $rowResult['status'] = 'warning';
                $results['warn_count']++;
            } else {
                $rowResult['status'] = 'ok';
                $results['valid_count']++;
            }

            $results['rows'][] = $rowResult;
        }

        return $results;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;
        $value = trim((string)$value);

        // Excel serial number
        if (is_numeric($value) && $value > 30000 && $value < 70000) {
            $unix = ($value - 25569) * 86400;
            return date('Y-m-d', $unix);
        }

        // Try common formats
        foreach (['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $value);
            if ($dt && $dt->format($fmt) === $value) return $dt->format('Y-m-d');
        }

        // Relaxed format matching
        foreach (['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-m-Y'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $value);
            if ($dt) return $dt->format('Y-m-d');
        }

        $ts = strtotime($value);
        if ($ts) return date('Y-m-d', $ts);

        return null;
    }

    private function parseNumber($value)
    {
        if (empty($value)) return 0;
        return floatval(str_replace([',', ' ', '.'], ['', '', ''], $value));
    }

    private function mapStatus($status)
    {
        if (empty($status)) return 'available';
        $s = strtolower(trim($status));
        $map = [
            'on'          => 'on_board',
            'active'      => 'on_board',
            'on board'    => 'on_board',
            'onboard'     => 'on_board',
            'on_board'    => 'on_board',
            'on-board'    => 'on_board',
            'boarding'    => 'on_board',
            'joined'      => 'on_board',
            'join'        => 'on_board',
            'standby'     => 'standby',
            'stand by'    => 'standby',
            'waiting'     => 'standby',
            'off'         => 'available',
            'off board'   => 'available',
            'available'   => 'available',
            'ex crew'     => 'available',
            'resigned'    => 'terminated',
            'terminated'  => 'terminated',
            'blacklisted' => 'terminated',
            'finish'      => 'available',
            'completed'   => 'available',
            'done'        => 'available',
        ];
        return $map[$s] ?? 'available';
    }

    private function findOrNullRank($name, &$lookups)
    {
        if (empty($name)) return null;
        $key = strtolower(trim($name));
        if (isset($lookups['ranks_map'][$key])) return $lookups['ranks_map'][$key];

        // Common abbreviation aliases used in maritime Excel files
        // Includes Indonesian terms (MUALIM, MASINIS, NAKHODA) and common typos
        $aliases = [
            'master' => ['captain','master / captain','master/captain','nahkoda','nakhoda'],
            'captain' => ['master','master / captain','master/captain','nahkoda','nakhoda'],
            'nakhoda' => ['captain','master','master / captain'],
            'nahkoda' => ['captain','master','master / captain'],
            'c/o' => ['chief officer'],
            'chief officer' => ['c/o','chief mate','1st officer','1st mate','mualim i','mualim 1'],
            'chieff officer' => ['chief officer','c/o'],    // typo
            'chuef officer' => ['chief officer','c/o'],     // typo
            'c/e' => ['chief engineer'],
            'chief engineer' => ['c/e','kkm'],
            'chief enginer' => ['chief engineer','c/e'],    // typo
            'kkm' => ['chief engineer','c/e'],              // Indonesian
            '2/o' => ['2nd officer','second officer'],
            '2nd officer' => ['2/o','second officer','mualim ii','mualim 2'],
            'mualim ii' => ['2nd officer','2/o','second officer'],
            'mualim 2' => ['2nd officer','2/o','second officer'],
            'mualim i' => ['chief officer','c/o','1st officer'],
            'mualim 1' => ['chief officer','c/o','1st officer'],
            '3/o' => ['3rd officer','third officer'],
            '3rd officer' => ['3/o','third officer','mualim iii','mualim 3'],
            'mualim iii' => ['3rd officer','3/o'],
            'mualim 3' => ['3rd officer','3/o'],
            '2/e' => ['2nd engineer','second engineer'],
            '2nd engineer' => ['2/e','second engineer','masinis ii','masinis 2'],
            'masinis ii' => ['2nd engineer','2/e'],
            'masinis 2' => ['2nd engineer','2/e'],
            '3/e' => ['3rd engineer','third engineer'],
            '3rd engineer' => ['3/e','third engineer','masinis iii','masinis 3'],
            'masinis iii' => ['3rd engineer','3/e'],
            'masinis 3' => ['3rd engineer','3/e'],
            '4/e' => ['4th engineer','fourth engineer'],
            '4th engineer' => ['4/e','fourth engineer','masinis iv','masinis 4'],
            'masinis iv' => ['4th engineer','4/e'],
            'masinis 4' => ['4th engineer','4/e'],
            'ab' => ['ab (able seaman)','able seaman','able bodied','a/b','juru mudi'],
            'able body' => ['ab (able seaman)','able seaman','ab'],
            'seaman able' => ['ab (able seaman)','able seaman','ab'],
            'juru mudi' => ['ab (able seaman)','able seaman','ab'],
            'os' => ['os (ordinary seaman)','ordinary seaman','o/s','kelasi'],
            'kelasi' => ['os (ordinary seaman)','ordinary seaman','os'],
            'oiler' => ['oiler','motor man','juru oli'],
            'juru oli' => ['oiler'],
            'bosun' => ['boatswain','bo\'sun','bos\'n','serang'],
            'serang' => ['bosun','boatswain'],
            'eto' => ['eto (electro-technical officer)','electro-technical officer','electro technical'],
            'cook' => ['chief cook','cook','koki'],
            'koki' => ['chief cook','cook'],
            '2nd cook' => ['second cook','assistant cook'],
            'messman' => ['mess boy','messboy'],
            'fitter' => ['fitter','engine fitter'],
            'wiper' => ['wiper','engine wiper'],
            'deck cadet' => ['cadet deck','d/cadet','cadet (deck)','taruna deck'],
            'engine cadet' => ['cadet engine','e/cadet','cadet (engine)','taruna mesin'],
            'electrician' => ['electrician','electrical'],
            'steward' => ['steward','chief steward'],
            'radio officer' => ['radio officer','r/o','sparks'],
        ];

        // Check if the key matches any alias
        if (isset($aliases[$key])) {
            foreach ($aliases[$key] as $altName) {
                foreach ($lookups['ranks_map'] as $rName => $rId) {
                    if ($rName === $altName || str_contains($rName, $altName) || str_contains($altName, $rName)) {
                        return $rId;
                    }
                }
            }
        }

        // Reverse: check if any alias maps to the key
        foreach ($aliases as $alias => $altNames) {
            if (in_array($key, $altNames) || $alias === $key) {
                // try the alias itself
                foreach ($lookups['ranks_map'] as $rName => $rId) {
                    if ($rName === $alias || str_contains($rName, $alias) || str_contains($alias, $rName)) {
                        return $rId;
                    }
                }
                // try other alt names
                foreach ($altNames as $an) {
                    foreach ($lookups['ranks_map'] as $rName => $rId) {
                        if ($rName === $an || str_contains($rName, $an) || str_contains($an, $rName)) {
                            return $rId;
                        }
                    }
                }
            }
        }

        // Last resort: substring match
        foreach ($lookups['ranks_map'] as $rName => $rId) {
            if (str_contains($rName, $key) || str_contains($key, $rName)) {
                return $rId;
            }
        }
        return null;
    }

    private function findOrCreateVessel($name, $imo, $flag, $port, $company, &$lookups)
    {
        if (empty($name)) return null;
        $key = strtolower(trim($name));

        // Level 1: Exact name match
        if (isset($lookups['vessels_map'][$key])) return $lookups['vessels_map'][$key];

        // Level 2: Normalized name match (ignore spaces, dots, commas)
        $normalized = strtolower(str_replace([' ', '.', ','], '', $name));
        if (isset($lookups['vessels_normalized_map'][$normalized])) {
            // Found match via normalized name - use existing vessel
            $existingId = $lookups['vessels_normalized_map'][$normalized];
            $lookups['vessels_map'][$key] = $existingId; // cache for future
            return $existingId;
        }

        // Level 3: IMO number match
        if (!empty($imo) && $imo !== 'KOSONG' && isset($lookups['vessels_imo_map'][$imo])) {
            $existingId = $lookups['vessels_imo_map'][$imo];
            $lookups['vessels_map'][$key] = $existingId; // cache for future
            return $existingId;
        }

        // No match found - create new vessel
        $flagId = null;
        if (!empty($flag)) {
            $flagKey = strtolower(trim($flag));
            $flagId = $lookups['flags_map'][$flagKey] ?? null;
        }

        $clientId = null;
        if (!empty($company)) {
            $clientId = $this->findOrCreateClient($company, $lookups);
        }

        $notes = !empty($port) ? "Port of Registry: $port" : null;

        $stmt = $this->db->prepare("INSERT INTO vessels (name, imo_number, flag_state_id, client_id, notes, status, is_active, created_at) VALUES (?, ?, ?, ?, ?, 'active', 1, NOW())");
        $stmt->bind_param('ssiss', $name, $imo, $flagId, $clientId, $notes);
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();

        // Update all lookup maps
        $lookups['vessels_map'][$key] = $id;
        $lookups['vessels_normalized_map'][$normalized] = $id;
        if (!empty($imo) && $imo !== 'KOSONG') {
            $lookups['vessels_imo_map'][$imo] = $id;
        }
        $lookups['_new_vessels'][] = $name;
        return $id;
    }

    private function findOrCreateClient($name, &$lookups)
    {
        if (empty($name)) return null;
        $key = strtolower(trim($name));

        if (isset($lookups['clients_map'][$key])) return $lookups['clients_map'][$key];

        // Fuzzy match
        foreach ($lookups['clients_map'] as $cName => $cId) {
            if (str_contains($cName, $key) || str_contains($key, $cName)) {
                return $cId;
            }
        }

        // Auto-create client
        $stmt = $this->db->prepare("INSERT INTO clients (name, is_active, created_at) VALUES (?, 1, NOW())");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();

        $lookups['clients_map'][$key] = $id;
        $lookups['_new_clients'][] = $name;
        return $id;
    }

    /**
     * Sync document — insert if not exists, update if changed
     */
    private function syncDocument($crewId, $type, $name, $number, $expiryDate)
    {
        if (empty($number) && empty($expiryDate)) return;

        $status = 'valid';
        if ($expiryDate && strtotime($expiryDate) < time()) {
            $status = 'expired';
        } elseif ($expiryDate && strtotime($expiryDate) < strtotime('+180 days')) {
            $status = 'expiring_soon';
        }

        // Check if document exists for this crew+type
        $stmt = $this->db->prepare("SELECT id, document_number, expiry_date FROM crew_documents WHERE crew_id = ? AND document_type = ? LIMIT 1");
        $stmt->bind_param('is', $crewId, $type);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($existing) {
            // Update only if changed
            $numChanged = $number && $number !== ($existing['document_number'] ?? '');
            $expChanged = $expiryDate && $expiryDate !== ($existing['expiry_date'] ?? '');
            if ($numChanged || $expChanged) {
                $stmt = $this->db->prepare("UPDATE crew_documents SET document_number = COALESCE(?, document_number), expiry_date = COALESCE(?, expiry_date), status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->bind_param('sssi', $number, $expiryDate, $status, $existing['id']);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // Insert new
            $stmt = $this->db->prepare("INSERT INTO crew_documents (crew_id, document_type, document_name, document_number, expiry_date, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('isssss', $crewId, $type, $name, $number, $expiryDate, $status);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Get purge stats — returns JSON with counts of data that will be deleted
     */
    public function purgeStats()
    {
        $this->requirePermission('crews', 'delete');
        header('Content-Type: application/json');

        $stats = [];
        $tables = ['crew_documents', 'contract_salaries', 'contracts', 'crews', 'vessels', 'clients'];
        foreach ($tables as $t) {
            $r = $this->db->query("SELECT COUNT(*) as c FROM $t");
            $stats[$t] = (int)($r->fetch_assoc()['c'] ?? 0);
        }

        echo json_encode(['success' => true, 'stats' => $stats]);
        exit;
    }

    /**
     * Purge ALL imported data — deletes in FK-safe order
     * Requires POST + confirmation token
     */
    public function purgeAll()
    {
        $this->requirePermission('crews', 'delete');
        if (!$this->isPost()) {
            $this->setFlash('error', 'Invalid request method');
            $this->redirect('SmartImport');
        }

        $confirm = trim($_POST['confirm_text'] ?? '');
        if ($confirm !== 'HAPUS') {
            $this->setFlash('error', 'Konfirmasi tidak valid. Ketik "HAPUS" untuk menghapus semua data.');
            $this->redirect('SmartImport');
        }

        try {
            $this->db->begin_transaction();

            $deleted = [];

            // Delete in FK-safe order
            $tables = [
                'crew_documents'    => 'DELETE FROM crew_documents',
                'contract_salaries' => 'DELETE FROM contract_salaries',
                'contracts'         => 'DELETE FROM contracts',
                'crews'             => 'DELETE FROM crews',
            ];

            foreach ($tables as $name => $sql) {
                $this->db->query($sql);
                $deleted[$name] = $this->db->affected_rows;
            }

            // Vessels & clients — disable FK checks temporarily
            $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
            $this->db->query("DELETE FROM vessels");
            $deleted['vessels'] = $this->db->affected_rows;
            $this->db->query("DELETE FROM clients");
            $deleted['clients'] = $this->db->affected_rows;
            $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

            // Reset auto-increment
            foreach (['crews','contracts','contract_salaries','crew_documents','vessels','clients'] as $t) {
                $this->db->query("ALTER TABLE $t AUTO_INCREMENT = 1");
            }

            $this->db->commit();

            $total = array_sum($deleted);
            $msg = "✅ Berhasil menghapus semua data: ";
            $parts = [];
            if ($deleted['crews'] > 0) $parts[] = "{$deleted['crews']} crew";
            if ($deleted['contracts'] > 0) $parts[] = "{$deleted['contracts']} kontrak";
            if ($deleted['contract_salaries'] > 0) $parts[] = "{$deleted['contract_salaries']} salary";
            if ($deleted['crew_documents'] > 0) $parts[] = "{$deleted['crew_documents']} dokumen";
            if ($deleted['vessels'] > 0) $parts[] = "{$deleted['vessels']} vessel";
            if ($deleted['clients'] > 0) $parts[] = "{$deleted['clients']} client";
            $msg .= implode(', ', $parts) . " ($total total records)";

            $this->setFlash('success', $msg);

        } catch (\Exception $e) {
            $this->db->rollback();
            $this->setFlash('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        $this->redirect('SmartImport');
    }
}
