<?php
// EARNNOVA - Admin Users Management
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;
if (!$isAdmin) { header('Location: /admin/index.php'); exit; }

$allUsers = db()->getAllUsers();
$pageTitle = 'User Management';
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
        .admin-sidebar .nav-item { display: block; padding: 10px 20px; color: var(--current-text-secondary); text-decoration: none; font-size: 0.9rem; }
        .admin-sidebar .nav-item:hover { background: rgba(255,255,255,0.05); }
        .admin-sidebar .nav-item.active { background: var(--gradient-primary); color: white; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .search-box { padding: 10px 16px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 8px; color: var(--current-text); width: 300px; }
        @media (max-width: 768px) { .admin-sidebar { display: none; } .admin-content { margin-left: 0; } }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div style="padding: 0 20px 20px; text-align: center; border-bottom: 1px solid var(--glass-border); margin-bottom: 16px;">
            <h2 style="font-size: 1.2rem;"><?= SITE_NAME ?></h2>
            <p style="font-size: 0.8rem; color: var(--current-text-muted);">Admin Panel</p>
        </div>
        <nav>
            <a href="/admin/index.php" class="nav-item">📊 Dashboard</a>
            <a href="/admin/users.php" class="nav-item active">👥 Users</a>
            <a href="/admin/withdrawals.php" class="nav-item">💰 Withdrawals</a>
            <a href="/admin/ads.php" class="nav-item">📺 Ads</a>
            <a href="/admin/settings.php" class="nav-item">⚙️ Settings</a>
            <a href="/dashboard.php" class="nav-item">← Back</a>
        </nav>
    </aside>
    <div class="admin-content">
        <div class="admin-header">
            <h1><?= $pageTitle ?></h1>
            <input type="text" class="search-box" placeholder="🔍 Search users..." id="searchInput" onkeyup="filterUsers()">
        </div>
        
        <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="data-table" id="usersTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>UID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Balance</th>
                            <th>Referral</th>
                            <th>Status</th>
                            <th>Activation</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $u): ?>
                        <tr class="user-row">
                            <td style="font-size: 0.8rem;"><?= htmlspecialchars(substr($u['uid'] ?? '', 0, 12)) ?>...</td>
                            <td><strong><?= htmlspecialchars($u['username'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                            <td>$<?= number_format(floatval($u['balance'] ?? 0), 2) ?></td>
                            <td>$<?= number_format(floatval($u['referral_balance'] ?? 0), 2) ?></td>
                            <td>
                                <?php $status = $u['activation_status'] ?? 'inactive'; ?>
                                <span class="badge badge-<?= $status === 'active' ? 'success' : ($status === 'banned' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;"><?= !empty($u['activation_date']) ? date('M d', strtotime($u['activation_date'])) : 'N/A' ?></td>
                            <td style="font-size: 0.85rem;"><?= isset($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : 'N/A' ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="banUser('<?= htmlspecialchars($u['uid'] ?? '') ?>')">Ban</button>
                                <button class="btn btn-sm btn-secondary" onclick="viewUser('<?= htmlspecialchars($u['uid'] ?? '') ?>')">View</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?v=2.0.0"></script>
    <script src="/assets/js/premium.js?v=2.0.0"></script>
    <script src="/assets/js/mobile.js?v=1.0.0"></script>
    <script>
    function filterUsers() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('.user-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function banUser(uid) {
        if (confirm('Ban this user?')) {
            fetch('/admin/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'ban_user', uid: uid})
            }).then(r => r.json()).then(d => {
                if (d.success) { EARNNOVA.showToast('Success', 'User banned', 'success'); setTimeout(() => location.reload(), 1000); }
                else { EARNNOVA.showToast('Error', d.message, 'error'); }
            });
        }
    }

    function viewUser(uid) {
        alert('User details: ' + uid + '\nFull details view coming soon.');
    }
    </script>
</body>
</html>
