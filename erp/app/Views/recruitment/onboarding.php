<?php
/**
 * Candidate Onboarding View
 */
$currentPage = 'recruitment-onboarding';
ob_start();
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><i class="fas fa-user-check" style="color: var(--success);"></i> <span
                    data-translate="candidate_onboarding">Candidate Onboarding</span></h1>
            <p data-translate="onboarding_subtitle">Approved candidates ready for import to ERP</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>recruitment/pipeline" class="btn" style="background: var(--navy); color: white;">
                <i class="fas fa-arrow-left"></i> <span data-translate="back_to_pipeline">Back to Pipeline</span>
            </a>
        </div>
    </div>
</div>

<?php if (isset($flash) && $flash): ?>
    <?php if ($flash['type'] === 'success'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php elseif ($flash['type'] === 'error'): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Bulk Import Form -->
<form action="<?= BASE_URL ?>recruitment/bulk-import" method="POST" id="bulkImportForm">
    <div class="table-card">
        <div
            style="padding: 20px; background: var(--success); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;"><i class="fas fa-download"></i> <span
                    data-translate="approved_candidates_title">Approved Candidates - Ready for Import</span></h3>
            <button type="submit" class="btn" style="background: white; color: var(--success);" id="bulkImportBtn"
                disabled>
                <i class="fas fa-file-import"></i> <span data-translate="bulk_import_selected">Bulk Import
                    Selected</span>
            </button>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="selectAll" style="width: 18px; height: 18px;">
                    </th>
                    <th data-translate="candidate">Candidate</th>
                    <th data-translate="position">Position</th>
                    <th data-translate="contact">Contact</th>
                    <th data-translate="documents">Documents</th>
                    <th data-translate="sync_status">Sync Status</th>
                    <th width="180" style="text-align: center;" data-translate="th_actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($candidates)): ?>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr style="<?= !empty($candidate['is_synced_to_erp']) ? 'background-color: #e6ffe6;' : '' ?>">
                            <td>
                                <?php if (empty($candidate['is_synced_to_erp'])): ?>
                                    <input type="checkbox" name="candidate_ids[]" value="<?= $candidate['id'] ?>"
                                        class="candidate-checkbox" style="width: 18px; height: 18px;">
                                <?php else: ?>
                                    <i class="fas fa-check" style="color: var(--success);"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <?php if (!empty($candidate['avatar'])): ?>
                                        <img src="http://localhost/PT_indoocean/recruitment/uploads/avatars/<?= htmlspecialchars($candidate['avatar']) ?>"
                                            style="width: 40px; height: 40px; border-radius: 50%;">
                                    <?php else: ?>
                                        <div
                                            style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent-gold); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            <?= strtoupper(substr($candidate['full_name'] ?? 'N', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= htmlspecialchars($candidate['full_name'] ?? '') ?></strong><br>
                                        <small style="color: var(--text-muted);">
                                            Passport: <?= htmlspecialchars($candidate['passport_no'] ?? 'N/A') ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--accent-gold); color: white;">
                                    <?= htmlspecialchars($candidate['vacancy_title'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-envelope" style="color: var(--text-muted);"></i>
                                <?= htmlspecialchars($candidate['email'] ?? '-') ?><br>
                                <i class="fas fa-phone" style="color: var(--text-muted);"></i>
                                <?= htmlspecialchars($candidate['phone'] ?? '-') ?>
                            </td>
                            <td>
                                <?php
                                $docCount = !empty($candidate['documents']) ? count($candidate['documents']) : 0;
                                $docColor = $docCount > 3 ? 'success' : ($docCount > 0 ? 'gold' : 'red');
                                ?>
                                <span class="badge" style="background: var(--<?= $docColor ?>); color: white;">
                                    <i class="fas fa-file"></i> <?= $docCount ?> docs
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($candidate['is_synced_to_erp'])): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Synced
                                    </span><br>
                                    <small style="color: var(--text-muted);">
                                        <?= date('d M Y', strtotime($candidate['synced_at'])) ?>
                                    </small>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--gold); color: white;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="<?= BASE_URL ?>recruitment/candidate/<?= $candidate['id'] ?>" class="btn-icon"
                                    title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (empty($candidate['is_synced_to_erp'])): ?>
                                    <a href="<?= BASE_URL ?>recruitment/import/<?= $candidate['id'] ?>" class="btn-icon"
                                        style="color: var(--success);" title="Import to ERP"
                                        onclick="return confirm('Import this candidate? Medical checkup email will be sent.')">
                                        <i class="fas fa-file-import"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 60px;">
                            <i class="fas fa-user-check"
                                style="font-size: 64px; color: var(--text-muted); display: block; margin-bottom: 20px;"></i>
                            <h3 style="color: var(--text-muted);" data-translate="no_approved_candidates">No approved
                                candidates</h3>
                            <p style="color: var(--text-muted);" data-translate="onboarding_empty_message">Candidates will
                                appear here once approved in recruitment
                                system</p>
                            <a href="<?= BASE_URL ?>recruitment/pipeline" class="btn"
                                style="background: var(--accent-gold); color: white;">
                                <i class="fas fa-arrow-left"></i> <span data-translate="view_all_candidates">View All
                                    Candidates</span>
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

<script>
    // Select all checkbox
    document.getElementById('selectAll')?.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.candidate-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkButton();
    });

    // Individual checkbox
    document.querySelectorAll('.candidate-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkButton);
    });

    function updateBulkButton() {
        const checkedCount = document.querySelectorAll('.candidate-checkbox:checked').length;
        const btn = document.getElementById('bulkImportBtn');
        btn.disabled = checkedCount === 0;
        btn.innerHTML = checkedCount > 0
            ? '<i class="fas fa-file-import"></i> Import ' + checkedCount + ' Selected'
            : '<i class="fas fa-file-import"></i> Bulk Import Selected';
    }

    // Confirm form submit
    document.getElementById('bulkImportForm')?.addEventListener('submit', function (e) {
        const checkedCount = document.querySelectorAll('.candidate-checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Please select at least one candidate');
            return;
        }
        if (!confirm('Import ' + checkedCount + ' candidate(s) to ERP? Medical checkup emails will be sent automatically.')) {
            e.preventDefault();
        }
    });
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>