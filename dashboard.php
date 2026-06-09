<?php
// EARNNOVA - Ultra-Premium Dashboard v4.0
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser();
if (!$user) { header('Location: /login.php'); exit; }

$pageTitle = 'Dashboard';
$showSidebar = true;

$todayRewards = db()->getTodayAdRewards($user['uid']);
$xp = intval($user['xp'] ?? 0);
$level = intval($user['level'] ?? 1);
$rank = getUserRank($xp);
$streak = intval($user['streak_days'] ?? 0);
$achievements = json_decode($user['achievements'] ?? '[]', true);

updateMissionProgress($user['uid'], 'watch_ads', count($todayRewards));
$newAchievements = checkAchievements($user['uid']);
if (!empty($newAchievements)) $user = getCurrentUser();

$todayEarnings = array_sum(array_column($todayRewards, 'reward_amount'));
$totalEarnings = floatval($user['balance'] ?? 0) + floatval($user['referral_balance'] ?? 0);

// Chart data
$chartLabels = []; $chartData = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('D', strtotime($date));
    $dayRewards = db()->request('GET', "/rest/v1/ad_rewards?user_id=eq.{$user['uid']}&created_at=gte.$date 00:00:00&created_at=lte.$date 23:59:59&select=reward_amount");
    $dayAmount = 0;
    if ($dayRewards['success']) foreach ($dayRewards['data'] ?? [] as $r) $dayAmount += floatval($r['reward_amount'] ?? 0);
    $chartData[] = round($dayAmount, 4);
}
$transactions = db()->getTransactions($user['uid']);

$greeting = match ((int)date('H')) { 0..11 => 'Good morning', 12..17 => 'Good afternoon', default => 'Good evening' };
include __DIR__ . '/includes/header.php';
?>
<script id="user-data" type="application/json"><?= json_encode($user) ?></script>
<script>window.__chartData = { labels: <?= json_encode($chartLabels) ?>, earnings: <?= json_encode($chartData) ?> };</script>

<!-- ============================================ -->
<!-- PREMIUM 3D BALANCE CARD v4.0 -->
<!-- ============================================ -->
<div class="card-3d" style="margin-bottom:20px;" data-reveal="fadeInUp">
    <div class="card-3d-inner">
        <div class="card-3d-glow"></div>
        <div class="balance-card-3d" style="border-radius:24px;padding:32px;">
            <div class="balance-pattern"></div>
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;position:relative;z-index:1;">
                <div>
                    <div style="font-size:0.8rem;opacity:0.6;letter-spacing:1px;text-transform:uppercase;margin-bottom:4px;"><?= $greeting ?>, <?= htmlspecialchars($user['username'] ?? 'User') ?> 👋</div>
                    <div style="font-size:0.75rem;opacity:0.5;text-transform:uppercase;letter-spacing:1px;">Lifetime Earnings</div>
                    <div class="balance-amount" data-balance style="font-size:clamp(2.2rem,5vw,3.8rem);font-weight:900;color:white;text-shadow:0 4px 20px rgba(0,0,0,0.3);font-family:'SF Mono','SFMono-Regular',monospace;letter-spacing:0.02em;">
                        $<?= number_format($user['balance'] ?? 0, 2) ?>
                    </div>
                    <div style="display:flex;gap:20px;margin-top:8px;">
                        <span style="font-size:0.85rem;color:var(--mint-emerald);">📈 Today: +$<?= number_format($todayEarnings, 2) ?></span>
                        <span style="font-size:0.85rem;color:var(--current-text-muted);">📅 This week: +$<?= number_format(array_sum($chartData), 2) ?></span>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:12px;">
                        <a href="/ads.php" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:white;border:none;border-radius:20px;padding:8px 16px;font-size:0.8rem;text-decoration:none;">📺 Deposit</a>
                        <a href="/withdrawal.php" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:white;border:none;border-radius:20px;padding:8px 16px;font-size:0.8rem;text-decoration:none;">💰 Withdraw</a>
                        <a href="/plans.php" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:white;border:none;border-radius:20px;padding:8px 16px;font-size:0.8rem;text-decoration:none;">📋 Buy Plan</a>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.85rem;color:rgba(255,255,255,0.6);">👑 <?= $rank['name'] ?> Lv.<?= $level ?></div>
                    <div style="font-size:0.85rem;color:rgba(255,255,255,0.6);">🔥 <?= $streak ?> day streak</div>
                </div>
            </div>
            <div style="display:flex;gap:32px;margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.12);position:relative;z-index:1;flex-wrap:wrap;">
                <div><div style="font-size:0.7rem;opacity:0.5;text-transform:uppercase;letter-spacing:1px;">Referral</div><div class="balance-sub-value" data-referral-balance style="font-size:1.1rem;font-weight:700;color:white;">$<?= number_format($user['referral_balance'] ?? 0, 2) ?></div></div>
                <div><div style="font-size:0.7rem;opacity:0.5;text-transform:uppercase;letter-spacing:1px;">Today</div><div class="balance-sub-value" style="font-size:1.1rem;font-weight:700;color:white;">+$<?= number_format($todayEarnings, 4) ?></div></div>
                <div><div style="font-size:0.7rem;opacity:0.5;text-transform:uppercase;letter-spacing:1px;">Status</div><span class="badge badge-<?= ($user['activation_status'] ?? 'inactive') === 'active' ? 'success' : 'warning' ?>" style="border-radius:20px;"><?= ucfirst($user['activation_status'] ?? 'Inactive') ?></span></div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- EARNINGS BREAKDOWN GRID (2x2) -->
<!-- ============================================ -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;" class="responsive-grid-2">
    <div class="glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp">
        <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Today's Earnings</div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--mint-emerald);margin-top:4px;font-family:'SF Mono',monospace;">+$<?= number_format($todayEarnings, 2) ?></div>
        <div style="font-size:0.8rem;color:var(--current-text-muted);margin-top:4px;">↑ 12% vs yesterday</div>
    </div>
    <div class="glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp" data-reveal-delay="50">
        <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Referral Earnings</div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--neon-coral);margin-top:4px;font-family:'SF Mono',monospace;">$<?= number_format($user['referral_balance'] ?? 0, 2) ?></div>
        <div style="font-size:0.8rem;color:var(--current-text-muted);margin-top:4px;">👥 <?= count(db()->getReferrals($user['uid'])) ?> active users</div>
    </div>
    <div class="glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp" data-reveal-delay="100">
        <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Active Plan</div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--gold);margin-top:4px;">Premium</div>
        <div style="font-size:0.8rem;color:var(--current-text-muted);margin-top:4px;">⏱ 23 days remaining</div>
    </div>
    <div class="glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp" data-reveal-delay="150">
        <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Withdrawal Status</div>
        <div style="font-size:1.6rem;font-weight:800;color:var(--cyber-cyan);margin-top:4px;font-family:'SF Mono',monospace;">$0.00</div>
        <div style="font-size:0.8rem;color:var(--current-text-muted);margin-top:4px;">✅ Completed: $<?= number_format(array_sum(array_column(db()->getWithdrawals($user['uid']), 'amount')), 2) ?></div>
    </div>
</div>

<!-- ============================================ -->
<!-- EARNINGS CHART -->
<!-- ============================================ -->
<div class="glass-2" style="padding:20px;border-radius:20px;margin-bottom:20px;" data-reveal="fadeInUp">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h4 style="font-size:0.9rem;font-weight:600;">📈 Earnings (7 days)</h4>
        <div style="display:flex;gap:6px;">
            <span class="badge badge-primary" style="border-radius:12px;font-size:0.7rem;">Daily</span>
            <span class="badge" style="border-radius:12px;font-size:0.7rem;background:var(--glass-bg-2);color:var(--current-text-muted);">Weekly</span>
        </div>
    </div>
    <div class="chart-container"><canvas id="earningsChart" height="140"></canvas></div>
</div>

<!-- ============================================ -->
<!-- STATS CARDS + RECENT ACTIVITY -->
<!-- ============================================ -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" class="responsive-grid">
    <!-- Stats Grid -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div class="card-3d glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div style="font-size:1.8rem;margin-bottom:4px;">👁️</div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Ads Today</div>
                <div style="font-size:1.5rem;font-weight:800;" data-ads-watched><?= count($todayRewards) ?></div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);">/ <?= DAILY_AD_LIMIT ?> daily limit</div>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp" data-reveal-delay="50">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div style="font-size:1.8rem;margin-bottom:4px;">📊</div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Total Earnings</div>
                <div style="font-size:1.5rem;font-weight:800;">$<?= number_format($totalEarnings, 2) ?></div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);">Lifetime</div>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp" data-reveal-delay="100">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div style="font-size:1.8rem;margin-bottom:4px;">⭐</div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Rank</div>
                <div style="font-size:1.5rem;font-weight:800;background:linear-gradient(135deg,<?= $rank['color'] ?>,var(--cyber-cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent;"><?= $rank['name'] ?></div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);">🔥 <?= number_format($xp) ?> XP · Lv.<?= $level ?></div>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp" data-reveal-delay="150">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div style="font-size:1.8rem;margin-bottom:4px;">🏆</div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);text-transform:uppercase;letter-spacing:1px;">Achievements</div>
                <div style="font-size:1.5rem;font-weight:800;"><?= count($achievements) ?></div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);"><a href="/missions.php" style="color:var(--cyber-cyan);text-decoration:none;">View missions →</a></div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="glass-2" style="padding:20px;border-radius:20px;" data-reveal="fadeInUp">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h4 style="font-size:0.9rem;font-weight:600;">📝 Recent Activity</h4>
            <a href="/analytics.php" style="font-size:0.8rem;color:var(--cyber-cyan);text-decoration:none;">View all →</a>
        </div>
        <?php if (empty($transactions)): ?>
        <div style="text-align:center;padding:30px;color:var(--current-text-muted);">
            <div style="font-size:2.5rem;margin-bottom:8px;">📭</div>
            <p style="font-size:0.9rem;">No activity yet. Start watching ads!</p>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:6px;max-height:350px;overflow-y:auto;">
            <?php foreach (array_slice($transactions, 0, 12) as $tx): 
                $icons = ['ad_reward'=>'📺','referral_bonus'=>'👥','mission_reward'=>'⭐','daily_reward'=>'🎁','activation_bonus'=>'🔐','withdrawal'=>'💰'];
                $icon = $icons[$tx['type'] ?? 'other'] ?? '📊';
            ?>
            <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:rgba(255,255,255,0.02);border-radius:12px;transition:all 0.2s;" class="hover-lift">
                <span style="font-size:1.1rem;"><?= $icon ?></span>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:500;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($tx['description'] ?? ucfirst($tx['type'] ?? 'Activity')) ?></div>
                    <div style="font-size:0.75rem;color:var(--current-text-muted);"><?= timeAgo($tx['created_at'] ?? date('Y-m-d H:i:s')) ?></div>
                </div>
                <div style="font-weight:700;font-size:1rem;color:var(--mint-emerald);white-space:nowrap;">+$<?= number_format(floatval($tx['amount'] ?? 0), 4) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Action Bar -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:20px;" class="responsive-grid-4">
    <a href="/ads.php" class="btn btn-premium btn-primary btn-magnetic" style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));color:white;padding:14px;border-radius:20px;font-weight:600;text-decoration:none;text-align:center;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:0.9rem;">
        <span style="font-size:1.5rem;">📺</span> Watch
    </a>
    <a href="/referral.php" class="btn btn-premium btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:14px;border-radius:20px;font-weight:600;text-decoration:none;text-align:center;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:0.9rem;">
        <span style="font-size:1.5rem;">👥</span> Refer
    </a>
    <a href="/missions.php" class="btn btn-premium btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:14px;border-radius:20px;font-weight:600;text-decoration:none;text-align:center;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:0.9rem;">
        <span style="font-size:1.5rem;">⭐</span> Missions
    </a>
    <a href="/withdrawal.php" class="btn btn-premium btn-magnetic" style="background:linear-gradient(135deg,var(--neon-coral),var(--mint-emerald));color:white;padding:14px;border-radius:20px;font-weight:600;text-decoration:none;text-align:center;display:flex;flex-direction:column;align-items:center;gap:4px;font-size:0.9rem;">
        <span style="font-size:1.5rem;">💰</span> Withdraw
    </a>
</div>

<!-- New Achievement Notification -->
<?php if (!empty($newAchievements)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($newAchievements as $ach): ?>
    setTimeout(function() {
        EARNNOVA.showSuccess('🏆 Achievement Unlocked!', '<?= htmlspecialchars(addslashes($ach['name'] ?? '')) ?> — +<?= intval($ach['xp'] ?? 0) ?> XP, +$<?= number_format(floatval($ach['coins'] ?? 0), 2) ?>');
    }, 1000);
    <?php endforeach; ?>
});
</script>
<?php endif; ?>

<!-- Banner Ad -->
<div style="max-width:728px;margin:24px auto;text-align:center;">
    <div class="glass-card" style="padding:10px;border-radius:20px;">
        <div style="font-size:0.65rem;color:var(--current-text-muted);margin-bottom:4px;">Advertisement</div>
        <script>
        atOptions = { 'key' : 'b93981a2156c99e7ad6889545f4412c0', 'format' : 'iframe', 'height' : 50, 'width' : 320, 'params' : {} };
        </script>
        <script src="https://intermediatenormalconfederate.com/b93981a2156c99e7ad6889545f4412c0/invoke.js" async></script>
    </div>
</div>

<script>
function drawMiniChart(canvasId, data, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.parentElement.getBoundingClientRect();
    const w = rect.width - 32;
    const h = 140;
    canvas.width = w * dpr; canvas.height = h * dpr;
    canvas.style.width = w + 'px'; canvas.style.height = h + 'px';
    ctx.scale(dpr, dpr);
    const maxVal = Math.max(...data, 0.001);
    const pad = { top: 10, bottom: 20, left: 0, right: 0 };
    const cw = w - pad.left - pad.right, ch = h - pad.top - pad.bottom;
    const stepX = cw / Math.max(data.length - 1, 1);
    const grad = ctx.createLinearGradient(0, pad.top, 0, h - pad.bottom);
    grad.addColorStop(0, color + '50'); grad.addColorStop(1, color + '02');
    ctx.beginPath();
    data.forEach((val, i) => { const x = pad.left + i * stepX, y = pad.top + ch - (val / maxVal) * ch; i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y); });
    ctx.lineTo(pad.left + (data.length - 1) * stepX, pad.top + ch); ctx.lineTo(pad.left, pad.top + ch); ctx.closePath();
    ctx.fillStyle = grad; ctx.fill();
    ctx.beginPath();
    data.forEach((val, i) => { const x = pad.left + i * stepX, y = pad.top + ch - (val / maxVal) * ch; i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y); });
    ctx.strokeStyle = color; ctx.lineWidth = 2.5; ctx.lineJoin = 'round'; ctx.stroke();
    data.forEach((val, i) => { const x = pad.left + i * stepX, y = pad.top + ch - (val / maxVal) * ch; ctx.beginPath(); ctx.arc(x, y, 3, 0, Math.PI * 2); ctx.fillStyle = '#fff'; ctx.fill(); ctx.strokeStyle = color; ctx.lineWidth = 2; ctx.stroke(); });
    ctx.fillStyle = 'rgba(255,255,255,0.3)'; ctx.font = '9px system-ui'; ctx.textAlign = 'center';
    const labels = window.__chartData?.labels || [];
    data.forEach((val, i) => { const x = pad.left + i * stepX; ctx.fillText(labels[i] || '', x, h - 5); });
}
document.addEventListener('DOMContentLoaded', function() {
    const cd = window.__chartData || { earnings: [] };
    setTimeout(() => drawMiniChart('earningsChart', cd.earnings, '#00F0FF'), 300);
    window.addEventListener('resize', () => drawMiniChart('earningsChart', cd.earnings, '#00F0FF'));
});
</script>

<style>
.responsive-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.responsive-grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
@media (max-width: 768px) { .responsive-grid { grid-template-columns:1fr !important; } .responsive-grid-2 { grid-template-columns:1fr; } .responsive-grid-4 { grid-template-columns:repeat(2,1fr); } }
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
