<?php
$title = 'Let\'s Check Our Photo Gallery';
$subtitle = 'Our Gallery';
$images = [
    [ 'image' => 'assets/img/gallery/01.jpg', 'link' => '#' ],
    [ 'image' => 'assets/img/gallery/02.jpg', 'link' => '#' ],
    [ 'image' => 'assets/img/gallery/03.jpg', 'link' => '#' ],
    [ 'image' => 'assets/img/gallery/04.jpg', 'link' => '#' ],
    [ 'image' => 'assets/img/gallery/05.jpg', 'link' => '#' ],
    [ 'image' => 'assets/img/gallery/06.jpg', 'link' => '#' ]
];

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    if (isset($content[0])) {
        $images = $content;
    } else {
        $title = $content['title'] ?? $title;
        $subtitle = $content['subtitle'] ?? $subtitle;
        $images = $content['images'] ?? $images;
    }
}
?>
<!-- gallery-area -->
<div class="gallery-area py-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="site-heading text-center">
                    <span class="site-title-tagline"><?= esc($subtitle) ?></span>
                    <h2 class="site-title"><?= esc($title) ?></h2>
                </div>
            </div>
        </div>
        <div class="row g-4 popup-gallery">
            <?php foreach ($images as $index => $img): ?>
                <?php
                // Emulate the original CI grid sizes (e.g. column 5 is col-md-8 col-lg-6, others are col-md-4 col-lg-3)
                $colClass = 'col-md-4 col-lg-3';
                if ($index === 4) {
                    $colClass = 'col-md-8 col-lg-6';
                }
                ?>
                <div class="<?= $colClass ?>">
                    <div class="gallery-item wow fadeInUp" data-wow-delay=".25s">
                        <div class="gallery-img">
                            <img src="<?= base_url(esc($img['image'])) ?>" alt="<?= esc($img['alt'] ?? 'Gallery Image ' . ($index + 1)) ?>">

                            <a class="popup-img gallery-link" href="<?= base_url(esc($img['image'])) ?>"><i class="fal fa-plus"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- gallery-area end -->
