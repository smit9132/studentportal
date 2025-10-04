<?php
// db/check_admin.php
// Prints admin hash and verifies sample password. Remove after use.
require_once __DIR__ . '/db_connect.php';

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT user_id, username, password FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => 'admin']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo "No admin user found.\n";
        exit(0);
    }
    echo "Admin user_id: " . $row['user_id'] . "\n";
    echo "Stored hash: " . $row['password'] . "\n";

    $testPw = 'S!tudentP0rtal#2025';
    echo "Verifying known password ('S!tudentP0rtal#2025'): ";
    echo password_verify($testPw, $row['password']) ? "MATCH\n" : "NO MATCH\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
