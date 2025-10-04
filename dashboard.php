<?php
require_once __DIR__ . '/db/db_connect.php';
requireLogin();
$timeout = 300;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

$pdo = getPDO();
$students = $pdo->query("SELECT * FROM students ORDER BY year, name")->fetchAll();
$events = $pdo->query("SELECT * FROM events ORDER BY date")->fetchAll();
$users = isAdmin() ? $pdo->query("SELECT user_id, username, email, role, status FROM users")->fetchAll() : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard | StudentPortal</title>
<link rel="stylesheet" href="css/style.css">
<style>
table { border-collapse: collapse; width: 90%; margin: 20px auto; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background-color: #f4f4f4; }
h2 { text-align: center; margin-top: 20px; }
.logout { text-align: center; margin-top: 20px; }
a.btn { text-decoration: none; padding: 4px 8px; background: #007BFF; color: white; border-radius: 4px; margin: 2px; }
a.btn.delete { background: #DC3545; }
</style>
</head>
<body>
<h2>Welcome, <?= e($_SESSION['username']) ?>!</h2>
<p style="text-align:center;">Role: <b><?= e($_SESSION['role']) ?></b></p>

<!-- Students Section -->
<h2>Students <?php if(isAdmin()): ?><a class="btn" href="students/students_add.php">Add Student</a><?php endif; ?></h2>
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><?php if(isAdmin()): ?><th>Actions</th><?php endif; ?></tr>
<?php foreach($students as $s): ?>
<tr>
<td><?= e($s['student_id']) ?></td>
<td><?= e($s['name']) ?></td>
<td><?= e($s['email']) ?></td>
<td><?= e($s['course']) ?></td>
<td><?= e($s['year']) ?></td>
<?php if(isAdmin()): ?>
<td>
<a class="btn" href="students/students_edit.php?id=<?= $s['student_id'] ?>">Edit</a>
<a class="btn delete" href="students/students_delete.php?id=<?= $s['student_id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
</td>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</table>

<!-- Events Section -->
<h2>Events <?php if(isAdmin()): ?><a class="btn" href="events/events_add.php">Add Event</a><?php endif; ?></h2>
<table>
<tr><th>ID</th><th>Title</th><th>Date</th><th>Location</th><th>Status</th><?php if(isAdmin()): ?><th>Actions</th><?php endif; ?></tr>
<?php foreach($events as $e): ?>
<tr>
<td><?= e($e['event_id']) ?></td>
<td><?= e($e['title']) ?></td>
<td><?= e($e['date']) ?></td>
<td><?= e($e['location']) ?></td>
<td><?= e($e['status']) ?></td>
<?php if(isAdmin()): ?>
<td>
<a class="btn" href="events/events_edit.php?id=<?= $e['event_id'] ?>">Edit</a>
<a class="btn delete" href="events/events_delete.php?id=<?= $e['event_id'] ?>" onclick="return confirm('Delete this event?')">Delete</a>
</td>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</table>

<!-- Admin Users Section -->
<?php if(isAdmin()): ?>
<h2>Manage Users</h2>
<table>
<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr>
<?php foreach($users as $u): ?>
<tr>
<td><?= e($u['user_id']) ?></td>
<td><?= e($u['username']) ?></td>
<td><?= e($u['email']) ?></td>
<td><?= e($u['role']) ?></td>
<td><?= e($u['status']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<div class="logout"><a href="logout.php">Logout</a></div>
</body>
</html>
