<?php
// EARNNOVA - Utility Functions
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// Authentication Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['uid']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireActive() {
    requireLogin();
    $user = db()->getUser($_SESSION['uid']);
    if (!$user || $user['activation_status'] ?? 'inactive' !== 'active') {
        header('Location: /activation.php');
        exit;
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return db()->getUser($_SESSION['uid']);
}

// Balance Functions
function getUserBalance($uid) {
    $user = db()->getUser($uid);
    return $user ? floatval($user['balance'] ?? 0) : 0;
}

function getUserReferralBalance($uid) {
    $user = db()->getUser($uid);
    return $user ? floatval($user['referral_balance'] ?? 0) : 0;
}

function addBalance($uid, $amount) {
    $user = db()->getUser($uid);
    if (!$user) return false;
    $newBalance = floatval($user['balance']) + $amount;
    return db()->updateUser($uid, ['balance' => $newBalance]);
}

function addReferralBalance($uid, $amount) {
    $user = db()->getUser($uid);
    if (!$user) return false;
    $newBalance = floatval($user['referral_balance']) + $amount;
    return db()->updateUser($uid, ['referral_balance' => $newBalance]);
}

// Reward Functions
function awardAdReward($userId, $adType, $amount) {
    // Check cooldown
    if (db()->checkCooldown($userId) > 0) {
        return ['success' => false, 'message' => 'Cooldown period not passed'];
    }

    // Check daily limit
    $todayRewards = db()->getTodayAdRewards($userId);
    if (count($todayRewards) >= DAILY_AD_LIMIT) {
        return ['success' => false, 'message' => 'Daily limit reached'];
    }

    // Get IP and device fingerprint
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $deviceFingerprint = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $transactionId = bin2hex(random_bytes(16));

    // Check duplicate IP
    $ipCount = db()->checkDuplicateIP($ip, $adType);
    if ($ipCount > 5) {
        return ['success' => false, 'message' => 'Suspicious activity detected'];
    }

    // Create reward record
    $rewardData = [
        'user_id' => $userId,
        'ad_type' => $adType,
        'reward_amount' => $amount,
        'ip_address' => $ip,
        'device_fingerprint' => $deviceFingerprint,
        'transaction_id' => $transactionId,
        'status' => 'completed',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $result = db()->createAdReward($rewardData);
    if (!$result['success']) {
        return ['success' => false, 'message' => 'Failed to record reward'];
    }

    // Add balance
    $balanceResult = addBalance($userId, $amount);
    if (!$balanceResult['success']) {
        return ['success' => false, 'message' => 'Failed to update balance'];
    }

    // Record transaction
    db()->createTransaction([
        'user_id' => $userId,
        'amount' => $amount,
        'type' => 'ad_reward',
        'description' => ucfirst($adType) . ' ad reward',
        'status' => 'completed',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    return ['success' => true, 'message' => 'Reward credited', 'amount' => $amount, 'transaction_id' => $transactionId];
}

// Referral Functions
function generateReferralCode($length = 8) {
    return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
}

function processReferralReward($referrerId, $amount = REFERRAL_BONUS) {
    $result = addReferralBalance($referrerId, $amount);
    if ($result['success']) {
        db()->createTransaction([
            'user_id' => $referrerId,
            'amount' => $amount,
            'type' => 'referral_bonus',
            'description' => 'Referral reward',
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    return $result;
}

// Withdrawal Functions
function canWithdraw($uid) {
    $user = db()->getUser($uid);
    if (!$user) return ['can' => false, 'reason' => 'User not found'];
    if (($user['activation_status'] ?? 'inactive') !== 'active') {
        return ['can' => false, 'reason' => 'Account not activated'];
    }
    if (isset($user['is_banned']) && $user['is_banned']) {
        return ['can' => false, 'reason' => 'Account is banned'];
    }
    if (floatval($user['balance'] ?? 0) < MIN_WITHDRAWAL) {
        return ['can' => false, 'reason' => 'Minimum withdrawal is $' . number_format(MIN_WITHDRAWAL, 2)];
    }
    return ['can' => true, 'reason' => ''];
}

// Security Functions
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function rateLimitCheck($key, $maxAttempts = 5, $window = 60) {
    $rateKey = "rate_limit_$key";
    $attempts = $_SESSION[$rateKey]['attempts'] ?? 0;
    $lastAttempt = $_SESSION[$rateKey]['time'] ?? 0;

    if (time() - $lastAttempt > $window) {
        $_SESSION[$rateKey] = ['attempts' => 1, 'time' => time()];
        return true;
    }

    if ($attempts >= $maxAttempts) {
        return false;
    }

    $_SESSION[$rateKey]['attempts'] = $attempts + 1;
    return true;
}

function getDeviceFingerprint() {
    $components = [
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    return hash('sha256', implode('|', $components));
}

function logActivity($userId, $action, $details = '') {
    return db()->request('POST', '/rest/v1/activity_logs', [
        'user_id' => $userId,
        'action' => $action,
        'details' => is_string($details) ? $details : json_encode($details),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

// Format Functions
function formatAmount($amount) {
    return '$' . number_format(floatval($amount), 2);
}

function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}

// ============================================
// Gamification Functions (v2.0)
// ============================================

function getUserRank($xp) {
    $ranks = [
        ['name' => 'Bronze', 'level' => 1, 'min_xp' => 0, 'color' => '#cd7f32'],
        ['name' => 'Silver', 'level' => 2, 'min_xp' => 500, 'color' => '#c0c0c0'],
        ['name' => 'Gold', 'level' => 3, 'min_xp' => 2000, 'color' => '#ffd700'],
        ['name' => 'Diamond', 'level' => 4, 'min_xp' => 5000, 'color' => '#b9f2ff'],
        ['name' => 'Platinum', 'level' => 5, 'min_xp' => 10000, 'color' => '#e5e4e2'],
        ['name' => 'Elite', 'level' => 6, 'min_xp' => 25000, 'color' => '#ff6b35'],
        ['name' => 'Legend', 'level' => 7, 'min_xp' => 50000, 'color' => '#ffd700'],
    ];
    
    $currentRank = $ranks[0];
    foreach ($ranks as $rank) {
        if ($xp >= $rank['min_xp']) {
            $currentRank = $rank;
        }
    }
    return $currentRank;
}

function getNextRank($xp) {
    $ranks = [
        ['name' => 'Silver', 'level' => 2, 'min_xp' => 500],
        ['name' => 'Gold', 'level' => 3, 'min_xp' => 2000],
        ['name' => 'Diamond', 'level' => 4, 'min_xp' => 5000],
        ['name' => 'Platinum', 'level' => 5, 'min_xp' => 10000],
        ['name' => 'Elite', 'level' => 6, 'min_xp' => 25000],
        ['name' => 'Legend', 'level' => 7, 'min_xp' => 50000],
    ];
    
    foreach ($ranks as $rank) {
        if ($xp < $rank['min_xp']) {
            return $rank;
        }
    }
    return null;
}

function awardXP($uid, $xpAmount) {
    $user = db()->getUser($uid);
    if (!$user) return false;
    
    $currentXp = intval($user['xp'] ?? 0);
    $newXp = $currentXp + $xpAmount;
    
    // Calculate level (every 100 XP = 1 level)
    $newLevel = floor($newXp / 100) + 1;
    
    return db()->updateUser($uid, ['xp' => $newXp, 'level' => $newLevel]);
}

function checkAchievements($uid) {
    $user = db()->getUser($uid);
    if (!$user) return [];
    
    $unlocked = [];
    $achievements = [
        ['name' => 'First Steps', 'type' => 'ads_total', 'value' => 1, 'xp' => 50, 'coins' => 0.01, 'icon' => '👣'],
        ['name' => 'Getting Started', 'type' => 'ads_total', 'value' => 100, 'xp' => 200, 'coins' => 0.10, 'icon' => '🚀'],
        ['name' => 'Ad Enthusiast', 'type' => 'ads_total', 'value' => 1000, 'xp' => 1000, 'coins' => 1.00, 'icon' => '💎'],
        ['name' => 'Social Starter', 'type' => 'referrals_total', 'value' => 1, 'xp' => 100, 'coins' => 0.05, 'icon' => '🌟'],
        ['name' => 'Network Builder', 'type' => 'referrals_total', 'value' => 10, 'xp' => 500, 'coins' => 0.50, 'icon' => '🌐'],
        ['name' => 'Earning Newbie', 'type' => 'earnings_total', 'value' => 1, 'xp' => 50, 'coins' => 0.05, 'icon' => '💰'],
        ['name' => 'Earning Pro', 'type' => 'earnings_total', 'value' => 100, 'xp' => 1000, 'coins' => 1.00, 'icon' => '💵'],
    ];
    
    $userAchievements = json_decode($user['achievements'] ?? '[]', true);
    $earnedNames = array_column($userAchievements, 'name');
    
    // Get stats
    $adsResult = db()->request('GET', "/rest/v1/ad_rewards?user_id=eq.$uid&select=id");
    $totalAds = $adsResult['success'] ? count($adsResult['data'] ?? []) : 0;
    
    $refs = db()->getReferrals($uid);
    $totalRefs = count($refs);
    
    $earningsTotal = floatval($user['balance'] ?? 0) + floatval($user['referral_balance'] ?? 0);
    
    foreach ($achievements as $ach) {
        if (in_array($ach['name'], $earnedNames)) continue;
        
        $met = false;
        switch ($ach['type']) {
            case 'ads_total': $met = $totalAds >= $ach['value']; break;
            case 'referrals_total': $met = $totalRefs >= $ach['value']; break;
            case 'earnings_total': $met = $earningsTotal >= $ach['value']; break;
        }
        
        if ($met) {
            // Award the achievement
            $userAchievements[] = [
                'name' => $ach['name'],
                'icon' => $ach['icon'],
                'earned_at' => date('Y-m-d H:i:s'),
                'xp' => $ach['xp'],
                'coins' => $ach['coins']
            ];
            
            // Award XP and coins
            awardXP($uid, $ach['xp']);
            addBalance($uid, $ach['coins']);
            
            $unlocked[] = $ach;
        }
    }
    
    if (!empty($unlocked)) {
        db()->updateUser($uid, ['achievements' => json_encode($userAchievements)]);
    }
    
    return $unlocked;
}

function updateMissionProgress($uid, $type, $increment = 1) {
    $today = date('Y-m-d');
    
    // Get active missions matching this type
    $missionsResult = db()->request('GET', "/rest/v1/missions?type=eq.$type&is_daily=eq.true&is_active=eq.true&select=*");
    $missions = $missionsResult['success'] ? ($missionsResult['data'] ?? []) : [];
    
    foreach ($missions as $mission) {
        $mid = $mission['id'];
        
        // Get or create user mission progress
        $progressResult = db()->request('GET', "/rest/v1/user_missions?user_id=eq.$uid&mission_id=eq.$mid&date_assigned=eq.$today&select=*");
        $progressData = $progressResult['success'] ? ($progressResult['data'] ?? []) : [];
        
        if (empty($progressData)) {
            // Create new progress
            db()->request('POST', '/rest/v1/user_missions', [
                'user_id' => $uid,
                'mission_id' => $mid,
                'progress' => $increment,
                'completed' => $increment >= $mission['requirement'],
                'completed_at' => $increment >= $mission['requirement'] ? date('Y-m-d H:i:s') : null,
                'date_assigned' => $today
            ]);
            
            // Check if completed
            if ($increment >= $mission['requirement']) {
                completeMission($uid, $mid, $mission);
            }
        } else {
            $existing = $progressData[0];
            $newProgress = intval($existing['progress']) + $increment;
            $isComplete = $newProgress >= $mission['requirement'];
            
            db()->request('PATCH', "/rest/v1/user_missions?id=eq." . $existing['id'], [
                'progress' => $newProgress,
                'completed' => $isComplete,
                'completed_at' => $isComplete ? date('Y-m-d H:i:s') : null
            ]);
            
            if ($isComplete && !$existing['completed']) {
                completeMission($uid, $mid, $mission);
            }
        }
    }
}

function completeMission($uid, $missionId, $missionData) {
    // Award XP
    awardXP($uid, $missionData['xp_reward'] ?? 50);
    
    // Award coins
    $coinReward = floatval($missionData['coin_reward'] ?? 0);
    if ($coinReward > 0) {
        addBalance($uid, $coinReward);
    }
    
    // Log transaction
    db()->createTransaction([
        'user_id' => $uid,
        'amount' => $coinReward,
        'type' => 'mission_reward',
        'description' => 'Completed mission: ' . ($missionData['title'] ?? 'Daily Mission'),
        'status' => 'completed',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Increment total missions completed
    $user = db()->getUser($uid);
    $totalMissions = intval($user['total_missions_completed'] ?? 0) + 1;
    db()->updateUser($uid, ['total_missions_completed' => $totalMissions]);
}

function claimDailyReward($uid) {
    $today = date('Y-m-d');
    $user = db()->getUser($uid);
    if (!$user) return ['success' => false, 'message' => 'User not found'];
    
    // Check if already claimed today
    $lastDaily = $user['last_daily_date'] ?? null;
    if ($lastDaily && date('Y-m-d', strtotime($lastDaily)) === $today) {
        return ['success' => false, 'message' => 'Already claimed today'];
    }
    
    // Update streak
    $streak = intval($user['streak_days'] ?? 0);
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    if ($lastDaily && date('Y-m-d', strtotime($lastDaily)) === $yesterday) {
        $streak++;
    } else {
        $streak = 1;
    }
    
    // Calculate reward based on streak (up to 7)
    $dayNumber = min($streak, 7);
    $dailyRewards = [
        1 => ['xp' => 50, 'coins' => 0.01],
        2 => ['xp' => 75, 'coins' => 0.02],
        3 => ['xp' => 100, 'coins' => 0.03],
        4 => ['xp' => 150, 'coins' => 0.05],
        5 => ['xp' => 200, 'coins' => 0.08],
        6 => ['xp' => 250, 'coins' => 0.10],
        7 => ['xp' => 500, 'coins' => 0.25],
    ];
    
    $reward = $dailyRewards[$dayNumber] ?? $dailyRewards[1];
    
    // Apply rank multiplier
    $rank = getUserRank(intval($user['xp'] ?? 0));
    $multiplier = 1.0 + (($rank['level'] - 1) * 0.1);
    $reward['coins'] = round($reward['coins'] * $multiplier, 4);
    $reward['xp'] = round($reward['xp'] * $multiplier);
    
    // Award rewards
    db()->updateUser($uid, [
        'streak_days' => $streak,
        'last_daily_date' => date('Y-m-d H:i:s')
    ]);
    
    awardXP($uid, $reward['xp']);
    addBalance($uid, $reward['coins']);
    
    db()->createTransaction([
        'user_id' => $uid,
        'amount' => $reward['coins'],
        'type' => 'daily_reward',
        'description' => "Day $dayNumber daily reward (Streak: $streak)",
        'status' => 'completed',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    return [
        'success' => true,
        'streak' => $streak,
        'day' => $dayNumber,
        'xp' => $reward['xp'],
        'coins' => $reward['coins'],
        'multiplier' => $multiplier
    ];
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M d, Y', $time);
}

// JSON Response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Error Handler
function handleError($message, $statusCode = 400) {
    jsonResponse(['success' => false, 'message' => $message], $statusCode);
}
