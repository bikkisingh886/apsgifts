<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=giftshop;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        CREATE TABLE IF NOT EXISTS `faqs` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `category` VARCHAR(100) DEFAULT 'General',
          `question` VARCHAR(255) NOT NULL,
          `answer` TEXT NOT NULL,
          `sort_order` INT DEFAULT 0,
          `is_active` TINYINT DEFAULT 1,
          `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
          `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);
    echo "Faqs table created successfully.\n";

    // Seed default FAQs if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM `faqs`");
    if ($stmt->fetchColumn() == 0) {
        $seedSql = "
            INSERT INTO `faqs` (`category`, `question`, `answer`, `sort_order`, `is_active`) VALUES
            ('General', 'Do I need an account to place an order?', 'You can place an order as a guest or create an account to track your orders, save addresses, and earn rewards on future purchases.', 1, 1),
            ('Payments', 'What payment methods do you accept?', 'We accept all major Credit Cards, Debit Cards, Net Banking, and UPI (Google Pay, PhonePe, Paytm) via our 100% secure payment gateway.', 2, 1),
            ('Delivery', 'How long will delivery take?', 'Same-day delivery is available for express gifts, fresh flowers, and cakes in selected cities. Standard courier items are delivered within 3-5 business days across India.', 3, 1),
            ('Delivery', 'Do you provide midnight delivery options?', 'Yes, midnight delivery is available for selected cities on flowers and cakes. Please select midnight delivery option during checkout.', 4, 1),
            ('Orders', 'How can I track my order?', 'Once your order is dispatched, you will receive an SMS and email notification with a live tracking link. You can also track under your Account Orders tab.', 5, 1),
            ('Returns', 'What are the product refund conditions?', 'If an item is damaged or incorrect upon delivery, please contact our support team within 24 hours with photos for a quick replacement or refund.', 6, 1);
        ";
        $pdo->exec($seedSql);
        echo "Default FAQs seeded successfully.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
