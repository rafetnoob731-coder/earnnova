<?php
// EARNNOVA - Premium User Profile
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser();

$pageTitle = 'Profile';
$showSidebar = true;

$message = '';
$messageType = '';

$xp = intval($user['xp'] ?? 0);
$level = intval($user['level'] ?? 1);
$rank = getUserRank($xp);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = sanitizeInput($_POST['username'] ?? '');
    if (!empty($username) && $username !== $user['username']) {
        $existing = db()->getUserByUsername($username);
        if ($existing && $existing['uid'] !== $user['uid']) {
            $message = 'Username already taken';
            $messageType = 'error';
        } else {
            db()->updateUser($user['uid'], ['username' => $username]);
            $message = 'Profile updated successfully';
            $messageType = 'success';
            $user = getCurrentUser();
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">👤 Profile</h1>
        <p class="page-subtitle">Manage your account settings</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="responsive-grid">
    <!-- Profile Info -->
    <div class="glass-2" style="padding:32px;" data-scroll-animate="slide-in-left">
        <div style="text-align:center;margin-bottom:28px;">
            <div style="width:96px;height:96px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-size:2.5rem;font-weight:700;color:white;margin:0 auto 16px;box-shadow:0 0 40px rgba(67,97,238,0.3);position:relative;">
                <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                <div class="pulse-ring" style="position:absolute;inset:-4px;border-radius:50%;border:2px solid var(--electric-blue);"></div>
            </div>
            <h2 style="font-size:1.5rem;" data-username><?= htmlspecialchars($user['username'] ?? 'User') ?></h2>
            <p style="color:var(--current-text-secondary);" data-email><?= htmlspecialchars($user['email'] ?? '') ?></p>
            <div style="display:flex;gap:8px;justify-content:center;margin-top:8px;">
                <span class="badge badge-primary"><?= $rank['name'] ?> Lv.<?= $level ?></span>
                <span class="badge badge-<?= ($user['activation_status'] ?? 'inactive') === 'active' ? 'success' : 'warning' ?>">
                    <?= ucfirst($user['activation_status'] ?? 'Inactive') ?>
                </span>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="toast toast-<?= $messageType ?>" style="margin-bottom:20px;animation:none;">
            <span class="toast-icon"><?= $messageType === 'success' ? '✅' : '❌' ?></span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group input-premium">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                <span class="input-focus-line"></span>
            </div>
            <div class="form-group input-premium">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled style="opacity:0.7;">
                <span class="input-focus-line"></span>
            </div>
            <div class="form-group input-premium">
                <label class="form-label">Referral Code</label>
                <input type="text" class="form-input" value="<?= htmlspecialchars($user['referral_code'] ?? '') ?>" readonly>
                <span class="input-focus-line"></span>
            </div>
            <button type="submit" name="update_profile" class="btn btn-premium btn-primary btn-block btn-magnetic">💾 Update Profile</button>
        </form>
    </div>

    <!-- Account Details -->
    <div class="glass-2" style="padding:32px;" data-scroll-animate="slide-in-right">
        <h3 style="margin-bottom:24px;">📋 Account Details</h3>
        
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="display:flex;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,0.03);border-radius:10px;">
                <span style="color:var(--current-text-secondary);">Account Status</span>
                <span class="badge badge-<?= ($user['activation_status'] ?? 'inactive') === 'active' ? 'success' : 'warning' ?>">
                    <?= ucfirst($user['activation_status'] ?? 'Inactive') ?>
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,0.03);border-radius:10px;">
                <span style="color:var(--current-text-secondary);">Balance</span>
                <span style="font-weight:700;" data-balance>$<?= number_format($user['balance'] ?? 0, 2) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,0.03);border-radius:10px;">
                <span style="color:var(--current-text-secondary);">Referral Balance</span>
                <span style="font-weight:700;color:var(--emerald-green);" data-referral-balance>$<?= number_format($user['referral_balance'] ?? 0, 2) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,0.03);border-radius:10px;">
                <span style="color:var(--current-text-secondary);">Total XP</span>
                <span style="font-weight:700;color:var(--gold);"><?= number_format($xp) ?> XP</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,0.03);border-radius:10px;">
                <span style="color:var(--current-text-secondary);">Member Since</span>
                <span style="font-weight:600;"><?= formatDate($user['created_at'] ?? date('Y-m-d H:i:s')) ?></span>
            </div>
            <?php if (!empty($user['activation_date'])): ?>
            <div style="display:flex;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,0.03);border-radius:10px;">
                <span style="color:var(--current-text-secondary);">Activation Date</span>
                <span style="font-weight:600;"><?= formatDate($user['activation_date']) ?></span>
            </div>
            <?php endif; ?>
            <div style="display:flex;justify-content:space-between;padding:14px 18px;background:rgba(255,255,255,0.03);border-radius:10px;">
                <span style="color:var(--current-text-secondary);">User ID</span>
                <span style="font-weight:600;font-size:0.85rem;font-family:monospace;"><?= htmlspecialchars(substr($user['uid'] ?? '', 0, 16)) ?>...</span>
            </div>
        </div>

        <div style="margin-top:24px;display:flex;flex-direction:column;gap:10px;">
            <?php if (($user['activation_status'] ?? 'inactive') !== 'active'): ?>
            <a href="/activation.php" class="btn btn-premium btn-warning btn-block btn-magnetic" style="padding:14px;">🔐 Activate Account</a>
            <?php endif; ?>
            <a href="/api/auth.php?action=logout" class="btn btn-premium btn-secondary btn-block btn-magnetic" style="padding:14px;">🚪 Logout</a>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .responsive-grid { grid-template-columns: 1fr !important; }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
