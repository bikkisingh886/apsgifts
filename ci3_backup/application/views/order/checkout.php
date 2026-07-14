<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="checkout-page py-5 bg-light">
    <div class="container">
        <div class="mb-4">
            <h3 class="fw-bold">Checkout</h3>
            <p class="text-muted">Fill in your delivery address to place your order</p>
        </div>

        <form action="<?= base_url('order/place') ?>" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

            <div class="row">
                <!-- Left: Delivery Address Form -->
                <div class="col-lg-7 mb-4">
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                        <h5 class="fw-bold mb-4"><i class="far fa-map-marker-alt text-success me-2"></i> Delivery Address</h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label text-secondary small fw-semibold">Full Name *</label>
                            <input type="text" name="name" id="name" class="form-control bg-light" value="<?= htmlspecialchars($user_name) ?>" placeholder="Enter recipient full name" required>
                        </div>

                        <div class="mb-3">
                            <label for="mobile" class="form-label text-secondary small fw-semibold">Mobile Number *</label>
                            <input type="tel" name="mobile" id="mobile" class="form-control bg-light" value="<?= htmlspecialchars($user_mobile) ?>" placeholder="Enter recipient mobile number" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label text-secondary small fw-semibold">Address *</label>
                            <textarea name="address" id="address" rows="3" class="form-control bg-light" placeholder="Flat, House no., Building, Street address" required></textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="city" class="form-label text-secondary small fw-semibold">City *</label>
                                <input type="text" name="city" id="city" class="form-control bg-light" placeholder="e.g. Patna" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="pin" class="form-label text-secondary small fw-semibold">PIN Code *</label>
                                <input type="text" name="pin" id="pin" class="form-control bg-light" placeholder="6-digit PIN code" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Review & Pricing Summary -->
                <div class="col-lg-5">
                    <!-- Items review -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                        <h5 class="fw-bold mb-3">Cart (<?= count($cart) ?> Items)</h5>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($cart as $item): ?>
                                <li class="list-group-item px-0 py-3 border-bottom d-flex justify-content-between">
                                    <div class="me-3">
                                        <h6 class="fw-bold mb-1 small"><?= htmlspecialchars($item['name']) ?></h6>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <?php if ($item['delivery_type'] === 'Express'): ?>
                                                <span class="badge badge-express small"><i class="far fa-bolt"></i> Express &middot; Today</span>
                                            <?php else: ?>
                                                <span class="badge badge-courier small"><i class="far fa-truck"></i> Courier &middot; Est. 7 days</span>
                                            <?php endif; ?>
                                            <span class="text-secondary small">&times; <?= $item['qty'] ?></span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold text-dark text-end align-self-center small">₹<?= number_format($item['price'] * $item['qty'], 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Pricing card -->
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                        <h5 class="fw-bold mb-4">Payment Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3.5">
                            <span class="text-secondary small">Subtotal</span>
                            <span class="fw-semibold text-dark">₹<?= number_format($subtotal, 2) ?></span>
                        </div>
                        
                        <?php if ($discount > 0): ?>
                            <div class="d-flex justify-content-between mb-3.5">
                                <span class="text-secondary small text-danger">Offer applied (10% off)</span>
                                <span class="fw-semibold text-danger">-₹<?= number_format($discount, 2) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold">Total</span>
                            <h4 class="fw-extrabold text-primary m-0">₹<?= number_format($total, 2) ?></h4>
                        </div>

                        <div class="alert alert-info border-0 rounded-3 mb-4 py-2.5 px-3 small d-flex align-items-start">
                            <i class="far fa-info-circle text-primary fs-5 me-2 mt-0.5"></i>
                            <span>Payment on delivery or manual UPI (Razorpay setup in Phase 2).</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm py-3 mt-2">Place Order →</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
