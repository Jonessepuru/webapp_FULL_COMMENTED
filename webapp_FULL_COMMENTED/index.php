<?php
require 'auth.php';
require 'db.php';
require_admin(); // ENFORCE ACCESS CONTROL

// READ operation - using query (no user input, safe)
$users = $pdo->query("SELECT id, name, email, age FROM users ORDER BY id DESC")->fetchAll();
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Secure User Management</title>
<style>
    input:invalid { border: 2px solid red; }
    table { border-collapse: collapse; margin-top:20px; }
    td, th { padding: 8px; }
</style>
</head>
<body>
<h1>Secure CRUD Demo</h1>
<p>Logged in as: <?= htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8') ?> | <a href="logout.php">Logout</a></p>

<h2>Create User (Client + Server Validation)</h2>
<!-- CLIENT-SIDE VALIDATION: HTML5 attributes for UX only -->
<form id="userForm" method="POST" action="save.php" novalidate>
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    
    <label>Name (letters only):
        <input type="text" name="name" id="name" required minlength="2" maxlength="50"
               pattern="[A-Za-z\s'-]{2,50}"
               title="2-50 letters, spaces, apostrophes">
    </label><br>
    
    <label>Email:
        <input type="email" name="email" id="email" required maxlength="100">
    </label><br>
    
    <label>Age (18-120):
        <input type="number" name="age" id="age" required min="18" max="120">
    </label><br>
    
    <button type="submit">Save</button>
</form>

<h2>Current Users</h2>
<table border="1">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Age</th><th>Actions</th></tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= (int)$u['id'] ?></td>
    <!-- XSS DEFENSE: htmlspecialchars on EVERY output -->
    <td><?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></td>
    <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
    <td><?= (int)$u['age'] ?></td>
    <td>
        <a href="edit.php?id=<?= (int)$u['id'] ?>">Edit</a> |
        <!-- DELETE uses POST + CSRF, not GET -->
        <form method="POST" action="delete.php" style="display:inline" onsubmit="return confirm('Delete this user?');">
            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">
            <button type="submit">Delete</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<script>
// CLIENT-SIDE VALIDATION: JavaScript for immediate feedback
// NOTE: This can be bypassed - server must re-validate
document.getElementById('userForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const age = Number(document.getElementById('age').value);
    
    const nameOk = /^[A-Za-z\s'-]{2,50}$/.test(name);
    const emailOk = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(email);
    const ageOk = age >= 18 && age <= 120;
    
    if (!nameOk || !emailOk || !ageOk) {
        e.preventDefault();
        alert('Please correct the form errors. Name: letters only, valid email, age 18-120');
    }
});
</script>
</body>
</html>
