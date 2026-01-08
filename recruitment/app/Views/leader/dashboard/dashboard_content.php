<!-- Leader Dashboard Content -->
<div class="row g-4 mb-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="stat-card-leader">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 opacity-75">Team Crewing</h6>
                    <h2 class="mb-0"><?= $teamStats['total_crewing'] ?? 0 ?></h2>
                </div>
                <div class="fs-1 opacity-50"><i class="bi bi-people"></i></div>
            </div>
            <div class="mt-2">
                <span class="online-indicator online me-1"></span>
                <small><?= $teamStats['online_crewing'] ?? 0 ?> online</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Pending Requests</h6>
                        <h2 class="mb-0 text-warning"><?= count($pendingRequests) ?></h2>
                    </div>
                    <div class="fs-1 text-warning opacity-50"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <?php if (count($pendingRequests) > 0): ?>
                <a href="<?= url('/leader/requests') ?>" class="btn btn-sm btn-warning mt-2">Review Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Active Assignments</h6>
                        <h2 class="mb-0 text-primary"><?= $teamStats['active_assignments'] ?? 0 ?></h2>
                    </div>
                    <div class="fs-1 text-primary opacity-50"><i class="bi bi-file-earmark-check"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Processed</h6>
                        <h2 class="mb-0 text-success"><?= $teamStats['total_assignments'] ?? 0 ?></h2>
                    </div>
                    <div class="fs-1 text-success opacity-50"><i class="bi bi-check-circle"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Requests & Online Team -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-check2-square me-2 text-warning"></i>Pending Requests</h5>
                <span class="badge bg-warning"><?= count($pendingRequests) ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($pendingRequests)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-check-circle fs-1"></i>
                    <p class="mt-2">No pending requests</p>
                </div>
                <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($pendingRequests, 0, 5) as $request): ?>
                    <div class="list-group-item request-card bg-light mb-2 rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($request['applicant_name']) ?></h6>
                                <small class="text-muted">
                                    <span class="badge bg-secondary"><?= $request['from_status_name'] ?></span>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <span class="badge bg-primary"><?= $request['to_status_name'] ?></span>
                                </small>
                                <p class="mb-0 small text-muted mt-1">By: <?= htmlspecialchars($request['crewing_name']) ?></p>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <form action="<?= url('/leader/requests/approve/' . $request['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check"></i></button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $request['id'] ?>">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal<?= $request['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="<?= url('/leader/requests/reject/' . $request['id']) ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Reason for rejection</label>
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
                </div>
                <?php if (count($pendingRequests) > 5): ?>
                <a href="<?= url('/leader/requests') ?>" class="btn btn-outline-warning w-100 mt-3">View All (<?= count($pendingRequests) ?>)</a>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="bi bi-people me-2 text-success"></i>Team Status</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($onlineCrewingStaff, 0, 6) as $crewing): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="d-flex align-items-center">
                            <span class="online-indicator <?= $crewing['is_online'] ? 'online' : 'offline' ?> me-3"></span>
                            <div>
                                <strong><?= htmlspecialchars($crewing['full_name']) ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?= $crewing['rank'] ?? 'Staff' ?> · <?= $crewing['company'] ?? '-' ?>
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <?php if ($crewing['is_online']): ?>
                            <span class="badge bg-success">Online</span>
                            <?php else: ?>
                            <small class="text-muted">
                                <?= $crewing['minutes_ago'] ? $crewing['minutes_ago'] . 'm ago' : 'Never' ?>
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?= url('/leader/team') ?>" class="btn btn-outline-success w-100 mt-3">Manage Team</a>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline Overview -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-kanban me-2 text-primary"></i>Pipeline Overview</h5>
                <a href="<?= url('/leader/pipeline') ?>" class="btn btn-sm btn-primary">View Full Pipeline</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($pipelineStats as $stat): ?>
                    <div class="col-md-2">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0" style="color: <?= $stat['color'] ?? '#666' ?>"><?= $stat['count'] ?></h4>
                                <small class="text-muted"><?= $stat['name'] ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Crewing Performance -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2 text-info"></i>Crewing Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Crewing PIC</th>
                                <th>Position</th>
                                <th>Company</th>
                                <th>Active</th>
                                <th>Completed</th>
                                <th>Hired</th>
                                <th>Rating</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($crewingPerformance as $i => $perf): ?>
                            <tr>
                                <td>
                                    <?php if ($i == 0): ?>
                                    <span class="fs-5">🥇</span>
                                    <?php elseif ($i == 1): ?>
                                    <span class="fs-5">🥈</span>
                                    <?php elseif ($i == 2): ?>
                                    <span class="fs-5">🥉</span>
                                    <?php else: ?>
                                    <span class="text-muted"><?= $i + 1 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($perf['full_name']) ?></strong>
                                </td>
                                <td><?= $perf['rank'] ?? '-' ?></td>
                                <td><?= $perf['company'] ?? '-' ?></td>
                                <td><span class="badge bg-primary"><?= $perf['active_count'] ?></span></td>
                                <td><span class="badge bg-success"><?= $perf['completed_count'] ?></span></td>
                                <td><span class="badge bg-info"><?= $perf['hired_count'] ?></span></td>
                                <td>
                                    <?php 
                                    $rating = round($perf['avg_rating'], 1);
                                    for ($s = 1; $s <= 5; $s++): 
                                    ?>
                                    <i class="bi <?= $s <= $rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' ?>"></i>
                                    <?php endfor; ?>
                                    <small class="text-muted">(<?= $rating ?>)</small>
                                </td>
                                <td>
                                    <span class="online-indicator <?= $perf['is_online'] ? 'online' : 'offline' ?>"></span>
                                    <?= $perf['is_online'] ? 'Online' : 'Offline' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
