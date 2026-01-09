<?php
/**
 * Crew Documents View
 */
$currentPage = 'documents';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="<?= BASE_URL ?>crews/<?= $crew['id'] ?>" class="btn-icon" style="width: 40px; height: 40px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1><i class="fas fa-folder-open"></i> Documents: <?= htmlspecialchars($crew['full_name']) ?></h1>
            <p>
                <code style="background: rgba(212, 175, 55, 0.2); color: var(--accent-gold); padding: 2px 8px; border-radius: 4px;">
                    <?= htmlspecialchars($crew['employee_id']) ?>
                </code>
            </p>
        </div>
    </div>
    <?php if ($this->checkPermission('documents', 'create')): ?>
    <a href="<?= BASE_URL ?>documents/upload/<?= $crew['id'] ?>" class="btn btn-primary">
        <i class="fas fa-upload"></i> Upload Document
    </a>
    <?php endif; ?>
</div>

<?php if (empty($documents)): ?>
<div class="card" style="text-align: center; padding: 60px;">
    <i class="fas fa-folder-open" style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px; opacity: 0.3;"></i>
    <h3>Belum Ada Dokumen</h3>
    <p style="color: var(--text-muted); margin-bottom: 20px;">Upload dokumen pertama untuk crew ini.</p>
    <?php if ($this->checkPermission('documents', 'create')): ?>
    <a href="<?= BASE_URL ?>documents/upload/<?= $crew['id'] ?>" class="btn btn-primary">
        <i class="fas fa-upload"></i> Upload Document
    </a>
    <?php endif; ?>
</div>
<?php else: ?>

<?php
// Group documents by category
$grouped = [];
foreach ($documents as $doc) {
    $category = $doc['category'] ?? 'other';
    $grouped[$category][] = $doc;
}

$categoryNames = [
    'identity' => ['Identity Documents', 'fas fa-id-card', '#3B82F6'],
    'license' => ['Licenses & Certificates', 'fas fa-certificate', '#10B981'],
    'training' => ['Training Certificates', 'fas fa-graduation-cap', '#8B5CF6'],
    'medical' => ['Medical Documents', 'fas fa-heartbeat', '#EF4444'],
    'other' => ['Other Documents', 'fas fa-file-alt', '#6B7280']
];
?>

<?php foreach ($categoryNames as $catKey => $catInfo): ?>
    <?php if (!empty($grouped[$catKey])): ?>
        <div class="card" style="margin-bottom: 20px;">
            <h4 style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                <i class="<?= $catInfo[1] ?>" style="color: <?= $catInfo[2] ?>;"></i>
                <?= $catInfo[0] ?> (<?= count($grouped[$catKey]) ?>)
            </h4>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Number</th>
                            <th>Issue Date</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grouped[$catKey] as $doc): ?>
                            <?php
                            $daysLeft = $doc['expiry_date'] ? floor((strtotime($doc['expiry_date']) - time()) / 86400) : null;
                            $statusClass = 'success';
                            $statusText = 'Valid';
                            
                            if ($doc['status'] === 'expired' || ($daysLeft !== null && $daysLeft < 0)) {
                                $statusClass = 'danger';
                                $statusText = 'Expired';
                            } elseif ($doc['status'] === 'expiring_soon' || ($daysLeft !== null && $daysLeft < 90)) {
                                $statusClass = 'warning';
                                $statusText = 'Expiring Soon';
                            } elseif ($doc['status'] === 'pending') {
                                $statusClass = 'secondary';
                                $statusText = 'Pending';
                            }
                            ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 36px; height: 36px; background: rgba(139, 92, 246, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-<?= strpos($doc['mime_type'] ?? '', 'pdf') !== false ? 'file-pdf' : 'file-image' ?>" style="color: #8B5CF6;"></i>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($doc['document_name']) ?></strong>
                                            <div style="font-size: 12px; color: var(--text-muted);">
                                                <?= htmlspecialchars($doc['type_name_id'] ?? $doc['type_name'] ?? $doc['document_type']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($doc['document_number'] ?: '-') ?></td>
                                <td><?= $doc['issue_date'] ? date('d M Y', strtotime($doc['issue_date'])) : '-' ?></td>
                                <td>
                                    <?php if ($doc['expiry_date']): ?>
                                        <span style="color: var(--<?= $statusClass ?>);">
                                            <?= date('d M Y', strtotime($doc['expiry_date'])) ?>
                                        </span>
                                        <?php if ($daysLeft !== null): ?>
                                            <div style="font-size: 11px; color: var(--text-muted);">
                                                <?= $daysLeft < 0 ? abs($daysLeft) . ' days ago' : $daysLeft . ' days left' ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">No expiry</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span>
                                    <?php if ($doc['verified_at']): ?>
                                        <div style="font-size: 11px; color: var(--success); margin-top: 4px;">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 4px;">
                                        <a href="<?= BASE_URL ?>documents/preview/<?= $doc['id'] ?>" target="_blank" class="btn-icon" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>documents/download/<?= $doc['id'] ?>" class="btn-icon" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <?php if ($this->checkPermission('documents', 'edit') && !$doc['verified_at']): ?>
                                        <a href="<?= BASE_URL ?>documents/verify/<?= $doc['id'] ?>" class="btn-icon" title="Verify" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($this->checkPermission('documents', 'delete')): ?>
                                        <button onclick="deleteDocument(<?= $doc['id'] ?>, '<?= htmlspecialchars($doc['document_name']) ?>')" class="btn-icon" title="Delete" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php endif; ?>

<!-- Delete Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closeDeleteModal()"></div>
    <div class="modal-content" style="max-width: 400px;">
        <h3 style="margin-bottom: 16px; color: var(--danger);">
            <i class="fas fa-exclamation-triangle"></i> Hapus Document
        </h3>
        <p id="deleteMessage" style="margin-bottom: 24px;"></p>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
function deleteDocument(id, name) {
    document.getElementById('deleteMessage').textContent = 'Apakah Anda yakin ingin menghapus "' + name + '"?';
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>documents/delete/' + id;
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
