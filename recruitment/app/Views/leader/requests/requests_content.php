<!-- Leader Requests Content -->
<div class="row g-4">
    <!-- Pending Requests -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-white border-0">
                <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Pending Requests (<?= count($pendingRequests) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($pendingRequests)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                    <h5 class="mt-3">All Clear!</h5>
                    <p class="text-muted">No pending requests to review</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pendingRequests as $request): ?>
                    <div class="list-group-item request-card py-3 mb-3 rounded border">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">
                                    <i class="bi bi-person me-1"></i>
                                    <?= htmlspecialchars($request['applicant_name']) ?>
                                </h6>
                                <div class="mb-2">
                                    <span class="badge" style="background-color: #6b7280"><?= $request['from_status_name'] ?></span>
                                    <i class="bi bi-arrow-right mx-2"></i>
                                    <span class="badge bg-primary"><?= $request['to_status_name'] ?></span>
                                </div>
                            </div>
                            <small class="text-muted"><?= date('d M Y H:i', strtotime($request['created_at'])) ?></small>
                        </div>
                        
                        <div class="bg-light p-2 rounded mb-3">
                            <small class="text-muted d-block">Requested by:</small>
                            <strong><?= htmlspecialchars($request['crewing_name']) ?></strong>
                            <?php if ($request['reason']): ?>
                            <p class="mb-0 mt-2 small"><em>"<?= htmlspecialchars($request['reason']) ?>"</em></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <form action="<?= url('/leader/requests/approve/' . $request['id']) ?>" method="POST" class="flex-grow-1">
                                <?= csrf_field() ?>
                                <input type="hidden" name="notes" value="Approved">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-lg me-1"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger flex-grow-1" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $request['id'] ?>">
                                <i class="bi bi-x-lg me-1"></i> Reject
                            </button>
                        </div>
                    </div>
                    
                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal<?= $request['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= url('/leader/requests/reject/' . $request['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Request</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Rejecting request for <strong><?= htmlspecialchars($request['applicant_name']) ?></strong></p>
                                        <div class="mb-3">
                                            <label class="form-label">Reason for rejection <span class="text-danger">*</span></label>
                                            <textarea name="notes" class="form-control" rows="3" required placeholder="Explain why this request is rejected..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Confirm Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- History -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent History</h5>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                <?php if (empty($history)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">No history yet</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($history as $item): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <?php if ($item['status'] == 'approved'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check"></i> Approved</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-x"></i> Rejected</span>
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($item['applicant_name']) ?></strong>
                                </div>
                                <small class="text-muted d-block">
                                    <?= $item['from_status_name'] ?> → <?= $item['to_status_name'] ?>
                                </small>
                                <small class="text-muted d-block">
                                    By: <?= htmlspecialchars($item['crewing_name']) ?>
                                </small>
                                <?php if ($item['response_notes']): ?>
                                <small class="text-muted fst-italic">"<?= htmlspecialchars($item['response_notes']) ?>"</small>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= date('d/m H:i', strtotime($item['responded_at'])) ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
