<?php
/**
 * Client Detail View - Premium International Standard Design
 */
$currentPage = 'clients';
ob_start();
?>

<style>
/* Premium Client Detail Styles */
.client-hero {
    background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.9), rgba(var(--primary-dark-rgb), 0.95));
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.client-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(var(--accent-gold-rgb), 0.15) 0%, transparent 70%);
    border-radius: 50%;
}
.client-logo {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--accent-gold), #f5c542);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 800;
    color: var(--primary-dark);
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
}
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.premium-stat {
    background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    transition: all 0.3s ease;
}
.premium-stat:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.3);
    border-color: var(--accent-gold);
}
.premium-stat .value {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 8px;
    background: linear-gradient(135deg, var(--accent-gold), #f5c542);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.premium-stat .value.green {
    background: linear-gradient(135deg, #10B981, #34D399);
    -webkit-background-clip: text;
    background-clip: text;
}
.premium-stat .value.red {
    background: linear-gradient(135deg, #EF4444, #F87171);
    -webkit-background-clip: text;
    background-clip: text;
}
.premium-stat .value.blue {
    background: linear-gradient(135deg, #3B82F6, #60A5FA);
    -webkit-background-clip: text;
    background-clip: text;
}
.premium-stat .label {
    font-size: 13px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.section-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid var(--border-color);
}
.section-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-title i {
    color: var(--accent-gold);
}
.crew-card {
    background: rgba(0,0,0,0.2);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}
.crew-card:hover {
    border-color: var(--accent-gold);
    transform: translateX(4px);
}
.crew-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary-light), var(--primary));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: white;
}
.crew-info {
    flex: 1;
}
.crew-name {
    font-weight: 600;
    margin-bottom: 4px;
}
.crew-meta {
    font-size: 12px;
    color: var(--text-muted);
}
.crew-salary {
    text-align: right;
}
.crew-salary .amount {
    font-size: 16px;
    font-weight: 700;
    color: var(--accent-gold);
}
.crew-salary .usd {
    font-size: 12px;
    color: var(--success);
}
.vessel-mini {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(var(--accent-gold-rgb), 0.1);
    padding: 8px 16px;
    border-radius: 20px;
    margin-right: 8px;
    margin-bottom: 8px;
    font-size: 13px;
    color: var(--accent-gold);
    border: 1px solid rgba(var(--accent-gold-rgb), 0.3);
    transition: all 0.3s ease;
}
.vessel-mini:hover {
    background: rgba(var(--accent-gold-rgb), 0.2);
    transform: scale(1.02);
}
.profit-highlight {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.1));
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
}
.profit-value {
    font-size: 36px;
    font-weight: 800;
    color: var(--success);
    margin-bottom: 8px;
}
.profit-label {
    font-size: 14px;
    color: var(--text-muted);
}
.tab-nav {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 12px;
}
.tab-btn {
    padding: 10px 20px;
    border-radius: 8px;
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}
.tab-btn.active, .tab-btn:hover {
    background: rgba(var(--accent-gold-rgb), 0.1);
    color: var(--accent-gold);
}
</style>

<!-- Client Hero Section -->
<div class="client-hero">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <div class="client-logo">
                <?= strtoupper(substr($client['short_name'] ?? $client['name'], 0, 2)) ?>
            </div>
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 8px;"><?= htmlspecialchars($client['name']) ?></h1>
            <p style="color: var(--text-muted); margin-bottom: 16px;">
                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($client['country'] ?? 'Unknown') ?>
                <?php if ($client['city']): ?> • <?= htmlspecialchars($client['city']) ?><?php endif; ?>
            </p>
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <?php if ($client['email']): ?>
                <span style="font-size: 13px; color: var(--text-secondary);"><i class="fas fa-envelope" style="color: var(--accent-gold);"></i> <?= htmlspecialchars($client['email']) ?></span>
                <?php endif; ?>
                <?php if ($client['phone']): ?>
                <span style="font-size: 13px; color: var(--text-secondary);"><i class="fas fa-phone" style="color: var(--accent-gold);"></i> <?= htmlspecialchars($client['phone']) ?></span>
                <?php endif; ?>
                <?php if ($client['website']): ?>
                <a href="<?= htmlspecialchars($client['website']) ?>" target="_blank" style="font-size: 13px; color: var(--accent-gold);"><i class="fas fa-globe"></i> Website</a>
                <?php endif; ?>
            </div>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= BASE_URL ?>clients/edit/<?= $client['id'] ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?= BASE_URL ?>clients" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<!-- Statistics Grid -->
<div class="stat-grid">
    <div class="premium-stat">
        <div class="value"><?= count($vessels ?? []) ?></div>
        <div class="label"><i class="fas fa-ship"></i> Total Vessels</div>
    </div>
    <div class="premium-stat">
        <div class="value blue"><?= $stats['active_crew'] ?? 0 ?></div>
        <div class="label"><i class="fas fa-user-check"></i> Active Crew</div>
    </div>
    <div class="premium-stat">
        <div class="value red"><?= $stats['inactive_crew'] ?? 0 ?></div>
        <div class="label"><i class="fas fa-user-times"></i> Non-Active Crew</div>
    </div>
    
    <!-- Total Revenue (Pendapatan Kotor dari Client) -->
    <div class="premium-stat" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.15)); border-color: rgba(59, 130, 246, 0.4);">
        <?php 
        $totalRevenue = $profit['accumulated_client_rate_usd'] ?? 0;
        if ($totalRevenue >= 1000000) {
            $displayRevenue = '$' . number_format($totalRevenue / 1000000, 2) . 'M';
        } elseif ($totalRevenue >= 1000) {
            $displayRevenue = '$' . number_format($totalRevenue / 1000, 1) . 'K';
        } else {
            $displayRevenue = '$' . number_format($totalRevenue, 0);
        }
        ?>
        <div class="value blue" style="font-size: 28px;"><?= $displayRevenue ?></div>
        <div class="label"><i class="fas fa-hand-holding-usd"></i> Total Revenue (Kotor)</div>
    </div>
    
    <!-- Total Cost (Biaya Gaji) -->
    <div class="premium-stat" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.1)); border-color: rgba(239, 68, 68, 0.3);">
        <?php 
        $totalCost = $profit['accumulated_salary_usd'] ?? 0;
        if ($totalCost >= 1000000) {
            $displayCost = '$' . number_format($totalCost / 1000000, 2) . 'M';
        } elseif ($totalCost >= 1000) {
            $displayCost = '$' . number_format($totalCost / 1000, 1) . 'K';
        } else {
            $displayCost = '$' . number_format($totalCost, 0);
        }
        ?>
        <div class="value red" style="font-size: 28px;"><?= $displayCost ?></div>
        <div class="label"><i class="fas fa-money-bill-wave"></i> Total Cost (Gaji)</div>
    </div>
    
    <!-- Total Profit -->
    <div class="premium-stat" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(5, 150, 105, 0.2)); border-color: rgba(16, 185, 129, 0.5);">
        <?php 
        $accProfit = $profit['accumulated_profit_usd'] ?? 0;
        if ($accProfit >= 1000000) {
            $displayProfit = '$' . number_format($accProfit / 1000000, 2) . 'M';
        } elseif ($accProfit >= 1000) {
            $displayProfit = '$' . number_format($accProfit / 1000, 1) . 'K';
        } else {
            $displayProfit = '$' . number_format($accProfit, 0);
        }
        ?>
        <div class="value green" style="font-size: 28px;"><?= $displayProfit ?></div>
        <div class="label"><i class="fas fa-piggy-bank"></i> Total Profit (Bersih)</div>
    </div>
</div>

<!-- Monthly Cost Breakdown -->
<?php if (!empty($monthlyCost['by_currency'])): ?>
<div class="section-card">
    <h3 class="section-title"><i class="fas fa-chart-pie"></i> Monthly Cost Breakdown</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <?php foreach ($monthlyCost['by_currency'] as $code => $amount): ?>
        <div style="background: rgba(0,0,0,0.2); padding: 20px; border-radius: 12px; text-align: center;">
            <div style="font-size: 24px; font-weight: 700; color: var(--accent-gold); margin-bottom: 8px;">
                <?= $monthlyCost['symbols'][$code] ?? '' ?><?= number_format($amount, 0) ?>
            </div>
            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;"><?= $code ?></div>
        </div>
        <?php endforeach; ?>
        <div class="profit-highlight">
            <div class="profit-value">$<?= number_format($monthlyCost['total_usd'] ?? 0, 2) ?></div>
            <div class="profit-label">Converted to USD</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Vessels Section -->
<?php if (!empty($vessels)): ?>
<div class="section-card">
    <h3 class="section-title"><i class="fas fa-ship"></i> Fleet Overview</h3>
    <div style="display: flex; flex-wrap: wrap;">
        <?php foreach ($vessels as $v): ?>
        <a href="<?= BASE_URL ?>vessels/<?= $v['id'] ?>" class="vessel-mini">
            <?= $v['flag_emoji'] ?? '🚢' ?> <?= htmlspecialchars($v['name']) ?>
            <span class="badge badge-<?= $v['status'] === 'active' ? 'success' : 'secondary' ?>" style="font-size: 10px;"><?= $v['crew_count'] ?? 0 ?> crew</span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Crew Details -->
<div class="section-card">
    <h3 class="section-title"><i class="fas fa-users"></i> Crew Members</h3>
    
    <!-- Tab Navigation -->
    <div class="tab-nav">
        <button class="tab-btn active" onclick="showTab('active')">
            <i class="fas fa-user-check"></i> Active (<?= $stats['active_crew'] ?? 0 ?>)
        </button>
        <button class="tab-btn" onclick="showTab('inactive')">
            <i class="fas fa-user-times"></i> Non-Active (<?= $stats['inactive_crew'] ?? 0 ?>)
        </button>
        <button class="tab-btn" onclick="showTab('all')">
            <i class="fas fa-users"></i> All
        </button>
    </div>
    
    <!-- Crew List -->
    <div id="crew-list">
        <?php if (empty($contracts)): ?>
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
            <p>No crew contracts found for this client</p>
        </div>
        <?php else: ?>
            <?php foreach ($contracts as $c): ?>
            <div class="crew-card" data-status="<?= $c['status'] ?>">
                <div class="crew-avatar">
                    <?= strtoupper(substr($c['crew_name'], 0, 2)) ?>
                </div>
                <div class="crew-info">
                    <div class="crew-name"><?= htmlspecialchars($c['crew_name']) ?></div>
                    <div class="crew-meta">
                        <span class="badge badge-<?= ['active' => 'success', 'onboard' => 'primary', 'pending' => 'warning', 'draft' => 'secondary', 'completed' => 'info', 'terminated' => 'danger'][$c['status']] ?? 'secondary' ?>">
                            <?= ucfirst($c['status']) ?>
                        </span>
                        • <?= htmlspecialchars($c['rank_name'] ?? '-') ?>
                        • <?= htmlspecialchars($c['vessel_name'] ?? '-') ?>
                    </div>
                </div>
                <div class="crew-salary" style="text-align: center;">
                    <div style="font-size: 11px; color: var(--text-muted);">Salary</div>
                    <div class="amount"><?= $c['currency_symbol'] ?? 'Rp' ?><?= number_format($c['total_monthly'] ?? 0, 0) ?></div>
                    <?php if (($c['currency_code'] ?? 'USD') !== 'USD' && !empty($c['salary_usd'])): ?>
                    <div class="usd">≈ $<?= number_format($c['salary_usd'], 2) ?></div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($c['client_rate']) && $c['client_rate'] > 0): ?>
                <div style="text-align: center; padding: 0 12px; border-left: 1px solid var(--border-color);">
                    <div style="font-size: 11px; color: var(--text-muted);">Client Rate</div>
                    <div style="font-weight: 600; color: var(--accent-gold);"><?= $c['currency_symbol'] ?? 'Rp' ?><?= number_format($c['client_rate'], 0) ?></div>
                </div>
                <div style="text-align: center; padding: 0 12px; border-left: 1px solid var(--border-color);">
                    <div style="font-size: 11px; color: var(--text-muted);">Profit/Bln</div>
                    <div style="font-weight: 700; color: <?= ($c['profit'] ?? 0) > 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                        <?php if (($c['profit'] ?? 0) > 0): ?>+<?php endif; ?>$<?= number_format($c['profit_usd'] ?? 0, 0) ?>
                    </div>
                </div>
                <div style="text-align: center; padding: 0 12px; border-left: 1px solid var(--border-color); background: rgba(16, 185, 129, 0.1); border-radius: 8px; margin: -8px 0;">
                    <div style="font-size: 10px; color: var(--text-muted); padding-top: 8px;"><?= $c['months_active'] ?? 0 ?> bulan</div>
                    <div style="font-weight: 700; font-size: 14px; color: var(--success); padding-bottom: 8px;">
                        Total: $<?= number_format($c['total_profit_usd'] ?? 0, 0) ?>
                    </div>
                </div>
                <?php else: ?>
                <div style="text-align: center; padding: 0 12px; border-left: 1px solid var(--border-color); opacity: 0.5;">
                    <div style="font-size: 11px; color: var(--text-muted);">Profit</div>
                    <div style="font-size: 12px; color: var(--text-muted);">No rate set</div>
                </div>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>contracts/<?= $c['id'] ?>" class="btn-icon" title="View Contract">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Company Details -->
<div class="section-card">
    <h3 class="section-title"><i class="fas fa-building"></i> Company Information</h3>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
        <div>
            <table style="width: 100%;">
                <tr><td style="padding: 8px 0; color: var(--text-muted); width: 40%;">Full Name</td><td style="padding: 8px 0;"><strong><?= htmlspecialchars($client['name']) ?></strong></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Short Name</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['short_name'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Country</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['country'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">City</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['city'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Address</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['address'] ?? '-') ?></td></tr>
            </table>
        </div>
        <div>
            <table style="width: 100%;">
                <tr><td style="padding: 8px 0; color: var(--text-muted); width: 40%;">Email</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['email'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Phone</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['phone'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Website</td><td style="padding: 8px 0;"><?= $client['website'] ? '<a href="'.$client['website'].'" target="_blank" style="color: var(--accent-gold);">'.$client['website'].'</a>' : '-' ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Contact Person</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['contact_person'] ?? '-') ?></td></tr>
                <tr><td style="padding: 8px 0; color: var(--text-muted);">Contact Email</td><td style="padding: 8px 0;"><?= htmlspecialchars($client['contact_email'] ?? '-') ?></td></tr>
            </table>
        </div>
    </div>
</div>

<script>
function showTab(type) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase().includes(type) || (type === 'all' && btn.textContent.includes('All'))) {
            btn.classList.add('active');
        }
    });
    
    // Filter crew cards
    document.querySelectorAll('.crew-card').forEach(card => {
        const status = card.dataset.status;
        if (type === 'all') {
            card.style.display = 'flex';
        } else if (type === 'active') {
            card.style.display = (status === 'active' || status === 'onboard') ? 'flex' : 'none';
        } else if (type === 'inactive') {
            card.style.display = (status !== 'active' && status !== 'onboard') ? 'flex' : 'none';
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include APPPATH . 'Views/layouts/main.php';
?>
