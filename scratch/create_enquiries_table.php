<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=giftshop;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        CREATE TABLE IF NOT EXISTS `enquiries` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `name` VARCHAR(150) NOT NULL,
          `email` VARCHAR(150) NOT NULL,
          `phone` VARCHAR(50) DEFAULT NULL,
          `subject` VARCHAR(255) NOT NULL,
          `message` TEXT NOT NULL,
          `status` VARCHAR(20) DEFAULT 'unread',
          `ip_address` VARCHAR(45) DEFAULT NULL,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);
    echo "Enquiries table created or verified successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
