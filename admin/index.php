<?php
// EARNNOVA - Admin Dashboard
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Admin authentication (simplified - in production use proper admin auth)
session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;
$adminRole = $_SESSION['admin_role'] ?? '';

// Simple admin login check
if (!$isAdmin) {
    // Check for admin cookie/token
    if (isset($_COOKIE['admin_token']) && $_COOKIE['admin_token'] === hash('sha256', ADMIN_SECRET . 'admin')) {
        $isAdmin = true;
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_role'] = 'super_admin';
    }
}

// Handle admin login
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Simple admin auth (in production, check against database)
    if ($username === 'admin' && $password === ADMIN_SECRET) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_role'] = 'super_admin';
        setcookie('admin_token', hash('sha256', ADMIN_SECRET . 'admin'), time() + 86400 * 7, '/');
        $isAdmin = true;
    } else {
        $loginError = 'Invalid admin credentials';
    }
}

if (!$isAdmin):
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Admin Login</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=2.0.0">
    <link rel="stylesheet" href="/assets/css/premium.css?v=2.0.0">
    <link rel="stylesheet" href="/assets/css/mobile.css?v=1.0.0">>
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 24px; }
    </style>
</head>
<body>
    <div class="glass-card" style="max-width: 400px; width: 100%; padding: 40px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <h1 style="font-size: 1.5rem;">🔐 Admin Login</h1>
            <p style="color: var(--current-text-secondary); margin-top: 8px;"><?= SITE_NAME ?> Administration</p>
        </div>
        <?php if ($loginError): ?>
        <div class="toast toast-error" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon">❌</span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($loginError) ?></div>
            </div>
        </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" name="admin_login" class="btn btn-primary btn-block btn-lg">Sign In</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// Admin is authenticated - show dashboard
$totalUsers = db()->getTotalUsers();
$activeUsers = db()->getActiveUsers();
$todayRegistrations = db()->getTodayRegistrations();
$totalEarnings = db()->getTotalEarnings();
$totalWithdrawals = db()->getTotalWithdrawals();
$pendingWithdrawals = count(db()->getPendingWithdrawals());

// Get all users
$allUsers = db()->getAllUsers();
$allWithdrawals = db()->getAllWithdrawals();

$pageTitle = 'Admin Dashboard';
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
    <link rel="stylesheet" href="/assets/css/admin.css?v=1.0.0">
    <style>
        .admin-body { display: flex; }
        .admin-sidebar { width: 240px; min-height: 100vh; background: var(--bg-card-dark); border-right: 1px solid var(--glass-border); padding: 20px 0; position: fixed; left: 0; top: 0; overflow-y: auto; }
        .admin-content { margin-left: 240px; flex: 1; padding: 24px; min-height: 100vh; }
        .admin-sidebar .nav-item { padding: 10px 20px; font-size: 0.9rem; }
        .admin-sidebar .nav-item.active { background: var(--gradient-primary); color: white; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-widget { padding: 20px; border-radius: 12px; background: var(--glass-bg); border: 1px solid var(--glass-border); }
        .stat-widget .widget-label { font-size: 0.8rem; color: var(--current-text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .stat-widget .widget-value { font-size: 1.8rem; font-weight: 800; margin-top: 8px; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; padding: 12px 16px; font-size: 0.8rem; text-transform: uppercase; color: var(--current-text-muted); border-bottom: 1px solid var(--glass-border); }
        .admin-table td { padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem; }
        .admin-table tr:hover td { background: rgba(255,255,255,0.03); }
        .section-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 16px; margin-top: 32px; }
        .action-btn { padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 500; }
        .action-btn.approve { background: rgba(6,214,160,0.2); color: var(--emerald-green); }
        .action-btn.reject { background: rgba(239,71,111,0.2); color: var(--error); }
        .action-btn.view { background: rgba(67,97,238,0.2); color: var(--electric-blue); }
        @media (max-width: 768px) { .admin-sidebar { display: none; } .admin-content { margin-left: 0; } }
    </style>
</head>
<body class="admin-body">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div style="padding: 0 20px 20px; text-align: center; border-bottom: 1px solid var(--glass-border); margin-bottom: 16px;">
            <h2 style="font-size: 1.2rem;"><?= SITE_NAME ?></h2>
            <p style="font-size: 0.8rem; color: var(--current-text-muted);">Admin Panel</p>
        </div>
        <nav>
            <a href="/admin/index.php" class="nav-item active">📊 Dashboard</a>
            <a href="/admin/users.php" class="nav-item">👥 Users</a>
            <a href="/admin/withdrawals.php" class="nav-item">💰 Withdrawals</a>
            <a href="/admin/ads.php" class="nav-item">📺 Ads</a>
            <a href="/admin/referrals.php" class="nav-item">🔗 Referrals</a>
            <a href="/admin/tasks.php" class="nav-item">📋 Tasks</a>
            <a href="/admin/plans.php" class="nav-item">⭐ Plans</a>
            <a href="/admin/settings.php" class="nav-item">⚙️ Settings</a>
            <a href="/admin/security.php" class="nav-item">🛡️ Security</a>
            <a href="/admin/logs.php" class="nav-item">📝 Logs</a>
            <hr style="border-color: var(--glass-border); margin: 16px 20px;">
            <a href="/dashboard.php" class="nav-item">← Back to Site</a>
            <a href="/api/auth.php?action=logout" class="nav-item" style="color: var(--error);">🚪 Logout</a>
        </nav>
    </aside>

    <!-- Admin Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1><?= $pageTitle ?></h1>
            <div style="display: flex; gap: 12px; align-items: center;">
                <span style="font-size: 0.85rem; color: var(--current-text-muted);">Role: <strong><?= ucfirst($adminRole) ?></strong></span>
                <a href="/api/auth.php?action=logout" class="btn btn-sm btn-secondary">Logout</a>
            </div>
        </div>

        <!-- Statistics Widgets -->
        <div class="stats-grid">
            <div class="stat-widget">
                <div class="widget-label">Total Users</div>
                <div class="widget-value" style="color: var(--electric-blue);"><?= $totalUsers ?></div>
                <div style="font-size: 0.85rem; color: var(--current-text-muted); margin-top: 4px;">+<?= $todayRegistrations ?> today</div>
            </div>
            <div class="stat-widget">
                <div class="widget-label">Active Users</div>
                <div class="widget-value" style="color: var(--emerald-green);"><?= $activeUsers ?></div>
                <div style="font-size: 0.85rem; color: var(--current-text-muted); margin-top: 4px;"><?= $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0 ?>% of total</div>
            </div>
            <div class="stat-widget">
                <div class="widget-label">Total Earnings</div>
                <div class="widget-value" style="color: var(--emerald-green);">$<?= number_format($totalEarnings, 2) ?></div>
            </div>
            <div class="stat-widget">
                <div class="widget-label">Total Withdrawals</div>
                <div class="widget-value" style="color: var(--warning);">$<?= number_format($totalWithdrawals, 2) ?></div>
            </div>
            <div class="stat-widget">
                <div class="widget-label">Pending Withdrawals</div>
                <div class="widget-value" style="color: var(--error);"><?= $pendingWithdrawals ?></div>
            </div>
            <div class="stat-widget">
                <div class="widget-label">New Today</div>
                <div class="widget-value" style="color: var(--cyan-accent);"><?= $todayRegistrations ?></div>
            </div>
        </div>

        <!-- Recent Users -->
        <h2 class="section-title">Recent Users</h2>
        <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($allUsers, 0, 10) as $u): ?>
                        <tr>
                            <td style="font-size: 0.8rem;"><?= htmlspecialchars(substr($u['uid'] ?? '', 0, 8)) ?>...</td>
                            <td><strong><?= htmlspecialchars($u['username'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                            <td>$<?= number_format(floatval($u['balance'] ?? 0), 2) ?></td>
                            <td><span class="badge badge-<?= ($u['activation_status'] ?? 'inactive') === 'active' ? 'success' : 'warning' ?>"><?= ucfirst($u['activation_status'] ?? 'Inactive') ?></span></td>
                            <td style="font-size: 0.8rem;"><?= isset($u['created_at']) ? date('M d', strtotime($u['created_at'])) : 'N/A' ?></td>
                            <td>
                                <button class="action-btn view" onclick="alert('View user: <?= htmlspecialchars($u['uid'] ?? '') ?>')">View</button>
                                <button class="action-btn reject" onclick="alert('Ban user?')">Ban</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($allUsers)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--current-text-muted);">No users found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <h2 class="section-title">Pending Withdrawals</h2>
        <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $pending = array_filter($allWithdrawals ?? [], fn($w) => ($w['status'] ?? '') === 'pending');
                        foreach (array_slice($pending, 0, 10) as $wd): 
                        ?>
                        <tr>
                            <td><?= htmlspecialchars(substr($wd['user_id'] ?? '', 0, 12)) ?>...</td>
                            <td style="font-weight: 600;">$<?= number_format(floatval($wd['amount'] ?? 0), 2) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $wd['method'] ?? 'Binance')) ?></td>
                            <td style="font-size: 0.8rem;"><?= timeAgo($wd['created_at'] ?? '') ?></td>
                            <td>
                                <button class="action-btn approve" onclick="approveWithdrawal('<?= $wd['id'] ?? '' ?>')">✓ Approve</button>
                                <button class="action-btn reject" onclick="rejectWithdrawal('<?= $wd['id'] ?? '' ?>')">✗ Reject</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($pending)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--current-text-muted);">No pending withdrawals</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="section-title">Quick Actions</h2>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="/admin/users.php" class="btn btn-primary btn-sm">👥 Manage Users</a>
            <a href="/admin/withdrawals.php" class="btn btn-secondary btn-sm">💰 Manage Withdrawals</a>
            <a href="/admin/ads.php" class="btn btn-secondary btn-sm">📺 Manage Ads</a>
            <a href="/admin/settings.php" class="btn btn-secondary btn-sm">⚙️ Settings</a>
            <a href="/admin/security.php" class="btn btn-secondary btn-sm">🛡️ Security</a>
        </div>
    </div>

    <script src="/assets/js/main.js?v=2.0.0"></script>
    <script src="/assets/js/premium.js?v=2.0.0"></script>
    <script src="/assets/js/mobile.js?v=1.0.0"></script>
    <script>
    function approveWithdrawal(id) {
        if (confirm('Approve this withdrawal?')) {
            fetch('/admin/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'approve_withdrawal', id: id})
            }).then(r => r.json()).then(d => {
                if (d.success) { EARNNOVA.showToast('Success', 'Withdrawal approved', 'success'); setTimeout(() => location.reload(), 1000); }
                else { EARNNOVA.showToast('Error', d.message || 'Failed', 'error'); }
            });
        }
    }

    function rejectWithdrawal(id) {
        if (confirm('Reject this withdrawal?')) {
            fetch('/admin/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'reject_withdrawal', id: id})
            }).then(r => r.json()).then(d => {
                if (d.success) { EARNNOVA.showToast('Success', 'Withdrawal rejected', 'success'); setTimeout(() => location.reload(), 1000); }
                else { EARNNOVA.showToast('Error', d.message || 'Failed', 'error'); }
            });
        }
    }
    </script>
</body>
</html>
