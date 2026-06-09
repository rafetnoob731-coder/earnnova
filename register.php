<?php
// EARNNOVA - Registration Page
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Create Account';
$showSidebar = false;

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $username = sanitizeInput($_POST['username'] ?? '');
    $referralCode = sanitizeInput($_POST['ref'] ?? '');
    
    if (empty($email) || empty($password) || empty($username)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        // Create Firebase user
        $firebaseApiKey = FIREBASE_API_KEY;
        $firebaseUrl = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=$firebaseApiKey";
        
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
            
            if ($uid) {
                // Generate referral code
                $userRefCode = generateReferralCode();
                
                // Create user in database
                $userData = [
                    'uid' => $uid,
                    'email' => $email,
                    'username' => $username,
                    'balance' => 0,
                    'referral_balance' => 0,
                    'activation_status' => 'inactive',
                    'referral_code' => $userRefCode,
                    'referred_by' => '',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Process referral if code provided
                if (!empty($referralCode)) {
                    // Find referrer by referral code
                    $referrers = db()->request('GET', "/rest/v1/users?referral_code=eq.$referralCode&select=uid")['data'] ?? [];
                    if (!empty($referrers)) {
                        $referrerUid = $referrers[0]['uid'];
                        $userData['referred_by'] = $referrerUid;
                        
                        // Create referral record
                        db()->createReferral([
                            'referrer_id' => $referrerUid,
                            'referred_uid' => $uid,
                            'referred_email' => $email,
                            'earnings' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        
                        // Give referral bonus
                        processReferralReward($referrerUid);
                    }
                }
                
                $result = db()->createUser($userData);
                
                if ($result['success']) {
                    $_SESSION['user_id'] = $result['data'][0]['id'] ?? $uid;
                    $_SESSION['uid'] = $uid;
                    $_SESSION['id_token'] = $data['idToken'] ?? '';
                    
                    logActivity($uid, 'registration', 'New user registered');
                    
                    header('Location: /activation.php');
                    exit;
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } else {
            $errorData = json_decode($response, true);
            $error = $errorData['error']['message'] ?? 'Registration failed';
            if ($error === 'EMAIL_EXISTS') {
                $error = 'An account with this email already exists';
            }
        }
    }
}

$ref = sanitizeInput($_GET['ref'] ?? '');

include __DIR__ . '/includes/header.php';
?>

<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px;">
    <div class="glass-card" style="max-width: 480px; width: 100%; padding: 40px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <img src="/assets/images/logo.png" alt="<?= SITE_NAME ?>" style="height: 50px; margin-bottom: 16px;" onerror="this.style.display='none'">
            <h1 style="font-size: 1.8rem; font-weight: 700;">Create Account</h1>
            <p style="color: var(--current-text-secondary); margin-top: 8px;">Join <?= SITE_NAME ?> and start earning</p>
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
            <input type="hidden" name="ref" value="<?= htmlspecialchars($ref) ?>">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" placeholder="Choose a username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="Min. 6 characters" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-input" placeholder="Repeat password" required>
            </div>
            <?php if (!empty($ref)): ?>
            <div class="form-group">
                <label class="form-label">Referral Code</label>
                <input type="text" class="form-input" value="<?= htmlspecialchars($ref) ?>" disabled style="opacity: 0.7;">
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
        </form>

        <div style="text-align: center; margin-top: 24px;">
            <p style="color: var(--current-text-secondary);">
                Already have an account? 
                <a href="/login.php" style="color: var(--electric-blue); text-decoration: none; font-weight: 600;">Sign In</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
