<?php
require_once __DIR__ . '/config.php';

function get_db() {
    static $pdo;
    if ($pdo) return $pdo;
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die("DB connection failed: " . $e->getMessage());
    }
    return $pdo;
}

function add_log($source, $level, $message) {
    $pdo = get_db();
    $stmt = $pdo->prepare("INSERT INTO logs (source, level, message) VALUES (?, ?, ?)");
    $stmt->execute([$source, $level, $message]);
}
