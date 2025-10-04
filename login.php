<?php
require_once __DIR__ . '/db/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // If user not found and username is 'admin', attempt one-time auto-create using the seeded hash
        if (!$user && $username === 'admin') {
            // The schema.sql includes a seeded admin hash; try to insert admin row if not present.
            // NOTE: This is a development convenience. Remove this block after first successful login.
            $seededHash = '$2y$10$u8K1s9dFh3pQmL2zVxY4Ou6q7w8e9r0tABCDefghijkLMNOPQRstu';
            try {
                $ins = $pdo->prepare('INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)');
                $ins->execute(['admin','admin@example.com',$seededHash,'admin','active']);
                // re-fetch the user
                $stmt->execute([$username]);
                $user = $stmt->fetch();
            } catch (Exception $e) {
                // ignore duplicate key errors etc.
            }
        }

        if ($user && password_verify($password, $user['password'])) {
            // Save session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | StudentPortal</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?= e($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
