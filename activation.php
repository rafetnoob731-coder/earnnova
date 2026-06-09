<?php
// EARNNOVA - Account Activation Page (PlatoBoost Integration)
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/lib/Platoboost.php';

requireLogin();
$user = getCurrentUser();

$pageTitle = 'Activate Account';
$showSidebar = true;

// Check if already activated
if (($user['activation_status'] ?? 'inactive') === 'active') {
    header('Location: /dashboard.php');
    exit;
}

$message = '';
$messageType = '';
$boostLink = '';
$activationStep = 'initial'; // initial, got_link, key_entered

// Initialize PlatoBoost
$boostCallback = function($msg) use (&$message, &$messageType) {
    $message = $msg;
    $messageType = 'info';
};

try {
    $boost = new Platoboost($boostCallback);
    $boostLink = $boost->getLink();
    if ($boostLink) {
        $activationStep = 'got_link';
    }
} catch (Exception $e) {
    $message = 'Failed to connect to activation service: ' . $e->getMessage();
    $messageType = 'error';
}

// Handle key verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activation_key'])) {
    $key = sanitizeInput($_POST['activation_key']);
    
    if (empty($key)) {
        $message = 'Please enter your activation key';
        $messageType = 'error';
    } else {
        try {
            $boost = new Platoboost();
            $valid = $boost->verifyKey($key);
            
            if ($valid) {
                // Activate the user
                $updateResult = db()->updateUser($user['uid'], [
                    'activation_status' => 'active',
                    'activation_key' => $key,
                    'activation_date' => date('Y-m-d H:i:s')
                ]);
                
                if ($updateResult['success']) {
                    // Log activation
                    db()->createActivationLog([
                        'user_id' => $user['uid'],
                        'key' => $key,
                        'result' => 'success',
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'device' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    // Give activation bonus
                    $activationBonus = 0.05;
                    addBalance($user['uid'], $activationBonus);
                    db()->createTransaction([
                        'user_id' => $user['uid'],
                        'amount' => $activationBonus,
                        'type' => 'activation_bonus',
                        'description' => 'Account activation bonus',
                        'status' => 'completed',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    logActivity($user['uid'], 'activation', 'Account activated via PlatoBoost');
                    
                    $message = '🎉 Account activated successfully! You received a $' . number_format($activationBonus, 2) . ' bonus!';
                    $messageType = 'success';
                    $activationStep = 'completed';
                } else {
                    $message = 'Failed to activate account. Please try again.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Invalid activation key. Please try again.';
                $messageType = 'error';
                
                // Log failed attempt
                db()->createActivationLog([
                    'user_id' => $user['uid'],
                    'key' => $key,
                    'result' => 'failed',
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'device' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (Exception $e) {
            $message = 'Verification error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div style="max-width: 600px; margin: 40px auto;">
    <?php if ($activationStep === 'completed'): ?>
    <!-- Success State -->
    <div class="glass-card" style="padding: 40px; text-align: center;" data-scroll-animate="scale-in">
        <div style="font-size: 4rem; margin-bottom: 16px;">🎉</div>
        <h2 style="font-size: 1.8rem; margin-bottom: 12px;">Account Activated!</h2>
        <p style="color: var(--current-text-secondary); margin-bottom: 24px;">
            Your account is now active. You can start earning rewards immediately!
        </p>
        <a href="/dashboard.php" class="btn btn-primary btn-lg">Go to Dashboard</a>
    </div>
    <?php else: ?>
    <!-- Activation Flow -->
    <div class="glass-card" style="padding: 40px;" data-scroll-animate="fade-in">
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="font-size: 3rem; margin-bottom: 12px;">🔐</div>
            <h2 style="font-size: 1.5rem;">Activate Your Account</h2>
            <p style="color: var(--current-text-secondary); margin-top: 8px;">
                Complete the PlatoBoost verification to unlock all features
            </p>
        </div>

        <?php if ($message): ?>
        <div class="toast toast-<?= $messageType ?>" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon"><?= $messageType === 'success' ? '✅' : ($messageType === 'error' ? '❌' : 'ℹ️') ?></span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Step 1: Get Link -->
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem;">1</div>
                <h3>Get Activation Link</h3>
            </div>
            <?php if ($boostLink): ?>
            <p style="color: var(--current-text-secondary); margin-bottom: 12px;">
                Click the button below to open the PlatoBoost verification page:
            </p>
            <a href="<?= htmlspecialchars($boostLink) ?>" target="_blank" class="btn btn-primary btn-block" rel="noopener noreferrer">
                🔗 Open Verification Page
            </a>
            <?php else: ?>
            <div class="toast toast-warning" style="animation: none;">
                <span class="toast-icon">⚠️</span>
                <div class="toast-content">
                    <div class="toast-message">Unable to generate activation link. Please try again later.</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Step 2: Instructions -->
        <div style="margin-bottom: 24px; padding: 16px; background: rgba(255, 255, 255, 0.03); border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem;">2</div>
                <h3>Complete Verification</h3>
            </div>
            <ol style="color: var(--current-text-secondary); padding-left: 20px; line-height: 1.8;">
                <li>Click the button above to open the verification page</li>
                <li>Complete the shortlink or key process</li>
                <li>Copy the key you receive</li>
                <li>Paste it below and click Verify</li>
            </ol>
        </div>

        <!-- Step 3: Enter Key -->
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem;">3</div>
                <h3>Enter Activation Key</h3>
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="activation_key" class="form-input" placeholder="Paste your activation key here..." required>
                </div>
                <button type="submit" class="btn btn-success btn-block btn-lg">✓ Verify & Activate</button>
            </form>
        </div>
    </div>

    <!-- Features that will be unlocked -->
    <div class="glass-card" style="padding: 24px; margin-top: 24px;" data-scroll-animate="fade-in">
        <h3 style="margin-bottom: 16px;">🚀 Features You'll Unlock</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 8px; color: var(--current-text-secondary);">
                <span>📺</span> Watch Ads
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--current-text-secondary);">
                <span>📋</span> Complete Tasks
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--current-text-secondary);">
                <span>🔗</span> Shortlinks
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--current-text-secondary);">
                <span>👥</span> Referral Rewards
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--current-text-secondary);">
                <span>💰</span> Withdrawals
            </div>
            <div style="display: flex; align-items: center; gap: 8px; color: var(--current-text-secondary);">
                <span>🎁</span> Daily Bonuses
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
