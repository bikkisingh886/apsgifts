<?php
/**
 * Setup and seed database table for dynamic homepage sections.
 * Run this from root directory: php database/setup_homepage_sections.php
 */

try {
    // 1. Establish PDO Connection (defaults matching .env)
    $host = 'localhost';
    $dbName = 'giftshop';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ensure DB exists and select it
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    
    echo "Connected to database: $dbName\n";

    // 2. Create the Table
    $createTableSql = "
    CREATE TABLE IF NOT EXISTS `homepage_sections` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `section_key` VARCHAR(50) NOT NULL UNIQUE,
      `title` VARCHAR(255) NOT NULL,
      `subtitle` VARCHAR(255) DEFAULT NULL,
      `content_json` LONGTEXT DEFAULT NULL,
      `sort_order` INT DEFAULT 0,
      `is_active` TINYINT DEFAULT 1,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($createTableSql);
    echo "Table 'homepage_sections' verified/created.\n";

    // 3. Define Seed Data
    $sections = [
        [
            'section_key' => 'hero_slider',
            'title'       => 'Choose Perfect Gifts From Us',
            'subtitle'    => 'Best Gift Shop',
            'sort_order'  => 10,
            'is_active'   => 1,
            'content'     => [
                [
                    'title' => 'Choose Perfect <span>Gifts</span> From Us',
                    'subtitle' => 'Best Gift Shop',
                    'description' => 'There are many variations of passages orem psum available but the majority have suffered.',
                    'image' => 'assets/img/hero/slider-1.jpg',
                    'link' => 'shop',
                    'button_text' => 'Shop Now'
                ],
                [
                    'title' => 'Choose Perfect <span>Gifts</span> From Us',
                    'subtitle' => 'Best Gift Shop',
                    'description' => 'There are many variations of passages orem psum available but the majority have suffered.',
                    'image' => 'assets/img/hero/slider-2.jpg',
                    'link' => 'shop',
                    'button_text' => 'Shop Now'
                ],
                [
                    'title' => 'Choose Perfect <span>Gifts</span> From Us',
                    'subtitle' => 'Best Gift Shop',
                    'description' => 'There are many variations of passages orem psum available but the majority have suffered.',
                    'image' => 'assets/img/hero/slider-3.jpg',
                    'link' => 'shop',
                    'button_text' => 'Shop Now'
                ],
                [
                    'title' => 'Choose Perfect <span>Gifts</span> From Us',
                    'subtitle' => 'Best Gift Shop',
                    'description' => 'There are many variations of passages orem psum available but the majority have suffered.',
                    'image' => 'assets/img/hero/slider-4.jpg',
                    'link' => 'shop',
                    'button_text' => 'Shop Now'
                ]
            ]
        ],
        [
            'section_key' => 'features',
            'title'       => 'Our Core Features',
            'subtitle'    => 'Services',
            'sort_order'  => 20,
            'is_active'   => 1,
            'content'     => [
                [
                    'icon' => 'flaticon-delivery-truck',
                    'title' => 'Free Delivery',
                    'description' => 'On all orders above ₹499'
                ],
                [
                    'icon' => 'flaticon-refund',
                    'title' => 'Get Refund',
                    'description' => 'Easy return and exchange policy'
                ],
                [
                    'icon' => 'flaticon-credit-card',
                    'title' => 'Safe Payment',
                    'description' => '100% secure payment gateway'
                ],
                [
                    'icon' => 'flaticon-support',
                    'title' => '24/7 Support',
                    'description' => 'Dedicated customer assistance'
                ]
            ]
        ],
        [
            'section_key' => 'shop_by_occasion',
            'title'       => 'Shop By Occasions',
            'subtitle'    => 'Occasion Categories',
            'sort_order'  => 30,
            'is_active'   => 1,
            'content'     => [
                'title'        => 'Shop By Occasion',
                'subtitle'     => 'Occasion',
                'category_ids' => [3, 4] // Birthday, Anniversary
            ]
        ],
        [
            'section_key' => 'shop_by_recipient',
            'title'       => 'Shop By Recipient',
            'subtitle'    => 'Recipient Categories',
            'sort_order'  => 40,
            'is_active'   => 1,
            'content'     => [
                'title'        => 'Shop By Recipient',
                'subtitle'     => 'Recipient',
                'category_ids' => [2] // For Her
            ]
        ],
        [
            'section_key' => 'gift_finder',
            'title'       => 'Amaze With Perfect Gift',
            'subtitle'    => 'Find Gift',
            'sort_order'  => 50,
            'is_active'   => 1,
            'content'     => [
                'title'    => 'Amaze With Perfect Gift',
                'subtitle' => 'Gift Finder'
            ]
        ],
        [
            'section_key' => 'category_promotional_banners',
            'title'       => 'Exclusive Collections',
            'subtitle'    => 'Promo Banners',
            'sort_order'  => 60,
            'is_active'   => 1,
            'content'     => [
                [
                    'title' => 'Awesome Gifts Box Collections',
                    'image' => 'assets/img/banner/banner-1.jpg',
                    'link' => 'category/anniversary-gifts'
                ],
                [
                    'title' => 'Best Occasion Gifts Collections',
                    'image' => 'assets/img/banner/banner-2.jpg',
                    'link' => 'category/birthday-gifts'
                ],
                [
                    'title' => 'Combo Sets Gift Box Up To 50% Off',
                    'image' => 'assets/img/banner/banner-3.jpg',
                    'link' => 'category/flowers'
                ]
            ]
        ],
        [
            'section_key' => 'delivery_banner',
            'title'       => 'Same Day & Midnight Delivery Across India',
            'subtitle'    => 'Express Shipping',
            'sort_order'  => 70,
            'is_active'   => 1,
            'content'     => [
                'title'       => 'Same Day & Midnight Delivery Across India',
                'subtitle'    => 'Express Shipping',
                'image'       => 'assets/img/banner/delivery-banner.jpg',
                'link'        => 'shop',
                'button_text' => 'Order Now'
            ]
        ],
        [
            'section_key' => 'trending_items',
            'title'       => 'Trending Items',
            'subtitle'    => 'Trending',
            'sort_order'  => 80,
            'is_active'   => 1,
            'content'     => [
                'title'    => 'Trending Items',
                'subtitle' => 'Trending',
                'limit'    => 8
            ]
        ],
        [
            'section_key' => 'personalized_gifts',
            'title'       => 'Personalized Gifts',
            'subtitle'    => 'Customized',
            'sort_order'  => 90,
            'is_active'   => 1,
            'content'     => [
                'title'       => 'Personalized Gifts',
                'subtitle'    => 'Customized',
                'category_id' => 2, // For Her (we'll treat as personalized context)
                'limit'       => 8
            ]
        ],
        [
            'section_key' => 'promo_video',
            'title'       => 'What makes us different check our video',
            'subtitle'    => 'Latest Video',
            'sort_order'  => 100,
            'is_active'   => 1,
            'content'     => [
                'title'     => 'What makes us different check our video',
                'subtitle'  => 'Latest Video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'image'     => 'assets/img/video/video-bg.jpg'
            ]
        ],
        [
            'section_key' => 'popular_items',
            'title'       => 'Popular Items',
            'subtitle'    => 'Popular',
            'sort_order'  => 110,
            'is_active'   => 1,
            'content'     => [
                'title'    => 'Popular Items',
                'subtitle' => 'Popular',
                'limit'    => 12
            ]
        ],
        [
            'section_key' => 'weekly_deals',
            'title'       => 'Best Deals For This Week',
            'subtitle'    => 'Weekly Special',
            'sort_order'  => 120,
            'is_active'   => 1,
            'content'     => [
                'title'    => 'Best Deals For This Week',
                'subtitle' => 'Weekly Special',
                'limit'    => 6
            ]
        ],
        [
            'section_key' => 'about_us',
            'title'       => 'We Provide Best And Quality Gifts Box Product For You',
            'subtitle'    => 'About Us',
            'sort_order'  => 130,
            'is_active'   => 1,
            'content'     => [
                'title'            => 'We Provide Best And Quality Gifts Box Product For You',
                'subtitle'         => 'About Us',
                'about_text'       => 'We are standard text ever since the when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five but also the leap into electronic remaining essentially by injected humour unchanged.',
                'experience_years' => 30,
                'features'         => [
                    'Streamlined Shipping Experience',
                    'Affordable Modern Design',
                    'Competitive Price & Easy To Shop',
                    'We Made Awesome Products'
                ],
                'image'            => 'assets/img/about/about.jpg'
            ]
        ],
        [
            'section_key' => 'why_choose_us',
            'title'       => 'We Provide Premium Quality Gifts For You',
            'subtitle'    => 'Why Choose Us',
            'sort_order'  => 140,
            'is_active'   => 1,
            'content'     => [
                'title'    => 'We Provide Premium Quality Gifts For You',
                'subtitle' => 'Why Choose Us',
                'reasons'  => [
                    [
                        'icon' => 'flaticon-handshake',
                        'title' => 'Trusted Partner',
                        'description' => 'Over 10 years of reliable gifting service'
                    ],
                    [
                        'icon' => 'flaticon-wallet',
                        'title' => 'Affordable Price',
                        'description' => 'Best prices in the market without quality compromise'
                    ],
                    [
                        'icon' => 'flaticon-fast-delivery',
                        'title' => 'Free Shipping',
                        'description' => 'Free standard shipping on most items'
                    ]
                ]
            ]
        ],
        [
            'section_key' => 'testimonials',
            'title'       => 'What Our Client Say\'s About Us',
            'subtitle'    => 'Testimonials',
            'sort_order'  => 150,
            'is_active'   => 1,
            'content'     => [
                [
                    'name' => 'Sylvia H Green',
                    'role' => 'Client',
                    'text' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
                    'image' => 'assets/img/testimonial/01.jpg',
                    'rating' => 5
                ],
                [
                    'name' => 'Gordo Novak',
                    'role' => 'Client',
                    'text' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
                    'image' => 'assets/img/testimonial/02.jpg',
                    'rating' => 5
                ],
                [
                    'name' => 'Bessie Cooper',
                    'role' => 'Client',
                    'text' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.',
                    'image' => 'assets/img/testimonial/03.jpg',
                    'rating' => 5
                ]
            ]
        ],
        [
            'section_key' => 'photo_gallery',
            'title'       => 'Let\'s Check Our Photo Gallery',
            'subtitle'    => 'Our Gallery',
            'sort_order'  => 160,
            'is_active'   => 1,
            'content'     => [
                [ 'image' => 'assets/img/gallery/01.jpg', 'link' => '#' ],
                [ 'image' => 'assets/img/gallery/02.jpg', 'link' => '#' ],
                [ 'image' => 'assets/img/gallery/03.jpg', 'link' => '#' ],
                [ 'image' => 'assets/img/gallery/04.jpg', 'link' => '#' ],
                [ 'image' => 'assets/img/gallery/05.jpg', 'link' => '#' ],
                [ 'image' => 'assets/img/gallery/06.jpg', 'link' => '#' ]
            ]
        ],
        [
            'section_key' => 'blog',
            'title'       => 'Our Latest News & Blog',
            'subtitle'    => 'Our Blog',
            'sort_order'  => 170,
            'is_active'   => 1,
            'content'     => [
                'title'    => 'Our Latest News & Blog',
                'subtitle' => 'Our Blog',
                'articles' => [
                    [
                        'title' => 'Top 10 Gift Ideas For Your Partner',
                        'image' => 'assets/img/blog/01.jpg',
                        'date' => '07 July 2026',
                        'summary' => 'Discover the most romantic and thoughtful gift boxes to express your love on special occasions.',
                        'link' => 'blog'
                    ],
                    [
                        'title' => 'How To Choose The Perfect Cake',
                        'image' => 'assets/img/blog/02.jpg',
                        'date' => '06 July 2026',
                        'summary' => 'A complete guide on choosing the best flavor and size of cake for birthdays and weddings.',
                        'link' => 'blog'
                    ],
                    [
                        'title' => 'Why Personalized Gifts Mean More',
                        'image' => 'assets/img/blog/03.jpg',
                        'date' => '05 July 2026',
                        'summary' => 'Customized photo frames, keychains, and mugs create lasting memories that generic items can\'t match.',
                        'link' => 'blog'
                    ]
                ]
            ]
        ],
        [
            'section_key' => 'faq',
            'title'       => 'Frequently Asked Questions',
            'subtitle'    => 'FAQ',
            'sort_order'  => 180,
            'is_active'   => 1,
            'content'     => [
                [
                    'question' => 'Can I get my gift delivered today?',
                    'answer' => 'Yes, we offer same-day express delivery for cake and flower categories in supported cities.'
                ],
                [
                    'question' => 'Can I add a custom message or note with my gift?',
                    'answer' => 'Absolutely! During checkout, you can add a personalized message that will be printed on a card.'
                ],
                [
                    'question' => 'What payment methods are supported?',
                    'answer' => 'We accept all major credit cards, debit cards, UPI, net banking, and popular mobile wallets.'
                ],
                [
                    'question' => 'Is it possible to schedule a delivery date?',
                    'answer' => 'Yes, during checkout you can select a future delivery date of your choice.'
                ]
            ]
        ]
    ];

    // 4. Seed Data
    $stmt = $pdo->prepare("
        INSERT INTO `homepage_sections` (`section_key`, `title`, `subtitle`, `content_json`, `sort_order`, `is_active`)
        VALUES (:key, :title, :subtitle, :content, :order, :active)
        ON DUPLICATE KEY UPDATE 
            `title` = VALUES(`title`),
            `subtitle` = VALUES(`subtitle`),
            `content_json` = VALUES(`content_json`),
            `sort_order` = VALUES(`sort_order`),
            `is_active` = VALUES(`is_active`)
    ");
    
    foreach ($sections as $section) {
        $stmt->execute([
            ':key'     => $section['section_key'],
            ':title'   => $section['title'],
            ':subtitle'=> $section['subtitle'],
            ':content' => json_encode($section['content'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ':order'   => $section['sort_order'],
            ':active'  => $section['is_active']
        ]);
        echo "Seeded section: " . $section['section_key'] . "\n";
    }

    echo "Database initialization completed successfully!\n";

} catch (PDOException $e) {
    fwrite(STDERR, "Database Error: " . $e->getMessage() . "\n");
    exit(1);
}
