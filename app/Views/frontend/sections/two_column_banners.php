<?php
$banner1 = [
    'image' => 'assets/img/banner/mini-banner-1.jpg',
    'link' => 'shop'
];
$banner2 = [
    'image' => 'assets/img/banner/mini-banner-2.jpg',
    'link' => 'shop'
];

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $banner1 = $content['banner_1'] ?? $banner1;
    $banner2 = $content['banner_2'] ?? $banner2;
}
?>
<!-- two column promotional banners area -->
<div class="promotional-banners-area pb-80">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="promo-banner-wrap" style="border-radius: 20px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.05); position: relative; transition: all 0.3s;">
                    <a href="<?= base_url(esc($banner1['link'] ?? 'shop')) ?>" class="d-block w-100 h-100">
                        <img src="<?= base_url(esc($banner1['image'])) ?>" alt="<?= esc($banner1['alt'] ?? 'Promotion') ?>" style="width: 100%; height: auto; object-fit: cover; border-radius: 20px; display: block; transition: transform 0.5s ease;">
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="promo-banner-wrap" style="border-radius: 20px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.05); position: relative; transition: all 0.3s;">
                    <a href="<?= base_url(esc($banner2['link'] ?? 'shop')) ?>" class="d-block w-100 h-100">
                        <img src="<?= base_url(esc($banner2['image'])) ?>" alt="<?= esc($banner2['alt'] ?? 'Promotion') ?>" style="width: 100%; height: auto; object-fit: cover; border-radius: 20px; display: block; transition: transform 0.5s ease;">
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.promo-banner-wrap:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08) !important;
}
.promo-banner-wrap:hover img {
    transform: scale(1.02);
}
</style>
