<?php
$productModel = new \App\Models\ProductModel();
$limit = 6;
$title = 'Best Deals For This Week';
$subtitle = 'Weekly Special';
$countdownDate = '2026/12/30'; // Far future default
$view_more_link = 'shop';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $limit = $content['limit'] ?? $limit;
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $countdownDate = $content['countdown_date'] ?? $countdownDate;
    $view_more_link = $content['view_more_link'] ?? $view_more_link;
}

// Fetch products with active offers or fallback to on sale
$db = \Config\Database::connect();
$cityId = session('selected_city_id');
$builder = $db->table('products p')
    ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
    ->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'inner') // Only products with active offers
    ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
    ->where('p.is_active', 1)
    ->where('p.hide_from_frontend', 0)
    ->limit($limit)
    ->orderBy('p.id', 'DESC');
    
if ($cityId) {
    $builder->select('COALESCE(pcit.price_override, p.price) as price')
        ->join('product_cities pcit', 'pcit.product_id = p.id AND pcit.city_id = ' . (int)$cityId, 'left');
}

$products = $builder->get()->getResultArray();

if (empty($products)) {
    // fallback to generic on-sale products
    $products = $productModel->getOnSale($limit);
}
?>
<!-- deal area -->
<div class="deal-area deal-bg deal-negative py-80">
    <div class="container">
        <!-- Flex Header -->
        <div class="row align-items-center mb-4">
            <div class="col-8">
                <div class="site-heading mb-0 text-start" style="text-align: left !important;">
                    <span class="site-title-tagline text-white" style="margin-left: 0; justify-content: flex-start; opacity: 0.8;"><?= esc($subtitle) ?></span>
                    <h2 class="site-title text-white" style="text-align: left !important;"><?= esc($title) ?></h2>
                </div>
            </div>
            <div class="col-4 text-end">
                <?php if (!empty($view_more_link)): ?>
                    <a href="<?= base_url(esc($view_more_link)) ?>" class="theme-btn btn-sm" style="background-color: #ff3366; color: white; border: none; border-radius: 10px; padding: 8px 20px; font-weight: bold; text-decoration: none;">View More <i class="fas fa-arrow-right ms-1"></i></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="product-wrap wow fadeInUp" data-wow-delay=".25s">
            <div class="col-lg-5 mx-auto mb-4">
                <div class="deal-countdown">
                    <div class="countdown" data-countdown="<?= esc($countdownDate) ?>"></div>
                </div>
            </div>
            <div class="product-slider owl-carousel owl-theme">
                <?php foreach ($products as $product): ?>
                    <?= view('frontend/sections/_product_card_single', ['product' => $product]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- deal area end -->
