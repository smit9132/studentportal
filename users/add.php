<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) { header('Location: /StudentPortal/dashboard.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user';

    if ($username === '' || $email === '' || $password === '') $errors[] = 'All fields required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
    if (empty($errors)) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT user_id FROM users WHERE username = :u OR email = :e');
        $stmt->execute([':u' => $username, ':e' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (username,email,password,role,status) VALUES (:u,:e,:p,:r,:s)');
            $ins->execute([':u'=>$username,':e'=>$email,':p'=>$hash,':r'=>$role,':s'=>'active']);
            $_SESSION['flash'] = 'User added.';
            header('Location: /StudentPortal/users/index.php'); exit;
        }
    }
}

require_once __DIR__ . '/../inc/header.php';
?>

<h2>Add User</h2>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo e($e) . '<br>'; ?></div><?php endif; ?>

<form method="post" data-validate="true">
  <div class="mb-3"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Password</label><input name="password" type="password" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Role</label>
    <select name="role" class="form-select"><option value="user">User</option><option value="admin">Admin</option></select>
  </div>
  <button class="btn btn-success">Create</button>
</form>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
