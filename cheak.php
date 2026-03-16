<?php

// Database configuration
$host = "localhost";
$dbname = "testdbdocker ps";
$username = "root";
$password = "root"; // XAMPP me default blank hota hai

try {
    // Step 1: Host se connect
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Host se successfully connect ho gaya.<br>";

    // Step 2: Database select check
    $pdo->exec("USE `$dbname`");

    echo "✅ Database se bhi successfully connect ho gaya.";

} catch (PDOException $e) {
    
    echo "❌ Connection failed: " . $e->getMessage();
}

?>