<?php
/**
 * Active Contracts Report - PDF View
 * Print-friendly report of all active contracts
 */
$reportTitle = 'ACTIVE CONTRACTS REPORT';
$reportSubtitle = 'All Currently Active Crew Contracts';
$reportDate = date('d F Y');
$contractCount = count($contracts ?? []);
include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Summary -->
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Active</div>
            <div class="value text-blue"><?= $contractCount ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Vessels</div>
            <div class="value"><?= count(array_unique(array_column($contracts, 'vessel_name'))) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Clients</div>
            <div class="value"><?= count(array_unique(array_column($contracts, 'client_name'))) ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Ranks</div>
            <div class="value"><?= count(array_unique(array_filter(array_column($contracts, 'rank_name')))) ?></div>
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
                <th>Client</th>
                <th>Sign On</th>
                <th>Sign Off</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($contracts)): ?>
            <tr><td colspan="9" style="text-align:center; padding:30px; color:#999;">No active contracts found</td></tr>
            <?php else: ?>
                <?php foreach ($contracts as $i => $c): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td class="font-bold" style="color:#1e3a5f"><?= htmlspecialchars($c['contract_no'] ?? '-') ?></td>
                    <td class="font-bold"><?= htmlspecialchars($c['crew_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['vessel_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['client_name'] ?? '-') ?></td>
                    <td class="text-sm"><?= !empty($c['sign_on_date']) ? date('d/m/Y', strtotime($c['sign_on_date'])) : '-' ?></td>
                    <td class="text-sm"><?= !empty($c['sign_off_date']) ? date('d/m/Y', strtotime($c['sign_off_date'])) : '-' ?></td>
                    <td class="text-center"><span class="badge badge-green"><?= ucfirst($c['status'] ?? 'active') ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="font-size:7.5pt; color:#94a3b8; margin-top:8px;">
        Total: <strong><?= $contractCount ?></strong> active contracts as of <?= date('d F Y') ?>
    </div>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
