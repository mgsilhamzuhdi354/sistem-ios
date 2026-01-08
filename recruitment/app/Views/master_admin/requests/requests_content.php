<!-- Master Admin Requests Page -->
<div class="page-header-modern">
    <div class="header-content">
        <div class="header-icon pending">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div>
            <h1>Pipeline Requests</h1>
            <p>Approve or reject status change requests from crewing staff</p>
        </div>
    </div>
    <div class="pending-count">
        <span class="count"><?= count($pendingRequests) ?></span>
        <span>Pending</span>
    </div>
</div>

<!-- Pending Requests -->
<?php if (empty($pendingRequests)): ?>
<div class="empty-state-card">
    <i class="fas fa-check-circle"></i>
    <h3>All Caught Up!</h3>
    <p>No pending requests to review</p>
</div>
<?php else: ?>
<div class="requests-section">
    <h2 class="section-title"><i class="fas fa-hourglass-half"></i> Pending Requests</h2>
    
    <div class="requests-grid">
        <?php foreach ($pendingRequests as $req): ?>
        <div class="request-card">
            <div class="request-header">
                <div class="applicant-info">
                    <div class="applicant-avatar">
                        <?= strtoupper(substr($req['applicant_name'], 0, 2)) ?>
                    </div>
                    <div>
                        <strong><?= htmlspecialchars($req['applicant_name']) ?></strong>
                        <span><?= htmlspecialchars($req['vacancy_title']) ?></span>
                    </div>
                </div>
                <span class="request-time">
                    <i class="fas fa-clock"></i>
                    <?= date('M d, H:i', strtotime($req['created_at'])) ?>
                </span>
            </div>
            
            <div class="status-change-visual">
                <div class="status-from" style="background: <?= $req['from_status_color'] ?>">
                    <?= $req['from_status_name'] ?>
                </div>
                <div class="arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
                <div class="status-to" style="background: <?= $req['to_status_color'] ?>">
                    <?= $req['to_status_name'] ?>
                </div>
            </div>
            
            <div class="request-reason">
                <label>Reason:</label>
                <p><?= htmlspecialchars($req['reason']) ?></p>
            </div>
            
            <div class="requested-by">
                <i class="fas fa-user"></i>
                Requested by: <strong><?= htmlspecialchars($req['requested_by_name']) ?></strong>
            </div>
            
            <div class="request-actions">
                <!-- Approve Form -->
                <form action="<?= url('/master-admin/requests/approve/' . $req['id']) ?>" method="POST" class="action-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="notes" value="">
                    <button type="submit" class="btn-approve" onclick="return confirm('Approve this request?')">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </form>
                
                <!-- Reject Button -->
                <button type="button" class="btn-reject" onclick="showRejectModal(<?= $req['id'] ?>, '<?= htmlspecialchars($req['applicant_name']) ?>')">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Request History -->
<?php if (!empty($historyRequests)): ?>
<div class="history-section">
    <h2 class="section-title"><i class="fas fa-history"></i> Recent History</h2>
    
    <table class="history-table">
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Status Change</th>
                <th>Requested By</th>
                <th>Result</th>
                <th>Responded By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($historyRequests as $h): ?>
            <tr>
                <td><strong><?= htmlspecialchars($h['applicant_name']) ?></strong></td>
                <td>
                    <span class="mini-status"><?= $h['from_status_name'] ?></span>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <span class="mini-status"><?= $h['to_status_name'] ?></span>
                </td>
                <td><?= htmlspecialchars($h['requested_by_name']) ?></td>
                <td>
                    <span class="result-badge <?= $h['status'] ?>">
                        <?= ucfirst($h['status']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($h['responded_by_name'] ?? '-') ?></td>
                <td><?= $h['responded_at'] ? date('M d, Y H:i', strtotime($h['responded_at'])) : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Reject Modal -->
<div class="modal-overlay" id="rejectModal">
    <div class="modal-container">
        <div class="modal-header">
            <h3><i class="fas fa-times-circle"></i> Reject Request</h3>
            <button class="modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <p class="reject-info">Rejecting request for: <strong id="rejectApplicantName"></strong></p>
                
                <div class="form-group">
                    <label for="rejectReason"><i class="fas fa-comment-alt"></i> Reason for Rejection <span class="required">*</span></label>
                    <textarea name="notes" id="rejectReason" rows="3" class="form-input" required 
                              placeholder="Please explain why you are rejecting this request..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn-reject-submit">
                    <i class="fas fa-times"></i> Reject Request
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Page Header */
.page-header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    border-radius: 16px;
    margin-bottom: 1.5rem;
    color: white;
}
.header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.header-icon {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.header-icon.pending {
    background: rgba(245, 158, 11, 0.3);
    color: #fcd34d;
}
.header-content h1 {
    margin: 0;
    font-size: 1.5rem;
}
.header-content p {
    margin: 0;
    opacity: 0.8;
    font-size: 0.9rem;
}
.pending-count {
    text-align: center;
    background: rgba(255,255,255,0.15);
    padding: 1rem 1.5rem;
    border-radius: 12px;
}
.pending-count .count {
    display: block;
    font-size: 2rem;
    font-weight: 700;
}
.pending-count span {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* Empty State */
.empty-state-card {
    background: white;
    border-radius: 16px;
    padding: 4rem 2rem;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.empty-state-card i {
    font-size: 4rem;
    color: #22c55e;
    margin-bottom: 1rem;
}
.empty-state-card h3 {
    margin: 0 0 0.5rem;
    color: #1f2937;
}
.empty-state-card p {
    color: #6b7280;
    margin: 0;
}

/* Section Title */
.section-title {
    font-size: 1.1rem;
    color: #1f2937;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.section-title i {
    color: #3b82f6;
}

/* Requests Grid */
.requests-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 1.25rem;
}

/* Request Card */
.request-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: hidden;
}
.request-header {
    padding: 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid #f3f4f6;
}
.applicant-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.applicant-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}
.applicant-info strong {
    display: block;
    font-size: 1rem;
    color: #1f2937;
}
.applicant-info span {
    font-size: 0.8rem;
    color: #6b7280;
}
.request-time {
    font-size: 0.75rem;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Status Change Visual */
.status-change-visual {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.25rem;
    background: #f8fafc;
}
.status-from, .status-to {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    color: white;
    font-size: 0.85rem;
    font-weight: 500;
}
.arrow {
    color: #9ca3af;
}

/* Request Reason */
.request-reason {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
}
.request-reason label {
    font-size: 0.75rem;
    color: #9ca3af;
    text-transform: uppercase;
}
.request-reason p {
    margin: 0.25rem 0 0;
    font-size: 0.9rem;
    color: #374151;
}

/* Requested By */
.requested-by {
    padding: 0.75rem 1.25rem;
    font-size: 0.8rem;
    color: #6b7280;
    background: #fafafa;
}
.requested-by i {
    color: #3b82f6;
    margin-right: 0.25rem;
}

/* Action Buttons */
.request-actions {
    display: flex;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
}
.action-form {
    flex: 1;
}
.btn-approve, .btn-reject {
    width: 100%;
    padding: 0.75rem;
    border: none;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.btn-approve {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    flex: 1;
}
.btn-approve:hover {
    transform: translateY(-2px);
}
.btn-reject {
    background: #fee2e2;
    color: #dc2626;
    flex: 1;
}
.btn-reject:hover {
    background: #fecaca;
}

/* History Section */
.history-section {
    margin-top: 2rem;
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.history-table {
    width: 100%;
    border-collapse: collapse;
}
.history-table th {
    text-align: left;
    padding: 0.75rem 1rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #9ca3af;
    border-bottom: 1px solid #f3f4f6;
}
.history-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.9rem;
}
.mini-status {
    background: #e5e7eb;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
}
.result-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}
.result-badge.approved {
    background: #dcfce7;
    color: #166534;
}
.result-badge.rejected {
    background: #fee2e2;
    color: #dc2626;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-overlay.active {
    display: flex;
}
.modal-container {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 450px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.modal-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-header h3 {
    margin: 0;
    color: #dc2626;
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #9ca3af;
    cursor: pointer;
}
.modal-body {
    padding: 1.5rem;
}
.reject-info {
    margin-bottom: 1rem;
    color: #374151;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #374151;
}
.required {
    color: #ef4444;
}
.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.9rem;
    font-family: inherit;
}
.modal-footer {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-radius: 0 0 16px 16px;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
.btn-cancel {
    padding: 0.625rem 1.25rem;
    background: #e5e7eb;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
.btn-reject-submit {
    padding: 0.625rem 1.25rem;
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}
</style>

<script>
function showRejectModal(requestId, applicantName) {
    document.getElementById('rejectApplicantName').textContent = applicantName;
    document.getElementById('rejectForm').action = '<?= url('/master-admin/requests/reject/') ?>' + requestId;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('active');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
