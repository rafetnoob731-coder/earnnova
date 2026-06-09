<?php
// EARNNOVA - Premium Login Page v4.0
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Sign In';
$showSidebar = false;

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

<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;position:relative;overflow:hidden;">
    <!-- Animated 3D floating shapes -->
    <div class="floating-shape" style="width:400px;height:400px;background:var(--cyber-cyan);top:5%;right:2%;opacity:0.06;animation-duration:15s;"></div>
    <div class="floating-shape" style="width:300px;height:300px;background:var(--neon-coral);bottom:10%;left:3%;opacity:0.05;animation-duration:18s;animation-delay:-5s;"></div>
    <div class="floating-shape" style="width:200px;height:200px;background:var(--royal-violet);top:50%;left:45%;opacity:0.04;animation-duration:12s;animation-delay:-8s;"></div>
    
    <!-- Particle network overlay -->
    <div style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;opacity:0.03;background-image:radial-gradient(circle at 20% 30%,var(--cyber-cyan) 0px,transparent 1px),radial-gradient(circle at 80% 70%,var(--royal-violet) 0px,transparent 1px);background-size:60px 60px;"></div>
    
    <div class="glass-2" style="max-width:440px;width:100%;padding:48px 40px;border-radius:32px;position:relative;z-index:1;" data-reveal="fadeInUp">
        <!-- Logo -->
        <div style="text-align:center;margin-bottom:32px;">
            <div style="width:68px;height:68px;border-radius:20px;background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;color:white;margin:0 auto 16px;box-shadow:0 10px 30px rgba(0,240,255,0.25);">E</div>
            <h1 style="font-size:1.8rem;font-weight:800;letter-spacing:-0.02em;background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Welcome Back</h1>
            <p style="color:var(--current-text-secondary);margin-top:8px;font-size:0.95rem;">Sign in to your <?= SITE_NAME ?> account</p>
        </div>

        <?php if ($error): ?>
        <div class="toast toast-error" style="margin-bottom:20px;animation:none;border-radius:16px;">
            <span class="toast-icon">❌</span>
            <div class="toast-content"><div class="toast-message"><?= htmlspecialchars($error) ?></div></div>
        </div>
        <?php endif; ?>

        <!-- Social Login -->
        <div class="social-login" style="gap:10px;">
            <button class="social-btn google-btn" style="border-radius:14px;padding:12px;font-size:0.9rem;" onclick="EARNNOVA.showToast('Google Login', 'Google authentication coming soon!', 'info')">
                <span style="font-size:1.3rem;font-weight:700;background:conic-gradient(from 0deg,#4285f4,#34a853,#fbbc05,#ea4335);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">G</span> Google
            </button>
            <button class="social-btn" style="border-radius:14px;padding:12px;font-size:0.9rem;" onclick="EARNNOVA.showToast('Coming Soon', 'Apple login coming soon!', 'info')">
                <span style="font-size:1.3rem;">🍎</span> Apple
            </button>
            <button class="social-btn" style="border-radius:14px;padding:12px;font-size:0.9rem;" onclick="EARNNOVA.showToast('Coming Soon', 'Telegram login coming soon!', 'info')">
                <span style="font-size:1.3rem;">✈️</span> Telegram
            </button>
        </div>

        <div class="divider">or continue with email</div>

        <form method="POST" action="">
            <div class="form-group input-premium">
                <label class="form-label" style="font-size:0.85rem;font-weight:500;">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email" style="border-radius:16px;padding:16px 20px;">
                <span class="input-focus-line"></span>
            </div>
            <div class="form-group input-premium">
                <label class="form-label" style="font-size:0.85rem;font-weight:500;">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="loginPassword" class="form-input" placeholder="••••••••" required autocomplete="current-password" style="border-radius:16px;padding:16px 48px 16px 20px;">
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('loginPassword', this)" aria-label="Toggle password visibility" style="right:8px;">👁️</button>
                </div>
                <span class="input-focus-line"></span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;color:var(--current-text-secondary);cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:18px;height:18px;accent-color:var(--cyber-cyan);border-radius:4px;"> Remember me
                </label>
                <a href="#" onclick="showForgotPasswordModal()" style="color:var(--cyber-cyan);text-decoration:none;font-size:0.9rem;font-weight:500;">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-premium btn-primary btn-block btn-lg btn-glow-premium btn-magnetic" style="background:linear-gradient(135deg,var(--cyber-cyan),var(--royal-violet));color:white;padding:16px;border-radius:40px;font-weight:700;">
                <span>Sign In</span>
            </button>
        </form>

        <!-- Biometric + 2FA indicators -->
        <div style="display:flex;justify-content:center;gap:16px;margin-top:20px;">
            <span style="font-size:0.8rem;color:var(--current-text-muted);display:flex;align-items:center;gap:4px;">🔒 2FA Ready</span>
            <span style="font-size:0.8rem;color:var(--current-text-muted);display:flex;align-items:center;gap:4px;">📱 Face ID</span>
        </div>

        <div style="text-align:center;margin-top:20px;">
            <p style="color:var(--current-text-secondary);font-size:0.9rem;">
                New to <?= SITE_NAME ?>? 
                <a href="/register.php" style="color:var(--cyber-cyan);text-decoration:none;font-weight:600;">Create account →</a>
            </p>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal-overlay" id="forgotPasswordModal">
    <div class="modal modal-premium" style="border-radius:32px;">
        <div class="modal-header">
            <h2 class="modal-title" style="font-size:1.3rem;">🔑 Reset Password</h2>
            <button class="modal-close" onclick="EARNNOVA.closeModal('forgotPasswordModal')">✕</button>
        </div>
        <div class="modal-body">
            <p style="color:var(--current-text-secondary);margin-bottom:20px;line-height:1.6;">Enter your email and we'll send you a reset link.</p>
            <div class="form-group input-premium">
                <label class="form-label">Email Address</label>
                <input type="email" id="resetEmail" class="form-input" placeholder="your@email.com" style="border-radius:16px;">
                <span class="input-focus-line"></span>
            </div>
            <button class="btn btn-primary btn-block" style="border-radius:40px;padding:14px;" onclick="sendPasswordReset()">Send Reset Link</button>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') { field.type = 'text'; btn.textContent = '🙈'; }
    else { field.type = 'password'; btn.textContent = '👁️'; }
}
function showForgotPasswordModal() { EARNNOVA.openModal('forgotPasswordModal'); }
function sendPasswordReset() {
    const email = document.getElementById('resetEmail').value;
    if (!email) { EARNNOVA.showToast('Error', 'Please enter your email', 'error'); return; }
    const apiKey = '<?= FIREBASE_API_KEY ?>';
    fetch(`https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key=${apiKey}`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ requestType: 'PASSWORD_RESET', email: email })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) EARNNOVA.showToast('Error', data.error.message || 'Failed', 'error');
        else {
            EARNNOVA.closeModal('forgotPasswordModal');
            EARNNOVA.showToast('Success', 'Reset link sent to your email!', 'success');
            document.getElementById('resetEmail').value = '';
        }
    })
    .catch(() => EARNNOVA.showToast('Error', 'Network error', 'error'));
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
