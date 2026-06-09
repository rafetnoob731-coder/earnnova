<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="description" content="EARNNOVA - Premium Earning Platform. Watch ads, complete tasks, and earn real rewards.">
    <meta name="theme-color" content="#0a0a1a" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#f0f2f5" media="(prefers-color-scheme: light)">
    <meta name="csrf-token" content="<?= generateCSRFToken() ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EARNNOVA">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="EARNNOVA">
    <meta name="msapplication-TileColor" content="#4361ee">
    
    <title><?= SITE_NAME ?> — <?= $pageTitle ?? 'Premium Earning Platform' ?></title>
    
    <!-- Google Fonts: Inter + Clash Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Favicon & PWA Icons -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="icon" type="image/png" href="/assets/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="384x384" href="/assets/icons/icon-384x384.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css?v=2.0.0">
    <link rel="stylesheet" href="/assets/css/premium.css?v=2.0.0">
    <link rel="stylesheet" href="/assets/css/mobile.css?v=1.0.0">
    
    <!-- Ad Scripts (Lazy Loaded) -->
    <link rel="preconnect" href="https://intermediatenormalconfederate.com">
    
    <style>
        /* Critical inline styles for immediate rendering */
        .critical-hidden { display: none; }
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            transition: opacity 0.5s ease;
        }
        .loading-screen.hide { opacity: 0; pointer-events: none; }
        .loading-logo {
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 900;
            color: white;
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="<?= (isset($showSidebar) && !$showSidebar) ? 'no-mobile-nav' : '' ?>">
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-logo">E</div>
    </div>

    <!-- Aurora Background (Premium) -->
    <div class="aurora-bg">
        <div class="aurora-layer"></div>
        <div class="aurora-layer"></div>
        <div class="aurora-layer"></div>
        <div class="aurora-layer"></div>
    </div>
    
    <!-- Particle System -->
    <div class="particle-container"></div>

    <?php if (isset($showSidebar) && $showSidebar): ?>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="/assets/images/logo.png" alt="<?= SITE_NAME ?>" onerror="this.style.display='none'">
            <h2><?= SITE_NAME ?></h2>
        </div>
        <nav class="sidebar-nav">
            <a href="/dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                Dashboard
            </a>
            <a href="/ads.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'ads.php' ? 'active' : '' ?>">
                <span class="nav-icon">📺</span>
                Watch Ads
            </a>
            <a href="/tasks.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'tasks.php' ? 'active' : '' ?>">
                <span class="nav-icon">📋</span>
                Tasks
            </a>
            <a href="/shortlinks.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'shortlinks.php' ? 'active' : '' ?>">
                <span class="nav-icon">🔗</span>
                Shortlinks
            </a>
            <a href="/missions.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'missions.php' ? 'active' : '' ?>">
                <span class="nav-icon">⭐</span>
                Missions
            </a>
            <a href="/referral.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'referral.php' ? 'active' : '' ?>">
                <span class="nav-icon">👥</span>
                Referrals
            </a>
            <a href="/analytics.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'analytics.php' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                Analytics
            </a>
            <a href="/withdrawal.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'withdrawal.php' ? 'active' : '' ?>">
                <span class="nav-icon">💰</span>
                Withdraw
            </a>
            <a href="/plans.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'plans.php' ? 'active' : '' ?>">
                <span class="nav-icon">📋</span>
                Plans
            </a>
            <a href="/profile.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">
                <span class="nav-icon">👤</span>
                Profile
            </a>
            <hr style="border-color: var(--glass-border); margin: 16px 0;">
            <a href="/api/auth.php?action=logout" class="nav-item">
                <span class="nav-icon">🚪</span>
                Logout
            </a>
        </nav>
    </aside>

    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <button class="sidebar-toggle btn btn-secondary btn-sm" onclick="document.getElementById('sidebar').classList.toggle('open')" style="border-radius:12px;padding:8px 12px;">
                ☰
            </button>
            <h3 style="font-size: 1.1rem; font-weight: 700; font-family:'Inter',sans-serif;letter-spacing:-0.02em;"><?= $pageTitle ?? 'Dashboard' ?></h3>
            <span id="liveClock" style="font-family:'SF Mono','SFMono-Regular',monospace;font-size:0.85rem;color:var(--current-text-muted);margin-left:8px;letter-spacing:0.02em;"></span>
        </div>
        <div class="header-right">
            <span style="font-family:'SF Mono','SFMono-Regular',monospace;font-size:0.9rem;font-weight:600;background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;letter-spacing:0.02em;" data-balance>
                $<?= number_format(getCurrentUser()['balance'] ?? 0, 2) ?>
            </span>
            <button class="btn btn-sm" style="position:relative;background:var(--glass-bg-2);border:1px solid var(--glass-border-2);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;cursor:pointer;" title="Notifications" onclick="EARNNOVA.showToast('Notifications','No new notifications','info')">
                🔔
                <span style="position:absolute;top:6px;right:6px;width:8px;height:8px;background:var(--neon-coral);border-radius:50%;border:2px solid var(--bg-dark);"></span>
            </button>
            <button class="theme-toggle" title="Toggle theme" style="border-radius:50%;width:40px;height:40px;font-size:1.1rem;">☀️</button>
            <a href="/profile.php" style="text-decoration: none; color: inherit;">
                <?php $cu = getCurrentUser(); ?>
                <div style="width:36px;height:36px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;color:white;cursor:pointer;">
                    <?= strtoupper(substr($cu['username'] ?? 'U', 0, 1)) ?>
                </div>
            </a>
        </div>
    </header>
    <?php endif; ?>

    <!-- Main Content Start -->
    <main class="main-content" style="<?= isset($showSidebar) && $showSidebar ? '' : 'margin-left:0; padding-top:24px;' ?>">
    
    <?php if (isset($showSidebar) && !$showSidebar): ?>
    <style>
        /* Hide all navigation on auth/home pages */
        .mobile-bottom-nav { display: none !important; }
        .sidebar, .top-header { display: none !important; }
        body.no-mobile-nav { padding-bottom: 0 !important; }
        body.no-mobile-nav .main-content { margin-left: 0 !important; padding-top: 24px !important; }
    </style>
    <?php endif; ?>

    <!-- Live clock script -->
    <script>
    function updateLiveClock() {
        const el = document.getElementById('liveClock');
        if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }
    setInterval(updateLiveClock, 10000);
    document.addEventListener('DOMContentLoaded', updateLiveClock);
    </script>

<script>
// Hide loading screen immediately
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var loader = document.getElementById('loadingScreen');
        if (loader) loader.classList.add('hide');
    }, 300);
});
</script>
