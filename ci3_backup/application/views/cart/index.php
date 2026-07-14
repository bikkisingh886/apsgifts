<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="cart-page py-5 bg-light">
    <div class="container">
        <div class="mb-4">
            <h3 class="fw-bold"><i class="far fa-shopping-bag text-primary me-2"></i> Cart</h3>
            <p class="text-muted">Review your items and selected delivery dates</p>
        </div>

        <?php if (empty($cart)): ?>
            <div class="card border-0 shadow-sm p-5 text-center rounded-3">
                <div class="card-body">
                    <div class="d-inline-flex bg-primary-subtle text-primary rounded-circle p-4 mb-3">
                        <i class="far fa-shopping-cart fa-3x"></i>
                    </div>
                    <h4>Your Cart is Empty</h4>
                    <p class="text-muted mb-4">You have no items in your shopping cart. Add some gifts to get started!</p>
                    <a href="<?= base_url() ?>" class="btn btn-primary rounded-pill px-4">Continue Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Left: Cart Items List -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-align-middle align-middle mb-0">
                                    <thead class="bg-light text-secondary small text-uppercase">
                                        <tr>
                                            <th class="ps-4 py-3" style="width: 100px;">Product</th>
                                            <th class="py-3">Details</th>
                                            <th class="py-3">Price</th>
                                            <th class="py-3" style="width: 120px;">Qty</th>
                                            <th class="py-3">Total</th>
                                            <th class="py-3 text-end pe-4">Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $item): ?>
                                            <tr id="cart-row-<?= $item['id'] ?>" class="border-bottom">
                                                <td class="ps-4 py-3">
                                                    <img src="<?= $item['image'] ? base_url($item['image']) : base_url('assets/img/product/18.png') ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="img-fluid rounded" style="width: 70px; height: 70px; object-fit: contain;">
                                                </td>
                                                <td class="py-3">
                                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                                    <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                                        <?php if ($item['delivery_type'] === 'Express'): ?>
                                                            <span class="badge badge-express small"><i class="far fa-bolt"></i> Express</span>
                                                            <span class="text-muted small">Date: <?= date('d M Y', strtotime($item['delivery_date'])) ?></span>
                                                        <?php else: ?>
                                                            <span class="badge badge-courier small"><i class="far fa-truck"></i> Courier</span>
                                                            <span class="text-muted small">Est: <?= date('d M Y', strtotime($item['delivery_date'])) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="fw-semibold">₹<?= number_format($item['price'], 2) ?></span>
                                                </td>
                                                <td class="py-3">
                                                    <input type="number" class="form-control qty-input" value="<?= $item['qty'] ?>" min="1" max="10" data-id="<?= $item['id'] ?>">
                                                </td>
                                                <td class="py-3">
                                                    <span class="fw-bold text-primary" id="item-total-<?= $item['id'] ?>">₹<?= number_format($item['price'] * $item['qty'], 2) ?></span>
                                                </td>
                                                <td class="py-3 text-end pe-4">
                                                    <a href="<?= base_url('cart/remove/' . $item['id']) ?>" class="btn btn-sm btn-outline-danger rounded-circle"><i class="far fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="<?= base_url() ?>" class="btn btn-outline-primary rounded-pill px-4 fw-semibold"><i class="far fa-arrow-left me-2"></i> Continue Shopping</a>
                        <a href="<?= base_url('cart/clear') ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold"><i class="far fa-trash me-2"></i> Clear Cart</a>
                    </div>
                </div>

                <!-- Right: Order Summary Card -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                        <h5 class="fw-bold mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3.5">
                            <span class="text-secondary small">Subtotal</span>
                            <span class="fw-semibold text-dark text-end" id="subtotal-val">₹<?= number_format($subtotal, 2) ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3.5" id="discount-section" style="<?= $discount > 0 ? '' : 'display: none !important;' ?>">
                            <span class="text-secondary small">Offer applied (10% off)</span>
                            <span class="fw-semibold text-danger text-end" id="discount-val">-₹<?= number_format($discount, 2) ?></span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold">Total</span>
                            <h4 class="fw-extrabold text-primary m-0 text-end" id="total-val">₹<?= number_format($total, 2) ?></h4>
                        </div>

                        <?php if ($subtotal <= 999): ?>
                            <div class="alert alert-warning border-0 rounded-3 mb-4 py-2 px-3 small" id="promo-notice">
                                <i class="far fa-gift me-1"></i> Add items worth <strong>₹<?= number_format(1000 - $subtotal, 2) ?></strong> more to get flat <strong>10% off</strong>!
                            </div>
                        <?php endif; ?>

                        <div class="alert alert-info border-0 rounded-3 mb-4 py-2 px-3 small">
                            <i class="far fa-info-circle me-1"></i> Payment on delivery or manual UPI accepted at checkout.
                        </div>

                        <a href="<?= base_url('checkout') ?>" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm py-3 mt-2">Place Order →</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- AJAX Cart updates -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qtyInputs = document.querySelectorAll('.qty-input');
        
        qtyInputs.forEach(input => {
            input.addEventListener('change', function() {
                const productId = this.getAttribute('data-id');
                const qty = this.value;
                
                if (qty < 1) {
                    this.value = 1;
                    return;
                }
                
                // Call update endpoint
                fetch('<?= base_url("cart/update") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `product_id=${productId}&qty=${qty}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update line item total
                        document.getElementById(`item-total-${productId}`).textContent = '₹' + data.item_total;
                        
                        // Update summaries
                        document.getElementById('subtotal-val').textContent = '₹' + data.subtotal;
                        document.getElementById('total-val').textContent = '₹' + data.total;
                        
                        // Handle discount section visibility
                        const discountSec = document.getElementById('discount-section');
                        const discountVal = document.getElementById('discount-val');
                        
                        const numericDiscount = parseFloat(data.discount.replace(/,/g, ''));
                        const numericSubtotal = parseFloat(data.subtotal.replace(/,/g, ''));
                        
                        if (numericDiscount > 0) {
                            discountSec.style.setProperty('display', 'flex', 'important');
                            discountVal.textContent = '-₹' + data.discount;
                            
                            // Hide notice if present
                            const notice = document.getElementById('promo-notice');
                            if (notice) notice.style.display = 'none';
                        } else {
                            discountSec.style.setProperty('display', 'none', 'important');
                            
                            // Dynamically update promo notice
                            const notice = document.getElementById('promo-notice');
                            if (notice) {
                                notice.style.display = 'block';
                                const remaining = 1000 - numericSubtotal;
                                notice.innerHTML = `<i class="far fa-gift me-1"></i> Add items worth <strong>₹${remaining.toFixed(2)}</strong> more to get flat <strong>10% off</strong>!`;
                            }
                        }
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>
