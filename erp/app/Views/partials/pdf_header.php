<?php
/**
 * Shared PDF Header Partial
 * Usage: include this partial in all PDF views
 * Variables: $reportTitle (required), $reportSubtitle (optional), $reportDate (optional)
 */
$reportDate = $reportDate ?? date('d F Y');
$reportSubtitle = $reportSubtitle ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($reportTitle ?? 'Report') ?> - IndoOcean ERP</title>
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
            border: 2px solid #1e3a5f;
        }
        .company-logo img { width: 65px; height: 65px; object-fit: cover; }
        .company-info h1 {
            font-size: 14pt; font-weight: 800; color: #1e3a5f;
            letter-spacing: 0.3px; margin-bottom: 2px;
        }
        .company-info p { font-size: 7.5pt; color: #666; line-height: 1.4; }
        
        /* ===== Report Title Bar ===== */
        .report-title-bar {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 50%, #3182ce 100%);
            color: #fff; text-align: center;
            padding: 10px 20px; margin: 12px 0;
            border-radius: 6px;
            position: relative; overflow: hidden;
        }
        .report-title-bar::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }
        .report-title-bar h2 {
            font-size: 13pt; font-weight: 800; letter-spacing: 2px;
            text-transform: uppercase; position: relative; z-index: 1;
        }
        .report-title-bar .subtitle {
            font-size: 8pt; font-weight: 400; opacity: 0.85;
            margin-top: 2px; position: relative; z-index: 1;
        }
        
        /* ===== Meta Info ===== */
        .report-meta {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 8pt; color: #666; margin-bottom: 12px;
            padding: 6px 0; border-bottom: 1px dashed #ddd;
        }
        .report-meta .date { font-weight: 600; }
        .report-meta .generator { font-style: italic; color: #999; }
        
        /* ===== Common Table Styles ===== */
        .report-table {
            width: 100%; border-collapse: collapse; margin-bottom: 15px;
            font-size: 8.5pt;
        }
        .report-table thead th {
            background: #e8edf3; color: #1e3a5f;
            padding: 7px 10px; text-align: left;
            font-size: 7.5pt; font-weight: 700;
            border: 1px solid #ccd5e0;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .report-table tbody td {
            padding: 5px 10px; border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .report-table tbody tr:nth-child(even) { background: #f8fafc; }
        .report-table tbody tr:hover { background: #edf2f7; }
        .report-table .text-right { text-align: right; }
        .report-table .text-center { text-align: center; }
        .report-table .font-bold { font-weight: 700; }
        .report-table .text-sm { font-size: 8pt; }
        
        /* ===== Summary Cards ===== */
        .summary-grid {
            display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap;
        }
        .summary-card {
            flex: 1; min-width: 120px;
            background: linear-gradient(135deg, #f0f4f8, #e8edf3);
            border: 1px solid #d1d9e6;
            border-radius: 8px; padding: 12px 14px;
            text-align: center;
        }
        .summary-card .label {
            font-size: 7pt; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.8px;
        }
        .summary-card .value {
            font-size: 18pt; font-weight: 800; color: #1e3a5f;
            margin-top: 2px;
        }
        .summary-card .value.text-green { color: #16a34a; }
        .summary-card .value.text-red { color: #dc2626; }
        .summary-card .value.text-amber { color: #d97706; }
        .summary-card .value.text-blue { color: #2563eb; }
        
        /* ===== Status Badges ===== */
        .badge {
            display: inline-block; padding: 2px 8px;
            border-radius: 10px; font-size: 7pt;
            font-weight: 700; text-transform: uppercase;
        }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f1f5f9; color: #475569; }
        
        /* ===== Footer ===== */
        .report-footer {
            margin-top: 20px; padding-top: 10px;
            border-top: 2px solid #1e3a5f;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 7pt; color: #94a3b8;
        }
        .report-footer .confidential {
            font-weight: 700; color: #1e3a5f; font-size: 7.5pt;
        }
        
        /* ===== Print Styles ===== */
        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .pdf-container { padding: 0; max-width: 100%; }
            .no-print { display: none !important; }
            .report-table thead th { background: #e8edf3 !important; }
            .summary-card { background: #f0f4f8 !important; }
            .report-title-bar { background: #1e3a5f !important; }
        }
        @media screen {
            body { background: #e2e8f0; padding: 20px; }
            .pdf-container {
                background: #fff; box-shadow: 0 4px 25px rgba(0,0,0,0.12);
                border-radius: 10px; padding: 35px;
            }
        }
    </style>
</head>
<body>
<div class="pdf-container">
    <!-- Company Header -->
    <div class="company-header">
        <div class="company-logo">
            <img src="<?= defined('BASE_URL') ? BASE_URL . 'assets/images/logo.png' : 'assets/images/logo.png' ?>" alt="Logo">
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
        <h2><?= htmlspecialchars($reportTitle ?? 'REPORT') ?></h2>
        <?php if ($reportSubtitle): ?>
        <div class="subtitle"><?= htmlspecialchars($reportSubtitle) ?></div>
        <?php endif; ?>
    </div>
    
    <!-- Meta -->
    <div class="report-meta">
        <span class="date">📅 <?= $reportDate ?></span>
        <span class="generator">Generated by IndoOcean ERP System</span>
    </div>
