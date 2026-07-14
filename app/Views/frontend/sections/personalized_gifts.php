<?php
$db = \Config\Database::connect();
$limit = 15; // exactly 15 products
$title = 'Create Your Own Unique Gifts ✨ 🎁';
$subtitle = 'Personalized with love, made just for you 💖';
$categoryId = 12; // Personalised Gifts Category
$view_more_link = 'personalised-gifts';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $categoryId = $content['category_id'] ?? $categoryId;
    $view_more_link = $content['view_more_link'] ?? $view_more_link;
}

$cityId = session('selected_city_id');
$builder = $db->table('products p')
    ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
    ->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
    ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
    ->join('product_categories pc', 'pc.product_id = p.id')
    ->groupStart()
        ->where('pc.category_id', $categoryId)
        ->orWhere('p.is_customizable', 1)
    ->groupEnd()
    ->where('p.is_active', 1)
    ->where('p.hide_from_frontend', 0)
    ->orderBy('RAND()') // Random sorting
    ->limit($limit); // 15 products

if ($cityId) {
    $builder->select('COALESCE(pcit.price_override, p.price) as price')
        ->join('product_cities pcit', 'pcit.product_id = p.id AND pcit.city_id = ' . (int)$cityId, 'left');
}

$products = $builder->get()->getResultArray();
?>
<!-- personalized gifts area -->
<div class="product-area pb-100">
    <div class="container">
        <!-- Flex Header -->
        <div class="row align-items-center mb-4">
            <div class="col-8">
                <div class="site-heading mb-0 text-start" style="text-align: left !important;">
                    <span class="site-title-tagline" style="margin-left: 0; justify-content: flex-start;"><?= esc($subtitle) ?></span>
                    <h2 class="site-title" style="text-align: left !important;"><?= esc($title) ?></h2>
                </div>
            </div>
            <div class="col-4 text-end">
                <?php if (!empty($view_more_link)): ?>
                    <a href="<?= base_url(esc($view_more_link)) ?>" class="theme-btn btn-sm" style="background-color: #ff3366; color: white; border: none; border-radius: 10px; padding: 8px 20px; font-weight: bold; text-decoration: none;">View All <i class="fas fa-arrow-right ms-1"></i></a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Full width slider -->
        <div class="row">
            <div class="col-12">
                <div class="product-wrap wow fadeInUp" data-wow-delay=".25s">
                    <div class="product-slider owl-carousel owl-theme">
                        <?php if (empty($products)): ?>
                            <div class="product-item p-4 text-center text-muted">No customized gifts found.</div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <?= view('frontend/sections/_product_card_single', ['product' => $product]) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
