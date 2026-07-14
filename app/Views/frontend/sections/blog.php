<?php
$title = 'Our Latest News & Blog';
$subtitle = 'Our Blog';
$articles = [
    [
        'title' => 'Top 10 Gift Ideas For Your Partner',
        'image' => 'assets/img/blog/01.jpg',
        'date' => '07 July 2026',
        'summary' => 'Discover the most romantic and thoughtful gift boxes to express your love on special occasions.',
        'link' => 'blog'
    ],
    [
        'title' => 'How To Choose The Perfect Cake',
        'image' => 'assets/img/blog/02.jpg',
        'date' => '06 July 2026',
        'summary' => 'A complete guide on choosing the best flavor and size of cake for birthdays and weddings.',
        'link' => 'blog'
    ],
    [
        'title' => 'Why Personalized Gifts Mean More',
        'image' => 'assets/img/blog/03.jpg',
        'date' => '05 July 2026',
        'summary' => 'Customized photo frames, keychains, and mugs create lasting memories that generic items can\'t match.',
        'link' => 'blog'
    ]
];

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $articles = $content['articles'] ?? $articles;
}
?>
<!-- blog area -->
<div class="blog-area py-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="site-heading text-center">
                    <span class="site-title-tagline"><?= esc($subtitle) ?></span>
                    <h2 class="site-title"><?= esc($title) ?></h2>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach ($articles as $art): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="blog-item wow fadeInUp" data-wow-delay=".25s">
                        <div class="blog-item-img">
                            <img src="<?= base_url(esc($art['image'])) ?>" alt="<?= esc($art['alt'] ?? $art['title']) ?>">

                            <span class="blog-date"><i class="far fa-calendar-alt"></i> <?= esc($art['date'] ?? '') ?></span>
                        </div>
                        <div class="blog-item-info">
                            <div class="blog-item-meta">
                                <ul>
                                    <li><a href="#"><i class="far fa-user-circle"></i> By Admin</a></li>
                                    <li><a href="#"><i class="far fa-comments"></i> 25 Comments</a></li>
                                </ul>
                            </div>
                            <h4 class="blog-title">
                                <a href="<?= base_url(esc($art['link'] ?? 'blog')) ?>"><?= esc($art['title']) ?></a>
                            </h4>
                            <p><?= esc($art['summary']) ?></p>
                            <a class="theme-btn" href="<?= base_url(esc($art['link'] ?? 'blog')) ?>">Read More<i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- blog area end -->
