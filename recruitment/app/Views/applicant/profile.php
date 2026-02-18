<?php
/**
 * Applicant Profile View - Matched to applicant layout
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - PT Indo Ocean Crew Services</title>
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

        /* Profile Completion */
        .completion-bar {
            background: white;
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
        }
        .completion-bar h3 { font-size: 0.95rem; margin-bottom: 0.75rem; color: #1a1a2e; display: flex; align-items: center; gap: 8px; }
        .completion-bar h3 i { color: #0A2463; }
        .progress-container { display: flex; align-items: center; gap: 1rem; }
        .progress-bar { flex: 1; height: 10px; background: #e9ecef; border-radius: 6px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #00A8E8, #10B981); border-radius: 6px; transition: width 0.5s ease; }
        .progress-text { font-weight: 600; color: #0A2463; min-width: 45px; font-size: 0.9rem; }

        /* Form Cards */
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            margin-bottom: 1.25rem;
            overflow: hidden;
        }
        .form-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: #fafbfc;
        }
        .form-card-header i { color: #0A2463; font-size: 1.1rem; }
        .form-card-header h2 { font-size: 1rem; font-weight: 600; color: #1a1a2e; margin: 0; }
        .form-card-body { padding: 1.5rem; }

        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        .form-grid.three-cols { grid-template-columns: repeat(3, 1fr); }
        .form-group.full-width { grid-column: span 2; }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .form-group label .required { color: #EF4444; }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.2s;
            background: #fff;
            color: #1a1a2e;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0A2463;
            box-shadow: 0 0 0 3px rgba(10,36,99,0.08);
        }
        .form-group input:disabled {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }
        .form-group textarea { min-height: 90px; resize: vertical; }

        /* Save Button */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #f0f0f0;
        }
        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.75rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: linear-gradient(135deg, #0A2463, #1E5AA8);
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(10,36,99,0.25); }

        /* Responsive */
        @media (max-width: 1024px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-grid.three-cols { grid-template-columns: 1fr 1fr; }
            .form-group.full-width { grid-column: span 1; }
        }
        @media (max-width: 768px) {
            .form-grid.three-cols { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="applicant-body">
    <!-- Sidebar - same as other applicant pages -->
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
                    <a href="<?= url('/applicant/applications') ?>" class="nav-link">
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
                <li>
                    <a href="<?= url('/applicant/profile') ?>" class="nav-link active">
                        <i class="fas fa-user"></i>
                        <span data-translate="nav.profile">Profile</span>
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
        <header class="page-header-bar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
            <h1 style="font-size:22px;color:#1a1a2e;display:flex;align-items:center;gap:12px;">
                <i class="fas fa-user" style="color:#0A2463;"></i>
                <span data-translate="profile.title">My Profile</span>
            </h1>
            <div class="language-selector">
                <select id="langSelect">
                    <option value="en">🇺🇸 EN</option>
                    <option value="id">🇮🇩 ID</option>
                    <option value="zh">🇨🇳 中文</option>
                </select>
            </div>
        </header>

        <?php if ($flash = flash('success')): ?>
            <div class="alert alert-success" style="padding:12px 18px;border-radius:10px;margin-bottom:20px;background:#d4edda;color:#155724;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-check-circle"></i> <?= $flash ?>
            </div>
        <?php endif; ?>

        <?php if ($flash = flash('error')): ?>
            <div class="alert alert-danger" style="padding:12px 18px;border-radius:10px;margin-bottom:20px;background:#f8d7da;color:#721c24;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-exclamation-circle"></i> <?= $flash ?>
            </div>
        <?php endif; ?>

        <!-- Profile Completion -->
        <div class="completion-bar">
            <h3><i class="fas fa-chart-line"></i> <span data-translate="profile.completion">Profile Completion</span></h3>
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $user['profile_completion'] ?? 0 ?>%"></div>
                </div>
                <span class="progress-text"><?= $user['profile_completion'] ?? 0 ?>%</span>
            </div>
        </div>

        <form action="<?= url('/applicant/profile/update') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Personal Information -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-user"></i>
                    <h2 data-translate="profile.personalInfo">Personal Information</h2>
                </div>
                <div class="form-card-body">
                    <!-- Profile Photo Upload -->
                    <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:2rem;padding-bottom:2rem;border-bottom:1px solid #f0f0f0;">
                        <div style="position:relative;width:150px;height:150px;margin-bottom:1rem;">
                            <img id="photoPreview" 
                                 src="<?= !empty($user['profile_photo']) ? '/recruitment/public/uploads/profiles/' . $user['profile_photo'] : '/recruitment/public/images/default-avatar.png' ?>" 
                                 alt="Profile Photo" 
                                 style="width:100%;height:100%;border-radius:50%;object-fit:cover;border:4px solid #f0f0f0;box-shadow:0 4px 12px rgba(0,0,0,0.1);"
                                 onerror="this.src='/recruitment/public/images/default-avatar.png'; this.onerror=null;">
                            <label for="profilePhoto" style="position:absolute;bottom:5px;right:5px;width:40px;height:40px;background:linear-gradient(135deg,#0A2463,#1E5AA8);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.2);transition:transform 0.2s;">
                                <i class="fas fa-camera" style="color:white;font-size:16px;"></i>
                            </label>
                        </div>
                        <input type="file" id="profilePhoto" name="profile_photo" accept="image/jpeg,image/jpg,image/png" style="display:none;">
                        <p style="font-size:0.85rem;color:#64748b;margin:0;text-align:center;">
                            <strong>Upload Photo</strong><br>
                            <small>JPG, PNG (max 2MB)</small>
                        </p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label><span data-translate="profile.fullName">Full Name</span> <span class="required">*</span></label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.email">Email</label>
                            <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.phone">Phone Number</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><span data-translate="profile.dob">Date of Birth</span> <span class="required">*</span></label>
                            <input type="date" name="date_of_birth" value="<?= $user['date_of_birth'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label><span data-translate="profile.gender">Gender</span> <span class="required">*</span></label>
                            <select name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><span data-translate="profile.nationality">Nationality</span> <span class="required">*</span></label>
                            <input type="text" name="nationality" value="<?= htmlspecialchars($user['nationality'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.pob">Place of Birth</label>
                            <input type="text" name="place_of_birth" value="<?= htmlspecialchars($user['place_of_birth'] ?? '') ?>">
                        </div>
                        <div class="form-group full-width">
                            <label data-translate="profile.address">Address</label>
                            <textarea name="address"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.city">City</label>
                            <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.country">Country</label>
                            <input type="text" name="country" value="<?= htmlspecialchars($user['country'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.postalCode">Postal Code</label>
                            <input type="text" name="postal_code" value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seafarer Information -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-ship"></i>
                    <h2 data-translate="profile.seafarerInfo">Seafarer Information</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label><span data-translate="profile.seamanBook">Seaman Book Number</span> <span class="required">*</span></label>
                            <input type="text" name="seaman_book_no" value="<?= htmlspecialchars($user['seaman_book_no'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.seamanExpiry">Seaman Book Expiry</label>
                            <input type="date" name="seaman_book_expiry" value="<?= $user['seaman_book_expiry'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label><span data-translate="profile.passport">Passport Number</span> <span class="required">*</span></label>
                            <input type="text" name="passport_no" value="<?= htmlspecialchars($user['passport_no'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.passportExpiry">Passport Expiry</label>
                            <input type="date" name="passport_expiry" value="<?= $user['passport_expiry'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Physical Information -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-heartbeat"></i>
                    <h2 data-translate="profile.physicalInfo">Physical Information</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid three-cols">
                        <div class="form-group">
                            <label data-translate="profile.height">Height (cm)</label>
                            <input type="number" name="height_cm" value="<?= $user['height_cm'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.weight">Weight (kg)</label>
                            <input type="number" name="weight_kg" value="<?= $user['weight_kg'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.bloodType">Blood Type</label>
                            <select name="blood_type">
                                <option value="">Select</option>
                                <?php foreach(['A', 'B', 'AB', 'O'] as $bt): ?>
                                    <option value="<?= $bt ?>" <?= ($user['blood_type'] ?? '') === $bt ? 'selected' : '' ?>><?= $bt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.shoeSize">Shoe Size</label>
                            <input type="text" name="shoe_size" value="<?= htmlspecialchars($user['shoe_size'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.overallSize">Overall Size</label>
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
                    <h2 data-translate="profile.emergencyContact">Emergency Contact</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid three-cols">
                        <div class="form-group">
                            <label><span data-translate="profile.contactName">Contact Name</span> <span class="required">*</span></label>
                            <input type="text" name="emergency_name" value="<?= htmlspecialchars($user['emergency_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.contactPhone">Contact Phone</label>
                            <input type="tel" name="emergency_phone" value="<?= htmlspecialchars($user['emergency_phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.relationship">Relationship</label>
                            <input type="text" name="emergency_relation" value="<?= htmlspecialchars($user['emergency_relation'] ?? '') ?>" placeholder="e.g., Spouse, Parent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sea Experience -->
            <div class="form-card">
                <div class="form-card-header">
                    <i class="fas fa-anchor"></i>
                    <h2 data-translate="profile.seaExperience">Sea Experience</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label data-translate="profile.totalService">Total Sea Service (months)</label>
                            <input type="number" name="total_sea_service_months" value="<?= $user['total_sea_service_months'] ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.lastVessel">Last Vessel Name</label>
                            <input type="text" name="last_vessel_name" value="<?= htmlspecialchars($user['last_vessel_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.lastVesselType">Last Vessel Type</label>
                            <input type="text" name="last_vessel_type" value="<?= htmlspecialchars($user['last_vessel_type'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.lastRank">Last Rank</label>
                            <input type="text" name="last_rank" value="<?= htmlspecialchars($user['last_rank'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label data-translate="profile.lastSignOff">Last Sign Off Date</label>
                            <input type="date" name="last_sign_off" value="<?= $user['last_sign_off'] ?? '' ?>">
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> <span data-translate="profile.save">Save Profile</span>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <script src="<?= asset('js/translate-recruitment.js') ?>"></script>
    <script>
    // Profile photo preview
    document.getElementById('profilePhoto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG or PNG)');
                e.target.value = '';
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                e.target.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('photoPreview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>
