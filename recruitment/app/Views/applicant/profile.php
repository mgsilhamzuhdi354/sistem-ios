<?php
/**
 * Applicant Profile View
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PT Indo Ocean Crew Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary: #0A2463;
            --primary-light: #1E3A8A;
            --secondary: #00A8E8;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-500: #6B7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--gray-50); color: var(--gray-900); }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--primary);
            padding: 1.5rem;
            z-index: 100;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo i { font-size: 1.5rem; }
        .sidebar-logo span { font-weight: 700; font-size: 1.1rem; }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }

        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar-nav a.active { background: var(--secondary); }
        .sidebar-nav a i { width: 20px; }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 { font-size: 1.75rem; font-weight: 700; }

        /* Profile Completion */
        .completion-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .completion-bar h3 { font-size: 1rem; margin-bottom: 0.75rem; }

        .progress-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .progress-bar {
            flex: 1;
            height: 12px;
            background: var(--gray-200);
            border-radius: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary), var(--success));
            border-radius: 6px;
            transition: width 0.5s ease;
        }

        .progress-text { font-weight: 600; color: var(--primary); min-width: 50px; }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .form-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-card-header i { color: var(--secondary); font-size: 1.25rem; }
        .form-card-header h2 { font-size: 1.125rem; font-weight: 600; }
        .form-card-body { padding: 1.5rem; }

        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        .form-grid.three-cols { grid-template-columns: repeat(3, 1fr); }
        .form-group.full-width { grid-column: span 2; }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-group label .required { color: var(--danger); }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(0,168,232,0.1);
        }

        .form-group textarea { min-height: 100px; resize: vertical; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
        }

        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(10,36,99,0.3); }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            padding: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        /* Alert */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-error { background: #FEE2E2; color: #991B1B; }

        /* Responsive */
        @media (max-width: 1024px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-grid.three-cols { grid-template-columns: 1fr 1fr; }
            .form-group.full-width { grid-column: span 1; }
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .form-grid.three-cols { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="<?= asset('images/logo.jpg') ?>" alt="Indo Ocean" style="height: 24px;">
            <span>Indo Ocean Crew</span>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= url('/applicant/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
            <a href="<?= url('/applicant/profile') ?>" class="active"><i class="fas fa-user"></i> My Profile</a>
            <a href="<?= url('/applicant/documents') ?>"><i class="fas fa-file-alt"></i> Documents</a>
            <a href="<?= url('/applicant/applications') ?>"><i class="fas fa-briefcase"></i> Applications</a>
            <a href="<?= url('/jobs') ?>"><i class="fas fa-search"></i> Find Jobs</a>
            <a href="<?= url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h1>My Profile</h1>
        </div>

        <?php if ($flash = flash('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= $flash ?>
            </div>
        <?php endif; ?>

        <?php if ($flash = flash('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $flash ?>
            </div>
        <?php endif; ?>

        <!-- Profile Completion -->
        <div class="completion-bar">
            <h3><i class="fas fa-chart-line"></i> Profile Completion</h3>
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $user['profile_completion'] ?? 0 ?>%"></div>
                </div>
                <span class="progress-text"><?= $user['profile_completion'] ?? 0 ?>%</span>
            </div>
        </div>

        <form action="<?= url('/applicant/profile/update') ?>" method="POST">
            <?= csrf_field() ?>

            <!-- Personal Information -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-user"></i>
                    <h2>Personal Information</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name <span class="required">*</span></label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Date of Birth <span class="required">*</span></label>
                            <input type="date" name="date_of_birth" value="<?= $user['date_of_birth'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Gender <span class="required">*</span></label>
                            <select name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nationality <span class="required">*</span></label>
                            <input type="text" name="nationality" value="<?= htmlspecialchars($user['nationality'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Place of Birth</label>
                            <input type="text" name="place_of_birth" value="<?= htmlspecialchars($user['place_of_birth'] ?? '') ?>">
                        </div>
                        <div class="form-group full-width">
                            <label>Address</label>
                            <textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="<?= htmlspecialchars($user['country'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" name="postal_code" value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seafarer Information -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-ship"></i>
                    <h2>Seafarer Information</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Seaman Book Number <span class="required">*</span></label>
                            <input type="text" name="seaman_book_no" value="<?= htmlspecialchars($user['seaman_book_no'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Seaman Book Expiry</label>
                            <input type="date" name="seaman_book_expiry" value="<?= $user['seaman_book_expiry'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Passport Number <span class="required">*</span></label>
                            <input type="text" name="passport_no" value="<?= htmlspecialchars($user['passport_no'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Passport Expiry</label>
                            <input type="date" name="passport_expiry" value="<?= $user['passport_expiry'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physical Information -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-heartbeat"></i>
                    <h2>Physical Information</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid three-cols">
                        <div class="form-group">
                            <label>Height (cm)</label>
                            <input type="number" name="height_cm" value="<?= $user['height_cm'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="number" name="weight_kg" value="<?= $user['weight_kg'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Blood Type</label>
                            <select name="blood_type">
                                <option value="">Select</option>
                                <?php foreach(['A', 'B', 'AB', 'O'] as $bt): ?>
                                    <option value="<?= $bt ?>" <?= ($user['blood_type'] ?? '') === $bt ? 'selected' : '' ?>><?= $bt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Shoe Size</label>
                            <input type="text" name="shoe_size" value="<?= htmlspecialchars($user['shoe_size'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Overall Size</label>
                            <select name="overall_size">
                                <option value="">Select</option>
                                <?php foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size): ?>
                                    <option value="<?= $size ?>" <?= ($user['overall_size'] ?? '') === $size ? 'selected' : '' ?>><?= $size ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-phone-alt"></i>
                    <h2>Emergency Contact</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid three-cols">
                        <div class="form-group">
                            <label>Contact Name <span class="required">*</span></label>
                            <input type="text" name="emergency_name" value="<?= htmlspecialchars($user['emergency_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Contact Phone</label>
                            <input type="tel" name="emergency_phone" value="<?= htmlspecialchars($user['emergency_phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Relationship</label>
                            <input type="text" name="emergency_relation" value="<?= htmlspecialchars($user['emergency_relation'] ?? '') ?>" placeholder="e.g., Spouse, Parent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sea Experience -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-ship"></i>
                    <h2>Sea Experience</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Total Sea Service (months)</label>
                            <input type="number" name="total_sea_service_months" value="<?= $user['total_sea_service_months'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Vessel Name</label>
                            <input type="text" name="last_vessel_name" value="<?= htmlspecialchars($user['last_vessel_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Vessel Type</label>
                            <input type="text" name="last_vessel_type" value="<?= htmlspecialchars($user['last_vessel_type'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Rank</label>
                            <input type="text" name="last_rank" value="<?= htmlspecialchars($user['last_rank'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Last Sign Off Date</label>
                            <input type="date" name="last_sign_off" value="<?= $user['last_sign_off'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                </div>
            </div>
        </form>
    </main>
</body>
</html>
