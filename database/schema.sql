-- Gift E-commerce Platform Database Schema
-- MySQL/MariaDB compatible

CREATE DATABASE IF NOT EXISTS `giftshop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `giftshop`;

-- 1. Users Table (Authentication and profile)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `mobile` VARCHAR(15) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories Table (SEO-ready)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `summary` TEXT DEFAULT NULL,
  `footer_content` TEXT DEFAULT NULL,
  `meta_title` VARCHAR(150) DEFAULT NULL,
  `meta_desc` TEXT DEFAULT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Offers Table (Discounts)
CREATE TABLE IF NOT EXISTS `offers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('percent', 'flat') NOT NULL,
  `value` DECIMAL(10, 2) NOT NULL,
  `applies_to` ENUM('product', 'category') NOT NULL,
  `is_active` TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Products Table (Core)
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `price` DECIMAL(10, 2) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `delivery_type` ENUM('Express', 'Courier') NOT NULL DEFAULT 'Express',
  `offer_id` INT DEFAULT NULL,
  `meta_title` VARCHAR(150) DEFAULT NULL,
  `meta_desc` TEXT DEFAULT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_products_offer` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Product Categories Table (Pivot M-M)
CREATE TABLE IF NOT EXISTS `product_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  CONSTRAINT `fk_prod_cat_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_cat_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Product Images Table (Gallery)
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Orders Table (Core)
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `status` ENUM('Processing', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Processing',
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `discount` DECIMAL(10, 2) DEFAULT 0.00,
  `total` DECIMAL(10, 2) NOT NULL,
  `delivery_date` DATE DEFAULT NULL,
  `tracking_url` TEXT DEFAULT NULL,
  `tracking_code` VARCHAR(100) DEFAULT NULL,
  `address_json` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Order Items Table (Line Items)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT DEFAULT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `delivery_type` ENUM('Express', 'Courier') NOT NULL,
  `delivery_date` DATE DEFAULT NULL,
  `qty` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10, 2) NOT NULL,
  CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Seed Data (Initial inserts for testing)
INSERT INTO `users` (`name`, `email`, `mobile`, `password`, `is_active`) VALUES
('Admin User', 'admin@giftshop.in', '9876543210', '$2y$10$wKzI07p/qJ4BqG.hKpe0oeyw6hR5TqE3k6N/Z2fJj.fM0H59Yl35.', 1); -- password is 'admin123' hashed with bcrypt

INSERT INTO `categories` (`name`, `slug`, `summary`, `footer_content`, `meta_title`, `meta_desc`, `is_active`) VALUES
('Flowers', 'flowers', 'Beautiful fresh flowers for your loved ones', 'Find the best flower delivery service in Patna, Bihar and surrounding areas.', 'Fresh Flowers Online - GiftShop', 'Buy roses, lilies, and carnations online.', 1),
('For Her', 'for-her', 'Special gifts curated for the lovely ladies', 'Select from personalized frames, chocolates, and flowers for women.', 'Gifts For Her - GiftShop', 'Perfect gifts for girlfriend, wife, mother, or sister.', 1),
('Birthday', 'birthday-gifts', 'Awesome birthday gift collection with instant delivery', 'Send Birthday Gifts Online with Same Day Delivery across India.', 'Send Birthday Gifts Online | Same Day Delivery - GiftShop', 'Order birthday gifts online with guaranteed same-day delivery.', 1),
('Anniversary', 'anniversary-gifts', 'Celebrate milestones with special gifts', 'Anniversary combos, custom photo frames, and chocolate hampers.', 'Anniversary Gifts Online - GiftShop', 'Best couples gifts and flower bouquets.', 1),
('Chocolates', 'chocolates', 'Sweet treats and premium chocolate bouquets', 'Ferrero Rocher, Cadbury, and customized chocolate bouquets.', 'Premium Chocolates Online - GiftShop', 'Buy delicious chocolate gift packs.', 0); -- Inactive initially

INSERT INTO `offers` (`name`, `type`, `value`, `applies_to`, `is_active`) VALUES
('Summer Sale 10%', 'percent', 10.00, 'product', 1),
('Flat 100 Off', 'flat', 100.00, 'category', 1),
('Anniversary Special', 'percent', 15.00, 'product', 0);

INSERT INTO `products` (`name`, `slug`, `sku`, `price`, `description`, `delivery_type`, `offer_id`, `meta_title`, `meta_desc`, `is_active`) VALUES
('Red Rose Bouquet – 20 Stems', 'red-rose-bouquet-20-stems', 'RRB-001', 499.00, 'Fresh red roses, beautifully arranged. Perfect for birthdays & anniversaries. Delivered fresh same day.', 'Express', 1, 'Red Rose Bouquet - GiftShop', 'Buy red rose bouquet of 20 stems online with express delivery.', 1),
('Photo Frame', 'custom-photo-frame', 'CPF-002', 799.00, 'High-quality custom photo frame to preserve your special memories.', 'Courier', NULL, 'Custom Photo Frame - GiftShop', 'Get premium custom photo frames.', 1),
('Chocolate Cake 1kg', 'chocolate-cake-1kg', 'CC-003', 649.00, 'Rich and delicious double chocolate cake, baked fresh.', 'Express', NULL, 'Chocolate Cake 1kg - GiftShop', 'Order delicious chocolate cake online.', 1),
('Balloon Bouquet', 'balloon-bouquet', 'BB-004', 349.00, 'Colorful balloons for decorations and birthday surprises.', 'Express', NULL, 'Balloon Bouquet - GiftShop', 'Order balloon bouquets online.', 1),
('Gift Hamper', 'gift-hamper', 'GH-005', 1299.00, 'Curated gift hamper including chocolates, card, and small teddy bear.', 'Courier', NULL, 'Gift Hamper - GiftShop', 'Premium gift hampers for delivery.', 1);

INSERT INTO `product_categories` (`product_id`, `category_id`) VALUES
(1, 1), -- Red Rose Bouquet is Flowers
(1, 2), -- Red Rose Bouquet is For Her
(1, 3), -- Red Rose Bouquet is Birthday
(2, 2), -- Photo Frame is For Her
(2, 4), -- Photo Frame is Anniversary
(3, 3), -- Chocolate Cake is Birthday
(4, 3), -- Balloon Bouquet is Birthday
(5, 2), -- Gift Hamper is For Her
(5, 4); -- Gift Hamper is Anniversary

INSERT INTO `product_images` (`product_id`, `image_path`, `is_primary`, `sort_order`) VALUES
(1, 'uploads/products/red-rose-bouquet.jpg', 1, 1),
(2, 'uploads/products/photo-frame.jpg', 1, 1),
(3, 'uploads/products/chocolate-cake.jpg', 1, 1),
(4, 'uploads/products/balloon-bouquet.jpg', 1, 1),
(5, 'uploads/products/gift-hamper.jpg', 1, 1);
