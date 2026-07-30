<?php
$mysqli = new mysqli('localhost', 'root', '', 'giftshop');
if ($mysqli->connect_error) {
    die('Connect Error: ' . $mysqli->connect_error);
}

$queries = [
    "CREATE TABLE IF NOT EXISTS `colors` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(100) NOT NULL UNIQUE,
      `color_code` VARCHAR(50) DEFAULT NULL,
      `is_active` TINYINT DEFAULT 1,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

    "CREATE TABLE IF NOT EXISTS `product_colors` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `product_id` INT NOT NULL,
      `color_id` INT NOT NULL,
      CONSTRAINT `fk_prod_col_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_prod_col_color` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      UNIQUE KEY `uk_product_color` (`product_id`, `color_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($queries as $i => $query) {
    if (!$mysqli->query($query)) {
        die("Query " . ($i + 1) . " failed: " . $mysqli->error);
    }
}

echo "Tables created successfully.\n";
