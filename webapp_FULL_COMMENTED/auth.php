<?php
/**
 * auth.php - Authentication and Authorization helpers
 * Defends against Broken Access Control (OWASP Top 10)
 */
session_start();

// Enforce session cookie security (set in php.ini ideally)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

function require_login() {
    // BROKEN ACCESS CONTROL: Check authentication on EVERY protected page
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    require_login();
    // Authorization check - never trust client-side role
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Access denied - admin privileges required');
    }
}

function csrf_token() {
    // CSRF protection - unique token per session
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf($token) {
    // Prevent CSRF attacks on state-changing requests
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
