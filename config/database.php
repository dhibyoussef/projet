<?php
// Database configuration
$host = 'localhost';
$db = 'fitness_tracker';
$user = 'root'; // Change as needed
$pass = ''; // Change as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log($e->getMessage());
    throw new PDOException('Database connection failed. Please check your configuration.', (int)$e->getCode());
}
?>