<?php
/**
 * save.php - CREATE operation
 * Demonstrates: server validation, prepared statements, CSRF, access control
 */
require 'auth.php';
require 'db.php';
require_admin();

// Verify request method and CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('Invalid request');
}

// SERVER-SIDE VALIDATION - NEVER TRUST CLIENT
$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$age   = $_POST['age'] ?? '';

$errors = [];

// Allow-list validation (whitelisting) - OWASP recommended
if (!preg_match("/^[A-Za-z\s'-]{2,50}$/", $name)) {
    $errors[] = 'Name must be 2-50 letters';
}

// filter_var for email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $errors[] = 'Invalid email format';
}

// Integer validation with range
if (!filter_var($age, FILTER_VALIDATE_INT, ['options' => ['min_range' => 18, 'max_range' => 120]])) {
    $errors[] = 'Age must be 18-120';
}

if ($errors) {
    // Log validation failures for security monitoring
    error_log('CREATE validation failed: ' . implode(', ', $errors));
    http_response_code(400);
    // XSS protection on error output
    exit(implode('<br>', array_map(fn($e) => htmlspecialchars($e, ENT_QUOTES, 'UTF-8'), $errors)));
}

try {
    // SQL INJECTION PREVENTION: Prepared statement separates code from data
    $stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, (int)$age]);
    
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    error_log('CREATE DB error: ' . $e->getMessage());
    // Don't expose DB errors to user
    exit('Error saving user - email may already exist');
}
