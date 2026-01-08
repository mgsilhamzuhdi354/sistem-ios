<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Vacancies - PT Indo Ocean Crew Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        .language-selector {
            display: flex;
            align-items: center;
            margin-left: 15px;
        }
        .language-selector select {
            padding: 6px 10px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 13px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .language-selector select option {
            background: #0A2463;
            color: white;
        }
    </style>
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
                    <li><a href="<?= url('/') ?>" data-translate="nav.home">Home</a></li>
                    <li><a href="<?= url('/jobs') ?>" class="active" data-translate="nav.jobs">Jobs</a></li>
                    <li><a href="/PT_indoocean/about.html">About</a></li>
                    <li><a href="/PT_indoocean/contact.html">Contact</a></li>
                </ul>
                <div class="nav-actions">
                    <div class="language-selector">
                        <select id="langSelect">
                            <option value="en">🇺🇸 EN</option>
                            <option value="id">🇮🇩 ID</option>
                            <option value="zh">🇨🇳 中文</option>
                        </select>
                    </div>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= url('/applicant/dashboard') ?>" class="btn btn-outline-light" data-translate="nav.dashboard">Dashboard</a>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-outline-light" data-translate="nav.login">Login</a>
                        <a href="<?= url('/register') ?>" class="btn btn-light" data-translate="nav.register">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="jobs-hero">
        <div class="container">
            <h1><i class="fas fa-ship"></i> <span data-translate="jobs.heroTitle">Find Your Career at Sea</span></h1>
            <p data-translate="jobs.heroSubtitle">Explore exciting maritime job opportunities with competitive packages</p>
            
            <!-- Search Bar -->
            <form action="<?= url('/jobs') ?>" method="GET" class="search-form">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" data-translate-placeholder="jobs.search"
                           placeholder="Search positions..." 
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                <select name="department">
                    <option value="" data-translate="jobs.allDepartments">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= ($filters['department'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" data-translate="common.search">Search</button>
            </form>
        </div>
    </section>

    <!-- Jobs Listing -->
    <section class="jobs-section">
        <div class="container">
            <div class="jobs-layout">
                <!-- Filters Sidebar -->
                <aside class="jobs-sidebar">
                    <div class="filter-card">
                        <h3><i class="fas fa-filter"></i> <span data-translate="jobs.filter">Filters</span></h3>
                        
                        <div class="filter-group">
                            <label data-translate="application.department">Department</label>
                            <?php foreach ($departments as $dept): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="dept_<?= $dept['id'] ?>">
                                    <span><?= htmlspecialchars($dept['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="filter-group">
                            <label data-translate="application.vesselType">Vessel Type</label>
                            <?php foreach ($vesselTypes as $vt): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="vt_<?= $vt['id'] ?>">
                                    <span><?= htmlspecialchars($vt['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <button class="btn btn-secondary btn-block" data-translate="jobs.applyFilters">Apply Filters</button>
                    </div>
                </aside>

                <!-- Jobs Grid -->
                <div class="jobs-content">
                    <div class="jobs-header">
                        <h2><?= count($jobs) ?> <span data-translate="jobs.positionsAvailable">Positions Available</span></h2>
                        <select class="sort-select">
                            <option data-translate="jobs.newestFirst">Newest First</option>
                            <option data-translate="jobs.salaryHighLow">Salary: High to Low</option>
                            <option data-translate="jobs.salaryLowHigh">Salary: Low to High</option>
                        </select>
                    </div>
                    
                    <?php if (empty($jobs)): ?>
                        <div class="no-jobs">
                            <i class="fas fa-briefcase"></i>
                            <h3 data-translate="jobs.noPositions">No positions found</h3>
                            <p data-translate="jobs.adjustFilters">Try adjusting your search filters</p>
                        </div>
                    <?php else: ?>
                        <div class="jobs-grid">
                            <?php foreach ($jobs as $job): ?>
                                <div class="job-card">
                                    <div class="job-header">
                                        <div class="job-icon" style="background-color: <?= $job['department_color'] ?? '#0A2463' ?>">
                                            <i class="fas <?= $job['department_icon'] ?? 'fa-ship' ?>"></i>
                                        </div>
                                        <div class="job-meta">
                                            <span class="department"><?= htmlspecialchars($job['department_name'] ?? 'General') ?></span>
                                            <?php if ($job['is_featured']): ?>
                                                <span class="featured-badge"><i class="fas fa-star"></i> <span data-translate="jobs.featured">Featured</span></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <h3 class="job-title"><?= htmlspecialchars($job['title']) ?></h3>
                                    
                                    <div class="job-details">
                                        <span><i class="fas fa-ship"></i> <?= htmlspecialchars($job['vessel_type'] ?? 'Various') ?></span>
                                        <span><i class="fas fa-calendar"></i> <?= $job['contract_duration_months'] ?? '6' ?> <span data-translate="jobs.months">months</span></span>
                                    </div>
                                    
                                    <div class="job-salary">
                                        <i class="fas fa-dollar-sign"></i>
                                        <?php if ($job['salary_min'] && $job['salary_max']): ?>
                                            $<?= number_format($job['salary_min']) ?> - $<?= number_format($job['salary_max']) ?>
                                        <?php else: ?>
                                            <span data-translate="jobs.negotiable">Negotiable</span>
                                        <?php endif; ?>
                                        <span class="salary-period" data-translate="jobs.perMonth">/month</span>
                                    </div>
                                    
                                    <div class="job-actions">
                                        <a href="<?= url('/jobs/' . $job['id']) ?>" class="btn btn-outline" data-translate="jobs.viewDetails">View Details</a>
                                        <a href="<?= url('/jobs/' . $job['id']) ?>" class="btn btn-primary" data-translate="jobs.applyNow">Apply Now</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 data-translate="jobs.ctaTitle">Can't find the right position?</h2>
                <p data-translate="jobs.ctaDesc">Register your profile and we'll notify you when matching positions become available.</p>
                <a href="<?= url('/register') ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-user-plus"></i> <span data-translate="jobs.registerNow">Register Now</span>
                </a>
            </div>
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

    <script src="<?= asset('js/main.js') ?>"></script>
    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
</body>
</html>
