<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
$pdo = getPDO();

if (isAdmin()) {
    $stmt = $pdo->query('SELECT s.student_id, s.name, s.email, s.course, s.year, u.username FROM students s JOIN users u ON s.user_id = u.user_id ORDER BY s.student_id DESC');
    $students = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare('SELECT student_id, name, email, course, year FROM students WHERE user_id = :uid');
    $stmt->execute([':uid' => $_SESSION['user_id']]);
    $students = $stmt->fetchAll();
}

require_once __DIR__ . '/../inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center">
  <h2>Students</h2>
  <a href="add.php" class="btn btn-success">Add / Edit Profile</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-info"><?= e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>

<table class="table">
  <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><?php if (isAdmin()) echo '<th>User</th>'; ?><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($students as $s): ?>
      <tr>
        <td><?= e($s['student_id']) ?></td>
        <td><?= e($s['name']) ?></td>
        <td><?= e($s['email']) ?></td>
        <td><?= e($s['course']) ?></td>
        <td><?= e($s['year']) ?></td>
        <?php if (isAdmin()): ?><td><?= e($s['username']) ?></td><?php endif; ?>
        <td>
          <a class="btn btn-sm btn-primary" href="edit.php?id=<?= e($s['student_id']) ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="delete.php?id=<?= e($s['student_id']) ?>" onclick="return confirm('Delete record?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
