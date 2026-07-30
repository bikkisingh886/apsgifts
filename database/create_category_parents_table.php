<?php
try {
    $host = 'localhost';
    $dbName = 'giftshop';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table category_parents
    $createTableSql = "
        CREATE TABLE IF NOT EXISTS `category_parents` (
            `category_id` INT NOT NULL,
            `parent_id` INT NOT NULL,
            PRIMARY KEY (`category_id`, `parent_id`),
            CONSTRAINT `fk_cat_parents_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk_cat_parents_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($createTableSql);
    echo "Table 'category_parents' created or verified successfully.\n";

    // Migrate existing relationships from categories.parent_id
    $stmt = $pdo->query("SELECT id, parent_id FROM categories WHERE parent_id IS NOT NULL AND parent_id != 0");
    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $inserted = 0;
    $insertStmt = $pdo->prepare("INSERT IGNORE INTO category_parents (category_id, parent_id) VALUES (:category_id, :parent_id)");
    foreach ($existing as $row) {
        $insertStmt->execute([
            'category_id' => $row['id'],
            'parent_id'   => $row['parent_id']
        ]);
        $inserted += $insertStmt->rowCount();
    }

    echo "Migrated $inserted existing parent-child relationships into 'category_parents'.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
