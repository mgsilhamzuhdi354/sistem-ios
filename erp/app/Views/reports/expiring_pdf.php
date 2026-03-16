<?php
/**
 * Expiring Contracts Report - PDF View
 */
$reportTitle = 'EXPIRING CONTRACTS REPORT';
$reportSubtitle = 'Contracts Expiring Within ' . ($days ?? 60) . ' Days';
$reportDate = date('d F Y');
$contractCount = count($contracts ?? []);

// Categorize by urgency
$urgent = array_filter($contracts, fn($c) => ($c['days_remaining'] ?? 999) <= 7);
$warning = array_filter($contracts, fn($c) => ($c['days_remaining'] ?? 999) > 7 && ($c['days_remaining'] ?? 999) <= 30);
$normal = array_filter($contracts, fn($c) => ($c['days_remaining'] ?? 999) > 30);

include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Summary -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Expiring</div>
            <div class="value text-amber"><?= $contractCount ?></div>
        </div>
        <div class="summary-card">
            <div class="label">🔴 Critical (≤7 days)</div>
            <div class="value text-red"><?= count($urgent) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">🟡 Warning (8-30 days)</div>
            <div class="value text-amber"><?= count($warning) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">🟢 Normal (30+ days)</div>
            <div class="value text-green"><?= count($normal) ?></div>
        </div>
    </div>

    <!-- Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Contract No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Vessel</th>
                <th>Sign Off Date</th>
                <th class="text-center">Days Left</th>
                <th class="text-center">Urgency</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contracts)): ?>
            <tr><td colspan="8" style="text-align:center; padding:30px; color:#999;">No expiring contracts found</td></tr>
            <?php else: ?>
                <?php foreach ($contracts as $i => $c): 
                    $daysLeft = $c['days_remaining'] ?? 999;
                    if ($daysLeft <= 7) { $badgeClass = 'badge-red'; $urgency = 'CRITICAL'; }
                    elseif ($daysLeft <= 30) { $badgeClass = 'badge-amber'; $urgency = 'WARNING'; }
                    else { $badgeClass = 'badge-green'; $urgency = 'NORMAL'; }
                ?>
                <tr style="<?= $daysLeft <= 7 ? 'background:#fef2f2 !important;' : '' ?>">
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td class="font-bold" style="color:#1e3a5f"><?= htmlspecialchars($c['contract_no'] ?? '-') ?></td>
                    <td class="font-bold"><?= htmlspecialchars($c['crew_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['vessel_name'] ?? '-') ?></td>
                    <td class="text-sm"><?= !empty($c['sign_off_date']) ? date('d/m/Y', strtotime($c['sign_off_date'])) : '-' ?></td>
                    <td class="text-center font-bold" style="color:<?= $daysLeft <= 7 ? '#dc2626' : ($daysLeft <= 30 ? '#d97706' : '#16a34a') ?>"><?= $daysLeft ?></td>
                    <td class="text-center"><span class="badge <?= $badgeClass ?>"><?= $urgency ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
