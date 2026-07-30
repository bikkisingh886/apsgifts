<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- 404 Error Hero Section -->
    <div class="error-area py-4 py-md-5 bg-light">
        <div class="container py-2 py-md-4">
            <div class="row justify-content-center text-center">
                <div class="col-lg-7 col-md-9 col-12">
                    <div class="error-wrapper bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                        <div class="error-img mb-2">
                            <span class="display-3 fw-bold text-coral d-block" style="color: #e76f51; font-size: 4rem;">404</span>
                        </div>
                        <h2 class="fw-bold text-dark mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.6rem;">Oops! Page Not Found</h2>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 460px; font-size: 0.9rem; line-height: 1.5;">
                            We couldn't find the page you were looking for. It might have been removed, renamed, or is temporarily unavailable.
                        </p>

                        <!-- Search box (Single Row Flex Pill Bar) -->
                        <div class="mb-4 mx-auto" style="max-width: 480px; width: 100%;">
                            <form action="<?= base_url('search') ?>" method="get" class="w-100">
                                <div class="d-flex align-items-center bg-white p-1 rounded-pill shadow-sm" style="border: 2px solid #e76f51; overflow: hidden;">
                                    <input type="text" name="q" class="form-control border-0 bg-transparent px-3 py-2 shadow-none" placeholder="Search gifts, cakes, flowers..." style="font-size: 0.88rem; min-width: 0; flex: 1; outline: none;">
                                    <button type="submit" class="btn theme-btn rounded-pill px-3 px-md-4 py-2 d-flex align-items-center justify-content-center text-nowrap flex-shrink-0" style="background: #e76f51; color: #fff; border: none; font-size: 0.85rem; height: 38px;">
                                        <i class="far fa-search me-1"></i> <span>Search</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap justify-content-center gap-2 gap-md-3">
                            <a href="<?= base_url() ?>" class="btn theme-btn rounded-pill px-4 py-2" style="font-size: 0.88rem;">
                                <i class="far fa-home me-2"></i>Back to Homepage
                            </a>
                            <a href="<?= base_url('shop') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2" style="font-size: 0.88rem;">
                                <i class="far fa-shopping-bag me-2"></i>Browse Shop
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories Section (3 per row on Mobile, 8 per row on Desktop) -->
    <div class="py-4 py-md-5 bg-white border-top">
        <div class="container">
            <div class="text-center mb-4">
                <span class="text-coral text-uppercase fw-bold" style="color: #e76f51; font-size: 0.8rem; letter-spacing: 1px;">Explore Popular Categories</span>
                <h3 class="fw-bold text-dark mt-1 mb-1" style="font-family: 'Outfit', sans-serif; font-size: 1.4rem;">Looking for something special?</h3>
                <p class="text-muted small mb-0">Browse our top categories to find the perfect gift</p>
            </div>

            <div class="row cat-grid-404 g-2 g-md-3 justify-content-center">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <?php 
                        $imageUrl = !empty($cat['image_path']) ? base_url($cat['image_path']) : base_url('assets/img/product/default.png');
                        ?>
                        <div class="col text-center">
                            <a href="<?= get_category_url($cat) ?>" class="category-item-shortcut text-center text-decoration-none d-flex flex-column align-items-center">
                                <div class="category-img-box mb-2 shadow-sm d-flex align-items-center justify-content-center bg-white">
                                    <img src="<?= $imageUrl ?>" alt="<?= esc($cat['name']) ?>" class="img-fluid rounded-3">
                                </div>
                                <span class="category-title-label text-dark fw-bold" style="display: block; word-wrap: break-word; line-height: 1.2;"><?= esc($cat['name']) ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted py-3">No categories available.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</main>
<?= $this->endSection() ?>
