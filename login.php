<?php
// EARNNOVA - Premium Login Page
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
        // Firebase Auth REST API verification
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
            if ($error === 'EMAIL_NOT_FOUND' || $error === 'INVALID_PASSWORD') {
                $error = 'Invalid email or password';
            } elseif ($error === 'USER_DISABLED') {
                $error = 'Account has been disabled';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;position:relative;overflow:hidden;">
    <!-- Floating Shapes -->
    <div class="floating-shape" style="width:300px;height:300px;background:var(--electric-blue);top:10%;right:5%;animation-delay:0s;"></div>
    <div class="floating-shape" style="width:200px;height:200px;background:var(--royal-purple);bottom:15%;left:8%;animation-delay:-4s;"></div>
    <div class="floating-shape" style="width:150px;height:150px;background:var(--cyan-accent);top:40%;left:50%;animation-delay:-8s;"></div>
    
    <div class="glass-card" style="max-width:440px;width:100%;padding:48px 40px;position:relative;z-index:1;" data-reveal="fadeInUp">
        <!-- Logo -->
        <div style="text-align:center;margin-bottom:32px;">
            <div style="width:64px;height:64px;border-radius:18px;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:900;color:white;margin:0 auto 16px;box-shadow:0 10px 30px rgba(67,97,238,0.3);">E</div>
            <h1 style="font-size:1.8rem;font-weight:700;">Welcome Back</h1>
            <p style="color:var(--current-text-secondary);margin-top:8px;font-size:0.95rem;">Sign in to your <?= SITE_NAME ?> account</p>
        </div>

        <?php if ($error): ?>
        <div class="toast toast-error" style="margin-bottom:20px;animation:none;">
            <span class="toast-icon">❌</span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($error) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Social Login Buttons -->
        <div class="social-login">
            <button class="social-btn google-btn" onclick="EARNNOVA.showToast('Google Login', 'Google authentication coming soon!', 'info')">
                <span style="font-size:1.2rem;">G</span> Google
            </button>
            <button class="social-btn" onclick="EARNNOVA.showToast('Coming Soon', 'More login methods coming soon!', 'info')">
                <span style="font-size:1.2rem;">🔵</span> More
            </button>
        </div>

        <div class="divider">or continue with email</div>

        <form method="POST" action="">
            <div class="form-group input-premium">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email">
                <span class="input-focus-line"></span>
            </div>
            <div class="form-group input-premium">
                <label class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="loginPassword" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePasswordVisibility('loginPassword', this)" aria-label="Toggle password visibility">👁️</button>
                </div>
                <span class="input-focus-line"></span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;color:var(--current-text-secondary);cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:18px;height:18px;accent-color:var(--electric-blue);"> Remember me
                </label>
                <a href="#" onclick="showForgotPasswordModal()" style="color:var(--electric-blue);text-decoration:none;font-size:0.9rem;font-weight:500;">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" style="padding:16px;">
                <span>Sign In</span>
            </button>
        </form>

        <div style="text-align:center;margin-top:24px;">
            <p style="color:var(--current-text-secondary);font-size:0.9rem;">
                Don't have an account? 
                <a href="/register.php" style="color:var(--electric-blue);text-decoration:none;font-weight:600;">Create One →</a>
            </p>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal-overlay" id="forgotPasswordModal">
    <div class="modal modal-premium">
        <div class="modal-header">
            <h2 class="modal-title">🔑 Reset Password</h2>
            <button class="modal-close" onclick="EARNNOVA.closeModal('forgotPasswordModal')">✕</button>
        </div>
        <div class="modal-body">
            <p style="color:var(--current-text-secondary);margin-bottom:20px;line-height:1.6;">
                Enter your email address and we'll send you a link to reset your password.
            </p>
            <div class="form-group input-premium">
                <label class="form-label">Email Address</label>
                <input type="email" id="resetEmail" class="form-input" placeholder="your@email.com">
                <span class="input-focus-line"></span>
            </div>
            <button class="btn btn-primary btn-block" onclick="sendPasswordReset()">
                Send Reset Link
            </button>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        btn.textContent = '🙈';
    } else {
        field.type = 'password';
        btn.textContent = '👁️';
    }
}

function showForgotPasswordModal() {
    EARNNOVA.openModal('forgotPasswordModal');
}

function sendPasswordReset() {
    const email = document.getElementById('resetEmail').value;
    if (!email) {
        EARNNOVA.showToast('Error', 'Please enter your email', 'error');
        return;
    }
    
    // Firebase password reset
    const apiKey = '<?= FIREBASE_API_KEY ?>';
    fetch(`https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key=${apiKey}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            requestType: 'PASSWORD_RESET',
            email: email
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            EARNNOVA.showToast('Error', data.error.message || 'Failed to send reset email', 'error');
        } else {
            EARNNOVA.closeModal('forgotPasswordModal');
            EARNNOVA.showToast('Success', 'Password reset link sent to your email!', 'success');
            document.getElementById('resetEmail').value = '';
        }
    })
    .catch(() => {
        EARNNOVA.showToast('Error', 'Network error. Please try again.', 'error');
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
