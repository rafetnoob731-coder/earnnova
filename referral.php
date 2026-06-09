<?php
// EARNNOVA - Referral System
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();

$pageTitle = 'Referrals';
$showSidebar = true;

$referrals = db()->getReferrals($user['uid']);
$referralEarnings = floatval($user['referral_balance'] ?? 0);

// Generate share links
$referralLink = rtrim(SITE_URL, '/') . '/register.php?ref=' . urlencode($user['referral_code'] ?? '');

$facebookShare = 'https://facebook.com/sharer/sharer.php?quote=' . urlencode("Join EARNNOVA and start earning online. Watch Ads, Complete Tasks, Earn Real Rewards!") . '&u=' . urlencode($referralLink);
$telegramShare = 'https://t.me/share/url?url=' . urlencode($referralLink) . '&text=' . urlencode('💰 Earn Online with EARNNOVA! Watch ads and complete tasks to earn rewards.');
$whatsappShare = 'https://wa.me/?text=' . urlencode("Hello! I am earning through EARNNOVA. You can join using my referral link: $referralLink Start earning today!");

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">Referral Program</h1>
        <p class="page-subtitle">Invite friends and earn $<?= number_format(REFERRAL_BONUS, 2) ?> per referral</p>
    </div>
</div>

<div class="dashboard-grid" data-scroll-animate="fade-in">
    <div class="glass-card stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-label">Total Referrals</div>
        <div class="stat-value"><?= count($referrals) ?></div>
        <div class="stat-change positive">↑ Keep inviting!</div>
    </div>
    <div class="glass-card stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Referral Earnings</div>
        <div class="stat-value">$<?= number_format($referralEarnings, 2) ?></div>
        <div class="stat-change positive">↑ Earn more</div>
    </div>
    <div class="glass-card stat-card">
        <div class="stat-icon">🔗</div>
        <div class="stat-label">Your Code</div>
        <div class="stat-value" style="font-size: 1.2rem;"><?= htmlspecialchars($user['referral_code'] ?? 'N/A') ?></div>
        <div class="stat-change positive">Click to copy</div>
    </div>
</div>

<!-- Share Section -->
<div class="glass-card" style="padding: 32px; margin-bottom: 24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom: 16px;">Share Your Referral Link</h3>
    <div style="display: flex; gap: 12px; margin-bottom: 20px;">
        <input type="text" class="form-input" value="<?= htmlspecialchars($referralLink) ?>" readonly id="referralLink" style="flex: 1;">
        <button class="btn btn-primary" onclick="EARNNOVA.copyToClipboard('<?= htmlspecialchars($referralLink) ?>')">Copy Link</button>
    </div>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="<?= htmlspecialchars($facebookShare) ?>" target="_blank" class="btn btn-secondary" rel="noopener noreferrer">
            📘 Facebook
        </a>
        <a href="<?= htmlspecialchars($telegramShare) ?>" target="_blank" class="btn btn-secondary" rel="noopener noreferrer">
            ✈️ Telegram
        </a>
        <a href="<?= htmlspecialchars($whatsappShare) ?>" target="_blank" class="btn btn-secondary" rel="noopener noreferrer">
            💬 WhatsApp
        </a>
        <button class="btn btn-secondary" onclick="copyAllShareText()">📋 Copy All</button>
    </div>
</div>

<!-- Referral Tree -->
<div class="glass-card" style="padding: 24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom: 20px;">Your Referrals</h3>
    
    <?php if (empty($referrals)): ?>
    <div style="text-align: center; padding: 40px; color: var(--current-text-muted);">
        <div style="font-size: 3rem; margin-bottom: 16px;">🌱</div>
        <p>No referrals yet. Share your link to start earning!</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Referred User</th>
                    <th>Date</th>
                    <th>Earnings</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($referrals as $ref): ?>
                <tr>
                    <td><?= htmlspecialchars($ref['referred_email'] ?? 'Unknown') ?></td>
                    <td><?= timeAgo($ref['created_at'] ?? '') ?></td>
                    <td style="color: var(--emerald-green);">+$<?= number_format(floatval($ref['earnings'] ?? 0), 2) ?></td>
                    <td><span class="badge badge-success">Active</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function copyAllShareText() {
    const text = `Join EARNNOVA and start earning online!\n\n✅ Watch Ads\n✅ Complete Tasks\n✅ Earn Real Rewards\n\nRegister Now: <?= htmlspecialchars($referralLink) ?>`;
    EARNNOVA.copyToClipboard(text);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
