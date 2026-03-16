<?php
/**
 * Pipeline Summary Report - PDF View (Recruitment)
 * Premium design matching ERP PDF quality
 */
$reportTitle = 'RECRUITMENT PIPELINE SUMMARY';
$reportSubtitle = 'Status Overview - All Active Applicants';
$reportDate = date('d F Y');
$totalApplicants = 0;
foreach ($pipeline as $apps) { $totalApplicants += count($apps); }

// Logo path - use recruitment's own logo
$logoPath = function_exists('asset') ? asset('images/logo.jpg') : '/PT_indoocean/PT_indoocean/recruitment/public/assets/images/logo.jpg';

// Status color presets
$statusColors = [
    'New Applied' => '#3b82f6',
    'Screening' => '#8b5cf6', 
    'Interview' => '#f59e0b',
    'Offering' => '#10b981',
    'Hired' => '#059669',
    'Rejected' => '#ef4444',
    'On Hold' => '#6b7280',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pipeline Summary - IndoOcean Recruitment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page { size: A4; margin: 12mm 15mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; font-size: 9pt; color: #1a1a2e; background: #fff; line-height: 1.5; }
        
        /* ===== PDF Container ===== */
        .pdf-container { max-width: 800px; margin: 0 auto; padding: 25px; }
        
        /* ===== Company Header ===== */
        .company-header {
            display: flex; align-items: center; gap: 18px;
            padding-bottom: 15px; margin-bottom: 0;
            border-bottom: 3px solid #1e3a5f;
        }
        .company-logo {
            width: 65px; height: 65px; flex-shrink: 0;
            border-radius: 50%; overflow: hidden;
            border: 2.5px solid #1e3a5f;
            background: #f0f4f8;
        }
        .company-logo img { width: 65px; height: 65px; object-fit: cover; display: block; }
        .company-info h1 {
            font-size: 14pt; font-weight: 800; color: #1e3a5f;
            letter-spacing: 0.3px; margin-bottom: 2px;
        }
        .company-info p { font-size: 7.5pt; color: #666; line-height: 1.4; }
        
        /* ===== Report Title Bar ===== */
        .report-title-bar {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 50%, #3182ce 100%);
            color: #fff; text-align: center;
            padding: 12px 20px; margin: 14px 0;
            border-radius: 8px;
            position: relative; overflow: hidden;
        }
        .report-title-bar::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }
        .report-title-bar h2 {
            font-size: 13pt; font-weight: 800; letter-spacing: 2.5px;
            text-transform: uppercase; position: relative; z-index: 1;
        }
        .report-title-bar .subtitle {
            font-size: 8pt; font-weight: 400; opacity: 0.85;
            margin-top: 3px; position: relative; z-index: 1;
        }

        /* ===== Meta ===== */
        .report-meta {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 8pt; color: #666; margin-bottom: 14px;
            padding: 6px 0; border-bottom: 1px dashed #ddd;
        }
        .report-meta .date { font-weight: 600; }
        .report-meta .generator { font-style: italic; color: #999; }

        /* ===== Summary Cards ===== */
        .summary-grid {
            display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap;
        }
        .summary-card {
            flex: 1; min-width: 100px;
            background: linear-gradient(135deg, #f0f4f8, #e8edf3);
            border: 1px solid #d1d9e6;
            border-radius: 10px; padding: 12px 10px;
            text-align: center; position: relative;
            overflow: hidden;
        }
        .summary-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            border-radius: 10px 10px 0 0;
        }
        .summary-card.total::before { background: linear-gradient(90deg, #2563eb, #3b82f6); }
        .summary-card .label {
            font-size: 6pt; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.6px;
            margin-bottom: 3px;
        }
        .summary-card .value {
            font-size: 20pt; font-weight: 800; color: #1e3a5f;
        }

        /* ===== Status Table Sections ===== */
        .status-section { margin-bottom: 16px; page-break-inside: avoid; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        .status-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 14px;
            font-weight: 800; font-size: 9pt; color: #fff;
            letter-spacing: 0.5px;
        }
        .status-header .status-name { display: flex; align-items: center; gap: 8px; }
        .status-header .status-icon { font-size: 12pt; }
        .status-header .count {
            background: rgba(255,255,255,0.25); padding: 3px 10px;
            border-radius: 12px; font-size: 8pt; font-weight: 700;
        }

        .report-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        .report-table thead th {
            background: #e8edf3; color: #1e3a5f;
            padding: 7px 10px; text-align: left;
            font-size: 7pt; font-weight: 700;
            border: 1px solid #ccd5e0;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .report-table tbody td {
            padding: 6px 10px; border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .report-table tbody tr:nth-child(even) { background: #f8fafc; }
        .report-table tbody tr:hover { background: #edf2f7; }
        .report-table .num { text-align: center; color: #94a3b8; font-weight: 600; width: 30px; }
        .report-table .name { font-weight: 700; color: #1e3a5f; }
        .report-table .vacancy { color: #475569; }
        .report-table .handler { color: #64748b; font-size: 7.5pt; }
        .report-table .date { font-size: 7.5pt; color: #94a3b8; text-align: center; }
        .report-table .email { font-size: 7pt; color: #94a3b8; }

        /* ===== Notes Section ===== */
        .notes-box {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 10px 14px; margin-top: 15px; font-size: 7.5pt; color: #64748b;
        }
        .notes-box strong { color: #1e3a5f; }

        /* ===== Footer ===== */
        .report-footer {
            margin-top: 20px; padding-top: 10px;
            border-top: 2px solid #1e3a5f;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 7pt; color: #94a3b8;
        }
        .report-footer .confidential { font-weight: 700; color: #1e3a5f; font-size: 7.5pt; }
        
        /* ===== Print ===== */
        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .pdf-container { padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
            .report-table thead th { background: #e8edf3 !important; }
            .summary-card { background: #f0f4f8 !important; }
            .report-title-bar { background: #1e3a5f !important; }
            .status-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @media screen {
            body { background: linear-gradient(135deg, #e2e8f0, #cbd5e1); padding: 20px; }
            .pdf-container {
                background: #fff; box-shadow: 0 8px 40px rgba(0,0,0,0.15);
                border-radius: 12px; padding: 40px;
            }
        }
    </style>
</head>
<body>
<div class="pdf-container">
    <!-- Company Header -->
    <div class="company-header">
        <div class="company-logo">
            <img src="<?= $logoPath ?>" alt="IndoOcean Logo" onerror="this.parentElement.innerHTML='<div style=\'width:65px;height:65px;background:linear-gradient(135deg,#1e3a5f,#2c5282);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:16pt;border-radius:50%\'>IO</div>'">
        </div>
        <div class="company-info">
            <h1>PT. INDO OCEAN CREW SERVICES</h1>
            <p>Menara Cakrawala Lt. 15 No. 1506, Jl. M.H. Thamrin No. 9</p>
            <p>Kb. Sirih, Kec. Menteng, Jakarta Pusat, DKI Jakarta 10340</p>
            <p>📞 +62 21 39700825 &nbsp;|&nbsp; 📧 info@indooceancrew.com</p>
        </div>
    </div>
    
    <!-- Report Title -->
    <div class="report-title-bar">
        <h2><?= $reportTitle ?></h2>
        <div class="subtitle"><?= $reportSubtitle ?></div>
    </div>
    
    <!-- Meta -->
    <div class="report-meta">
        <span class="date">📅 <?= $reportDate ?></span>
        <span class="generator">Generated by IndoOcean Recruitment System</span>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card total">
            <div class="label">Total Applicants</div>
            <div class="value" style="color:#2563eb"><?= $totalApplicants ?></div>
        </div>
        <?php 
        $statusIcons = ['🆕', '🔍', '💬', '📋', '✅', '❌', '⏸️', '📝', '🎯', '📞'];
        $iconIndex = 0;
        foreach ($statuses as $s): 
            $count = count($pipeline[$s['id']] ?? []);
            $icon = $statusIcons[$iconIndex % count($statusIcons)] ?? '📊';
            $iconIndex++;
        ?>
        <div class="summary-card" style="border-top: 3px solid <?= $s['color'] ?? '#475569' ?>">
            <div class="label"><?= htmlspecialchars($s['name']) ?></div>
            <div class="value" style="font-size:16pt; color:<?= $s['color'] ?? '#475569' ?>"><?= $count ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Per-Status Tables -->
    <?php foreach ($statuses as $status): 
        $apps = $pipeline[$status['id']] ?? [];
        if (empty($apps)) continue;
        $color = $status['color'] ?? '#475569';
    ?>
    <div class="status-section">
        <div class="status-header" style="background: linear-gradient(135deg, <?= $color ?>, <?= $color ?>dd);">
            <span class="status-name">
                <?= htmlspecialchars($status['name']) ?>
            </span>
            <span class="count"><?= count($apps) ?> applicant<?= count($apps) > 1 ? 's' : '' ?></span>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width:30px; text-align:center">No</th>
                    <th>Applicant Name</th>
                    <th>Email</th>
                    <th>Vacancy / Position</th>
                    <th>Handler</th>
                    <th style="text-align:center">Applied Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apps as $i => $app): ?>
                <tr>
                    <td class="num"><?= $i + 1 ?></td>
                    <td class="name"><?= htmlspecialchars($app['applicant_name'] ?? '-') ?></td>
                    <td class="email"><?= htmlspecialchars($app['applicant_email'] ?? '-') ?></td>
                    <td class="vacancy"><?= htmlspecialchars($app['vacancy_title'] ?? '-') ?></td>
                    <td class="handler"><?= htmlspecialchars($app['crewing_name'] ?? 'Unassigned') ?></td>
                    <td class="date"><?= !empty($app['created_at']) ? date('d/m/Y', strtotime($app['created_at'])) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <?php if ($totalApplicants === 0): ?>
    <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
        <div style="font-size:28pt; margin-bottom:10px;">📭</div>
        <p style="font-size:10pt; font-weight:600;">No Active Applicants</p>
        <p style="font-size:8pt;">There are currently no applicants in the pipeline.</p>
    </div>
    <?php endif; ?>

    <!-- Notes -->
    <div class="notes-box">
        <strong>Notes:</strong> This recruitment pipeline summary includes all active applicants across all pipeline stages. 
        Data is current as of <?= date('d F Y H:i') ?> WIB. This report is confidential and intended for internal use only.
    </div>

    <!-- Footer -->
    <div class="report-footer">
        <span class="confidential">🔒 CONFIDENTIAL — PT. Indo Ocean Crew Services</span>
        <span>Printed: <?= date('d/m/Y H:i') ?> | Page 1</span>
    </div>
</div>

<!-- Print Button -->
<div class="no-print" style="text-align:center; margin:25px auto; max-width:800px; display:flex; justify-content:center; gap:12px;">
    <button onclick="window.print()" style="padding:14px 45px; background:linear-gradient(135deg,#1e3a5f,#2c5282); color:#fff; border:none; border-radius:12px; font-weight:700; cursor:pointer; font-size:14px; font-family:'Inter',sans-serif; box-shadow:0 4px 15px rgba(30,58,95,0.3); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(30,58,95,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(30,58,95,0.3)'">
        🖨️ Print / Save as PDF
    </button>
    <button onclick="window.history.back()" style="padding:14px 35px; background:#fff; color:#475569; border:2px solid #cbd5e1; border-radius:12px; font-weight:600; cursor:pointer; font-size:14px; font-family:'Inter',sans-serif; transition:all 0.2s;" onmouseover="this.style.borderColor='#1e3a5f';this.style.color='#1e3a5f'" onmouseout="this.style.borderColor='#cbd5e1';this.style.color='#475569'">
        ← Back
    </button>
</div>
</body>
</html>
