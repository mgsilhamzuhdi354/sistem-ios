<?php
/**
 * Contract Form View (Create/Edit)
 * Covers Features 1-7: Master Contract, Vessel/Client, Period, Salary, Tax, Deductions, Rank
 */
$currentPage = 'contracts';
$isEdit = !empty($contract);
ob_start();
?>

<div class="page-header">
    <h1 data-translate="<?= $isEdit ? 'edit_contract' : 'create_new_contract' ?>">
        <?= $isEdit ? 'Edit Contract' : 'Create New Contract' ?>
    </h1>
    <p data-translate="<?= $isEdit ? 'update_contract_subtitle' : 'create_contract_subtitle' ?>">
        <?= $isEdit ? 'Update contract details for ' . htmlspecialchars($contract['crew_name']) : 'Fill in the contract details below' ?>
    </p>
</div>

<form method="POST" action="<?= BASE_URL ?>contracts/<?= $isEdit ? 'update/' . $contract['id'] : 'store' ?>">

    <!-- Contract Information -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 8px;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 16px;">1</span>
            <i class="fas fa-file-contract" style="color: var(--accent-gold);"></i> 
            <span data-translate="contract_information">Contract Information</span>
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin: 0 0 20px 44px;">Informasi dasar kontrak yang akan dibuat</p>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" data-translate="contract_number">
                    Nomor Kontrak <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="contract_no" class="form-control"
                    value="<?= htmlspecialchars($contract['contract_no'] ?? $contractNo ?? '') ?>" readonly
                    style="background: #f8fafc; cursor: not-allowed;">
                <small style="color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-info-circle"></i> Nomor otomatis dari sistem
                </small>
            </div>
            <div class="form-group">
                <label class="form-label" data-translate="contract_type">
                    Tipe Kontrak <span style="color: #ef4444;">*</span>
                </label>
                <select name="contract_type" class="form-control" required>
                    <?php foreach ($contractTypes as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($contract['contract_type'] ?? '') === $key ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-info-circle"></i> Temporary untuk kontrak sementara, Voyage untuk per pelayaran
                </small>
            </div>
        </div>
    </div>

    <!-- Crew Assignment (Feature 2 & 7) - with Recruitment Integration -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 8px;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 16px;">2</span>
            <i class="fas fa-user" style="color: var(--accent-gold);"></i> 
            <span data-translate="crew_assignment">Crew Assignment</span>
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin: 0 0 20px 44px;">Pilih crew dari recruitment atau masukkan manual</p>

        <?php if (!empty($recruitmentCrews)): ?>
            <!-- Recruitment Crew Section -->
            <div
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 16px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                    <i class="fas fa-star" style="color: #fbbf24; font-size: 20px;"></i>
                    <h4 style="margin: 0; color: white; font-size: 16px;">🎯 Newly Approved from Recruitment
                        (<?= count($recruitmentCrews) ?>)</h4>
                </div>
                <p style="color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 16px;">
                    Select a crew member from the recruitment pipeline below. Their information will auto-populate the
                    contract form.
                </p>

                <div
                    style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; max-height: 400px; overflow-y: auto; padding: 4px;">
                    <?php foreach ($recruitmentCrews as $rc): ?>
                        <div class="recruitment-crew-card"
                            onclick="selectRecruitmentCrew(<?= htmlspecialchars(json_encode($rc)) ?>)"
                            style="background: white; padding: 14px; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 2px solid transparent; position: relative;">
                            <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 8px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: bold; color: #1e40af; margin-bottom: 4px; font-size: 14px;">
                                        <?= htmlspecialchars($rc['full_name']) ?>
                                    </div>
                                    <div style="font-size: 11px; color: #64748b; font-family: monospace;">
                                        ID: <?= htmlspecialchars($rc['employee_id']) ?>
                                    </div>
                                </div>
                                <?php if ($rc['days_since_approval'] <= 7): ?>
                                    <span
                                        style="background: #ef4444; color: white; font-size: 9px; padding: 2px 6px; border-radius: 4px; font-weight: bold;">NEW</span>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 12px; color: #475569; margin-bottom: 6px;">
                                <i class="fas fa-briefcase" style="color: #f59e0b; width: 14px;"></i>
                                <?= htmlspecialchars($rc['rank_name'] ?? 'Not assigned') ?>
                            </div>
                            <div style="font-size: 11px; color: #94a3b8;">
                                <i class="fas fa-calendar-check" style="width: 14px;"></i>
                                <?= $rc['days_since_approval'] ?> days ago
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Manual Crew Input -->
        <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <h5 style="margin: 0 0 12px 0; font-size: 14px; color: #475569;">
                <i class="fas fa-keyboard"></i> Or Enter Crew Details Manually
            </h5>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" data-translate="crew_id">Crew ID (dari Recruitment) <span style="color: #ef4444;">*</span></label>
                    <input type="number" name="crew_id" id="crew_id_input" class="form-control"
                        value="<?= htmlspecialchars($contract['crew_id'] ?? '') ?>" required
                        placeholder="Masukkan ID crew">
                </div>
                <div class="form-group">
                    <label class="form-label">Crew Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="crew_name" id="crew_name_input" class="form-control"
                        value="<?= htmlspecialchars($contract['crew_name'] ?? '') ?>" required
                        placeholder="Nama lengkap crew">
                </div>
            </div>
        </div>

        <!-- Vessel & Client Selection -->
        <div class="form-row" style="margin-top: 20px;">
            <div class="form-group">
                <label class="form-label">Vessel <span style="color: #ef4444;">*</span></label>
                <select name="vessel_id" class="form-control" required>
                    <option value="">-- Pilih Kapal --</option>
                    <?php foreach ($vessels as $v): ?>
                        <option value="<?= $v['id'] ?>" <?= ($contract['vessel_id'] ?? '') == $v['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($v['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Client / Principal <span style="color: #ef4444;">*</span></label>
                <select name="client_id" class="form-control" required>
                    <option value="">-- Pilih Client --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($contract['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Rank / Position <span style="color: #ef4444;">*</span></label>
            <select name="rank_id" id="rank_id_input" class="form-control" required>
                <option value="">-- Pilih Posisi --</option>
                <?php foreach ($ranks as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ($contract['rank_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['name']) ?> (<?= ucfirst($r['department']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <style>
        .recruitment-crew-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #667eea !important;
        }

        .recruitment-crew-card.selected {
            border-color: #10b981 !important;
            background: #f0fdf4 !important;
        }
    </style>

    <script>
        function selectRecruitmentCrew(crew) {
            // Populate form fields
            document.getElementById('crew_id_input').value = crew.id;
            document.getElementById('crew_name_input').value = crew.full_name;
            if (crew.current_rank_id) {
                document.getElementById('rank_id_input').value = crew.current_rank_id;
            }

            // Visual feedback
            document.querySelectorAll('.recruitment-crew-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');

            // Scroll to crew assignment detail
            document.getElementById('crew_id_input').scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Flash success
            const nameInput = document.getElementById('crew_name_input');
            nameInput.style.transition = 'all 0.3s';
            nameInput.style.background = '#dcfce7';
            setTimeout(() => {
                nameInput.style.background = '';
            }, 1000);
        }
    </script>

    <!-- Contract Period (Feature 3) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 8px;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 16px;">3</span>
            <i class="fas fa-calendar" style="color: var(--accent-gold);"></i> 
            <span data-translate="contract_period">Periode Kontrak</span>
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin: 0 0 20px 44px;">Tentukan periode waktu kontrak</p>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Sign On Date <span style="color: #ef4444;">*</span></label>
                <input type="date" name="sign_on_date" id="sign_on_date" class="form-control"
                    value="<?= $contract['sign_on_date'] ?? '' ?>" required onchange="calculateDuration()">
                <small style="color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-info-circle"></i> Tanggal mulai bekerja
                </small>
            </div>
            <div class="form-group">
                <label class="form-label">Sign Off Date <span style="color: #ef4444;">*</span></label>
                <input type="date" name="sign_off_date" id="sign_off_date" class="form-control"
                    value="<?= $contract['sign_off_date'] ?? '' ?>" required onchange="calculateDuration()">
                <small style="color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-info-circle"></i> Tanggal akhir kontrak
                </small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Duration (Months) <span style="color: #ef4444;">*</span></label>
                <input type="number" name="duration_months" id="duration_months" class="form-control"
                    value="<?= $contract['duration_months'] ?? '9' ?>" min="1" max="36" required
                    placeholder="9">
                <small style="color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-info-circle"></i> Otomatis terisi dari tanggal
                </small>
            </div>
            <div class="form-group">
                <label class="form-label">Embarkation Port</label>
                <input type="text" name="embarkation_port" class="form-control"
                    value="<?= htmlspecialchars($contract['embarkation_port'] ?? '') ?>"
                    placeholder="e.g. Jakarta, Indonesia">
                <small style="color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-info-circle"></i> Pelabuhan keberangkatan
                </small>
            </div>
        </div>
    </div>

    <!-- Salary Structure (Feature 4) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 8px;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 16px;">4</span>
            <i class="fas fa-money-bill" style="color: var(--accent-gold);"></i> 
            <span data-translate="salary_structure">Struktur Gaji</span>
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin: 0 0 20px 44px;">Rincian gaji dan kompensasi crew</p>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Currency</label>
                <select name="currency_id" id="currencySelect" class="form-control" onchange="toggleExchangeRate()">
                    <?php
                    // Get current contract's currency_id from contract_salaries if available
                    $currentCurrencyId = $contract['currency_id'] ?? 1;
                    // If we have currency_code, try to match it
                    if (isset($contract['currency_code'])) {
                        foreach ($currencies as $cur) {
                            if ($cur['code'] === $contract['currency_code']) {
                                $currentCurrencyId = $cur['id'];
                                break;
                            }
                        }
                    }
                    ?>
                    <?php foreach ($currencies as $cur): ?>
                        <option value="<?= $cur['id'] ?>" data-code="<?= $cur['code'] ?>" <?= $currentCurrencyId == $cur['id'] ? 'selected' : '' ?>><?= $cur['code'] ?> - <?= $cur['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="exchangeRateGroup"
                style="<?= ($contract['currency_code'] ?? 'USD') === 'USD' ? 'display:none;' : '' ?>">
                <label class="form-label">Exchange Rate to USD <small style="color:var(--text-muted);">(1 USD =
                        ?)</small></label>
                <input type="text" name="exchange_rate" id="exchangeRateInput" class="form-control"
                    value="<?= isset($contract['exchange_rate']) && $contract['exchange_rate'] > 0 ? number_format($contract['exchange_rate'], 0, '', '') : '' ?>"
                    placeholder="Contoh: 15800 (1 USD = Rp15.800)">
                <small style="color: var(--text-muted);">Diatur oleh Owner Kapal. Kosongkan untuk rate default.</small>
            </div>
        </div>

        <!-- Client Rate for Profit Calculation -->
        <div class="form-row"
            style="background: rgba(16, 185, 129, 0.1); padding: 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid rgba(16, 185, 129, 0.3);">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" style="color: var(--success);"><i class="fas fa-hand-holding-usd"></i> Client
                    Rate (Harga dari Client)</label>
                <input type="text" name="client_rate" id="client_rate_input" class="form-control salary-input"
                    value="<?= isset($contract['client_rate']) && $contract['client_rate'] > 0 ? number_format($contract['client_rate'], 0) : '' ?>"
                    placeholder="Berapa client bayar untuk crew ini?" oninput="calculateTotals()">
                <small style="color: var(--success);">Harga yang dibayar client ke perusahaan. Profit = Client Rate -
                    Total Salary</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Basic Salary</label>
                <input type="text" name="basic_salary" class="form-control salary-input"
                    value="<?= number_format($contract['basic_salary'] ?? 0, 0) ?>" placeholder="0" oninput="calculateTotals()">
                <small style="color: var(--text-muted); font-size: 11px;">Gaji pokok bulanan</small>
            </div>
            <div class="form-group">
                <label class="form-label">Overtime Allowance</label>
                <input type="text" name="overtime_allowance" class="form-control salary-input"
                    value="<?= number_format($contract['overtime_allowance'] ?? 0, 0) ?>" placeholder="0" oninput="calculateTotals()">
            </div>
        </div>
        <div class="form-group">
                <small style="color: var(--text-muted); font-size: 11px;">Masukkan 0 jika tidak ada overtime</small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Leave Pay</label>
                <input type="text" name="leave_pay" class="form-control salary-input"
                    value="<?= number_format($contract['leave_pay'] ?? 0, 0) ?>" placeholder="0" oninput="calculateTotals()">
            </div>
            <div class="form-group">
                <label class="form-label">Bonus</label>
                <input type="text" name="bonus" class="form-control salary-input"
                    value="<?= number_format($contract['bonus'] ?? 0, 0) ?>" placeholder="0" oninput="calculateTotals()">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Other Allowance</label>
            <input type="text" name="other_allowance" class="form-control salary-input"
                value="<?= number_format($contract['other_allowance'] ?? 0, 0) ?>" placeholder="0" oninput="calculateTotals()">
            <small style="color: var(--text-muted); font-size: 11px;">Tunjangan lainnya seperti food allowance, etc.</small>
        </div>
        
        <!-- Real-time Total Salary & Profit Calculator -->
        <div id="salary-summary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 12px; margin-top: 24px; color: white;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;"><i class="fas fa-calculator"></i> Total Salary</div>
                    <div id="total-salary-display" style="font-size: 24px; font-weight: bold;">$0</div>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Basic + Overtime + Leave + Bonus + Other</div>
                </div>
                <div>
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;"><i class="fas fa-money-bill-wave"></i> Client Rate</div>
                    <div id="client-rate-display" style="font-size: 24px; font-weight: bold;">$0</div>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Harga dari client</div>
                </div>
                <div id="profit-container">
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;"><i class="fas fa-chart-line"></i> Profit/Loss</div>
                    <div id="profit-display" style="font-size: 24px; font-weight: bold;">$0</div>
                    <div style="font-size: 11px; opacity: 0.8; margin-top: 2px;">Client Rate - Total Salary</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Settings (Feature 5) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 8px;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 16px;">5</span>
            <i class="fas fa-percent" style="color: var(--accent-gold);"></i> 
            <span data-translate="tax_settings">Pengaturan Pajak (PPh 21)</span>
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin: 0 0 20px 44px;">Pengaturan pajak penghasilan crew</p>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tax Type</label>
                <select name="tax_type" class="form-control">
                    <?php foreach ($taxTypes as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($contract['tax_type'] ?? 'pph21') === $key ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">NPWP Number</label>
                <input type="text" name="npwp_number" id="npwp_input" class="form-control"
                    value="<?= htmlspecialchars($contract['npwp_number'] ?? '') ?>" placeholder="xx.xxx.xxx.x-xxx.xxx"
                    maxlength="20" oninput="formatNPWP(this)">
                <small style="color: var(--text-muted); font-size: 11px;">
                    <i class="fas fa-info-circle"></i> Format: xx.xxx.xxx.x-xxx.xxx
                </small>
            </div>
        </div>
    </div>

    <!-- Deductions (Feature 6) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 8px;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 16px;">6</span>
            <i class="fas fa-minus-circle" style="color: var(--accent-gold);"></i> 
            <span data-translate="deductions">Potongan</span>
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin: 0 0 20px 44px;">Potongan gaji seperti pinjaman, asuransi, dll</p>
        <div id="deductions-container">
            <?php if (!empty($deductions)): ?>
                <?php foreach ($deductions as $i => $ded): ?>
                    <div class="form-row deduction-row" style="align-items: flex-end;">
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label">Type</label>
                            <select name="deduction_type[]" class="form-control">
                                <?php foreach ($deductionTypes as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= $ded['deduction_type'] === $key ? 'selected' : '' ?>><?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label">Description</label>
                            <input type="text" name="deduction_desc[]" class="form-control"
                                value="<?= htmlspecialchars($ded['description']) ?>">
                        </div>
                        <div class="form-group" style="flex: 0.5;">
                            <label class="form-label">Amount</label>
                        <input type="text" name="deduction_amount[]" class="form-control deduction-input"
                            value="<?= number_format($ded['amount'], 0, '', '') ?>" placeholder="100000" oninput="calculateTotals(); formatCurrency(this);">
                        </div>
                        <div class="form-group" style="flex: 0.5;">
                            <label class="form-label">Frequency</label>
                            <select name="deduction_recurring[]" class="form-control">
                                <option value="monthly" <?= $ded['is_recurring'] ? 'selected' : '' ?>>Monthly</option>
                                <option value="onetime" <?= !$ded['is_recurring'] ? 'selected' : '' ?>>One-time</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.deduction-row').remove()"><i
                                class="fas fa-times"></i></button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addDeduction()">
            <i class="fas fa-plus"></i> Tambah Potongan
        </button>
        
        <!-- Total Deductions Summary -->
        <div id="deductions-summary" style="background: #fef3c7; padding: 12px 16px; border-radius: 8px; margin-top: 16px; display: none; border-left: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #92400e; font-size: 13px; font-weight: 500;"><i class="fas fa-calculator"></i> Total Potongan:</span>
                <span id="total-deductions-display" style="color: #92400e; font-size: 18px; font-weight: bold;">$0</span>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 8px;">
            <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 16px;">7</span>
            <i class="fas fa-sticky-note" style="color: var(--accent-gold);"></i> 
            <span data-translate="notes">Notes</span>
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin: 0 0 20px 44px;">Catatan tambahan untuk kontrak ini</p>
        <div class="form-group" style="margin: 0;">
            <textarea name="notes" class="form-control" rows="3"
                placeholder="Additional notes..."><?= htmlspecialchars($contract['notes'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Actions -->
    <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <a href="<?= BASE_URL ?>contracts" class="btn btn-secondary" data-translate="btn_cancel">Cancel</a>
        <button type="submit" name="save_draft" class="btn btn-secondary"><i class="fas fa-save"></i> <span
                data-translate="save_as_draft">Save as Draft</span></button>
        <button type="submit" name="submit_approval" value="1" class="btn btn-primary"><i
                class="fas fa-paper-plane"></i> <span data-translate="submit_for_approval">Submit for
                Approval</span></button>
    </div>
</form>

<script>
    // ======================
    // DEDUCTIONS MANAGEMENT
    // ======================
    function addDeduction() {
        const container = document.getElementById('deductions-container');
        const row = document.createElement('div');
        row.className = 'form-row deduction-row';
        row.style.alignItems = 'flex-end';
        row.innerHTML = `
        <div class="form-group" style="flex: 1;">
            <label class="form-label">Type</label>
            <select name="deduction_type[]" class="form-control">
                <?php foreach ($deductionTypes as $key => $label): ?>
                    <option value="<?= $key ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="flex: 1;">
            <label class="form-label">Description</label>
            <input type="text" name="deduction_desc[]" class="form-control" placeholder="Description">
        </div>
        <div class="form-group" style="flex: 0.5;">
            <label class="form-label">Amount</label>
            <input type="text" name="deduction_amount[]" class="form-control deduction-input" placeholder="100000" oninput="calculateTotals(); formatCurrency(this);">
        </div>
        <div class="form-group" style="flex: 0.5;">
            <label class="form-label">Frequency</label>
            <select name="deduction_recurring[]" class="form-control">
                <option value="monthly">Monthly</option>
                <option value="onetime">One-time</option>
            </select>
        </div>
        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.deduction-row').remove(); calculateTotals();"><i class="fas fa-times"></i></button>
    `;
        container.appendChild(row);
        
        // Smooth scroll to new deduction
        setTimeout(() => {
            row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
        
        calculateTotals();
    }

    // ======================
    // CURRENCY FORMATTING
    // ======================
    function formatCurrency(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            input.value = parseInt(value).toLocaleString('en-US');
        }
    }

    function parseCurrency(value) {
        if (typeof value === 'string') {
            return parseFloat(value.replace(/,/g, '')) || 0;
        }
        return value || 0;
    }

    // ======================
    // NPWP FORMATTING
    // ======================
    function formatNPWP(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        
        // Format: xx.xxx.xxx.x-xxx.xxx
        if (value.length > 0) {
            let formatted = '';
            if (value.length >= 2) formatted = value.substr(0, 2);
            if (value.length >= 3) formatted += '.' + value.substr(2, 3);
            if (value.length >= 6) formatted += '.' + value.substr(5, 3);
            if (value.length >= 9) formatted += '.' + value.substr(8, 1);
            if (value.length >= 10) formatted += '-' + value.substr(9, 3);
            if (value.length >= 13) formatted += '.' + value.substr(12, 3);
            
            input.value = formatted;
        }
    }

    // ======================
    // REAL-TIME CALCULATIONS
    // ======================
    function calculateTotals() {
        // Get all salary inputs
        const basicSalary = parseCurrency(document.querySelector('input[name="basic_salary"]').value);
        const overtime = parseCurrency(document.querySelector('input[name="overtime_allowance"]').value);
        const leavePay = parseCurrency(document.querySelector('input[name="leave_pay"]').value);
        const bonus = parseCurrency(document.querySelector('input[name="bonus"]').value);
        const otherAllowance = parseCurrency(document.querySelector('input[name="other_allowance"]').value);
        
        // Calculate total salary
        const totalSalary = basicSalary + overtime + leavePay + bonus + otherAllowance;
        
        // Get client rate
        const clientRate = parseCurrency(document.querySelector('input[name="client_rate"]').value);
        
        // Calculate profit
        const profit = clientRate - totalSalary;
        
        // Update displays
        updateSalaryDisplay(totalSalary, clientRate, profit);
        
        // Calculate deductions
        calculateDeductions();
    }

    function updateSalaryDisplay(totalSalary, clientRate, profit) {
        // Get currency symbol
        const currencySelect = document.getElementById('currencySelect');
        const currencyCode = currencySelect.options[currencySelect.selectedIndex].getAttribute('data-code') || 'USD';
        const symbol = currencyCode === 'USD' ? '$' : 'Rp';
        
        // Update total salary
        document.getElementById('total-salary-display').textContent = 
            symbol + totalSalary.toLocaleString('en-US');
        
        // Update client rate
        document.getElementById('client-rate-display').textContent = 
            symbol + clientRate.toLocaleString('en-US');
        
        // Update profit with color coding
        const profitDisplay = document.getElementById('profit-display');
        const profitContainer = document.getElementById('profit-container');
        
        if (profit > 0) {
            profitDisplay.textContent = symbol + profit.toLocaleString('en-US');
            profitContainer.style.background = 'rgba(16, 185, 129, 0.2)';
            profitContainer.style.borderRadius = '8px';
            profitContainer.style.padding = '12px';
        } else if (profit < 0) {
            profitDisplay.textContent = '-' + symbol + Math.abs(profit).toLocaleString('en-US');
            profitContainer.style.background = 'rgba(239, 68, 68, 0.2)';
            profitContainer.style.borderRadius = '8px';
            profitContainer.style.padding = '12px';
        } else {
            profitDisplay.textContent = symbol + '0';
            profitContainer.style.background = 'transparent';
            profitContainer.style.padding = '12px';
        }
    }

   function calculateDeductions() {
        const deductionInputs = document.querySelectorAll('input[name="deduction_amount[]"]');
        let totalDeductions = 0;
        
        deductionInputs.forEach(input => {
            totalDeductions += parseCurrency(input.value);
        });
        
        // Show/hide deductions summary
        const summary = document.getElementById('deductions-summary');
        if (totalDeductions > 0) {
            summary.style.display = 'block';
            
            // Get currency
            const currencySelect = document.getElementById('currencySelect');
            const currencyCode = currencySelect.options[currencySelect.selectedIndex].getAttribute('data-code') || 'USD';
            const symbol = currencyCode === 'USD' ? '$' : 'Rp';
            
            document.getElementById('total-deductions-display').textContent = 
                symbol + totalDeductions.toLocaleString('en-US');
        } else {
            summary.style.display = 'none';
        }
    }

    // ======================
    // DATE CALCULATIONS
    // ======================
    function calculateDuration() {
        const signOnDate = document.getElementById('sign_on_date').value;
        const signOffDate = document.getElementById('sign_off_date').value;
        
        if (signOnDate && signOffDate) {
            const start = new Date(signOnDate);
            const end = new Date(signOffDate);
            
            // Validate dates
            if (end <= start) {
                alert('Sign Off Date harus setelah Sign On Date!');
                document.getElementById('sign_off_date').value = '';
                document.getElementById('duration_months').value = '';
                return;
            }
            
            // Calculate months difference
            const months = (end.getFullYear() - start.getFullYear()) * 12 + 
                          (end.getMonth() - start.getMonth());
            
            document.getElementById('duration_months').value = Math.max(1, months);
        }
    }

    // ======================
    // EXCHANGE RATE TOGGLE
    // ======================
    function toggleExchangeRate() {
        const select = document.getElementById('currencySelect');
        const exchangeRateGroup = document.getElementById('exchangeRateGroup');
        const selectedOption = select.options[select.selectedIndex];
        const currencyCode = selectedOption.getAttribute('data-code');

        if (currencyCode === 'USD') {
            exchangeRateGroup.style.display = 'none';
            document.getElementById('exchangeRateInput').value = '';
        } else {
            exchangeRateGroup.style.display = 'block';
        }
        
        // Recalculate with new currency
        calculateTotals();
    }

    // ======================
    // RECRUITMENT CREW SELECTION
    // ======================
    function selectRecruitmentCrew(crew) {
        // Populate form fields
        document.getElementById('crew_id_input').value = crew.id;
        document.getElementById('crew_name_input').value = crew.full_name;
        if (crew.current_rank_id) {
            document.getElementById('rank_id_input').value = crew.current_rank_id;
        }

        // Visual feedback
        document.querySelectorAll('.recruitment-crew-card').forEach(card => {
            card.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');

        // Scroll to crew assignment detail
        document.getElementById('crew_id_input').scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Flash success
        const nameInput = document.getElementById('crew_name_input');
        nameInput.style.transition = 'all 0.3s';
        nameInput.style.background = '#dcfce7';
        setTimeout(() => {
            nameInput.style.background = '';
        }, 1000);
    }

    // ======================
    // AUTO-FORMAT ON INPUT
    // ======================
    function setupAutoFormatting() {
        // Format all salary inputs
        const salaryInputs = document.querySelectorAll('.salary-input');
        salaryInputs.forEach(input => {
            // Format on load
            formatCurrency(input);
            
            // Format on blur
            input.addEventListener('blur', function() {
                formatCurrency(this);
            });
        });
        
        // Format deduction inputs
        const deductionInputs = document.querySelectorAll('.deduction-input');
        deductionInputs.forEach(input => {
            input.addEventListener('blur', function() {
                formatCurrency(this);
            });
        });
    }

    // ======================
    // FORM VALIDATION
    // ======================
    function validateForm(event) {
        let isValid = true;
        let errors = [];
        
        // Check required fields
        const crewId = document.getElementById('crew_id_input').value;
        const crewName = document.getElementById('crew_name_input').value;
        const signOnDate = document.getElementById('sign_on_date').value;
        const signOffDate = document.getElementById('sign_off_date').value;
        
        if (!crewId || !crewName) {
            errors.push('Crew ID dan Crew Name wajib diisi');
            isValid = false;
        }
        
        if (!signOnDate || !signOffDate) {
            errors.push('Sign On Date dan Sign Off Date wajib Diisi');
            isValid = false;
        }
        
        // Validate dates
        if (signOnDate && signOffDate) {
            const start = new Date(signOnDate);
            const end = new Date(signOffDate);
            if (end <= start) {
                errors.push('Sign Off Date harus setelah Sign On Date');
                isValid = false;
            }
        }
        
        if (!isValid) {
            event.preventDefault();
            alert('Error:\n' + errors.join('\n'));
        }
        
        return isValid;
    }

    // ======================
    // INITIALIZATION
    // ======================
    document.addEventListener('DOMContentLoaded', function () {
        toggleExchangeRate();
        setupAutoFormatting();
        calculateTotals();
        
        // Add form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', validateForm);
        }
        
        // Add currency change listener
        document.getElementById('currencySelect').addEventListener('change', function() {
            toggleExchangeRate();
        });
    });
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>