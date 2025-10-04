<?php
require_once __DIR__ . '/../db/db_connect.php';

// Require admin access
requireLogin();
if (!isAdmin()) exit('Access denied. Admins only.');

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status = trim($_POST['status'] ?? 'upcoming'); // default status

    // Validation
    if ($title === '' || $date === '' || $location === '') {
        $errors[] = 'All fields are required.';
    }
    if (!in_array($status, ['upcoming','completed','cancelled'])) {
        $status = 'upcoming';
    }

    if (empty($errors)) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('INSERT INTO events (title, date, location, status) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$title, $date, $location, $status])) {
            $success = 'Event added successfully!';
        } else {
            $errors[] = 'Failed to add event. Try again.';
        }
    }
}

require_once __DIR__ . '/../inc/header.php';
?>

<h2>Add New Event</h2>

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
        <input name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input name="date" type="date" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Location</label>
        <input name="location" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="upcoming">Upcoming</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <button class="btn btn-primary">Add Event</button>
</form>

<p><a href="../dashboard.php">Back to Dashboard</a></p>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
