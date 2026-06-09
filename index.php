<?php
// EARNNOVA - Homepage
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Premium Earning Platform';
$showSidebar = false;

// Redirect to dashboard if logged in
if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================ -->
<!-- PREMIUM HERO SECTION -->
<!-- ============================================ -->
<div class="hero-section" style="position:relative;z-index:1;">
    <div class="hero-content stagger-children" data-reveal="fadeInUp">
        <!-- 3D Logo Animation -->
        <div class="float-3d" style="font-size:5rem;margin-bottom:20px;" data-parallax="0.15">
            <span style="display:inline-block;width:100px;height:100px;line-height:100px;background:var(--gradient-primary);border-radius:24px;font-weight:900;color:white;box-shadow:0 20px 60px rgba(67,97,238,0.3);">E</span>
        </div>
        
        <h1 class="hero-title" style="font-size:clamp(2.5rem,6vw,5rem);font-weight:900;line-height:1.1;margin-bottom:24px;">
            Earn Rewards with<br>
            <span class="gradient-text" style="background:linear-gradient(135deg,var(--electric-blue),var(--royal-purple),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-size:200%200%;animation:gradientShift 4s ease infinite;"><?= SITE_NAME ?></span>
        </h1>
        
        <p class="hero-subtitle" style="font-size:clamp(1rem,2vw,1.3rem);color:var(--current-text-secondary);margin-bottom:40px;line-height:1.6;max-width:600px;margin-left:auto;margin-right:auto;">
            Watch advertisements, complete tasks, and earn real rewards. 
            Join thousands of users already earning with the most premium earning platform.
        </p>
        
        <div class="hero-buttons" style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="/register.php" class="btn btn-premium btn-primary btn-lg btn-glow-premium btn-magnetic" style="background:linear-gradient(135deg,var(--electric-blue),var(--royal-purple));color:white;padding:16px 40px;border-radius:12px;font-weight:700;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
                ✨ Get Started Free
            </a>
            <a href="/login.php" class="btn btn-premium btn-secondary btn-lg btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:16px 40px;border-radius:12px;font-weight:600;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
                🔐 Sign In
            </a>
        </div>
        
        <!-- Premium Animated Stats -->
        <div class="hero-stats" style="display:flex;gap:40px;justify-content:center;margin-top:60px;flex-wrap:wrap;" data-reveal="fadeInUp" data-reveal-delay="300">
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number">
                    <span data-count-premium="12500" data-count-duration="3000" data-count-prefix="">0</span>
                </div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Active Users</div>
            </div>
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number">
                    <span data-count-premium="50000" data-count-duration="3000" data-count-prefix="">0</span>
                </div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Ads Watched</div>
            </div>
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number" style="background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    <span data-count-premium="250000" data-count-duration="3000" data-count-prefix="$">0</span>
                </div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Total Paid</div>
            </div>
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number" style="background:linear-gradient(135deg,var(--gold),var(--warning));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    <span data-count-premium="99.9" data-count-duration="2000" data-count-prefix="" data-count-decimals="1">0</span>
                    <span style="font-size:1.5rem;-webkit-text-fill-color:initial;">%</span>
                </div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Uptime</div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- PREMIUM FEATURES SECTION -->
<!-- ============================================ -->
<section style="padding: 100px 24px; max-width: 1200px; margin: 0 auto; position: relative; z-index: 1;">
    <h2 style="text-align: center; font-size: clamp(2rem,4vw,2.8rem); margin-bottom: 20px;" data-reveal="fadeInUp">
        Why Choose <span style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;"><?= SITE_NAME ?></span>?
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:60px;max-width:600px;margin-left:auto;margin-right:auto;" data-reveal="fadeInUp" data-reveal-delay="100">
        Experience the most premium earning platform with cutting-edge technology
    </p>
    <div class="card-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
        <div class="card-3d glass-2" style="padding: 36px 28px; text-align: center;" data-reveal="fadeInUp" data-reveal-delay="0">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div style="font-size: 3rem; margin-bottom: 16px;">📺</div>
                <h3 style="margin-bottom: 12px;font-size:1.3rem;">Watch & Earn</h3>
                <p style="color: var(--current-text-secondary);line-height:1.6;">Watch premium advertisements and earn real rewards instantly.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding: 36px 28px; text-align: center;" data-reveal="fadeInUp" data-reveal-delay="100">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div style="font-size: 3rem; margin-bottom: 16px;">👥</div>
                <h3 style="margin-bottom: 12px;font-size:1.3rem;">Referral System</h3>
                <p style="color: var(--current-text-secondary);line-height:1.6;">Invite friends and earn commissions from their earnings.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding: 36px 28px; text-align: center;" data-reveal="fadeInUp" data-reveal-delay="200">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div style="font-size: 3rem; margin-bottom: 16px;">💰</div>
                <h3 style="margin-bottom: 12px;font-size:1.3rem;">Instant Withdrawals</h3>
                <p style="color: var(--current-text-secondary);line-height:1.6;">Withdraw your earnings via Binance Pay with no delays.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding: 36px 28px; text-align: center;" data-reveal="fadeInUp" data-reveal-delay="300">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div style="font-size: 3rem; margin-bottom: 16px;">🛡️</div>
                <h3 style="margin-bottom: 12px;font-size:1.3rem;">Secure Platform</h3>
                <p style="color: var(--current-text-secondary);line-height:1.6;">Enterprise-grade security with fraud detection and anti-abuse.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding: 36px 28px; text-align: center;" data-reveal="fadeInUp" data-reveal-delay="400">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div style="font-size: 3rem; margin-bottom: 16px;">📱</div>
                <h3 style="margin-bottom: 12px;font-size:1.3rem;">Mobile Optimized</h3>
                <p style="color: var(--current-text-secondary);line-height:1.6;">Fully responsive design works perfectly on all devices.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding: 36px 28px; text-align: center;" data-reveal="fadeInUp" data-reveal-delay="500">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div style="font-size: 3rem; margin-bottom: 16px;">⚡</div>
                <h3 style="margin-bottom: 12px;font-size:1.3rem;">Real-Time Updates</h3>
                <p style="color: var(--current-text-secondary);line-height:1.6;">Track your earnings and rewards in real-time.</p>
            </div>
        </div>
    </div>
</section>

<!-- Top Banner Ad (Premium) -->
<div style="max-width: 728px; margin: 40px auto; text-align: center; position:relative;z-index:1;" data-reveal="fadeInUp">
    <div class="glass-2 border-animated" style="padding: 16px;">
        <div style="font-size: 0.7rem; color: var(--current-text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 2px;font-weight:600;">— Sponsored —</div>
        <div id="banner-ad-top">
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

<!-- ============================================ -->
<!-- PREMIUM CTA SECTION -->
<!-- ============================================ -->
<section style="padding: 100px 24px; text-align: center; position:relative;z-index:1;margin-top:40px;">
    <div class="glass-3" style="max-width: 700px; margin: 0 auto; padding: 60px 40px;" data-reveal="fadeInUp">
        <div style="font-size: 4rem; margin-bottom: 20px;" class="float-3d">🚀</div>
        <h2 style="font-size: clamp(1.8rem,3vw,2.5rem); margin-bottom: 16px; font-weight:800;">
            Ready to Start Earning?
        </h2>
        <p style="color: var(--current-text-secondary); margin-bottom: 32px; font-size: 1.1rem; max-width: 500px; margin-left: auto; margin-right: auto; line-height:1.6;">
            Join <?= SITE_NAME ?> today and start earning real rewards. 
            It's free to register and start watching ads immediately.
        </p>
        <a href="/register.php" class="btn btn-premium btn-primary btn-lg btn-glow-premium btn-magnetic" style="background:linear-gradient(135deg,var(--electric-blue),var(--royal-purple));color:white;padding:18px 48px;border-radius:12px;font-weight:700;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
            ✨ Create Free Account
        </a>
        <div style="display:flex;gap:20px;justify-content:center;margin-top:24px;color:var(--current-text-muted);font-size:0.85rem;">
            <span>🔒 No credit card required</span>
            <span>⚡ Instant access</span>
            <span>🎯 Start earning in 2 minutes</span>
        </div>
    </div>
</section>

<!-- Premium Footer -->
<footer style="padding: 60px 24px 40px; text-align: center; border-top: 1px solid var(--glass-border-2); position:relative;z-index:1;margin-top:60px;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="float-3d" style="display:inline-block;width:56px;height:56px;line-height:56px;background:var(--gradient-primary);border-radius:16px;font-weight:900;font-size:1.5rem;color:white;margin-bottom:16px;box-shadow:0 10px 30px rgba(67,97,238,0.3);">E</div>
        <h3 style="font-size:1.3rem;font-weight:700;margin-bottom:8px;"><?= SITE_NAME ?></h3>
        <p style="color: var(--current-text-muted); font-size: 0.9rem; margin-bottom: 20px;">
            Premium Earning Platform. Watch. Earn. Withdraw.
        </p>
        <div style="display:flex;gap:24px;justify-content:center;margin-bottom:20px;flex-wrap:wrap;">
            <a href="#" style="color: var(--current-text-muted); text-decoration: none; font-size:0.9rem;transition:color 0.3s ease;">Terms of Service</a>
            <a href="#" style="color: var(--current-text-muted); text-decoration: none; font-size:0.9rem;transition:color 0.3s ease;">Privacy Policy</a>
            <a href="#" style="color: var(--current-text-muted); text-decoration: none; font-size:0.9rem;transition:color 0.3s ease;">Contact</a>
            <a href="#" style="color: var(--current-text-muted); text-decoration: none; font-size:0.9rem;transition:color 0.3s ease;">FAQ</a>
        </div>
        <div style="height:1px;background:linear-gradient(90deg,transparent,var(--glass-border-3),transparent);margin-bottom:20px;"></div>
        <p style="color: var(--current-text-muted); font-size: 0.85rem;">
            &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
        </p>
    </div>
</footer>

<?php include __DIR__ . '/includes/footer.php'; ?>
