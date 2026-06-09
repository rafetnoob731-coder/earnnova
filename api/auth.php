<?php
// EARNNOVA - Authentication API
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'register':
        handleRegister();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'check_session':
        checkSession();
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function handleLogin() {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = sanitizeInput($input['email'] ?? '');
    $password = $input['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'Email and password required'], 400);
    }
    
    // Firebase Auth
    $firebaseApiKey = FIREBASE_API_KEY;
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=$firebaseApiKey";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['email' => $email, 'password' => $password, 'returnSecureToken' => true]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        $uid = $data['localId'] ?? '';
        
        if ($uid) {
            $_SESSION['uid'] = $uid;
            $_SESSION['id_token'] = $data['idToken'] ?? '';
            logActivity($uid, 'api_login', 'API login');
            
            jsonResponse(['success' => true, 'data' => ['uid' => $uid, 'email' => $email]]);
        }
    }
    
    jsonResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
}

function handleRegister() {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = sanitizeInput($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $username = sanitizeInput($input['username'] ?? '');
    
    if (empty($email) || empty($password) || empty($username)) {
        jsonResponse(['success' => false, 'message' => 'All fields required'], 400);
    }
    
    $firebaseApiKey = FIREBASE_API_KEY;
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=$firebaseApiKey";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['email' => $email, 'password' => $password, 'returnSecureToken' => true]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        $uid = $data['localId'] ?? '';
        
        if ($uid) {
            // Create user in database
            db()->createUser([
                'uid' => $uid,
                'email' => $email,
                'username' => $username,
                'balance' => 0,
                'referral_balance' => 0,
                'activation_status' => 'inactive',
                'referral_code' => generateReferralCode(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            $_SESSION['uid'] = $uid;
            $_SESSION['id_token'] = $data['idToken'] ?? '';
            
            jsonResponse(['success' => true, 'message' => 'Account created']);
        }
    }
    
    $errorData = json_decode($response, true);
    jsonResponse(['success' => false, 'message' => $errorData['error']['message'] ?? 'Registration failed'], 400);
}

function handleLogout() {
    session_destroy();
    header('Location: /login.php');
    exit;
}

function checkSession() {
    if (isLoggedIn()) {
        $user = getCurrentUser();
        jsonResponse(['success' => true, 'data' => ['logged_in' => true, 'user' => $user]]);
    } else {
        jsonResponse(['success' => true, 'data' => ['logged_in' => false]]);
    }
}
