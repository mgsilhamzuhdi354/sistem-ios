<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($vacancy) ? 'Edit' : 'Create' ?> Vacancy - Admin | PT Indo Ocean Crew</title>
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
                <li><a href="<?= url('/admin/vacancies') ?>" class="nav-link active"><i class="fas fa-briefcase"></i><span>Job Vacancies</span></a></li>
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
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

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <div class="admin-content">
            <div class="page-header">
                <div>
                    <a href="<?= url('/admin/vacancies') ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Vacancies</a>
                    <h1><?= isset($vacancy) ? 'Edit Vacancy' : 'Create New Vacancy' ?></h1>
                </div>
            </div>

            <form action="<?= url('/admin/vacancies/' . (isset($vacancy) ? 'update/' . $vacancy['id'] : 'store')) ?>" method="POST" class="vacancy-form">
                <?= csrf_field() ?>
                
                <div class="form-grid">
                    <div class="form-main">
                        <div class="card">
                            <div class="card-header"><h3>Basic Information</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Job Title <span class="required">*</span></label>
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($vacancy['title'] ?? old('title')) ?>" required>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Department <span class="required">*</span></label>
                                        <select name="department_id" class="form-control" required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments ?? [] as $dept): ?>
                                                <option value="<?= $dept['id'] ?>" <?= ($vacancy['department_id'] ?? old('department_id')) == $dept['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($dept['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Vessel Type</label>
                                        <select name="vessel_type_id" class="form-control">
                                            <option value="">Select Vessel Type</option>
                                            <?php foreach ($vesselTypes ?? [] as $vt): ?>
                                                <option value="<?= $vt['id'] ?>" <?= ($vacancy['vessel_type_id'] ?? old('vessel_type_id')) == $vt['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($vt['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Description <span class="required">*</span></label>
                                    <textarea name="description" class="form-control" rows="6" required><?= htmlspecialchars($vacancy['description'] ?? old('description')) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Requirements</label>
                                    <textarea name="requirements" class="form-control" rows="4" placeholder="Enter each requirement on a new line"><?= htmlspecialchars($vacancy['requirements'] ?? old('requirements')) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Required Certificates</label>
                                    <textarea name="certificates_required" class="form-control" rows="3" placeholder="Enter required certificates"><?= htmlspecialchars($vacancy['certificates_required'] ?? old('certificates_required')) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-sidebar">
                        <div class="card">
                            <div class="card-header"><h3>Compensation</h3></div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Min Salary ($)</label>
                                        <input type="number" name="salary_min" class="form-control" value="<?= $vacancy['salary_min'] ?? old('salary_min') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Max Salary ($)</label>
                                        <input type="number" name="salary_max" class="form-control" value="<?= $vacancy['salary_max'] ?? old('salary_max') ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Contract Duration (months)</label>
                                    <input type="number" name="contract_duration_months" class="form-control" value="<?= $vacancy['contract_duration_months'] ?? old('contract_duration_months') ?? 6 ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header"><h3>Settings</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="draft" <?= ($vacancy['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="published" <?= ($vacancy['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                                        <option value="closed" <?= ($vacancy['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Positions Available</label>
                                    <input type="number" name="positions_available" class="form-control" value="<?= $vacancy['positions_available'] ?? old('positions_available') ?? 1 ?>" min="1">
                                </div>
                                <div class="form-group">
                                    <label>Application Deadline</label>
                                    <input type="date" name="deadline" class="form-control" value="<?= $vacancy['deadline'] ?? old('deadline') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_featured" value="1" <?= !empty($vacancy['is_featured']) ? 'checked' : '' ?>>
                                        Featured Vacancy
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> <?= isset($vacancy) ? 'Update Vacancy' : 'Create Vacancy' ?>
                            </button>
                            <a href="<?= url('/admin/vacancies') ?>" class="btn btn-outline btn-block">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
    .back-link { color: #0A2463; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 10px; }
    .form-grid { display: grid; grid-template-columns: 1fr 350px; gap: 25px; }
    .form-main .card, .form-sidebar .card { margin-bottom: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 14px; font-weight: 500; color: #333; margin-bottom: 8px; }
    .form-group .required { color: #dc3545; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; transition: all 0.3s; }
    .form-control:focus { border-color: #0A2463; outline: none; box-shadow: 0 0 0 3px rgba(10,36,99,0.1); }
    textarea.form-control { resize: vertical; }
    .checkbox-label { display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .checkbox-label input { width: 18px; height: 18px; }
    .form-actions { margin-top: 20px; }
    .btn-block { width: 100%; margin-bottom: 10px; }
    @media (max-width: 992px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
