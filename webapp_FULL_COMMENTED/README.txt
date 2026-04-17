SECURE PHP CRUD - FULLY COMMENTED VERSION
============================================

FOR LAB ASSESSMENT - PROTECTIONS DEMONSTRATED:

1. CLIENT-SIDE VALIDATION (index.php, edit.php)
   - HTML5: required, type="email", pattern, min/max
   - JavaScript regex for instant feedback
   - PURPOSE: UX only, easily bypassed

2. SERVER-SIDE VALIDATION (save.php, update.php)
   - trim() to remove whitespace
   - preg_match() allow-list for names
   - filter_var() for email and integers
   - Length and range checks
   - OWASP: "Validate all data on trusted system"

3. SQL INJECTION PREVENTION
   - db.php: PDO::ATTR_EMULATE_PREPARES = false
   - ALL queries use $pdo->prepare() + execute([$data])
   - No string concatenation in SQL

4. XSS PREVENTION
   - htmlspecialchars($output, ENT_QUOTES, 'UTF-8') everywhere
   - Input validation + output encoding = defense in depth

5. BROKEN ACCESS CONTROL PREVENTION
   - auth.php: require_admin() on every CRUD file
   - session_regenerate_id() on login
   - CSRF tokens on all state-changing forms
   - ID validation with FILTER_VALIDATE_INT
   - POST for delete (not GET)

SETUP:
1. Unzip to C:/xampp/htdocs/webapp
2. Import schema.sql
3. Browse to http://localhost/webapp/login.php
4. Login: admin / admin123

TEST FOR REPORT:
- Try XSS: <script>alert('xss')</script> as name -> shows as text
- Try SQLi: ' OR 1=1 -- as email -> blocked by prepared statement
- Try access control: open save.php directly without login -> redirected
