<?php
// EARNNOVA - Premium Homepage v4.0
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Premium Earning Platform';
$showSidebar = false;

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================ -->
<!-- PREMIUM HERO SECTION v4.0 -->
<!-- ============================================ -->
<div class="hero-section" style="position:relative;z-index:1;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:80px 24px;overflow:hidden;">
    <!-- Floating 3D shapes -->
    <div class="floating-shape" style="width:500px;height:500px;background:var(--cyber-cyan);top:-10%;right:-10%;opacity:0.06;animation-delay:0s;"></div>
    <div class="floating-shape" style="width:400px;height:400px;background:var(--neon-coral);bottom:-15%;left:-8%;opacity:0.05;animation-delay:-4s;"></div>
    <div class="floating-shape" style="width:300px;height:300px;background:var(--royal-violet);top:40%;left:60%;opacity:0.04;animation-delay:-8s;"></div>
    
    <div class="hero-content stagger-children" data-reveal="fadeInUp" style="max-width:860px;z-index:1;">
        <!-- 3D Logo with parallax -->
        <div class="float-3d" style="font-size:5rem;margin-bottom:24px;" data-parallax="0.12">
            <span style="display:inline-block;width:110px;height:110px;line-height:110px;background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));border-radius:28px;font-weight:900;color:white;box-shadow:0 20px 60px rgba(0,240,255,0.25);position:relative;font-size:3rem;">
                E
                <span class="pulse-ring" style="position:absolute;inset:-5px;border-radius:inherit;border:2px solid rgba(0,240,255,0.3);animation:pulseRing 2.5s cubic-bezier(0.215,0.61,0.355,1) infinite;"></span>
            </span>
        </div>
        
        <h1 class="hero-title" style="font-size:clamp(2.8rem,7vw,5.5rem);font-weight:900;line-height:1.05;margin-bottom:16px;letter-spacing:-0.03em;">
            Earn Smarter.<br>
            <span style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet),var(--neon-coral));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-size:200%200%;animation:gradientShift 4s ease infinite;">Grow Faster.</span>
        </h1>
        
        <p class="hero-subtitle" style="font-size:clamp(1.1rem,2vw,1.35rem);color:var(--current-text-secondary);margin-bottom:36px;line-height:1.6;max-width:640px;margin-left:auto;margin-right:auto;">
            Join <strong style="color:var(--cyber-cyan);">1M+</strong> users already earning with EARNNOVA. Watch ads, complete missions, refer friends — and get paid instantly.
        </p>
        
        <div class="hero-buttons" style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="/register.php" class="btn btn-premium btn-primary btn-lg btn-glow-premium btn-magnetic" style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));color:white;padding:18px 44px;border-radius:40px;font-weight:700;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
                ✨ Get Started Free
            </a>
            <a href="/login.php" class="btn btn-premium btn-secondary btn-lg btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:18px 44px;border-radius:40px;font-weight:600;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
                ▶ Watch Demo
            </a>
        </div>
        
        <!-- Trust Badges -->
        <div style="display:flex;gap:32px;justify-content:center;margin-top:40px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:8px;font-size:0.9rem;color:var(--current-text-muted);">
                <span style="font-size:1.1rem;">★</span> Trustpilot 4.8
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:0.9rem;color:var(--current-text-muted);">
                <span style="font-size:1.1rem;">📱</span> 1M+ Downloads
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:0.9rem;color:var(--current-text-muted);">
                <span style="font-size:1.1rem;">🛡️</span> 24/7 Support
            </div>
        </div>
        
        <!-- Live Ticker -->
        <div class="metric-ticker" style="margin-top:32px;text-align:left;display:inline-flex;border-radius:40px;padding:16px 28px;">
            <div class="ticker-item">
                <span class="ticker-label">💰 Total Paid</span>
                <span class="ticker-value">$<span data-count-premium="2500000" data-count-duration="3000">0</span></span>
            </div>
            <div class="ticker-item">
                <span class="ticker-label">👥 Active Users</span>
                <span class="ticker-value"><span data-count-premium="12847" data-count-duration="2500">0</span></span>
            </div>
            <div class="ticker-item">
                <span class="ticker-label">📺 Ads Today</span>
                <span class="ticker-value"><span data-count-premium="45230" data-count-duration="2000">0</span></span>
            </div>
            <div class="ticker-item">
                <span class="ticker-label">⭐ Rating</span>
                <span class="ticker-value">4.9 ★</span>
            </div>
        </div>
        
        <!-- Premium Animated Stats -->
        <div class="hero-stats" style="display:flex;gap:48px;justify-content:center;margin-top:40px;flex-wrap:wrap;" data-reveal="fadeInUp" data-reveal-delay="400">
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number" style="font-size:2.8rem;">$<span data-count-premium="2.5" data-count-duration="2500" data-count-decimals="1">0</span>M+</div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Paid Out</div>
            </div>
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number" style="font-size:2.8rem;">
                    <span data-count-premium="12500" data-count-duration="3000">0</span>+
                </div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Active Users</div>
            </div>
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number" style="font-size:2.8rem;background:linear-gradient(135deg,var(--mint-emerald),var(--cyber-cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    <span data-count-premium="50000" data-count-duration="3000">0</span>+
                </div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Ads Watched</div>
            </div>
            <div class="hero-stat-item stat-premium" style="text-align:center;">
                <div class="stat-number" style="font-size:2.8rem;background:linear-gradient(135deg,var(--gold),var(--warning));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">
                    <span data-count-premium="99.9" data-count-duration="2000" data-count-decimals="1">0</span>%
                </div>
                <div class="label" style="color:var(--current-text-muted);margin-top:4px;font-size:0.9rem;">Uptime</div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- FEATURE HIGHLIGHTS — 3 Floating Glass Cards -->
<!-- ============================================ -->
<section style="padding:60px 24px 80px;max-width:1100px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;" class="responsive-grid-3">
        <div class="card-3d glass-2" style="padding:40px 28px;text-align:center;border-radius:24px;" data-reveal="fadeInUp" data-reveal-delay="0">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));">📺</div></div>
                <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:8px;">Watch Ads, Earn Crypto</h3>
                <p style="color:var(--current-text-secondary);font-size:0.95rem;line-height:1.6;">Watch premium video ads and earn real rewards instantly. Daily limits with bonus multipliers.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:40px 28px;text-align:center;border-radius:24px;" data-reveal="fadeInUp" data-reveal-delay="100">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--neon-coral),var(--gold));">👥</div></div>
                <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:8px;">Refer Friends, Get Rewards</h3>
                <p style="color:var(--current-text-secondary);font-size:0.95rem;line-height:1.6;">Earn lifetime commissions from your referrals. Build your team and grow your income.</p>
            </div>
        </div>
        <div class="card-3d glass-2" style="padding:40px 28px;text-align:center;border-radius:24px;" data-reveal="fadeInUp" data-reveal-delay="200">
            <div class="card-3d-inner">
                <div class="card-3d-glow"></div>
                <div class="feature-icon-3d"><div class="icon-inner" style="background:linear-gradient(135deg,var(--mint-emerald),var(--cyber-cyan));">⭐</div></div>
                <h3 style="font-size:1.25rem;font-weight:700;margin-bottom:8px;">Complete Missions, Level Up</h3>
                <p style="color:var(--current-text-secondary);font-size:0.95rem;line-height:1.6;">Daily missions, achievements, and XP system. Unlock ranks from Bronze to Legend.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- HOW IT WORKS — 4-Step Vertical Timeline -->
<!-- ============================================ -->
<section style="padding:80px 24px;max-width:900px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <h2 style="text-align:center;font-size:clamp(2rem,4vw,2.8rem);margin-bottom:12px;font-weight:800;letter-spacing:-0.02em;">
        How It <span style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Works</span>
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:50px;">Start earning in 4 simple steps</p>
    
    <div style="position:relative;padding-left:60px;">
        <div style="position:absolute;left:24px;top:12px;bottom:12px;width:2px;background:linear-gradient(180deg,var(--cyber-cyan),var(--royal-violet),var(--neon-coral),var(--mint-emerald));border-radius:1px;"></div>
        
        <?php $steps = [
            ['icon'=>'📝','title'=>'Create Free Account','desc'=>'Sign up in seconds — no credit card needed. Start your earning journey instantly.'],
            ['icon'=>'🔐','title'=>'Activate Your Account','desc'=>'Complete a quick PlatoBoost verification to unlock all earning features.'],
            ['icon'=>'📺','title'=>'Watch & Complete Tasks','desc'=>'Watch ads, complete missions, refer friends — earn rewards from every action.'],
            ['icon'=>'💰','title'=>'Withdraw Your Earnings','desc'=>'Request withdrawals via Binance Pay. Funds arrive within 24-48 hours.']
        ]; ?>
        <?php foreach ($steps as $i => $step): ?>
        <div style="position:relative;padding-bottom:48px;<?= $i === 3 ? 'padding-bottom:0;' : '' ?>" data-reveal="fadeInUp" data-reveal-delay="<?= $i * 100 ?>">
            <div style="position:absolute;left:-36px;top:0;width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,<?= ['var(--cyber-cyan)','var(--royal-violet)','var(--neon-coral)','var(--mint-emerald)'][$i] ?>,rgba(255,255,255,0.1));display:flex;align-items:center;justify-content:center;font-size:1.3rem;box-shadow:0 0 20px <?= ['rgba(0,240,255,0.3)','rgba(124,58,237,0.3)','rgba(255,0,102,0.3)','rgba(0,229,178,0.3)'][$i] ?>;">
                <span style="color:white;font-weight:700;"><?= $i + 1 ?></span>
            </div>
            <div style="background:var(--glass-bg-2);border:1px solid var(--glass-border-2);border-radius:20px;padding:24px 28px;">
                <div style="font-size:1.8rem;margin-bottom:8px;"><?= $step['icon'] ?></div>
                <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:4px;"><?= $step['title'] ?></h3>
                <p style="color:var(--current-text-secondary);font-size:0.95rem;line-height:1.6;margin:0;"><?= $step['desc'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ============================================ -->
<!-- TESTIMONIALS -->
<!-- ============================================ -->
<section style="padding:80px 24px;max-width:1100px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <h2 style="text-align:center;font-size:clamp(2rem,4vw,2.8rem);margin-bottom:12px;font-weight:800;letter-spacing:-0.02em;">
        ⭐ What Our <span style="background:linear-gradient(135deg,var(--gold),var(--warning));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Users</span> Say
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:40px;">Join thousands of satisfied earners worldwide</p>
    
    <div class="carousel-snap" style="padding:10px 4px 20px;">
        <div class="review-card" style="border-radius:24px;padding:32px;">
            <div class="review-stars">★★★★★</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.7;color:var(--current-text-secondary);">
                "Best earning platform I've ever used! The payouts are instant and the interface is incredibly smooth. Made over $50 in my first week."
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:white;">S</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">Sarah M.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Premium User · 3 months</div>
                </div>
                <div style="margin-left:auto;font-weight:700;color:var(--mint-emerald);">+$2,450</div>
            </div>
        </div>
        <div class="review-card" style="border-radius:24px;padding:32px;">
            <div class="review-stars">★★★★★</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.7;color:var(--current-text-secondary);">
                "The referral system is amazing! I've built a team of 50+ people and earn passive income every day. Highly recommended!"
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--mint-emerald),var(--cyber-cyan));width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:white;">J</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">John D.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Top Earner · 6 months</div>
                </div>
                <div style="margin-left:auto;font-weight:700;color:var(--mint-emerald);">+$8,200</div>
            </div>
        </div>
        <div class="review-card" style="border-radius:24px;padding:32px;">
            <div class="review-stars">★★★★★</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.7;color:var(--current-text-secondary);">
                "Professional platform with amazing UI. The 3D effects and smooth animations make earning feel like a premium experience. Love it!"
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--neon-coral),var(--gold));width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:white;">A</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">Alex K.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Power User · 2 months</div>
                </div>
                <div style="margin-left:auto;font-weight:700;color:var(--mint-emerald);">+$3,680</div>
            </div>
        </div>
        <div class="review-card" style="border-radius:24px;padding:32px;">
            <div class="review-stars">★★★★★</div>
            <p style="margin:16px 0;font-size:0.95rem;line-height:1.7;color:var(--current-text-secondary);">
                "Great platform with consistent payouts. The missions and achievements add a fun gamification layer. Withdrawals are always on time."
            </p>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="review-avatar" style="background:linear-gradient(135deg,var(--cyber-cyan),var(--mint-emerald));width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:white;">M</div>
                <div>
                    <div style="font-weight:600;font-size:0.9rem;">Maria R.</div>
                    <div style="font-size:0.8rem;color:var(--current-text-muted);">Active User · 1 month</div>
                </div>
                <div style="margin-left:auto;font-weight:700;color:var(--mint-emerald);">+$890</div>
            </div>
        </div>
    </div>
</section>

<!-- Banner Ad -->
<div style="max-width:728px;margin:20px auto;text-align:center;position:relative;z-index:1;" data-reveal="fadeInUp">
    <div class="glass-2 border-animated" style="padding:16px;border-radius:24px;">
        <div style="font-size:0.7rem;color:var(--current-text-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:2px;font-weight:600;">— Sponsored —</div>
        <div id="banner-ad-top">
            <script>
            atOptions = { 'key' : 'e877abd9733752f8dbd622496db4c8a3', 'format' : 'iframe', 'height' : 90, 'width' : 728, 'params' : {} };
            </script>
            <script src="https://intermediatenormalconfederate.com/e877abd9733752f8dbd622496db4c8a3/invoke.js" async></script>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- FAQ SECTION v4.0 -->
<!-- ============================================ -->
<section style="padding:80px 24px 100px;max-width:800px;margin:0 auto;position:relative;z-index:1;" data-reveal="fadeInUp">
    <h2 style="text-align:center;font-size:clamp(2rem,4vw,2.8rem);margin-bottom:12px;font-weight:800;letter-spacing:-0.02em;">
        ❓ Frequently Asked <span style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Questions</span>
    </h2>
    <p style="text-align:center;color:var(--current-text-secondary);font-size:1.1rem;margin-bottom:40px;">Everything you need to know</p>
    
    <div style="display:flex;flex-direction:column;gap:10px;">
        <div class="faq-item" style="border-radius:16px;">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>How do I start earning?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">Simply create a free account, activate it through PlatoBoost, and start watching ads, completing tasks, and inviting friends. Your earnings are instantly credited to your wallet.</div>
        </div>
        <div class="faq-item" style="border-radius:16px;">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>What is the minimum withdrawal?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">The minimum withdrawal amount is $<?= number_format(MIN_WITHDRAWAL, 2) ?>. Withdrawals are processed via Binance Pay within 24-48 hours after admin approval.</div>
        </div>
        <div class="faq-item" style="border-radius:16px;">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>How does the referral system work?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">You earn $<?= number_format(REFERRAL_BONUS, 2) ?> for each friend who signs up using your referral link and activates their account. There's no limit to how many people you can refer.</div>
        </div>
        <div class="faq-item" style="border-radius:16px;">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>Is it free to join?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">Yes! Registration is completely free. You only need to complete a one-time activation through PlatoBoost to unlock all earning features.</div>
        </div>
        <div class="faq-item" style="border-radius:16px;">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                <span>How many ads can I watch per day?</span>
                <span class="faq-arrow">▼</span>
            </div>
            <div class="faq-answer">You can watch up to <?= DAILY_AD_LIMIT ?> ads per day. Each ad has a <?= AD_COOLDOWN ?>-second cooldown between views.</div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- PREMIUM CTA SECTION -->
<!-- ============================================ -->
<section style="padding:60px 24px 100px;text-align:center;position:relative;z-index:1;">
    <div class="glass-3 border-animated" style="max-width:720px;margin:0 auto;padding:64px 48px;border-radius:32px;" data-reveal="fadeInUp">
        <div style="font-size:4.5rem;margin-bottom:20px;" class="float-3d">🚀</div>
        <h2 style="font-size:clamp(2rem,3.5vw,2.8rem);margin-bottom:16px;font-weight:800;letter-spacing:-0.02em;">
            Ready to Start Earning?
        </h2>
        <p style="color:var(--current-text-secondary);margin-bottom:32px;font-size:1.1rem;max-width:520px;margin-left:auto;margin-right:auto;line-height:1.6;">
            Join <?= SITE_NAME ?> today and start earning real rewards. 
            It's free to register and start watching ads immediately.
        </p>
        <a href="/register.php" class="btn btn-premium btn-primary btn-lg btn-glow-premium btn-magnetic" style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));color:white;padding:18px 48px;border-radius:40px;font-weight:700;font-size:1.1rem;text-decoration:none;display:inline-flex;align-items:center;gap:10px;">
            ✨ Create Free Account
        </a>
        <div style="display:flex;gap:20px;justify-content:center;margin-top:24px;color:var(--current-text-muted);font-size:0.85rem;flex-wrap:wrap;">
            <span>🔒 No credit card required</span>
            <span>⚡ Instant access</span>
            <span>🎯 Start earning in 2 minutes</span>
        </div>
    </div>
</section>

<!-- Premium Footer v4.0 -->
<footer style="padding:60px 24px 100px;text-align:center;border-top:1px solid var(--glass-border-2);position:relative;z-index:1;margin-top:40px;">
    <div style="max-width:600px;margin:0 auto;">
        <div class="float-3d" style="display:inline-block;width:60px;height:60px;line-height:60px;background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));border-radius:18px;font-weight:900;font-size:1.5rem;color:white;margin-bottom:16px;box-shadow:0 10px 30px rgba(0,240,255,0.25);">E</div>
        <h3 style="font-size:1.4rem;font-weight:700;margin-bottom:4px;letter-spacing:-0.02em;"><?= SITE_NAME ?></h3>
        <p style="color:var(--current-text-muted);font-size:0.9rem;margin-bottom:24px;">
            Premium Earning Platform. Watch. Earn. Withdraw.
        </p>
        <div style="display:flex;gap:16px;justify-content:center;margin-bottom:20px;font-size:1.3rem;">
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s;border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:var(--glass-bg-2);border:1px solid var(--glass-border-2);">📘</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s;border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:var(--glass-bg-2);border:1px solid var(--glass-border-2);">🐦</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s;border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:var(--glass-bg-2);border:1px solid var(--glass-border-2);">💬</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;transition:color 0.3s;border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:var(--glass-bg-2);border:1px solid var(--glass-border-2);">📱</a>
        </div>
        <div style="display:flex;gap:24px;justify-content:center;margin-bottom:20px;flex-wrap:wrap;">
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.85rem;">Terms of Service</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.85rem;">Privacy Policy</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.85rem;">Contact</a>
            <a href="#" style="color:var(--current-text-muted);text-decoration:none;font-size:0.85rem;">FAQ</a>
        </div>
        <div style="height:1px;background:linear-gradient(90deg,transparent,var(--glass-border-3),transparent);margin-bottom:20px;"></div>
        <p style="color:var(--current-text-muted);font-size:0.85rem;">
            &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
        </p>
    </div>
</footer>

<style>
.responsive-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
@media (max-width: 768px) { .responsive-grid-3 { grid-template-columns:1fr; } }
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
