<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) { header('Location: /StudentPortal/dashboard.php'); exit; }

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: /StudentPortal/users/index.php'); exit; }

$stmt = $pdo->prepare('SELECT user_id, username, email, role, status FROM users WHERE user_id = :id');
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();
if (!$user) { header('Location: /StudentPortal/users/index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '') $errors[] = 'Username and email required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
    if (empty($errors)) {
        // check uniqueness
        $check = $pdo->prepare('SELECT user_id FROM users WHERE (username = :u OR email = :e) AND user_id != :id');
        $check->execute([':u'=>$username,':e'=>$email,':id'=>$id]);
        if ($check->fetch()) {
            $errors[] = 'Username or email already in use.';
        } else {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $pdo->prepare('UPDATE users SET username = :u, email = :e, role = :r, password = :p WHERE user_id = :id');
                $upd->execute([':u'=>$username,':e'=>$email,':r'=>$role,':p'=>$hash,':id'=>$id]);
            } else {
                $upd = $pdo->prepare('UPDATE users SET username = :u, email = :e, role = :r WHERE user_id = :id');
                $upd->execute([':u'=>$username,':e'=>$email,':r'=>$role,':id'=>$id]);
            }
            $_SESSION['flash'] = 'User updated.';
            header('Location: /StudentPortal/users/index.php'); exit;
        }
    }
}

require_once __DIR__ . '/../inc/header.php';
?>

<h2>Edit User</h2>
<?php if ($errors): ?><div class="alert alert-danger"><?php foreach ($errors as $e) echo e($e) . '<br>'; ?></div><?php endif; ?>

<form method="post" data-validate="true">
  <div class="mb-3"><label class="form-label">Username</label><input name="username" value="<?= e($user['username']) ?>" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" value="<?= e($user['email']) ?>" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">New Password (leave blank to keep)</label><input name="password" type="password" class="form-control"></div>
  <div class="mb-3"><label class="form-label">Role</label>
    <select name="role" class="form-select"><option value="user" <?= $user['role']==='user' ? 'selected':'' ?>>User</option><option value="admin" <?= $user['role']==='admin' ? 'selected':'' ?>>Admin</option></select>
  </div>
  <button class="btn btn-primary">Save</button>
</form>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
