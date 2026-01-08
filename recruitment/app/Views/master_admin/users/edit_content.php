<!-- Edit User Content -->
<style>
.edit-container { max-width: 700px; margin: 0 auto; }
.edit-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
.edit-header { background: linear-gradient(135deg, #1e3a5f, #2c5282); color: white; padding: 1.5rem 2rem; }
.edit-header h2 { margin: 0; font-size: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
.edit-body { padding: 2rem; }
.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
.form-group label small { font-weight: 400; color: #64748b; }
.form-control { width: 100%; padding: 0.875rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem; transition: all 0.2s; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-select { width: 100%; padding: 0.875rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 1rem; background: white; }

.status-toggle { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 10px; }
.toggle-switch { position: relative; width: 50px; height: 26px; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #cbd5e1; border-radius: 26px; transition: 0.3s; }
.toggle-slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
input:checked + .toggle-slider { background: #10b981; }
input:checked + .toggle-slider:before { transform: translateX(24px); }

.role-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; }
.role-badge.admin { background: #dbeafe; color: #1d4ed8; }
.role-badge.leader { background: #fef3c7; color: #d97706; }
.role-badge.crewing { background: #dcfce7; color: #16a34a; }
.role-badge.master { background: #f3e8ff; color: #7c3aed; }

.btn-row { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; }
.btn { padding: 0.875rem 1.5rem; border-radius: 10px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; }
.btn-primary { background: linear-gradient(135deg, #1e3a5f, #2c5282); color: white; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(30, 58, 95, 0.3); }
.btn-secondary { background: #f1f5f9; color: #64748b; }
.btn-secondary:hover { background: #e2e8f0; }

.user-avatar-preview { width: 80px; height: 80px; border-radius: 16px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; color: #64748b; margin-bottom: 1rem; overflow: hidden; }
.user-avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
</style>

<div class="edit-container">
    <div class="edit-card">
        <div class="edit-header">
            <h2><i class="fas fa-user-edit"></i> Edit User</h2>
        </div>
        <div class="edit-body">
            <form action="<?= url('/master-admin/users/update/' . $user['id']) ?>" method="POST">
                <?= csrf_field() ?>
                
                <!-- User Avatar & Role Badge -->
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div class="user-avatar-preview" style="margin: 0 auto;">
                        <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= url('/uploads/avatars/' . $user['avatar']) ?>" alt="">
                        <?php else: ?>
                        <?= strtoupper(substr($user['full_name'], 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top: 1rem;">
                        <?php
                        $roleClass = 'admin';
                        $roleIcon = 'fa-user-shield';
                        $roleName = 'Admin';
                        if ($user['role_id'] == ROLE_LEADER) {
                            $roleClass = 'leader';
                            $roleIcon = 'fa-user-tie';
                            $roleName = 'Leader';
                        } elseif ($user['role_id'] == ROLE_CREWING) {
                            $roleClass = 'crewing';
                            $roleIcon = 'fa-id-badge';
                            $roleName = 'Crewing Staff';
                        } elseif ($user['role_id'] == ROLE_MASTER_ADMIN) {
                            $roleClass = 'master';
                            $roleIcon = 'fa-crown';
                            $roleName = 'Master Admin';
                        }
                        ?>
                        <span class="role-badge <?= $roleClass ?>">
                            <i class="fas <?= $roleIcon ?>"></i> <?= $roleName ?>
                        </span>
                    </div>
                </div>
                
                <!-- Full Name -->
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <!-- Password -->
                <div class="form-group">
                    <label>Password <small>(leave blank to keep current)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password...">
                </div>
                
                <!-- Leader specific fields -->
                <?php if ($user['role_id'] == ROLE_LEADER): ?>
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($user['department'] ?? '') ?>" placeholder="e.g., Recruitment, HR, Operations">
                </div>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" value="<?= htmlspecialchars($user['employee_id'] ?? '') ?>" placeholder="e.g., LDR001">
                </div>
                <?php endif; ?>
                
                <!-- Crewing specific fields -->
                <?php if ($user['role_id'] == ROLE_CREWING): ?>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" class="form-control" value="<?= htmlspecialchars($user['employee_id'] ?? '') ?>" placeholder="e.g., CRW001">
                </div>
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($user['specialization'] ?? '') ?>" placeholder="e.g., Deck, Engine, All Departments">
                </div>
                <?php endif; ?>
                
                <!-- Active Status -->
                <div class="form-group">
                    <label>Account Status</label>
                    <div class="status-toggle">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1" <?= $user['is_active'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span>
                            <strong><?= $user['is_active'] ? 'Active' : 'Inactive' ?></strong>
                            <br><small class="text-muted">Inactive users cannot login</small>
                        </span>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="btn-row">
                    <a href="<?= url('/master-admin/users') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
