<?php
/**
 * Audit Log Report View (Feature 15)
 */
$currentPage = 'reports';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1><i class="fas fa-history" style="color: var(--accent-gold);"></i> Audit Log</h1>
        <p>Track all contract changes and activities</p>
    </div>
    <a href="<?= BASE_URL ?>reports" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="<?= BASE_URL ?>reports/audit" style="display: flex; gap: 16px; align-items: center;">
        <label>Filter by Contract ID:</label>
        <input type="number" name="contract_id" class="form-control" style="width: 150px;" 
               value="<?= htmlspecialchars($contractId ?? '') ?>" placeholder="All contracts">
        <button type="submit" class="btn btn-secondary">Filter</button>
        <?php if (!empty($contractId)): ?>
            <a href="<?= BASE_URL ?>reports/audit" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Contract</th>
                <th>Action</th>
                <th>Field Changed</th>
                <th>Old Value</th>
                <th>New Value</th>
                <th>User</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">No audit logs found</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <?php
                    $actionColors = [
                        'created' => 'success',
                        'updated' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'terminated' => 'danger',
                        'renewed' => 'warning'
                    ];
                    $actionColor = 'secondary';
                    foreach ($actionColors as $key => $color) {
                        if (stripos($log['action'], $key) !== false) {
                            $actionColor = $color;
                            break;
                        }
                    }
                    ?>
                    <tr>
                        <td style="white-space: nowrap; font-size: 12px;">
                            <?= date('d M Y', strtotime($log['created_at'])) ?><br>
                            <span style="color: var(--text-muted);"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>contracts/<?= $log['contract_id'] ?>" style="color: var(--accent-gold);">
                                <?= htmlspecialchars($log['contract_no'] ?? '#'.$log['contract_id']) ?>
                            </a>
                        </td>
                        <td><span class="badge badge-<?= $actionColor ?>"><?= ucfirst(str_replace('_', ' ', $log['action'])) ?></span></td>
                        <td><?= htmlspecialchars($log['field_changed'] ?? '-') ?></td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; color: var(--text-muted);">
                            <?= htmlspecialchars($log['old_value'] ?? '-') ?>
                        </td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($log['new_value'] ?? '-') ?>
                        </td>
                        <td><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                        <td style="font-family: monospace; font-size: 12px; color: var(--text-muted);">
                            <?= htmlspecialchars($log['ip_address'] ?? '-') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
