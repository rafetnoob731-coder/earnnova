<?php
// EARNNOVA - Premium Referral System
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

requireActive();
$user = getCurrentUser();

$pageTitle = 'Referrals';
$showSidebar = true;

$referrals = db()->getReferrals($user['uid']);
$referralEarnings = floatval($user['referral_balance'] ?? 0);
$activeReferrals = count(array_filter($referrals, fn($r) => ($r['status'] ?? 'active') === 'active'));

$referralLink = rtrim(SITE_URL, '/') . '/register.php?ref=' . urlencode($user['referral_code'] ?? '');
$facebookShare = 'https://facebook.com/sharer/sharer.php?quote=' . urlencode("Join EARNNOVA and start earning online!") . '&u=' . urlencode($referralLink);
$telegramShare = 'https://t.me/share/url?url=' . urlencode($referralLink) . '&text=' . urlencode('💰 Earn Online with EARNNOVA!');
$whatsappShare = 'https://wa.me/?text=' . urlencode("Hello! I am earning through EARNNOVA. Join using my referral link: $referralLink");

include __DIR__ . '/includes/header.php';
?>

<div class="page-header" data-scroll-animate="fade-in">
    <div>
        <h1 class="page-title">👥 Referral Program</h1>
        <p class="page-subtitle">Invite friends and earn <strong>$<?= number_format(REFERRAL_BONUS, 2) ?></strong> per active referral</p>
    </div>
</div>

<div class="dashboard-grid" data-scroll-animate="fade-in">
    <div class="card-3d glass-2 stat-card">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--electric-blue),var(--cyan-accent));">👥</div>
            <div class="stat-label">Total Referrals</div>
            <div class="stat-value"><?= count($referrals) ?></div>
            <div class="stat-change positive">↑ Keep inviting!</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--emerald-green),var(--cyan-accent));">💰</div>
            <div class="stat-label">Referral Earnings</div>
            <div class="stat-value" data-referral-balance>$<?= number_format($referralEarnings, 2) ?></div>
            <div class="stat-change positive">↑ Lifetime earnings</div>
        </div>
    </div>
    <div class="card-3d glass-2 stat-card">
        <div class="card-3d-inner">
            <div class="card-3d-glow"></div>
            <div class="stat-icon" style="background:linear-gradient(135deg,var(--gold),var(--warning));">🔗</div>
            <div class="stat-label">Your Referral Code</div>
            <div class="stat-value" style="font-size:1.4rem;letter-spacing:2px;"><?= htmlspecialchars($user['referral_code'] ?? 'N/A') ?></div>
            <div class="stat-change positive">🎯 Share & earn</div>
        </div>
    </div>
</div>

<!-- Referral Link + Share -->
<div class="glass-2 border-animated" style="padding:32px;margin-bottom:24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom:16px;">🔗 Share Your Referral Link</h3>
    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
        <input type="text" class="form-input" value="<?= htmlspecialchars($referralLink) ?>" readonly id="referralLink" style="flex:1;min-width:200px;">
        <button class="btn btn-premium btn-primary btn-magnetic" onclick="EARNNOVA.copyToClipboard('<?= htmlspecialchars($referralLink) ?>')">📋 Copy Link</button>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="<?= htmlspecialchars($facebookShare) ?>" target="_blank" class="btn btn-premium btn-magnetic" style="background:#1877f2;color:white;padding:12px 24px;border-radius:10px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;" rel="noopener noreferrer">
            📘 Facebook
        </a>
        <a href="<?= htmlspecialchars($telegramShare) ?>" target="_blank" class="btn btn-premium btn-magnetic" style="background:#0088cc;color:white;padding:12px 24px;border-radius:10px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;" rel="noopener noreferrer">
            ✈️ Telegram
        </a>
        <a href="<?= htmlspecialchars($whatsappShare) ?>" target="_blank" class="btn btn-premium btn-magnetic" style="background:#25d366;color:white;padding:12px 24px;border-radius:10px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;" rel="noopener noreferrer">
            💬 WhatsApp
        </a>
        <button class="btn btn-premium btn-magnetic" style="background:var(--glass-bg-3);backdrop-filter:blur(20px);border:1px solid var(--glass-border-3);color:var(--current-text);padding:12px 24px;border-radius:10px;font-weight:600;" onclick="copyAllShareText()">📋 Copy All</button>
    </div>
</div>

<!-- Referral Tree Visualization -->
<div class="glass-2" style="padding:28px;margin-bottom:24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom:16px;">🌳 Referral Network</h3>
    <?php if (empty($referrals)): ?>
    <div style="text-align:center;padding:40px;color:var(--current-text-muted);">
        <div style="font-size:4rem;margin-bottom:16px;">🌱</div>
        <p style="font-size:1.1rem;">No referrals yet. Share your link to start building your network!</p>
    </div>
    <?php else: ?>
    <div class="chart-container" style="min-height:200px;">
        <canvas id="referralTreeCanvas" height="200"></canvas>
    </div>
    <?php endif; ?>
</div>

<!-- Referral Growth Chart -->
<div class="glass-2" style="padding:28px;margin-bottom:24px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom:16px;">📈 Referral Growth</h3>
    <?php 
    $refByDate = [];
    foreach ($referrals as $ref) {
        $date = date('Y-m-d', strtotime($ref['created_at'] ?? 'today'));
        $refByDate[$date] = ($refByDate[$date] ?? 0) + 1;
    }
    $last14Days = [];
    for ($i = 13; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $last14Days[] = $refByDate[$date] ?? 0;
    }
    $labels14 = [];
    for ($i = 13; $i >= 0; $i--) {
        $labels14[] = date('d/m', strtotime("-$i days"));
    }
    ?>
    <div class="chart-container">
        <canvas id="referralGrowthChart" height="150"></canvas>
    </div>
    <script>
    window.__referralChartData = {
        labels: <?= json_encode($labels14) ?>,
        data: <?= json_encode($last14Days) ?>
    };
    </script>
</div>

<!-- Referral Table -->
<div class="glass-2" style="padding:28px;" data-scroll-animate="fade-in">
    <h3 style="margin-bottom:20px;">📋 Your Referrals</h3>
    <?php if (empty($referrals)): ?>
    <div style="text-align:center;padding:40px;color:var(--current-text-muted);">
        <div style="font-size:3rem;margin-bottom:16px;">🌱</div>
        <p>No referrals yet. Share your link to start earning!</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>Referred User</th>
                    <th>Date</th>
                    <th>Earnings</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($referrals as $ref): ?>
                <tr>
                    <td><?= htmlspecialchars($ref['referred_email'] ?? 'Unknown') ?></td>
                    <td><?= timeAgo($ref['created_at'] ?? '') ?></td>
                    <td style="color:var(--emerald-green);font-weight:700;">+$<?= number_format(floatval($ref['earnings'] ?? 0), 2) ?></td>
                    <td><span class="badge badge-success">🟢 Active</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
// Referral tree visualization
function drawReferralTree() {
    const canvas = document.getElementById('referralTreeCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const rect = canvas.parentElement.getBoundingClientRect();
    const w = rect.width - 40;
    const h = 200;
    const dpr = window.devicePixelRatio || 1;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.scale(dpr, dpr);

    const count = <?= count($referrals) ?>;
    if (count === 0) return;

    // Center node (you)
    const cx = w / 2;
    const cy = 40;
    ctx.beginPath();
    ctx.arc(cx, cy, 18, 0, Math.PI * 2);
    ctx.fillStyle = '#4361ee';
    ctx.fill();
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 2;
    ctx.stroke();
    ctx.fillStyle = '#fff';
    ctx.font = 'bold 12px system-ui';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('You', cx, cy);

    // Branch lines to referrals
    const radius = Math.min(w * 0.35, 180);
    for (let i = 0; i < Math.min(count, 12); i++) {
        const angle = (i / Math.min(count, 12)) * Math.PI * 2 - Math.PI / 2;
        const x = cx + Math.cos(angle) * radius;
        const y = cy + Math.sin(angle) * radius + 20;
        
        // Line
        ctx.beginPath();
        ctx.moveTo(cx, cy + 18);
        const midX = (cx + x) / 2;
        const midY = (cy + 20 + y) / 2;
        ctx.quadraticCurveTo(midX, midY - 20, x, y);
        ctx.strokeStyle = 'rgba(67,97,238,0.3)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        // Node
        ctx.beginPath();
        ctx.arc(x, y, 8, 0, Math.PI * 2);
        ctx.fillStyle = '#06d6a0';
        ctx.fill();
    }
}

function drawReferralGrowth() {
    const canvas = document.getElementById('referralGrowthChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const rect = canvas.parentElement.getBoundingClientRect();
    const w = rect.width - 40;
    const h = 150;
    const dpr = window.devicePixelRatio || 1;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.scale(dpr, dpr);

    const data = window.__referralChartData?.data || [];
    const maxVal = Math.max(...data, 1);
    const padding = { top: 10, bottom: 20, left: 0, right: 0 };
    const chartW = w - padding.left - padding.right;
    const chartH = h - padding.top - padding.bottom;
    const stepX = chartW / Math.max(data.length - 1, 1);

    // Bars
    data.forEach((val, i) => {
        const x = padding.left + i * stepX;
        const barW = Math.max(stepX * 0.6, 4);
        const barH = (val / maxVal) * chartH;
        const y = padding.top + chartH - barH;
        
        const grad = ctx.createLinearGradient(0, y, 0, padding.top + chartH);
        grad.addColorStop(0, '#06d6a0');
        grad.addColorStop(1, '#06d6a060');
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.roundRect(x - barW/2, y, barW, barH, [3, 3, 0, 0]);
        ctx.fill();
    });

    // Labels
    ctx.fillStyle = 'rgba(255,255,255,0.4)';
    ctx.font = '9px system-ui';
    ctx.textAlign = 'center';
    const labels = window.__referralChartData?.labels || [];
    data.forEach((val, i) => {
        if (i % 2 === 0 || i === data.length - 1) {
            const x = padding.left + i * stepX;
            ctx.fillText(labels[i] || '', x, h - 4);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        drawReferralTree();
        drawReferralGrowth();
    }, 500);
    window.addEventListener('resize', () => {
        drawReferralTree();
        drawReferralGrowth();
    });
});

function copyAllShareText() {
    const text = `Join EARNNOVA and start earning online!\n\n✅ Watch Ads\n✅ Complete Tasks\n✅ Earn Real Rewards\n\nRegister Now: <?= htmlspecialchars($referralLink) ?>`;
    EARNNOVA.copyToClipboard(text);
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
