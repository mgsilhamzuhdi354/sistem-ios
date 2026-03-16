<?php
/**
 * Crew List per Vessel - PDF View
 */
$reportTitle = 'CREW LIST — ' . strtoupper($vessel['vessel_name'] ?? 'VESSEL');
$reportSubtitle = 'IMO: ' . ($vessel['imo_number'] ?? '-') . ' | Flag: ' . ($vessel['flag'] ?? '-') . ' | Type: ' . ($vessel['vessel_type'] ?? '-');
$reportDate = date('d F Y');
$crewCount = count($crew ?? []);

include APPPATH . 'Views/partials/pdf_header.php';
?>

    <!-- Vessel Info -->
    <div style="display:flex; gap:10px; margin-bottom:15px; flex-wrap:wrap;">
        <div class="summary-card" style="flex:2; min-width:200px; text-align:left;">
            <table style="width:100%; font-size:8.5pt;">
                <tr><td style="width:120px; font-weight:700; color:#475569; padding:3px 0;">Vessel Name</td><td style="padding:3px 0;">: <strong><?= htmlspecialchars($vessel['vessel_name'] ?? '-') ?></strong></td></tr>
                <tr><td style="font-weight:700; color:#475569; padding:3px 0;">Vessel Type</td><td style="padding:3px 0;">: <?= htmlspecialchars($vessel['vessel_type'] ?? '-') ?></td></tr>
                <tr><td style="font-weight:700; color:#475569; padding:3px 0;">Flag State</td><td style="padding:3px 0;">: <?= htmlspecialchars($vessel['flag'] ?? '-') ?></td></tr>
                <tr><td style="font-weight:700; color:#475569; padding:3px 0;">Client/Owner</td><td style="padding:3px 0;">: <?= htmlspecialchars($vessel['client_name'] ?? '-') ?></td></tr>
            </table>
        </div>
        <div class="summary-card">
            <div class="label">Total Crew</div>
            <div class="value text-blue"><?= $crewCount ?></div>
        </div>
        <div class="summary-card">
            <div class="label">Status</div>
            <div class="value" style="font-size:11pt">
                <span class="badge badge-green"><?= ucfirst($vessel['status'] ?? 'Active') ?></span>
            </div>
        </div>
    </div>

    <!-- Crew Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width:25px">No</th>
                <th>Crew Name</th>
                <th>Rank</th>
                <th>Nationality</th>
                <th>Sign On Date</th>
                <th>Sign Off Date</th>
                <th>Contract No</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($crew)): ?>
            <tr><td colspan="8" style="text-align:center; padding:30px; color:#999;">No crew assigned to this vessel</td></tr>
            <?php else: ?>
                <?php foreach ($crew as $i => $c): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td class="font-bold"><?= htmlspecialchars($c['crew_name'] ?? $c['full_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['rank_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['nationality'] ?? '-') ?></td>
                    <td class="text-sm"><?= !empty($c['sign_on_date']) ? date('d/m/Y', strtotime($c['sign_on_date'])) : '-' ?></td>
                    <td class="text-sm"><?= !empty($c['sign_off_date']) ? date('d/m/Y', strtotime($c['sign_off_date'])) : '-' ?></td>
                    <td class="text-sm" style="color:#1e3a5f; font-weight:600"><?= htmlspecialchars($c['contract_no'] ?? '-') ?></td>
                    <td class="text-center"><span class="badge badge-green"><?= ucfirst($c['status'] ?? 'active') ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Master Signature -->
    <div style="margin-top:30px; display:flex; justify-content:space-between; padding:0 40px;">
        <div style="text-align:center; width:200px;">
            <p style="font-size:8pt; color:#666; margin-bottom:60px;">Master / Captain:</p>
            <div style="border-top:1px solid #333; padding-top:5px;">
                <p style="font-size:8pt; font-weight:700;">Name & Signature</p>
            </div>
        </div>
        <div style="text-align:center; width:200px;">
            <p style="font-size:8pt; color:#666; margin-bottom:60px;">Manning Agent:</p>
            <div style="border-top:1px solid #333; padding-top:5px;">
                <p style="font-size:8pt; font-weight:700;">PT. Indo Ocean Crew Services</p>
            </div>
        </div>
    </div>

<?php include APPPATH . 'Views/partials/pdf_footer.php'; ?>
