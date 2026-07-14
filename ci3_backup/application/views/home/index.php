<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Hero Section -->
<div class="hero-section py-5 bg-primary-subtle text-primary-emphasis position-relative overflow-hidden mb-5">
    <div class="container py-md-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-extrabold mb-3">Find Perfect Gifts for Your Loved Ones</h1>
                <p class="lead mb-4">Explore our wide selection of fresh flowers, delicious cakes, custom frames, and hampers with same-day express delivery.</p>
                <div class="d-flex gap-2">
                    <a href="#trending-gifts" class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold">Shop Now</a>
                    <a href="#categories" class="btn btn-outline-primary btn-lg rounded-pill px-4 fw-semibold">View Categories</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?= base_url('assets/img/product/18.png') ?>" alt="Gifts hero image" class="img-fluid hero-img rounded-4" style="max-height: 400px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));">
            </div>
        </div>
    </div>
</div>

<!-- Shop by Category Section -->
<div class="category-section py-4 mb-5" id="categories">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Shop by Category</h2>
            <p class="text-muted">Handpicked gift collections curated for every special moment</p>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-4 justify-content-center">
            <?php foreach ($categories as $cat): ?>
                <div class="col">
                    <a href="<?= base_url('category/' . $cat['slug']) ?>" class="card text-center border-0 shadow-sm rounded-4 h-100 text-decoration-none transition-all p-3 hover-lift">
                        <div class="card-body">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle p-3 mb-3" style="width: 60px; height: 60px;">
                                <i class="far fa-gift fa-lg"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($cat['name']) ?></h5>
                            <p class="text-muted small mb-0"><?= $cat['product_count'] ?> Products</p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Trending Gifts Section -->
<div class="trending-section py-4 mb-5" id="trending-gifts">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary mb-2 rounded-pill px-3 py-2 fw-bold">Trending Gifts</span>
            <h2 class="fw-bold">Most Popular Selections</h2>
            <p class="text-muted">Explore the top gifts our customers are loving right now</p>
        </div>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($trending_products as $product): ?>
                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative product-card">
                        
                        <!-- Wishlist Toggle (AJAX) -->
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
                                        <span class="text-muted small text-decoration-line-through">₹<?= number_format($orig_price, 2) ?></span>
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
    </div>
</div>

<!-- AJAX Wishlist Handling Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle wishlist items using AJAX
        const wishlistButtons = document.querySelectorAll('.wishlist-btn');
        wishlistButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const heartIcon = this.querySelector('i');
                
                // Perform POST request
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
                        
                        // Update the badge counts globally
                        document.querySelectorAll('.wishlist-count-badge').forEach(badge => {
                            badge.textContent = data.count;
                        });
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>
