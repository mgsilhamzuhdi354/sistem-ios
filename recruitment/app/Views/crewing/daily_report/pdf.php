<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crewing Daily Report - <?= date('d M Y', strtotime($dateFrom)) ?> to <?= date('d M Y', strtotime($dateTo)) ?></title>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { size: landscape; margin: 15mm; }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
            color: #333;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #0d9488;
        }
        .header h1 {
            color: #0d9488;
            font-size: 22px;
            margin-bottom: 8px;
        }
        .header .subtitle {
            color: #666;
            font-size: 13px;
        }
        .info-bar {
            background: #f8fafc;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .info-item { font-size: 12px; }
        .info-label { color: #666; font-weight: 600; }
        .info-value { color: #1e3a5f; font-weight: 700; }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #0d9488;
            text-align: center;
        }
        .stat-box .stat-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .stat-box .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1e3a5f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead tr { background: #0d9488; color: white; }
        th {
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .status-pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
            color: white;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        .print-date { margin-top: 8px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CREWING DAILY REPORT</h1>
        <div class="subtitle">PT Indo Ocean Crew Services</div>
    </div>

    <div class="info-bar">
        <div class="info-item">
            <span class="info-label">Crewing Officer:</span>
            <span class="info-value"><?= htmlspecialchars($crewingName) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Period:</span>
            <span class="info-value">
                <?= date('d M Y', strtotime($dateFrom)) ?> — <?= date('d M Y', strtotime($dateTo)) ?>
            </span>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-label">Total Applicants</div>
            <div class="stat-value"><?= $stats['total'] ?></div>
        </div>
        <div class="stat-box" style="border-left-color: #f59e0b;">
            <div class="stat-label">Pending</div>
            <div class="stat-value"><?= $stats['pending'] ?></div>
        </div>
        <div class="stat-box" style="border-left-color: #10b981;">
            <div class="stat-label">Approved</div>
            <div class="stat-value"><?= $stats['approved'] ?></div>
        </div>
        <div class="stat-box" style="border-left-color: #8b5cf6;">
            <div class="stat-label">Sent to ERP</div>
            <div class="stat-value"><?= $stats['sent_to_erp'] ?></div>
        </div>
    </div>

    <?php if (empty($applications)): ?>
        <div style="text-align: center; padding: 40px; color: #9ca3af;">
            <p>No applicant data found for this period</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Applicant Name</th>
                    <th>Rank</th>
                    <th>Vessel Type</th>
                    <th>Department</th>
                    <th>Handler</th>
                    <th>Status</th>
                    <th style="width: 100px;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($applications as $app): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($app['applicant_name'] ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($app['last_rank'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($app['vessel_type'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($app['department_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($app['handler_name'] ?? '-') ?></td>
                        <td>
                            <span class="status-pill" style="background: <?= $app['status_color'] ?? '#6c757d' ?>;">
                                <?= htmlspecialchars($app['status_name'] ?? '-') ?>
                            </span>
                        </td>
                        <td><?= date('d M Y H:i', strtotime($app['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        <div>&copy; <?= date('Y') ?> PT Indo Ocean Crew Services — Recruitment System</div>
        <div class="print-date">Printed on: <?= date('d M Y, H:i:s') ?></div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
