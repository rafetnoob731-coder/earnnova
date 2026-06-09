<?php
// EARNNOVA - Register (reads static HTML from register.html)
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}

// Serve the static HTML registration page
readfile(__DIR__ . '/register.html');
exit;
