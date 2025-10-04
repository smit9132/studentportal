<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) exit('Access denied. Admins only.');

$pdo = getPDO();
$errors = [];
$success = '';

// Get student ID
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM students WHERE student_id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) exit('Student not found.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = intval($_POST['year'] ?? 0);

    if ($name === '' || $email === '' || $course === '' || $year <= 0) {
        $errors[] = 'All fields are required and year must be positive.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';

    if (empty($errors)) {
        // Check if email exists for another student
        $stmt = $pdo->prepare('SELECT student_id FROM students WHERE email = ? AND student_id != ?');
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $errors[] = 'Another student with this email already exists.';
        } else {
            $stmt = $pdo->prepare('UPDATE students SET name=?, email=?, course=?, year=? WHERE student_id=?');
            if ($stmt->execute([$name, $email, $course, $year, $id])) {
                $success = 'Student updated successfully!';
                // Refresh student data
                $stmt = $pdo->prepare('SELECT * FROM students WHERE student_id = ?');
                $stmt->execute([$id]);
                $student = $stmt->fetch();
            } else {
                $errors[] = 'Failed to update student.';
            }
        }
    }
}

require_once __DIR__ . '/../inc/header.php';
?>

<h2>Edit Student</h2>

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
        <label class="form-label">Name</label>
        <input name="name" class="form-control" value="<?= e($student['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="<?= e($student['email']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Course</label>
        <input name="course" class="form-control" value="<?= e($student['course']) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Year</label>
        <input name="year" type="number" class="form-control" value="<?= e($student['year']) ?>" min="1" required>
    </div>
    <button class="btn btn-primary">Update Student</button>
</form>

<p><a href="../dashboard.php">Back to Dashboard</a></p>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
