<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
$pdo = getPDO();

$search = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT * FROM events';
if ($search !== '') {
    $sql .= ' WHERE title LIKE :q OR location LIKE :q';
    $params[':q'] = '%' . $search . '%';
}
$sql .= ' ORDER BY date DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

require_once __DIR__ . '/../inc/header.php';
?>

<div class="d-flex justify-content-between align-items-center">
  <h2>Events</h2>
  <?php if (isAdmin()): ?><a href="add.php" class="btn btn-success">Add Event</a><?php endif; ?>
</div>

<form method="get" class="row g-2 mt-2">
  <div class="col-auto"><input name="q" value="<?= e($search) ?>" class="form-control" placeholder="Search by title or location"></div>
  <div class="col-auto"><button class="btn btn-outline-primary">Search</button></div>
</form>

<?php if (!empty($_SESSION['flash'])): ?><div class="alert alert-info mt-2"><?= e($_SESSION['flash']); unset($_SESSION['flash']); ?></div><?php endif; ?>

<table class="table mt-3">
  <thead><tr><th>ID</th><th>Title</th><th>Date</th><th>Location</th><th>Status</th><?php if (isAdmin()) echo '<th>Actions</th>'; ?></tr></thead>
  <tbody>
    <?php foreach ($events as $ev): ?>
      <tr>
        <td><?= e($ev['event_id']) ?></td>
        <td><?= e($ev['title']) ?></td>
        <td><?= e($ev['date']) ?></td>
        <td><?= e($ev['location']) ?></td>
        <td><?= e($ev['status']) ?></td>
        <?php if (isAdmin()): ?>
          <td>
            <a class="btn btn-sm btn-primary" href="edit.php?id=<?= e($ev['event_id']) ?>">Edit</a>
            <a class="btn btn-sm btn-warning" href="toggle.php?id=<?= e($ev['event_id']) ?>">Toggle</a>
            <a class="btn btn-sm btn-danger" href="delete.php?id=<?= e($ev['event_id']) ?>" onclick="return confirm('Delete event?')">Delete</a>
          </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
