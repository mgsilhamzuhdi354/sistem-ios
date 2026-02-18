<div class="page-header">
    <h1><i class="fas fa-heartbeat"></i> Medical Checkups</h1>
    <a href="<?= url('/admin/medical/schedule') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Schedule MCU
    </a>
</div>

<!-- Stats -->
<div class="stats-grid stats-small">
    <div class="stat-card mini" style="border-left: 4px solid #ffc107">
        <h3>
            <?= $stats['scheduled'] ?? 0 ?>
        </h3>
        <p>Scheduled</p>
    </div>
    <div class="stat-card mini" style="border-left: 4px solid #17a2b8">
        <h3>
            <?= $stats['in_progress'] ?? 0 ?>
        </h3>
        <p>In Progress</p>
    </div>
    <div class="stat-card mini" style="border-left: 4px solid #28a745">
        <h3>
            <?= $stats['fit'] ?? 0 ?>
        </h3>
        <p>Fit</p>
    </div>
    <div class="stat-card mini" style="border-left: 4px solid #dc3545">
        <h3>
            <?= $stats['unfit'] ?? 0 ?>
        </h3>
        <p>Unfit</p>
    </div>
</div>

<!-- Medical Checkups Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($checkups)): ?>
            <div class="empty-state">
                <i class="fas fa-heartbeat"></i>
                <h3>No Medical Checkups</h3>
                <p>Schedule medical checkups for applicants who passed the interview stage.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Position</th>
                            <th>Hospital</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checkups as $mcu): ?>
                            <tr>
                                <td>
                                    <div class="user-badge">
                                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                                        <div class="user-info">
                                            <strong>
                                                <?= htmlspecialchars($mcu['full_name']) ?>
                                            </strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= htmlspecialchars($mcu['vacancy_title'] ?? '-') ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($mcu['hospital_name']) ?>
                                    </strong>
                                    <?php if (!empty($mcu['hospital_address'])): ?>
                                        <br><small class="text-muted">
                                            <?= htmlspecialchars($mcu['hospital_address']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= date('d M Y', strtotime($mcu['scheduled_date'])) ?>
                                    </strong>
                                    <br><small>
                                        <?= $mcu['scheduled_time'] ?? '09:00' ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    $colors = ['scheduled' => '#ffc107', 'in_progress' => '#17a2b8', 'completed' => '#28a745', 'cancelled' => '#dc3545'];
                                    $color = $colors[$mcu['status']] ?? '#6c757d';
                                    ?>
                                    <span class="badge" style="background: <?= $color ?>20; color: <?= $color ?>">
                                        <?= ucfirst(str_replace('_', ' ', $mcu['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($mcu['result'])): ?>
                                        <span
                                            class="badge badge-<?= $mcu['result'] === 'fit' ? 'success' : ($mcu['result'] === 'unfit' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($mcu['result']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= url('/admin/medical/' . $mcu['id']) ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .stats-small {
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 20px;
    }

    .stat-card.mini {
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .stat-card.mini h3 {
        font-size: 28px;
        color: #1a1a2e;
        margin-bottom: 5px;
    }

    .stat-card.mini p {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
    }

    .text-muted {
        color: #6c757d;
    }
</style>