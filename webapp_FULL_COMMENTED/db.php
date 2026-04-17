<?php
/**
 * db.php - Central database connection
 * SECURITY: Uses PDO with real prepared statements to prevent SQL Injection
 */
$host = 'localhost';
$db   = 'webapp';
$user = 'root';      // XAMPP default
$pass = '';          // change in production
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions, don't leak details
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // CRITICAL: forces real prepared statements (SQLi defense)
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Never show $e->getMessage() to user - log only
    error_log('DB Connection failed: '.$e->getMessage());
    http_response_code(500);
    exit('Database error. Contact administrator.');
}
