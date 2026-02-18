<?php
if (isMasterAdmin()) {
    $content = 'medical/index';
    include APPPATH . 'Views/layouts/master_admin.php';
    return;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Checkups - <?= isMasterAdmin() ? 'Master Admin' : 'Admin' ?> | PT Indo Ocean Crew</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <?php if (isMasterAdmin()): ?>
        <style>
            .admin-sidebar {
                background: linear-gradient(180deg, #1e3a5f 0%, #0d1f33 100%);
            }

            .nav-link.active {
                background: linear-gradient(90deg, #dc2626 0%, #b91c1c 100%);
            }

            .nav-link:hover {
                background: rgba(220, 38, 38, 0.2);
            }
        </style>
    <?php endif; ?>
</head>

<body class="admin-body">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <?php if (isMasterAdmin()): ?>
                <a href="<?= url('/master-admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>"
                        alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Master Admin</span></a>
            <?php else: ?>
                <a href="<?= url('/admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean"
                        style="width:32px;height:32px;object-fit:contain;"><span>Recruitment</span></a>
            <?php endif; ?>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <?php if (isMasterAdmin()): ?>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/dashboard') ?>" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/users') ?>" class="nav-link">
                            <i class="fas fa-users-cog"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/vacancies') ?>" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            <span>Job Vacancies</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/pipeline') ?>" class="nav-link">
                            <i class="fas fa-stream"></i>
                            <span>Pipeline</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/requests') ?>" class="nav-link">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Requests</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/interviews') ?>" class="nav-link">
                            <i class="fas fa-robot"></i>
                            <span>AI Interview</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/documents') ?>" class="nav-link">
                            <i class="fas fa-file-alt"></i>
                            <span>Documents</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/medical') ?>" class="nav-link active">
                            <i class="fas fa-heartbeat"></i>
                            <span>Medical</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/reports') ?>" class="nav-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/master-admin/settings') ?>" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li><a href="<?= url('/admin/dashboard') ?>" class="nav-link"><i
                                class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                    <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job
                                Vacancies</span></a></li>
                    <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i
                                class="fas fa-users"></i><span>Applicants</span></a></li>
                    <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link"><i
                                class="fas fa-stream"></i><span>Pipeline</span></a></li>
                    <li><a href="<?= url('/admin/interviews') ?>" class="nav-link"><i class="fas fa-robot"></i><span>AI
                                Interviews</span></a></li>
                    <li><a href="<?= url('/admin/documents') ?>" class="nav-link"><i
                                class="fas fa-file-alt"></i><span>Documents</span></a></li>
                    <li><a href="<?= url('/admin/medical') ?>" class="nav-link active"><i
                                class="fas fa-heartbeat"></i><span>Medical</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i
                    class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <div class="header-actions">
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                        <span><?= $_SESSION['user_name'] ?? 'Admin' ?></span>
                    </button>
                </div>
            </div>
        </header>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-heartbeat"></i> Medical Checkups</h1>
                <a href="<?= url('/admin/medical/schedule') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Schedule MCU
                </a>
            </div>

            <!-- Stats -->
            <div class="stats-grid stats-small">
                <div class="stat-card mini" style="border-left: 4px solid #ffc107">
                    <h3><?= $stats['scheduled'] ?? 0 ?></h3>
                    <p>Scheduled</p>
                </div>
                <div class="stat-card mini" style="border-left: 4px solid #17a2b8">
                    <h3><?= $stats['in_progress'] ?? 0 ?></h3>
                    <p>In Progress</p>
                </div>
                <div class="stat-card mini" style="border-left: 4px solid #28a745">
                    <h3><?= $stats['fit'] ?? 0 ?></h3>
                    <p>Fit</p>
                </div>
                <div class="stat-card mini" style="border-left: 4px solid #dc3545">
                    <h3><?= $stats['unfit'] ?? 0 ?></h3>
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
                                                        <strong><?= htmlspecialchars($mcu['full_name']) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($mcu['vacancy_title'] ?? '-') ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($mcu['hospital_name']) ?></strong>
                                                <?php if (!empty($mcu['hospital_address'])): ?>
                                                    <br><small
                                                        class="text-muted"><?= htmlspecialchars($mcu['hospital_address']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= date('d M Y', strtotime($mcu['scheduled_date'])) ?></strong>
                                                <br><small><?= $mcu['scheduled_time'] ?? '09:00' ?></small>
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
                                                <a href="<?= url('/admin/medical/' . $mcu['id']) ?>"
                                                    class="btn btn-sm btn-outline">
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
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>

</html>