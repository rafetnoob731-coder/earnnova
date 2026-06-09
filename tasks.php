<?php
// EARNNOVA - Tasks Page
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();

$pageTitle = 'Tasks';
$showSidebar = true;

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">Tasks Center</h1>
        <p class="page-subtitle">Complete tasks and earn additional rewards</p>
    </div>
</div>

<!-- Rewarded Ad Tasks -->
<div class="card-grid stagger-children">
    <!-- Rewarded Interstitial Task -->
    <div class="glass-card" style="padding: 32px; text-align: center;" data-scroll-animate="slide-in-up">
        <div style="font-size: 3rem; margin-bottom: 16px;">📺</div>
        <h3 style="margin-bottom: 8px;">Watch Rewarded Interstitial</h3>
        <p style="color: var(--current-text-secondary); margin-bottom: 16px;">
            Watch a full-screen rewarded ad
        </p>
        <div style="font-size: 1.5rem; font-weight: 700; color: var(--emerald-green); margin-bottom: 16px;">
            +$<?= number_format(DEFAULT_AD_REWARD * 2, 4) ?>
        </div>
        <button class="btn btn-primary btn-lg btn-block" onclick="startInterstitialTask()">
            ▶ Watch Ad
        </button>
    </div>

    <!-- Rewarded Popup Task -->
    <div class="glass-card" style="padding: 32px; text-align: center;" data-scroll-animate="slide-in-up">
        <div style="font-size: 3rem; margin-bottom: 16px;">🪟</div>
        <h3 style="margin-bottom: 8px;">Watch Rewarded Popup</h3>
        <p style="color: var(--current-text-secondary); margin-bottom: 16px;">
            Watch a popup advertisement
        </p>
        <div style="font-size: 1.5rem; font-weight: 700; color: var(--emerald-green); margin-bottom: 16px;">
            +$<?= number_format(DEFAULT_AD_REWARD * 1.5, 4) ?>
        </div>
        <button class="btn btn-primary btn-lg btn-block" onclick="startPopupTask()">
            ▶ Watch Ad
        </button>
    </div>

    <!-- Coming Soon Tasks -->
    <div class="glass-card" style="padding: 32px; text-align: center; opacity: 0.6;" data-scroll-animate="slide-in-up">
        <div style="font-size: 3rem; margin-bottom: 16px;">📋</div>
        <h3 style="margin-bottom: 8px;">Social Tasks</h3>
        <p style="color: var(--current-text-secondary); margin-bottom: 16px;">
            Follow, like, and share on social media
        </p>
        <span class="badge badge-warning">Coming Soon</span>
    </div>

    <div class="glass-card" style="padding: 32px; text-align: center; opacity: 0.6;" data-scroll-animate="slide-in-up">
        <div style="font-size: 3rem; margin-bottom: 16px;">📱</div>
        <h3 style="margin-bottom: 8px;">App Install Tasks</h3>
        <p style="color: var(--current-text-secondary); margin-bottom: 16px;">
            Install and try partner apps
        </p>
        <span class="badge badge-warning">Coming Soon</span>
    </div>

    <div class="glass-card" style="padding: 32px; text-align: center; opacity: 0.6;" data-scroll-animate="slide-in-up">
        <div style="font-size: 3rem; margin-bottom: 16px;">📝</div>
        <h3 style="margin-bottom: 8px;">Survey Tasks</h3>
        <p style="color: var(--current-text-secondary); margin-bottom: 16px;">
            Complete surveys and earn
        </p>
        <span class="badge badge-warning">Coming Soon</span>
    </div>

    <div class="glass-card" style="padding: 32px; text-align: center; opacity: 0.6;" data-scroll-animate="slide-in-up">
        <div style="font-size: 3rem; margin-bottom: 16px;">🌐</div>
        <h3 style="margin-bottom: 8px;">Website Visit Tasks</h3>
        <p style="color: var(--current-text-secondary); margin-bottom: 16px;">
            Visit partner websites
        </p>
        <span class="badge badge-warning">Coming Soon</span>
    </div>
</div>

<!-- Footer Banner Ad -->
<div style="max-width: 728px; margin: 30px auto; text-align: center;" data-scroll-animate="fade-in">
    <div class="glass-card" style="padding: 12px;">
        <div style="font-size: 0.7rem; color: var(--current-text-muted); margin-bottom: 4px;">Sponsored</div>
        <script>
        atOptions = {
            'key' : 'b93981a2156c99e7ad6889545f4412c0',
            'format' : 'iframe',
            'height' : 50,
            'width' : 320,
            'params' : {}
        };
        </script>
        <script src="https://intermediatenormalconfederate.com/b93981a2156c99e7ad6889545f4412c0/invoke.js" async></script>
    </div>
</div>

<script>
function startInterstitialTask() {
    if (typeof show_9622450 === 'function') {
        EARNNOVA.showToast('Loading', 'Loading advertisement...', 'info');
        show_9622450().then(() => {
            claimTaskReward('rewarded_interstitial', <?= DEFAULT_AD_REWARD * 2 ?>);
        }).catch((e) => {
            EARNNOVA.showToast('Error', 'Ad failed to load', 'error');
        });
    } else {
        EARNNOVA.showToast('Info', 'Ad network loading. Please try again in a moment.', 'info');
        // Load ad script dynamically
        var script = document.createElement('script');
        script.src = 'https://intermediatenormalconfederate.com/4f/ce/2d/4fce2d5c0aff67487512169e5ed4ba87.js';
        script.onload = function() {
            setTimeout(() => {
                if (typeof show_9622450 === 'function') {
                    show_9622450().then(() => {
                        claimTaskReward('rewarded_interstitial', <?= DEFAULT_AD_REWARD * 2 ?>);
                    }).catch((e) => {
                        EARNNOVA.showToast('Error', 'Ad failed to load', 'error');
                    });
                }
            }, 1000);
        };
        document.body.appendChild(script);
    }
}

function startPopupTask() {
    if (typeof show_9622450 === 'function') {
        EARNNOVA.showToast('Loading', 'Loading advertisement...', 'info');
        show_9622450('pop').then(() => {
            claimTaskReward('rewarded_popup', <?= DEFAULT_AD_REWARD * 1.5 ?>);
        }).catch((e) => {
            EARNNOVA.showToast('Error', 'Ad failed to load', 'error');
        });
    } else {
        EARNNOVA.showToast('Info', 'Ad network loading. Please try again in a moment.', 'info');
    }
}

function claimTaskReward(adType, amount) {
    fetch('/api/rewards.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= generateCSRFToken() ?>'
        },
        body: JSON.stringify({
            action: 'claim_reward',
            ad_type: adType,
            reward_amount: amount
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            EARNNOVA.showSuccess(
                '🎉 Task Completed!',
                `You earned $${parseFloat(amount).toFixed(4)}`
            );
            document.querySelectorAll('[data-balance]').forEach(el => {
                el.textContent = '$' + parseFloat(data.new_balance || 0).toFixed(2);
            });
            setTimeout(() => location.reload(), 3000);
        } else {
            EARNNOVA.showToast('Error', data.message || 'Failed to claim reward', 'error');
        }
    })
    .catch(error => {
        EARNNOVA.showToast('Error', 'Network error', 'error');
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
