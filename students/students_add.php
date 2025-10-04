<?php
require_once __DIR__ . '/../db/db_connect.php';

// Require login and admin access
requireLogin();
if (!isAdmin()) {
    exit('Access denied. Admins only.');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = intval($_POST['year'] ?? 0);

    // Validation
    if ($name === '' || $email === '' || $course === '' || $year <= 0) {
        $errors[] = 'All fields are required and year must be positive.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    if (empty($errors)) {
        $pdo = getPDO();

        // Optional: Check if email already exists
        $stmt = $pdo->prepare('SELECT student_id FROM students WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Student with this email already exists.';
        } else {
            // Associate the student record with the currently logged in user (admin)
            $user_id = $_SESSION['user_id'] ?? null;
            if ($user_id === null) {
                $errors[] = 'Unable to determine current user. Please login again.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO students (user_id, name, email, course, year) VALUES (?, ?, ?, ?, ?)');
                if ($stmt->execute([$user_id, $name, $email, $course, $year])) {
                    $success = 'Student added successfully!';
                } else {
                    $errors[] = 'Failed to add student. Try again.';
                }
            }
        }
    }
}

require_once __DIR__ . '/../inc/header.php';
?>

<h2>Add New Student</h2>

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
        <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Course</label>
        <input name="course" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Year</label>
        <input name="year" type="number" class="form-control" required min="1">
    </div>
    <button class="btn btn-primary">Add Student</button>
</form>

<p><a href="../dashboard.php">Back to Dashboard</a></p>

<?php require_once __DIR__ . '/../inc/footer.php'; ?>
