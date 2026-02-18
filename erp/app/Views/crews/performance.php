<?php
/**
 * Crew Performance View
 */
?>
<div class="page-header">
    <h1>Performa Crew</h1>
    <p>Tracking performa dan KPI crew di kapal</p>
</div>

<div class="card" style="margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 12px; align-items: end;">
        <div class="form-group" style="flex: 1; margin: 0;">
            <label class="form-label">Crew</label>
            <select name="crew_id" class="form-control">
                <option value="">-- Semua Crew Onboard --</option>
                <?php foreach ($crews as $crew): ?>
                    <option value="<?= $crew['id'] ?>" <?= ($selectedCrew == $crew['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($crew['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin: 0;">
            <label class="form-label">Periode</label>
            <input type="month" name="period" value="<?= $year ?>-<?= str_pad($month, 2, '0', STR_PAD_LEFT) ?>"
                class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>
    </form>
</div>

<?php if (empty($performanceData)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-info-circle"></i>
        Tidak ada data performa untuk periode yang dipilih.
    </div>
<?php else: ?>
    <div class="grid-4" style="margin-bottom: 20px;">
        <?php
        $totalScore = 0;
        $count = count($performanceData);
        foreach ($performanceData as $data) {
            $totalScore += $data['score'];
        }
        $avgScore = $count > 0 ? round($totalScore / $count) : 0;
        ?>
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>
                    <?= $count ?>
                </h3>
                <p>Total Crew</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon <?= $avgScore >= 80 ? 'green' : ($avgScore >= 60 ? 'gold' : 'red') ?>">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>
                    <?= $avgScore ?>%
                </h3>
                <p>Avg Performance</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>
                    <?= count(array_filter($performanceData, fn($d) => $d['score'] >= 80)) ?>
                </h3>
                <p>Excellent</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>
                    <?= count(array_filter($performanceData, fn($d) => $d['score'] < 60)) ?>
                </h3>
                <p>Need Improvement</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 16px;">Detail Performance</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Crew</th>
                    <th>Posisi</th>
                    <th>Kapal</th>
                    <th>Attendance</th>
                    <th>Skills</th>
                    <th>Discipline</th>
                    <th>Score</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($performanceData as $data): ?>
                    <tr>
                        <td>
                            <strong>
                                <?= htmlspecialchars($data['crew']['full_name'] ?? '-') ?>
                            </strong>
                            <div style="font-size: 11px; color: var(--text-muted);">
                                <?= htmlspecialchars($data['crew']['employee_id'] ?? '') ?>
                            </div>
                        </td>
                        <td>
                            <?= htmlspecialchars($data['crew']['rank_name'] ?? $data['crew']['position'] ?? '-') ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($data['crew']['vessel_name'] ?? '-') ?>
                        </td>
                        <td>
                            <div class="progress-bar"
                                style="width: 80px; height: 6px; background: var(--border-color); border-radius: 3px;">
                                <div
                                    style="width: <?= $data['attendance'] ?>%; height: 100%; background: var(--success); border-radius: 3px;">
                                </div>
                            </div>
                            <small>
                                <?= $data['attendance'] ?>%
                            </small>
                        </td>
                        <td>
                            <div class="progress-bar"
                                style="width: 80px; height: 6px; background: var(--border-color); border-radius: 3px;">
                                <div
                                    style="width: <?= $data['skills'] ?>%; height: 100%; background: var(--info); border-radius: 3px;">
                                </div>
                            </div>
                            <small>
                                <?= $data['skills'] ?>%
                            </small>
                        </td>
                        <td>
                            <div class="progress-bar"
                                style="width: 80px; height: 6px; background: var(--border-color); border-radius: 3px;">
                                <div
                                    style="width: <?= $data['discipline'] ?>%; height: 100%; background: var(--warning); border-radius: 3px;">
                                </div>
                            </div>
                            <small>
                                <?= $data['discipline'] ?>%
                            </small>
                        </td>
                        <td><strong style="font-size: 16px;">
                                <?= $data['score'] ?>
                            </strong></td>
                        <td>
                            <?php
                            $score = $data['score'];
                            if ($score >= 80):
                                ?>
                                <span class="badge badge-success">Excellent</span>
                            <?php elseif ($score >= 60): ?>
                                <span class="badge badge-warning">Good</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Needs Improvement</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>