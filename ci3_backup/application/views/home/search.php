<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="search-results-area py-5 bg-light">
    <div class="container">
        <div class="mb-4">
            <h3 class="fw-bold">Search Results</h3>
            <p class="text-muted">Showing results for "<span class="fw-semibold text-primary"><?= htmlspecialchars($keyword) ?></span>"</p>
        </div>

        <?php if (empty($products)): ?>
            <div class="card border-0 shadow-sm p-5 text-center rounded-3">
                <div class="card-body">
                    <div class="d-inline-flex bg-warning-subtle text-warning rounded-circle p-4 mb-3">
                        <i class="far fa-search fa-3x"></i>
                    </div>
                    <h4>No Products Found</h4>
                    <p class="text-muted mb-4">We couldn't find any products matching your search term. Try looking for "rose", "bouquet", or "cake".</p>
                    <a href="<?= base_url() ?>" class="btn btn-primary rounded-pill px-4">Go Back Home</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
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
        <?php endif; ?>
    </div>
</div>

<!-- AJAX Wishlist Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>
