<?php
/**
 * Profit per Vessel Report
 * Shows revenue, cost, and profit for each vessel
 */

// Include main layout
$pageTitle = 'Profit per Vessel';
include APPPATH . 'Views/layouts/main.php';
?>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1><i class="fas fa-chart-line" style="color: var(--accent-gold);"></i> <span
                    data-translate="profit_per_vessel">Profit per Vessel</span></h1>
            <p data-translate="vessel_profit_subtitle">Analisis profit margin untuk setiap kapal</p>
        </div>
        <a href="<?= BASE_URL ?>vessels" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> <span data-translate="btn_back">Kembali</span>
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
    <?php
    $totalRevenue = array_sum(array_column($profitData, 'revenue_usd'));
    $totalCost = array_sum(array_column($profitData, 'cost_usd'));
    $totalProfit = array_sum(array_column($profitData, 'profit_usd'));
    $avgMargin = count($profitData) > 0 ? array_sum(array_column($profitData, 'margin_percent')) / count($profitData) : 0;
    ?>
    <div class="stat-card">
        <div class="stat-icon gold"><i class="fas fa-coins"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($totalRevenue, 0) ?></h3>
            <p data-translate="total_revenue">Total Revenue</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="fas fa-minus-circle"></i></div>
        <div class="stat-info">
            <h3>$<?= number_format($totalCost, 0) ?></h3>
            <p data-translate="total_cost">Total Cost</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon <?= $totalProfit >= 0 ? 'green' : 'red' ?>"><i class="fas fa-chart-line"></i></div>
        <div class="stat-info">
            <h3 style="color: <?= $totalProfit >= 0 ? 'var(--success)' : 'var(--danger)' ?>">
                $<?= number_format(abs($totalProfit), 0) ?>
            </h3>
            <p data-translate="<?= $totalProfit >= 0 ? 'total_profit' : 'total_loss' ?>">
                <?= $totalProfit >= 0 ? 'Total Profit' : 'Total Loss' ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-percentage"></i></div>
        <div class="stat-info">
            <h3><?= round($avgMargin, 1) ?>%</h3>
            <p data-translate="avg_margin">Avg Margin</p>
        </div>
    </div>
</div>

<!-- Profit Table -->
<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th data-translate="th_vessel">Vessel</th>
                <th data-translate="type">Type</th>
                <th data-translate="client">Client</th>
                <th data-translate="crew">Crew</th>
                <th style="text-align: right;" data-translate="revenue_usd">Revenue (USD)</th>
                <th style="text-align: right;" data-translate="cost_usd">Cost (USD)</th>
                <th style="text-align: right;" data-translate="profit_usd">Profit (USD)</th>
                <th style="text-align: center;" data-translate="margin">Margin</th>
                <th style="text-align: center;" data-translate="th_status">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($profitData)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-ship" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                        <span data-translate="no_vessel_data">Belum ada data vessel</span>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($profitData as $vessel): ?>
                    <tr>
                        <td>
                            <a href="<?= BASE_URL ?>vessels/<?= $vessel['id'] ?>"
                                style="color: var(--accent-gold); text-decoration: none; font-weight: 600;">
                                <?= htmlspecialchars($vessel['name']) ?>
                            </a>
                        </td>
                        <td>
                            <?= htmlspecialchars($vessel['vessel_type']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($vessel['client']) ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $vessel['crew_count'] ?>
                        </td>
                        <td style="text-align: right; color: var(--success);">
                            $
                            <?= number_format($vessel['revenue_usd'], 0) ?>
                        </td>
                        <td style="text-align: right; color: var(--danger);">
                            $
                            <?= number_format($vessel['cost_usd'], 0) ?>
                        </td>
                        <td
                            style="text-align: right; font-weight: 600; color: <?= $vessel['is_profitable'] ? 'var(--success)' : 'var(--danger)' ?>;">
                            <?= $vessel['is_profitable'] ? '+' : '' ?>$
                            <?= number_format($vessel['profit_usd'], 0) ?>
                        </td>
                        <td style="text-align: center;">
                            <span style="display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;
                        background: <?= $vessel['is_profitable'] ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' ?>;
                        color: <?= $vessel['is_profitable'] ? 'var(--success)' : 'var(--danger)' ?>;">
                                <?= $vessel['margin_percent'] ?>%
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($vessel['is_profitable']): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Profit</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Loss</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Chart placeholder -->
<div class="card" style="margin-top: 20px;">
    <h3 style="margin-bottom: 16px;"><i class="fas fa-chart-bar"></i> <span
            data-translate="profit_comparison_chart">Profit Comparison Chart</span></h3>
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <?php foreach ($profitData as $vessel): ?>
            <?php
            $maxProfit = max(array_column($profitData, 'profit_usd'));
            $minProfit = min(array_column($profitData, 'profit_usd'));
            $range = max(abs($maxProfit), abs($minProfit));
            $width = $range > 0 ? abs($vessel['profit_usd']) / $range * 100 : 0;
            ?>
            <div style="display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px 0;">
                <span
                    style="width: 150px; font-size: 13px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                    <?= htmlspecialchars($vessel['name']) ?>
                </span>
                <div
                    style="flex: 1; background: rgba(255,255,255,0.05); border-radius: 4px; height: 24px; position: relative;">
                    <div style="position: absolute; left: 0; top: 0; height: 100%; width: <?= min($width, 100) ?>%;
                    background: <?= $vessel['is_profitable'] ? 'linear-gradient(90deg, var(--success), #34D399)' : 'linear-gradient(90deg, var(--danger), #F87171)' ?>;
                    border-radius: 4px; transition: width 0.5s ease;">
                    </div>
                </div>
                <span style="width: 100px; text-align: right; font-weight: 600; font-size: 13px;
                color: <?= $vessel['is_profitable'] ? 'var(--success)' : 'var(--danger)' ?>;">
                    $
                    <?= number_format($vessel['profit_usd'], 0) ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>