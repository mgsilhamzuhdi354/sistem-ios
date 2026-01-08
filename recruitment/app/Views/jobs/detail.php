<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar">
                <a href="<?= url('/') ?>" class="logo">
                    <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="height: 28px;">
                    <span>Indo Ocean Crew</span>
                </a>
                <ul class="nav-menu">
                    <li><a href="<?= url('/') ?>">Home</a></li>
                    <li><a href="<?= url('/jobs') ?>" class="active">Jobs</a></li>
                    <li><a href="/PT_indoocean/about.html">About</a></li>
                </ul>
                <div class="nav-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= url('/applicant/dashboard') ?>" class="btn btn-outline-light">Dashboard</a>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-outline-light">Login</a>
                        <a href="<?= url('/register') ?>" class="btn btn-light">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success container">
            <i class="fas fa-check-circle"></i> <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger container">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Job Detail -->
    <section class="job-detail-section">
        <div class="container">
            <div class="job-detail-layout">
                <!-- Main Content -->
                <div class="job-detail-main">
                    <!-- Breadcrumb -->
                    <nav class="breadcrumb">
                        <a href="<?= url('/jobs') ?>">Job Vacancies</a>
                        <span>/</span>
                        <span><?= htmlspecialchars($job['title']) ?></span>
                    </nav>

                    <!-- Job Header -->
                    <div class="job-detail-header">
                        <div class="job-icon-large" style="background-color: <?= $job['department_color'] ?? '#0A2463' ?>">
                            <i class="fas <?= $job['department_icon'] ?? 'fa-ship' ?>"></i>
                        </div>
                        <div class="job-info">
                            <span class="department-badge"><?= htmlspecialchars($job['department_name']) ?></span>
                            <h1><?= htmlspecialchars($job['title']) ?></h1>
                            <div class="job-meta-row">
                                <span><i class="fas fa-ship"></i> <?= htmlspecialchars($job['vessel_type'] ?? 'Various Vessels') ?></span>
                                <span><i class="fas fa-calendar-alt"></i> <?= $job['contract_duration_months'] ?? '6' ?> Months Contract</span>
                                <span><i class="fas fa-clock"></i> Posted <?= date('M d, Y', strtotime($job['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="job-section">
                        <h2><i class="fas fa-file-alt"></i> Job Description</h2>
                        <div class="job-content">
                            <?= nl2br(htmlspecialchars($job['description'] ?? 'No description available.')) ?>
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="job-section">
                        <h2><i class="fas fa-list-check"></i> Requirements</h2>
                        <div class="job-content">
                            <?= nl2br(htmlspecialchars($job['requirements'] ?? 'Please contact us for requirements.')) ?>
                        </div>
                    </div>

                    <!-- Certificates -->
                    <?php 
                    $certificates = json_decode($job['required_certificates'] ?? '[]', true);
                    if (!empty($certificates)): 
                    ?>
                    <div class="job-section">
                        <h2><i class="fas fa-certificate"></i> Required Certificates</h2>
                        <div class="certificates-grid">
                            <?php foreach ($certificates as $cert): ?>
                                <span class="cert-badge"><i class="fas fa-check"></i> <?= htmlspecialchars($cert) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="job-detail-sidebar">
                    <!-- Salary Card -->
                    <div class="sidebar-card salary-card">
                        <h3>Salary Package</h3>
                        <div class="salary-amount">
                            <?php if ($job['salary_min'] && $job['salary_max']): ?>
                                <span class="currency"><?= $job['salary_currency'] ?? 'USD' ?></span>
                                <span class="amount">$<?= number_format($job['salary_min']) ?> - $<?= number_format($job['salary_max']) ?></span>
                            <?php else: ?>
                                <span class="amount">Negotiable</span>
                            <?php endif; ?>
                            <span class="period">/month</span>
                        </div>
                    </div>

                    <!-- Job Info Card -->
                    <div class="sidebar-card">
                        <h3>Job Information</h3>
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-building"></i>
                                <div>
                                    <span class="label">Department</span>
                                    <span class="value"><?= htmlspecialchars($job['department_name']) ?></span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-ship"></i>
                                <div>
                                    <span class="label">Vessel Type</span>
                                    <span class="value"><?= htmlspecialchars($job['vessel_type'] ?? 'Various') ?></span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <span class="label">Contract Duration</span>
                                    <span class="value"><?= $job['contract_duration_months'] ?? '6' ?> Months</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-user-clock"></i>
                                <div>
                                    <span class="label">Experience</span>
                                    <span class="value"><?= $job['min_experience_months'] ? ($job['min_experience_months'] . '+ months') : 'Fresh Graduate Welcome' ?></span>
                                </div>
                            </li>
                            <?php if ($job['joining_date']): ?>
                            <li>
                                <i class="fas fa-plane-departure"></i>
                                <div>
                                    <span class="label">Joining Date</span>
                                    <span class="value"><?= date('M d, Y', strtotime($job['joining_date'])) ?></span>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Apply Card -->
                    <div class="sidebar-card apply-card">
                        <?php if ($hasApplied): ?>
                            <div class="applied-notice">
                                <i class="fas fa-check-circle"></i>
                                <span>You have applied to this position</span>
                            </div>
                            <a href="<?= url('/applicant/applications') ?>" class="btn btn-secondary btn-block">
                                View My Applications
                            </a>
                        <?php elseif (isLoggedIn()): ?>
                            <form action="<?= url('/applicant/applications/apply/' . $job['id']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label>Cover Letter (Optional)</label>
                                    <textarea name="cover_letter" rows="4" placeholder="Why are you interested in this position?"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Expected Salary (USD)</label>
                                    <input type="number" name="expected_salary" placeholder="e.g., 5000">
                                </div>
                                <div class="form-group">
                                    <label>Available From</label>
                                    <input type="date" name="available_date">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block btn-lg">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            </form>
                        <?php else: ?>
                            <p class="login-notice">Please login or register to apply for this position.</p>
                            <a href="<?= url('/login') ?>" class="btn btn-primary btn-block">Login to Apply</a>
                            <a href="<?= url('/register') ?>" class="btn btn-outline btn-block">Create Account</a>
                        <?php endif; ?>
                    </div>

                    <!-- Deadline -->
                    <?php if ($job['application_deadline']): ?>
                    <div class="deadline-notice">
                        <i class="fas fa-exclamation-triangle"></i>
                        Application Deadline: <?= date('M d, Y', strtotime($job['application_deadline'])) ?>
                    </div>
                    <?php endif; ?>
                </aside>
            </div>

            <!-- Similar Jobs -->
            <?php if (!empty($similarJobs)): ?>
            <div class="similar-jobs">
                <h2>Similar Positions</h2>
                <div class="jobs-grid small">
                    <?php foreach ($similarJobs as $similar): ?>
                        <a href="<?= url('/jobs/' . $similar['id']) ?>" class="job-card-small">
                            <h4><?= htmlspecialchars($similar['title']) ?></h4>
                            <span class="dept"><?= htmlspecialchars($similar['department_name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> PT Indo Ocean Crew Services. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
