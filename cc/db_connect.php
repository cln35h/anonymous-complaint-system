<?php
// Database configuration

$dbHost = "localhost";
$dbName = "college";
$dbUser = "root";
$dbPass = "";



try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set charset to UTF-8
    $conn->exec("set names utf8");
} catch(PDOException $e) {
    // Display error message if connection fails
    die("Connection failed: " . $e->getMessage());
}
?>
