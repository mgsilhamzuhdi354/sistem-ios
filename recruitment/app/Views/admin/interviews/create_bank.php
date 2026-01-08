<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Question Bank - Admin | PT Indo Ocean Crew</title>
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
                <li><a href="<?= url('/admin/applicants') ?>" class="nav-link"><i class="fas fa-users"></i><span>Applicants</span></a></li>
                <li><a href="<?= url('/admin/interviews') ?>" class="nav-link active"><i class="fas fa-robot"></i><span>AI Interviews</span></a></li>
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
                <a href="<?= url('/admin/interviews') ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Interviews
                </a>
            </div>
        </header>

        <div class="admin-content">
            <div class="page-header">
                <h1><i class="fas fa-plus-circle"></i> Create Question Bank</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="<?= url('/admin/interviews/questions/store-bank') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label>Bank Name <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., Deck Department Questions" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Department (Optional)</label>
                            <select name="department_id" class="form-control">
                                <option value="">-- All Departments --</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text">Link this question bank to a specific department</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe what this question bank is for..."></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <a href="<?= url('/admin/interviews') ?>" class="btn btn-outline">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Question Bank
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1a2e; }
    .form-group .required { color: #dc3545; }
    .form-control { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: all 0.3s; }
    .form-control:focus { outline: none; border-color: #0A2463; box-shadow: 0 0 0 3px rgba(10,36,99,0.1); }
    .form-text { font-size: 12px; color: #666; margin-top: 5px; }
    .form-actions { display: flex; gap: 15px; justify-content: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0; }
    </style>
    
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
