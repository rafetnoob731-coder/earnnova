<?php
// EARNNOVA - Login (reads static HTML from login.html)
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

// Serve the static HTML login page
readfile(__DIR__ . '/login.html');
exit;
