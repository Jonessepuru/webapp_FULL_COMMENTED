<?php
require 'auth.php';
require 'db.php';
require_admin();

// Validate ID from URL - prevents injection and broken access
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Invalid user ID');
}

// Prepared statement for READ
$stmt = $pdo->prepare("SELECT id, name, email, age FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit('User not found');
}

$csrf = csrf_token();
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Edit User</title></head>
<body>
<h2>Edit User #<?= (int)$user['id'] ?></h2>
<form method="POST" action="update.php">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
    
    Name: <input type="text" name="name" 
           value="<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>" 
           required pattern="[A-Za-z\s'-]{2,50}"><br>
    
    Email: <input type="email" name="email" 
            value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" 
            required><br>
    
    Age: <input type="number" name="age" 
          value="<?= (int)$user['age'] ?>" 
          required min="18" max="120"><br>
    
    <button type="submit">Update</button>
    <a href="index.php">Cancel</a>
</form>
</body></html>
