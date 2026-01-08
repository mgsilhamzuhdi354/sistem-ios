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
    <h1><?= $isEdit ? 'Edit Contract' : 'Create New Contract' ?></h1>
    <p><?= $isEdit ? 'Update contract details for ' . htmlspecialchars($contract['crew_name']) : 'Fill in the contract details below' ?></p>
</div>

<form method="POST" action="<?= BASE_URL ?>contracts/<?= $isEdit ? 'update/' . $contract['id'] : 'store' ?>">
    
    <!-- Contract Information -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-file-contract" style="color: var(--accent-gold);"></i> Contract Information</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Contract Number</label>
                <input type="text" name="contract_no" class="form-control" 
                       value="<?= htmlspecialchars($contract['contract_no'] ?? $contractNo ?? '') ?>" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Contract Type</label>
                <select name="contract_type" class="form-control">
                    <?php foreach ($contractTypes as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($contract['contract_type'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Crew Assignment (Feature 2 & 7) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-user" style="color: var(--accent-gold);"></i> Crew Assignment</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Crew ID (from Recruitment)</label>
                <input type="number" name="crew_id" class="form-control" 
                       value="<?= htmlspecialchars($contract['crew_id'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Crew Name</label>
                <input type="text" name="crew_name" class="form-control" 
                       value="<?= htmlspecialchars($contract['crew_name'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Vessel</label>
                <select name="vessel_id" class="form-control" required>
                    <option value="">Select Vessel</option>
                    <?php foreach ($vessels as $v): ?>
                        <option value="<?= $v['id'] ?>" <?= ($contract['vessel_id'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Client / Principal</label>
                <select name="client_id" class="form-control" required>
                    <option value="">Select Client</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($contract['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Rank / Position</label>
            <select name="rank_id" class="form-control" required>
                <option value="">Select Rank</option>
                <?php foreach ($ranks as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ($contract['rank_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['name']) ?> (<?= ucfirst($r['department']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <!-- Contract Period (Feature 3) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-calendar" style="color: var(--accent-gold);"></i> Contract Period</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Sign On Date</label>
                <input type="date" name="sign_on_date" class="form-control" 
                       value="<?= $contract['sign_on_date'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Sign Off Date</label>
                <input type="date" name="sign_off_date" class="form-control" 
                       value="<?= $contract['sign_off_date'] ?? '' ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Duration (Months)</label>
                <input type="number" name="duration_months" class="form-control" 
                       value="<?= $contract['duration_months'] ?? '9' ?>" min="1" max="36">
            </div>
            <div class="form-group">
                <label class="form-label">Embarkation Port</label>
                <input type="text" name="embarkation_port" class="form-control" 
                       value="<?= htmlspecialchars($contract['embarkation_port'] ?? '') ?>" placeholder="e.g. Jakarta, Indonesia">
            </div>
        </div>
    </div>
    
    <!-- Salary Structure (Feature 4) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-money-bill" style="color: var(--accent-gold);"></i> Salary Structure</h3>
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
            <div class="form-group" id="exchangeRateGroup" style="<?= ($contract['currency_code'] ?? 'USD') === 'USD' ? 'display:none;' : '' ?>">
                <label class="form-label">Exchange Rate to USD <small style="color:var(--text-muted);">(1 USD = ?)</small></label>
                <input type="text" name="exchange_rate" id="exchangeRateInput" class="form-control" 
                       value="<?= isset($contract['exchange_rate']) && $contract['exchange_rate'] > 0 ? number_format($contract['exchange_rate'], 0, '', '') : '' ?>" 
                       placeholder="Contoh: 15800 (1 USD = Rp15.800)">
                <small style="color: var(--text-muted);">Diatur oleh Owner Kapal. Kosongkan untuk rate default.</small>
            </div>
        </div>
        
        <!-- Client Rate for Profit Calculation -->
        <div class="form-row" style="background: rgba(16, 185, 129, 0.1); padding: 16px; border-radius: 8px; margin-bottom: 16px; border: 1px solid rgba(16, 185, 129, 0.3);">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" style="color: var(--success);"><i class="fas fa-hand-holding-usd"></i> Client Rate (Harga dari Client)</label>
                <input type="text" name="client_rate" class="form-control" 
                       value="<?= isset($contract['client_rate']) && $contract['client_rate'] > 0 ? number_format($contract['client_rate'], 0) : '' ?>" 
                       placeholder="Berapa client bayar untuk crew ini?">
                <small style="color: var(--success);">Harga yang dibayar client ke perusahaan. Profit = Client Rate - Total Salary</small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Basic Salary</label>
                <input type="text" name="basic_salary" class="form-control" 
                       value="<?= number_format($contract['basic_salary'] ?? 0, 0) ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Overtime Allowance</label>
                <input type="text" name="overtime_allowance" class="form-control" 
                       value="<?= number_format($contract['overtime_allowance'] ?? 0, 0) ?>" placeholder="0">
            </div>
        </div>
            <div class="form-group">
                <label class="form-label">Leave Pay</label>
                <input type="text" name="leave_pay" class="form-control" 
                       value="<?= number_format($contract['leave_pay'] ?? 0, 0) ?>" placeholder="0">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Bonus</label>
                <input type="text" name="bonus" class="form-control" 
                       value="<?= number_format($contract['bonus'] ?? 0, 0) ?>" placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Other Allowance</label>
                <input type="text" name="other_allowance" class="form-control" 
                       value="<?= number_format($contract['other_allowance'] ?? 0, 0) ?>" placeholder="0">
            </div>
        </div>
    </div>
    
    <!-- Tax Settings (Feature 5) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-percent" style="color: var(--accent-gold);"></i> Tax Settings (PPh 21)</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Tax Type</label>
                <select name="tax_type" class="form-control">
                    <?php foreach ($taxTypes as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($contract['tax_type'] ?? 'pph21') === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">NPWP Number</label>
                <input type="text" name="npwp_number" class="form-control" 
                       value="<?= htmlspecialchars($contract['npwp_number'] ?? '') ?>" placeholder="xx.xxx.xxx.x-xxx.xxx">
            </div>
        </div>
    </div>
    
    <!-- Deductions (Feature 6) -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-minus-circle" style="color: var(--accent-gold);"></i> Deductions</h3>
        <div id="deductions-container">
            <?php if (!empty($deductions)): ?>
                <?php foreach ($deductions as $i => $ded): ?>
                <div class="form-row deduction-row" style="align-items: flex-end;">
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Type</label>
                        <select name="deduction_type[]" class="form-control">
                            <?php foreach ($deductionTypes as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $ded['deduction_type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">Description</label>
                        <input type="text" name="deduction_desc[]" class="form-control" value="<?= htmlspecialchars($ded['description']) ?>">
                    </div>
                    <div class="form-group" style="flex: 0.5;">
                        <label class="form-label">Amount</label>
                        <input type="text" name="deduction_amount[]" class="form-control" value="<?= number_format($ded['amount'], 0, '', '') ?>" placeholder="100000">
                    </div>
                    <div class="form-group" style="flex: 0.5;">
                        <label class="form-label">Frequency</label>
                        <select name="deduction_recurring[]" class="form-control">
                            <option value="monthly" <?= $ded['is_recurring'] ? 'selected' : '' ?>>Monthly</option>
                            <option value="onetime" <?= !$ded['is_recurring'] ? 'selected' : '' ?>>One-time</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.deduction-row').remove()"><i class="fas fa-times"></i></button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addDeduction()">
            <i class="fas fa-plus"></i> Add Deduction
        </button>
    </div>
    
    <!-- Notes -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-sticky-note" style="color: var(--accent-gold);"></i> Notes</h3>
        <div class="form-group" style="margin: 0;">
            <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."><?= htmlspecialchars($contract['notes'] ?? '') ?></textarea>
        </div>
    </div>
    
    <!-- Actions -->
    <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <a href="<?= BASE_URL ?>contracts" class="btn btn-secondary">Cancel</a>
        <button type="submit" name="save_draft" class="btn btn-secondary"><i class="fas fa-save"></i> Save as Draft</button>
        <button type="submit" name="submit_approval" value="1" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit for Approval</button>
    </div>
</form>

<script>
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
            <input type="text" name="deduction_amount[]" class="form-control" placeholder="100000">
        </div>
        <div class="form-group" style="flex: 0.5;">
            <label class="form-label">Frequency</label>
            <select name="deduction_recurring[]" class="form-control">
                <option value="monthly">Monthly</option>
                <option value="onetime">One-time</option>
            </select>
        </div>
        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.deduction-row').remove()"><i class="fas fa-times"></i></button>
    `;
    container.appendChild(row);
}

// Toggle exchange rate field visibility based on currency selection
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
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleExchangeRate();
});
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
