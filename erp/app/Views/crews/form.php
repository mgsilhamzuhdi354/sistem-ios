<?php
/**
 * Crew Form View - Create/Edit
 */
$currentPage = 'crews';
$isEdit = !empty($crew);
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>
            <i class="fas fa-<?= $isEdit ? 'user-edit' : 'user-plus' ?>"></i> 
            <?= $isEdit ? 'Edit Crew' : 'Tambah Crew Baru' ?>
        </h1>
        <p><?= $isEdit ? 'Edit informasi crew' : 'Tambahkan crew baru ke database' ?></p>
    </div>
    <a href="<?= BASE_URL ?>crews" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<form method="POST" action="<?= BASE_URL ?>crews/<?= $isEdit ? 'update/' . $crew['id'] : 'store' ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
    
    <div class="grid-2" style="gap: 24px;">
        <!-- Personal Information -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user" style="color: var(--accent-gold);"></i> Informasi Pribadi
            </h3>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Employee ID</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($employeeId ?? '') ?>" disabled style="background: rgba(0,0,0,0.2);">
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($crew['full_name'] ?? '') ?>" required>
            </div>
            
            <div class="grid-2" style="gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Nama Panggilan</label>
                    <input type="text" name="nickname" class="form-control" value="<?= htmlspecialchars($crew['nickname'] ?? '') ?>">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="gender" class="form-control">
                        <option value="male" <?= ($crew['gender'] ?? 'male') === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="female" <?= ($crew['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            
            <div class="grid-2" style="gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="birth_date" class="form-control" value="<?= $crew['birth_date'] ?? '' ?>">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="birth_place" class="form-control" value="<?= htmlspecialchars($crew['birth_place'] ?? '') ?>">
                </div>
            </div>
            
            <div class="grid-2" style="gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Kewarganegaraan</label>
                    <input type="text" name="nationality" class="form-control" value="<?= htmlspecialchars($crew['nationality'] ?? 'Indonesia') ?>">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Agama</label>
                    <select name="religion" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Islam" <?= ($crew['religion'] ?? '') === 'Islam' ? 'selected' : '' ?>>Islam</option>
                        <option value="Kristen" <?= ($crew['religion'] ?? '') === 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                        <option value="Katolik" <?= ($crew['religion'] ?? '') === 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                        <option value="Hindu" <?= ($crew['religion'] ?? '') === 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                        <option value="Buddha" <?= ($crew['religion'] ?? '') === 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                        <option value="Konghucu" <?= ($crew['religion'] ?? '') === 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Status Pernikahan</label>
                <select name="marital_status" class="form-control">
                    <option value="single" <?= ($crew['marital_status'] ?? 'single') === 'single' ? 'selected' : '' ?>>Belum Menikah</option>
                    <option value="married" <?= ($crew['marital_status'] ?? '') === 'married' ? 'selected' : '' ?>>Menikah</option>
                    <option value="divorced" <?= ($crew['marital_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Cerai</option>
                    <option value="widowed" <?= ($crew['marital_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>Duda/Janda</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Foto</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <?php if (!empty($crew['photo'])): ?>
                    <div style="margin-top: 8px;">
                        <img src="<?= BASE_URL . $crew['photo'] ?>" alt="Current Photo" style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-address-book" style="color: var(--accent-gold);"></i> Kontak & Alamat
            </h3>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($crew['email'] ?? '') ?>">
            </div>
            
            <div class="grid-2" style="gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">No. Telepon</label>
                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($crew['phone'] ?? '') ?>">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">No. WhatsApp</label>
                    <input type="tel" name="whatsapp" class="form-control" value="<?= htmlspecialchars($crew['whatsapp'] ?? '') ?>">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($crew['address'] ?? '') ?></textarea>
            </div>
            
            <div class="grid-3" style="gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($crew['city'] ?? '') ?>">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Provinsi</label>
                    <input type="text" name="province" class="form-control" value="<?= htmlspecialchars($crew['province'] ?? '') ?>">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Kode Pos</label>
                    <input type="text" name="postal_code" class="form-control" value="<?= htmlspecialchars($crew['postal_code'] ?? '') ?>">
                </div>
            </div>
            
            <hr style="border-color: var(--border-color); margin: 20px 0;">
            
            <h4 style="margin-bottom: 16px;"><i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i> Kontak Darurat</h4>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Nama</label>
                <input type="text" name="emergency_name" class="form-control" value="<?= htmlspecialchars($crew['emergency_name'] ?? '') ?>">
            </div>
            
            <div class="grid-2" style="gap: 16px;">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Hubungan</label>
                    <input type="text" name="emergency_relation" class="form-control" value="<?= htmlspecialchars($crew['emergency_relation'] ?? '') ?>" placeholder="Ayah, Ibu, Istri, dll">
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">No. Telepon</label>
                    <input type="tel" name="emergency_phone" class="form-control" value="<?= htmlspecialchars($crew['emergency_phone'] ?? '') ?>">
                </div>
            </div>
        </div>
        
        <!-- Banking & Professional -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-university" style="color: var(--accent-gold);"></i> Informasi Bank
            </h3>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Nama Bank</label>
                <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($crew['bank_name'] ?? '') ?>" placeholder="BCA, Mandiri, BNI, dll">
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">No. Rekening</label>
                <input type="text" name="bank_account" class="form-control" value="<?= htmlspecialchars($crew['bank_account'] ?? '') ?>">
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Nama Pemilik Rekening</label>
                <input type="text" name="bank_holder" class="form-control" value="<?= htmlspecialchars($crew['bank_holder'] ?? '') ?>">
            </div>
        </div>
        
        <!-- Professional Info -->
        <div class="card">
            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-anchor" style="color: var(--accent-gold);"></i> Informasi Profesional
            </h3>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Pengalaman (Tahun)</label>
                <input type="number" name="years_experience" class="form-control" value="<?= $crew['years_experience'] ?? 0 ?>" min="0">
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="available" <?= ($crew['status'] ?? 'available') === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="onboard" <?= ($crew['status'] ?? '') === 'onboard' ? 'selected' : '' ?>>On Board</option>
                    <option value="leave" <?= ($crew['status'] ?? '') === 'leave' ? 'selected' : '' ?>>On Leave</option>
                    <option value="blacklisted" <?= ($crew['status'] ?? '') === 'blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
                    <option value="retired" <?= ($crew['status'] ?? '') === 'retired' ? 'selected' : '' ?>>Retired</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($crew['notes'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end;">
        <a href="<?= BASE_URL ?>crews" class="btn btn-secondary">
            <i class="fas fa-times"></i> Batal
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?= $isEdit ? 'Update Crew' : 'Simpan Crew' ?>
        </button>
    </div>
</form>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
