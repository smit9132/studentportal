<?php
// db/db_connect.php
// PDO-based connection helper for StudentPortal
declare(strict_types=1);

session_start();

// Use environment variables if available (for Render or other hosts)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'studentportal';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

// Function to return PDO instance
function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    global $host, $db, $user, $pass;

    $dsn = 'mysql:host=' . $host . ';dbname=' . $db . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        exit('Database connection failed: ' . $e->getMessage());
    }
}

// Helper for escaping output
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Auth helpers
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /StudentPortal/login.php');
        exit;
    }
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
