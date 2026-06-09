<?php
// EARNNOVA - PlatoBoost Verification API
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../lib/Platoboost.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

requireLogin();

$user = getCurrentUser();
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'get_link':
        handleGetLink();
        break;
    case 'verify_key':
        handleVerifyKey($user, $input);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

function handleGetLink() {
    try {
        $boost = new Platoboost();
        $link = $boost->getLink();
        if ($link) {
            jsonResponse(['success' => true, 'data' => ['url' => $link]]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to generate link'], 500);
        }
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Service error: ' . $e->getMessage()], 500);
    }
}

function handleVerifyKey($user, $input) {
    $key = sanitizeInput($input['key'] ?? '');
    
    if (empty($key)) {
        jsonResponse(['success' => false, 'message' => 'Key is required'], 400);
    }
    
    // Check if already activated
    if (($user['activation_status'] ?? 'inactive') === 'active') {
        jsonResponse(['success' => false, 'message' => 'Account already activated'], 400);
    }
    
    try {
        $boost = new Platoboost();
        $valid = $boost->verifyKey($key);
        
        if ($valid) {
            // Activate account
            db()->updateUser($user['uid'], [
                'activation_status' => 'active',
                'activation_key' => $key,
                'activation_date' => date('Y-m-d H:i:s')
            ]);
            
            db()->createActivationLog([
                'user_id' => $user['uid'],
                'key' => $key,
                'result' => 'success',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                'device' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Activation bonus
            $bonus = 0.05;
            addBalance($user['uid'], $bonus);
            db()->createTransaction([
                'user_id' => $user['uid'],
                'amount' => $bonus,
                'type' => 'activation_bonus',
                'description' => 'Account activation bonus',
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            logActivity($user['uid'], 'api_activation', 'Account activated via PlatoBoost API');
            
            jsonResponse([
                'success' => true,
                'message' => 'Account activated successfully!',
                'bonus' => $bonus
            ]);
        } else {
            db()->createActivationLog([
                'user_id' => $user['uid'],
                'key' => $key,
                'result' => 'failed',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                'device' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            jsonResponse(['success' => false, 'message' => 'Invalid activation key'], 400);
        }
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Verification error: ' . $e->getMessage()], 500);
    }
}
