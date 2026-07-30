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
        $slides = is_array($content) ? $content : [];
    }
}

// Fallback default slides if no slides are set in backend
if (empty($slides)) {
    $slides = [
        [
            'image' => 'assets/img/slider/hero-1.jpg',
            'link'  => 'shop',
            'alt'   => 'Hero Banner 1'
        ],
        [
            'image' => 'assets/img/slider/hero-2.jpg',
            'link'  => 'shop',
            'alt'   => 'Hero Banner 2'
        ]
    ];
}

if (!function_exists('resolve_hero_img')) {
    function resolve_hero_img($path) {
        if (empty($path)) return base_url('assets/img/slider/hero-1.jpg');
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) return $path;
        return base_url(ltrim($path, '/'));
    }
}

// Ensure categories are loaded if needed
if (!isset($categories)) {
    $categoryModel = new \App\Models\CategoryModel();
    $categories = $categoryModel->getWithProductCounts(true);
}
?>

<!-- hero slider -->
<div class="hero-section hs-1 mt-30 mb-4">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side Banner Ad -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="hero-banner" style="height: 480px; overflow: hidden; border-radius: 15px; background: #f8f9fa;">
                    <a href="<?= base_url(esc($sidebar['link'] ?? 'shop')) ?>" class="d-block w-100 h-100">
                        <img src="<?= resolve_hero_img($sidebar['image'] ?? 'assets/img/banner/hs-1-banner.jpg') ?>" alt="<?= esc($sidebar['alt'] ?? 'Ad Banner') ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px;" onerror="this.onerror=null; this.src='<?= base_url('assets/img/banner/hs-1-banner.jpg') ?>';">
                    </a>
                </div>
            </div>
            
            <!-- Main Slider -->
            <div class="col-lg-9 col-12">
                <div class="hero-slider owl-carousel owl-theme">
                    <?php foreach ($slides as $slide): ?>
                        <?php $imgUrl = resolve_hero_img($slide['image'] ?? ''); ?>
                        <div class="hero-single position-relative" style="background-image: url('<?= $imgUrl ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 480px; min-height: 480px; border-radius: 15px; overflow: hidden;">
                            <img src="<?= $imgUrl ?>" alt="<?= esc($slide['alt'] ?? 'Hero Banner') ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 15px; display: block;" onerror="this.onerror=null; this.src='<?= base_url('assets/img/slider/hero-1.jpg') ?>';">
                            <a href="<?= base_url(esc($slide['link'] ?? 'shop')) ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;" aria-label="<?= esc($slide['alt'] ?? 'Slide Link') ?>"></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- hero slider end -->
