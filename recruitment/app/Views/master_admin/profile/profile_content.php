<style>
.profile-container { max-width: 900px; margin: 0 auto; }
.profile-header { background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); border-radius: 16px; padding: 2rem; color: white; margin-bottom: 2rem; display: flex; align-items: center; gap: 2rem; }
.avatar-container { position: relative; }
.avatar-large { width: 120px; height: 120px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #64748b; overflow: hidden; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.avatar-large img { width: 100%; height: 100%; object-fit: cover; }
.avatar-upload-btn { position: absolute; bottom: 0; right: 0; width: 36px; height: 36px; background: #3182ce; border: 3px solid white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; transition: all 0.2s; }
.avatar-upload-btn:hover { background: #2c5282; transform: scale(1.1); }
.profile-info h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
.role-badge { background: rgba(255,255,255,0.2); padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.85rem; display: inline-block; }
.profile-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
.profile-card-header { padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem; }
.profile-card-body { padding: 1.5rem; }
.form-label { font-weight: 500; color: #374151; margin-bottom: 0.5rem; }
.form-control { border-radius: 8px; border: 1px solid #d1d5db; padding: 0.75rem 1rem; width: 100%; }
.form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15); outline: none; }
.btn-save { background: linear-gradient(135deg, #3182ce, #2c5282); color: white; border: none; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(49, 130, 206, 0.25); }
.btn-danger-outline { background: transparent; color: #dc3545; border: 2px solid #dc3545; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-danger-outline:hover { background: #dc3545; color: white; }
.row { display: flex; gap: 1rem; flex-wrap: wrap; }
.col-md-6 { flex: 1; min-width: 250px; }
.col-md-4 { flex: 1; min-width: 200px; }
.mb-3 { margin-bottom: 1rem; }
</style>

<div class="profile-container">
    <div class="profile-header">
        <div class="avatar-container">
            <div class="avatar-large">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= url('/uploads/avatars/' . $user['avatar']) ?>" alt="Avatar" id="avatarPreview">
                <?php else: ?>
                    <i class="fas fa-user" id="avatarIcon"></i>
                    <img src="" alt="Avatar" id="avatarPreview" style="display: none;">
                <?php endif; ?>
            </div>
            <label for="avatarInput" class="avatar-upload-btn"><i class="fas fa-camera"></i></label>
            <input type="file" id="avatarInput" accept="image/*" style="display: none;">
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['full_name'] ?? 'User') ?></h1>
            <p style="opacity: 0.9; margin-bottom: 0.5rem;"><?= htmlspecialchars($user['email'] ?? '') ?></p>
            <span class="role-badge"><i class="fas fa-shield-alt me-1"></i> Master Admin</span>
        </div>
    </div>
    
    <div class="profile-card">
        <div class="profile-card-header"><i class="fas fa-user text-primary"></i> Personal Information</div>
        <div class="profile-card-body">
            <form action="<?= url('/master-admin/profile/update') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+62 xxx xxxx xxxx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="<?= htmlspecialchars($user['employee_id'] ?? '') ?>" placeholder="EMP001">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($user['department'] ?? '') ?>" placeholder="IT / HR / Operations">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($user['position'] ?? '') ?>" placeholder="Manager / Staff">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" class="form-control" rows="3" placeholder="Tell us about yourself..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn-save"><i class="fas fa-save me-2"></i> Save Changes</button>
            </form>
        </div>
    </div>
    
    <div class="profile-card">
        <div class="profile-card-header"><i class="fas fa-lock text-warning"></i> Change Password</div>
        <div class="profile-card-body">
            <form action="<?= url('/master-admin/profile/change-password') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Current Password *</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">New Password *</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn-danger-outline"><i class="fas fa-key me-2"></i> Change Password</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('avatarPreview').src = e.target.result;
        document.getElementById('avatarPreview').style.display = 'block';
        const icon = document.getElementById('avatarIcon');
        if (icon) icon.style.display = 'none';
    };
    reader.readAsDataURL(file);
    
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('csrf_token', '<?= csrf_token() ?>');
    
    fetch('<?= url('/master-admin/profile/avatar') ?>', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => { alert(data.success ? '✅ ' + data.message : '❌ ' + data.message); });
});
</script>
