<?php
/**
 * Contract Detail View
 * Shows contract details with approval workflow, audit log
 */
$currentPage = 'contracts';
$statusColors = [
    'draft' => 'secondary', 'pending_approval' => 'warning', 'active' => 'success',
    'onboard' => 'info', 'completed' => 'info', 'terminated' => 'danger', 'cancelled' => 'secondary'
];
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <h1><?= htmlspecialchars($contract['contract_no']) ?></h1>
        <p><?= htmlspecialchars($contract['crew_name']) ?> - <?= htmlspecialchars($contract['rank_name'] ?? '-') ?></p>
    </div>
    <div style="display: flex; gap: 10px;">
        <span class="badge badge-<?= $statusColors[$contract['status']] ?? 'secondary' ?>" style="font-size: 14px; padding: 8px 16px;">
            <?= ucfirst(str_replace('_', ' ', $contract['status'])) ?>
        </span>
        <?php if (in_array($contract['status'], ['active', 'onboard'])): ?>
            <a href="<?= BASE_URL ?>contracts/renew/<?= $contract['id'] ?>" class="btn btn-success btn-sm"><i class="fas fa-redo"></i> Renew</a>
            <a href="<?= BASE_URL ?>contracts/terminate/<?= $contract['id'] ?>" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Terminate</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>contracts/export-pdf/<?= $contract['id'] ?>" target="_blank" class="btn btn-info btn-sm"><i class="fas fa-file-pdf"></i> Print PDF</a>
        <a href="<?= BASE_URL ?>contracts/edit/<?= $contract['id'] ?>" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a>
    </div>
</div>

<div class="grid-2">
    <!-- Left Column -->
    <div>
        <!-- Contract Information -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-file-contract" style="color: var(--accent-gold);"></i> Contract Information</h3>
            <table style="width: 100%;">
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Contract No</td><td style="padding: 8px 0;"><strong><?= htmlspecialchars($contract['contract_no']) ?></strong></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Type</td><td style="padding: 8px 0;"><?= CONTRACT_TYPES[$contract['contract_type']] ?? $contract['contract_type'] ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Vessel</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['vessel_name'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Client</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['client_name'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Rank</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['rank_name'] ?? '-') ?></td></tr>
            </table>
        </div>
        
        <!-- Period -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-calendar" style="color: var(--accent-gold);"></i> Contract Period</h3>
            <table style="width: 100%;">
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Sign On</td><td style="padding: 8px 0;"><?= $contract['sign_on_date'] ? date('d M Y', strtotime($contract['sign_on_date'])) : '-' ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Sign Off</td><td style="padding: 8px 0;"><?= $contract['sign_off_date'] ? date('d M Y', strtotime($contract['sign_off_date'])) : '-' ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Duration</td><td style="padding: 8px 0;"><?= $contract['duration_months'] ?? '-' ?> months</td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Days Remaining</td><td style="padding: 8px 0;">
                    <?php if ($daysRemaining !== null && in_array($contract['status'], ['active', 'onboard'])): ?>
                        <?php $daysClass = $daysRemaining <= 7 ? 'danger' : ($daysRemaining <= 30 ? 'warning' : 'success'); ?>
                        <span class="badge badge-<?= $daysClass ?>"><?= $daysRemaining ?> days</span>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td></tr>
            </table>
        </div>
        
        <!-- Salary -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-money-bill" style="color: var(--accent-gold);"></i> Salary Structure</h3>
            <table style="width: 100%;">
                <?php if (!empty($contract['exchange_rate']) && $contract['exchange_rate'] > 0): ?>
                <tr style="background: rgba(var(--accent-gold-rgb), 0.1);">
                    <td style="padding: 8px 0; color: var(--warning);"><i class="fas fa-exchange-alt"></i> Exchange Rate</td>
                    <td style="padding: 8px 0; text-align: right; color: var(--warning); font-weight: 600;">1 USD = <?= $contract['currency_symbol'] ?? 'Rp' ?><?= number_format($contract['exchange_rate'], 0) ?></td>
                </tr>   
                <?php endif; ?>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Basic Salary</td><td style="padding: 8px 0; text-align: right;"><?= $contract['currency_symbol'] ?? '$' ?><?= number_format($contract['basic_salary'] ?? 0, 2) ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Overtime</td><td style="padding: 8px 0; text-align: right;"><?= $contract['currency_symbol'] ?? '$' ?><?= number_format($contract['overtime_allowance'] ?? 0, 2) ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Leave Pay</td><td style="padding: 8px 0; text-align: right;"><?= $contract['currency_symbol'] ?? '$' ?><?= number_format($contract['leave_pay'] ?? 0, 2) ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Bonus</td><td style="padding: 8px 0; text-align: right;"><?= $contract['currency_symbol'] ?? '$' ?><?= number_format($contract['bonus'] ?? 0, 2) ?></td></tr>
                <tr style="border-top: 1px solid var(--border-color);"><td style="padding: 12px 0; font-weight: 600;">Total Monthly</td><td style="padding: 12px 0; text-align: right; font-size: 18px; color: var(--accent-gold); font-weight: 700;"><?= $contract['currency_symbol'] ?? '$' ?><?= number_format($contract['total_monthly'] ?? 0, 2) ?></td></tr>
            </table>
        </div>
    </div>
    
    <!-- Right Column -->
    <div>
        <!-- Tax -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-percent" style="color: var(--accent-gold);"></i> Tax Information</h3>
            <table style="width: 100%;">
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Tax Type</td><td style="padding: 8px 0;"><?= TAX_TYPES[$contract['tax_type']] ?? $contract['tax_type'] ?? '-' ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">NPWP</td><td style="padding: 8px 0;"><?= htmlspecialchars($contract['npwp_number'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Tax Rate</td><td style="padding: 8px 0;"><?= $contract['tax_rate'] ?? 5 ?>%</td></tr>
            </table>
        </div>
        
        <!-- Deductions -->
        <?php if (!empty($deductions)): ?>
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-minus-circle" style="color: var(--accent-gold);"></i> Deductions</h3>
            <table style="width: 100%;">
                <?php foreach ($deductions as $ded): ?>
                <tr>
                    <td style="padding: 8px 0; color: var(--text-muted);"><?= DEDUCTION_TYPES[$ded['deduction_type']] ?? $ded['deduction_type'] ?></td>
                    <td style="padding: 8px 0;"><?= htmlspecialchars($ded['description']) ?></td>
                    <td style="padding: 8px 0; text-align: right; color: var(--danger);">-<?= $contract['currency_symbol'] ?? 'Rp' ?><?= number_format($ded['amount'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        
        <!-- Approval Workflow (Feature 9) -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-check-circle" style="color: var(--accent-gold);"></i> Approval Workflow</h3>
            <?php if (empty($approvals)): ?>
                <p style="color: var(--text-muted);">No approvals yet</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($approvals as $approval): ?>
                        <?php
                        $apprColor = $approval['status'] === 'approved' ? 'success' : ($approval['status'] === 'rejected' ? 'danger' : 'warning');
                        $apprIcon = $approval['status'] === 'approved' ? 'check' : ($approval['status'] === 'rejected' ? 'times' : 'clock');
                        ?>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(var(--<?= $apprColor ?>), 0.2); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-<?= $apprIcon ?>" style="color: var(--<?= $apprColor ?>);"></i>
                            </div>
                            <div style="flex: 1;">
                                <strong><?= APPROVAL_LEVELS[$approval['approval_level']] ?? $approval['approval_level'] ?></strong>
                                <span class="badge badge-<?= $apprColor ?>" style="margin-left: 8px;"><?= ucfirst($approval['status']) ?></span>
                                <?php if ($approval['approver_name']): ?>
                                    <br><small style="color: var(--text-muted);">by <?= htmlspecialchars($approval['approver_name']) ?> at <?= date('d M Y H:i', strtotime($approval['approved_at'])) ?></small>
                                <?php endif; ?>
                            </div>
                            <?php if ($approval['status'] === 'pending'): ?>
                                <form method="POST" action="<?= BASE_URL ?>contracts/approve/<?= $contract['id'] ?>" style="display: inline;">
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Approve</button>
                                </form>
                                <button class="btn btn-danger btn-sm" onclick="showRejectModal()"><i class="fas fa-times"></i> Reject</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Documents (Feature 8) -->
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-file-pdf" style="color: var(--accent-gold);"></i> Documents</h3>
            
            <!-- Upload Form -->
            <form method="POST" action="<?= BASE_URL ?>documents/upload/<?= $contract['id'] ?>" enctype="multipart/form-data" style="margin-bottom: 16px; padding: 12px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                    <div style="flex: 1; min-width: 150px;">
                        <label class="form-label" style="font-size: 11px;">Document Type</label>
                        <select name="document_type" class="form-control" style="padding: 6px 10px;">
                            <option value="contract">Contract</option>
                            <option value="amendment">Amendment</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 120px;">
                        <label class="form-label" style="font-size: 11px;">Language</label>
                        <select name="language" class="form-control" style="padding: 6px 10px;">
                            <option value="id">Indonesia</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    <div style="flex: 2; min-width: 200px;">
                        <label class="form-label" style="font-size: 11px;">File (PDF, Word, Image)</label>
                        <input type="file" name="document" class="form-control" style="padding: 4px 10px;" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>
            
            <!-- Document List -->
            <?php if (empty($documents)): ?>
                <p style="color: var(--text-muted); text-align: center; padding: 20px;">No documents uploaded</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <?php foreach ($documents as $doc): ?>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 6px;">
                            <i class="fas fa-<?= $doc['document_type'] === 'contract' ? 'file-contract' : 'file-alt' ?>" style="font-size: 20px; color: var(--accent-gold);"></i>
                            <div style="flex: 1;">
                                <strong style="font-size: 13px;"><?= htmlspecialchars($doc['file_name']) ?></strong>
                                <br><small style="color: var(--text-muted);">
                                    <?= ucfirst($doc['document_type']) ?> • <?= strtoupper($doc['language']) ?> • 
                                    <?= round($doc['file_size'] / 1024, 1) ?> KB
                                    <?php if ($doc['is_signed']): ?>
                                        <span class="badge badge-success" style="margin-left: 8px;"><i class="fas fa-check"></i> Signed</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <a href="<?= BASE_URL ?>documents/download/<?= $doc['id'] ?>" class="btn-icon" title="Download"><i class="fas fa-download"></i></a>
                            <?php if (!$doc['is_signed']): ?>
                                <form method="POST" action="<?= BASE_URL ?>documents/signed/<?= $doc['id'] ?>" style="display: inline;">
                                    <button type="submit" class="btn-icon" title="Mark as Signed" style="color: var(--success);"><i class="fas fa-signature"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Audit Log (Feature 15) -->
        <div class="card">
            <h3 style="margin-bottom: 16px;"><i class="fas fa-history" style="color: var(--accent-gold);"></i> Activity Log</h3>
            <?php if (empty($logs)): ?>
                <p style="color: var(--text-muted);">No activity yet</p>
            <?php else: ?>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php foreach ($logs as $log): ?>
                        <div style="padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                            <div style="display: flex; justify-content: space-between;">
                                <strong style="font-size: 13px;"><?= ucfirst(str_replace('_', ' ', $log['action'])) ?></strong>
                                <small style="color: var(--text-muted);"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></small>
                            </div>
                            <small style="color: var(--text-muted);">by <?= htmlspecialchars($log['user_name'] ?? 'System') ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div class="card" style="width: 400px;">
        <h3 style="margin-bottom: 16px;">Reject Contract</h3>
        <form method="POST" action="<?= BASE_URL ?>contracts/reject/<?= $contract['id'] ?>">
            <div class="form-group">
                <label class="form-label">Reason for rejection</label>
                <textarea name="reason" class="form-control" rows="3" required></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="hideRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() { document.getElementById('rejectModal').style.display = 'flex'; }
function hideRejectModal() { document.getElementById('rejectModal').style.display = 'none'; }
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
