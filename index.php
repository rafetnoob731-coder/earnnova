<?php
// EARNNOVA - Premium Homepage
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
<div class="hero-section" style="position:relative;z-index:1;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:80px 24px;">
    <div class="hero-content stagger-children" data-reveal="fadeInUp" style="max-width:800px;z-index:1;">
        <!-- 3D Logo Animation -->
        <div class="float-3d" style="font-size:5rem;margin-bottom:20px;" data-parallax="0.15">
            <span style="display:inline-block;width:100px;height:100px;line-height:100px;background:var(--gradient-primary);border-radius:24px;font-weight:900;color:white;box-shadow:0 20px 60px rgba(67,97,238,0.3);position:relative;">
                E
                <span class="pulse-ring" style="position:absolute;inset:-4px;border-radius:inherit;border:2px solid rgba(67,97,238,0.4);animation:pulseRing 2s cubic-bezier(0.215,0.61,0.355,1) infinite;"></span>
            </span>
        </div>
        
        <h1 class="hero-title" style="font-size:clamp(2.5rem,6vw,5rem);font-weight:900;line-height:1.1;margin-bottom:24px;">
            Earn Rewards with<br>
            <span class="gradient-text" style="background:linear-gradient(135deg,var(--electric-blue),var(--royal-purple),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-size:200%200%;animation:gradientShift 4s ease infinite;"><?= SITE_NAME ?></span>
        </h1>
        
        <p class="hero-subtitle" style="font-size:clamp(1rem,2vw,1.3rem);color:var(--current-text-secondary);margin-bottom:40px;line-height:1.6;max-width:600px;margin-left:auto;margin-right:auto;">
            Watch premium advertisements, complete engaging tasks, and earn real rewards instantly. 
            Join thousands of users already earning with the most advanced earning platform.
        </p>
        
        <div class="hero-buttons" style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="/register.php" class="btn btn-premium btn-primary btn-lg btn-glow-premium btn-magnetic" style="background:linear-gradient(135deg,var(--electric-blue),var(--royal-purple));color:white;padding:16px 40px;border-radius:12px;font-weight:700;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
                ✨ Get Started Free
            </a>
            <a href="/login.php" class="btn btn-premium btn-secondary btn-lg btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:16px 40px;border-radius:12px;font-weight:600;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
                🔐 Sign In
            </a>
        </div>
        
        <!-- Live Ticker -->
        <div class="metric-ticker" style="margin-top:40px;text-align:left;display:inline-flex;">
            <div class="ticker-item">
                <span class="ticker-label">👥 Live Users</span>
                <span class="ticker-value"><span data-count-premium="2847" data-count-duration="2000">0</span></span>
            </div>
            <div class="ticker-item">
                <span class="ticker-label">💰 Total Paid</span>
                <span class="ticker-value">$<span data-count-premium="48250" data-count-duration="2500" data-count-prefix="">0</span></span>
            </div>
            <div class="ticker-item">
                <span class="ticker-label">📺 Ads Today</span>
                <span class="ticker-value"><span data-count-premium="15230" data-count-duration="2000">0</span></span>
            </div>
            <div class="ticker-item">
                <span class="ticker-label">⭐ Avg Rating</span>
                <span class="ticker-value">4.9 ★</span>
            </div>
        </div>
        
        <!-- Premium Animated Stats -->
        <div class="hero-stats" style="display:flex;gap:40px;justify-content:center;margin-top:50px;flex-wrap:wrap;" data-reveal="fadeInUp" data-reveal-delay="300">
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
<!-- WHY CHOOSE EARNNOVA -->
<!-- ============================================ -->
<section style="padding:100px 24px;max-width:1200px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <h2 style="text-align:center;font-size:clamp(2rem,4vw,2.8rem);margin-bottom:20px;">
        Why Choose <span style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;"><?= SITE_NAME ?></span>?
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:60px;max-width:600px;margin-left:auto;margin-right:auto;">
        Experience the most premium earning platform with cutting-edge technology
    </p>
    <div class="card-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr));">
        <div class="card-3d glass-2" style="padding:36px 28px;text-align:center;" data-reveal="fadeInUp" data-reveal-delay="0">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));">📺</div></div>
                <h3 style="margin-bottom:12px;font-size:1.3rem;">Watch & Earn</h3>
                <p style="color:var(--current-text-secondary);line-height:1.6;">Watch premium advertisements and earn real rewards instantly deposited to your wallet.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:36px 28px;text-align:center;" data-reveal="fadeInUp" data-reveal-delay="100">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--gold),var(--warning));">👥</div></div>
                <h3 style="margin-bottom:12px;font-size:1.3rem;">Referral System</h3>
                <p style="color:var(--current-text-secondary);line-height:1.6;">Invite friends and earn lifetime commissions from their earnings with our multi-level system.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:36px 28px;text-align:center;" data-reveal="fadeInUp" data-reveal-delay="200">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));">💰</div></div>
                <h3 style="margin-bottom:12px;font-size:1.3rem;">Instant Withdrawals</h3>
                <p style="color:var(--current-text-secondary);line-height:1.6;">Withdraw your earnings via Binance Pay with lightning-fast processing and no hidden fees.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:36px 28px;text-align:center;" data-reveal="fadeInUp" data-reveal-delay="300">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--royal-purple),var(--electric-blue));">🛡️</div></div>
                <h3 style="margin-bottom:12px;font-size:1.3rem;">Enterprise Security</h3>
                <p style="color:var(--current-text-secondary);line-height:1.6;">Bank-grade encryption, fraud detection, and anti-abuse systems protect your earnings.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:36px 28px;text-align:center;" data-reveal="fadeInUp" data-reveal-delay="400">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--cyan-accent),var(--emerald-green));">📱</div></div>
                <h3 style="margin-bottom:12px;font-size:1.3rem;">Mobile Optimized</h3>
                <p style="color:var(--current-text-secondary);line-height:1.6;">Fully responsive design with PWA support works perfectly on all devices and screen sizes.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:36px 28px;text-align:center;" data-reveal="fadeInUp" data-reveal-delay="500">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="card-3d-shine"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--gold),var(--royal-purple));">⚡</div></div>
                <h3 style="margin-bottom:12px;font-size:1.3rem;">Real-Time Updates</h3>
                <p style="color:var(--current-text-secondary);line-height:1.6;">Track your earnings, balance, and rewards update instantly with our real-time system.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- EARNINGS METHODS -->
<!-- ============================================ -->
<section style="padding:80px 24px;max-width:1200px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <h2 style="text-align:center;font-size:clamp(2rem,4vw,2.8rem);margin-bottom:20px;">
        💰 <span style="background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Earn</span> in Multiple Ways
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:50px;max-width:600px;margin-left:auto;margin-right:auto;">
        Multiple earning streams designed to maximize your income potential
    </p>
    <div class="card-grid" style="grid-template-columns:repeat(auto-fill,minmax(250px,1fr));">
        <div class="earnings-method" data-reveal="fadeInUp" data-reveal-delay="0">
            <span class="method-icon">📺</span>
            <div class="method-name">Watch Videos</div>
            <div class="method-reward">$0.02+</div>
            <div class="method-desc">Per rewarded video ad watched</div>
        </div>
        <div class="earnings-method" data-reveal="fadeInUp" data-reveal-delay="100">
            <span class="method-icon">🖱️</span>
            <div class="method-name">Shortlinks</div>
            <div class="method-reward">$0.01+</div>
            <div class="method-desc">Per shortlink visit completed</div>
        </div>
        <div class="earnings-method" data-reveal="fadeInUp" data-reveal-delay="200">
            <span class="method-icon">👥</span>
            <div class="method-name">Referrals</div>
            <div class="method-reward">$0.10</div>
            <div class="method-desc">Per active referral you bring</div>
        </div>
        <div class="earnings-method" data-reveal="fadeInUp" data-reveal-delay="300">
            <span class="method-icon">🎯</span>
            <div class="method-name">Missions</div>
            <div class="method-reward">$0.25+</div>
            <div class="method-desc">Complete daily & weekly missions</div>
        </div>
        <div class="earnings-method" data-reveal="fadeInUp" data-reveal-delay="400">
            <span class="method-icon">📋</span>
            <div class="method-name">Tasks</div>
            <div class="method-reward">$0.50+</div>
            <div class="method-desc">Complete offers and surveys</div>
        </div>
        <div class="earnings-method" data-reveal="fadeInUp" data-reveal-delay="500">
            <span class="method-icon">🎁</span>
            <div class="method-name">Daily Rewards</div>
            <div class="method-reward">$0.25</div>
            <div class="method-desc">Claim daily streak bonuses</div>
        </div>
    </div>
</section>

<!-- Banner Ad -->
<div style="max-width:728px;margin:40px auto;text-align:center;position:relative;z-index:1;" data-reveal="fadeInUp">
    <div class="glass-2 border-animated" style="padding:16px;">
        <div style="font-size:0.7rem;color:var(--current-text-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:2px;font-weight:600;">— Sponsored —</div>
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
<!-- USER REVIEWS / TESTIMONIALS -->
<!-- ============================================ -->
<section style="padding:80px 24px;max-width:1200px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <h2 style="text-align:center;font-size:clamp(2rem,4vw,2.8rem);margin-bottom:20px;">
        ⭐ What Our <span style="background:linear-gradient(135deg,var(--gold),var(--warning));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Users</span> Say
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:50px;max-width:600px;margin-left:auto;margin-right:auto;">
        Join thousands of satisfied earners worldwide
    </p>
    
    <div class="carousel-snap" style="padding:10px 4px 20px;">
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.6;color:var(--current-text-secondary);">
                "Best earning platform I've ever used! The payouts are instant and the interface is incredibly smooth. Made over $50 in my first week."
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--electric-blue),var(--royal-purple));">S</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">Sarah M.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Premium User • 3 months</div>
                </div>
            </div>
        </div>
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.6;color:var(--current-text-secondary);">
                "The referral system is amazing! I've built a team of 50+ people and earn passive income every day. Highly recommended!"
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));">J</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">John D.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Top Earner • 6 months</div>
                </div>
            </div>
        </div>
        <div class="review-card">
            <div class="review-stars">★★★★★</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.6;color:var(--current-text-secondary);">
                "Professional platform with amazing UI. The 3D effects and smooth animations make earning feel like a premium experience. Love it!"
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--gold),var(--royal-purple));">A</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">Alex K.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Power User • 2 months</div>
                </div>
            </div>
        </div>
        <div class="review-card">
            <div class="review-stars">★★★★☆</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.6;color:var(--current-text-secondary);">
                "Great platform with consistent payouts. The missions and achievements add a fun gamification layer. Withdrawals are always on time."
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--cyan-accent),var(--electric-blue));">M</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">Maria R.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Active User • 1 month</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- FAQ SECTION -->
<!-- ============================================ -->
<section style="padding:80px 24px 100px;max-width:800px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <h2 style="text-align:center;font-size:clamp(2rem,4vw,2.8rem);margin-bottom:20px;">
        ❓ Frequently Asked <span style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Questions</span>
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:50px;max-width:600px;margin-left:auto;margin-right:auto;">
        Everything you need to know about earning with <?= SITE_NAME ?>
    </p>
    
    <div style="display:flex;flex-direction:column;gap:12px;">
        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>How do I start earning?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">
                Simply create a free account, activate it through PlatoBoost, and start watching ads, completing tasks, and inviting friends. Your earnings are instantly credited to your wallet.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>What is the minimum withdrawal?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">
                The minimum withdrawal amount is $<?= number_format(MIN_WITHDRAWAL, 2) ?>. Withdrawals are processed via Binance Pay within 24-48 hours after admin approval.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>How does the referral system work?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">
                You earn $<?= number_format(REFERRAL_BONUS, 2) ?> for each friend who signs up using your referral link and activates their account. There's no limit to how many people you can refer.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>Is it free to join?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">
                Yes! Registration is completely free. You only need to complete a one-time activation through PlatoBoost to unlock all earning features. This ensures platform security and prevents abuse.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>How many ads can I watch per day?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">
                You can watch up to <?= DAILY_AD_LIMIT ?> ads per day. Each ad has a <?= AD_COOLDOWN ?>-second cooldown between views. This ensures fair distribution and prevents automated abuse.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>How do I contact support?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">
                For support inquiries, please contact us through the support channels in your dashboard or email our support team. We typically respond within 24 hours.
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- PREMIUM CTA SECTION -->
<!-- ============================================ -->
<section style="padding:80px 24px 100px;text-align:center;position:relative;z-index:1;">
    <div class="glass-3 border-animated" style="max-width:700px;margin:0 auto;padding:60px 40px;" data-reveal="fadeInUp">
        <div style="font-size:4rem;margin-bottom:20px;" class="float-3d">🚀</div>
        <h2 style="font-size:clamp(1.8rem,3vw,2.5rem);margin-bottom:16px;font-weight:800;">
            Ready to Start Earning?
        </h2>
        <p style="color:var(--current-text-secondary);margin-bottom:32px;font-size:1.1rem;max-width:500px;margin-left:auto;margin-right:auto;line-height:1.6;">
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
<footer style="padding:60px 24px 100px;text-align:center;border-top:1px solid var(--glass-border-2);position:relative;z-index:1;margin-top:40px;">
    <div style="max-width:600px;margin:0 auto;">
        <div class="float-3d" style="display:inline-block;width:56px;height:56px;line-height:56px;background:var(--gradient-primary);border-radius:16px;font-weight:900;font-size:1.5rem;color:white;margin-bottom:16px;box-shadow:0 10px 30px rgba(67,97,238,0.3);">E</div>
        <h3 style="font-size:1.3rem;font-weight:700;margin-bottom:8px;"><?= SITE_NAME ?></h3>
        <p style="color:var(--current-text-muted);font-size:0.9rem;margin-bottom:20px;">
            Premium Earning Platform. Watch. Earn. Withdraw.
        </p>
        <div style="display:flex;gap:24px;justify-content:center;margin-bottom:20px;flex-wrap:wrap;">
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.9rem;transition:color 0.3s ease;">Terms of Service</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.9rem;transition:color 0.3s ease;">Privacy Policy</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.9rem;transition:color 0.3s ease;">Contact</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.9rem;transition:color 0.3s ease;">FAQ</a>
        </div>
        <div style="display:flex;gap:16px;justify-content:center;margin-bottom:20px;font-size:1.2rem;">
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s ease;">📘</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s ease;">🐦</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s ease;">💬</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s ease;">📱</a>
        </div>
        <div style="height:1px;background:linear-gradient(90deg,transparent,var(--glass-border-3),transparent);margin-bottom:20px;"></div>
        <p style="color:var(--current-text-muted);font-size:0.85rem;">
            &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
        </p>
    </div>
</footer>

<?php include __DIR__ . '/includes/footer.php'; ?>
