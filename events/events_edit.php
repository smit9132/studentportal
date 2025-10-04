<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) exit('Access denied. Admins only.');

$pdo = getPDO();
$errors = [];
$success = '';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) exit('Event not found.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status = trim($_POST['status'] ?? 'upcoming');

    if ($title === '' || $date === '' || $location === '') $errors[] = 'All fields are required.';
    if (!in_array($status, ['upcoming','completed','cancelled'])) $status = 'upcoming';

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE events SET title=?, date=?, location=?, status=? WHERE event_id=?');
        if ($stmt->execute([$title, $date, $location, $status, $id])) {
            $success = 'Event updated successfully!';
            $stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
            $stmt->execute([$id]);
            $event = $stmt->fetch();
        } else {
            $errors[] = 'Failed to update event.';
        }
    }
}

require_once __DIR__ . '/../inc/header.php';
?>

<h2>Edit Event</h2>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($errors as $err) echo '<li>' . e($err) . '</li>'; ?></ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" class="form-control" value="<?= e($event['title']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input name="date" type="date" class="form-control" value="<?= e($event['date']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Location</label>
        <input name="location" class="form-control" value="<?= e($event['location']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="upcoming" <?= $event['status']=='upcoming'?'selected':'' ?>>Upcoming</option>
            <option value="completed" <?= $event['status']=='completed'?'selected':'' ?>>Completed</option>
            <option value="cancelled" <?= $event['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
        </select>
    </div>
    <button class="btn btn-primary">Update Event</button>
</form>

<p><a href="../dashboard.php">Back to Dashboard</a></p>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
