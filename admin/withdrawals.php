<?php
// EARNNOVA - Admin Withdrawals Management
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;
if (!$isAdmin) { header('Location: /admin/index.php'); exit; }

$allWithdrawals = db()->getAllWithdrawals();
$pageTitle = 'Withdrawal Management';
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
            <a href="/admin/withdrawals.php" class="nav-item active">💰 Withdrawals</a>
            <a href="/admin/ads.php" class="nav-item">📺 Ads</a>
            <a href="/admin/settings.php" class="nav-item">⚙️ Settings</a>
        </nav>
    </aside>
    <div class="admin-content">
        <div class="admin-header">
            <h1><?= $pageTitle ?></h1>
            <div style="display: flex; gap: 12px;">
                <a href="?filter=pending" class="btn btn-sm btn-warning">Pending</a>
                <a href="?filter=approved" class="btn btn-sm btn-success">Approved</a>
                <a href="?filter=rejected" class="btn btn-sm btn-danger">Rejected</a>
                <a href="?" class="btn btn-sm btn-secondary">All</a>
            </div>
        </div>

        <div style="background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 12px; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="data-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Binance ID</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $filter = $_GET['filter'] ?? '';
                        foreach ($allWithdrawals as $wd): 
                            if ($filter && ($wd['status'] ?? '') !== $filter) continue;
                        ?>
                        <tr>
                            <td style="font-size: 0.8rem;">#<?= htmlspecialchars(substr($wd['id'] ?? '', 0, 8)) ?></td>
                            <td><?= htmlspecialchars(substr($wd['user_id'] ?? '', 0, 12)) ?>...</td>
                            <td style="font-weight: 700;">$<?= number_format(floatval($wd['amount'] ?? 0), 2) ?></td>
                            <td><?= ucfirst(str_replace('_', ' ', $wd['method'] ?? 'Binance Pay')) ?></td>
                            <td style="font-size: 0.85rem;"><?= htmlspecialchars($wd['binance_id'] ?? 'N/A') ?></td>
                            <td>
                                <?php $status = $wd['status'] ?? 'pending'; ?>
                                <span class="badge badge-<?= $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;"><?= timeAgo($wd['created_at'] ?? '') ?></td>
                            <td>
                                <?php if (($wd['status'] ?? '') === 'pending'): ?>
                                <button class="btn btn-sm btn-success" onclick="approve('<?= htmlspecialchars($wd['id'] ?? '') ?>')">✓</button>
                                <button class="btn btn-sm btn-danger" onclick="reject('<?= htmlspecialchars($wd['id'] ?? '') ?>')">✗</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($allWithdrawals)): ?>
                        <tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--current-text-muted);">No withdrawals found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="/assets/js/main.js?v=2.0.0"></script>
    <script src="/assets/js/premium.js?v=2.0.0"></script>
    <script src="/assets/js/mobile.js?v=1.0.0"></script>
    <script>
    function approve(id) {
        if (confirm('Approve this withdrawal?')) {
            fetch('/admin/api.php', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'approve_withdrawal', id: id})
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else EARNNOVA.showToast('Error', d.message, 'error');
            });
        }
    }
    function reject(id) {
        if (confirm('Reject this withdrawal?')) {
            fetch('/admin/api.php', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'reject_withdrawal', id: id})
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
                else EARNNOVA.showToast('Error', d.message, 'error');
            });
        }
    }
    </script>
</body>
</html>
