<?php
require 'db.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // SERVER-SIDE VALIDATION: trim whitespace
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // PREPARED STATEMENT prevents SQLi even on login
    $stmt = $pdo->prepare("SELECT id, password_hash, role FROM app_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Prevent session fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
        // Log failed attempts (do not reveal which part failed)
        error_log("Failed login for username: $username");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Login - Secure CRUD</title></head>
<body>
<h2>Login</h2>
<p>Demo: admin / admin123</p>
<?php if ($error): ?>
    <!-- XSS PROTECTION: htmlspecialchars on output -->
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<form method="POST" autocomplete="off">
    <label>Username: <input name="username" required maxlength="30"></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>
</body>
</html>
