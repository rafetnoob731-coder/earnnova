<?php
// EARNNOVA - Withdrawal API
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

requireActive();

$user = getCurrentUser();
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'request':
        handleWithdrawalRequest($user, $input);
        break;
    case 'history':
        handleWithdrawalHistory($user);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function handleWithdrawalRequest($user, $input) {
    $amount = floatval($input['amount'] ?? 0);
    $binanceId = sanitizeInput($input['binance_id'] ?? '');
    $binanceEmail = sanitizeInput($input['binance_email'] ?? '');
    
    // Validation
    if ($amount < MIN_WITHDRAWAL) {
        jsonResponse(['success' => false, 'message' => 'Minimum withdrawal amount is $' . number_format(MIN_WITHDRAWAL, 2)], 400);
    }
    if (empty($binanceId) || empty($binanceEmail)) {
        jsonResponse(['success' => false, 'message' => 'Binance details required'], 400);
    }
    if ($amount > floatval($user['balance'] ?? 0)) {
        jsonResponse(['success' => false, 'message' => 'Insufficient balance'], 400);
    }
    
    // Check eligibility
    $eligibility = canWithdraw($user['uid']);
    if (!$eligibility['can']) {
        jsonResponse(['success' => false, 'message' => $eligibility['reason']], 400);
    }
    
    // Check rate limit
    if (!rateLimitCheck('withdraw_api_' . $user['uid'], 3, 3600)) {
        jsonResponse(['success' => false, 'message' => 'Too many requests. Try again later.'], 429);
    }
    
    // Create withdrawal
    $result = db()->createWithdrawal([
        'user_id' => $user['uid'],
        'amount' => $amount,
        'method' => 'binance_pay',
        'binance_id' => $binanceId,
        'binance_email' => $binanceEmail,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    if ($result['success']) {
        // Deduct balance
        $newBalance = floatval($user['balance']) - $amount;
        db()->updateUser($user['uid'], ['balance' => $newBalance]);
        
        db()->createTransaction([
            'user_id' => $user['uid'],
            'amount' => $amount,
            'type' => 'withdrawal',
            'description' => 'Withdrawal via Binance Pay',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        logActivity($user['uid'], 'withdrawal_api', "Withdrawal request: $$amount");
        
        jsonResponse(['success' => true, 'message' => 'Withdrawal request submitted', 'new_balance' => $newBalance]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Failed to process withdrawal'], 500);
    }
}

function handleWithdrawalHistory($user) {
    $withdrawals = db()->getWithdrawals($user['uid']);
    jsonResponse(['success' => true, 'data' => $withdrawals]);
}
