<?php
if (isMasterAdmin()) {
    $content = 'vacancies/index';
    include APPPATH . 'Views/layouts/master_admin.php';
    return;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Vacancies' ?> - Admin | PT Indo Ocean Crew</title>
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
    <!-- Sidebar -->
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
                        <a href="<?= url('/master-admin/vacancies') ?>" class="nav-link active">
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
                    <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link active"><i
                                class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                    <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i
                                class="fas fa-users"></i><span>Applicants</span></a></li>
                    <li><a href="<?= url('/admin/applicants/pipeline') ?>" class="nav-link"><i
                                class="fas fa-stream"></i><span>Pipeline</span></a></li>
                    <li><a href="<?= url('/admin/interviews') ?>" class="nav-link"><i class="fas fa-robot"></i><span>AI
                                Interviews</span></a></li>
                    <li><a href="<?= url('/admin/documents') ?>" class="nav-link"><i
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
                <h1><i class="fas fa-briefcase"></i> Job Vacancies</h1>
                <a href="<?= url('/admin/vacancies/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Vacancy
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-20">
                <div class="card-body">
                    <form action="<?= url('/admin/vacancies') ?>" method="GET" class="filters-form">
                        <div class="filter-row">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="Search vacancies..."
                                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <select name="department" class="form-control">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments ?? [] as $dept): ?>
                                        <option value="<?= $dept['id'] ?>" <?= ($_GET['department'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>>
                                        Draft</option>
                                    <option value="published" <?= ($_GET['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                                    <option value="closed" <?= ($_GET['status'] ?? '') === 'closed' ? 'selected' : '' ?>>
                                        Closed</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vacancies Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($vacancies)): ?>
                        <div class="empty-state">
                            <i class="fas fa-briefcase"></i>
                            <h3>No Vacancies Found</h3>
                            <p>Start by creating a new job vacancy.</p>
                            <a href="<?= url('/admin/vacancies/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Vacancy
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Salary Range</th>
                                        <th>Applications</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vacancies as $vacancy): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($vacancy['title']) ?></strong>
                                                <?php if (!empty($vacancy['is_featured'])): ?>
                                                    <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($vacancy['department_name'] ?? '-') ?></td>
                                            <td>
                                                <?php if ($vacancy['salary_min'] && $vacancy['salary_max']): ?>
                                                    $<?= number_format($vacancy['salary_min']) ?> -
                                                    $<?= number_format($vacancy['salary_max']) ?>
                                                <?php else: ?>
                                                    Negotiable
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="badge badge-info"><?= $vacancy['applications_count'] ?? 0 ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = $vacancy['status'] === 'published' ? 'success' : ($vacancy['status'] === 'draft' ? 'secondary' : 'danger');
                                                ?>
                                                <span
                                                    class="badge badge-<?= $statusClass ?>"><?= ucfirst($vacancy['status']) ?></span>
                                            </td>
                                            <td><?= date('d M Y', strtotime($vacancy['created_at'])) ?></td>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="<?= url('/jobs/' . $vacancy['id']) ?>" class="action-btn view"
                                                        title="View" target="_blank"><i class="fas fa-eye"></i></a>
                                                    <a href="<?= url('/admin/vacancies/edit/' . $vacancy['id']) ?>"
                                                        class="action-btn edit" title="Edit"><i class="fas fa-edit"></i></a>
                                                    <form action="<?= url('/admin/vacancies/delete/' . $vacancy['id']) ?>"
                                                        method="POST" class="delete-form" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="action-btn delete" title="Delete"
                                                            onclick="return confirm('Are you sure?')"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
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
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>

</html>