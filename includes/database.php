<?php
// EARNNOVA - Supabase Database Handler
require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $supabaseUrl;
    private $supabaseKey;

    private function __construct() {
        $this->supabaseUrl = SUPABASE_URL;
        $this->supabaseKey = SUPABASE_ANON_KEY;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function request($method, $endpoint, $data = null) {
        $url = rtrim($this->supabaseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $this->supabaseKey,
                'Authorization: Bearer ' . $this->supabaseKey,
                'Content-Type: application/json',
                'Prefer: return=minimal'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($data !== null && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("Supabase Error: $error");
            return ['error' => $error, 'success' => false];
        }

        $decoded = json_decode($response, true);
        
        if ($httpCode >= 400) {
            error_log("Supabase HTTP $httpCode: " . print_r($decoded, true));
            return ['error' => $decoded['error'] ?? 'Request failed', 'success' => false, 'http_code' => $httpCode];
        }

        return ['success' => true, 'data' => $decoded, 'http_code' => $httpCode];
    }

    // User Methods
    public function getUser($uid) {
        $result = $this->request('GET', "/rest/v1/users?uid=eq.$uid&select=*");
        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }
        return null;
    }

    public function getUserByEmail($email) {
        $result = $this->request('GET', "/rest/v1/users?email=eq.$email&select=*");
        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }
        return null;
    }

    public function getUserByUsername($username) {
        $result = $this->request('GET', "/rest/v1/users?username=eq.$username&select=*");
        if ($result['success'] && !empty($result['data'])) {
            return $result['data'][0];
        }
        return null;
    }

    public function createUser($data) {
        return $this->request('POST', '/rest/v1/users', $data);
    }

    public function updateUser($uid, $data) {
        return $this->request('PATCH', "/rest/v1/users?uid=eq.$uid", $data);
    }

    // Ad Rewards
    public function getAdRewards($userId, $limit = 50) {
        $result = $this->request('GET', "/rest/v1/ad_rewards?user_id=eq.$userId&order=created_at.desc&limit=$limit");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function getTodayAdRewards($userId) {
        $today = date('Y-m-d');
        $result = $this->request('GET', "/rest/v1/ad_rewards?user_id=eq.$userId&created_at=gte.$today&select=*");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function createAdReward($data) {
        return $this->request('POST', '/rest/v1/ad_rewards', $data);
    }

    // Referrals
    public function getReferrals($userId) {
        $result = $this->request('GET', "/rest/v1/referrals?referrer_id=eq.$userId&select=*");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function createReferral($data) {
        return $this->request('POST', '/rest/v1/referrals', $data);
    }

    // Withdrawals
    public function getWithdrawals($userId) {
        $result = $this->request('GET', "/rest/v1/withdrawals?user_id=eq.$userId&order=created_at.desc");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function createWithdrawal($data) {
        return $this->request('POST', '/rest/v1/withdrawals', $data);
    }

    public function updateWithdrawal($id, $data) {
        return $this->request('PATCH', "/rest/v1/withdrawals?id=eq.$id", $data);
    }

    // Transactions
    public function getTransactions($userId) {
        $result = $this->request('GET', "/rest/v1/transactions?user_id=eq.$userId&order=created_at.desc&limit=100");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function createTransaction($data) {
        return $this->request('POST', '/rest/v1/transactions', $data);
    }

    // Plans
    public function getPlans() {
        $result = $this->request('GET', '/rest/v1/plans?select=*');
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    // Activation Logs
    public function createActivationLog($data) {
        return $this->request('POST', '/rest/v1/activation_logs', $data);
    }

    // Admin Methods
    public function getAllUsers() {
        $result = $this->request('GET', '/rest/v1/users?select=*&order=created_at.desc');
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function getAllWithdrawals() {
        $result = $this->request('GET', '/rest/v1/withdrawals?select=*&order=created_at.desc');
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function getPendingWithdrawals() {
        $result = $this->request('GET', "/rest/v1/withdrawals?status=eq.pending&select=*");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    // Device Fingerprinting
    public function getDeviceHistory($userId) {
        $result = $this->request('GET', "/rest/v1/device_logs?user_id=eq.$userId&select=*");
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function logDevice($data) {
        return $this->request('POST', '/rest/v1/device_logs', $data);
    }

    // Settings
    public function getSettings() {
        $result = $this->request('GET', '/rest/v1/settings?select=*');
        return $result['success'] ? ($result['data'] ?? []) : [];
    }

    public function updateSetting($key, $value) {
        return $this->request('PATCH', "/rest/v1/settings?key=eq.$key", ['value' => $value]);
    }

    // Fraud Detection
    public function checkDuplicateIP($ip, $type) {
        $result = $this->request('GET', "/rest/v1/ad_rewards?ip_address=eq.$ip&ad_type=eq.$type&created_at=gte." . date('Y-m-d H:i:s', strtotime('-1 hour')));
        return $result['success'] ? count($result['data'] ?? []) : 0;
    }

    public function checkCooldown($userId) {
        $cooldown = AD_COOLDOWN;
        $timeThreshold = date('Y-m-d H:i:s', time() - $cooldown);
        $result = $this->request('GET', "/rest/v1/ad_rewards?user_id=eq.$userId&created_at=gt.$timeThreshold&select=id");
        return $result['success'] ? count($result['data'] ?? []) : 0;
    }

    // Analytics
    public function getTotalUsers() {
        $result = $this->request('GET', '/rest/v1/users?select=id');
        return $result['success'] ? count($result['data'] ?? []) : 0;
    }

    public function getActiveUsers() {
        $result = $this->request('GET', "/rest/v1/users?status=eq.active&select=id");
        return $result['success'] ? count($result['data'] ?? []) : 0;
    }

    public function getTotalEarnings() {
        $result = $this->request('GET', '/rest/v1/users?select=balance');
        if ($result['success'] && !empty($result['data'])) {
            return array_sum(array_column($result['data'], 'balance'));
        }
        return 0;
    }

    public function getTotalWithdrawals() {
        $result = $this->request('GET', '/rest/v1/withdrawals?select=amount');
        if ($result['success'] && !empty($result['data'])) {
            return array_sum(array_column($result['data'], 'amount'));
        }
        return 0;
    }

    public function getTodayRegistrations() {
        $today = date('Y-m-d');
        $result = $this->request('GET', "/rest/v1/users?created_at=gte.$today&select=id");
        return $result['success'] ? count($result['data'] ?? []) : 0;
    }
}

// Helper function
function db() {
    return Database::getInstance();
}
