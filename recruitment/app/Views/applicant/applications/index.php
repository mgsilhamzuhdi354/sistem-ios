<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/applicant.css') ?>">
    <style>
        .language-selector {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .language-selector select {
            padding: 6px 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: white;
            font-size: 13px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="applicant-body">
    <!-- Sidebar -->
    <aside class="applicant-sidebar">
        <div class="sidebar-header">
            <a href="<?= url('/') ?>" class="logo">
                <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="width:32px;height:32px;object-fit:contain;">
                <span>Indo Ocean</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?= url('/applicant/dashboard') ?>" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span data-translate="nav.dashboard">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/jobs') ?>" class="nav-link">
                        <i class="fas fa-briefcase"></i>
                        <span data-translate="nav.jobs">Job Vacancies</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/applications') ?>" class="nav-link active">
                        <i class="fas fa-file-alt"></i>
                        <span data-translate="nav.applications">My Applications</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/documents') ?>" class="nav-link">
                        <i class="fas fa-folder"></i>
                        <span data-translate="nav.documents">Documents</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('/applicant/interview') ?>" class="nav-link">
                        <i class="fas fa-video"></i>
                        <span data-translate="nav.interview">Interview</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?= url('/logout') ?>" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span data-translate="nav.logout">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="applicant-main">
        <header class="page-header-bar">
            <h1><i class="fas fa-file-alt"></i> <span data-translate="application.title">My Applications</span></h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <div class="language-selector">
                    <select id="langSelect">
                        <option value="en">🇺🇸 EN</option>
                        <option value="id">🇮🇩 ID</option>
                        <option value="zh">🇨🇳 中文</option>
                    </select>
                </div>
                <a href="<?= url('/jobs') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> <span data-translate="application.applyNewJob">Apply to New Job</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>
        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Applications List -->
        <div class="card">
            <div class="card-body">
                <?php if (empty($applications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h3 data-translate="application.noApplications">No Applications Yet</h3>
                        <p data-translate="application.noApplicationsDesc">You haven't applied to any positions yet. Start exploring job vacancies!</p>
                        <a href="<?= url('/jobs') ?>" class="btn btn-primary">
                            <i class="fas fa-search"></i> <span data-translate="dashboard.browseJobs">Browse Jobs</span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th data-translate="application.position">Position</th>
                                    <th data-translate="application.department">Department</th>
                                    <th data-translate="application.salaryRange">Salary Range</th>
                                    <th data-translate="application.appliedOn">Applied On</th>
                                    <th data-translate="application.status">Status</th>
                                    <th data-translate="common.actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($app['vacancy_title']) ?></strong>
                                            <?php if (!empty($app['vessel_type'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($app['vessel_type']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($app['department_name'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($app['salary_min'] && $app['salary_max']): ?>
                                                $<?= number_format($app['salary_min']) ?> - $<?= number_format($app['salary_max']) ?>
                                            <?php else: ?>
                                                <span data-translate="jobs.negotiable">Negotiable</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                                        <td>
                                            <span class="status-badge" style="background: <?= $app['status_color'] ?>20; color: <?= $app['status_color'] ?>">
                                                <?= htmlspecialchars($app['status_name']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= url('/applicant/applications/' . $app['id']) ?>" class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye"></i> <span data-translate="common.view">View</span>
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
    </main>

    <style>
    .page-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    .page-header-bar h1 {
        font-size: 24px;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .page-header-bar h1 i {
        color: #0A2463;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th, .data-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }
    .data-table th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: #6c757d;
        background: #f8f9fa;
    }
    .data-table tbody tr:hover {
        background: #f8f9fa;
    }
    .text-muted {
        color: #6c757d;
    }
    .btn-outline {
        background: transparent;
        border: 1px solid #0A2463;
        color: #0A2463;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }
    .btn-outline:hover {
        background: #0A2463;
        color: white;
    }
    </style>
    
    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
</body>
</html>
