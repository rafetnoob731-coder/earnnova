<?php
// EARNNOVA - Premium Withdrawal Page
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();

$pageTitle = 'Withdraw Funds';
$showSidebar = true;

$message = '';
$messageType = '';

$eligibility = canWithdraw($user['uid']);

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
        if (!rateLimitCheck('withdraw_' . $user['uid'], 3, 3600)) {
            $message = 'Too many withdrawal requests. Please try again later.';
            $messageType = 'error';
        } else {
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
                $newBalance = floatval($user['balance']) - $amount;
                db()->updateUser($user['uid'], ['balance' => $newBalance]);
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
                $user = getCurrentUser();
            } else {
                $message = 'Failed to process withdrawal. Please try again.';
                $messageType = 'error';
            }
        }
    }
}

$withdrawals = db()->getWithdrawals($user['uid']);

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">💰 Withdraw Funds</h1>
        <p class="page-subtitle">Withdraw your earnings via Binance Pay</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="responsive-grid">
    <!-- Withdrawal Form -->
    <div class="glass-2" style="padding:32px;" data-scroll-animate="slide-in-left">
        <h3 style="margin-bottom:24px;font-size:1.3rem;">📤 Withdrawal Request</h3>

        <?php if ($message): ?>
        <div class="toast toast-<?= $messageType ?>" style="margin-bottom:20px;animation:none;">
            <span class="toast-icon"><?= $messageType === 'success' ? '✅' : '❌' ?></span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$eligibility['can']): ?>
        <div class="toast toast-warning" style="margin-bottom:20px;animation:none;">
            <span class="toast-icon">⚠️</span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($eligibility['reason']) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Wallet Card -->
        <div class="balance-card-3d" style="padding:24px;margin-bottom:24px;">
            <div class="balance-pattern"></div>
            <div style="position:relative;z-index:1;">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
                    <div>
                        <div style="font-size:0.8rem;opacity:0.7;text-transform:uppercase;letter-spacing:1px;">Available Balance</div>
                        <div style="font-size:2rem;font-weight:900;color:white;" data-balance>
                            $<?= number_format($user['balance'] ?? 0, 2) ?>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:0.8rem;opacity:0.7;text-transform:uppercase;letter-spacing:1px;">Min. Withdraw</div>
                        <div style="font-size:1.5rem;font-weight:700;color:var(--emerald-green);">$<?= number_format(MIN_WITHDRAWAL, 2) ?></div>
                    </div>
                </div>
                <div style="display:flex;gap:24px;margin-top:16px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.15);">
                    <div>
                        <div style="font-size:0.7rem;opacity:0.6;text-transform:uppercase;">Pending</div>
                        <div style="font-size:1rem;font-weight:600;color:white;">$<?= number_format(array_sum(array_column(array_filter($withdrawals, fn($w) => $w['status'] === 'pending'), 'amount')), 2) ?></div>
                    </div>
                    <div>
                        <div style="font-size:0.7rem;opacity:0.6;text-transform:uppercase;">Total Withdrawn</div>
                        <div style="font-size:1rem;font-weight:600;color:white;">$<?= number_format(array_sum(array_column(array_filter($withdrawals, fn($w) => $w['status'] === 'approved'), 'amount')), 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="action" value="withdraw">
            
            <div class="form-group input-premium">
                <label class="form-label">Amount ($)</label>
                <input type="number" name="amount" class="form-input" step="0.01" min="<?= MIN_WITHDRAWAL ?>" max="<?= $user['balance'] ?? 0 ?>" placeholder="0.00" required>
                <span class="input-focus-line"></span>
            </div>
            <div class="form-group input-premium">
                <label class="form-label">Binance Pay ID</label>
                <input type="text" name="binance_id" class="form-input" placeholder="Enter your Binance ID" required>
                <span class="input-focus-line"></span>
            </div>
            <div class="form-group input-premium">
                <label class="form-label">Binance Email</label>
                <input type="email" name="binance_email" class="form-input" placeholder="your@email.com" required>
                <span class="input-focus-line"></span>
            </div>
            
            <button type="submit" class="btn btn-premium btn-success btn-block btn-lg btn-magnetic" style="padding:16px;" <?= !$eligibility['can'] ? 'disabled' : '' ?>>
                💰 Submit Withdrawal Request
            </button>
        </form>
    </div>

    <!-- Withdrawal History + Timeline -->
    <div class="glass-2" style="padding:28px;" data-scroll-animate="slide-in-right">
        <h3 style="margin-bottom:24px;">📜 Withdrawal History</h3>
        
        <?php if (empty($withdrawals)): ?>
        <div style="text-align:center;padding:40px;color:var(--current-text-muted);">
            <div style="font-size:4rem;margin-bottom:16px;">📭</div>
            <p style="font-size:1.1rem;">No withdrawal requests yet</p>
        </div>
        <?php else: ?>
        <!-- Timeline -->
        <div style="position:relative;padding-left:24px;margin-bottom:24px;">
            <div style="position:absolute;left:8px;top:0;bottom:0;width:2px;background:var(--glass-border-2);"></div>
            <?php foreach (array_slice($withdrawals, 0, 10) as $wd): 
                $status = $wd['status'] ?? 'pending';
                $colors = ['pending' => 'var(--warning)', 'approved' => 'var(--emerald-green)', 'rejected' => 'var(--error)'];
                $icons = ['pending' => '⏳', 'approved' => '✅', 'rejected' => '❌'];
            ?>
            <div style="position:relative;padding:12px 0 12px 16px;border-left:2px solid <?= $colors[$status] ?>;margin-bottom:8px;">
                <div style="position:absolute;left:-9px;top:16px;width:16px;height:16px;border-radius:50%;background:<?= $colors[$status] ?>;border:3px solid var(--current-bg);"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <div style="font-weight:600;">$<?= number_format(floatval($wd['amount'] ?? 0), 2) ?></div>
                        <div style="font-size:0.85rem;color:var(--current-text-muted);"><?= timeAgo($wd['created_at'] ?? '') ?></div>
                    </div>
                    <div>
                        <span class="badge badge-<?= $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') ?>">
                            <?= $icons[$status] ?> <?= ucfirst($status) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Info Cards -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:20px;">
            <div style="padding:16px;background:rgba(255,255,255,0.03);border-radius:12px;">
                <div style="font-size:0.8rem;color:var(--current-text-muted);">Processing Time</div>
                <div style="font-weight:600;margin-top:4px;">⏱ 24-48 hours</div>
            </div>
            <div style="padding:16px;background:rgba(255,255,255,0.03);border-radius:12px;">
                <div style="font-size:0.8rem;color:var(--current-text-muted);">Payment Method</div>
                <div style="font-weight:600;margin-top:4px;">💳 Binance Pay</div>
            </div>
            <div style="padding:16px;background:rgba(255,255,255,0.03);border-radius:12px;">
                <div style="font-size:0.8rem;color:var(--current-text-muted);">Fees</div>
                <div style="font-weight:600;margin-top:4px;color:var(--emerald-green);">✅ No fees</div>
            </div>
            <div style="padding:16px;background:rgba(255,255,255,0.03);border-radius:12px;">
                <div style="font-size:0.8rem;color:var(--current-text-muted);">Minimum</div>
                <div style="font-weight:600;margin-top:4px;">$<?= number_format(MIN_WITHDRAWAL, 2) ?></div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .responsive-grid { grid-template-columns: 1fr !important; }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
