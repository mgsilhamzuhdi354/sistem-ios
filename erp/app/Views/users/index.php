<?php
/**
 * User Management - List View
 */
$currentPage = 'users';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-users-cog"></i> User Management</h1>
        <p>Kelola pengguna sistem</p>
    </div>
    <?php if ($this->checkPermission('users', 'create')): ?>
    <a href="<?= BASE_URL ?>users/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah User
    </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="<?= BASE_URL ?>users" class="filter-form" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Username, email, nama..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        <div style="min-width: 150px;">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
                <option value="">Semua Role</option>
                <option value="super_admin" <?= ($filters['role'] ?? '') === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="hr" <?= ($filters['role'] ?? '') === 'hr' ? 'selected' : '' ?>>HR</option>
                <option value="finance" <?= ($filters['role'] ?? '') === 'finance' ? 'selected' : '' ?>>Finance</option>
                <option value="manager" <?= ($filters['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                <option value="viewer" <?= ($filters['role'] ?? '') === 'viewer' ? 'selected' : '' ?>>Viewer</option>
            </select>
        </div>
        <div style="min-width: 120px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="1" <?= isset($filters['is_active']) && $filters['is_active'] === 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= isset($filters['is_active']) && $filters['is_active'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>
        <a href="<?= BASE_URL ?>users" class="btn btn-secondary">
            <i class="fas fa-times"></i> Reset
        </a>
    </form>
</div>

<!-- Users Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Last Login</th>
                <th>Status</th>
                <th style="width: 120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.3;"></i>
                        Tidak ada user ditemukan
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $i => $user): ?>
                    <tr>
                        <td><?= ($page - 1) * 20 + $i + 1 ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name']) ?>&background=0A2463&color=fff&size=40" 
                                     alt="" style="width: 40px; height: 40px; border-radius: 50%;">
                                <div>
                                    <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                                    <div style="font-size: 12px; color: var(--text-muted);">@<?= htmlspecialchars($user['username']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php
                            $roleColors = [
                                'super_admin' => ['#EF4444', 'Super Admin'],
                                'admin' => ['#8B5CF6', 'Admin'],
                                'hr' => ['#10B981', 'HR'],
                                'finance' => ['#3B82F6', 'Finance'],
                                'manager' => ['#F59E0B', 'Manager'],
                                'viewer' => ['#6B7280', 'Viewer']
                            ];
                            $roleInfo = $roleColors[$user['role']] ?? ['#6B7280', $user['role']];
                            ?>
                            <span class="badge" style="background: <?= $roleInfo[0] ?>20; color: <?= $roleInfo[0] ?>; padding: 6px 12px; border-radius: 6px;">
                                <?= $roleInfo[1] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['last_login']): ?>
                                <span title="<?= $user['last_login'] ?>">
                                    <?= date('d M Y H:i', strtotime($user['last_login'])) ?>
                                </span>
                            <?php else: ?>
                                <span style="color: var(--text-muted);">Belum pernah</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['is_active']): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 4px;">
                                <a href="<?= BASE_URL ?>users/<?= $user['id'] ?>" class="btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($this->checkPermission('users', 'edit')): ?>
                                <a href="<?= BASE_URL ?>users/edit/<?= $user['id'] ?>" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($this->checkPermission('users', 'delete') && $user['id'] != $this->getCurrentUser()['id']): ?>
                                <button onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" 
                                        class="btn-icon" title="Delete" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total > 20): ?>
    <div class="pagination" style="display: flex; justify-content: center; gap: 8px; padding: 20px;">
        <?php 
        $totalPages = ceil($total / 20);
        for ($i = 1; $i <= $totalPages; $i++):
        ?>
            <a href="<?= BASE_URL ?>users?page=<?= $i ?>&role=<?= $filters['role'] ?? '' ?>&search=<?= $filters['search'] ?? '' ?>" 
               class="btn <?= $i == $page ? 'btn-primary' : 'btn-secondary' ?>" style="min-width: 40px;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="modal-content" style="max-width: 400px;">
        <h3 style="margin-bottom: 16px; color: var(--danger);">
            <i class="fas fa-exclamation-triangle"></i> Nonaktifkan User
        </h3>
        <p id="deleteMessage" style="margin-bottom: 24px;"></p>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-danger">Nonaktifkan</button>
            </div>
        </form>
    </div>
</div>

<script>
function deleteUser(id, username) {
    document.getElementById('deleteMessage').textContent = 'Apakah Anda yakin ingin menonaktifkan user "' + username + '"?';
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>users/delete/' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
