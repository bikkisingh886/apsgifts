<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="wishlist-page-area py-5 bg-light">
    <div class="container">
        <div class="mb-4">
            <h3 class="fw-bold"><i class="far fa-heart text-danger me-2"></i> My Wishlist</h3>
            <p class="text-muted">You have <span class="fw-semibold text-primary"><?= count($products) ?></span> items in your wishlist</p>
        </div>

        <?php if (empty($products)): ?>
            <div class="card border-0 shadow-sm p-5 text-center rounded-3">
                <div class="card-body">
                    <div class="d-inline-flex bg-danger-subtle text-danger rounded-circle p-4 mb-3">
                        <i class="far fa-heart fa-3x"></i>
                    </div>
                    <h4>Your Wishlist is Empty</h4>
                    <p class="text-muted mb-4">Start browsing our trending gifts and add your favorites to the wishlist!</p>
                    <a href="<?= base_url() ?>" class="btn btn-primary rounded-pill px-4">Browse Trending Gifts</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-align-middle mb-0 align-middle">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 120px;">Product</th>
                                    <th class="py-3">Name</th>
                                    <th class="py-3">Price</th>
                                    <th class="py-3">Delivery</th>
                                    <th class="py-3 text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <?php 
                                    $orig_price = (float)$product['price'];
                                    $disc_price = $orig_price;
                                    if ($product['offer_value'] > 0) {
                                        $disc_price = $product['offer_type'] === 'percent' ? $orig_price * (1 - $product['offer_value']/100) : $orig_price - $product['offer_value'];
                                    }
                                    ?>
                                    <tr id="wishlist-row-<?= $product['id'] ?>" class="border-bottom">
                                        <td class="ps-4 py-3">
                                            <img src="<?= $product['image_path'] ? base_url($product['image_path']) : base_url('assets/img/product/18.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded" style="width: 70px; height: 70px; object-fit: contain;">
                                        </td>
                                        <td class="py-3">
                                            <h6 class="fw-bold mb-1">
                                                <a href="<?= base_url($product['slug']) ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($product['name']) ?></a>
                                            </h6>
                                            <span class="text-muted small">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                                        </td>
                                        <td class="py-3">
                                            <span class="fw-bold text-primary">₹<?= number_format($disc_price, 2) ?></span>
                                            <?php if ($product['offer_value'] > 0): ?>
                                                <span class="text-muted small text-decoration-line-through d-block">₹<?= number_format($orig_price, 2) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3">
                                            <?php if ($product['delivery_type'] === 'Express'): ?>
                                                <span class="badge badge-express"><i class="far fa-bolt"></i> Express</span>
                                            <?php else: ?>
                                                <span class="badge badge-courier"><i class="far fa-truck"></i> Courier</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3 text-end pe-4">
                                            <form action="<?= base_url('cart/add') ?>" method="POST" class="d-inline-block me-2">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <input type="hidden" name="qty" value="1">
                                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold">Add to Cart</button>
                                            </form>
                                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 remove-wishlist-row-btn" data-product-id="<?= $product['id'] ?>">Remove</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-5">
                <a href="<?= base_url() ?>" class="btn btn-outline-primary rounded-pill px-4 fw-semibold"><i class="far fa-arrow-left me-2"></i> Continue Shopping</a>
                
                <!-- Action to add all to cart -->
                <form action="<?= base_url('cart/add') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <input type="hidden" name="add_all_wishlist" value="1">
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2.5 fw-bold shadow-sm">Add All to Cart</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- AJAX Wishlist Removal Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const removeButtons = document.querySelectorAll('.remove-wishlist-row-btn');
        removeButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const row = document.getElementById(`wishlist-row-${productId}`);
                
                fetch('<?= base_url("wishlist/toggle") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `product_id=${productId}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.action === 'removed') {
                        // Animate and remove row
                        row.style.transition = 'all 0.5s ease';
                        row.style.opacity = 0;
                        setTimeout(() => {
                            row.remove();
                            // Reload page if all items removed to show empty state
                            if (document.querySelectorAll('tbody tr').length === 0) {
                                window.location.reload();
                            }
                        }, 500);

                        // Update badges
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
