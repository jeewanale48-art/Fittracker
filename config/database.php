<?php
declare(strict_types=1);

$host = '127.0.0.1';
$dbname = 'FitTracker';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    error_log('FitTrack database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Database connection failed. Check config/database.php and make sure MySQL is running.');
}
