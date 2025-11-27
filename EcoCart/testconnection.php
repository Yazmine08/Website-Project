<?php
$db_name = 'EcoCart'; // Ensure this DB exists in phpMyAdmin first!

// Array of credentials to try
$attempts = [
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'port' => 3306, 'desc' => 'Default XAMPP (No Password, IPv4)'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'port' => 3306, 'desc' => 'Default XAMPP (No Password, localhost)'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'mySQL08', 'port' => 3306, 'desc' => 'Your Custom Password (IPv4)'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'port' => 3307, 'desc' => 'Alternate Port 3307 (No Password)']
];

echo "<h2>MySQL Connection Diagnostics</h2>";

foreach ($attempts as $try) {
    echo "<hr>";
    echo "<strong>Trying:</strong> {$try['desc']}<br>";
    echo "<em>Host: {$try['host']} | User: {$try['user']} | Pass: " . ($try['pass'] ? '****' : '(empty)') . " | Port: {$try['port']}</em><br>";

    try {
        $dsn = "mysql:host={$try['host']};port={$try['port']};dbname=$db_name;charset=utf8mb4";
        $pdo = new PDO($dsn, $try['user'], $try['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "<span style='color:green; font-weight:bold;'>SUCCESS! This is the correct configuration.</span><br>";
        echo "Please update your db_connect.php with these details.";
        break; // Stop after finding a working one
    } catch (PDOException $e) {
        echo "<span style='color:red;'>FAILED:</span> " . $e->getMessage() . "<br>";
    }
}
?>