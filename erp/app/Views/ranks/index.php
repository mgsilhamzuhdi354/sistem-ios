<?php
$currentPage = 'ranks';
ob_start();
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-medal" style="color: var(--accent-gold);"></i> Master Pangkat (Ranks)</h1>
        <p>Manajemen daftar pangkat, departemen, dan urutan.</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>ranks/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Pangkat
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px; padding: 20px;">
    <form action="" method="GET" style="display: flex; gap: 15px; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex-grow: 1;">
            <label>Cari Pangkat</label>
            <input type="text" name="search" class="form-control" placeholder="Nama pangkat..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        
        <div class="form-group" style="margin-bottom: 0; width: 200px;">
            <label>Department</label>
            <select name="department" class="form-control">
                <option value="">Semua Dept</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= $dept ?>" <?= ($filters['department'] ?? '') === $dept ? 'selected' : '' ?>><?= $dept ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if (!empty($filters['search']) || !empty($filters['department'])): ?>
            <a href="<?= BASE_URL ?>ranks" class="btn btn-text">Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- List -->
<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Level</th>
                <th>Nama Pangkat</th>
                <th>Code</th>
                <th>Department</th>
                <th style="text-align: center;">Officer?</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ranks)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                        Tidak ada data pangkat ditemukan.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($ranks as $rank): ?>
                <tr>
                    <td><span class="badge badge-secondary"><?= $rank['level'] ?></span></td>
                    <td><strong><?= htmlspecialchars($rank['name']) ?></strong></td>
                    <td><?= htmlspecialchars($rank['code'] ?? '-') ?></td>
                    <td>
                        <?php
                        $deptColors = [
                            'Deck' => '#3B82F6',
                            'Engine' => '#EF4444', 
                            'Galley' => '#F59E0B',
                            'Hotel' => '#10B981'
                        ];
                        $color = $deptColors[$rank['department']] ?? '#6B7280';
                        ?>
                        <span style="color: <?= $color ?>; font-weight: bold;"><?= $rank['department'] ?></span>
                    </td>
                    <td style="text-align: center;">
                        <?php if ($rank['is_officer']): ?>
                            <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        <?php else: ?>
                            <span style="color: #ccc;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($rank['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                        <a href="<?= BASE_URL ?>ranks/edit/<?= $rank['id'] ?>" class="btn btn-sm btn-secondary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="<?= BASE_URL ?>ranks/delete/<?= $rank['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus pangkat ini?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total > $perPage): ?>
    <div style="margin-top: 20px; text-align: center;">
        <?php
        $totalPages = ceil($total / $perPage);
        for ($i = 1; $i <= $totalPages; $i++):
        ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($filters['search'] ?? '') ?>&department=<?= $filters['department'] ?? '' ?>" 
               class="btn <?= $i === $page ? 'btn-primary' : 'btn-secondary' ?>" 
               style="min-width: 30px; padding: 5px 10px;">
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
