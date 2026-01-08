<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants - Admin | PT Indo Ocean Crew</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/admin') ?>" class="logo"><img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;"><span>Recruitment</span></a>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="<?= url('/admin/dashboard') ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link active"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link"><i class="fas fa-stream"></i><span>Pipeline</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
                <li><a href="<?= url('/admin/documents') ?>" class="nav-link"><i class="fas fa-file-alt"></i><span>Documents</span></a></li>
                <li><a href="<?= url('/admin/medical') ?>" class="nav-link"><i class="fas fa-heartbeat"></i><span>Medical</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
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

        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-users"></i> Applicants</h1>
                <a href="<?= url('/admin/applicants/pipeline') ?>" class="btn btn-outline">
                    <i class="fas fa-stream"></i> View Pipeline
                </a>
            </div>

            <!-- Stats -->
            <div class="stats-grid stats-small">
                <?php foreach ($statusCounts ?? [] as $status): ?>
                    <div class="stat-card mini" style="border-left: 4px solid <?= $status['color'] ?? '#0A2463' ?>">
                        <h3><?= $status['count'] ?? 0 ?></h3>
                        <p><?= htmlspecialchars($status['name'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Filters -->
            <div class="card mb-20">
                <div class="card-body">
                    <form action="<?= url('/admin/applicants') ?>" method="GET" class="filters-form">
                        <div class="filter-row">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <?php foreach ($statuses ?? [] as $status): ?>
                                        <option value="<?= $status['id'] ?>" <?= ($_GET['status'] ?? '') == $status['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($status['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="vacancy" class="form-control">
                                    <option value="">All Positions</option>
                                    <?php foreach ($vacancies ?? [] as $vacancy): ?>
                                        <option value="<?= $vacancy['id'] ?>" <?= ($_GET['vacancy'] ?? '') == $vacancy['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($vacancy['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Applicants Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($applicants)): ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h3>No Applicants Found</h3>
                            <p>No applicants match your filter criteria.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Position</th>
                                        <th>Status</th>
                                        <th>Applied</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applicants as $app): ?>
                                        <tr>
                                            <td>
                                                <div class="user-badge">
                                                    <img src="<?= asset('images/avatar-default.svg') ?>" alt="">
                                                    <div class="user-info">
                                                        <strong><?= htmlspecialchars($app['full_name']) ?></strong>
                                                        <span><?= htmlspecialchars($app['email']) ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($app['vacancy_title'] ?? '-') ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($app['department_name'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: <?= $app['status_color'] ?? '#6c757d' ?>20; color: <?= $app['status_color'] ?? '#6c757d' ?>">
                                                    <?= htmlspecialchars($app['status_name'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td><?= date('d M Y', strtotime($app['submitted_at'] ?? $app['created_at'])) ?></td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="<?= url('/admin/applicants/' . $app['id']) ?>" class="action-btn view" title="View"><i class="fas fa-eye"></i></a>
                                                </div>
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
    .stats-small { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); margin-bottom: 20px; }
    .stat-card.mini { padding: 15px 20px; }
    .stat-card.mini h3 { font-size: 24px; margin-bottom: 2px; }
    .stat-card.mini p { font-size: 12px; color: #6c757d; }
    .filter-row { display: flex; gap: 15px; align-items: flex-end; }
    .filter-row .form-group { flex: 1; margin-bottom: 0; }
    .mb-20 { margin-bottom: 20px; }
    .text-muted { color: #6c757d; }
    </style>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
