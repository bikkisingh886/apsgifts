<?php
$title = 'What makes us different check our video';
$subtitle = 'Latest Video';
$videoUrl = 'https://www.youtube.com/watch?v=ckHzmP1evNU';
$image = 'assets/img/video/01.jpg';
$description = 'There are many variations of passages available but the majority have suffered alteration in some form by injected humour randomised words which don\'t look even slightly you are going to use a passage believable.';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $videoUrl = $content['video_url'] ?? $videoUrl;
    $image = $content['image'] ?? $image;
    $description = $content['description'] ?? $description;
}
?>
<!-- video area -->
<div class="video-area py-100">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-4">
                <div class="site-heading mb-0 wow fadeInLeft" data-wow-delay=".25s">
                    <span class="site-title-tagline"><?= esc($subtitle) ?></span>
                    <h2 class="site-title"><?= $title ?></h2>
                    <p><?= esc($description) ?></p>
                    <a href="<?= base_url('shop') ?>" class="theme-btn mt-20">Shop Now <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="video-content wow fadeInRight" data-wow-delay=".25s" style="background-image: url(<?= base_url(esc($image)) ?>);">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="video-wrapper">
                                <a class="play-btn popup-youtube" href="<?= esc($videoUrl) ?>">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- video area end -->
