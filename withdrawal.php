<?php
// EARNNOVA - Withdrawal Page (Binance Pay)
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();

$pageTitle = 'Withdraw Funds';
$showSidebar = true;

$message = '';
$messageType = '';

// Check withdrawal eligibility
$eligibility = canWithdraw($user['uid']);

// Process withdrawal request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'withdraw') {
    $amount = floatval($_POST['amount'] ?? 0);
    $binanceId = sanitizeInput($_POST['binance_id'] ?? '');
    $binanceEmail = sanitizeInput($_POST['binance_email'] ?? '');
    
    if ($amount < MIN_WITHDRAWAL) {
        $message = 'Minimum withdrawal amount is $' . number_format(MIN_WITHDRAWAL, 2);
        $messageType = 'error';
    } elseif (empty($binanceId) || empty($binanceEmail)) {
        $message = 'Please fill in all Binance details';
        $messageType = 'error';
    } elseif ($amount > floatval($user['balance'] ?? 0)) {
        $message = 'Insufficient balance';
        $messageType = 'error';
    } else {
        // Check rate limit
        if (!rateLimitCheck('withdraw_' . $user['uid'], 3, 3600)) {
            $message = 'Too many withdrawal requests. Please try again later.';
            $messageType = 'error';
        } else {
            // Create withdrawal request
            $withdrawalResult = db()->createWithdrawal([
                'user_id' => $user['uid'],
                'amount' => $amount,
                'method' => 'binance_pay',
                'binance_id' => $binanceId,
                'binance_email' => $binanceEmail,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($withdrawalResult['success']) {
                // Deduct balance
                $newBalance = floatval($user['balance']) - $amount;
                db()->updateUser($user['uid'], ['balance' => $newBalance]);
                
                // Record transaction
                db()->createTransaction([
                    'user_id' => $user['uid'],
                    'amount' => $amount,
                    'type' => 'withdrawal',
                    'description' => 'Withdrawal request via Binance Pay',
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                logActivity($user['uid'], 'withdrawal_request', "Withdrawal request: $$amount");
                
                $message = 'Withdrawal request submitted successfully! Pending admin approval.';
                $messageType = 'success';
                
                // Refresh user data
                $user = getCurrentUser();
            } else {
                $message = 'Failed to process withdrawal. Please try again.';
                $messageType = 'error';
            }
        }
    }
}

// Get withdrawal history
$withdrawals = db()->getWithdrawals($user['uid']);

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">Withdraw Funds</h1>
        <p class="page-subtitle">Withdraw your earnings via Binance Pay</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;" class="responsive-grid">
    <!-- Withdrawal Form -->
    <div class="glass-card" style="padding: 32px;" data-scroll-animate="slide-in-left">
        <h3 style="margin-bottom: 24px;">Withdrawal Request</h3>

        <?php if ($message): ?>
        <div class="toast toast-<?= $messageType ?>" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon"><?= $messageType === 'success' ? '✅' : '❌' ?></span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$eligibility['can']): ?>
        <div class="toast toast-warning" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon">⚠️</span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($eligibility['reason']) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Balance Summary -->
        <div style="display: flex; justify-content: space-between; padding: 16px; background: rgba(6, 214, 160, 0.1); border-radius: 12px; margin-bottom: 24px;">
            <div>
                <div style="font-size: 0.85rem; color: var(--current-text-secondary);">Available Balance</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--emerald-green);" data-balance>
                    $<?= number_format($user['balance'] ?? 0, 2) ?>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.85rem; color: var(--current-text-secondary);">Min. Withdrawal</div>
                <div style="font-size: 1.5rem; font-weight: 700;">$<?= number_format(MIN_WITHDRAWAL, 2) ?></div>
            </div>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="action" value="withdraw">
            
            <div class="form-group">
                <label class="form-label">Amount ($)</label>
                <input type="number" name="amount" class="form-input" step="0.01" min="<?= MIN_WITHDRAWAL ?>" max="<?= $user['balance'] ?? 0 ?>" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label class="form-label">Binance Pay ID</label>
                <input type="text" name="binance_id" class="form-input" placeholder="Enter your Binance ID" required>
            </div>
            <div class="form-group">
                <label class="form-label">Binance Email</label>
                <input type="email" name="binance_email" class="form-input" placeholder="your@email.com" required>
            </div>
            
            <button type="submit" class="btn btn-success btn-block btn-lg" <?= !$eligibility['can'] ? 'disabled' : '' ?>>
                💰 Submit Withdrawal Request
            </button>
        </form>
    </div>

    <!-- Withdrawal History -->
    <div class="glass-card" style="padding: 24px;" data-scroll-animate="slide-in-right">
        <h3 style="margin-bottom: 24px;">Withdrawal History</h3>
        
        <?php if (empty($withdrawals)): ?>
        <div style="text-align: center; padding: 40px; color: var(--current-text-muted);">
            <div style="font-size: 3rem; margin-bottom: 16px;">📭</div>
            <p>No withdrawal requests yet</p>
        </div>
        <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($withdrawals as $wd): ?>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: rgba(255,255,255,0.03); border-radius: 12px;">
                <div>
                    <div style="font-weight: 600;">$<?= number_format(floatval($wd['amount'] ?? 0), 2) ?></div>
                    <div style="font-size: 0.85rem; color: var(--current-text-muted);"><?= timeAgo($wd['created_at'] ?? '') ?></div>
                </div>
                <div>
                    <?php $status = $wd['status'] ?? 'pending'; ?>
                    <span class="badge badge-<?= $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($status) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Withdrawal Info -->
<div class="glass-card" style="padding: 24px; margin-top: 24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom: 16px;">ℹ️ Withdrawal Information</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div>
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Processing Time</div>
            <div style="font-weight: 500;">24-48 hours</div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Minimum Amount</div>
            <div style="font-weight: 500;">$<?= number_format(MIN_WITHDRAWAL, 2) ?></div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Payment Method</div>
            <div style="font-weight: 500;">Binance Pay</div>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--current-text-muted);">Fees</div>
            <div style="font-weight: 500;">No additional fees</div>
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
