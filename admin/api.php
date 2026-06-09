<?php
// EARNNOVA - Admin API
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Admin auth check
session_start();
$isAdmin = $_SESSION['is_admin'] ?? false;
if (!$isAdmin) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'approve_withdrawal':
        handleApproveWithdrawal($input);
        break;
    case 'reject_withdrawal':
        handleRejectWithdrawal($input);
        break;
    case 'ban_user':
        handleBanUser($input);
        break;
    case 'unban_user':
        handleUnbanUser($input);
        break;
    case 'update_settings':
        handleUpdateSettings($input);
        break;
    case 'get_stats':
        handleGetStats();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function handleApproveWithdrawal($input) {
    $id = $input['id'] ?? '';
    if (empty($id)) {
        jsonResponse(['success' => false, 'message' => 'Withdrawal ID required'], 400);
    }
    
    $result = db()->updateWithdrawal($id, [
        'status' => 'approved',
        'approved_at' => date('Y-m-d H:i:s')
    ]);
    
    if ($result['success']) {
        logActivity('admin', 'withdrawal_approved', "Withdrawal $id approved");
        jsonResponse(['success' => true, 'message' => 'Withdrawal approved']);
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to approve'], 500);
    }
}

function handleRejectWithdrawal($input) {
    $id = $input['id'] ?? '';
    if (empty($id)) {
        jsonResponse(['success' => false, 'message' => 'Withdrawal ID required'], 400);
    }
    
    $result = db()->updateWithdrawal($id, [
        'status' => 'rejected',
        'rejected_at' => date('Y-m-d H:i:s')
    ]);
    
    if ($result['success']) {
        logActivity('admin', 'withdrawal_rejected', "Withdrawal $id rejected");
        jsonResponse(['success' => true, 'message' => 'Withdrawal rejected']);
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to reject'], 500);
    }
}

function handleBanUser($input) {
    $uid = $input['uid'] ?? '';
    if (empty($uid)) {
        jsonResponse(['success' => false, 'message' => 'User ID required'], 400);
    }
    
    $result = db()->updateUser($uid, ['is_banned' => true, 'status' => 'banned']);
    if ($result['success']) {
        logActivity('admin', 'user_banned', "User $uid banned");
        jsonResponse(['success' => true, 'message' => 'User banned']);
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to ban user'], 500);
    }
}

function handleUnbanUser($input) {
    $uid = $input['uid'] ?? '';
    if (empty($uid)) {
        jsonResponse(['success' => false, 'message' => 'User ID required'], 400);
    }
    
    $result = db()->updateUser($uid, ['is_banned' => false, 'status' => 'active']);
    if ($result['success']) {
        logActivity('admin', 'user_unbanned', "User $uid unbanned");
        jsonResponse(['success' => true, 'message' => 'User unbanned']);
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to unban user'], 500);
    }
}

function handleUpdateSettings($input) {
    $settings = $input['settings'] ?? [];
    foreach ($settings as $key => $value) {
        db()->updateSetting($key, $value);
    }
    logActivity('admin', 'settings_updated', 'Admin updated settings');
    jsonResponse(['success' => true, 'message' => 'Settings updated']);
}

function handleGetStats() {
    $totalUsers = db()->getTotalUsers();
    $activeUsers = db()->getActiveUsers();
    $todayReg = db()->getTodayRegistrations();
    $totalEarnings = db()->getTotalEarnings();
    $totalWithdrawals = db()->getTotalWithdrawals();
    $pendingWithdrawals = count(db()->getPendingWithdrawals());
    
    jsonResponse([
        'success' => true,
        'data' => [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'today_registrations' => $todayReg,
            'total_earnings' => $totalEarnings,
            'total_withdrawals' => $totalWithdrawals,
            'pending_withdrawals' => $pendingWithdrawals
        ]
    ]);
}
