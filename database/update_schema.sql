-- Update Schema for Gift E-commerce Platform

USE `giftshop`;

-- 1. Alter Categories table for subcategories
ALTER TABLE `categories` 
ADD COLUMN `parent_id` INT DEFAULT NULL AFTER `id`,
ADD COLUMN `show_in_menu` TINYINT DEFAULT 1 AFTER `is_active`,
ADD COLUMN `sort_order` INT DEFAULT 0 AFTER `show_in_menu`,
ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE INDEX `idx_categories_parent` ON `categories` (`parent_id`, `is_active`, `sort_order`);


-- 2. Create Cities table
CREATE TABLE IF NOT EXISTS `cities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `is_popular` TINYINT DEFAULT 0,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_cities_active` (`is_active`, `is_popular`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 3. Create Product Cities pivot table
CREATE TABLE IF NOT EXISTS `product_cities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `city_id` INT NOT NULL,
  `price_override` DECIMAL(10, 2) DEFAULT NULL,
  `is_available` TINYINT DEFAULT 1,
  CONSTRAINT `fk_prod_city_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_prod_city_city` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `uk_product_city` (`product_id`, `city_id`),
  INDEX `idx_city_product_lookup` (`city_id`, `product_id`, `is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 4. Alter Products table for type
ALTER TABLE `products` 
ADD COLUMN `product_type` ENUM('simple', 'combo') NOT NULL DEFAULT 'simple' AFTER `sku`;


-- 5. Create Combo Items table
CREATE TABLE IF NOT EXISTS `combo_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `combo_product_id` INT NOT NULL,
  `child_product_id` INT NOT NULL,
  `qty` INT NOT NULL DEFAULT 1,
  CONSTRAINT `fk_combo_parent` FOREIGN KEY (`combo_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_combo_child` FOREIGN KEY (`child_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `uk_combo_item` (`combo_product_id`, `child_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 6. Seed initial Cities
INSERT INTO `cities` (`name`, `slug`, `is_popular`, `is_active`) VALUES
('Delhi', 'delhi', 1, 1),
('Mumbai', 'mumbai', 1, 1),
('Bangalore', 'bangalore', 1, 1),
('Kolkata', 'kolkata', 1, 1),
('Patna', 'patna', 1, 1),
('Pune', 'pune', 0, 1),
('Hyderabad', 'hyderabad', 0, 1),
('Chennai', 'chennai', 0, 1),
('Gurgaon', 'gurgaon', 0, 1),
('Noida', 'noida', 0, 1);
