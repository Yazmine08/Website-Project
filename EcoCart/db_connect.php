<?php
$host = '127.0.0.1'; // Use IP address to avoid IPv6 issues
$db   = 'EcoCart';
$user = 'root';
$pass = 'mySQL08'; // The password validated by your test
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create the PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // If connection fails, stop everything and show error
    die("Database Connection Failed: " . $e->getMessage());
}
?>