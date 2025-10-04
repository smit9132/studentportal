<?php
require_once __DIR__ . '/../db/db_connect.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>StudentPortal</title>
  <link href="/StudentPortal/css/style.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="/StudentPortal/index.php">StudentPortal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsMain" aria-controls="navbarsMain" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/StudentPortal/index.php">Home</a></li>
        <?php if (isLoggedIn()): ?>
          <li class="nav-item"><a class="nav-link" href="/StudentPortal/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/StudentPortal/events/index.php">Events</a></li>
          <li class="nav-item"><a class="nav-link" href="/StudentPortal/students/index.php">My Profile</a></li>
          <?php if (isAdmin()): ?>
            <li class="nav-item"><a class="nav-link" href="/StudentPortal/users/index.php">Users</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
          <li class="nav-item"><span class="navbar-text me-2">Hello, <?= e($_SESSION['username'] ?? '') ?></span></li>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="/StudentPortal/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/StudentPortal/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/StudentPortal/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container mt-4">
