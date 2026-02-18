<?php
$currentPage = 'ranks';
ob_start();
?>

<div class="page-header">
    <h1><?= $title ?></h1>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="<?= isset($rank) ? BASE_URL . 'ranks/update/' . $rank['id'] : BASE_URL . 'ranks/store' ?>" method="POST">
        
        <div class="grid-2">
            <div class="form-group">
                <label>Nama Pangkat / Rank Name <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= $rank['name'] ?? '' ?>" placeholder="e.g. Chief Officer">
            </div>
            
            <div class="form-group">
                <label>Kode (Optional)</label>
                <input type="text" name="code" class="form-control" value="<?= $rank['code'] ?? '' ?>" placeholder="e.g. C/O">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Department <span class="required">*</span></label>
                <select name="department" class="form-control" required>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept ?>" <?= ($rank['department'] ?? '') === $dept ? 'selected' : '' ?>>
                            <?= $dept ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Urutan Level (1 = Tertinggi) <span class="required">*</span></label>
                <input type="number" name="level" class="form-control" required value="<?= $rank['level'] ?? 99 ?>" min="1">
                <small class="text-muted">Semakin kecil angkanya, semakin tinggi jabatannya (muncul paling atas).</small>
            </div>
        </div>

        <div class="form-group">
            <label>Apakah Officer? (Perwira)</label>
            <div style="margin-top: 5px;">
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_officer" <?= ($rank['is_officer'] ?? 0) ? 'checked' : '' ?>>
                    <span>Ya, ini jabatan Officer</span>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Deskripsi / Catatan</label>
            <textarea name="description" class="form-control" rows="3"><?= $rank['description'] ?? '' ?></textarea>
        </div>

        <?php if (isset($rank)): ?>
        <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
                <option value="1" <?= $rank['is_active'] ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= !$rank['is_active'] ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-actions" style="margin-top: 30px; text-align: right;">
            <a href="<?= BASE_URL ?>ranks" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Pangkat
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
