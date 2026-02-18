<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekrutmen - <?= date('d F Y', strtotime($dateFrom)) ?><?= $dateFrom !== $dateTo ? ' s/d ' . date('d F Y', strtotime($dateTo)) : '' ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            color: #333; 
            font-size: 11px;
            line-height: 1.4;
            padding: 15px;
        }
        
        /* Header */
        .pdf-header {
            text-align: center;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .pdf-header h1 {
            font-size: 18px;
            color: #1e3a5f;
            margin-bottom: 3px;
        }
        .pdf-header h2 {
            font-size: 14px;
            color: #dc2626;
            font-weight: 600;
            margin-bottom: 3px;
        }
        .pdf-header .date-range {
            font-size: 11px;
            color: #666;
        }
        
        /* Stats Summary */
        .pdf-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .pdf-stat {
            text-align: center;
        }
        .pdf-stat .value {
            font-size: 20px;
            font-weight: 700;
            color: #1e3a5f;
        }
        .pdf-stat .label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        thead th {
            background: #1e3a5f;
            color: white;
            padding: 8px 6px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
            font-weight: 600;
        }
        tbody td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        tbody tr:hover {
            background: #eff6ff;
        }
        
        /* Status Tags */
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }
        .tag-sent { background: #dcfce7; color: #166534; }
        .tag-not-sent { background: #fef9c3; color: #854d0e; }
        .tag-rejected { background: #fee2e2; color: #991b1b; }
        
        /* Footer */
        .pdf-footer {
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        /* Print specific */
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { 
                size: A4 landscape; 
                margin: 10mm; 
            }
        }
        
        .print-actions {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
        }
        .print-actions button {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            margin: 0 5px;
        }
        .btn-pdf {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }
        .btn-back {
            background: #f1f5f9;
            color: #475569;
        }
    </style>
</head>
<body>
    <!-- Action Buttons (hidden in print) -->
    <div class="print-actions no-print">
        <button class="btn-pdf" onclick="window.print()">
            🖨️ Print / Save as PDF
        </button>
        <button class="btn-back" onclick="history.back()">
            ← Kembali
        </button>
    </div>
    
    <!-- Report Header -->
    <div class="pdf-header">
        <h1>PT INDO OCEAN CREW SERVICES</h1>
        <h2>LAPORAN HARIAN REKRUTMEN</h2>
        <div class="date-range">
            Periode: <?= date('d F Y', strtotime($dateFrom)) ?>
            <?= $dateFrom !== $dateTo ? ' s/d ' . date('d F Y', strtotime($dateTo)) : '' ?>
            <?php if ($currentStatus !== 'all'): ?>
                &nbsp;|&nbsp; Filter: <?= $currentStatus === 'sent' ? 'Terkirim ke ERP' : ($currentStatus === 'not_sent' ? 'Belum Dikirim' : 'Ditolak') ?>
            <?php endif; ?>
            &nbsp;|&nbsp; Dicetak: <?= date('d F Y, H:i') ?> WIB
        </div>
    </div>
    
    <!-- Stats Summary -->
    <div class="pdf-stats">
        <div class="pdf-stat">
            <div class="value"><?= number_format($stats['total']) ?></div>
            <div class="label">Total Pelamar</div>
        </div>
        <div class="pdf-stat">
            <div class="value" style="color:#059669;"><?= number_format($stats['sent_to_erp']) ?></div>
            <div class="label">Terkirim ERP</div>
        </div>
        <div class="pdf-stat">
            <div class="value" style="color:#d97706;"><?= number_format($stats['not_sent']) ?></div>
            <div class="label">Belum Dikirim</div>
        </div>
        <div class="pdf-stat">
            <div class="value" style="color:#dc2626;"><?= number_format($stats['rejected']) ?></div>
            <div class="label">Ditolak</div>
        </div>
        <div class="pdf-stat">
            <div class="value" style="color:#7c3aed;"><?= number_format($stats['approved']) ?></div>
            <div class="label">Disetujui</div>
        </div>
    </div>
    
    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Nama Pelamar</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Posisi</th>
                <th>Departemen</th>
                <th>Status Lamaran</th>
                <th>Status ERP</th>
                <th>Tanggal Kirim ERP</th>
                <th>ERP ID</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($applications)): ?>
            <tr>
                <td colspan="11" style="text-align:center; padding:20px; color:#999;">
                    Tidak ada data pelamar untuk periode ini.
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($applications as $i => $app): ?>
                <tr>
                    <td style="text-align:center;"><?= $i + 1 ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($app['full_name']) ?></td>
                    <td><?= htmlspecialchars($app['email']) ?></td>
                    <td><?= htmlspecialchars($app['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($app['vacancy_title'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($app['department_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($app['status_name'] ?? '-') ?></td>
                    <td>
                        <?php if (!empty($app['sent_to_erp_at'])): ?>
                            <span class="tag tag-sent">✓ Terkirim</span>
                        <?php elseif (($app['status_id'] ?? 0) == 7): ?>
                            <span class="tag tag-rejected">✗ Ditolak</span>
                        <?php else: ?>
                            <span class="tag tag-not-sent">⏳ Belum</span>
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($app['sent_to_erp_at']) ? date('d/m/Y H:i', strtotime($app['sent_to_erp_at'])) : '-' ?></td>
                    <td><?= !empty($app['erp_crew_id']) ? '#' . $app['erp_crew_id'] : '-' ?></td>
                    <td><?= date('d/m/Y', strtotime($app['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Footer -->
    <div class="pdf-footer">
        <p>Total Data: <strong><?= number_format($totalCount) ?></strong> pelamar</p>
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Rekrutmen PT Indo Ocean Crew Services</p>
        <p>&copy; <?= date('Y') ?> PT Indo Ocean Crew Services. All rights reserved.</p>
    </div>
    
    <script>
        // Auto-print on load if coming from export button
        if (window.location.search.includes('auto_print=1')) {
            window.addEventListener('load', function() {
                setTimeout(function() { window.print(); }, 500);
            });
        }
    </script>
</body>
</html>
