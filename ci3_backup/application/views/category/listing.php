<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="category-listing-area py-5 bg-light">
    <div class="container">
        <!-- Breadcrumb / Header -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category['name']) ?></li>
            </ol>
        </nav>

        <!-- Category Banner & Top Info -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 bg-white">
            <div class="row g-0 align-items-center">
                <div class="col-lg-8 p-4 p-md-5">
                    <h1 class="display-5 fw-bold text-dark mb-3"><?= htmlspecialchars($category['name']) ?></h1>
                    <?php if (!empty($category['summary'])): ?>
                        <p class="lead text-muted mb-0"><?= nl2br(htmlspecialchars($category['summary'])) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4 text-center bg-primary-subtle py-4 d-none d-lg-block" style="min-height: 200px;">
                    <i class="far fa-gift-card text-primary fa-5x opacity-50 mt-4"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Info Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h5 class="fw-bold m-0"><span class="text-primary"><?= count($products) ?></span> Products Found</h5>
            </div>
            
            <!-- Delivery Type Filters -->
            <div class="d-flex align-items-center gap-2 mt-3 mt-sm-0">
                <span class="text-muted small fw-semibold me-2">Delivery:</span>
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 active filter-btn" data-type="all">All</button>
                <button class="btn btn-outline-warning btn-sm rounded-pill px-3 filter-btn" data-type="Express"><i class="far fa-bolt"></i> Express Only</button>
                <button class="btn btn-outline-info btn-sm rounded-pill px-3 filter-btn" data-type="Courier"><i class="far fa-truck"></i> Courier Only</button>
            </div>
        </div>

        <!-- Product Grid -->
        <?php if (empty($products)): ?>
            <div class="card border-0 shadow-sm p-5 text-center rounded-3 mb-5">
                <div class="card-body">
                    <div class="d-inline-flex bg-light text-muted rounded-circle p-4 mb-3">
                        <i class="far fa-boxes fa-3x"></i>
                    </div>
                    <h4>No Products in Category</h4>
                    <p class="text-muted">Currently, there are no active products assigned to this category. Please check back later!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5" id="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="col product-item" data-delivery="<?= $product['delivery_type'] ?>">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative product-card bg-white">
                            
                            <!-- Wishlist Button -->
                            <?php 
                            $in_wishlist = FALSE;
                            if ($this->session->userdata('wishlist') && in_array($product['id'], $this->session->userdata('wishlist'))) {
                                $in_wishlist = TRUE;
                            }
                            ?>
                            <button class="btn btn-white btn-sm rounded-circle shadow-sm position-absolute end-0 top-0 m-3 z-3 wishlist-btn" data-product-id="<?= $product['id'] ?>">
                                <i class="<?= $in_wishlist ? 'fas' : 'far' ?> fa-heart text-danger"></i>
                            </button>
                            
                            <!-- Product Image -->
                            <div class="product-image-container bg-light text-center py-4 position-relative">
                                <?php if ($product['offer_value'] > 0): ?>
                                    <span class="badge bg-danger position-absolute start-0 top-0 m-3 rounded-pill">
                                        <?= $product['offer_type'] === 'percent' ? (int)$product['offer_value'] . '% OFF' : '₹' . (int)$product['offer_value'] . ' OFF' ?>
                                    </span>
                                <?php endif; ?>
                                
                                <a href="<?= base_url($product['slug']) ?>">
                                    <img src="<?= $product['image_path'] ? base_url($product['image_path']) : base_url('assets/img/product/18.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid" style="height: 180px; object-fit: contain;">
                                </a>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="card-body p-3.5">
                                <!-- Delivery Badge -->
                                <div class="mb-2">
                                    <?php if ($product['delivery_type'] === 'Express'): ?>
                                        <span class="badge badge-express"><i class="far fa-bolt"></i> Express</span>
                                    <?php else: ?>
                                        <span class="badge badge-courier"><i class="far fa-truck"></i> Courier</span>
                                    <?php endif; ?>
                                </div>
                                
                                <h6 class="fw-bold mb-2">
                                    <a href="<?= base_url($product['slug']) ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($product['name']) ?></a>
                                </h6>
                                
                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <div class="price-container">
                                        <?php if ($product['offer_value'] > 0): ?>
                                            <?php 
                                            $orig_price = (float)$product['price'];
                                            $disc_price = $product['offer_type'] === 'percent' ? $orig_price * (1 - $product['offer_value']/100) : $orig_price - $product['offer_value'];
                                            ?>
                                            <h5 class="m-0 fw-bold text-primary">₹<?= number_format($disc_price, 2) ?></h5>
                                            <span class="text-muted small text-decoration-line-through text-opacity-50">₹<?= number_format($orig_price, 2) ?></span>
                                        <?php else: ?>
                                            <h5 class="m-0 fw-bold text-primary">₹<?= number_format($product['price'], 2) ?></h5>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url($product['slug']) ?>" class="btn btn-primary btn-sm rounded-pill px-3">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- SEO Long-Form Footer Content -->
        <?php if (!empty($category['footer_content'])): ?>
            <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white mt-5">
                <div class="card-body">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">About <?= htmlspecialchars($category['name']) ?></h5>
                    <div class="seo-footer-text text-muted small" style="line-height: 1.6;">
                        <?= nl2br($category['footer_content']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- AJAX Wishlist & JS Listing Filtering Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle wishlist items using AJAX
        const wishlistButtons = document.querySelectorAll('.wishlist-btn');
        wishlistButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const heartIcon = this.querySelector('i');
                
                fetch('<?= base_url("wishlist/toggle") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `product_id=${productId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.action === 'added') {
                            heartIcon.classList.remove('far');
                            heartIcon.classList.add('fas');
                        } else {
                            heartIcon.classList.remove('fas');
                            heartIcon.classList.add('far');
                        }
                        
                        document.querySelectorAll('.wishlist-count-badge').forEach(badge => {
                            badge.textContent = data.count;
                        });
                    }
                })
                .catch(err => console.error(err));
            });
        });

        // Client-side filtering of products
        const filterBtns = document.querySelectorAll('.filter-btn');
        const productItems = document.querySelectorAll('.product-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filterType = this.getAttribute('data-type');

                productItems.forEach(item => {
                    const deliveryType = item.getAttribute('data-delivery');
                    if (filterType === 'all' || deliveryType === filterType) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
