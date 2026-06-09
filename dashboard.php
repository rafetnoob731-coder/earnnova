<?php
// EARNNOVA - Premium User Dashboard
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser();
if (!$user) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'Dashboard';
$showSidebar = true;

// Get today's earnings
$todayRewards = db()->getTodayAdRewards($user['uid']);

// Gamification stats
$xp = intval($user['xp'] ?? 0);
$level = intval($user['level'] ?? 1);
$rank = getUserRank($xp);
$streak = intval($user['streak_days'] ?? 0);
$achievements = json_decode($user['achievements'] ?? '[]', true);

updateMissionProgress($user['uid'], 'watch_ads', count($todayRewards));

$newAchievements = checkAchievements($user['uid']);
if (!empty($newAchievements)) {
    $user = getCurrentUser();
}
$todayEarnings = 0;
foreach ($todayRewards as $reward) {
    $todayEarnings += floatval($reward['reward_amount'] ?? 0);
}

$totalEarnings = floatval($user['balance'] ?? 0) + floatval($user['referral_balance'] ?? 0);

// Chart data: last 7 days
$chartLabels = [];
$chartData = [];
$referralChartData = [];
$withdrawalChartData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('D', strtotime($date));
    $dayRewards = db()->request('GET', "/rest/v1/ad_rewards?user_id=eq.{$user['uid']}&created_at=gte.$date 00:00:00&created_at=lte.$date 23:59:59&select=reward_amount");
    $dayAmount = 0;
    if ($dayRewards['success']) {
        foreach ($dayRewards['data'] ?? [] as $r) {
            $dayAmount += floatval($r['reward_amount'] ?? 0);
        }
    }
    $chartData[] = round($dayAmount, 4);
    
    // Referral earnings per day
    $refDay = db()->request('GET', "/rest/v1/transactions?user_id=eq.{$user['uid']}&type=eq.referral_bonus&created_at=gte.$date 00:00:00&created_at=lte.$date 23:59:59&select=amount");
    $refAmount = 0;
    if ($refDay['success']) {
        foreach ($refDay['data'] ?? [] as $r) {
            $refAmount += floatval($r['amount'] ?? 0);
        }
    }
    $referralChartData[] = round($refAmount, 4);
    
    // Withdrawals
    $wdDay = db()->request('GET', "/rest/v1/withdrawals?user_id=eq.{$user['uid']}&created_at=gte.$date 00:00:00&created_at=lte.$date 23:59:59&select=amount");
    $wdAmount = 0;
    if ($wdDay['success']) {
        foreach ($wdDay['data'] ?? [] as $r) {
            $wdAmount += floatval($r['amount'] ?? 0);
        }
    }
    $withdrawalChartData[] = round($wdAmount, 4);
}

$transactions = db()->getTransactions($user['uid']);

include __DIR__ . '/includes/header.php';
?>

<!-- User Data for JavaScript -->
<script id="user-data" type="application/json"><?= json_encode($user) ?></script>
<script>
window.__chartData = {
    labels: <?= json_encode($chartLabels) ?>,
    earnings: <?= json_encode($chartData) ?>,
    referrals: <?= json_encode($referralChartData) ?>,
    withdrawals: <?= json_encode($withdrawalChartData) ?>
};
</script>

<!-- ============================================ -->
<!-- PREMIUM 3D BALANCE CARD -->
<!-- ============================================ -->
<div class="card-3d" style="margin-bottom:24px;" data-reveal="fadeInUp">
    <div class="card-3d-inner">
        <div class="card-3d-glow"></div>
        <div class="card-3d-shine"></div>
        <div class="balance-card-3d">
            <div class="balance-pattern"></div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:20px;position:relative;z-index:1;">
                <div>
                    <div class="balance-label" style="font-size:0.9rem;opacity:0.7;margin-bottom:8px;letter-spacing:1px;text-transform:uppercase;">Available Balance</div>
                    <div class="balance-amount" data-balance style="font-size:clamp(2rem,4vw,3.5rem);font-weight:900;color:white;text-shadow:0 4px 20px rgba(0,0,0,0.3);">
                        $<?= number_format($user['balance'] ?? 0, 2) ?>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.85rem;color:rgba(255,255,255,0.6);">👑 <?= $rank['name'] ?> Level <?= $level ?></div>
                    <div style="font-size:0.85rem;color:rgba(255,255,255,0.6);">🔥 <?= $streak ?> day streak</div>
                </div>
            </div>
            <div style="display:flex;gap:32px;margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.15);position:relative;z-index:1;flex-wrap:wrap;">
                <div>
                    <div class="balance-sub-label" style="font-size:0.75rem;opacity:0.6;text-transform:uppercase;letter-spacing:1px;">Referral Balance</div>
                    <div class="balance-sub-value" data-referral-balance style="font-size:1.2rem;font-weight:700;color:white;">$<?= number_format($user['referral_balance'] ?? 0, 2) ?></div>
                </div>
                <div>
                    <div class="balance-sub-label" style="font-size:0.75rem;opacity:0.6;text-transform:uppercase;letter-spacing:1px;">Today's Earnings</div>
                    <div class="balance-sub-value" style="font-size:1.2rem;font-weight:700;color:white;">$<?= number_format($todayEarnings, 4) ?></div>
                </div>
                <div>
                    <div class="balance-sub-label" style="font-size:0.75rem;opacity:0.6;text-transform:uppercase;letter-spacing:1px;">Account Status</div>
                    <div class="balance-sub-value">
                        <span class="badge badge-<?= ($user['activation_status'] ?? 'inactive') === 'active' ? 'success' : 'warning' ?>">
                            <?= ucfirst($user['activation_status'] ?? 'Inactive') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:24px;" class="responsive-grid-3">
    <div class="glass-2" style="padding:20px;" data-reveal="fadeInUp">
        <h4 style="font-size:0.9rem;margin-bottom:12px;display:flex;align-items:center;gap:6px;">📈 Earnings (7d)</h4>
        <div class="chart-container"><canvas id="earningsChart" height="120"></canvas></div>
    </div>
    <div class="glass-2" style="padding:20px;" data-reveal="fadeInUp" data-reveal-delay="100">
        <h4 style="font-size:0.9rem;margin-bottom:12px;display:flex;align-items:center;gap:6px;">👥 Referrals (7d)</h4>
        <div class="chart-container"><canvas id="referralChart" height="120"></canvas></div>
    </div>
    <div class="glass-2" style="padding:20px;" data-reveal="fadeInUp" data-reveal-delay="200">
        <h4 style="font-size:0.9rem;margin-bottom:12px;display:flex;align-items:center;gap:6px;">💰 Withdrawals (7d)</h4>
        <div class="chart-container"><canvas id="withdrawalChart" height="120"></canvas></div>
    </div>
</div>

<!-- ============================================ -->
<!-- PREMIUM STATISTICS CARDS (3D) -->
<!-- ============================================ -->
<div class="dashboard-grid">
    <div class="card-3d glass-2 stat-card" data-reveal="fadeInUp" data-reveal-delay="0">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));">👁️</div>
            <div class="stat-label">Ads Watched Today</div>
            <div class="stat-value premium-value" data-ads-watched><?= count($todayRewards) ?></div>
            <div class="stat-change positive">↑ Daily Limit: <?= DAILY_AD_LIMIT ?></div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-reveal="fadeInUp" data-reveal-delay="100">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));">📊</div>
            <div class="stat-label">Total Earnings</div>
            <div class="stat-value" data-total-earnings>$<?= number_format($totalEarnings, 2) ?></div>
            <div class="stat-change positive">↑ Lifetime earnings</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-reveal="fadeInUp" data-reveal-delay="200">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--gold),var(--warning));">👥</div>
            <div class="stat-label">Referrals</div>
            <div class="stat-value"><?= count(db()->getReferrals($user['uid'])) ?></div>
            <div class="stat-change positive">↑ Invite friends</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-reveal="fadeInUp" data-reveal-delay="300">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,<?= $rank['color'] ?>,var(--royal-purple));">⭐</div>
            <div class="stat-label">User Rank</div>
            <div class="stat-value" style="background:linear-gradient(135deg,<?= $rank['color'] ?>,var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;"><?= $rank['name'] ?> Lv.<?= $level ?></div>
            <div class="stat-change positive">🔥 <?= number_format($xp) ?> XP · <?= $streak ?> day streak</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-reveal="fadeInUp" data-reveal-delay="400">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--gold),var(--warning));">🏆</div>
            <div class="stat-label">Achievements</div>
            <div class="stat-value"><?= count($achievements) ?></div>
            <div class="stat-change positive">↑ <a href="/missions.php" style="color:var(--electric-blue);text-decoration:none;">View missions</a></div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card" data-reveal="fadeInUp" data-reveal-delay="500">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--electric-blue),var(--royal-purple));">🔐</div>
            <div class="stat-label">Account</div>
            <div class="stat-value" data-status><?= ucfirst($user['activation_status'] ?? 'Inactive') ?></div>
            <div class="stat-change <?= ($user['activation_status'] ?? 'inactive') === 'active' ? 'positive' : 'negative' ?>">
                <?= ($user['activation_status'] ?? 'inactive') === 'active' ? '✓ Activated' : '⚠ Not Activated' ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Activity -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="responsive-grid">
    <div class="card-3d glass-2" style="padding:28px;" data-reveal="fadeInUp" data-reveal-delay="0">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="card-3d-shine"></div>
            <h3 style="margin-bottom:20px;font-size:1.2rem;display:flex;align-items:center;gap:8px;">⚡ Quick Actions</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <a href="/ads.php" class="btn btn-premium btn-primary btn-magnetic" style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));color:white;padding:14px 20px;border-radius:10px;font-weight:600;text-decoration:none;text-align:center;">📺 Watch Ads</a>
                <a href="/missions.php" class="btn btn-premium btn-magnetic" style="background:linear-gradient(135deg,var(--gold),var(--warning));color:#1a1a2e;padding:14px 20px;border-radius:10px;font-weight:600;text-decoration:none;text-align:center;">⭐ Missions</a>
                <a href="/referral.php" class="btn btn-premium btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:14px 20px;border-radius:10px;font-weight:600;text-decoration:none;text-align:center;">👥 Referrals</a>
                <a href="/analytics.php" class="btn btn-premium btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:14px 20px;border-radius:10px;font-weight:600;text-decoration:none;text-align:center;">📊 Analytics</a>
                <a href="/withdrawal.php" class="btn btn-premium btn-magnetic" style="background:linear-gradient(135deg,var(--emerald-green),#0bb5a0);color:white;padding:14px 20px;border-radius:10px;font-weight:600;text-decoration:none;text-align:center;">💰 Withdraw</a>
                <a href="/tasks.php" class="btn btn-premium btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:14px 20px;border-radius:10px;font-weight:600;text-decoration:none;text-align:center;">📋 Tasks</a>
            </div>

            <?php $cooldownCheck = db()->checkCooldown($user['uid']); ?>
            <?php if ($cooldownCheck > 0): ?>
            <div class="cooldown-timer" style="margin-top:16px;">
                ⏳ Cooldown active: <span class="timer" data-cooldown>30s</span>
            </div>
            <?php endif; ?>

            <div style="margin-top:20px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:0.9rem;color:var(--current-text-secondary);">Daily Ad Progress</span>
                    <span style="font-size:0.9rem;font-weight:600;"><?= count($todayRewards) ?> / <?= DAILY_AD_LIMIT ?></span>
                </div>
                <div class="progress-container" style="height:8px;border-radius:4px;background:rgba(255,255,255,0.08);">
                    <div class="progress-bar" style="width:<?= min(100, (count($todayRewards) / DAILY_AD_LIMIT) * 100) ?>%;height:100%;border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-3d glass-2" style="padding:28px;" data-reveal="fadeInUp" data-reveal-delay="100">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="card-3d-shine"></div>
            <h3 style="margin-bottom:20px;font-size:1.2rem;display:flex;align-items:center;gap:8px;">📝 Recent Activity</h3>
            <?php if (empty($transactions)): ?>
            <div style="text-align:center;padding:40px 0;color:var(--current-text-muted);">
                <div style="font-size:3rem;margin-bottom:16px;">📭</div>
                <p>No activity yet. Start watching ads!</p>
            </div>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;max-height:400px;overflow-y:auto;">
                <?php foreach (array_slice($transactions, 0, 15) as $tx): 
                    $icons = ['ad_reward'=>'📺','referral_bonus'=>'👥','mission_reward'=>'⭐','daily_reward'=>'🎁','activation_bonus'=>'🔐','withdrawal'=>'💰'];
                    $icon = $icons[$tx['type'] ?? 'other'] ?? '📊';
                ?>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:rgba(255,255,255,0.03);border-radius:10px;transition:all 0.3s ease;" class="hover-lift">
                    <span style="font-size:1.2rem;"><?= $icon ?></span>
                    <div style="flex:1;">
                        <div style="font-weight:500;font-size:0.9rem;"><?= htmlspecialchars($tx['description'] ?? ucfirst($tx['type'] ?? 'Activity')) ?></div>
                        <div style="font-size:0.8rem;color:var(--current-text-muted);"><?= timeAgo($tx['created_at'] ?? date('Y-m-d H:i:s')) ?></div>
                    </div>
                    <div style="font-weight:700;font-size:1.1rem;color:var(--emerald-green);">
                        +$<?= number_format(floatval($tx['amount'] ?? 0), 4) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- New Achievement Notification -->
<?php if (!empty($newAchievements)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($newAchievements as $ach): ?>
    setTimeout(function() {
        EARNNOVA.showSuccess(
            '🏆 Achievement Unlocked!',
            '<?= htmlspecialchars(addslashes($ach['name'] ?? '')) ?> — +<?= intval($ach['xp'] ?? 0) ?> XP, +$<?= number_format(floatval($ach['coins'] ?? 0), 2) ?>'
        );
    }, 1000);
    <?php endforeach; ?>
});
</script>
<?php endif; ?>

<!-- Middle Banner Ad -->
<div style="max-width:728px;margin:30px auto;text-align:center;">
    <div class="glass-card" style="padding:12px;">
        <div style="font-size:0.7rem;color:var(--current-text-muted);margin-bottom:4px;">Advertisement</div>
        <script>
        atOptions = {
            'key' : 'b93981a2156c99e7ad6889545f4412c0',
            'format' : 'iframe',
            'height' : 50,
            'width' : 320,
            'params' : {}
        };
        </script>
        <script src="https://intermediatenormalconfederate.com/b93981a2156c99e7ad6889545f4412c0/invoke.js" async></script>
    </div>
</div>

<script>
// Canvas-based charts
function drawMiniChart(canvasId, data, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.parentElement.getBoundingClientRect();
    const w = rect.width - 40;
    const h = 120;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.scale(dpr, dpr);

    const maxVal = Math.max(...data, 0.001);
    const padding = { top: 10, bottom: 20, left: 0, right: 0 };
    const chartW = w - padding.left - padding.right;
    const chartH = h - padding.top - padding.bottom;
    const stepX = chartW / Math.max(data.length - 1, 1);

    // Gradient fill
    const grad = ctx.createLinearGradient(0, padding.top, 0, h - padding.bottom);
    grad.addColorStop(0, color + '60');
    grad.addColorStop(1, color + '05');
    
    // Area
    ctx.beginPath();
    data.forEach((val, i) => {
        const x = padding.left + i * stepX;
        const y = padding.top + chartH - (val / maxVal) * chartH;
        i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
    });
    const lastX = padding.left + (data.length - 1) * stepX;
    ctx.lineTo(lastX, padding.top + chartH);
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
    ctx.strokeStyle = color;
    ctx.lineWidth = 2.5;
    ctx.lineJoin = 'round';
    ctx.stroke();

    // Dots
    data.forEach((val, i) => {
        const x = padding.left + i * stepX;
        const y = padding.top + chartH - (val / maxVal) * chartH;
        ctx.beginPath();
        ctx.arc(x, y, 3, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.stroke();
    });

    // Labels
    ctx.fillStyle = 'rgba(255,255,255,0.4)';
    ctx.font = '10px system-ui';
    ctx.textAlign = 'center';
    const labels = window.__chartData?.labels || [];
    data.forEach((val, i) => {
        const x = padding.left + i * stepX;
        ctx.fillText(labels[i] || '', x, h - 4);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const cd = window.__chartData || { earnings: [], referrals: [], withdrawals: [] };
    setTimeout(() => {
        drawMiniChart('earningsChart', cd.earnings, '#4361ee');
        drawMiniChart('referralChart', cd.referrals, '#06d6a0');
        drawMiniChart('withdrawalChart', cd.withdrawals, '#ffd166');
    }, 500);
    
    // Resize handler
    window.addEventListener('resize', () => {
        drawMiniChart('earningsChart', cd.earnings, '#4361ee');
        drawMiniChart('referralChart', cd.referrals, '#06d6a0');
        drawMiniChart('withdrawalChart', cd.withdrawals, '#ffd166');
    });
});
</script>

<style>
@media (max-width: 768px) {
    .responsive-grid { grid-template-columns: 1fr !important; }
    .responsive-grid-3 { grid-template-columns: 1fr !important; }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
