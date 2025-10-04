<?php
require_once __DIR__ . '/db/db_connect.php';
requireLogin();
if (!isAdmin()) {
    // Only admin can view analytics
    header('Location: /StudentPortal/dashboard.php');
    exit;
}

$pdo = getPDO();
$totUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
$totEvents = $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
$openEvents = $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'open'")->fetchColumn();

require_once __DIR__ . '/inc/header.php';
?>

<h2>Analytics</h2>
<div class="row mt-3">
  <div class="col-md-3">
    <div class="card text-bg-light mb-3">
      <div class="card-body">
        <h5 class="card-title">Total Users</h5>
        <p class="card-text"><?= e((string)$totUsers) ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-light mb-3">
      <div class="card-body">
        <h5 class="card-title">Active Users</h5>
        <p class="card-text"><?= e((string)$activeUsers) ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-light mb-3">
      <div class="card-body">
        <h5 class="card-title">Total Events</h5>
        <p class="card-text"><?= e((string)$totEvents) ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-bg-light mb-3">
      <div class="card-body">
        <h5 class="card-title">Open Events</h5>
        <p class="card-text"><?= e((string)$openEvents) ?></p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
