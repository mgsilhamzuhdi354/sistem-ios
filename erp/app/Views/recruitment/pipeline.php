<?php
/**
 * Recruitment Pipeline View
 */
$currentPage = 'recruitment-pipeline';
ob_start();
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><i class="fas fa-users-cog" style="color: var(--accent-gold);"></i> <span
                    data-translate="recruitment_pipeline">Recruitment Pipeline</span></h1>
            <p data-translate="recruitment_subtitle">Candidate management from recruitment system <span
                    class="badge badge-info">LIVE</span></p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="<?= BASE_URL ?>recruitment/onboarding" class="btn"
                style="background: var(--accent-gold); color: white;">
                <i class="fas fa-user-check"></i> <span data-translate="view_onboarding">View Onboarding</span>
            </a>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        <strong data-translate="connection_error">Connection Error</strong><br>
        <span data-translate="recruitment_api_error">Unable to connect to recruitment system:</span>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Stats Cards -->
<?php if (!empty($candidates)): ?>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <?php
        $total = count($candidates);
        $interview = count(array_filter($candidates, fn($c) => ($c['status_name'] ?? '') === 'Interview'));
        $approved = count(array_filter($candidates, fn($c) => in_array($c['status_name'] ?? '', ['Approved', 'Hired'])));
        $synced = count(array_filter($candidates, fn($c) => !empty($c['is_synced_to_erp'])));
        ?>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3><?= $total ?></h3>
                <p data-translate="total_candidates">Total Candidates</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold"><i class="fas fa-comments"></i></div>
            <div class="stat-info">
                <h3><?= $interview ?></h3>
                <p data-translate="in_interview">In Interview</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3><?= $approved ?></h3>
                <p data-translate="approved">Approved</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-sync"></i></div>
            <div class="stat-info">
                <h3><?= $synced ?></h3>
                <p data-translate="synced_to_erp">Synced to ERP</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Candidates Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th data-translate="avatar">Avatar</th>
                <th data-translate="name">Name</th>
                <th data-translate="position">Position</th>
                <th data-translate="department">Department</th>
                <th data-translate="th_status">Status</th>
                <th data-translate="applied">Applied</th>
                <th style="text-align: center;" data-translate="th_actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($candidates)): ?>
                <?php foreach ($candidates as $candidate): ?>
                    <tr>
                        <td>
                            <?php if (!empty($candidate['avatar'])): ?>
                                <img src="http://localhost/PT_indoocean/recruitment/uploads/avatars/<?= htmlspecialchars($candidate['avatar']) ?>"
                                    alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%;">
                            <?php else: ?>
                                <div
                                    style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent-gold); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <?= strtoupper(substr($candidate['full_name'] ?? 'N', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($candidate['full_name'] ?? '') ?></strong><br>
                            <small style="color: var(--text-muted);"><?= htmlspecialchars($candidate['email'] ?? '') ?></small>
                        </td>
                        <td><?= htmlspecialchars($candidate['vacancy_title'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($candidate['department_name'] ?? '-') ?></td>
                        <td>
                            <span class="badge"
                                style="background-color: <?= $candidate['status_color'] ?? '#6c757d' ?>; color: white;">
                                <?= htmlspecialchars($candidate['status_name'] ?? 'Unknown') ?>
                            </span>
                            <?php if (!empty($candidate['is_synced_to_erp'])): ?>
                                <br><span class="badge badge-info" style="margin-top: 4px;">Synced</span>
                            <?php endif; ?>
                        </td>
                        <td><?= !empty($candidate['submitted_at']) ? date('d M Y', strtotime($candidate['submitted_at'])) : '-' ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?= BASE_URL ?>recruitment/candidate/<?= $candidate['id'] ?>" class="btn-icon"
                                title="View Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                        <span data-translate="no_candidates_found">No candidates found</span>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>