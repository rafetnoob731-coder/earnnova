<?php
// EARNNOVA - Daily Missions & Gamification
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();
$uid = $user['uid'];

$pageTitle = 'Missions';
$showSidebar = true;

// Handle daily reward claim
$dailyResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_daily'])) {
    $dailyResult = claimDailyReward($uid);
    $user = getCurrentUser(); // Refresh
}

// Get missions
$today = date('Y-m-d');
$missionsResult = db()->request('GET', '/rest/v1/missions?is_daily=eq.true&is_active=eq.true&select=*');
$missions = $missionsResult['success'] ? ($missionsResult['data'] ?? []) : [];

// Get user progress for today's missions
$progressResult = db()->request('GET', "/rest/v1/user_missions?user_id=eq.$uid&date_assigned=eq.$today&select=*");
$userMissions = [];
if ($progressResult['success']) {
    foreach ($progressResult['data'] ?? [] as $p) {
        $userMissions[$p['mission_id']] = $p;
    }
}

// Get achievements
$achievements = json_decode($user['achievements'] ?? '[]', true);

// Get rank info
$xp = intval($user['xp'] ?? 0);
$level = intval($user['level'] ?? 1);
$rank = getUserRank($xp);
$nextRank = getNextRank($xp);
$streak = intval($user['streak_days'] ?? 0);

// Check for new achievements on page load
$newAchievements = checkAchievements($uid);
if (!empty($newAchievements)) {
    $user = getCurrentUser(); // Refresh again
    $achievements = json_decode($user['achievements'] ?? '[]', true);
}

include __DIR__ . '/includes/header.php';
?>

<!-- User XP & Rank Data for JS -->
<script id="user-data" type="application/json">
<?= json_encode($user) ?>
</script>

<!-- Rank & Level Header -->
<div class="glass-card" style="padding: 24px; margin-bottom: 24px;" data-scroll-animate="fade-in">
    <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
        <!-- Rank Badge -->
        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, <?= $rank['color'] ?>, rgba(255,255,255,0.2)); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 900; color: white; box-shadow: 0 0 30px <?= $rank['color'] ?>66;">
            <?= $rank['level'] ?>
        </div>
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <h2 style="font-size: 1.5rem;"><?= $rank['name'] ?> Rank</h2>
                <span class="badge badge-primary">Level <?= $level ?></span>
            </div>
            <div style="margin-top: 12px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 0.85rem; color: var(--current-text-secondary);">XP: <?= number_format($xp) ?></span>
                    <?php if ($nextRank): ?>
                    <span style="font-size: 0.85rem; color: var(--current-text-muted);">Next: <?= $nextRank['name'] ?> (<?= number_format($nextRank['min_xp']) ?> XP)</span>
                    <?php endif; ?>
                </div>
                <div class="progress-container">
                    <?php 
                    $currentMin = $rank['min_xp'];
                    $nextMin = $nextRank['min_xp'] ?? ($currentMin + 25000);
                    $progress = ($xp - $currentMin) / max(1, ($nextMin - $currentMin)) * 100;
                    ?>
                    <div class="progress-bar" style="width: <?= min(100, $progress) ?>%"></div>
                </div>
            </div>
            <div style="display: flex; gap: 24px; margin-top: 16px;">
                <div><span style="color: var(--current-text-muted); font-size: 0.85rem;">Streak</span> <strong>🔥 <?= $streak ?> days</strong></div>
                <div><span style="color: var(--current-text-muted); font-size: 0.85rem;">Missions Done</span> <strong>⭐ <?= intval($user['total_missions_completed'] ?? 0) ?></strong></div>
                <div><span style="color: var(--current-text-muted); font-size: 0.85rem;">Achievements</span> <strong>🏆 <?= count($achievements) ?></strong></div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Reward Claim -->
<div class="glass-card" style="padding: 24px; margin-bottom: 24px;" data-scroll-animate="fade-in">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="font-size: 1.2rem;">🎁 Daily Reward</h3>
            <p style="color: var(--current-text-secondary); font-size: 0.9rem;">Day <?= min($streak + 1, 7) ?> / 7 streak rewards</p>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 700;" id="dailyXp">+<?= [50, 75, 100, 150, 200, 250, 500][min($streak, 6)] ?></div>
                <div style="font-size: 0.75rem; color: var(--current-text-muted);">XP</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.2rem; font-weight: 700; color: var(--emerald-green);" id="dailyCoins">+$<?= number_format([0.01, 0.02, 0.03, 0.05, 0.08, 0.10, 0.25][min($streak, 6)], 2) ?></div>
                <div style="font-size: 0.75rem; color: var(--current-text-muted);">Coins</div>
            </div>
            <form method="POST">
                <button type="submit" name="claim_daily" class="btn btn-primary btn-lg btn-glow">
                    🎁 Claim
                </button>
            </form>
        </div>
    </div>
    
    <!-- Streak Calendar -->
    <div style="display: flex; gap: 8px; margin-top: 16px; justify-content: center;">
        <?php for ($i = 1; $i <= 7; $i++): 
            $claimed = $i <= $streak;
            $isToday = $i === min($streak + 1, 7);
        ?>
        <div style="width: 48px; height: 48px; border-radius: 12px; background: <?= $claimed ? 'var(--gradient-primary)' : ($isToday ? 'rgba(255,255,255,0.1)' : 'rgba(255,255,255,0.05)') ?>; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: 2px solid <?= $isToday ? 'var(--electric-blue)' : 'transparent' ?>;">
            <?= $claimed ? '✅' : ($isToday ? '🎁' : '📅') ?>
        </div>
        <?php endfor; ?>
    </div>
</div>

<!-- Daily Missions -->
<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">📋 Daily Missions</h1>
        <p class="page-subtitle">Complete missions to earn XP and coins</p>
    </div>
</div>

<div class="card-grid stagger-children">
    <?php foreach ($missions as $mission): 
        $mid = $mission['id'];
        $progress = intval($userMissions[$mid]['progress'] ?? 0);
        $completed = $userMissions[$mid]['completed'] ?? false;
        $requirement = intval($mission['requirement']);
        $percent = $requirement > 0 ? min(100, ($progress / $requirement) * 100) : 0;
    ?>
    <div class="glass-card" style="padding: 24px; <?= $completed ? 'opacity: 0.7;' : '' ?>" data-scroll-animate="slide-in-up">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="font-size: 2rem;"><?= htmlspecialchars($mission['icon'] ?? '⭐') ?></div>
            <?php if ($completed): ?>
            <span class="badge badge-success">✅ Completed</span>
            <?php endif; ?>
        </div>
        <h3 style="font-size: 1.1rem; margin-bottom: 4px;"><?= htmlspecialchars($mission['title'] ?? 'Mission') ?></h3>
        <p style="color: var(--current-text-secondary); font-size: 0.9rem; margin-bottom: 16px;">
            <?= htmlspecialchars($mission['description'] ?? '') ?>
        </p>
        <div class="progress-container" style="margin-bottom: 8px;">
            <div class="progress-bar" style="width: <?= $percent ?>%"></div>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
            <span style="color: var(--current-text-secondary);"><?= $progress ?> / <?= $requirement ?></span>
            <div style="display: flex; gap: 12px;">
                <span>+<?= intval($mission['xp_reward'] ?? 0) ?> XP</span>
                <?php if (floatval($mission['coin_reward'] ?? 0) > 0): ?>
                <span style="color: var(--emerald-green);">+$<?= number_format(floatval($mission['coin_reward']), 4) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Achievements Section -->
<div class="glass-card" style="padding: 24px; margin-top: 30px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom: 16px;">🏆 Achievements (<?= count($achievements) ?>)</h3>
    <?php if (empty($achievements)): ?>
    <div style="text-align: center; padding: 40px; color: var(--current-text-muted);">
        <div style="font-size: 3rem; margin-bottom: 16px;">🌟</div>
        <p>Complete missions and earn achievements!</p>
    </div>
    <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
        <?php foreach ($achievements as $ach): ?>
        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 12px;">
            <div style="font-size: 1.5rem;"><?= htmlspecialchars($ach['icon'] ?? '🏆') ?></div>
            <div>
                <div style="font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($ach['name'] ?? 'Achievement') ?></div>
                <div style="font-size: 0.8rem; color: var(--current-text-muted);"><?= isset($ach['earned_at']) ? timeAgo($ach['earned_at']) : '' ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Spin Wheel Section -->
<div class="glass-card" style="padding: 24px; margin-top: 24px; text-align: center;" data-scroll-animate="fade-in">
    <div style="font-size: 3rem; margin-bottom: 12px;">🎡</div>
    <h3 style="margin-bottom: 8px;">Lucky Spin Wheel</h3>
    <p style="color: var(--current-text-secondary); margin-bottom: 20px;">Spin to win XP, coins, and prizes!</p>
    <button class="btn btn-primary btn-lg btn-glow" onclick="EARNNOVA.showToast('Coming Soon', 'Spin wheel will be available in the next update!', 'info')">
        🎡 Spin Now
    </button>
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
    }, 500);
    <?php endforeach; ?>
});
</script>
<?php endif; ?>

<?php
// Auto-update mission progress (called on page load to sync ads watched etc.)
$todayRewards = db()->getTodayAdRewards($uid);
updateMissionProgress($uid, 'watch_ads', count($todayRewards));
$refs = db()->getReferrals($uid);
updateMissionProgress($uid, 'referrals', count($refs));

include __DIR__ . '/includes/footer.php';
?>
