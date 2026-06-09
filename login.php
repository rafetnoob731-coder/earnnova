<?php
// EARNNOVA - Login Page
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
                // Get or create user in database
                $user = db()->getUser($uid);
                if (!$user) {
                    // Create user record
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
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['uid'] = $uid;
                $_SESSION['id_token'] = $idToken;
                
                // Log activity
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

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px;">
    <div class="glass-card" style="max-width: 420px; width: 100%; padding: 40px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <img src="/assets/images/logo.png" alt="<?= SITE_NAME ?>" style="height: 50px; margin-bottom: 16px;" onerror="this.style.display='none'">
            <h1 style="font-size: 1.8rem; font-weight: 700;">Welcome Back</h1>
            <p style="color: var(--current-text-secondary); margin-top: 8px;">Sign in to your <?= SITE_NAME ?> account</p>
        </div>

        <?php if ($error): ?>
        <div class="toast toast-error" style="margin-bottom: 20px; animation: none;">
            <span class="toast-icon">❌</span>
            <div class="toast-content">
                <div class="toast-message"><?= htmlspecialchars($error) ?></div>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="#" style="color: var(--electric-blue); text-decoration: none; font-size: 0.9rem;">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
        </form>

        <div style="text-align: center; margin-top: 24px;">
            <p style="color: var(--current-text-secondary);">
                Don't have an account? 
                <a href="/register.php" style="color: var(--electric-blue); text-decoration: none; font-weight: 600;">Create One</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
