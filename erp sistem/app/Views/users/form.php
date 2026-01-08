<?php
/**
 * User Management - Form View (Create/Edit)
 */
$currentPage = 'users';
$isEdit = !empty($user);
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>
            <i class="fas fa-<?= $isEdit ? 'user-edit' : 'user-plus' ?>"></i> 
            <?= $isEdit ? 'Edit User' : 'Tambah User Baru' ?>
        </h1>
        <p><?= $isEdit ? 'Edit informasi user' : 'Tambahkan user baru ke sistem' ?></p>
    </div>
    <a href="<?= BASE_URL ?>users" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <form method="POST" action="<?= BASE_URL ?>users/<?= $isEdit ? 'update/' . $user['id'] : 'store' ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
        
        <div class="grid-2" style="gap: 24px;">
            <!-- Account Information -->
            <div>
                <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-user-circle" style="color: var(--accent-gold);"></i> Informasi Akun
                </h3>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Username <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="username" class="form-control" 
                           value="<?= htmlspecialchars($user['username'] ?? '') ?>" 
                           required minlength="3" maxlength="50"
                           placeholder="Minimal 3 karakter">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Email <span style="color: var(--danger);">*</span></label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                           required placeholder="email@example.com">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">
                        Password 
                        <?php if (!$isEdit): ?>
                        <span style="color: var(--danger);">*</span>
                        <?php else: ?>
                        <small style="color: var(--text-muted);">(kosongkan jika tidak ingin mengubah)</small>
                        <?php endif; ?>
                    </label>
                    <input type="password" name="password" class="form-control" 
                           <?= $isEdit ? '' : 'required' ?> minlength="8"
                           placeholder="Minimal 8 karakter">
                </div>
                
                <?php if (!$isEdit): ?>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Konfirmasi Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" 
                           required minlength="8" placeholder="Ulangi password">
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Personal Information -->
            <div>
                <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-id-card" style="color: var(--accent-gold);"></i> Informasi Pribadi
                </h3>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="full_name" class="form-control" 
                           value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" 
                           required placeholder="Nama lengkap">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="tel" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                           placeholder="08xxxxxxxxxx">
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Role <span style="color: var(--danger);">*</span></label>
                    <select name="role" class="form-control" required>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($user['role'] ?? 'viewer') === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: var(--text-muted); margin-top: 6px; display: block;">
                        <i class="fas fa-info-circle"></i> Role menentukan hak akses user dalam sistem
                    </small>
                </div>
                
                <?php if ($isEdit): ?>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-control">
                        <option value="1" <?= ($user['is_active'] ?? 1) ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= !($user['is_active'] ?? 1) ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Role Permissions Info -->
        <div style="margin-top: 30px; padding: 20px; background: rgba(59, 130, 246, 0.1); border-radius: 12px; border: 1px solid rgba(59, 130, 246, 0.2);">
            <h4 style="margin-bottom: 16px; color: var(--info);">
                <i class="fas fa-shield-alt"></i> Informasi Role & Hak Akses
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                <div>
                    <strong style="color: #EF4444;">Super Admin</strong>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Akses penuh ke seluruh sistem termasuk user management</p>
                </div>
                <div>
                    <strong style="color: #8B5CF6;">Admin</strong>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Akses hampir penuh, tidak bisa hapus data tertentu</p>
                </div>
                <div>
                    <strong style="color: #10B981;">HR</strong>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Kelola kontrak dan crew, lihat payroll</p>
                </div>
                <div>
                    <strong style="color: #3B82F6;">Finance</strong>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Kelola payroll dan laporan keuangan</p>
                </div>
                <div>
                    <strong style="color: #F59E0B;">Manager</strong>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Lihat semua, approve kontrak dan payroll</p>
                </div>
                <div>
                    <strong style="color: #6B7280;">Viewer</strong>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Hanya bisa melihat, tidak bisa mengubah data</p>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
            <a href="<?= BASE_URL ?>users" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?= $isEdit ? 'Update User' : 'Simpan User' ?>
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
