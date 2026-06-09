<?php
// EARNNOVA - Premium Missions & Gamification
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();
$uid = $user['uid'];

$pageTitle = 'Missions';
$showSidebar = true;

$dailyResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_daily'])) {
    $dailyResult = claimDailyReward($uid);
    $user = getCurrentUser();
}

$today = date('Y-m-d');
$missionsResult = db()->request('GET', '/rest/v1/missions?is_daily=eq.true&is_active=eq.true&select=*');
$missions = $missionsResult['success'] ? ($missionsResult['data'] ?? []) : [];

$progressResult = db()->request('GET', "/rest/v1/user_missions?user_id=eq.$uid&date_assigned=eq.$today&select=*");
$userMissions = [];
if ($progressResult['success']) {
    foreach ($progressResult['data'] ?? [] as $p) {
        $userMissions[$p['mission_id']] = $p;
    }
}

$achievements = json_decode($user['achievements'] ?? '[]', true);
$xp = intval($user['xp'] ?? 0);
$level = intval($user['level'] ?? 1);
$rank = getUserRank($xp);
$nextRank = getNextRank($xp);
$streak = intval($user['streak_days'] ?? 0);

$newAchievements = checkAchievements($uid);
if (!empty($newAchievements)) {
    $user = getCurrentUser();
    $achievements = json_decode($user['achievements'] ?? '[]', true);
}

include __DIR__ . '/includes/header.php';
?>

<script id="user-data" type="application/json"><?= json_encode($user) ?></script>

<!-- Rank & XP Header -->
<div class="glass-2" style="padding:28px;margin-bottom:24px;" data-scroll-animate="fade-in">
    <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,<?= $rank['color'] ?>,rgba(255,255,255,0.2));display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:900;color:white;box-shadow:0 0 40px <?= $rank['color'] ?>66;position:relative;">
            <?= $rank['level'] ?>
            <div class="pulse-ring" style="position:absolute;inset:-4px;border-radius:50%;border:2px solid <?= $rank['color'] ?>;"></div>
        </div>
        <div style="flex:1;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <h2 style="font-size:1.5rem;"><?= $rank['name'] ?> Rank</h2>
                <span class="badge badge-primary">Level <?= $level ?></span>
                <span class="badge badge-info">🔥 <?= $streak ?> day streak</span>
            </div>
            <div style="margin-top:12px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:0.85rem;color:var(--current-text-secondary);">XP: <?= number_format($xp) ?></span>
                    <?php if ($nextRank): ?>
                    <span style="font-size:0.85rem;color:var(--current-text-muted);">Next: <?= $nextRank['name'] ?> (<?= number_format($nextRank['min_xp']) ?> XP)</span>
                    <?php endif; ?>
                </div>
                <div class="progress-container" style="height:10px;border-radius:5px;">
                    <?php 
                    $currentMin = $rank['min_xp'];
                    $nextMin = $nextRank['min_xp'] ?? ($currentMin + 25000);
                    $progress = ($xp - $currentMin) / max(1, ($nextMin - $currentMin)) * 100;
                    ?>
                    <div class="progress-bar" style="width:<?= min(100, $progress) ?>%;height:100%;border-radius:5px;"></div>
                </div>
            </div>
            <div style="display:flex;gap:24px;margin-top:16px;flex-wrap:wrap;">
                <div><span style="color:var(--current-text-muted);font-size:0.85rem;">Missions Done</span> <strong>⭐ <?= intval($user['total_missions_completed'] ?? 0) ?></strong></div>
                <div><span style="color:var(--current-text-muted);font-size:0.85rem;">Achievements</span> <strong>🏆 <?= count($achievements) ?></strong></div>
                <div><span style="color:var(--current-text-muted);font-size:0.85rem;">Rank Progress</span> <strong><?= round($progress, 1) ?>%</strong></div>
            </div>
        </div>
    </div>
</div>

<!-- Daily Reward Claim -->
<div class="glass-2 border-animated" style="padding:28px;margin-bottom:24px;" data-scroll-animate="fade-in">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
            <h3 style="font-size:1.3rem;">🎁 Daily Reward</h3>
            <p style="color:var(--current-text-secondary);font-size:0.9rem;">Day <?= min($streak + 1, 7) ?> / 7 — Keep your streak alive for bigger rewards!</p>
        </div>
        <div style="display:flex;align-items:center;gap:20px;">
            <div style="text-align:center;">
                <div style="font-size:1.3rem;font-weight:800;background:linear-gradient(135deg,var(--gold),var(--warning));-webkit-background-clip:text;-webkit-text-fill-color:transparent;" id="dailyXp">+<?= [50, 75, 100, 150, 200, 250, 500][min($streak, 6)] ?></div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);">XP</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.3rem;font-weight:800;color:var(--emerald-green);" id="dailyCoins">+$<?= number_format([0.01, 0.02, 0.03, 0.05, 0.08, 0.10, 0.25][min($streak, 6)], 2) ?></div>
                <div style="font-size:0.75rem;color:var(--current-text-muted);">Coins</div>
            </div>
            <form method="POST">
                <button type="submit" name="claim_daily" class="btn btn-premium btn-primary btn-lg btn-glow-premium btn-magnetic" style="padding:14px 32px;">
                    🎁 Claim Now
                </button>
            </form>
        </div>
    </div>
    
    <!-- Streak Calendar -->
    <div style="display:flex;gap:10px;margin-top:20px;justify-content:center;flex-wrap:wrap;">
        <?php for ($i = 1; $i <= 7; $i++): 
            $claimed = $i <= $streak;
            $isToday = $i === min($streak + 1, 7);
            $rewards = ['+50 XP', '+75 XP', '+100 XP', '+150 XP', '+200 XP', '+250 XP', '+500 XP'];
            $coinRewards = ['$0.01', '$0.02', '$0.03', '$0.05', '$0.08', '$0.10', '$0.25'];
        ?>
        <div style="text-align:center;">
            <div style="width:56px;height:56px;border-radius:16px;background:<?= $claimed ? 'var(--gradient-primary)' : ($isToday ? 'rgba(255,255,255,0.12)' : 'rgba(255,255,255,0.04)') ?>;display:flex;align-items:center;justify-content:center;font-size:1.4rem;border:2px solid <?= $isToday ? 'var(--electric-blue)' : 'transparent' ?>;transition:all 0.3s ease;">
                <?= $claimed ? '✅' : ($isToday ? '🎁' : '📅') ?>
            </div>
            <div style="font-size:0.7rem;margin-top:4px;color:var(--current-text-muted);">Day <?= $i ?></div>
            <div style="font-size:0.65rem;color:var(--current-text-muted);"><?= $rewards[$i-1] ?></div>
        </div>
        <?php endfor; ?>
    </div>
</div>

<!-- Missions Tabs -->
<div style="display:flex;gap:12px;margin-bottom:20px;" data-scroll-animate="fade-in">
    <button class="btn btn-primary btn-sm" onclick="filterMissions('daily', this)">📅 Daily</button>
    <button class="btn btn-secondary btn-sm" onclick="filterMissions('weekly', this)">📆 Weekly</button>
    <button class="btn btn-secondary btn-sm" onclick="filterMissions('achievement', this)">🏆 Achievements</button>
</div>

<!-- Daily Missions -->
<div id="dailyMissions">
    <div class="card-grid stagger-children">
        <?php foreach ($missions as $mission): 
            $mid = $mission['id'];
            $progress = intval($userMissions[$mid]['progress'] ?? 0);
            $completed = $userMissions[$mid]['completed'] ?? false;
            $requirement = intval($mission['requirement']);
            $percent = $requirement > 0 ? min(100, ($progress / $requirement) * 100) : 0;
        ?>
        <div class="glass-2" style="padding:24px;<?= $completed ? 'opacity:0.7;' : '' ?>" data-scroll-animate="slide-in-up">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <div style="font-size:2.2rem;"><?= htmlspecialchars($mission['icon'] ?? '⭐') ?></div>
                <?php if ($completed): ?>
                <span class="badge badge-success">✅ Done</span>
                <?php elseif ($percent >= 100): ?>
                <span class="badge badge-primary">🎯 Ready</span>
                <?php endif; ?>
            </div>
            <h3 style="font-size:1.1rem;margin-bottom:4px;"><?= htmlspecialchars($mission['title'] ?? 'Mission') ?></h3>
            <p style="color:var(--current-text-secondary);font-size:0.9rem;margin-bottom:16px;">
                <?= htmlspecialchars($mission['description'] ?? '') ?>
            </p>
            <div class="progress-container" style="margin-bottom:8px;height:8px;border-radius:4px;">
                <div class="progress-bar" style="width:<?= $percent ?>%;height:100%;border-radius:4px;"></div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span style="color:var(--current-text-secondary);"><?= $progress ?> / <?= $requirement ?></span>
                <div style="display:flex;gap:12px;">
                    <span style="color:var(--gold);">+<?= intval($mission['xp_reward'] ?? 0) ?> XP</span>
                    <?php if (floatval($mission['coin_reward'] ?? 0) > 0): ?>
                    <span style="color:var(--emerald-green);">+$<?= number_format(floatval($mission['coin_reward']), 4) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Achievements Section -->
<div class="glass-2" style="padding:28px;margin-top:30px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom:16px;">🏆 Achievements (<?= count($achievements) ?>)</h3>
    <?php if (empty($achievements)): ?>
    <div style="text-align:center;padding:40px;color:var(--current-text-muted);">
        <div style="font-size:4rem;margin-bottom:16px;">🌟</div>
        <p style="font-size:1.1rem;">Complete missions and earn achievements!</p>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
        <?php foreach ($achievements as $ach): ?>
        <div style="display:flex;align-items:center;gap:14px;padding:14px;background:rgba(255,255,255,0.03);border-radius:12px;transition:all 0.3s ease;" class="hover-lift">
            <div style="font-size:1.8rem;"><?= htmlspecialchars($ach['icon'] ?? '🏆') ?></div>
            <div>
                <div style="font-weight:600;font-size:0.95rem;"><?= htmlspecialchars($ach['name'] ?? 'Achievement') ?></div>
                <div style="font-size:0.8rem;color:var(--current-text-muted);"><?= isset($ach['earned_at']) ? timeAgo($ach['earned_at']) : '' ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Spin Wheel -->
<div class="glass-2 border-animated" style="padding:28px;margin-top:24px;text-align:center;" data-scroll-animate="fade-in">
    <div style="font-size:4rem;margin-bottom:12px;" class="float-3d">🎡</div>
    <h3 style="margin-bottom:8px;font-size:1.3rem;">Lucky Spin Wheel</h3>
    <p style="color:var(--current-text-secondary);margin-bottom:20px;">Spin to win XP, coins, and exclusive prizes!</p>
    <button class="btn btn-premium btn-primary btn-lg btn-glow-premium btn-magnetic" onclick="EARNNOVA.showToast('Coming Soon', 'Spin wheel will be available in the next update!', 'info')">
        🎡 Spin Now
    </button>
</div>

<!-- New Achievement Notification -->
<?php if (!empty($newAchievements)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($newAchievements as $ach): ?>
    setTimeout(function() {
        // XP float animation
        const xpFloat = document.createElement('div');
        xpFloat.className = 'xp-float';
        xpFloat.textContent = '+<?= intval($ach['xp'] ?? 0) ?> XP';
        xpFloat.style.color = '<?= $rank['color'] ?>';    
        xpFloat.style.left = '50%';
        xpFloat.style.top = '40%';
        document.body.appendChild(xpFloat);
        setTimeout(() => xpFloat.remove(), 2000);
        
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
$todayRewards = db()->getTodayAdRewards($uid);
updateMissionProgress($uid, 'watch_ads', count($todayRewards));
$refs = db()->getReferrals($uid);
updateMissionProgress($uid, 'referrals', count($refs));

include __DIR__ . '/includes/footer.php';
?>
