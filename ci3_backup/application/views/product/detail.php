<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="product-detail-page py-5 bg-light">
    <div class="container">
        <!-- Back navigation -->
        <div class="mb-4">
            <a href="javascript:history.back()" class="text-decoration-none text-secondary fw-semibold">
                <i class="far fa-arrow-left me-1"></i> Back to Products
            </a>
        </div>

        <div class="row">
            <!-- Left Side: Product Image Carousel -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php if (empty($product['images'])): ?>
                                <div class="carousel-item active">
                                    <img src="<?= base_url('assets/img/product/18.png') ?>" alt="Default product image" class="img-fluid" style="max-height: 400px; object-fit: contain;">
                                </div>
                            <?php else: ?>
                                <?php foreach ($product['images'] as $index => $img): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <img src="<?= base_url($img['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid" style="max-height: 400px; object-fit: contain;">
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (count($product['images']) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Side: Product Info & Purchase Form -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
                    <div class="card-body p-0">
                        <!-- Category breadcrumb -->
                        <span class="text-primary small text-uppercase fw-bold">
                            <?php if (!empty($product['categories'])): ?>
                                <?= htmlspecialchars(implode(', ', array_column($product['categories'], 'name'))) ?>
                            <?php endif; ?>
                        </span>

                        <h2 class="fw-bold text-dark mt-2 mb-3"><?= htmlspecialchars($product['name']) ?></h2>
                        
                        <!-- Pricing Details -->
                        <div class="d-flex align-items-center mb-3">
                            <?php 
                            $orig_price = (float)$product['price'];
                            $disc_price = $orig_price;
                            if ($product['offer_value'] > 0) {
                                $disc_price = $product['offer_type'] === 'percent' ? $orig_price * (1 - $product['offer_value']/100) : $orig_price - $product['offer_value'];
                            }
                            ?>
                            <h3 class="fw-bold text-primary m-0 me-3">₹<?= number_format($disc_price, 2) ?></h3>
                            <?php if ($product['offer_value'] > 0): ?>
                                <h5 class="text-muted text-decoration-line-through m-0 me-3">₹<?= number_format($orig_price, 2) ?></h5>
                                <span class="badge bg-danger rounded-pill">
                                    <?= $product['offer_type'] === 'percent' ? (int)$product['offer_value'] . '% OFF' : '₹' . (int)$product['offer_value'] . ' OFF' ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Special Offer Promo -->
                        <div class="alert alert-success border-0 rounded-3 mb-4 py-2 px-3 small d-flex align-items-center">
                            <i class="far fa-tags text-success me-2"></i> <span>Get <strong>10% off</strong> on orders above ₹999</span>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary">Description</h6>
                            <p class="text-muted small" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        </div>

                        <hr class="my-4">

                        <!-- Cart Form -->
                        <form action="<?= base_url('cart/add') ?>" method="POST">
                            <!-- CSRF Token -->
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                            <!-- Delivery Logic Section (as defined on page 2 & 9 of the PDF) -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-secondary mb-2">Delivery Option</h6>
                                
                                <?php if ($product['delivery_type'] === 'Express'): ?>
                                    <!-- Express Delivery Date Selection -->
                                    <div class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="far fa-bolt text-warning fs-5 me-2"></i>
                                            <span class="fw-bold text-warning-emphasis">Express Delivery — Choose Date</span>
                                        </div>
                                        <label for="delivery_date" class="form-label small text-muted">Delivery date:</label>
                                        <select name="delivery_date" id="delivery_date" class="form-select bg-white" required>
                                            <?php foreach ($delivery_dates as $date): ?>
                                                <option value="<?= $date['value'] ?>"><?= htmlspecialchars($date['label']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php else: ?>
                                    <!-- Courier Delivery Static Estimate -->
                                    <div class="p-3 bg-info-subtle rounded-3 border border-info-subtle mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-truck text-info fs-5 me-2"></i>
                                            <span class="fw-bold text-info-emphasis">Courier Delivery</span>
                                        </div>
                                        <p class="mb-0 mt-2 text-muted small">Estimated delivery in <strong>7 working days</strong> (Estimated Delivery: <?= htmlspecialchars($courier_eta) ?>).</p>
                                        <input type="hidden" name="delivery_date" value="<?= date('Y-m-d', strtotime('+7 weekdays')) ?>">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Quantity and Buttons -->
                            <div class="row align-items-center g-3">
                                <div class="col-sm-4 col-md-3">
                                    <label for="qty" class="form-label small text-muted">Qty:</label>
                                    <input type="number" name="qty" id="qty" class="form-control" value="1" min="1" max="10" required>
                                </div>
                                <div class="col-sm-8 col-md-9 d-flex gap-2 align-self-end">
                                    <!-- Add to Cart -->
                                    <button type="submit" class="btn btn-danger py-2.5 px-4 rounded-pill fw-bold shadow-sm flex-grow-1">Add to Cart</button>
                                    
                                    <!-- Wishlist AJAX Button -->
                                    <?php 
                                    $in_wishlist = FALSE;
                                    if ($this->session->userdata('wishlist') && in_array($product['id'], $this->session->userdata('wishlist'))) {
                                        $in_wishlist = TRUE;
                                    }
                                    ?>
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 wishlist-btn-detail" data-product-id="<?= $product['id'] ?>">
                                        <i class="<?= $in_wishlist ? 'fas' : 'far' ?> fa-heart text-danger"></i> <span class="d-none d-lg-inline">Wishlist</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="mt-4 text-muted small">
                            <span>SKU: <strong class="text-dark"><?= htmlspecialchars($product['sku']) ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AJAX Wishlist Detail Handling Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wishlistBtn = document.querySelector('.wishlist-btn-detail');
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', function(e) {
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
        }
    });
</script>
