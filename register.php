<?php
require_once __DIR__ . '/db/db_connect.php';

// Require login and admin access
requireLogin();
if (!isAdmin()) {
    exit('Access denied. Admins only.');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user'; // default to 'user'

    // Validation
    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if (!in_array($role, ['user', 'admin'])) {
        $role = 'user';
    }

    if (empty($errors)) {
        $pdo = getPDO();
        // Check unique username/email
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE username = :u OR email = :e');
        $stmt->execute([':u' => $username, ':e' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (username,email,password,role,status) VALUES (:u,:e,:p,:r,:s)');
            $ins->execute([
                ':u' => $username,
                ':e' => $email,
                ':p' => $hash,
                ':r' => $role,
                ':s' => 'active'
            ]);
            $success = 'User registered successfully!';
        }
    }
}

require_once __DIR__ . '/inc/header.php';
?>

<h2>Register New User</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $err) echo '<li>' . e($err) . '</li>'; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input name="confirm_password" type="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-control">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <button class="btn btn-primary">Register</button>
</form>

<p><a href="dashboard.php">Back to Dashboard</a></p>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
