<?php
$title = 'We Provide Premium Quality Gifts For You';
$subtitle = 'Why Choose Us';
$reasons = [
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
];

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $reasons = $content['reasons'] ?? $reasons;
}
?>
<!-- choose-area -->
<div class="choose-area bg py-100" style="background-color: #fafbfc;">
    <div class="container">
        <div class="row g-4 align-items-center mb-5 wow fadeInDown" data-wow-delay=".25s">
            <div class="col-lg-5 text-start" style="text-align: left !important;">
                <span class="site-title-tagline justify-content-start" style="margin-left: 0; color: #e76f51;">
                    <i class="fas fa-gift me-2"></i> <?= esc($subtitle) ?>
                </span>
                <h2 class="site-title" style="text-align: left !important; font-size: 2.3rem; font-weight: 800; color: #2d3748; line-height: 1.25;"><?= esc($title) ?></h2>
            </div>
            <div class="col-lg-3">
                <div class="choose-img-new shadow" style="border-radius: 20px; overflow: hidden; max-height: 250px;">
                    <img src="<?= base_url('assets/img/choose/01.jpg') ?>" alt="Why Choose Us" class="img-fluid w-100" style="object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-4 text-start">
                <p class="text-secondary" style="font-size: 1.02rem; line-height: 1.7; margin-bottom: 0;">Our mission is to help you convey your deepest emotions to your loved ones with perfect, premium, and hand-delivered gifts. We guarantee quality, freshness, and instant delivery to put a smile on their faces.</p>
            </div>
        </div>
        
        <div class="choose-content-new wow fadeInUp" data-wow-delay=".25s">
            <div class="row g-4">
                <?php foreach ($reasons as $reason): ?>
                    <?php
                    $iconClass = 'warranty.svg';
                    if (stripos($reason['title'], 'trust') !== false || stripos($reason['title'], 'partner') !== false) {
                        $iconClass = 'warranty.svg';
                    } elseif (stripos($reason['title'], 'price') !== false || stripos($reason['title'], 'afford') !== false) {
                        $iconClass = 'price.svg';
                    } elseif (stripos($reason['title'], 'ship') !== false || stripos($reason['title'], 'deliver') !== false || stripos($reason['title'], 'free') !== false) {
                        $iconClass = 'delivery.svg';
                    }
                    ?>
                    <div class="col-lg-4">
                        <div class="choose-card-new">
                            <div class="choose-icon-box-new">
                                <img src="<?= base_url('assets/img/icon/' . $iconClass) ?>" alt="<?= esc($reason['title']) ?>" style="width: 32px; height: 32px; transition: all 0.3s;">
                            </div>
                            <div class="choose-info-new">
                                <h4 class="fw-bold mb-2" style="color: #2d3748; font-size: 1.25rem; font-family: 'Inter', sans-serif;"><?= esc($reason['title']) ?></h4>
                                <p class="text-secondary m-0" style="font-size: 0.95rem; line-height: 1.6;"><?= esc($reason['description']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Premium Why Choose Us Cards */
.choose-card-new {
    background: #ffffff;
    border-radius: 18px;
    padding: 35px 30px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.02);
    border: 1px solid #f1f3f5;
    transition: all 0.35s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
    height: 100%;
}
.choose-card-new::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #e76f51, #f4a261);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}
.choose-card-new:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(231, 111, 81, 0.12) !important;
    border-color: #fdf0eb;
}
.choose-card-new:hover::after {
    transform: scaleX(1);
}
.choose-icon-box-new {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background-color: #fdf0eb;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    transition: background-color 0.3s, transform 0.3s;
}
.choose-card-new:hover .choose-icon-box-new {
    background-color: #e76f51;
    transform: rotateY(180deg);
}
.choose-card-new:hover .choose-icon-box-new img {
    filter: brightness(0) invert(1);
}
</style>
