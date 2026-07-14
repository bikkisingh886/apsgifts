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

    // 1. Create Roles table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `roles` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(50) NOT NULL UNIQUE,
      `description` VARCHAR(255) DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table `roles` created or already exists.\n";

    // 2. Create Role Permissions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `role_permissions` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `role_id` INT NOT NULL,
      `module` VARCHAR(50) NOT NULL,
      `can_view` TINYINT DEFAULT 0,
      `can_create` TINYINT DEFAULT 0,
      `can_edit` TINYINT DEFAULT 0,
      `can_delete` TINYINT DEFAULT 0,
      CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
      UNIQUE KEY `uk_role_module` (`role_id`, `module`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table `role_permissions` created or already exists.\n";

    // 3. Create SEO Pages table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `seo_pages` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `page_key` VARCHAR(50) NOT NULL UNIQUE,
      `page_name` VARCHAR(100) NOT NULL,
      `meta_title` TEXT DEFAULT NULL,
      `meta_desc` TEXT DEFAULT NULL,
      `twitter_card` VARCHAR(50) DEFAULT 'summary_large_image',
      `twitter_title` TEXT DEFAULT NULL,
      `twitter_desc` TEXT DEFAULT NULL,
      `twitter_image` VARCHAR(255) DEFAULT NULL,
      `og_title` TEXT DEFAULT NULL,
      `og_desc` TEXT DEFAULT NULL,
      `og_image` VARCHAR(255) DEFAULT NULL,
      `og_type` VARCHAR(50) DEFAULT 'website',
      `schema_markup` TEXT DEFAULT NULL,
      `created_by` INT DEFAULT NULL,
      `updated_by` INT DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table `seo_pages` created or already exists.\n";

    // 4. Create Employee Activity Logs table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `employee_activity_logs` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` INT NOT NULL,
      `module` VARCHAR(50) NOT NULL,
      `action` VARCHAR(50) NOT NULL,
      `details` TEXT DEFAULT NULL,
      `ip_address` VARCHAR(45) DEFAULT NULL,
      `user_agent` VARCHAR(255) DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Table `employee_activity_logs` created or already exists.\n";

    // Helper function to check if column exists
    function add_column_if_not_exists($pdo, $table, $column, $definition) {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        $exists = $stmt->fetch();
        if (!$exists) {
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
            echo "Added column `$column` to `$table`.\n";
        }
    }

    // Helper to alter column
    function modify_column($pdo, $table, $column, $definition) {
        $pdo->exec("ALTER TABLE `$table` MODIFY COLUMN `$column` $definition");
        echo "Modified column `$column` in `$table`.\n";
    }

    // 5. Alter Categories table
    modify_column($pdo, 'categories', 'meta_title', 'TEXT DEFAULT NULL');
    add_column_if_not_exists($pdo, 'categories', 'twitter_card', "VARCHAR(50) DEFAULT 'summary_large_image'");
    add_column_if_not_exists($pdo, 'categories', 'twitter_title', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'twitter_desc', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'twitter_image', "VARCHAR(255) DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'og_title', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'og_desc', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'og_image', "VARCHAR(255) DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'og_type', "VARCHAR(50) DEFAULT 'website'");
    add_column_if_not_exists($pdo, 'categories', 'schema_markup', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'categories', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 6. Alter Products table
    modify_column($pdo, 'products', 'meta_title', 'TEXT DEFAULT NULL');
    add_column_if_not_exists($pdo, 'products', 'hide_from_frontend', "TINYINT DEFAULT 0");
    add_column_if_not_exists($pdo, 'products', 'twitter_card', "VARCHAR(50) DEFAULT 'summary_large_image'");
    add_column_if_not_exists($pdo, 'products', 'twitter_title', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'twitter_desc', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'twitter_image', "VARCHAR(255) DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'og_title', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'og_desc', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'og_image', "VARCHAR(255) DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'og_type', "VARCHAR(50) DEFAULT 'product'");
    add_column_if_not_exists($pdo, 'products', 'schema_markup', "TEXT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'products', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 7. Alter Cities table
    add_column_if_not_exists($pdo, 'cities', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'cities', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'cities', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 8. Alter Coupons table
    add_column_if_not_exists($pdo, 'coupons', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'coupons', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'coupons', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 9. Alter Offers table
    add_column_if_not_exists($pdo, 'offers', 'created_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    add_column_if_not_exists($pdo, 'offers', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'offers', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'offers', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 10. Alter Menus table
    add_column_if_not_exists($pdo, 'menus', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'menus', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'menus', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 11. Alter Menu Items table
    add_column_if_not_exists($pdo, 'menu_items', 'created_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    add_column_if_not_exists($pdo, 'menu_items', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'menu_items', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'menu_items', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 12. Alter Homepage Sections table
    add_column_if_not_exists($pdo, 'homepage_sections', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'homepage_sections', 'updated_by', "INT DEFAULT NULL");

    // 13. Alter Product Reviews table
    add_column_if_not_exists($pdo, 'product_reviews', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'product_reviews', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 14. Alter Settings table
    add_column_if_not_exists($pdo, 'settings', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'settings', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

    // 15. Alter Users table
    add_column_if_not_exists($pdo, 'users', 'role_id', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'users', 'created_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'users', 'updated_by', "INT DEFAULT NULL");
    add_column_if_not_exists($pdo, 'users', 'updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    
    // Add foreign key constraint to users role_id
    try {
        $pdo->exec("ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");
        echo "Foreign key constraint added for users role_id.\n";
    } catch (\Exception $e) {
        // Might already exist
        echo "Foreign key constraint on users role_id note: " . $e->getMessage() . "\n";
    }

    // 16. Seed Default Roles
    $stmt = $pdo->prepare("INSERT IGNORE INTO `roles` (`id`, `name`, `description`) VALUES 
      (1, 'Admin', 'Super Administrator with full privileges'),
      (2, 'Manager', 'Manager who can moderate products, categories and orders')");
    $stmt->execute();
    echo "Default roles seeded.\n";

    // 17. Seed Default Admin Role Permissions
    $modules = ['products', 'categories', 'cities', 'offers', 'menus', 'homepage', 'orders', 'users', 'settings', 'coupons', 'reviews', 'employees', 'activities', 'seo_pages'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO `role_permissions` (`role_id`, `module`, `can_view`, `can_create`, `can_edit`, `can_delete`) VALUES (?, ?, 1, 1, 1, 1)");
    foreach ($modules as $module) {
        $stmt->execute([1, $module]);
    }
    
    // Seed default permissions for Manager as well (e.g. view/create/edit for products, categories, orders; no delete, no settings/employees/roles/activities)
    $managerModules = [
        'products' => [1, 1, 1, 0],
        'categories' => [1, 1, 1, 0],
        'cities' => [1, 0, 0, 0],
        'offers' => [1, 1, 1, 0],
        'menus' => [1, 0, 0, 0],
        'homepage' => [1, 0, 1, 0],
        'orders' => [1, 0, 1, 0],
        'users' => [1, 0, 0, 0],
        'settings' => [0, 0, 0, 0],
        'coupons' => [1, 1, 1, 0],
        'reviews' => [1, 0, 1, 0],
        'employees' => [0, 0, 0, 0],
        'activities' => [0, 0, 0, 0],
        'seo_pages' => [1, 0, 1, 0]
    ];
    $stmtM = $pdo->prepare("INSERT IGNORE INTO `role_permissions` (`role_id`, `module`, `can_view`, `can_create`, `can_edit`, `can_delete`) VALUES (2, ?, ?, ?, ?, ?)");
    foreach ($managerModules as $m => $perms) {
        $stmtM->execute([$m, $perms[0], $perms[1], $perms[2], $perms[3]]);
    }
    echo "Role permissions seeded.\n";

    // 18. Link Admin user (admin@giftshop.in) to Admin role
    $pdo->exec("UPDATE `users` SET `role_id` = 1 WHERE `email` = 'admin@giftshop.in'");
    echo "Linked default Admin User (admin@giftshop.in) to role ID 1 (Admin).\n";

    // 19. Seed SEO Pages
    $pages = [
        ['home', 'Homepage', 'GiftShop - Send Cakes, Flowers & Gifts Online', 'Complete gift e-commerce shop with same-day express delivery and courier options across Patna, Bihar.'],
        ['shop', 'Shop Listing Page', 'All Products | GiftShop', 'Browse all our premium cakes, flowers, combos, and custom gifts online.'],
        ['about', 'About Us Page', 'About Us | GiftShop', 'Learn more about GiftShop, our journey, and same day delivery services.'],
        ['faq', 'Frequently Asked Questions', 'Frequently Asked Questions | GiftShop', 'Check the FAQs regarding order delivery times, express shipping, and payment options.'],
        ['terms', 'Terms of Service', 'Terms of Service | GiftShop', 'Terms of service and rules for using the GiftShop platform.'],
        ['privacy', 'Privacy Policy', 'Privacy Policy | GiftShop', 'Privacy policy and user data safety details.'],
        ['contact', 'Contact Us Page', 'Contact Us | GiftShop', 'Get in touch with GiftShop customer support for orders, delivery, and questions.']
    ];
    $stmtP = $pdo->prepare("INSERT IGNORE INTO `seo_pages` (`page_key`, `page_name`, `meta_title`, `meta_desc`) VALUES (?, ?, ?, ?)");
    foreach ($pages as $p) {
        $stmtP->execute($p);
    }
    echo "Initial SEO Pages seeded.\n";

    echo "Migration completed successfully!\n";

} catch (\PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
