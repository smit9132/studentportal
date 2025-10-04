<?php require_once __DIR__ . '/inc/header.php'; ?>

<div class="jumbotron py-4">
  <h1 class="display-5">Welcome to StudentPortal</h1>
  <p class="lead">A simple portal integrating authentication, events, and student management.</p>
  <hr class="my-4">
  <?php if (!isLoggedIn()): ?>
    <p><a class="btn btn-primary" href="/StudentPortal/login.php">Login</a>
    <a class="btn btn-outline-primary" href="/StudentPortal/register.php">Register</a></p>
  <?php else: ?>
    <p><a class="btn btn-primary" href="/StudentPortal/dashboard.php">Go to Dashboard</a></p>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
