<?php
/**
 * Candidate Detail View
 */
$currentPage = 'recruitment-pipeline';
ob_start();
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><i class="fas fa-user" style="color: var(--accent-gold);"></i> <span
                    data-translate="candidate_detail">Candidate Detail</span></h1>
        </div>
        <div>
            <a href="<?= BASE_URL ?>recruitment/pipeline" class="btn" style="background: var(--navy); color: white;">
                <i class="fas fa-arrow-left"></i> <span data-translate="back_to_pipeline">Back to Pipeline</span>
            </a>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
    <!-- Profile Card -->
    <div class="table-card">
        <div style="text-align: center; padding: 30px;">
            <?php if (!empty($candidate['avatar'])): ?>
                <img src="<?= BASE_URL ?>../recruitment/uploads/avatars/<?= htmlspecialchars($candidate['avatar']) ?>"
                    style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
            <?php else: ?>
                <div
                    style="width: 120px; height: 120px; border-radius: 50%; background: var(--accent-gold); display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold; margin-bottom: 20px;">
                    <?= strtoupper(substr($candidate['full_name'] ?? 'N', 0, 1)) ?>
                </div>
            <?php endif; ?>

            <h2 style="margin: 0 0 8px 0;"><?= htmlspecialchars($candidate['full_name'] ?? '') ?></h2>
            <p style="color: var(--text-muted); margin-bottom: 20px;">
                <span class="badge" style="background: var(--accent-gold); color: white;">
                    <?= htmlspecialchars($candidate['vacancy_title'] ?? '-') ?>
                </span>
            </p>

            <div style="text-align: left; margin-top: 20px;">
                <div style="padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <strong><i class="fas fa-envelope" style="color: var(--accent-gold);"></i> Email</strong><br>
                    <span style="color: var(--text-muted);"><?= htmlspecialchars($candidate['email'] ?? '-') ?></span>
                </div>
                <div style="padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <strong><i class="fas fa-phone" style="color: var(--accent-gold);"></i> Phone</strong><br>
                    <span style="color: var(--text-muted);"><?= htmlspecialchars($candidate['phone'] ?? '-') ?></span>
                </div>
                <div style="padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <strong><i class="fas fa-passport" style="color: var(--accent-gold);"></i> Passport</strong><br>
                    <span
                        style="color: var(--text-muted);"><?= htmlspecialchars($candidate['passport_no'] ?? '-') ?></span>
                </div>
                <div style="padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <strong><i class="fas fa-birthday-cake" style="color: var(--accent-gold);"></i> Date of
                        Birth</strong><br>
                    <span style="color: var(--text-muted);">
                        <?= !empty($candidate['date_of_birth']) ? date('d M Y', strtotime($candidate['date_of_birth'])) : '-' ?>
                    </span>
                </div>
                <div style="padding: 12px 0;">
                    <strong><i class="fas fa-flag" style="color: var(--accent-gold);"></i> Status</strong><br>
                    <span class="badge"
                        style="background-color: <?= $candidate['status_color'] ?? '#6c757d' ?>; color: white;">
                        <?= htmlspecialchars($candidate['status_name'] ?? 'Unknown') ?>
                    </span>
                </div>
            </div>

            <?php if (empty($candidate['is_synced_to_erp'])): ?>
                <a href="<?= BASE_URL ?>recruitment/import/<?= $candidate['id'] ?>" class="btn"
                    style="background: var(--success); color: white; width: 100%; margin-top: 20px;"
                    onclick="return confirm('Import to ERP and send medical checkup email?')">
                    <i class="fas fa-file-import"></i> Import to ERP
                </a>
            <?php else: ?>
                <button class="btn"
                    style="background: var(--text-muted); color: white; width: 100%; margin-top: 20px; cursor: not-allowed;"
                    disabled>
                    <i class="fas fa-check"></i> Already Synced to ERP
                </button>
            <?php endif; ?>
        </div>

        <!-- Address -->
        <div style="padding: 20px; border-top: 1px solid var(--border);">
            <h3><i class="fas fa-map-marker-alt" style="color: var(--accent-gold);"></i> <span
                    data-translate="address">Address</span></h3>
            <p style="color: var(--text-muted);"><?= htmlspecialchars($candidate['address'] ?? 'Not provided') ?></p>
            <p style="margin: 8px 0 0 0; color: var(--text-muted);">
                <strong>City:</strong> <?= htmlspecialchars($candidate['city'] ?? '-') ?><br>
                <strong>Country:</strong> <?= htmlspecialchars($candidate['country'] ?? '-') ?>
            </p>
        </div>

        <!-- Emergency Contact -->
        <div style="padding: 20px; border-top: 1px solid var(--border);">
            <h3><i class="fas fa-phone-alt" style="color: var(--gold);"></i> <span
                    data-translate="emergency_contact">Emergency Contact</span></h3>
            <p style="margin: 8px 0 0 0;">
                <strong><?= htmlspecialchars($candidate['emergency_name'] ?? 'Not provided') ?></strong><br>
                <span
                    style="color: var(--text-muted);"><?= htmlspecialchars($candidate['emergency_phone'] ?? '-') ?></span>
            </p>
        </div>
    </div>

    <!-- Details Section -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Documents -->
        <div class="table-card">
            <h3 style="padding: 20px; margin: 0; border-bottom: 1px solid var(--border);">
                <i class="fas fa-file-alt" style="color: var(--accent-gold);"></i> <span
                    data-translate="documents">Documents</span>
            </h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Document Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($candidate['documents'])): ?>
                        <?php foreach ($candidate['documents'] as $doc): ?>
                            <tr>
                                <td>
                                    <i class="fas fa-file-pdf" style="color: var(--red);"></i>
                                    <?= htmlspecialchars($doc['type_name'] ?? 'Document') ?>
                                </td>
                                <td>
                                    <?php
                                    $docStatus = $doc['status'] ?? 'uploaded';
                                    $statusColors = [
                                        'verified' => 'success',
                                        'rejected' => 'red',
                                        'uploaded' => 'gold'
                                    ];
                                    ?>
                                    <span class="badge"
                                        style="background: var(--<?= $statusColors[$docStatus] ?? 'text-muted' ?>); color: white;">
                                        <?= ucfirst($docStatus) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>../recruitment/uploads/documents/<?= htmlspecialchars($doc['file_path'] ?? '') ?>"
                                        target="_blank" class="btn-icon">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                No documents uploaded
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Interview History -->
        <div class="table-card">
            <h3 style="padding: 20px; margin: 0; border-bottom: 1px solid var(--border);">
                <i class="fas fa-comments" style="color: var(--accent-gold);"></i> <span
                    data-translate="interview_history">Interview History</span>
            </h3>
            <div style="padding: 20px;">
                <?php if (!empty($candidate['interviews'])): ?>
                    <?php foreach ($candidate['interviews'] as $interview): ?>
                        <div
                            style="padding: 16px; background: #f8f9fa; border-left: 4px solid var(--accent-gold); margin-bottom: 16px; border-radius: 4px;">
                            <h4 style="margin: 0 0 8px 0;">
                                <?= htmlspecialchars($interview['question_bank_name'] ?? 'Interview') ?>
                            </h4>
                            <p style="margin: 0; color: var(--text-muted);">
                                <strong>Status:</strong> <?= ucfirst($interview['status'] ?? 'pending') ?>
                                <?php if (!empty($interview['total_score'])): ?>
                                    | <strong>Score:</strong> <?= $interview['total_score'] ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 20px;">No interview sessions recorded
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Medical Checkups -->
        <div class="table-card">
            <h3 style="padding: 20px; margin: 0; border-bottom: 1px solid var(--border);">
                <i class="fas fa-heartbeat" style="color: var(--red);"></i> <span
                    data-translate="medical_checkups">Medical Checkups</span>
            </h3>
            <div style="padding: 20px;">
                <?php if (!empty($candidate['medical_checkups'])): ?>
                    <?php foreach ($candidate['medical_checkups'] as $medical): ?>
                        <div
                            style="padding: 16px; background: <?= $medical['status'] === 'completed' ? '#e6ffe6' : '#fff3cd' ?>; border-left: 4px solid <?= $medical['status'] === 'completed' ? 'var(--success)' : 'var(--gold)' ?>; margin-bottom: 16px; border-radius: 4px;">
                            <p style="margin: 0;">
                                <strong>Status:</strong> <?= ucfirst($medical['status'] ?? 'pending') ?>
                            </p>
                            <?php if (!empty($medical['scheduled_date'])): ?>
                                <p style="margin: 8px 0 0 0; color: var(--text-muted);">
                                    <strong>Scheduled:</strong> <?= date('d M Y H:i', strtotime($medical['scheduled_date'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 20px;">No medical checkups recorded</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>