<?php
/**
 * Contract List View
 */
$currentPage = 'contracts';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 data-translate="contract_management">Manajemen Kontrak</h1>
        <p data-translate="contract_management_subtitle">Kelola kontrak crew, perpanjangan, dan pemutusan</p>
    </div>
    <div style="display: flex; gap: 12px; align-items: center;">
        <a href="<?= BASE_URL ?>contracts/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> <span data-translate="btn_new_contract">Kontrak Baru</span>
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="<?= BASE_URL ?>contracts" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
            <label class="form-label" data-translate="btn_search">Cari</label>
            <input type="text" name="search" class="form-control" data-translate-placeholder="search_contract_crew" placeholder="No Kontrak atau Nama Crew" 
                   value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin: 0;">
            <label class="form-label" data-translate="label_status">Status</label>
            <select name="status" class="form-control" style="min-width: 150px;">
                <option value="" data-translate="filter_all_status">Semua Status</option>
                <?php foreach ($statuses as $key => $label): ?>
                    <option value="<?= $key ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label class="form-label" data-translate="th_vessel">Kapal</label>
            <select name="vessel_id" class="form-control" style="min-width: 150px;">
                <option value="" data-translate="filter_all_vessels">Semua Kapal</option>
                <?php foreach ($vessels as $v): ?>
                    <option value="<?= $v['id'] ?>" <?= ($filters['vessel_id'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label class="form-label" data-translate="client">Klien</label>
            <select name="client_id" class="form-control" style="min-width: 150px;">
                <option value="" data-translate="filter_all_clients">Semua Klien</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($filters['client_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> <span data-translate="btn_filter">Filter</span></button>
        <a href="<?= BASE_URL ?>contracts" class="btn btn-secondary"><i class="fas fa-times"></i></a>
    </form>
</div>

<!-- Contracts Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th data-translate="th_contract_no">No Kontrak</th>
                <th data-translate="th_crew_name">Nama Crew</th>
                <th data-translate="th_rank">Jabatan</th>
                <th data-translate="th_vessel">Kapal</th>
                <th data-translate="client">Klien</th>
                <th data-translate="th_sign_on">Naik Kapal</th>
                <th data-translate="th_sign_off">Turun Kapal</th>
                <th data-translate="th_remaining">Sisa</th>
                <th data-translate="th_status">Status</th>
                <th data-translate="th_actions">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contracts)): ?>
                <tr><td colspan="10" style="text-align: center; color: var(--text-muted); padding: 40px;">
                    <i class="fas fa-file-contract" style="font-size: 40px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                    <span data-translate="no_contracts_found">Tidak ada kontrak</span>
                </td></tr>
            <?php else: ?>
                <?php foreach ($contracts as $contract): ?>
                    <?php
                    $statusColors = [
                        'draft' => 'secondary', 'pending_approval' => 'warning', 'active' => 'success',
                        'onboard' => 'info', 'completed' => 'info', 'terminated' => 'danger', 'cancelled' => 'secondary'
                    ];
                    $days = $contract['days_remaining'] ?? null;
                    $daysClass = $days <= 7 ? 'danger' : ($days <= 30 ? 'warning' : 'success');
                    ?>
                    <tr>
                        <td><a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" style="color: var(--accent-gold); font-weight: 500;"><?= htmlspecialchars($contract['contract_no']) ?></a></td>
                        <td><strong><?= htmlspecialchars($contract['crew_name']) ?></strong></td>
                        <td><?= htmlspecialchars($contract['rank_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($contract['client_name'] ?? '-') ?></td>
                        <td><?= $contract['sign_on_date'] ? date('d M Y', strtotime($contract['sign_on_date'])) : '-' ?></td>
                        <td><?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></td>
                        <td>
                            <?php if ($days !== null && in_array($contract['status'], ['active', 'onboard'])): ?>
                                <span class="badge badge-<?= $daysClass ?>"><?= $days ?> <span data-translate="days">hari</span></span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-<?= $statusColors[$contract['status']] ?? 'secondary' ?>"><?= ucfirst(str_replace('_', ' ', $contract['status'])) ?></span></td>
                        <td style="white-space: nowrap;">
                            <a href="<?= BASE_URL ?>contracts/<?= $contract['id'] ?>" class="btn-icon" title="View"><i class="fas fa-eye"></i></a>
                            <a href="<?= BASE_URL ?>contracts/edit/<?= $contract['id'] ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                            <?php if (in_array($contract['status'], ['active', 'onboard'])): ?>
                                <a href="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>" class="btn-icon" title="Renew" style="color: var(--success);"><i class="fas fa-redo"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total > $perPage): ?>
    <div class="pagination">
        <span class="pagination-info"><span data-translate="showing">Menampilkan</span> <?= (($page - 1) * $perPage) + 1 ?> - <?= min($page * $perPage, $total) ?> <span data-translate="of">dari</span> <?= $total ?> <span data-translate="contracts">kontrak</span></span>
        <div class="pagination-buttons">
            <?php $totalPages = ceil($total / $perPage); ?>
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= min($totalPages, 5); $i++): ?>
                <a href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&<?= http_build_query(array_filter($filters)) ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
