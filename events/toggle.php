<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) { header('Location: /StudentPortal/events/index.php'); exit; }
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT status FROM events WHERE event_id = :id');
    $stmt->execute([':id'=>$id]);
    $cur = $stmt->fetchColumn();
    // Toggle between upcoming <-> cancelled to match app statuses
    $new = ($cur === 'upcoming') ? 'cancelled' : 'upcoming';
    $up = $pdo->prepare('UPDATE events SET status = :s WHERE event_id = :id');
    $up->execute([':s'=>$new,':id'=>$id]);
    $_SESSION['flash'] = 'Event status updated.';
}
header('Location: /StudentPortal/events/index.php'); exit;
