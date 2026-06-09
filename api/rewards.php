<?php
// EARNNOVA - Rewards API
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Verify CSRF token
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verifyCSRFToken($csrfToken)) {
    jsonResponse(['success' => false, 'message' => 'Invalid security token'], 403);
}

// Must be logged in
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Authentication required'], 401);
}

$user = getCurrentUser();
if (!$user) {
    jsonResponse(['success' => false, 'message' => 'User not found'], 404);
}

// Check account is active
if (($user['activation_status'] ?? 'inactive') !== 'active') {
    jsonResponse(['success' => false, 'message' => 'Account not activated'], 403);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'claim_reward':
        handleClaimReward($user);
        break;
    case 'get_stats':
        handleGetStats($user);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function handleClaimReward($user) {
    global $input;
    
    $adType = sanitizeInput($input['ad_type'] ?? '');
    $rewardAmount = floatval($input['reward_amount'] ?? 0);
    
    // Validate ad type
    $allowedTypes = ['rewarded_interstitial', 'rewarded_popup', 'banner', 'shortlink'];
    if (!in_array($adType, $allowedTypes)) {
        jsonResponse(['success' => false, 'message' => 'Invalid ad type'], 400);
    }
    
    // Validate reward amount
    if ($rewardAmount <= 0 || $rewardAmount > 1.0) {
        jsonResponse(['success' => false, 'message' => 'Invalid reward amount'], 400);
    }
    
    // Check rate limiting
    $rateKey = 'reward_' . $user['uid'];
    if (!rateLimitCheck($rateKey, 30, 60)) {
        jsonResponse(['success' => false, 'message' => 'Rate limit exceeded. Please slow down.'], 429);
    }
    
    // Process the reward
    $result = awardAdReward($user['uid'], $adType, $rewardAmount);
    
    if ($result['success']) {
        // Get updated user data
        $updatedUser = getCurrentUser();
        $todayRewards = db()->getTodayAdRewards($user['uid']);
        $todayEarnings = array_sum(array_column($todayRewards, 'reward_amount'));
        
        jsonResponse([
            'success' => true,
            'message' => $result['message'],
            'amount' => $result['amount'],
            'transaction_id' => $result['transaction_id'],
            'new_balance' => floatval($updatedUser['balance'] ?? 0),
            'today_earnings' => $todayEarnings,
            'ads_watched_today' => count($todayRewards)
        ]);
    } else {
        jsonResponse(['success' => false, 'message' => $result['message']], 400);
    }
}

function handleGetStats($user) {
    $todayRewards = db()->getTodayAdRewards($user['uid']);
    $todayEarnings = array_sum(array_column($todayRewards, 'reward_amount'));
    $referralBalance = floatval($user['referral_balance'] ?? 0);
    $transactions = db()->getTransactions($user['uid']);
    
    jsonResponse([
        'success' => true,
        'data' => [
            'balance' => floatval($user['balance'] ?? 0),
            'referral_balance' => $referralBalance,
            'today_earnings' => $todayEarnings,
            'ads_watched_today' => count($todayRewards),
            'daily_limit' => DAILY_AD_LIMIT,
            'total_transactions' => count($transactions),
            'account_status' => $user['activation_status'] ?? 'inactive'
        ]
    ]);
}
