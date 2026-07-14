<?php
$title = 'We Provide Best And Quality Gifts Box Product For You';
$subtitle = 'About Us';
$aboutText = 'We are standard text ever since the when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five but also the leap into electronic remaining essentially by injected humour unchanged.';
$experienceYears = 30;
$features = [
    'Streamlined Shipping Experience',
    'Affordable Modern Design',
    'Competitive Price & Easy To Shop',
    'We Made Awesome Products'
];
$image = 'assets/img/about/01.jpg';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $aboutText = $content['about_text'] ?? $aboutText;
    $experienceYears = $content['experience_years'] ?? $experienceYears;
    $features = $content['features'] ?? $features;
    $image = $content['image'] ?? $image;
}
?>
<!-- about area -->
<div class="about-area pb-120 mt-40">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-left wow fadeInLeft" data-wow-delay=".25s">
                    <div class="about-img">
                        <div class="img-1">
                            <img src="<?= base_url(esc($image)) ?>" alt="<?= esc($content['alt'] ?? 'About Image 1') ?>" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">

                        </div>
                        <img class="img-2" src="<?= base_url('assets/img/about/02.jpg') ?>" alt="About Image 2" style="border-radius: 15px; border: 5px solid #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
                        <img class="img-3" src="<?= base_url('assets/img/about/03.jpg') ?>" alt="About Image 3" style="border-radius: 15px; border: 5px solid #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
                    </div>
                    <div class="about-experience-new">
                        <div class="about-experience-icon-new">
                            <i class="fas fa-award text-white" style="font-size: 2.2rem;"></i>
                        </div>
                        <b class="text-white" style="font-size: 1.1rem; line-height: 1.4; display: block; font-family: 'Inter', sans-serif;"><?= (int)$experienceYears ?>+ Years Of<br>Experience</b>
                    </div>
                    <div class="about-shape">
                        <img src="<?= base_url('assets/img/shape/01.png') ?>" alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-right wow fadeInRight" data-wow-delay=".25s">
                    <div class="site-heading mb-4 text-start" style="text-align: left !important;">
                        <span class="site-title-tagline justify-content-start" style="margin-left:0; color: #e76f51;">
                            <i class="fas fa-heart me-2" style="color: #e76f51;"></i> <?= esc($subtitle) ?>
                        </span>
                        <h2 class="site-title" style="text-align: left !important; font-size: 2.5rem; font-weight: 800; color: #2d3748; line-height: 1.25;">
                            <?= $title ?>
                        </h2>
                    </div>
                    <p class="text-secondary" style="font-size: 1.05rem; line-height: 1.7; margin-bottom: 25px;">
                        <?= esc($aboutText) ?>
                    </p>
                    <div class="about-features-grid mt-4">
                        <?php foreach ($features as $feat): ?>
                            <div class="about-feature-item-new">
                                <div class="about-feature-icon-new">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <span class="about-feature-text-new fw-bold"><?= esc($feat) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= base_url('contact') ?>" class="btn btn-cyan btn-lg mt-4 text-white px-4 py-2" style="background-color: #e76f51; border: none; border-radius: 12px; font-weight: bold; transition: all 0.2s;">Discover More <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Premium About Us Section styling */
.about-features-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
@media (max-width: 575px) {
    .about-features-grid {
        grid-template-columns: 1fr;
    }
}
.about-feature-item-new {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border-left: 4px solid #e76f51;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.about-feature-item-new:hover {
    transform: translateX(6px);
    box-shadow: 0 10px 25px rgba(231, 111, 81, 0.08);
}
.about-feature-icon-new {
    color: #e76f51;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
}
.about-feature-text-new {
    color: #4a5568;
    font-size: 0.92rem;
}
.about-experience-new {
    position: absolute;
    bottom: 30px;
    right: 30px;
    background: linear-gradient(135deg, #e76f51, #f4a261);
    border-radius: 24px;
    padding: 22px 28px;
    box-shadow: 0 12px 35px rgba(231, 111, 81, 0.28);
    display: flex;
    align-items: center;
    gap: 18px;
    z-index: 5;
    animation: pulseGlow 3s infinite ease-in-out;
}
@keyframes pulseGlow {
    0% { box-shadow: 0 12px 35px rgba(231, 111, 81, 0.28); }
    50% { box-shadow: 0 12px 45px rgba(231, 111, 81, 0.45); }
    100% { box-shadow: 0 12px 35px rgba(231, 111, 81, 0.28); }
}
.about-experience-icon-new {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    width: 50px;
    height: 50px;
    border-radius: 12px;
}
</style>
