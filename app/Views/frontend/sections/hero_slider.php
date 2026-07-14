<?php
$slides = [];
$sidebar = [
    'image' => 'assets/img/banner/hs-1-banner.jpg',
    'link' => 'shop'
];

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    if (isset($content['slides'])) {
        $slides = $content['slides'];
        $sidebar = $content['sidebar_banner'] ?? $sidebar;
    } else {
        $slides = $content ?: [];
    }
}

// Ensure categories are loaded for the categories bar below the slider
if (!isset($categories)) {
    $categoryModel = new \App\Models\CategoryModel();
    $categories = $categoryModel->getWithProductCounts(true);
}
?>
<!-- Categories slider below hero slider image -->
<!--<div class="category-area pt-30">
    <div class="container">
        <div class="category-slider owl-carousel owl-theme">
            <?php /*foreach ($categories as $cat): ?>
                <?php
                $icon = 'gift-box.svg';
                if (stripos($cat['name'], 'flower') !== false) $icon = 'gift.svg';
                elseif (stripos($cat['name'], 'chocolate') !== false) $icon = 'gift-2.svg';
                elseif (stripos($cat['name'], 'anniversary') !== false) $icon = 'gift.svg';
                ?>
                <div class="category-item">
                    <a href="<?= get_category_url($cat) ?>">
                        <div class="category-info">
                            <div class="icon">
                                <img src="<?= base_url('assets/img/icon/' . $icon) ?>" alt="">
                            </div>
                            <div class="content">
                                <h4><?= esc($cat['name']) ?></h4>
                                <p><?= $cat['product_count'] ?> Items</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach;*/ ?>
        </div>
    </div>
</div> -->
<!-- Categories slider end -->
<!-- hero slider -->
<div class="hero-section hs-1 mt-30 mb-4">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side Banner Ad -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="hero-banner" style="height: 480px; overflow: hidden; border-radius: 15px;">
                    <a href="<?= base_url(esc($sidebar['link'] ?? 'shop')) ?>" class="d-block w-100 h-100">
                        <img src="<?= base_url(esc($sidebar['image'] ?? 'assets/img/banner/hs-1-banner.jpg')) ?>" alt="<?= esc($sidebar['alt'] ?? 'Ad Banner') ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;">
                    </a>
                </div>
            </div>
            
            <!-- Main Slider -->
            <div class="col-lg-9 col-12">
                <div class="hero-slider owl-carousel owl-theme">
                    <?php foreach ($slides as $slide): ?>
                        <div class="hero-single" style="background-image: url(<?= base_url(esc($slide['image'])) ?>); height: 480px; min-height: 480px; position: relative; border-radius: 15px;">
                            <a href="<?= base_url(esc($slide['link'] ?? 'shop')) ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;" aria-label="<?= esc($slide['alt'] ?? 'Slide Link') ?>"></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- hero slider end -->
