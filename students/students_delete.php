<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) exit('Access denied. Admins only.');

$pdo = getPDO();
$id = intval($_GET['id'] ?? 0);

// Optional: fetch student to confirm deletion
$stmt = $pdo->prepare('SELECT * FROM students WHERE student_id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) exit('Student not found.');

// Delete student
$stmt = $pdo->prepare('DELETE FROM students WHERE student_id = ?');
$stmt->execute([$id]);

header('Location: ../dashboard.php?msg=student_deleted');
exit;
