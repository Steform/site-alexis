<?php
/**
 * Quick script to test MariaDB connection on port 3607.
 * Run: php check-db.php
 *
 * @author Stephane H.
 * @created 2026-03-11
 */

$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = '';
$db = 'site_alexis';

echo "Testing connection to MariaDB on {$host}:{$port}...\n";

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "OK - Connected successfully!\n";
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "MariaDB version: {$version}\n";
} catch (PDOException $e) {
    echo "FAIL - " . $e->getMessage() . "\n";
    echo "\nCheck:\n";
    echo "- WAMP icon is green (MariaDB started)\n";
    echo "- Port 3607 in WAMP: Left click WAMP > MySQL > my.ini > port = 3607\n";
    exit(1);
}
