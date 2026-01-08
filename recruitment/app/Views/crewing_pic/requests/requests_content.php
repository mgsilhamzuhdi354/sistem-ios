<!-- Crewing PIC Requests Content -->
<div class="page-header">
    <h1>Pipeline Requests</h1>
    <p class="text-muted">Approve or reject status change requests from crewing staff</p>
</div>

<!-- Pending Requests -->
<div class="card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-clock me-2 text-warning"></i>Pending Requests (<?= count($pendingRequests) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($pendingRequests)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-check-circle fs-1"></i>
                <p class="mt-3">No pending requests at this time</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Requested By</th>
                            <th>Applicant</th>
                            <th>Vacancy</th>
                            <th>Status Change</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingRequests as $req): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($req['crewing_name']) ?></strong></td>
                            <td><?= htmlspecialchars($req['applicant_name']) ?></td>
                            <td><?= htmlspecialchars($req['vacancy_title'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= $req['from_status_name'] ?></span>
                                <i class="fas fa-arrow-right mx-1"></i>
                                <span class="badge bg-primary"><?= $req['to_status_name'] ?></span>
                            </td>
                            <td><?= htmlspecialchars($req['reason'] ?? '-') ?></td>
                            <td><?= date('d M Y H:i', strtotime($req['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal<?= $req['id'] ?>">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $req['id'] ?>">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal<?= $req['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= url('/crewing-pic/requests/approve/' . $req['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approve Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Approve status change from <strong><?= $req['from_status_name'] ?></strong> to <strong><?= $req['to_status_name'] ?></strong>?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Notes (optional)</label>
                                                <textarea name="notes" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal<?= $req['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="<?= url('/crewing-pic/requests/reject/' . $req['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Reject status change request?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                                <textarea name="notes" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Request History -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-history me-2"></i>Request History</h5>
    </div>
    <div class="card-body">
        <?php if (empty($history)): ?>
            <div class="text-center text-muted py-4">
                <p>No history yet</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request By</th>
                            <th>Status Change</th>
                            <th>Result</th>
                            <th>Response Notes</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['crewing_name']) ?></td>
                            <td>
                                <?= $h['from_status_name'] ?> → <?= $h['to_status_name'] ?>
                            </td>
                            <td>
                                <?php if ($h['status'] == 'approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($h['response_notes'] ?? '-') ?></td>
                            <td><?= date('d M Y H:i', strtotime($h['responded_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
