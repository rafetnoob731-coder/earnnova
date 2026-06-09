<?php
// EARNNOVA - Admin Settings
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;
if (!$isAdmin) { header('Location: /admin/index.php'); exit; }

$pageTitle = 'Settings';
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = substr($key, 8);
            $updates[$settingKey] = sanitizeInput($value);
        }
    }
    foreach ($updates as $k => $v) {
        db()->updateSetting($k, $v);
    }
    $message = 'Settings updated successfully';
    $messageType = 'success';
    logActivity('admin', 'settings_updated', 'Settings updated via form');
}

$settings = db()->getSettings();
$settingsMap = [];
foreach ($settings as $s) {
    $settingsMap[$s['key']] = $s['value'];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - <?= $pageTitle ?></title>
    <link rel="stylesheet" href="/assets/css/style.css?v=2.0.0">
    <link rel="stylesheet" href="/assets/css/premium.css?v=2.0.0">
    <link rel="stylesheet" href="/assets/css/mobile.css?v=1.0.0">>
    <style>
        body { display: flex; }
        .admin-sidebar { width: 240px; min-height: 100vh; background: var(--bg-card-dark); border-right: 1px solid var(--glass-border); padding: 20px 0; position: fixed; }
        .admin-content { margin-left: 240px; flex: 1; padding: 24px; max-width: 800px; }
        .admin-sidebar .nav-item { display: block; padding: 10px 20px; color: var(--current-text-secondary); text-decoration: none; }
        .admin-sidebar .nav-item:hover { background: rgba(255,255,255,0.05); }
        .admin-sidebar .nav-item.active { background: var(--gradient-primary); color: white; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        @media (max-width: 768px) { .admin-sidebar { display: none; } .admin-content { margin-left: 0; } }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div style="padding: 0 20px 20px; text-align: center; border-bottom: 1px solid var(--glass-border);">
            <h2><?= SITE_NAME ?></h2>
            <p style="font-size: 0.8rem; color: var(--current-text-muted);">Admin Panel</p>
        </div>
        <nav style="margin-top: 16px;">
            <a href="/admin/index.php" class="nav-item">📊 Dashboard</a>
            <a href="/admin/users.php" class="nav-item">👥 Users</a>
            <a href="/admin/withdrawals.php" class="nav-item">💰 Withdrawals</a>
            <a href="/admin/ads.php" class="nav-item">📺 Ads</a>
            <a href="/admin/settings.php" class="nav-item active">⚙️ Settings</a>
        </nav>
    </aside>
    <div class="admin-content">
        <div class="admin-header">
            <h1><?= $pageTitle ?></h1>
        </div>

        <?php if ($message): ?>
        <div class="toast toast-<?= $messageType ?>" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon"><?= $messageType === 'success' ? '✅' : '❌' ?></span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="glass-card" style="padding: 32px;">
                <h3 style="margin-bottom: 24px;">Website Settings</h3>
                
                <div class="form-group">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="setting_site_name" class="form-input" value="<?= htmlspecialchars($settingsMap['site_name'] ?? SITE_NAME) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Minimum Withdrawal ($)</label>
                    <input type="number" name="setting_min_withdrawal" class="form-input" step="0.01" value="<?= htmlspecialchars($settingsMap['min_withdrawal'] ?? MIN_WITHDRAWAL) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Referral Bonus ($)</label>
                    <input type="number" name="setting_referral_bonus" class="form-input" step="0.01" value="<?= htmlspecialchars($settingsMap['referral_bonus'] ?? REFERRAL_BONUS) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Daily Ad Limit</label>
                    <input type="number" name="setting_daily_ad_limit" class="form-input" value="<?= htmlspecialchars($settingsMap['daily_ad_limit'] ?? DAILY_AD_LIMIT) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Ad Cooldown (seconds)</label>
                    <input type="number" name="setting_ad_cooldown" class="form-input" value="<?= htmlspecialchars($settingsMap['ad_cooldown'] ?? AD_COOLDOWN) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Default Ad Reward ($)</label>
                    <input type="number" name="setting_default_ad_reward" class="form-input" step="0.0001" value="<?= htmlspecialchars($settingsMap['default_ad_reward'] ?? DEFAULT_AD_REWARD) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Activation Bonus ($)</label>
                    <input type="number" name="setting_activation_bonus" class="form-input" step="0.01" value="<?= htmlspecialchars($settingsMap['activation_bonus'] ?? '0.05') ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-lg">Save Settings</button>
            </div>
        </form>
    </div>
    <script src="/assets/js/main.js?v=2.0.0"></script>
    <script src="/assets/js/premium.js?v=2.0.0"></script>
    <script src="/assets/js/mobile.js?v=1.0.0"></script>
</body>
</html>
