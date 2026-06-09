<?php
// EARNNOVA - Shortlink System
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();

$pageTitle = 'Shortlinks';
$showSidebar = true;

$message = '';
$messageType = '';

// Direct link ad URL
$directLinkUrl = 'https://intermediatenormalconfederate.com/dsvae35e8?key=fc5ae318341bc073fbbe172b69b6f3fe';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $code = sanitizeInput($_POST['verify_code']);
    
    if (empty($code)) {
        $message = 'Please enter the verification code';
        $messageType = 'error';
    } else {
        // Verify the shortlink completion (server-side validation)
        // In production, this would verify against the ad network API
        if (strlen($code) >= 4) {
            $reward = DEFAULT_AD_REWARD * 3;
            
            // Check cooldown
            if (db()->checkCooldown($user['uid']) > 0) {
                $message = 'Please wait before completing another shortlink';
                $messageType = 'warning';
            } else {
                // Award reward
                $result = awardAdReward($user['uid'], 'shortlink', $reward);
                
                if ($result['success']) {
                    $message = 'Shortlink completed! You earned $' . number_format($reward, 4);
                    $messageType = 'success';
                    $user = getCurrentUser(); // Refresh
                } else {
                    $message = $result['message'];
                    $messageType = 'error';
                }
            }
        } else {
            $message = 'Invalid verification code';
            $messageType = 'error';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">Shortlinks</h1>
        <p class="page-subtitle">Visit shortlinks and earn rewards</p>
    </div>
</div>

<div style="max-width: 600px; margin: 0 auto;">
    <!-- Premium Verification Card -->
    <div class="glass-card" style="padding: 32px; text-align: center;" data-scroll-animate="scale-in">
        <div style="font-size: 3rem; margin-bottom: 16px;">🔗</div>
        <h2 style="margin-bottom: 8px;">Complete Shortlink</h2>
        <p style="color: var(--current-text-secondary); margin-bottom: 24px;">
            Open the shortlink, wait for completion, then enter the verification code
        </p>

        <?php if ($message): ?>
        <div class="toast toast-<?= $messageType ?>" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon"><?= $messageType === 'success' ? '✅' : ($messageType === 'error' ? '❌' : '⚠️') ?></span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Step 1: Open Shortlink -->
        <div style="margin-bottom: 24px; padding: 20px; background: rgba(255,255,255,0.03); border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">1</div>
                <h3>Open Shortlink</h3>
            </div>
            <a href="<?= htmlspecialchars($directLinkUrl) ?>" target="_blank" class="btn btn-primary btn-block btn-lg" rel="noopener noreferrer">
                🔗 Open Shortlink
            </a>
            <p style="color: var(--current-text-muted); font-size: 0.85rem; margin-top: 8px;">
                Wait a few seconds after opening, then return here
            </p>
        </div>

        <!-- Step 2: Enter Code -->
        <div style="padding: 20px; background: rgba(255,255,255,0.03); border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gradient-accent); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">2</div>
                <h3>Enter Verification Code</h3>
            </div>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="verify_code" class="form-input" placeholder="Enter your code here..." required style="text-align: center; font-size: 1.2rem; letter-spacing: 4px;">
                </div>
                <button type="submit" class="btn btn-success btn-block btn-lg">
                    ✓ Verify & Earn
                </button>
            </form>
            <div style="display: flex; justify-content: space-between; margin-top: 16px; color: var(--current-text-muted); font-size: 0.85rem;">
                <span>Reward: $<?= number_format(DEFAULT_AD_REWARD * 3, 4) ?></span>
                <span>Cooldown: <?= AD_COOLDOWN ?>s</span>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
