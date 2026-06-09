<?php
// EARNNOVA — Login Page v5.0 (Clean Glass Card — No Navigation)
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Sign In';
$showSidebar = false;
$hideNav = true; // signals header to suppress all nav

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $firebaseApiKey = FIREBASE_API_KEY;
        $firebaseUrl = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$firebaseApiKey";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $firebaseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'email' => $email,
                'password' => $password,
                'returnSecureToken' => true
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            $uid = $data['localId'] ?? '';
            $idToken = $data['idToken'] ?? '';
            
            if ($uid) {
                $user = db()->getUser($uid);
                if (!$user) {
                    db()->createUser([
                        'uid' => $uid,
                        'email' => $email,
                        'username' => explode('@', $email)[0],
                        'balance' => 0,
                        'referral_balance' => 0,
                        'activation_status' => 'inactive',
                        'referral_code' => generateReferralCode(),
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $user = db()->getUser($uid);
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['uid'] = $uid;
                $_SESSION['id_token'] = $idToken;
                logActivity($uid, 'login', 'User logged in');
                header('Location: /dashboard.php');
                exit;
            }
        } else {
            $errorData = json_decode($response, true);
            $error = $errorData['error']['message'] ?? 'Invalid email or password';
            if ($error === 'EMAIL_NOT_FOUND' || $error === 'INVALID_PASSWORD') $error = 'Invalid email or password';
            elseif ($error === 'USER_DISABLED') $error = 'Account has been disabled';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<style>
/* Login-specific overrides — no nav, pure centering */
body.no-mobile-nav {
    padding-bottom: 0 !important;
    background: var(--bg-dark) !important;
}
body.no-mobile-nav .main-content {
    margin-left: 0 !important;
    padding-top: 0 !important;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.login-wrapper {
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    position: relative;
    overflow: hidden;
}
.login-card {
    width: 100%;
    max-width: 420px;
    background: rgba(255,255,255,0.04);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 32px;
    padding: 48px 40px;
    box-shadow: 0 25px 60px -12px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.06);
    position: relative;
    z-index: 2;
    transition: transform 0.3s var(--ease-spring);
}
.login-card:hover {
    transform: translateY(-2px);
}
.login-logo {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--cyber-cyan), var(--royal-violet));
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin: 0 auto 20px;
    box-shadow: 0 12px 32px rgba(0,240,255,0.25);
}
.login-welcome {
    text-align: center;
    margin-bottom: 32px;
}
.login-welcome h1 {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, var(--cyber-cyan), var(--royal-violet));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 6px;
}
.login-welcome p {
    color: var(--current-text-muted);
    font-size: 0.95rem;
}
.input-group {
    margin-bottom: 20px;
}
.input-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--current-text-secondary);
    margin-bottom: 8px;
}
.input-field {
    position: relative;
}
.input-field .input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.1rem;
    pointer-events: none;
    opacity: 0.5;
}
.input-field input {
    width: 100%;
    padding: 16px 16px 16px 48px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    color: var(--current-text);
    font-size: 0.95rem;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s ease;
    outline: none;
}
.input-field input:focus {
    border-color: var(--cyber-cyan);
    box-shadow: 0 0 0 3px rgba(0,240,255,0.1);
    background: rgba(255,255,255,0.07);
}
.input-field input::placeholder {
    color: var(--current-text-muted);
    opacity: 0.5;
}
.input-field .toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 8px;
    opacity: 0.5;
    transition: opacity 0.2s;
}
.input-field .toggle-password:hover { opacity: 1; }
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    gap: 12px;
}
.form-options label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: var(--current-text-secondary);
    cursor: pointer;
}
.form-options input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--cyber-cyan);
    border-radius: 4px;
    cursor: pointer;
}
.form-options a {
    color: var(--cyber-cyan);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
}
.form-options a:hover { text-decoration: underline; }
.btn-gradient {
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 40px;
    font-size: 1rem;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    background: linear-gradient(135deg, var(--cyber-cyan), var(--royal-violet));
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s var(--ease-spring);
    box-shadow: 0 8px 24px rgba(0,240,255,0.25);
}
.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(0,240,255,0.35);
}
.btn-gradient:active {
    transform: scale(0.97);
}
.btn-gradient .ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    transform: scale(0);
    animation: rippleAnim 0.6s ease-out;
    pointer-events: none;
}
@keyframes rippleAnim {
    to { transform: scale(4); opacity: 0; }
}
.divider {
    display: flex;
    align-items: center;
    gap: 16px;
    margin: 24px 0;
    color: var(--current-text-muted);
    font-size: 0.85rem;
}
.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.08);
}
.social-row {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}
.social-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.04);
    color: var(--current-text);
    font-size: 0.9rem;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.social-btn:hover {
    background: rgba(255,255,255,0.08);
    transform: translateY(-1px);
}
.social-btn .icon { font-size: 1.3rem; }
.social-btn.google { border-color: rgba(66,133,244,0.2); }
.social-btn.google:hover { border-color: rgba(66,133,244,0.4); }
.social-btn.telegram { border-color: rgba(0,136,204,0.2); }
.social-btn.telegram:hover { border-color: rgba(0,136,204,0.4); }
.register-link {
    text-align: center;
    margin-top: 8px;
}
.register-link a {
    color: var(--cyber-cyan);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    transition: gap 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.register-link a:hover { gap: 10px; }
.register-link a .arrow { transition: transform 0.3s; }
.register-link a:hover .arrow { transform: translateX(4px); }
.security-note {
    text-align: center;
    margin-top: 20px;
    font-size: 0.8rem;
    color: var(--current-text-muted);
    opacity: 0.6;
}
/* Floating background shapes */
.floating-shape {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    animation: floatShape 20s ease-in-out infinite;
    z-index: 1;
}
@keyframes floatShape {
    0%, 100% { transform: translate(0,0) scale(1); }
    33% { transform: translate(30px,-30px) scale(1.05); }
    66% { transform: translate(-20px,20px) scale(0.95); }
}
/* Particles */
.particle-grid {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    pointer-events: none;
    z-index: 0;
    opacity: 0.03;
    background-image:
        radial-gradient(circle at 20% 30%, var(--cyber-cyan) 0px, transparent 1px),
        radial-gradient(circle at 80% 70%, var(--royal-violet) 0px, transparent 1px),
        radial-gradient(circle at 50% 50%, var(--neon-coral) 0px, transparent 1px);
    background-size: 60px 60px, 80px 80px, 50px 50px;
}
@media (max-width: 480px) {
    .login-card { padding: 36px 24px; border-radius: 24px; }
    .social-row { flex-direction: column; }
    .login-welcome h1 { font-size: 1.5rem; }
}
</style>

<div class="login-wrapper">
    <div class="particle-grid"></div>
    
    <!-- Floating shapes -->
    <div class="floating-shape" style="width:450px;height:450px;background:var(--cyber-cyan);top:2%;right:5%;opacity:0.05;animation-delay:0s;"></div>
    <div class="floating-shape" style="width:350px;height:350px;background:var(--neon-coral);bottom:5%;left:3%;opacity:0.04;animation-delay:-6s;"></div>
    <div class="floating-shape" style="width:250px;height:250px;background:var(--royal-violet);top:40%;left:50%;opacity:0.03;animation-delay:-12s;"></div>

    <div class="login-card" data-reveal="fadeInUp">
        <div class="login-logo">E</div>
        
        <div class="login-welcome">
            <h1>Welcome Back</h1>
            <p>Sign in to your <?= SITE_NAME ?> account</p>
        </div>

        <?php if ($error): ?>
        <div style="background:rgba(255,0,102,0.1);border:1px solid rgba(255,0,102,0.2);border-radius:14px;padding:12px 16px;margin-bottom:20px;color:var(--neon-coral);font-size:0.9rem;display:flex;align-items:center;gap:10px;">
            <span>❌</span> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <label for="email">Email Address</label>
                <div class="input-field">
                    <span class="input-icon">✉️</span>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email">
                </div>
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <div class="input-field">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="toggle-password" onclick="togglePass()" aria-label="Toggle password visibility" id="passToggle">👁️</button>
                </div>
            </div>

            <div class="form-options">
                <label>
                    <input type="checkbox" name="remember"> Remember me for 30 days
                </label>
                <a href="#" onclick="showForgot()">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-gradient" id="loginBtn">Sign In →</button>
        </form>

        <div class="divider">Or continue with</div>

        <div class="social-row">
            <button class="social-btn google" onclick="showToast('Google login coming soon!')">
                <span class="icon" style="font-weight:700;background:conic-gradient(from 0deg,#4285f4,#34a853,#fbbc05,#ea4335);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">G</span> Google
            </button>
            <button class="social-btn telegram" onclick="showToast('Telegram login coming soon!')">
                <span class="icon">✈️</span> Telegram
            </button>
        </div>

        <div class="register-link">
            <a href="/register.php">
                New to <?= SITE_NAME ?>? <strong>Create account</strong>
                <span class="arrow">→</span>
            </a>
        </div>

        <div class="security-note">🔒 Secured by SSL encryption &bull; 2FA available</div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal-overlay" id="forgotModal">
    <div class="modal" style="max-width:420px;border-radius:32px;">
        <div class="modal-header">
            <h2 style="font-size:1.3rem;font-weight:700;">🔑 Reset Password</h2>
            <button class="modal-close" onclick="closeForgot()">✕</button>
        </div>
        <div class="modal-body">
            <p style="color:var(--current-text-secondary);margin-bottom:20px;line-height:1.6;">Enter your email and we'll send you a reset link.</p>
            <div class="input-group">
                <label for="resetEmail">Email Address</label>
                <div class="input-field">
                    <span class="input-icon">✉️</span>
                    <input type="email" id="resetEmail" placeholder="your@email.com">
                </div>
            </div>
            <button class="btn-gradient" onclick="sendReset()">Send Reset Link</button>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const pwd = document.getElementById('password');
    const btn = document.getElementById('passToggle');
    if (pwd.type === 'password') { pwd.type = 'text'; btn.textContent = '🙈'; }
    else { pwd.type = 'password'; btn.textContent = '👁️'; }
}
function showForgot() {
    document.getElementById('forgotModal').classList.add('active');
}
function closeForgot() {
    document.getElementById('forgotModal').classList.remove('active');
}
function sendReset() {
    const email = document.getElementById('resetEmail').value.trim();
    if (!email) { showToast('Please enter your email', 'error'); return; }
    const apiKey = '<?= FIREBASE_API_KEY ?>';
    fetch(`https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key=${apiKey}`, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({requestType:'PASSWORD_RESET', email})
    })
    .then(r => r.json())
    .then(d => {
        if (d.error) showToast(d.error.message || 'Failed', 'error');
        else { showToast('✅ Reset link sent!'); closeForgot(); document.getElementById('resetEmail').value = ''; }
    })
    .catch(() => showToast('Network error', 'error'));
}
function showToast(msg, type) {
    const c = document.querySelector('.toast-container') || (() => { const d = document.createElement('div'); d.className='toast-container'; document.body.appendChild(d); return d; })();
    const t = document.createElement('div');
    t.className = 'toast ' + (type === 'error' ? 'toast-error' : 'toast-success');
    t.innerHTML = `<span class="toast-icon">${type === 'error' ? '❌' : '✅'}</span><div class="toast-content"><div class="toast-message">${msg}</div></div>`;
    c.appendChild(t);
    setTimeout(() => { t.classList.add('hide'); setTimeout(() => t.remove(), 400); }, 3500);
}
// Click overlay to close modal
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('active');
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
