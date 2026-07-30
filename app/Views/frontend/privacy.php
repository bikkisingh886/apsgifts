<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="site-breadcrumb">
        <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
        <div class="container">
            <div class="site-breadcrumb-wrap">
                <h4 class="breadcrumb-title"><?= esc($page_title ?? 'Privacy Policy') ?></h4>
                <ul class="breadcrumb-menu">
                    <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                    <li class="active"><?= esc($page_title ?? 'Privacy Policy') ?></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- privacy policy content -->
    <div class="py-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="terms-content shadow-sm p-4 p-md-5 rounded border bg-white">
                        <?php if (!empty($page_content)): ?>
                            <?= $page_content ?>
                        <?php else: ?>
                            <h2>Privacy Policy</h2>
                            <p>Privacy policy content will be updated soon from the backend admin panel.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- privacy policy end -->

</main>
<?= $this->endSection() ?>
