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
    $tables = ['categories', 'cities', 'coupons', 'homepage_sections', 'menu_items', 'menus', 'offers', 'products', 'product_reviews', 'settings', 'users'];
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("DESCRIBE `$table`");
        $cols = $stmt->fetchAll();
        echo "Table: $table\n";
        foreach ($cols as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
        echo "\n";
    }
} catch (\PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
