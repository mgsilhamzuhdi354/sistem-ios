<?php
/**
 * Vessel Form View (Create/Edit)
 */
$currentPage = 'vessels';
$isEdit = !empty($vessel);
ob_start();
?>

<div class="page-header">
    <h1><?= $isEdit ? 'Edit Vessel' : 'Add New Vessel' ?></h1>
    <p><?= $isEdit ? 'Update vessel details' : 'Enter vessel information below' ?></p>
</div>

<form method="POST" action="<?= BASE_URL ?>vessels/<?= $isEdit ? 'update/' . $vessel['id'] : 'store' ?>">
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-ship" style="color: var(--accent-gold);"></i> Vessel Information</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Vessel Name <span style="color: var(--danger);">*</span></label>
                <input type="text" name="name" class="form-control" 
                       value="<?= htmlspecialchars($vessel['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">IMO Number</label>
                <input type="text" name="imo_number" class="form-control" 
                       value="<?= htmlspecialchars($vessel['imo_number'] ?? '') ?>" placeholder="9xxxxxx">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Vessel Type</label>
                <select name="vessel_type_id" class="form-control">
                    <option value="">Select Type</option>
                    <?php foreach ($vesselTypes ?? [] as $vt): ?>
                        <option value="<?= $vt['id'] ?>" <?= ($vessel['vessel_type_id'] ?? '') == $vt['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($vt['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Flag State</label>
                <select name="flag_state_id" class="form-control">
                    <option value="">Select Flag</option>
                    <?php foreach ($flagStates ?? [] as $fs): ?>
                        <option value="<?= $fs['id'] ?>" <?= ($vessel['flag_state_id'] ?? '') == $fs['id'] ? 'selected' : '' ?>>
                            <?= $fs['emoji'] ?? '' ?> <?= htmlspecialchars($fs['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Client / Ship Owner</label>
            <select name="client_id" class="form-control">
                <option value="">Select Client</option>
                <?php foreach ($clients ?? [] as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($vessel['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-info-circle" style="color: var(--accent-gold);"></i> Technical Details</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Gross Tonnage (GT)</label>
                <input type="number" name="gross_tonnage" class="form-control" 
                       value="<?= $vessel['gross_tonnage'] ?? '' ?>" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Deadweight (DWT)</label>
                <input type="number" name="dwt" class="form-control" 
                       value="<?= $vessel['dwt'] ?? '' ?>" step="0.01">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Year Built</label>
                <input type="number" name="year_built" class="form-control" 
                       value="<?= $vessel['year_built'] ?? '' ?>" min="1900" max="<?= date('Y') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Call Sign</label>
                <input type="text" name="call_sign" class="form-control" 
                       value="<?= htmlspecialchars($vessel['call_sign'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Crew Capacity</label>
                <input type="number" name="crew_capacity" class="form-control" 
                       value="<?= $vessel['crew_capacity'] ?? 25 ?>" min="1">
            </div>
            <?php if ($isEdit): ?>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?= ($vessel['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="maintenance" <?= ($vessel['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    <option value="laid_up" <?= ($vessel['status'] ?? '') === 'laid_up' ? 'selected' : '' ?>>Laid Up</option>
                    <option value="sold" <?= ($vessel['status'] ?? '') === 'sold' ? 'selected' : '' ?>>Sold</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <a href="<?= BASE_URL ?>vessels" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?= $isEdit ? 'Update Vessel' : 'Add Vessel' ?>
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
