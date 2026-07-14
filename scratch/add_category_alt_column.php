<?php
$host = 'localhost';
$db   = 'giftshop';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to database successfully.\n";

    // Alter categories table to add image_alt column if not exists
    $stmt = $pdo->query("SHOW COLUMNS FROM `categories` LIKE 'image_alt'");
    $column = $stmt->fetch();
    if (!$column) {
        $pdo->exec("ALTER TABLE `categories` ADD COLUMN `image_alt` VARCHAR(255) DEFAULT NULL AFTER `image_path`");
        echo "Column `image_alt` added successfully to `categories` table.\n";
    } else {
        echo "Column `image_alt` already exists in `categories` table.\n";
    }
} catch (\PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
