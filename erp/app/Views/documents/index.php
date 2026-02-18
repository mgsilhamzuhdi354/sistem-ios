<?php
/**
 * Document Management Overview
 */
$currentPage = 'documents';
ob_start();
?>

<div class="page-header">
    <h1><i class="fas fa-folder-open"></i> <span data-translate="document_management">Manajemen Dokumen</span></h1>
    <p data-translate="document_subtitle">Kelola dokumen kru dan tracking expiry</p>
</div>

<!-- Stats Cards -->
<div class="grid-4" style="gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: var(--success);">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $statusCounts['valid'] ?? 0 ?></span>
            <span class="stat-label" data-translate="status_valid">Valid</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: var(--warning);">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $statusCounts['expiring_soon'] ?? 0 ?></span>
            <span class="stat-label" data-translate="stat_expiring_soon">Segera Berakhir</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: var(--danger);">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= $statusCounts['expired'] ?? 0 ?></span>
            <span class="stat-label" data-translate="status_expired">Expired</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(139, 92, 246, 0.15); color: #8B5CF6;">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?= array_sum($statusCounts) ?></span>
            <span class="stat-label" data-translate="total_documents">Total Dokumen</span>
        </div>
    </div>
</div>

<!-- Expired Documents Alert -->
<?php if (!empty($expired)): ?>
    <div class="card" style="margin-bottom: 24px; border-left: 4px solid var(--danger);">
        <h4 style="margin-bottom: 16px; color: var(--danger);">
            <i class="fas fa-exclamation-circle"></i> <span data-translate="expired_documents">Dokumen Expired</span>
            (<?= count($expired) ?>)
        </h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th data-translate="crew">Crew</th>
                    <th data-translate="document">Dokumen</th>
                    <th data-translate="type">Tipe</th>
                    <th data-translate="expired_on">Expired Pada</th>
                    <th data-translate="th_actions">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($expired, 0, 10) as $doc): ?>
                    <tr>
                        <td>
                            <a href="<?= BASE_URL ?>crews/<?= $doc['crew_id'] ?>"><?= htmlspecialchars($doc['crew_name']) ?></a>
                            <div style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($doc['employee_id']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($doc['document_name']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($doc['type_name'] ?? $doc['document_type']) ?></span></td>
                        <td style="color: var(--danger);"><?= date('d M Y', strtotime($doc['expiry_date'])) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>documents/<?= $doc['crew_id'] ?>" class="btn-icon" title="View Documents">
                                <i class="fas fa-folder-open"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Expiring Soon -->
<?php if (!empty($expiring)): ?>
    <div class="card" style="border-left: 4px solid var(--warning);">
        <h4 style="margin-bottom: 16px; color: var(--warning);">
            <i class="fas fa-clock"></i> <span data-translate="expiring_within_90days">Akan Expired dalam 90 Hari</span>
            (<?= count($expiring) ?>)
        </h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Crew</th>
                    <th>Document</th>
                    <th>Type</th>
                    <th>Expires On</th>
                    <th>Days Left</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiring as $doc): ?>
                    <?php $daysLeft = max(0, floor((strtotime($doc['expiry_date']) - time()) / 86400)); ?>
                    <tr>
                        <td>
                            <a href="<?= BASE_URL ?>crews/<?= $doc['crew_id'] ?>"><?= htmlspecialchars($doc['crew_name']) ?></a>
                            <div style="font-size: 12px; color: var(--text-muted);"><?= htmlspecialchars($doc['employee_id']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($doc['document_name']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars($doc['type_name'] ?? $doc['document_type']) ?></span></td>
                        <td><?= date('d M Y', strtotime($doc['expiry_date'])) ?></td>
                        <td>
                            <?php
                            $urgencyColor = $daysLeft < 30 ? 'var(--danger)' : ($daysLeft < 60 ? 'var(--warning)' : 'var(--info)');
                            ?>
                            <span style="color: <?= $urgencyColor ?>; font-weight: 600;"><?= $daysLeft ?> days</span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>documents/<?= $doc['crew_id'] ?>" class="btn-icon" title="View Documents">
                                <i class="fas fa-folder-open"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (empty($expired) && empty($expiring)): ?>
    <div class="card" style="text-align: center; padding: 60px;">
        <i class="fas fa-check-circle" style="font-size: 64px; color: var(--success); margin-bottom: 20px;"></i>
        <h3 data-translate="all_documents_valid">Semua Dokumen Valid!</h3>
        <p style="color: var(--text-muted);" data-translate="no_expired_docs_message">Tidak ada dokumen yang expired atau
            akan expired dalam 90 hari.</p>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>