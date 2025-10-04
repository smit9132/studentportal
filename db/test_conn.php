<?php
// db/test_conn.php
// Quick PDO connection test for StudentPortal
require_once __DIR__ . '/db_connect.php';

try {
    $pdo = getPDO();
    echo "PDO connected successfully.\n";
    // show server info if available
    $ver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . ' ' . $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
    echo "Driver/Client: " . $ver . "\n";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}

?>
