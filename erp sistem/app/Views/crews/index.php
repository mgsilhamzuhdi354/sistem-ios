<?php
/**
 * Crew List View
 */
$currentPage = 'crews';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-users"></i> <span data-translate="crew_database">Crew Database</span></h1>
        <p data-translate="crew_subtitle">Kelola data kru kapal</p>
    </div>
    <?php if ($this->checkPermission('crews', 'create')): ?>
    <a href="<?= BASE_URL ?>crews/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> <span data-translate="add_crew">Tambah Crew</span>
    </a>
    <?php endif; ?>
</div>

<!-- Stats Cards -->
<div class="grid-4" style="gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: var(--success);">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= count(array_filter($crews, fn($c) => $c['status'] === 'available')) ?></span>
            <span class="stat-label">Available</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: var(--info);">
            <i class="fas fa-ship"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= count(array_filter($crews, fn($c) => $c['status'] === 'onboard')) ?></span>
            <span class="stat-label">On Board</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: var(--warning);">
            <i class="fas fa-umbrella-beach"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= count(array_filter($crews, fn($c) => $c['status'] === 'leave')) ?></span>
            <span class="stat-label">On Leave</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: #8B5CF6;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $total ?></span>
            <span class="stat-label">Total Crew</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 20px;">
    <form method="GET" action="<?= BASE_URL ?>crews" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-control" placeholder="Nama, ID, email, telepon..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        <div style="min-width: 150px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                <option value="onboard" <?= ($filters['status'] ?? '') === 'onboard' ? 'selected' : '' ?>>On Board</option>
                <option value="leave" <?= ($filters['status'] ?? '') === 'leave' ? 'selected' : '' ?>>On Leave</option>
                <option value="blacklisted" <?= ($filters['status'] ?? '') === 'blacklisted' ? 'selected' : '' ?>>Blacklisted</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>
        <a href="<?= BASE_URL ?>crews" class="btn btn-secondary">
            <i class="fas fa-times"></i> Reset
        </a>
    </form>
</div>

<!-- Crew Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Crew</th>
                <th>Employee ID</th>
                <th>Contact</th>
                <th>Rank</th>
                <th>Status</th>
                <th style="width: 140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($crews)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.3;"></i>
                        Tidak ada crew ditemukan
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($crews as $i => $crew): ?>
                    <tr>
                        <td><?= ($page - 1) * 20 + $i + 1 ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <?php if ($crew['photo']): ?>
                                    <img src="<?= BASE_URL . $crew['photo'] ?>" alt="" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($crew['full_name']) ?>&background=0A2463&color=fff&size=40" alt="" style="width: 40px; height: 40px; border-radius: 50%;">
                                <?php endif; ?>
                                <div>
                                    <strong><?= htmlspecialchars($crew['full_name']) ?></strong>
                                    <?php if ($crew['nickname']): ?>
                                        <div style="font-size: 12px; color: var(--text-muted);">"<?= htmlspecialchars($crew['nickname']) ?>"</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code style="background: rgba(212, 175, 55, 0.1); color: var(--accent-gold); padding: 4px 8px; border-radius: 4px;">
                                <?= htmlspecialchars($crew['employee_id']) ?>
                            </code>
                        </td>
                        <td>
                            <?php if ($crew['phone']): ?>
                                <div><i class="fas fa-phone" style="width: 16px; color: var(--text-muted);"></i> <?= htmlspecialchars($crew['phone']) ?></div>
                            <?php endif; ?>
                            <?php if ($crew['email']): ?>
                                <div style="font-size: 12px; color: var(--text-muted);"><i class="fas fa-envelope" style="width: 16px;"></i> <?= htmlspecialchars($crew['email']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($crew['rank_name'] ?? '-') ?></td>
                        <td>
                            <?php
                            $statusColors = [
                                'available' => ['#10B981', 'Available'],
                                'onboard' => ['#3B82F6', 'On Board'],
                                'leave' => ['#F59E0B', 'On Leave'],
                                'blacklisted' => ['#EF4444', 'Blacklisted'],
                                'retired' => ['#6B7280', 'Retired']
                            ];
                            $statusInfo = $statusColors[$crew['status']] ?? ['#6B7280', $crew['status']];
                            ?>
                            <span class="badge" style="background: <?= $statusInfo[0] ?>20; color: <?= $statusInfo[0] ?>;">
                                <?= $statusInfo[1] ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 4px;">
                                <a href="<?= BASE_URL ?>crews/<?= $crew['id'] ?>" class="btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>documents/<?= $crew['id'] ?>" class="btn-icon" title="Documents" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                                    <i class="fas fa-folder-open"></i>
                                </a>
                                <?php if ($this->checkPermission('crews', 'edit')): ?>
                                <a href="<?= BASE_URL ?>crews/edit/<?= $crew['id'] ?>" class="btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
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
        for ($i = 1; $i <= min($totalPages, 10); $i++):
        ?>
            <a href="<?= BASE_URL ?>crews?page=<?= $i ?>&status=<?= $filters['status'] ?? '' ?>&search=<?= urlencode($filters['search'] ?? '') ?>" 
               class="btn <?= $i == $page ? 'btn-primary' : 'btn-secondary' ?>" style="min-width: 40px;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
