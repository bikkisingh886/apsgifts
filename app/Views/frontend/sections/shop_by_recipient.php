<?php
$db = \Config\Database::connect();
$categoryIds = [];
$title = 'Shop By Recipient';
$subtitle = 'Recipient';
$view_more_link = 'category';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $categoryIds = $content['category_ids'] ?? [];
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $view_more_link = $content['view_more_link'] ?? $view_more_link;
}

$categories = [];
if (!empty($categoryIds)) {
    $categories = $db->table('categories c')
        ->select('c.id, c.name, c.slug, c.image_path, c.summary, COUNT(pc.product_id) as product_count')
        ->join('product_categories pc', 'pc.category_id = c.id', 'left')
        ->whereIn('c.id', $categoryIds)
        ->where('c.is_active', 1)
        ->groupBy('c.id')
        ->get()
        ->getResultArray();
}
?>
<!-- shop by recipient -->
<div class="category-area pb-80">
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
                    <a href="<?= base_url(esc($view_more_link)) ?>" class="theme-btn btn-sm" style="background-color: #ff3366; color: white; border: none; border-radius: 10px; padding: 8px 20px; font-weight: bold; text-decoration: none;">View More <i class="fas fa-arrow-right ms-1"></i></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4 justify-content-center mobile-horizontal-scroll">
            <?php if (empty($categories)): ?>
                <div class="col-12 text-center text-muted"><p>No recipients configured.</p></div>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $imageUrl = $cat['image_path'] ? base_url($cat['image_path']) : base_url('assets/img/category/02.jpg');
                    
                    // Determine recipient pastel badge background and emoji icon
                    $iconHtml = '🎁';
                    $badgeBg = '#eef3ff'; // Light lavender blue

                    $nameLower = strtolower($cat['name']);
                    if (strpos($nameLower, 'her') !== false || strpos($nameLower, 'wife') !== false || strpos($nameLower, 'gf') !== false || strpos($nameLower, 'girlfriend') !== false || strpos($nameLower, 'mother') !== false || strpos($nameLower, 'sister') !== false) {
                        $iconHtml = '👩';
                        $badgeBg = '#ffeef2'; // Soft pink
                    } elseif (strpos($nameLower, 'him') !== false || strpos($nameLower, 'husband') !== false || strpos($nameLower, 'bf') !== false || strpos($nameLower, 'boyfriend') !== false || strpos($nameLower, 'father') !== false || strpos($nameLower, 'brother') !== false) {
                        $iconHtml = '👨';
                        $badgeBg = '#e6fafc'; // Soft cyan
                    } elseif (strpos($nameLower, 'kid') !== false || strpos($nameLower, 'child') !== false || strpos($nameLower, 'baby') !== false) {
                        $iconHtml = '👶';
                        $badgeBg = '#fff8dc'; // Soft cream
                    } elseif (strpos($nameLower, 'friend') !== false) {
                        $iconHtml = '🤝';
                        $badgeBg = '#f0fff0'; // Light green
                    }
                    
                    $summary = !empty($cat['summary']) ? $cat['summary'] : 'Explore curated gifts for ' . esc($cat['name']) . ' online.';
                    ?>
                    <div class="col d-flex align-items-stretch">
                        <div class="custom-card-item w-100">
                            <!-- Image Area -->
                            <div class="card-img-wrap">
                                <a href="<?= get_category_url($cat) ?>" class="d-block">
                                    <img src="<?= $imageUrl ?>" alt="<?= esc($cat['name']) ?>" style="height: 180px;">
                                </a>
                            </div>
                            
                            <!-- Content Area -->
                            <div class="card-body-content" style="padding-top: 20px;">
                                <!-- Dynamic category emoji badge overlapping image/content -->
                                <div class="card-icon-badge" style="background: <?= $badgeBg ?>;">
                                    <?= $iconHtml ?>
                                </div>
                                <h4 class="card-title-text" style="font-size: 1.1rem;">
                                    <a href="<?= get_category_url($cat) ?>"><?= esc($cat['name']) ?></a>
                                </h4>
                                <p class="card-desc-text" style="height: 38px;"><?= esc(mb_strimwidth($summary, 0, 70, '...')) ?></p>
                                
                                <div class="card-footer-info">
                                    <span class="text-muted small" style="font-weight: 500;"><?= (int)$cat['product_count'] ?> Products</span>
                                    <a href="<?= get_category_url($cat) ?>" class="card-action-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- shop by recipient end -->
