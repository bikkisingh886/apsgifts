<?php
$banners = [];
if (!empty($section['content_json'])) {
    $banners = json_decode($section['content_json'], true);
}
?>
<!-- small banner -->
<div class="small-banner pb-100">
    <div class="container wow fadeInUp" data-wow-delay=".25s">
        <div class="row g-4">
            <?php foreach ($banners as $index => $banner): ?>
                <?php
                // Divide into 3 columns automatically, or calculate column class based on count
                $colClass = 'col-12 col-md-6 col-lg-4';
                if (count($banners) == 2) {
                    $colClass = 'col-12 col-md-6';
                } elseif (count($banners) == 4) {
                    $colClass = 'col-12 col-md-6 col-lg-3';
                }
                ?>
                <div class="<?= $colClass ?>">
                    <div class="banner-item">
                        <img src="<?= base_url(esc($banner['image'])) ?>" alt="<?= esc($banner['alt'] ?? $banner['title'] ?? '') ?>">

                        <div class="banner-content">
                            <p><?= esc($banner['subtitle'] ?? 'Gift Box') ?></p>
                            <h3><?= $banner['title'] ?? '' ?></h3>
                            <a href="<?= base_url(esc($banner['link'] ?? 'shop')) ?>">Shop Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- small banner end -->
