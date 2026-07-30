<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="site-breadcrumb">
        <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
        <div class="container">
            <div class="site-breadcrumb-wrap">
                <h4 class="breadcrumb-title"><?= esc($page_title ?? 'About Us') ?></h4>
                <ul class="breadcrumb-menu">
                    <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                    <li class="active"><?= esc($page_title ?? 'About Us') ?></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- about area -->
    <div class="about-area py-120">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-left wow fadeInLeft" data-wow-delay=".25s">
                        <div class="about-img">
                            <div class="img-1">
                                <img src="<?= base_url('assets/img/about/01.jpg') ?>" alt="About APSgifts">
                            </div>
                            <img class="img-2" src="<?= base_url('assets/img/about/02.jpg') ?>" alt="Gifts and Flowers">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-right wow fadeInRight" data-wow-delay=".25s">
                        <div class="site-heading mb-3">
                            <span class="site-title-tagline justify-content-start">
                                <i class="flaticon-drive"></i> About Us
                            </span>
                            <h2 class="site-title">
                                We Provide Best And Quality <span>Gifts & Flowers</span> For You
                            </h2>
                        </div>
                        <?php if (!empty($page_content)): ?>
                            <div class="page-body-content mb-4">
                                <?= $page_content ?>
                            </div>
                        <?php else: ?>
                            <p>
                                Welcome to APSgifts! We are your premier destination for extraordinary gifts, fresh flowers, delicious cakes, and customized gift hampers. Our mission is to bring joy to your celebrations with seamless same-day delivery across India.
                            </p>
                        <?php endif; ?>
                        <a href="<?= base_url('contact-us') ?>" class="theme-btn mt-3">Get In Touch <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- about area end -->

</main>
<?= $this->endSection() ?>
