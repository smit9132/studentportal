<?php
require_once __DIR__ . '/../db/db_connect.php';
requireLogin();
if (!isAdmin()) { header('Location: /StudentPortal/dashboard.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = :id');
    $stmt->execute([':id' => $id]);
    $_SESSION['flash'] = 'User deleted.';
}
header('Location: /StudentPortal/users/index.php');
exit;
