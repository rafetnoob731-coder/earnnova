<?php
// EARNNOVA - User Profile
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser();

$pageTitle = 'Profile';
$showSidebar = true;

$message = '';
$messageType = '';

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
        <h1 class="page-title">Profile</h1>
        <p class="page-subtitle">Manage your account settings</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;" class="responsive-grid">
    <!-- Profile Info -->
    <div class="glass-card" style="padding: 32px;" data-scroll-animate="slide-in-left">
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: white; margin: 0 auto 16px;">
                <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
            </div>
            <h2 data-username><?= htmlspecialchars($user['username'] ?? 'User') ?></h2>
            <p style="color: var(--current-text-secondary);" data-email><?= htmlspecialchars($user['email'] ?? '') ?></p>
        </div>

        <?php if ($message): ?>
        <div class="toast toast-<?= $messageType ?>" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon"><?= $messageType === 'success' ? '✅' : '❌' ?></span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled style="opacity: 0.7;">
            </div>
            <div class="form-group">
                <label class="form-label">Referral Code</label>
                <input type="text" class="form-input" value="<?= htmlspecialchars($user['referral_code'] ?? '') ?>" readonly>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary btn-block">Update Profile</button>
        </form>
    </div>

    <!-- Account Details -->
    <div class="glass-card" style="padding: 32px;" data-scroll-animate="slide-in-right">
        <h3 style="margin-bottom: 24px;">Account Details</h3>
        
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="display: flex; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <span style="color: var(--current-text-secondary);">Account Status</span>
                <span class="badge badge-<?= ($user['activation_status'] ?? 'inactive') === 'active' ? 'success' : 'warning' ?>">
                    <?= ucfirst($user['activation_status'] ?? 'Inactive') ?>
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <span style="color: var(--current-text-secondary);">Balance</span>
                <span style="font-weight: 600;" data-balance>$<?= number_format($user['balance'] ?? 0, 2) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <span style="color: var(--current-text-secondary);">Referral Balance</span>
                <span style="font-weight: 600; color: var(--emerald-green);" data-referral-balance>$<?= number_format($user['referral_balance'] ?? 0, 2) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <span style="color: var(--current-text-secondary);">Member Since</span>
                <span style="font-weight: 600;"><?= formatDate($user['created_at'] ?? date('Y-m-d H:i:s')) ?></span>
            </div>
            <?php if (!empty($user['activation_date'])): ?>
            <div style="display: flex; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <span style="color: var(--current-text-secondary);">Activation Date</span>
                <span style="font-weight: 600;"><?= formatDate($user['activation_date']) ?></span>
            </div>
            <?php endif; ?>
            <div style="display: flex; justify-content: space-between; padding: 12px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <span style="color: var(--current-text-secondary);">User ID</span>
                <span style="font-weight: 600; font-size: 0.85rem;"><?= htmlspecialchars(substr($user['uid'] ?? '', 0, 16)) ?>...</span>
            </div>
        </div>

        <div style="margin-top: 24px;">
            <?php if (($user['activation_status'] ?? 'inactive') !== 'active'): ?>
            <a href="/activation.php" class="btn btn-warning btn-block">🔐 Activate Account</a>
            <?php endif; ?>
            <a href="/api/auth.php?action=logout" class="btn btn-secondary btn-block mt-2">🚪 Logout</a>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .responsive-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
