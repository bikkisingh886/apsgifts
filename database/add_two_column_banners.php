<?php
try {
    $host = 'localhost';
    $dbName = 'giftshop';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if key already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM homepage_sections WHERE section_key = 'two_column_banners'");
    $stmt->execute();
    $exists = (int)$stmt->fetchColumn();

    if ($exists === 0) {
        $contentJson = json_encode([
            'banner_1' => [
                'image' => 'assets/img/banner/mini-banner-1.jpg',
                'link' => 'shop'
            ],
            'banner_2' => [
                'image' => 'assets/img/banner/mini-banner-2.jpg',
                'link' => 'shop'
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $insert = $pdo->prepare("INSERT INTO homepage_sections (section_key, title, subtitle, sort_order, is_active, content_json) VALUES (:key, :title, :subtitle, :sort, :active, :content)");
        $insert->execute([
            'key' => 'two_column_banners',
            'title' => 'Featured Deals',
            'subtitle' => 'Exclusive Promotions',
            'sort' => 75,
            'active' => 1,
            'content' => $contentJson
        ]);
        echo "Successfully inserted 'two_column_banners' section!\n";
    } else {
        echo "Section 'two_column_banners' already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
