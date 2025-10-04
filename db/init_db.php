<?php
// db/init_db.php
// Simple script to import db/schema.sql into MySQL using mysqli.
// Usage: open in browser (http://localhost/StudentPortal/db/init_db.php) or run via CLI: php db/init_db.php

$host = '127.0.0.1';
$user = 'root';
$pass = ''; // adjust if you have a root password
$schemaFile = __DIR__ . DIRECTORY_SEPARATOR . 'schema.sql';

if (!file_exists($schemaFile)) {
    echo "schema.sql not found at: $schemaFile\n";
    exit(1);
}

$sql = file_get_contents($schemaFile);
if ($sql === false) {
    echo "Failed to read schema file\n";
    exit(1);
}

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_errno) {
    echo "MySQL connect failed: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}

// Allow multiple statements
if ($mysqli->multi_query($sql)) {
    $results = 0;
    do {
        if ($res = $mysqli->store_result()) {
            $res->free();
        }
        $results++;
    } while ($mysqli->more_results() && $mysqli->next_result());

    if ($mysqli->errno) {
        echo "Error executing statements: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
        exit(1);
    }

    echo "Imported schema successfully. Statements executed: $results\n";
} else {
    echo "multi_query failed: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
    exit(1);
}

$mysqli->close();

// If running from browser, show a simple link back
if (php_sapi_name() !== 'cli') {
    echo "<p>Done. <a href=\"/StudentPortal/\">Return to app</a></p>";
}

return 0;
