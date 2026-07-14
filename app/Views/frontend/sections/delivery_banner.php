<?php
$title = 'Same Day & Midnight Delivery Across India';
$subtitle = 'Express Shipping';
$image = 'assets/img/banner/big-banner.jpg'; // fallback
$link = 'shop';
$buttonText = 'Order Now';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $image = $content['image'] ?? $image;
    $link = $content['link'] ?? $link;
    $buttonText = $content['button_text'] ?? $buttonText;
}
?>
<!-- delivery banner -->
<div class="big-banner pb-100">
    <div class="container wow fadeInUp" data-wow-delay=".25s">
        <div class="banner-wrap" style="background-image: url(<?= base_url(esc($image)) ?>); background-size: cover; background-position: center; border-radius: 12px; overflow: hidden; padding: 80px 0;">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="banner-content text-center" style="background: rgba(0, 0, 0, 0.4); padding: 40px; border-radius: 8px;">
                        <div class="banner-info">
                            <h6 class="text-uppercase text-cyan fw-bold tracking-wider mb-2" style="color: #00bcd4 !important; font-size: 0.9rem;"><?= esc($subtitle) ?></h6>
                            <h2 class="text-white fw-bold mb-3" style="font-size: 2.2rem;"><?= esc($title) ?></h2>
                            <p class="text-white-50 mb-4">Express service to deliver smiles on time, every time.</p>
                        </div>
                        <a href="<?= base_url(esc($link)) ?>" class="theme-btn" style="background: #00bcd4; border-color: #00bcd4;"><?= esc($buttonText) ?><i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- delivery banner end -->
