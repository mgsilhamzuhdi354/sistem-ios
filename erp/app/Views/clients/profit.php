<?php
/**
 * Clients Profit Analysis View
 */
$currentPage = 'clients';
ob_start();
?>

<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-chart-pie"></i> Profit per Client</h2>
            <p class="text-muted">Analisis profit dari setiap client/principal</p>
        </div>
        <a href="<?= BASE_URL ?>clients" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Summary Cards -->
    <?php 
    $totalMonthlyProfit = array_sum(array_column($profitData, 'monthly_profit_usd'));
    $totalMonthlyRevenue = array_sum(array_column($profitData, 'monthly_revenue_usd'));
    $totalActiveCrew = array_sum(array_column($profitData, 'active_crew'));
    $avgMargin = $totalMonthlyRevenue > 0 ? round(($totalMonthlyProfit / $totalMonthlyRevenue) * 100, 1) : 0;
    ?>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Monthly Profit</p>
                    <h4 class="text-success mb-0">$<?= number_format($totalMonthlyProfit, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Monthly Revenue</p>
                    <h4 class="text-primary mb-0">$<?= number_format($totalMonthlyRevenue, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Active Crew</p>
                    <h4 class="mb-0"><?= $totalActiveCrew ?> Crew</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-1">Average Margin</p>
                    <h4 class="mb-0"><?= $avgMargin ?>%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Profit Breakdown per Client</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client Name</th>
                            <th class="text-center">Active Crew</th>
                            <th class="text-end">Monthly Revenue</th>
                            <th class="text-end">Monthly Salary</th>
                            <th class="text-end">Monthly Profit</th>
                            <th class="text-center">Margin</th>
                            <th class="text-end">Total Profit (All Time)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($profitData as $data): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>clients/<?= $data['id'] ?>">
                                    <?= htmlspecialchars($data['name']) ?>
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= $data['active_crew'] ?> crew</span>
                            </td>
                            <td class="text-end">$<?= number_format($data['monthly_revenue_usd'], 2) ?></td>
                            <td class="text-end text-danger">$<?= number_format($data['monthly_salary_usd'], 2) ?></td>
                            <td class="text-end fw-bold text-success">$<?= number_format($data['monthly_profit_usd'], 2) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $data['profit_margin'] >= 20 ? 'bg-success' : ($data['profit_margin'] >= 10 ? 'bg-warning' : 'bg-danger') ?>">
                                    <?= $data['profit_margin'] ?>%
                                </span>
                            </td>
                            <td class="text-end text-primary">$<?= number_format($data['total_profit_usd'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="2">TOTAL</td>
                            <td class="text-center"><?= $totalActiveCrew ?></td>
                            <td class="text-end">$<?= number_format($totalMonthlyRevenue, 2) ?></td>
                            <td class="text-end">$<?= number_format(array_sum(array_column($profitData, 'monthly_salary_usd')), 2) ?></td>
                            <td class="text-end text-success">$<?= number_format($totalMonthlyProfit, 2) ?></td>
                            <td class="text-center"><?= $avgMargin ?>%</td>
                            <td class="text-end">$<?= number_format(array_sum(array_column($profitData, 'total_profit_usd')), 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border: none;
}
</style>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>