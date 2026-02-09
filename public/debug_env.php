<?php
echo "<h1>Environment Debug</h1>";
echo "<strong>Loaded INI:</strong> " . php_ini_loaded_file() . "<br>";
echo "<strong>PDO Drivers:</strong> " . implode(', ', PDO::getAvailableDrivers()) . "<br>";
echo "<strong>Loaded Extensions:</strong><pre>";
print_r(get_loaded_extensions());
echo "</pre>";

echo "<h2>Test Connection</h2>";
try {
    // Try to connect to the file defined in .env (we simulate it here)
    // We need to look at what Laravel thinks is the DB
    echo "Attempting raw SQLite connection...<br>";
    $dbPath = __DIR__ . '/../database/database_bajocero.sqlite';
    echo "Path: $dbPath<br>";
    $pdo = new PDO("sqlite:$dbPath");
    echo "✅ Raw PDO connection successful!";
} catch (Exception $e) {
    echo "❌ Raw PDO connection failed: " . $e->getMessage();
}
