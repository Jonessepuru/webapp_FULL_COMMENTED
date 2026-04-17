<?php
/**
 * update.php - UPDATE operation
 */
require 'auth.php';
require 'db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('Invalid request');
}

$id    = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$age   = $_POST['age'] ?? '';

// Re-validate everything (defense in depth)
if (!$id || !preg_match("/^[A-Za-z\s'-]{2,50}$/", $name) ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !filter_var($age, FILTER_VALIDATE_INT, ['options' => ['min_range' => 18, 'max_range' => 120]])) {
    http_response_code(400);
    exit('Validation failed');
}

// Prepared statement prevents SQLi
$stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, age = ? WHERE id = ?");
$stmt->execute([$name, $email, (int)$age, $id]);

header('Location: index.php');
exit;
