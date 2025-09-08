<?php

// this details are for localhost
$host = "localhost";   
$user = "root";        
$pass = "";            
$dbname = "skgst_ngo";
$charset = 'utf8mb4';

// this details are for phpmyadmin in cpanel
// $host = "localhost";   
// $user = "skgstgulatiji_SKGST_NGO";        
// $pass = "Skgst@1@2@3";            
// $dbname = "skgstgulatiji_SKGST_NGO";
// $charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}
?>