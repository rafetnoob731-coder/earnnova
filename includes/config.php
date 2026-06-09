<?php
// EARNNOVA - Main Configuration
// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false || $value === null) {
        return $default;
    }
    return $value;
}

// Site Configuration
define('SITE_NAME', env('SITE_NAME', 'EARNNOVA'));
define('SITE_URL', env('SITE_URL', 'http://localhost:8000'));
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'admin@earnnova.com'));

// Supabase Configuration
define('SUPABASE_URL', env('SUPABASE_URL', ''));
define('SUPABASE_ANON_KEY', env('SUPABASE_ANON_KEY', ''));
define('SUPABASE_SERVICE_ROLE', env('SUPABASE_SERVICE_ROLE', ''));

// Firebase Configuration
define('FIREBASE_API_KEY', env('FIREBASE_API_KEY', ''));
define('FIREBASE_AUTH_DOMAIN', env('FIREBASE_AUTH_DOMAIN', ''));
define('FIREBASE_PROJECT_ID', env('FIREBASE_PROJECT_ID', ''));
define('FIREBASE_STORAGE_BUCKET', env('FIREBASE_STORAGE_BUCKET', ''));
define('FIREBASE_MESSAGING_SENDER_ID', env('FIREBASE_MESSAGING_SENDER_ID', ''));
define('FIREBASE_APP_ID', env('FIREBASE_APP_ID', ''));

// PlatoBoost Configuration
define('PLATOBOOST_SERVICE_ID', env('PLATOBOOST_SERVICE_ID', '18576'));
define('PLATOBOOST_SECRET', env('PLATOBOOST_SECRET', ''));

// Security
define('JWT_SECRET', env('JWT_SECRET', 'change-this-secret-key'));
define('ENCRYPTION_KEY', env('ENCRYPTION_KEY', ''));

// Rate Limiting
define('RATE_LIMIT_PER_MINUTE', env('RATE_LIMIT_PER_MINUTE', 60));
define('RATE_LIMIT_PER_HOUR', env('RATE_LIMIT_PER_HOUR', 1000));

// Wallet/Withdrawal Settings
define('MIN_WITHDRAWAL', 1.00);
define('REFERRAL_BONUS', 0.10);
define('DAILY_AD_LIMIT', 20);
define('AD_COOLDOWN', 30); // seconds
define('DEFAULT_AD_REWARD', 0.01);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error Reporting
if (env('APP_ENV', 'development') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Timezone
date_default_timezone_set('UTC');

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
