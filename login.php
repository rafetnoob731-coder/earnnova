<?php
// EARNNOVA - Login (Backend only — redirects to static HTML)
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

// Redirect to static HTML page
header('Location: /login.html');
exit;
