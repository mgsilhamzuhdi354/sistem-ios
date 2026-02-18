<?php
/**
 * Employee Performance View - Modern Design with Realtime Data
 * PT Indo Ocean - ERP System
 */
$monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$currentMonthName = $monthNames[intval($month) - 1];
?>

<style>
    /* Modern Performance Styles */
    .performance-dashboard {
        padding: 20px;
    }

    .performance-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .performance-title h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-section {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 500;
    }

    .filter-select {
        min-width: 180px;
        padding: 10px 14px;
        border: 2px solid rgba(139, 92, 246, 0.2);
        border-radius: 10px;
        background: white;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        outline: none;
    }

    .btn-filter {
        padding: 10px 20px;
        background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.3s ease;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
    }

    /* Summary Cards */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-4px);
    }

    .summary-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .summary-icon.purple {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.15) 0%, rgba(168, 85, 247, 0.15) 100%);
        color: #8b5cf6;
    }

    .summary-icon.green {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
        color: #10b981;
    }

    .summary-icon.yellow {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.15) 100%);
        color: #f59e0b;
    }

    .summary-icon.red {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.15) 100%);
        color: #ef4444;
    }

    .summary-info h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .summary-info p {
        font-size: 0.9rem;
        color: #6b7280;
        margin: 4px 0 0;
    }

    /* Performance Table */
    .performance-table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .performance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .performance-table thead {
        background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
    }

    .performance-table th {
        padding: 16px;
        color: white;
        font-weight: 600;
        text-align: left;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .performance-table td {
        padding: 16px;
        border-bottom: 1px solid #f3f4f6;
    }

    .performance-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.03) 0%, rgba(168, 85, 247, 0.03) 100%);
    }

    /* Employee Cell */
    .employee-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .employee-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
    }

    .employee-name {
        font-weight: 600;
        color: #1f2937;
    }

    .employee-position {
        font-size: 0.8rem;
        color: #9ca3af;
    }

    /* Score Display */
    .score-display {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .score-value {
        font-size: 1.3rem;
        font-weight: 700;
    }

    .score-value.excellent {
        color: #10b981;
    }

    .score-value.good {
        color: #f59e0b;
    }

    .score-value.needs-improvement {
        color: #ef4444;
    }

    .score-bar {
        flex: 1;
        max-width: 100px;
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .score-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .score-fill.excellent {
        background: linear-gradient(90deg, #10b981, #059669);
    }

    .score-fill.good {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .score-fill.needs-improvement {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-excellent {
        background: rgba(16, 185, 129, 0.15);
        color: #059669;
    }

    .status-good {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }

    .status-needs-improvement {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }

    /* Live Badge */
    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        animation: pulse-glow 2s infinite;
    }

    .live-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: blink 1.5s infinite;
    }

    @keyframes pulse-glow {

        0%,
        100% {
            box-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
        }

        50% {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.8);
        }
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 64px;
        display: block;
        margin-bottom: 16px;
        background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-box i {
        color: #f59e0b;
        font-size: 1.5rem;
    }

    .info-box-content h4 {
        margin: 0 0 4px;
        color: #d97706;
        font-weight: 600;
    }

    .info-box-content p {
        margin: 0;
        color: #92400e;
        font-size: 0.9rem;
    }
</style>

<div class="performance-dashboard">
    <div class="performance-header">
        <div class="performance-title">
            <h1><i class="fas fa-chart-line"></i> Performa Karyawan</h1>
            <p style="margin-top: 4px; color: #6b7280;">
                Tracking KPI & performa <?= $currentMonthName ?> <?= $year ?>
                <span class="live-badge">LIVE</span>
            </p>
        </div>
        <form method="GET" class="filter-section">
            <div class="filter-group">
                <span class="filter-label">Karyawan</span>
                <select name="employee_id" class="filter-select">
                    <option value="">Semua Karyawan</option>
                    <?php if (isset($employees) && is_array($employees)): ?>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?? '' ?>" <?= (isset($selectedEmployee) && $selectedEmployee == ($emp['id'] ?? '')) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['nama'] ?? $emp['name'] ?? '-') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Bulan</span>
                <input type="month" name="period" value="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>"
                    class="filter-select">
            </div>
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filter
            </button>
        </form>
    </div>

    <?php if (!$success): ?>
        <div class="info-box"
            style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%); border-color: rgba(239, 68, 68, 0.3);">
            <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i>
            <div class="info-box-content">
                <h4 style="color: #b91c1c;">Connection Error</h4>
                <p style="color: #991b1b;">Tidak dapat terhubung ke sistem HRIS untuk mengambil data performa.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php
    // Process performance data
    $records = [];
    if (is_array($performanceData)) {
        if (isset($performanceData['data']) && is_array($performanceData['data'])) {
            $records = $performanceData['data'];
        } elseif (!empty($performanceData)) {
            $records = $performanceData;
        }
    }

    // Calculate summary statistics
    $totalEmployees = count($records);
    $excellentCount = 0;
    $goodCount = 0;
    $needsImprovementCount = 0;
    $avgScore = 0;

    foreach ($records as $r) {
        $score = $r['current_score'] ?? $r['total_score'] ?? $r['penilaian_berjalan'] ?? $r['nilai'] ?? 0;
        $avgScore += $score;
        if ($score >= 80)
            $excellentCount++;
        elseif ($score >= 60)
            $goodCount++;
        else
            $needsImprovementCount++;
    }
    $avgScore = $totalEmployees > 0 ? round($avgScore / $totalEmployees, 1) : 0;
    ?>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-icon purple">
                <i class="fas fa-users"></i>
            </div>
            <div class="summary-info">
                <h3><?= $totalEmployees ?></h3>
                <p>Total Karyawan</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon green">
                <i class="fas fa-star"></i>
            </div>
            <div class="summary-info">
                <h3><?= $excellentCount ?></h3>
                <p>Excellent (≥80)</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon yellow">
                <i class="fas fa-thumbs-up"></i>
            </div>
            <div class="summary-info">
                <h3><?= $goodCount ?></h3>
                <p>Good (60-79)</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon red">
                <i class="fas fa-exclamation"></i>
            </div>
            <div class="summary-info">
                <h3><?= $needsImprovementCount ?></h3>
                <p>Perlu Perbaikan (<60)< /p>
            </div>
        </div>
    </div>

    <?php if (empty($records)): ?>
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div class="info-box-content">
                <h4>Data Performa Kosong</h4>
                <p>Belum ada data performa untuk periode ini. Pastikan data sudah diimport dari sistem produksi.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Performance Table -->
    <div class="performance-table-container">
        <table class="performance-table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th style="text-align: center;">Total Penilaian</th>
                    <th style="text-align: center;">Skor Berjalan</th>
                    <th style="text-align: center;">Nilai Bulan Ini</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($records)): ?>
                    <?php foreach ($records as $record): ?>
                        <?php
                        // Handle different data structures
                        $empName = $record['name'] ?? $record['user']['name'] ?? $record['employee_name'] ?? '-';
                        $empPosition = $record['jabatan']['nama_jabatan'] ?? $record['Jabatan']['nama_jabatan'] ?? $record['jabatan']['nama'] ?? $record['position'] ?? '-';
                        $totalEntries = $record['total_entries'] ?? 0;
                        $monthlyEntries = $record['monthly_entries'] ?? 0;

                        // running_score = all-time MAX penilaian_berjalan
                        // monthly_score = SUM nilai for selected month
                        $runningScore = $record['running_score'] ?? $record['current_score'] ?? $record['penilaian_berjalan'] ?? 0;
                        $monthlyScore = $record['monthly_score'] ?? $record['total_score'] ?? $record['nilai'] ?? 0;

                        // Determine status based on running score (all-time performance)
                        $displayScore = $runningScore > 0 ? $runningScore : $monthlyScore;
                        $scoreClass = $displayScore >= 80 ? 'excellent' : ($displayScore >= 60 ? 'good' : 'needs-improvement');
                        $statusClass = $displayScore >= 80 ? 'status-excellent' : ($displayScore >= 60 ? 'status-good' : 'status-needs-improvement');
                        $statusText = $displayScore >= 80 ? 'Excellent' : ($displayScore >= 60 ? 'Good' : 'Perlu Perbaikan');

                        $empInitial = strtoupper(substr($empName, 0, 1));
                        ?>
                        <tr>
                            <td>
                                <div class="employee-cell">
                                    <div class="employee-avatar"><?= $empInitial ?></div>
                                    <div>
                                        <div class="employee-name"><?= htmlspecialchars($empName) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="employee-position"><?= htmlspecialchars($empPosition) ?></span>
                            </td>
                            <td style="text-align: center;">
                                <strong><?= $totalEntries ?></strong> penilaian
                            </td>
                            <td style="text-align: center;">
                                <div class="score-display" style="justify-content: center;">
                                    <span class="score-value <?= $scoreClass ?>"><?= $runningScore ?></span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div class="score-display" style="justify-content: center;">
                                    <span
                                        class="score-value <?= $monthlyScore > 0 ? $scoreClass : 'needs-improvement' ?>"><?= $monthlyScore ?></span>
                                    <div class="score-bar">
                                        <div class="score-fill <?= $scoreClass ?>"
                                            style="width: <?= min($displayScore, 100) ?>%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="fas fa-chart-bar"></i>
                            <h3>Belum ada data performa</h3>
                            <p>Data performa akan muncul setelah diimport dari sistem HRIS produksi</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>