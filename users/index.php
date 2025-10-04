<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) { header('Location: /StudentPortal/dashboard.php'); exit; }

$pdo = getPDO();
$q = $pdo->query('SELECT user_id, username, email, role, status, created_at FROM users ORDER BY user_id DESC');
$users = $q->fetchAll();

require_once __DIR__ . '/../inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center">
  <h2>Users</h2>
  <a href="add.php" class="btn btn-success">Add User</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-info"><?= e($_SESSION['flash']); unset($_SESSION['flash']); ?></div>
<?php endif; ?>

<table class="table table-striped">
  <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= e($u['user_id']) ?></td>
        <td><?= e($u['username']) ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= e($u['role']) ?></td>
        <td><?= e($u['status']) ?></td>
        <td class="table-actions">
          <a class="btn btn-sm btn-primary" href="edit.php?id=<?= e($u['user_id']) ?>">Edit</a>
          <a class="btn btn-sm btn-warning" href="toggle.php?id=<?= e($u['user_id']) ?>">Toggle Status</a>
          <a class="btn btn-sm btn-danger" href="delete.php?id=<?= e($u['user_id']) ?>" onclick="return confirm('Delete user?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
