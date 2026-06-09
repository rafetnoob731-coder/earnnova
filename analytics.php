<?php
// EARNNOVA - Premium Analytics Dashboard
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();
$uid = $user['uid'];

$pageTitle = 'Analytics';
$showSidebar = true;

$totalAds = 0;
$adsResult = db()->request('GET', "/rest/v1/ad_rewards?user_id=eq.$uid&select=id");
if ($adsResult['success']) $totalAds = count($adsResult['data'] ?? []);

$todayRewards = db()->getTodayAdRewards($uid);
$todayEarnings = array_sum(array_column($todayRewards, 'reward_amount'));

$transactions = db()->getTransactions($uid);
$referrals = db()->getReferrals($uid);

$totalEarnings = floatval($user['balance'] ?? 0) + floatval($user['referral_balance'] ?? 0);

$earningsByType = [];
$earningsByDate = [];
foreach ($transactions as $tx) {
    $type = $tx['type'] ?? 'other';
    $earningsByType[$type] = ($earningsByType[$type] ?? 0) + floatval($tx['amount'] ?? 0);
    $date = date('Y-m-d', strtotime($tx['created_at'] ?? 'today'));
    $earningsByDate[$date] = ($earningsByDate[$date] ?? 0) + floatval($tx['amount'] ?? 0);
}

$xp = intval($user['xp'] ?? 0);
$level = intval($user['level'] ?? 1);
$rank = getUserRank($xp);

$withdrawals = db()->getWithdrawals($uid);
$totalWithdrawn = array_sum(array_column($withdrawals, 'amount'));
$pendingWithdrawals = array_filter($withdrawals, fn($w) => ($w['status'] ?? '') === 'pending');

// Chart data
$chartLabels = [];
$chartData = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d/m', strtotime($date));
    $chartData[] = round($earningsByDate[$date] ?? 0, 4);
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">📊 Analytics</h1>
        <p class="page-subtitle">Track your performance with detailed insights</p>
    </div>
</div>

<script>
window.__analyticsData = {
    labels: <?= json_encode($chartLabels) ?>,
    earnings: <?= json_encode($chartData) ?>,
    types: <?= json_encode(array_keys($earningsByType)) ?>,
    typeValues: <?= json_encode(array_values($earningsByType)) ?>
};
</script>

<!-- Overview Stats -->
<div class="dashboard-grid stagger-children">
    <div class="card-3d glass-2 stat-card" data-scroll-animate="slide-in-up">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));">👁️</div>
            <div class="stat-label">Total Ads Watched</div>
            <div class="stat-value"><?= number_format($totalAds) ?></div>
            <div class="stat-change positive">↑ Lifetime</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-scroll-animate="slide-in-up">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));">💰</div>
            <div class="stat-label">Total Earnings</div>
            <div class="stat-value">$<?= number_format($totalEarnings, 2) ?></div>
            <div class="stat-change positive">↑ All time</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-scroll-animate="slide-in-up">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--gold),var(--warning));">📤</div>
            <div class="stat-label">Total Withdrawn</div>
            <div class="stat-value">$<?= number_format($totalWithdrawn, 2) ?></div>
            <div class="stat-change positive"><?= $totalWithdrawn > 0 ? '✓' : '—' ?></div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-scroll-animate="slide-in-up">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--royal-purple),var(--electric-blue));">⭐</div>
            <div class="stat-label">User Level</div>
            <div class="stat-value"><?= $level ?></div>
            <div class="stat-change positive"><?= number_format($xp) ?> XP</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-scroll-animate="slide-in-up">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--cyan-accent),var(--emerald-green));">👥</div>
            <div class="stat-label">Referrals</div>
            <div class="stat-value"><?= count($referrals) ?></div>
            <div class="stat-change positive">↑ Network</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-scroll-animate="slide-in-up">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--warning),var(--gold));">📊</div>
            <div class="stat-label">Transactions</div>
            <div class="stat-value"><?= count($transactions) ?></div>
            <div class="stat-change">Lifetime</div>
        </div>
    </div>
</div>

<!-- Main Earnings Chart -->
<div class="glass-2" style="padding:28px;margin-bottom:24px;" data-scroll-animate="fade-in">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        <h3>📈 Earnings Trend (30 Days)</h3>
        <div style="display:flex;gap:8px;">
            <span class="badge badge-info">💰 $<?= number_format(array_sum($chartData), 2) ?> total</span>
            <span class="badge badge-primary">📊 <?= round(array_sum($chartData) / max(count($chartData), 1), 4) ?> avg</span>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="earningsTrendChart" height="200"></canvas>
    </div>
</div>

<!-- Breakdowns -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="responsive-grid">
    <!-- Earnings by Type -->
    <div class="glass-2" style="padding:24px;" data-scroll-animate="slide-in-left">
        <h3 style="margin-bottom:20px;">📊 Earnings by Type</h3>
        <?php if (empty($earningsByType)): ?>
        <div style="text-align:center;padding:40px;color:var(--current-text-muted);">
            <p>No earnings data yet</p>
        </div>
        <?php else: ?>
        <div class="chart-container">
            <canvas id="typeChart" height="200"></canvas>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;margin-top:16px;">
            <?php 
            $maxTypeValue = max($earningsByType) ?: 1;
            $labels = [
                'ad_reward' => ['📺', 'Ad Rewards'],
                'referral_bonus' => ['👥', 'Referral Bonuses'],
                'mission_reward' => ['⭐', 'Mission Rewards'],
                'daily_reward' => ['🎁', 'Daily Rewards'],
                'activation_bonus' => ['🔐', 'Activation Bonus'],
            ];
            foreach ($earningsByType as $type => $amount): 
                $label = $labels[$type] ?? ['📊', ucfirst(str_replace('_', ' ', $type))];
                $percentage = ($amount / $maxTypeValue) * 100;
            ?>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:0.9rem;"><?= $label[0] ?> <?= $label[1] ?></span>
                    <span style="font-weight:600;color:var(--emerald-green);">$<?= number_format($amount, 4) ?></span>
                </div>
                <div class="progress-container" style="height:6px;border-radius:3px;">
                    <div class="progress-bar" style="width:<?= $percentage ?>%;height:100%;border-radius:3px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Activity Timeline -->
    <div class="glass-2" style="padding:24px;" data-scroll-animate="slide-in-right">
        <h3 style="margin-bottom:20px;">📝 Activity Timeline</h3>
        <?php if (empty($transactions)): ?>
        <div style="text-align:center;padding:40px;color:var(--current-text-muted);">
            <p>No activity yet</p>
        </div>
        <?php else: ?>
        <div style="max-height:400px;overflow-y:auto;">
            <?php foreach (array_slice($transactions, 0, 25) as $tx): 
                $txType = $tx['type'] ?? 'other';
                $icons = ['ad_reward'=>'📺','referral_bonus'=>'👥','mission_reward'=>'⭐','daily_reward'=>'🎁','activation_bonus'=>'🔐','withdrawal'=>'💰'];
                $icon = $icons[$txType] ?? '📊';
                $isPositive = !in_array($txType, ['withdrawal']);
            ?>
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.04);" class="hover-lift">
                <span style="font-size:1.2rem;"><?= $icon ?></span>
                <div style="flex:1;">
                    <div style="font-size:0.9rem;"><?= htmlspecialchars($tx['description'] ?? $txType) ?></div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);"><?= timeAgo($tx['created_at'] ?? '') ?></div>
                </div>
                <div style="font-weight:700;color:<?= $isPositive ? 'var(--emerald-green)' : 'var(--error)' ?>;">
                    <?= $isPositive ? '+' : '-' ?>$<?= number_format(floatval($tx['amount'] ?? 0), 4) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Performance Summary -->
<div class="glass-2" style="padding:28px;margin-top:24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom:16px;">⚡ Performance Summary</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;">
        <div style="padding:20px;background:rgba(255,255,255,0.03);border-radius:12px;text-align:center;">
            <div style="font-size:0.85rem;color:var(--current-text-muted);">Avg. Daily</div>
            <div style="font-size:1.5rem;font-weight:700;margin-top:4px;background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                $<?= number_format($totalEarnings / max(1, count($earningsByDate)), 4) ?>
            </div>
        </div>
        <div style="padding:20px;background:rgba(255,255,255,0.03);border-radius:12px;text-align:center;">
            <div style="font-size:0.85rem;color:var(--current-text-muted);">Today</div>
            <div style="font-size:1.5rem;font-weight:700;margin-top:4px;color:var(--emerald-green);">
                $<?= number_format($todayEarnings, 4) ?>
            </div>
        </div>
        <div style="padding:20px;background:rgba(255,255,255,0.03);border-radius:12px;text-align:center;">
            <div style="font-size:0.85rem;color:var(--current-text-muted);">Conversion</div>
            <div style="font-size:1.5rem;font-weight:700;margin-top:4px;color:var(--electric-blue);">
                <?= $totalAds > 0 ? round(($totalEarnings / $totalAds) * 100, 2) : 0 ?>%
            </div>
        </div>
        <div style="padding:20px;background:rgba(255,255,255,0.03);border-radius:12px;text-align:center;">
            <div style="font-size:0.85rem;color:var(--current-text-muted);">Rank</div>
            <div style="font-size:1.5rem;font-weight:700;margin-top:4px;color:<?= $rank['color'] ?>;">
                <?= $rank['name'] ?>
            </div>
        </div>
        <div style="padding:20px;background:rgba(255,255,255,0.03);border-radius:12px;text-align:center;">
            <div style="font-size:0.85rem;color:var(--current-text-muted);">Ads/Day</div>
            <div style="font-size:1.5rem;font-weight:700;margin-top:4px;">
                <?= count($todayRewards) ?> / <?= DAILY_AD_LIMIT ?>
            </div>
        </div>
        <div style="padding:20px;background:rgba(255,255,255,0.03);border-radius:12px;text-align:center;">
            <div style="font-size:0.85rem;color:var(--current-text-muted);">Withdrawal Rate</div>
            <div style="font-size:1.5rem;font-weight:700;margin-top:4px;color:var(--warning);">
                <?= $totalEarnings > 0 ? round(($totalWithdrawn / $totalEarnings) * 100, 1) : 0 ?>%
            </div>
        </div>
    </div>
</div>

<script>
function drawEarningsChart() {
    const canvas = document.getElementById('earningsTrendChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const rect = canvas.parentElement.getBoundingClientRect();
    const w = rect.width - 40;
    const h = 200;
    const dpr = window.devicePixelRatio || 1;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.scale(dpr, dpr);

    const data = window.__analyticsData?.earnings || [];
    const maxVal = Math.max(...data, 0.001);
    const padding = { top: 20, bottom: 25, left: 50, right: 20 };
    const chartW = w - padding.left - padding.right;
    const chartH = h - padding.top - padding.bottom;
    const stepX = chartW / Math.max(data.length - 1, 1);

    // Grid lines
    ctx.strokeStyle = 'rgba(255,255,255,0.05)';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
        const y = padding.top + (chartH / 4) * i;
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(w - padding.right, y);
        ctx.stroke();
        
        ctx.fillStyle = 'rgba(255,255,255,0.3)';
        ctx.font = '10px system-ui';
        ctx.textAlign = 'right';
        ctx.fillText('$' + (maxVal * (1 - i / 4)).toFixed(2), padding.left - 8, y + 4);
    }

    // Area fill
    const grad = ctx.createLinearGradient(0, padding.top, 0, h - padding.bottom);
    grad.addColorStop(0, 'rgba(67,97,238,0.3)');
    grad.addColorStop(1, 'rgba(67,97,238,0.02)');
    ctx.beginPath();
    data.forEach((val, i) => {
        const x = padding.left + i * stepX;
        const y = padding.top + chartH - (val / maxVal) * chartH;
        i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
    });
    ctx.lineTo(padding.left + (data.length - 1) * stepX, padding.top + chartH);
    ctx.lineTo(padding.left, padding.top + chartH);
    ctx.closePath();
    ctx.fillStyle = grad;
    ctx.fill();

    // Line
    ctx.beginPath();
    data.forEach((val, i) => {
        const x = padding.left + i * stepX;
        const y = padding.top + chartH - (val / maxVal) * chartH;
        i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
    });
    ctx.strokeStyle = '#4361ee';
    ctx.lineWidth = 2.5;
    ctx.lineJoin = 'round';
    ctx.stroke();

    // Dots
    const step = Math.max(1, Math.floor(data.length / 15));
    data.forEach((val, i) => {
        if (i % step !== 0 && i !== data.length - 1) return;
        const x = padding.left + i * stepX;
        const y = padding.top + chartH - (val / maxVal) * chartH;
        ctx.beginPath();
        ctx.arc(x, y, 3.5, 0, Math.PI * 2);
        ctx.fillStyle = '#4361ee';
        ctx.fill();
        ctx.strokeStyle = '#fff';
        ctx.lineWidth = 2;
        ctx.stroke();
    });

    // Labels
    ctx.fillStyle = 'rgba(255,255,255,0.3)';
    ctx.font = '9px system-ui';
    ctx.textAlign = 'center';
    const labels = window.__analyticsData?.labels || [];
    const labelStep = Math.max(1, Math.floor(data.length / 10));
    data.forEach((val, i) => {
        if (i % labelStep !== 0 && i !== data.length - 1) return;
        const x = padding.left + i * stepX;
        ctx.fillText(labels[i] || '', x, h - 6);
    });
}

function drawTypeChart() {
    const canvas = document.getElementById('typeChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const rect = canvas.parentElement.getBoundingClientRect();
    const w = rect.width - 40;
    const h = 200;
    const dpr = window.devicePixelRatio || 1;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.scale(dpr, dpr);

    const types = window.__analyticsData?.types || [];
    const values = window.__analyticsData?.typeValues || [];
    const total = values.reduce((a, b) => a + b, 0) || 1;
    const colors = ['#4361ee', '#06d6a0', '#ffd166', '#7209b7', '#00b4d8', '#ef476f'];
    const cx = w / 2;
    const cy = h / 2;
    const radius = Math.min(cx, cy) - 20;
    
    let startAngle = -Math.PI / 2;
    values.forEach((val, i) => {
        const sliceAngle = (val / total) * Math.PI * 2;
        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, radius, startAngle, startAngle + sliceAngle);
        ctx.closePath();
        ctx.fillStyle = colors[i % colors.length];
        ctx.fill();
        
        // Label
        const midAngle = startAngle + sliceAngle / 2;
        const labelR = radius * 0.65;
        const lx = cx + Math.cos(midAngle) * labelR;
        const ly = cy + Math.sin(midAngle) * labelR;
        ctx.fillStyle = '#fff';
        ctx.font = 'bold 10px system-ui';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(types[i].replace(/_/g, ' ').substring(0, 10), lx, ly);
        
        startAngle += sliceAngle;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        drawEarningsChart();
        drawTypeChart();
    }, 500);
    window.addEventListener('resize', () => {
        drawEarningsChart();
        drawTypeChart();
    });
});
</script>

<style>
@media (max-width: 768px) {
    .responsive-grid { grid-template-columns: 1fr !important; }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
