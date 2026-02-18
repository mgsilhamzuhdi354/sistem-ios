<?php
if (isMasterAdmin()) {
    $content = 'documents/index';
    include APPPATH . 'Views/layouts/master_admin.php';
    return;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - <?= isMasterAdmin() ? 'Master Admin' : 'Admin' ?> | PT Indo Ocean Crew</title>
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
                        <a href="<?= url('/admin/documents') ?>" class="nav-link active">
                            <i class="fas fa-file-alt"></i>
                            <span>Documents</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= url('/admin/medical') ?>" class="nav-link">
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
                    <li><a href="<?= url('/admin/documents') ?>" class="nav-link active"><i
                                class="fas fa-file-alt"></i><span>Documents</span></a></li>
                    <li><a href="<?= url('/admin/medical') ?>" class="nav-link"><i
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
                <h1><i class="fas fa-file-alt"></i> Document Verification</h1>
            </div>

            <!-- Stats -->
            <div class="stats-grid stats-small">
                <div class="stat-card mini" style="border-left: 4px solid #ffc107">
                    <h3><?= $stats['pending'] ?? 0 ?></h3>
                    <p>Pending Review</p>
                </div>
                <div class="stat-card mini" style="border-left: 4px solid #28a745">
                    <h3><?= $stats['verified'] ?? 0 ?></h3>
                    <p>Verified</p>
                </div>
                <div class="stat-card mini" style="border-left: 4px solid #dc3545">
                    <h3><?= $stats['rejected'] ?? 0 ?></h3>
                    <p>Rejected</p>
                </div>
                <div class="stat-card mini" style="border-left: 4px solid #0A2463">
                    <h3><?= count($applicants ?? []) ?></h3>
                    <p>Applicants with Docs</p>
                </div>
            </div>

            <!-- Applicants with Documents -->
            <div class="card">
                <div class="card-header">
                    <h3>Applicants with Documents</h3>
                    <div class="filter-inline">
                        <select id="filterStatus" onchange="filterApplicants(this.value)">
                            <option value="">All Applicants</option>
                            <option value="pending">Has Pending Docs</option>
                            <option value="complete">All Verified</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($applicants)): ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h3>No Documents Yet</h3>
                            <p>Applicants who upload documents will appear here.</p>
                        </div>
                    <?php else: ?>
                        <div class="applicants-grid">
                            <?php foreach ($applicants as $app): ?>
                                <div class="applicant-card"
                                    data-status="<?= $app['pending_count'] > 0 ? 'pending' : 'complete' ?>">
                                    <div class="applicant-header">
                                        <div class="applicant-avatar">
                                            <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                                        </div>
                                        <div class="applicant-info">
                                            <h4><?= htmlspecialchars($app['full_name']) ?></h4>
                                            <span class="email"><?= htmlspecialchars($app['email']) ?></span>
                                        </div>
                                    </div>

                                    <div class="doc-summary">
                                        <div class="summary-item">
                                            <span class="count pending"><?= $app['pending_count'] ?></span>
                                            <span class="label">Pending</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="count verified"><?= $app['verified_count'] ?></span>
                                            <span class="label">Verified</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="count rejected"><?= $app['rejected_count'] ?></span>
                                            <span class="label">Rejected</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="count total"><?= $app['total_docs'] ?></span>
                                            <span class="label">Total</span>
                                        </div>
                                    </div>

                                    <div class="progress-bar">
                                        <?php $percent = $app['total_docs'] > 0 ? ($app['verified_count'] / $app['total_docs']) * 100 : 0; ?>
                                        <div class="progress" style="width: <?= $percent ?>%"></div>
                                    </div>

                                    <a href="<?= url('/admin/documents/applicant/' . $app['user_id']) ?>"
                                        class="btn btn-outline btn-block">
                                        <i class="fas fa-folder-open"></i> View Documents
                                    </a>
                                </div>
                            <?php endforeach; ?>
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

        .filter-inline select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .applicants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .applicant-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .applicant-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .applicant-card[data-status="pending"] {
            border-left: 4px solid #ffc107;
        }

        .applicant-card[data-status="complete"] {
            border-left: 4px solid #28a745;
        }

        .applicant-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .applicant-avatar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .applicant-info h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0 0 5px 0;
        }

        .applicant-info .email {
            font-size: 13px;
            color: #6c757d;
        }

        .doc-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .summary-item .count {
            display: block;
            font-size: 20px;
            font-weight: 700;
        }

        .summary-item .count.pending {
            color: #ffc107;
        }

        .summary-item .count.verified {
            color: #28a745;
        }

        .summary-item .count.rejected {
            color: #dc3545;
        }

        .summary-item .count.total {
            color: #0A2463;
        }

        .summary-item .label {
            font-size: 11px;
            color: #6c757d;
        }

        .progress-bar {
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-bar .progress {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            border-radius: 3px;
            transition: width 0.3s;
        }

        .btn-block {
            width: 100%;
            text-align: center;
        }
    </style>

    <script>
        function filterApplicants(status) {
            const cards = document.querySelectorAll('.applicant-card');
            cards.forEach(card => {
                if (!status || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>

</html>