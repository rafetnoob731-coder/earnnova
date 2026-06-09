<?php
// EARNNOVA - Analytics Dashboard
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();
$uid = $user['uid'];

$pageTitle = 'Analytics';
$showSidebar = true;

// Collect all stats
$totalAds = 0;
$adsResult = db()->request('GET', "/rest/v1/ad_rewards?user_id=eq.$uid&select=id");
if ($adsResult['success']) $totalAds = count($adsResult['data'] ?? []);

$todayRewards = db()->getTodayAdRewards($uid);
$todayEarnings = array_sum(array_column($todayRewards, 'reward_amount'));

$transactions = db()->getTransactions($uid);
$referrals = db()->getReferrals($uid);

$totalEarnings = floatval($user['balance'] ?? 0) + floatval($user['referral_balance'] ?? 0);

// Group transactions by type
$earningsByType = [];
$earningsByDate = [];
foreach ($transactions as $tx) {
    $type = $tx['type'] ?? 'other';
    $earningsByType[$type] = ($earningsByType[$type] ?? 0) + floatval($tx['amount'] ?? 0);
    
    $date = date('Y-m-d', strtotime($tx['created_at'] ?? 'today'));
    $earningsByDate[$date] = ($earningsByDate[$date] ?? 0) + floatval($tx['amount'] ?? 0);
}

// XP and level
$xp = intval($user['xp'] ?? 0);
$level = intval($user['level'] ?? 1);
$rank = getUserRank($xp);

// Get withdrawal stats
$withdrawals = db()->getWithdrawals($uid);
$totalWithdrawn = array_sum(array_column($withdrawals, 'amount'));
$pendingWithdrawals = array_filter($withdrawals, fn($w) => ($w['status'] ?? '') === 'pending');

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">📊 Analytics</h1>
        <p class="page-subtitle">Track your performance and earnings</p>
    </div>
</div>

<!-- Overview Stats -->
<div class="dashboard-grid stagger-children">
    <div class="glass-card stat-card" data-scroll-animate="slide-in-up">
        <div class="stat-icon">👁️</div>
        <div class="stat-label">Total Ads Watched</div>
        <div class="stat-value"><?= number_format($totalAds) ?></div>
        <div class="stat-change positive">↑ Lifetime total</div>
    </div>
    <div class="glass-card stat-card" data-scroll-animate="slide-in-up">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Total Earnings</div>
        <div class="stat-value">$<?= number_format($totalEarnings, 2) ?></div>
        <div class="stat-change positive">↑ All time</div>
    </div>
    <div class="glass-card stat-card" data-scroll-animate="slide-in-up">
        <div class="stat-icon">📤</div>
        <div class="stat-label">Total Withdrawn</div>
        <div class="stat-value">$<?= number_format($totalWithdrawn, 2) ?></div>
        <div class="stat-change <?= $totalWithdrawn > 0 ? 'positive' : '' ?>"><?= $totalWithdrawn > 0 ? '✓' : 'No withdrawals yet' ?></div>
    </div>
    <div class="glass-card stat-card" data-scroll-animate="slide-in-up">
        <div class="stat-icon">⭐</div>
        <div class="stat-label">User Level</div>
        <div class="stat-value"><?= $level ?></div>
        <div class="stat-change positive"><?= number_format($xp) ?> XP</div>
    </div>
    <div class="glass-card stat-card" data-scroll-animate="slide-in-up">
        <div class="stat-icon">👥</div>
        <div class="stat-label">Referrals</div>
        <div class="stat-value"><?= count($referrals) ?></div>
        <div class="stat-change positive">↑ <?= count(array_filter($referrals, fn($r) => date('Y-m-d', strtotime($r['created_at'] ?? '0000')) === date('Y-m-d'))) ?> today</div>
    </div>
    <div class="glass-card stat-card" data-scroll-animate="slide-in-up">
        <div class="stat-icon">📊</div>
        <div class="stat-label">Transactions</div>
        <div class="stat-value"><?= count($transactions) ?></div>
        <div class="stat-change">Lifetime activity</div>
    </div>
</div>

<!-- Earnings Breakdown -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;" class="responsive-grid">
    <!-- Earnings by Type -->
    <div class="glass-card" style="padding: 24px;" data-scroll-animate="slide-in-left">
        <h3 style="margin-bottom: 20px;">Earnings by Type</h3>
        <?php if (empty($earningsByType)): ?>
        <div style="text-align: center; padding: 40px; color: var(--current-text-muted);">
            <p>No earnings data yet</p>
        </div>
        <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php 
            $maxTypeValue = max($earningsByType) ?: 1;
            foreach ($earningsByType as $type => $amount): 
                $percentage = ($amount / $maxTypeValue) * 100;
                $labels = [
                    'ad_reward' => ['📺', 'Ad Rewards'],
                    'referral_bonus' => ['👥', 'Referral Bonuses'],
                    'mission_reward' => ['⭐', 'Mission Rewards'],
                    'daily_reward' => ['🎁', 'Daily Rewards'],
                    'activation_bonus' => ['🔐', 'Activation Bonus'],
                    'withdrawal' => ['💰', 'Withdrawals'],
                ];
                $label = $labels[$type] ?? ['📊', ucfirst(str_replace('_', ' ', $type))];
            ?>
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.9rem;"><?= $label[0] ?> <?= $label[1] ?></span>
                    <span style="font-weight: 600; color: var(--emerald-green);">$<?= number_format($amount, 4) ?></span>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: <?= $percentage ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity Timeline -->
    <div class="glass-card" style="padding: 24px;" data-scroll-animate="slide-in-right">
        <h3 style="margin-bottom: 20px;">Activity Timeline</h3>
        <?php if (empty($transactions)): ?>
        <div style="text-align: center; padding: 40px; color: var(--current-text-muted);">
            <p>No activity yet</p>
        </div>
        <?php else: ?>
        <div style="max-height: 400px; overflow-y: auto;">
            <?php foreach (array_slice($transactions, 0, 20) as $tx): 
                $txType = $tx['type'] ?? 'other';
                $icons = [
                    'ad_reward' => '📺',
                    'referral_bonus' => '👥',
                    'mission_reward' => '⭐',
                    'daily_reward' => '🎁',
                    'activation_bonus' => '🔐',
                    'withdrawal' => '💰',
                ];
                $icon = $icons[$txType] ?? '📊';
                $isPositive = !in_array($txType, ['withdrawal']);
            ?>
            <div style="display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span style="font-size: 1.2rem;"><?= $icon ?></span>
                <div style="flex: 1;">
                    <div style="font-size: 0.9rem;"><?= htmlspecialchars($tx['description'] ?? $txType) ?></div>
                    <div style="font-size: 0.8rem; color: var(--current-text-muted);"><?= timeAgo($tx['created_at'] ?? '') ?></div>
                </div>
                <div style="font-weight: 700; color: <?= $isPositive ? 'var(--emerald-green)' : 'var(--error)' ?>;">
                    <?= $isPositive ? '+' : '-' ?>$<?= number_format(floatval($tx['amount'] ?? 0), 4) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Performance Summary -->
<div class="glass-card" style="padding: 24px; margin-top: 24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom: 16px;">📈 Performance Summary</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
        <div style="padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Avg. Daily Earnings</div>
            <div style="font-size: 1.3rem; font-weight: 700; margin-top: 4px;">
                $<?= number_format($totalEarnings / max(1, count($earningsByDate)), 4) ?>
            </div>
        </div>
        <div style="padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Today's Earnings</div>
            <div style="font-size: 1.3rem; font-weight: 700; margin-top: 4px; color: var(--emerald-green);">
                $<?= number_format($todayEarnings, 4) ?>
            </div>
        </div>
        <div style="padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Conversion Rate</div>
            <div style="font-size: 1.3rem; font-weight: 700; margin-top: 4px; color: var(--electric-blue);">
                <?= $totalAds > 0 ? round(($totalEarnings / $totalAds) * 100, 2) : 0 ?>%
            </div>
        </div>
        <div style="padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Rank Progress</div>
            <div style="font-size: 1.3rem; font-weight: 700; margin-top: 4px; color: <?= $rank['color'] ?>;">
                <?= $rank['name'] ?> (Lv.<?= $level ?>)
            </div>
        </div>
        <div style="padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Ads per Session</div>
            <div style="font-size: 1.3rem; font-weight: 700; margin-top: 4px;">
                <?= count($todayRewards) ?> / <?= DAILY_AD_LIMIT ?>
            </div>
        </div>
        <div style="padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px; text-align: center;">
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Withdrawal Rate</div>
            <div style="font-size: 1.3rem; font-weight: 700; margin-top: 4px; color: var(--warning);">
                <?= $totalEarnings > 0 ? round(($totalWithdrawn / $totalEarnings) * 100, 1) : 0 ?>%
            </div>
        </div>
    </div>
</div>

<!-- Earnings Chart (simple visual) -->
<div class="glass-card" style="padding: 24px; margin-top: 24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom: 16px;">📅 Earnings History (Last 7 Days)</h3>
    <?php 
    $last7Days = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $last7Days[$date] = $earningsByDate[$date] ?? 0;
    }
    $maxDayEarnings = max($last7Days) ?: 1;
    ?>
    <div style="display: flex; align-items: flex-end; gap: 8px; height: 120px; padding: 20px 0;">
        <?php foreach ($last7Days as $date => $amount): 
            $height = ($amount / $maxDayEarnings) * 100;
            $dayLabel = date('D', strtotime($date));
        ?>
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="font-size: 0.75rem; color: var(--current-text-muted);">$<?= number_format($amount, 2) ?></div>
            <div style="width: 100%; height: <?= max(4, $height) ?>px; background: var(--gradient-primary); border-radius: 4px 4px 0 0; transition: height 0.5s ease; min-height: 4px;"></div>
            <div style="font-size: 0.75rem; color: var(--current-text-muted);"><?= $dayLabel ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .responsive-grid { grid-template-columns: 1fr !important; }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
