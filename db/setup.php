<?php
// db/setup.php
// Small setup page to create or update an admin user.
// Security notes:
// - Intended for local/dev use. Remove this file after setup in production.
// - Uses prepared statements and password_hash with bcrypt.

session_start();

// Use Render environment variables for DB credentials
$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'studentportal';

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        $msg = 'CSRF token mismatch';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $msg = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Invalid email.';
        } else {
            $mysqli = new mysqli($host, $user, $pass, $db);
            if ($mysqli->connect_errno) {
                $msg = 'DB connect error: ' . $mysqli->connect_error;
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $mysqli->prepare(
                    "INSERT INTO users (username, email, password, role, status) 
                     VALUES (?, ?, ?, 'admin', 'active') 
                     ON DUPLICATE KEY UPDATE password=VALUES(password), role='admin', status='active'"
                );
                $stmt->bind_param('sss', $username, $email, $hash);
                if ($stmt->execute()) {
                    $msg = 'Admin user created/updated successfully.';
                } else {
                    $msg = 'DB error: ' . $stmt->error;
                }
                $stmt->close();
                $mysqli->close();
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>StudentPortal - Setup Admin</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>body{font-family:Arial,sans-serif;max-width:680px;margin:2rem auto;padding:1rem}label{display:block;margin-top:1rem}input{width:100%;padding:.5rem}</style>
</head>
<body>
    <h1>Create Admin User</h1>
    <p style="color:green"><?= htmlspecialchars($msg) ?></p>
    <form method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
        <label>Username
            <input name="username" required maxlength="100" value="<?= htmlspecialchars($_POST['username'] ?? 'admin') ?>">
        </label>
        <label>Email
            <input name="email" required maxlength="255" value="<?= htmlspecialchars($_POST['email'] ?? 'admin@example.com') ?>">
        </label>
        <label>Password
            <input name="password" type="password" required minlength="8">
        </label>
        <p><button type="submit">Create / Update Admin</button></p>
    </form>
    <p>After creating the admin, remove <code>db/setup.php</code> from the server to avoid an attack vector.</p>
</body>
</html>
