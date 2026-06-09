<?php
// EARNNOVA - Plans Page
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();
$user = getCurrentUser();

$pageTitle = 'Plans';
$showSidebar = true;

$plans = db()->getPlans();

// Default plans if none exist
if (empty($plans)) {
    $plans = [
        ['name' => 'Starter', 'price' => 0.50, 'daily_earnings' => 0.50, 'features' => ['Watch Ads', 'Basic Tasks', 'Referral Rewards']],
        ['name' => 'Pro', 'price' => 2.00, 'daily_earnings' => 2.50, 'features' => ['Watch Ads', 'All Tasks', 'Referral Rewards', 'Priority Support', 'Higher Limits']],
        ['name' => 'Premium', 'price' => 5.00, 'daily_earnings' => 6.00, 'features' => ['Everything Unlimited', 'VIP Support', 'Max Earnings', 'Early Access', 'Exclusive Tasks']]
    ];
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">Plans & Pricing</h1>
        <p class="page-subtitle">Choose a plan and unlock premium features</p>
    </div>
</div>

<div class="card-grid stagger-children" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    <?php foreach ($plans as $plan): 
        $planName = $plan['name'] ?? 'Basic';
        $planPrice = floatval($plan['price'] ?? 0);
        $planDaily = floatval($plan['daily_earnings'] ?? 0);
        $features = $plan['features'] ?? [];
    ?>
    <div class="glass-card" style="padding: 32px; text-align: center; position: relative;" data-scroll-animate="slide-in-up">
        <?php if ($planName === 'Premium'): ?>
        <div style="position: absolute; top: -12px; right: 20px; background: var(--gradient-primary); padding: 4px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
            POPULAR
        </div>
        <?php endif; ?>
        
        <div style="font-size: 2.5rem; margin-bottom: 16px;">
            <?= $planName === 'Starter' ? '🌱' : ($planName === 'Pro' ? '⭐' : '👑') ?>
        </div>
        <h3 style="font-size: 1.5rem; margin-bottom: 8px;"><?= htmlspecialchars($planName) ?></h3>
        <div style="font-size: 2.5rem; font-weight: 800; margin-bottom: 8px;">
            $<?= number_format($planPrice, 2) ?>
        </div>
        <div style="color: var(--current-text-secondary); margin-bottom: 20px;">
            Daily earnings: $<?= number_format($planDaily, 2) ?>
        </div>
        
        <ul style="list-style: none; padding: 0; margin-bottom: 24px; text-align: left;">
            <?php foreach ($features as $feature): ?>
            <li style="padding: 8px 0; display: flex; align-items: center; gap: 8px;">
                <span style="color: var(--emerald-green);">✓</span>
                <?= htmlspecialchars(is_string($feature) ? $feature : 'Feature') ?>
            </li>
            <?php endforeach; ?>
        </ul>
        
        <button class="btn btn-primary btn-block btn-lg" onclick="subscribePlan('<?= htmlspecialchars($planName) ?>', <?= $planPrice ?>)">
            Get Started
        </button>
    </div>
    <?php endforeach; ?>
</div>

<!-- Plan Comparison -->
<div class="glass-card" style="padding: 24px; margin-top: 30px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom: 16px; text-align: center;">Plan Comparison</h3>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>Starter</th>
                    <th>Pro</th>
                    <th>Premium</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Daily Ad Limit</td>
                    <td>20</td>
                    <td>50</td>
                    <td>Unlimited</td>
                </tr>
                <tr>
                    <td>Tasks Access</td>
                    <td>Basic</td>
                    <td>All</td>
                    <td>All + Exclusive</td>
                </tr>
                <tr>
                    <td>Referral Bonus</td>
                    <td>$0.10</td>
                    <td>$0.25</td>
                    <td>$0.50</td>
                </tr>
                <tr>
                    <td>Support</td>
                    <td>Standard</td>
                    <td>Priority</td>
                    <td>VIP 24/7</td>
                </tr>
                <tr>
                    <td>Withdrawal Min.</td>
                    <td>$1.00</td>
                    <td>$0.50</td>
                    <td>$0.10</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function subscribePlan(name, price) {
    EARNNOVA.showToast('Coming Soon', 'Plan subscriptions will be available soon via Binance Pay', 'info');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
