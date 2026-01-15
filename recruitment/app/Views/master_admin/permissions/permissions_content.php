<style>
/* Modern Permissions Page Styles */
.permission-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #0d1f33 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 25px;
    box-shadow: 0 10px 40px rgba(30, 58, 95, 0.3);
}
.permission-header h2 { margin: 0; font-weight: 600; }
.permission-header p { margin: 10px 0 0; opacity: 0.8; }

.role-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}
.role-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12);
}
.role-card-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}
.role-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-right: 15px;
    color: white;
}
.role-icon.admin { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.role-icon.leader { background: linear-gradient(135deg, #10b981, #059669); }
.role-icon.crewing { background: linear-gradient(135deg, #f59e0b, #d97706); }

.permission-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 8px;
    background: #f8fafc;
    transition: all 0.2s ease;
}
.permission-item:hover { background: #e2e8f0; transform: translateX(5px); }
.permission-item.checked { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-left: 3px solid #22c55e; }

.permission-toggle {
    position: relative;
    width: 52px;
    height: 28px;
}
.permission-toggle input { opacity: 0; width: 0; height: 0; }
.permission-toggle .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: 0.3s;
    border-radius: 28px;
}
.permission-toggle .slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.permission-toggle input:checked + .slider { background: linear-gradient(135deg, #22c55e, #16a34a); }
.permission-toggle input:checked + .slider:before { transform: translateX(24px); }

.save-btn {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 12px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
}
.save-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
    background: linear-gradient(135deg, #ef4444, #dc2626);
}
.save-btn:active { transform: scale(0.98); }

.quick-action-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
}
.quick-action-btn:hover { transform: scale(1.05); }

/* Save Animation Overlay */
.save-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(5px);
}
.save-overlay.show { display: flex; animation: fadeIn 0.3s ease; }
.save-modal {
    background: white;
    padding: 50px;
    border-radius: 20px;
    text-align: center;
    animation: scaleIn 0.4s ease;
    max-width: 400px;
}
.save-modal .icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    animation: bounce 0.6s ease;
}
.save-modal .icon i { font-size: 40px; color: white; }
.save-modal h3 { margin: 0 0 10px; color: #1e3a5f; font-weight: 600; }
.save-modal p { color: #64748b; margin: 0; }

.checkmark-path {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: drawCheck 0.6s ease forwards 0.3s;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
@keyframes bounce { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
@keyframes drawCheck { to { stroke-dashoffset: 0; } }
@keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); } 70% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); } }

/* Category Badge */
.category-badge {
    background: linear-gradient(135deg, #1e3a5f, #0d1f33);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 15px;
    display: inline-block;
}

/* Animations */
.fade-in { animation: fadeIn 0.5s ease; }
.slide-up { animation: slideUp 0.5s ease; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<!-- Save Animation Overlay -->
<div class="save-overlay" id="saveOverlay">
    <div class="save-modal">
        <div class="icon">
            <i class="fas fa-check"></i>
        </div>
        <h3>Tersimpan!</h3>
        <p>Pengaturan hak akses berhasil disimpan</p>
    </div>
</div>

<div class="permission-header fade-in">
    <h2><i class="fas fa-shield-alt me-3"></i>Kelola Hak Akses</h2>
    <p>Atur fitur yang dapat diakses oleh setiap role pengguna</p>
</div>

<?php if (empty($permissions)): ?>
<div class="alert alert-warning slide-up">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Belum ada permissions di database!</strong><br>
    Jalankan SQL untuk menambahkan permissions terlebih dahulu.
</div>
<?php else: ?>

<div class="row">
    <?php foreach ($roles as $index => $role): ?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="role-card slide-up" style="animation-delay: <?= $index * 0.1 ?>s">
            <div class="role-card-header">
                <div class="role-icon <?= strtolower($role['name']) ?>">
                    <?php if ($role['name'] == 'admin'): ?>
                        <i class="fas fa-user-cog"></i>
                    <?php elseif ($role['name'] == 'leader'): ?>
                        <i class="fas fa-user-tie"></i>
                    <?php else: ?>
                        <i class="fas fa-users"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h5 class="mb-1" style="font-weight: 600;"><?= ucfirst($role['name']) ?></h5>
                    <small class="text-muted"><?= $role['description'] ?? 'Kelola hak akses' ?></small>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="quick-action-btn btn btn-outline-success btn-sm flex-fill" onclick="selectAll(<?= $role['id'] ?>)">
                    <i class="fas fa-check-double me-1"></i> Semua
                </button>
                <button type="button" class="quick-action-btn btn btn-outline-danger btn-sm flex-fill" onclick="deselectAll(<?= $role['id'] ?>)">
                    <i class="fas fa-times me-1"></i> Hapus
                </button>
            </div>
            
            <!-- Permissions List -->
            <div class="permissions-list" style="max-height: 350px; overflow-y: auto;">
                <?php 
                $currentCategory = '';
                foreach ($permissions as $perm): 
                    if ($perm['category'] !== $currentCategory):
                        $currentCategory = $perm['category'];
                ?>
                <div class="category-badge">
                    <i class="fas fa-folder me-1"></i> <?= $currentCategory ?>
                </div>
                <?php endif; 
                    $isChecked = in_array($perm['id'], $rolePermissions[$role['id']] ?? []);
                ?>
                <div class="permission-item <?= $isChecked ? 'checked' : '' ?>" id="item-<?= $role['id'] ?>-<?= $perm['id'] ?>">
                    <div>
                        <div style="font-weight: 500; color: #1e293b;"><?= htmlspecialchars($perm['display_name']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($perm['name']) ?></small>
                    </div>
                    <label class="permission-toggle">
                        <input type="checkbox" 
                               class="permission-checkbox"
                               data-role="<?= $role['id'] ?>" 
                               data-permission="<?= $perm['id'] ?>"
                               <?= $isChecked ? 'checked' : '' ?>
                               onchange="togglePermission(this)">
                        <span class="slider"></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Save Button -->
            <form action="<?= url('/master-admin/permissions/update/' . $role['id']) ?>" method="POST" id="form-role-<?= $role['id'] ?>" onsubmit="return handleSave(this, event)">
                <?= csrf_field() ?>
                <div class="permissions-container" id="permissions-<?= $role['id'] ?>"></div>
                <button type="submit" class="save-btn w-100 mt-3">
                    <i class="fas fa-save me-2"></i>Simpan <?= ucfirst($role['name']) ?>
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
// Toggle permission item visual
function togglePermission(checkbox) {
    const item = document.getElementById('item-' + checkbox.dataset.role + '-' + checkbox.dataset.permission);
    if (checkbox.checked) {
        item.classList.add('checked');
    } else {
        item.classList.remove('checked');
    }
    updateFormInputs(checkbox.dataset.role);
}

// Update hidden form inputs
function updateFormInputs(roleId) {
    const container = document.getElementById('permissions-' + roleId);
    if (!container) return;
    container.innerHTML = '';
    
    document.querySelectorAll('.permission-checkbox[data-role="' + roleId + '"]:checked').forEach(function(checkbox) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'permissions[]';
        input.value = checkbox.dataset.permission;
        container.appendChild(input);
    });
}

// Handle save with animation
function handleSave(form, event) {
    event.preventDefault();
    
    // Show save animation
    const overlay = document.getElementById('saveOverlay');
    overlay.classList.add('show');
    
    // Submit form after animation
    setTimeout(() => {
        form.submit();
    }, 1500);
    
    return false;
}

// Select all permissions for a role
function selectAll(roleId) {
    document.querySelectorAll('.permission-checkbox[data-role="' + roleId + '"]').forEach(function(checkbox) {
        checkbox.checked = true;
        togglePermission(checkbox);
    });
}

// Deselect all permissions for a role
function deselectAll(roleId) {
    document.querySelectorAll('.permission-checkbox[data-role="' + roleId + '"]').forEach(function(checkbox) {
        checkbox.checked = false;
        togglePermission(checkbox);
    });
}

// Initialize form inputs on page load
<?php foreach ($roles as $role): ?>
updateFormInputs(<?= $role['id'] ?>);
<?php endforeach; ?>

// Close overlay on click outside
document.getElementById('saveOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('show');
    }
});
</script>
<?php endif; ?>
