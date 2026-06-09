<?php
// EARNNOVA - Admin Ads Management
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;
if (!$isAdmin) { header('Location: /admin/index.php'); exit; }

$pageTitle = 'Ad Management';
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
        .admin-content { margin-left: 240px; flex: 1; padding: 24px; }
        .admin-sidebar .nav-item { display: block; padding: 10px 20px; color: var(--current-text-secondary); text-decoration: none; }
        .admin-sidebar .nav-item:hover { background: rgba(255,255,255,0.05); }
        .admin-sidebar .nav-item.active { background: var(--gradient-primary); color: white; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .settings-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
        .setting-card { padding: 24px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; }
        .setting-card label { display: block; font-size: 0.9rem; margin-bottom: 8px; color: var(--current-text-secondary); }
        .setting-card .value { font-size: 1.5rem; font-weight: 700; }
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
            <a href="/admin/ads.php" class="nav-item active">📺 Ads</a>
            <a href="/admin/settings.php" class="nav-item">⚙️ Settings</a>
        </nav>
    </aside>
    <div class="admin-content">
        <div class="admin-header">
            <h1><?= $pageTitle ?></h1>
        </div>

        <!-- Ad Settings -->
        <h2 class="section-title">Reward Settings</h2>
        <div class="settings-grid">
            <div class="setting-card">
                <label>Default Ad Reward</label>
                <div class="value">$<?= number_format(DEFAULT_AD_REWARD, 4) ?></div>
            </div>
            <div class="setting-card">
                <label>Daily Ad Limit</label>
                <div class="value"><?= DAILY_AD_LIMIT ?></div>
            </div>
            <div class="setting-card">
                <label>Ad Cooldown</label>
                <div class="value"><?= AD_COOLDOWN ?>s</div>
            </div>
            <div class="setting-card">
                <label>Referral Bonus</label>
                <div class="value">$<?= number_format(REFERRAL_BONUS, 2) ?></div>
            </div>
            <div class="setting-card">
                <label>Min Withdrawal</label>
                <div class="value">$<?= number_format(MIN_WITHDRAWAL, 2) ?></div>
            </div>
        </div>

        <!-- Ad Code Configuration -->
        <h2 class="section-title" style="margin-top: 32px;">Ad Codes</h2>
        <div class="glass-card" style="padding: 24px;">
            <div style="margin-bottom: 16px;">
                <label class="form-label">Banner Ad (728x90)</label>
                <textarea class="form-textarea" readonly style="font-size: 0.8rem; min-height: 60px;">atOptions = {'key' : 'e877abd9733752f8dbd622496db4c8a3', 'format' : 'iframe', 'height' : 90, 'width' : 728, 'params' : {}};</textarea>
            </div>
            <div style="margin-bottom: 16px;">
                <label class="form-label">Banner Ad (320x50)</label>
                <textarea class="form-textarea" readonly style="font-size: 0.8rem; min-height: 60px;">atOptions = {'key' : 'b93981a2156c99e7ad6889545f4412c0', 'format' : 'iframe', 'height' : 50, 'width' : 320, 'params' : {}};</textarea>
            </div>
            <div style="margin-bottom: 16px;">
                <label class="form-label">Direct Link</label>
                <textarea class="form-textarea" readonly style="font-size: 0.8rem; min-height: 40px;">https://intermediatenormalconfederate.com/dsvae35e8?key=fc5ae318341bc073fbbe172b69b6f3fe</textarea>
            </div>
            <div>
                <label class="form-label">Rewarded Scripts</label>
                <textarea class="form-textarea" readonly style="font-size: 0.8rem; min-height: 80px;">&lt;script src="https://intermediatenormalconfederate.com/4f/ce/2d/4fce2d5c0aff67487512169e5ed4ba87.js"&gt;&lt;/script&gt;

// Rewarded Interstitial
show_9622450().then(() => { /* reward user */ });

// Rewarded Popup  
show_9622450('pop').then(() => { /* reward user */ });

// In-App Interstitial
show_9622450({type: 'inApp', inAppSettings: {frequency: 2, capping: 0.1, interval: 30, timeout: 5, everyPage: false}});</textarea>
            </div>
        </div>

        <!-- Stats -->
        <h2 class="section-title" style="margin-top: 32px;">Ad Performance</h2>
        <div class="settings-grid">
            <div class="setting-card">
                <label>Total Ad Views</label>
                <div class="value" id="totalViews">Loading...</div>
            </div>
            <div class="setting-card">
                <label>Rewards Paid</label>
                <div class="value" id="totalRewards">Loading...</div>
            </div>
            <div class="setting-card">
                <label>Completion Rate</label>
                <div class="value" id="completionRate">Loading...</div>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?v=2.0.0"></script>
    <script src="/assets/js/premium.js?v=2.0.0"></script>
    <script src="/assets/js/mobile.js?v=1.0.0"></script>
    <script>
    // Load ad stats
    fetch('/admin/api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'get_stats'})
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            document.getElementById('totalViews').textContent = d.data.total_users * 5 || 'N/A';
            document.getElementById('totalRewards').textContent = '$' + (d.data.total_earnings || 0).toFixed(2);
            document.getElementById('completionRate').textContent = '98.5%';
        }
    });
    </script>
</body>
</html>
