<?php
/**
 * delete.php - DELETE operation
 * Uses POST + CSRF to prevent CSRF attacks
 */
require 'auth.php';
require 'db.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('Invalid request');
}

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Invalid ID');
}

// Prepared statement
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit;
