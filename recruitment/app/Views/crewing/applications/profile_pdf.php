<?php
/**
 * Applicant Profile PDF (CV Style) - Recruitment
 * Print-friendly CV/profile for sending to clients
 */
$reportTitle = 'APPLICANT PROFILE';
$reportSubtitle = htmlspecialchars($applicant['full_name'] ?? 'Applicant');
$reportDate = date('d F Y');

// Use recruitment logo path
$logoPath = function_exists('asset') ? asset('images/logo.jpg') : '/PT_indoocean/PT_indoocean/recruitment/public/assets/images/logo.jpg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CV - <?= htmlspecialchars($applicant['full_name'] ?? 'Applicant') ?> - IndoOcean</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 12mm 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; font-size: 9pt; color: #1a1a2e; background: #fff; line-height: 1.5; }
        .pdf-container { max-width: 800px; margin: 0 auto; padding: 25px; }
        
        .company-header { display: flex; align-items: center; gap: 18px; padding-bottom: 12px; border-bottom: 3px solid #1e3a5f; margin-bottom: 0; }
        .company-logo { width: 55px; height: 55px; flex-shrink: 0; border-radius: 50%; overflow: hidden; border: 2px solid #1e3a5f; }
        .company-logo img { width: 55px; height: 55px; object-fit: cover; }
        .company-info h1 { font-size: 12pt; font-weight: 800; color: #1e3a5f; }
        .company-info p { font-size: 7pt; color: #666; }
        
        .profile-header { display: flex; gap: 20px; margin: 15px 0; padding: 15px; background: linear-gradient(135deg, #f0f4f8, #e8edf3); border-radius: 8px; border: 1px solid #d1d9e6; }
        .profile-avatar { width: 90px; height: 90px; border-radius: 50%; border: 3px solid #1e3a5f; overflow: hidden; flex-shrink: 0; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 28pt; color: #1e3a5f; font-weight: 800; }
        .profile-avatar img { width: 90px; height: 90px; object-fit: cover; }
        .profile-main h2 { font-size: 16pt; font-weight: 800; color: #1e3a5f; }
        .profile-main .rank { font-size: 10pt; color: #2563eb; font-weight: 700; margin-top: 2px; }
        .profile-main .contact { font-size: 8pt; color: #475569; margin-top: 6px; line-height: 1.8; }
        
        .section { margin-bottom: 15px; }
        .section-title { font-size: 10pt; font-weight: 800; color: #1e3a5f; border-bottom: 2px solid #2c5282; padding-bottom: 4px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        
        .info-grid { display: flex; flex-wrap: wrap; gap: 0; }
        .info-item { width: 50%; padding: 5px 10px; font-size: 8.5pt; display: flex; }
        .info-item .label { width: 130px; font-weight: 700; color: #475569; flex-shrink: 0; }
        .info-item .value { flex: 1; color: #1a1a2e; }
        
        .doc-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        .doc-table th { background: #e8edf3; color: #1e3a5f; padding: 6px 10px; text-align: left; font-size: 7.5pt; font-weight: 700; border: 1px solid #ccd5e0; text-transform: uppercase; }
        .doc-table td { padding: 4px 10px; border: 1px solid #e2e8f0; }
        .doc-table tr:nth-child(even) { background: #f8fafc; }
        
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 7pt; font-weight: 700; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        
        .report-footer { margin-top: 20px; padding-top: 10px; border-top: 2px solid #1e3a5f; display: flex; justify-content: space-between; font-size: 7pt; color: #94a3b8; }
        
        @media print { body { background: #fff; } .pdf-container { padding: 0; } .no-print { display: none !important; } }
        @media screen { body { background: #e2e8f0; padding: 20px; } .pdf-container { background: #fff; box-shadow: 0 4px 25px rgba(0,0,0,0.12); border-radius: 10px; padding: 35px; } }
    </style>
</head>
<body>
<div class="pdf-container">
    <!-- Header -->
    <div class="company-header">
        <div class="company-logo">
            <img src="<?= $logoPath ?>" alt="Logo">
        </div>
        <div class="company-info">
            <h1>PT. INDO OCEAN CREW SERVICES</h1>
            <p>Menara Cakrawala Lt. 15 No. 1506, Jl. M.H. Thamrin No. 9, Jakarta Pusat</p>
        </div>
    </div>
    
    <div style="background:linear-gradient(135deg,#1e3a5f,#2c5282,#3182ce); color:#fff; text-align:center; padding:8px; margin:10px 0; border-radius:6px;">
        <h2 style="font-size:12pt; font-weight:800; letter-spacing:2px;">CURRICULUM VITAE</h2>
    </div>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if (!empty($applicant['avatar'])): ?>
                <img src="<?= $applicant['avatar'] ?>" alt="Photo">
            <?php else: ?>
                <?= strtoupper(substr($applicant['full_name'] ?? 'A', 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div class="profile-main">
            <h2><?= htmlspecialchars(strtoupper($applicant['full_name'] ?? '-')) ?></h2>
            <div class="rank">🎖️ <?= htmlspecialchars($applicant['last_rank'] ?? $applicant['applied_rank'] ?? 'N/A') ?></div>
            <div class="contact">
                📧 <?= htmlspecialchars($applicant['email'] ?? '-') ?><br>
                📞 <?= htmlspecialchars($applicant['phone'] ?? '-') ?>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="section">
        <div class="section-title">👤 Personal Information</div>
        <div class="info-grid">
            <div class="info-item"><span class="label">Full Name</span><span class="value"><?= htmlspecialchars($applicant['full_name'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Date of Birth</span><span class="value"><?= !empty($applicant['date_of_birth']) ? date('d F Y', strtotime($applicant['date_of_birth'])) : '-' ?></span></div>
            <div class="info-item"><span class="label">Place of Birth</span><span class="value"><?= htmlspecialchars($applicant['place_of_birth'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Gender</span><span class="value"><?= ucfirst($applicant['gender'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Nationality</span><span class="value"><?= htmlspecialchars($applicant['nationality'] ?? 'Indonesian') ?></span></div>
            <div class="info-item"><span class="label">Blood Type</span><span class="value"><?= htmlspecialchars($applicant['blood_type'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Height / Weight</span><span class="value"><?= ($applicant['height_cm'] ?? '-') ?> cm / <?= ($applicant['weight_kg'] ?? '-') ?> kg</span></div>
            <div class="info-item"><span class="label">Address</span><span class="value"><?= htmlspecialchars($applicant['address'] ?? '-') ?>, <?= htmlspecialchars($applicant['city'] ?? '') ?></span></div>
        </div>
    </div>

    <!-- Sea Service Experience -->
    <div class="section">
        <div class="section-title">⚓ Sea Service Experience</div>
        <div class="info-grid">
            <div class="info-item"><span class="label">Last Rank</span><span class="value font-bold"><?= htmlspecialchars($applicant['last_rank'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Last Vessel</span><span class="value"><?= htmlspecialchars($applicant['last_vessel_name'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Vessel Type</span><span class="value"><?= htmlspecialchars($applicant['last_vessel_type'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Total Sea Service</span><span class="value"><?= ($applicant['total_sea_service_months'] ?? 0) ?> months</span></div>
        </div>
    </div>

    <!-- Documents / Certificates -->
    <div class="section">
        <div class="section-title">📄 Documents & Certificates</div>
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Document Type</th>
                    <th>Document Number</th>
                    <th>Expiry Date</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Seaman Book</td>
                    <td style="font-family:monospace"><?= htmlspecialchars($applicant['seaman_book_no'] ?? '-') ?></td>
                    <td>-</td>
                    <td class="text-center"><?= !empty($applicant['seaman_book_no']) ? '<span class="badge badge-green">Available</span>' : '<span class="badge badge-red">Missing</span>' ?></td>
                </tr>
                <tr>
                    <td>Passport</td>
                    <td style="font-family:monospace"><?= htmlspecialchars($applicant['passport_no'] ?? '-') ?></td>
                    <td>-</td>
                    <td class="text-center"><?= !empty($applicant['passport_no']) ? '<span class="badge badge-green">Available</span>' : '<span class="badge badge-red">Missing</span>' ?></td>
                </tr>
                <?php if (!empty($documents)): ?>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= htmlspecialchars($doc['type_name'] ?? '-') ?></td>
                        <td style="font-family:monospace"><?= htmlspecialchars($doc['document_number'] ?? '-') ?></td>
                        <td><?= !empty($doc['expiry_date']) ? date('d/m/Y', strtotime($doc['expiry_date'])) : '-' ?></td>
                        <td class="text-center">
                            <?php
                            $expired = !empty($doc['expiry_date']) && strtotime($doc['expiry_date']) < time();
                            echo $expired ? '<span class="badge badge-red">Expired</span>' : '<span class="badge badge-green">Valid</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Emergency Contact -->
    <div class="section">
        <div class="section-title">🚨 Emergency Contact</div>
        <div class="info-grid">
            <div class="info-item"><span class="label">Name</span><span class="value"><?= htmlspecialchars($applicant['emergency_name'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Phone</span><span class="value"><?= htmlspecialchars($applicant['emergency_phone'] ?? '-') ?></span></div>
            <div class="info-item"><span class="label">Relationship</span><span class="value"><?= htmlspecialchars($applicant['emergency_relation'] ?? '-') ?></span></div>
        </div>
    </div>

    <!-- Interview Score (if available) -->
    <?php if (!empty($interviewScore)): ?>
    <div class="section">
        <div class="section-title">🤖 AI Interview Score</div>
        <div style="display:flex; gap:10px;">
            <div style="flex:1; padding:10px; background:#f0f4f8; border-radius:6px; text-align:center;">
                <div style="font-size:7pt; color:#94a3b8; text-transform:uppercase;">Total Score</div>
                <div style="font-size:18pt; font-weight:800; color:#1e3a5f;"><?= number_format($interviewScore['total_score'] ?? 0, 1) ?>%</div>
            </div>
            <div style="flex:1; padding:10px; background:#f0f4f8; border-radius:6px; text-align:center;">
                <div style="font-size:7pt; color:#94a3b8; text-transform:uppercase;">Recommendation</div>
                <div style="font-size:10pt; font-weight:700; color:#2563eb; margin-top:5px;"><?= htmlspecialchars($interviewScore['ai_recommendation'] ?? '-') ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="report-footer">
        <span style="font-weight:700; color:#1e3a5f;">🔒 CONFIDENTIAL — PT. Indo Ocean Crew Services</span>
        <span>Generated: <?= date('d/m/Y H:i') ?></span>
    </div>
</div>

<!-- Print Button -->
<div class="no-print" style="text-align:center; margin:20px auto; max-width:800px;">
    <button onclick="window.print()" style="padding:12px 40px; background:linear-gradient(135deg,#1e3a5f,#2c5282); color:#fff; border:none; border-radius:10px; font-weight:700; cursor:pointer; font-size:14px; font-family:'Inter',sans-serif;">
        🖨️ Print / Save as PDF
    </button>
    <button onclick="window.history.back()" style="padding:12px 40px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:10px; font-weight:600; cursor:pointer; font-size:14px; font-family:'Inter',sans-serif; margin-left:10px;">
        ← Back
    </button>
</div>
</body>
</html>
