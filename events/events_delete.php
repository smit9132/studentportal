<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) exit('Access denied. Admins only.');

$pdo = getPDO();
$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM events WHERE event_id = ?');
$stmt->execute([$id]);
$event = $stmt->fetch();
if (!$event) exit('Event not found.');

// Delete event
$stmt = $pdo->prepare('DELETE FROM events WHERE event_id = ?');
$stmt->execute([$id]);

header('Location: ../dashboard.php?msg=event_deleted');
exit;
