<?php
/**
 * Monitoring Activity Logs View
 */
?>
<div class="page-header">
    <h1>Activity Log</h1>
    <p>Log aktivitas dari semua sistem terintegrasi</p>
</div>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 12px; align-items: end;">
        <div class="form-group" style="flex: 1; margin: 0;">
            <label class="form-label">System Filter</label>
            <select name="system" class="form-control" onchange="this.form.submit()">
                <option value="all" <?= $system === 'all' ? 'selected' : '' ?>>All Systems</option>
                <option value="erp" <?= $system === 'erp' ? 'selected' : '' ?>>ERP</option>
                <option value="hris" <?= $system === 'hris' ? 'selected' : '' ?>>HRIS</option>
                <option value="recruitment" <?= $system === 'recruitment' ? 'selected' : '' ?>>Recruitment</option>
                <option value="company_profile" <?= $system === 'company_profile' ? 'selected' : '' ?>>Company Profile
                </option>
            </select>
        </div>

        <div class="form-group" style="margin: 0;">
            <label class="form-label">Limit</label>
            <select name="limit" class="form-control" onchange="this.form.submit()">
                <option value="50">50</option>
                <option value="100" selected>100</option>
                <option value="200">200</option>
                <option value="500">500</option>
            </select>
        </div>
    </form>
</div>

<div class="card">
    <h3 style="margin-bottom: 16px;">Activity Logs</h3>

    <?php if (empty($logs)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Belum ada activity logs. Sistem akan mulai merekam aktivitas setelah integrasi aktif.
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Time</th>
                    <th style="width: 120px;">System</th>
                    <th style="width: 150px;">Action</th>
                    <th>Entity</th>
                    <th style="width: 300px;">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </td>
                        <td>
                            <?php
                            $badgeClass = [
                                'erp' => 'badge-info',
                                'hris' => 'badge-success',
                                'recruitment' => 'badge-warning',
                                'company_profile' => 'badge-secondary'
                            ];
                            ?>
                            <span class="badge <?= $badgeClass[$log['source_system']] ?? 'badge-secondary' ?>">
                                <?= strtoupper($log['source_system']) ?>
                            </span>
                        </td>
                        <td><strong>
                                <?= htmlspecialchars($log['action']) ?>
                            </strong></td>
                        <td>
                            <?= htmlspecialchars($log['entity_type'] ?? '-') ?>
                            <?php if ($log['entity_id']): ?>
                                <code>#<?= $log['entity_id'] ?></code>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($log['details']): ?>
                                <small style="color: var(--text-muted);">
                                    <?php
                                    $details = json_decode($log['details'], true);
                                    if ($details) {
                                        echo htmlspecialchars(json_encode($details, JSON_UNESCAPED_UNICODE));
                                    }
                                    ?>
                                </small>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 16px; text-align: center; color: var(--text-muted);">
            <small>Showing
                <?= count($logs) ?> most recent activities
            </small>
        </div>
    <?php endif; ?>
</div>