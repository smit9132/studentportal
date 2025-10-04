<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) { header('Location: /StudentPortal/dashboard.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT status FROM users WHERE user_id = :id');
    $stmt->execute([':id'=>$id]);
    $cur = $stmt->fetchColumn();
    $new = ($cur === 'active') ? 'inactive' : 'active';
    $up = $pdo->prepare('UPDATE users SET status = :s WHERE user_id = :id');
    $up->execute([':s'=>$new,':id'=>$id]);
    $_SESSION['flash'] = 'User status updated.';
}
header('Location: /StudentPortal/users/index.php'); exit;
