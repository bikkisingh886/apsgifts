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
    echo "Connected successfully to giftshop!\n\n";

    $stmt = $pdo->query("SELECT id, name, slug, parent_id, is_active FROM categories");
    $categories = $stmt->fetchAll();

    echo "Categories in database:\n";
    printf("%-5s | %-30s | %-30s | %-10s | %-10s\n", "ID", "Name", "Slug", "Parent ID", "Is Active");
    echo str_repeat("-", 95) . "\n";
    foreach ($categories as $cat) {
        printf("%-5d | %-30s | %-30s | %-10s | %-10d\n", 
            $cat['id'], 
            $cat['name'], 
            $cat['slug'], 
            $cat['parent_id'] === null ? 'NULL' : $cat['parent_id'], 
            $cat['is_active']
        );
    }
} catch (\PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
