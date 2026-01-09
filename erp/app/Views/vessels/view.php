<?php
/**
 * Vessel Detail View
 */
$currentPage = 'vessels';
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <h1><i class="fas fa-ship" style="color: var(--accent-gold);"></i> <?= htmlspecialchars($vessel['name']) ?></h1>
        <p>IMO: <?= htmlspecialchars($vessel['imo_number'] ?? 'N/A') ?> | <?= htmlspecialchars($vessel['type_name'] ?? 'Unknown Type') ?></p>
    </div>
    <div style="display: flex; gap: 10px;">
        <?php 
        $statusColors = ['active' => 'success', 'maintenance' => 'warning', 'laid_up' => 'secondary', 'sold' => 'danger'];
        ?>
        <span class="badge badge-<?= $statusColors[$vessel['status']] ?? 'secondary' ?>" style="font-size: 14px; padding: 8px 16px;">
            <?= ucfirst(str_replace('_', ' ', $vessel['status'])) ?>
        </span>
        <a href="<?= BASE_URL ?>vessels/edit/<?= $vessel['id'] ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-edit"></i> Edit
        </a>
    </div>
</div>

<div class="grid-2">
    <!-- Vessel Info -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-info-circle" style="color: var(--accent-gold);"></i> Vessel Information</h3>
        <table style="width: 100%;">
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Vessel Name</td><td style="padding: 8px 0;"><strong><?= htmlspecialchars($vessel['name']) ?></strong></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">IMO Number</td><td style="padding: 8px 0;"><?= htmlspecialchars($vessel['imo_number'] ?? '-') ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Vessel Type</td><td style="padding: 8px 0;"><?= htmlspecialchars($vessel['type_name'] ?? '-') ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Flag State</td><td style="padding: 8px 0;"><?= ($vessel['flag_emoji'] ?? '') . ' ' . htmlspecialchars($vessel['flag_name'] ?? '-') ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Client</td><td style="padding: 8px 0;"><?= htmlspecialchars($vessel['client_name'] ?? '-') ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Year Built</td><td style="padding: 8px 0;"><?= $vessel['year_built'] ?? '-' ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Gross Tonnage</td><td style="padding: 8px 0;"><?= $vessel['gross_tonnage'] ? number_format($vessel['gross_tonnage']) . ' GT' : '-' ?></td></tr>
            <tr><td style="padding: 8px 0; color: var(--text-muted);">Crew Capacity</td><td style="padding: 8px 0;"><?= $vessel['crew_capacity'] ?? 25 ?></td></tr>
        </table>
    </div>
    
    <!-- Cost Summary -->
    <div class="card" style="margin-bottom: 24px;">
        <h3 style="margin-bottom: 16px;"><i class="fas fa-money-bill-wave" style="color: var(--accent-gold);"></i> Cost Summary</h3>
        
        <?php 
        // Display original currency amounts
        $costData = $totalCost ?? [];
        $byCurrency = $costData['by_currency'] ?? [];
        $symbols = $costData['symbols'] ?? [];
        $totalUsd = $costData['total_usd'] ?? 0;
        ?>
        
        <?php if (!empty($byCurrency)): ?>
            <?php foreach ($byCurrency as $currency => $amount): ?>
            <div class="stat-card" style="margin-bottom: 12px;">
                <div class="stat-icon <?= $currency === 'USD' ? 'green' : 'gold' ?>">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $symbols[$currency] ?? '' ?><?= number_format($amount, 0) ?></h3>
                    <p>Monthly Cost (<?= $currency ?>)</p>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (count($byCurrency) > 0 && !isset($byCurrency['USD'])): ?>
            <!-- USD Conversion Total -->
            <div class="stat-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.1) 100%); border: 1px solid rgba(16, 185, 129, 0.3);">
                <div class="stat-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3 style="color: var(--success);">$<?= number_format($totalUsd, 2) ?></h3>
                    <p>Converted to USD</p>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="stat-card" style="margin-bottom: 16px;">
                <div class="stat-icon gold"><i class="fas fa-coins"></i></div>
                <div class="stat-info">
                    <h3>$0.00</h3>
                    <p>Monthly Crew Cost</p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3><?= count($crewList ?? []) ?></h3>
                <p>Active Crew Members</p>
            </div>
        </div>
    </div>
</div>

<!-- Crew List -->
<div class="table-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3><i class="fas fa-users" style="color: var(--accent-gold);"></i> Crew List</h3>
        <a href="<?= BASE_URL ?>contracts/create?vessel_id=<?= $vessel['id'] ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Assign Crew
        </a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Contract No</th>
                <th>Sign Off Date</th>
                <th>Days Remaining</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($crewList)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">No crew assigned to this vessel</td></tr>
            <?php else: ?>
                <?php foreach ($crewList as $crew): ?>
                    <?php 
                    $d = $crew['days_remaining'] ?? null;
                    $dClass = $d !== null ? ($d <= 7 ? 'danger' : ($d <= 30 ? 'warning' : 'success')) : 'secondary';
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($crew['rank_name'] ?? '-') ?></strong></td>
                        <td><?= htmlspecialchars($crew['crew_name']) ?></td>
                        <td><a href="<?= BASE_URL ?>contracts/<?= $crew['id'] ?>" style="color: var(--accent-gold);"><?= htmlspecialchars($crew['contract_no']) ?></a></td>
                        <td><?= $crew['sign_off_date'] ? date('d M Y', strtotime($crew['sign_off_date'])) : '-' ?></td>
                        <td><?= $d !== null ? '<span class="badge badge-'.$dClass.'">'.$d.' days</span>' : '-' ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>contracts/<?= $crew['id'] ?>" class="btn-icon"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
