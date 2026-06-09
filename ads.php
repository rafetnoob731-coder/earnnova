<?php
// EARNNOVA - Premium Watch Ads Page
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();

$pageTitle = 'Watch & Earn';
$showSidebar = true;

$todayRewards = db()->getTodayAdRewards($user['uid']);
$adsWatchedToday = count($todayRewards);
$remainingAds = DAILY_AD_LIMIT - $adsWatchedToday;

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">📺 Watch & Earn</h1>
        <p class="page-subtitle">Watch premium advertisements and earn rewards instantly</p>
    </div>
    <div style="display:flex;gap:16px;align-items:center;">
        <div class="cooldown-timer" id="cooldownTimer" style="<?= $adsWatchedToday === 0 ? 'display:none;' : '' ?>">
            ⏳ <span class="timer" data-cooldown>Ready</span>
        </div>
        <span class="badge badge-info">🎯 <?= $remainingAds ?> / <?= DAILY_AD_LIMIT ?> remaining</span>
    </div>
</div>

<!-- Earnings Today Banner -->
<div class="glass-2" style="padding:20px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;" data-scroll-animate="fade-in">
    <div>
        <div style="font-size:0.85rem;color:var(--current-text-secondary);">Today's Earnings</div>
        <div style="font-size:1.8rem;font-weight:800;background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;" id="todayEarnings">
            $<?= number_format(array_sum(array_column($todayRewards, 'reward_amount')), 4) ?>
        </div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:0.85rem;color:var(--current-text-secondary);">Balance</div>
        <div style="font-size:1.8rem;font-weight:800;" data-balance>
            $<?= number_format($user['balance'] ?? 0, 2) ?>
        </div>
    </div>
    <div>
        <div class="progress-ring" data-percent="<?= min(100, ($adsWatchedToday / DAILY_AD_LIMIT) * 100) ?>" data-size="70" data-stroke-width="5" style="width:70px;height:70px;"></div>
    </div>
</div>

<!-- Ad Types Grid -->
<div class="card-grid stagger-children">
    <!-- Rewarded Interstitial -->
    <div class="glass-2 ad-card" onclick="watchRewardedInterstitial()" data-scroll-animate="slide-in-up">
        <div class="card-3d">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div style="font-size:3.5rem;margin-bottom:12px;">📺</div>
                <div class="ad-type">Rewarded Video</div>
                <div class="ad-reward" style="font-size:2.2rem;">$<?= number_format(DEFAULT_AD_REWARD * 2, 4) ?></div>
                <p style="color:var(--current-text-secondary);font-size:0.9rem;margin-bottom:16px;">
                    Watch a full-screen video ad to earn rewards
                </p>
                <div class="ad-progress">
                    <div class="ad-progress-bar" style="width:0%" id="progress1"></div>
                </div>
                <button class="btn btn-premium btn-primary btn-block mt-2 btn-magnetic btn-glow" id="watchBtn1" onclick="event.stopPropagation(); watchRewardedInterstitial()">
                    ▶️ Watch & Earn
                </button>
            </div>
        </div>
    </div>

    <!-- Rewarded Popup -->
    <div class="glass-2 ad-card" onclick="watchRewardedPopup()" data-scroll-animate="slide-in-up">
        <div class="card-3d">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div style="font-size:3.5rem;margin-bottom:12px;">🪟</div>
                <div class="ad-type">Rewarded Popup</div>
                <div class="ad-reward" style="font-size:2.2rem;">$<?= number_format(DEFAULT_AD_REWARD * 1.5, 4) ?></div>
                <p style="color:var(--current-text-secondary);font-size:0.9rem;margin-bottom:16px;">
                    Watch a popup ad and earn rewards
                </p>
                <div class="ad-progress">
                    <div class="ad-progress-bar" style="width:0%" id="progress2"></div>
                </div>
                <button class="btn btn-premium btn-primary btn-block mt-2 btn-magnetic" id="watchBtn2" onclick="event.stopPropagation(); watchRewardedPopup()">
                    ▶️ Watch & Earn
                </button>
            </div>
        </div>
    </div>

    <!-- Banner Ad -->
    <div class="glass-2 ad-card" onclick="watchBannerAd()" data-scroll-animate="slide-in-up">
        <div class="card-3d">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div style="font-size:3.5rem;margin-bottom:12px;">🖼️</div>
                <div class="ad-type">Banner View</div>
                <div class="ad-reward" style="font-size:2.2rem;">$<?= number_format(DEFAULT_AD_REWARD, 4) ?></div>
                <p style="color:var(--current-text-secondary);font-size:0.9rem;margin-bottom:16px;">
                    View a banner advertisement
                </p>
                <div class="ad-progress">
                    <div class="ad-progress-bar" style="width:0%" id="progress3"></div>
                </div>
                <button class="btn btn-premium btn-secondary btn-block mt-2 btn-magnetic" id="watchBtn3" onclick="event.stopPropagation(); watchBannerAd()">
                    👁️ View & Earn
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Banner Ad Placement -->
<div style="max-width:728px;margin:30px auto;text-align:center;" data-scroll-animate="fade-in">
    <div class="glass-2 border-animated" style="padding:16px;">
        <div style="font-size:0.75rem;color:var(--current-text-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:1px;">Sponsored</div>
        <div id="banner-ad-content">
            <script>
            atOptions = {
                'key' : 'e877abd9733752f8dbd622496db4c8a3',
                'format' : 'iframe',
                'height' : 90,
                'width' : 728,
                'params' : {}
            };
            </script>
            <script src="https://intermediatenormalconfederate.com/e877abd9733752f8dbd622496db4c8a3/invoke.js" async></script>
        </div>
    </div>
</div>

<!-- Ad History -->
<div class="glass-2" style="padding:24px;margin-top:30px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom:16px;">📜 Ad History</h3>
    <?php if (empty($todayRewards)): ?>
    <div style="text-align:center;padding:40px;color:var(--current-text-muted);">
        <div style="font-size:3rem;margin-bottom:8px;">📭</div>
        <p>No ads watched today. Start earning now!</p>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Reward</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todayRewards as $reward): ?>
                <tr>
                    <td><?= ucfirst(str_replace('_', ' ', $reward['ad_type'] ?? 'Unknown')) ?></td>
                    <td style="color:var(--emerald-green);font-weight:700;">+$<?= number_format(floatval($reward['reward_amount'] ?? 0), 4) ?></td>
                    <td><?= timeAgo($reward['created_at'] ?? date('Y-m-d H:i:s')) ?></td>
                    <td><span class="badge badge-success">✅ Completed</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
let isProcessing = false;

function watchRewardedInterstitial() {
    if (isProcessing) return;
    if (<?= $remainingAds <= 0 ? 'true' : 'false' ?>) {
        EARNNOVA.showToast('Limit Reached', 'You have reached your daily ad limit', 'warning');
        return;
    }
    isProcessing = true;
    const btn = document.getElementById('watchBtn1');
    const progress = document.getElementById('progress1');
    btn.disabled = true;
    btn.textContent = '⏳ Loading...';
    setTimeout(() => {
        btn.textContent = '📺 Playing...';
        if (typeof show_9622450 === 'function') {
            show_9622450().then(() => {
                rewardUser('rewarded_interstitial', <?= DEFAULT_AD_REWARD * 2 ?>);
            }).catch((e) => {
                isProcessing = false;
                btn.disabled = false;
                btn.textContent = '▶️ Watch & Earn';
                EARNNOVA.showToast('Error', 'Ad failed to load', 'error');
            });
        } else {
            simulateAdWatch(progress, btn, 'rewarded_interstitial', <?= DEFAULT_AD_REWARD * 2 ?>);
        }
    }, 1000);
}

function watchRewardedPopup() {
    if (isProcessing) return;
    if (<?= $remainingAds <= 0 ? 'true' : 'false' ?>) {
        EARNNOVA.showToast('Limit Reached', 'You have reached your daily ad limit', 'warning');
        return;
    }
    isProcessing = true;
    const btn = document.getElementById('watchBtn2');
    const progress = document.getElementById('progress2');
    btn.disabled = true;
    btn.textContent = '⏳ Loading...';
    setTimeout(() => {
        btn.textContent = '🪟 Opening...';
        if (typeof show_9622450 === 'function') {
            show_9622450('pop').then(() => {
                rewardUser('rewarded_popup', <?= DEFAULT_AD_REWARD * 1.5 ?>);
            }).catch((e) => {
                isProcessing = false;
                btn.disabled = false;
                btn.textContent = '▶️ Watch & Earn';
                EARNNOVA.showToast('Error', 'Ad failed to load', 'error');
            });
        } else {
            simulateAdWatch(progress, btn, 'rewarded_popup', <?= DEFAULT_AD_REWARD * 1.5 ?>);
        }
    }, 1000);
}

function watchBannerAd() {
    if (isProcessing) return;
    if (<?= $remainingAds <= 0 ? 'true' : 'false' ?>) {
        EARNNOVA.showToast('Limit Reached', 'You have reached your daily ad limit', 'warning');
        return;
    }
    isProcessing = true;
    const btn = document.getElementById('watchBtn3');
    const progress = document.getElementById('progress3');
    btn.disabled = true;
    btn.textContent = '⏳ Loading...';
    simulateAdWatch(progress, btn, 'banner', <?= DEFAULT_AD_REWARD ?>);
}

function simulateAdWatch(progress, btn, adType, reward) {
    let duration = 0;
    const maxDuration = 100;
    const interval = setInterval(() => {
        duration += 5;
        progress.style.width = duration + '%';
        btn.textContent = `⏳ Watching... ${duration}%`;
        if (duration >= maxDuration) {
            clearInterval(interval);
            rewardUser(adType, reward);
        }
    }, 150);
}

function rewardUser(adType, rewardAmount) {
    fetch('/api/rewards.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= generateCSRFToken() ?>'
        },
        body: JSON.stringify({
            action: 'claim_reward',
            ad_type: adType,
            reward_amount: rewardAmount
        })
    })
    .then(response => response.json())
    .then(data => {
        isProcessing = false;
        document.querySelectorAll('[id^="watchBtn"]').forEach(b => {
            b.disabled = false;
            b.textContent = '▶️ Watch & Earn';
        });
        document.querySelectorAll('[id^="progress"]').forEach(p => {
            p.style.width = '0%';
        });
        
        if (data.success) {
            document.querySelectorAll('[data-balance]').forEach(el => {
                el.textContent = '$' + parseFloat(data.new_balance || 0).toFixed(2);
            });
            document.getElementById('todayEarnings').textContent = 
                '$' + parseFloat(data.today_earnings || 0).toFixed(4);
            
            // Coin explosion animation
            createCoinExplosion();
            
            EARNNOVA.showSuccess(
                '🎉 Reward Earned!',
                `You earned $${parseFloat(rewardAmount).toFixed(4)} from ${adType.replace(/_/g, ' ')}`
            );
            setTimeout(() => location.reload(), 3000);
        } else {
            EARNNOVA.showToast('Error', data.message || 'Failed to claim reward', 'error');
        }
    })
    .catch(error => {
        isProcessing = false;
        document.querySelectorAll('[id^="watchBtn"]').forEach(b => {
            b.disabled = false;
            b.textContent = '▶️ Watch & Earn';
        });
        EARNNOVA.showToast('Error', 'Network error, please try again', 'error');
    });
}

function createCoinExplosion() {
    const emojis = ['🪙', '💰', '✨', '🪙', '💎', '🌟'];
    for (let i = 0; i < 20; i++) {
        const coin = document.createElement('div');
        coin.className = 'coin-particle';
        coin.textContent = emojis[Math.floor(Math.random() * emojis.length)];
        coin.style.left = (30 + Math.random() * 40) + '%';
        coin.style.top = (30 + Math.random() * 30) + '%';
        coin.style.setProperty('--tx', (Math.random() * 200 - 100) + 'px');
        coin.style.setProperty('--ty', (Math.random() * -200 - 50) + 'px');
        coin.style.setProperty('--tx2', (Math.random() * 300 - 150) + 'px');
        coin.style.setProperty('--ty2', (Math.random() * -300 - 100) + 'px');
        coin.style.animationDelay = (Math.random() * 0.3) + 's';
        document.body.appendChild(coin);
        setTimeout(() => coin.remove(), 2000);
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
