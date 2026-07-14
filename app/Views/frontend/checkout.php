<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- Minimalist Breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('cart') ?>" class="text-muted" style="text-decoration: none;">Cart</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;">Checkout</li>
            </ol>
        </nav>
    </div>

    <!-- shop checkout -->
    <div class="shop-checkout py-4">
        <div class="container">
            <div class="shop-checkout-wrap">
                <form action="<?= base_url('checkout/process') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="shop-checkout-step">
                                <div class="accordion" id="shopCheckout">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button">
                                                Delivery Address Details
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="shop-checkout-form">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-group">
                                                                <label>Recipient Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" placeholder="Full Name" value="<?= esc($user_name) ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label>Mobile Number <span class="text-danger">*</span></label>
                                                                <input type="text" name="mobile" class="form-control" placeholder="10-digit mobile number" value="<?= esc($user_mobile) ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label>City <span class="text-danger">*</span></label>
                                                                <input type="text" name="city" class="form-control" placeholder="City" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="form-group">
                                                                <label>Full Address <span class="text-danger">*</span></label>
                                                                <input type="text" name="address" class="form-control" placeholder="Street Address, Landmark, etc." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label>Pincode <span class="text-danger">*</span></label>
                                                                <input type="text" name="pin" class="form-control" placeholder="Pincode" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="accordion-item mt-4">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button">
                                                Payment Method
                                            </button>
                                        </h2>
                                        <div class="accordion-collapse collapse show">
                                            <div class="accordion-body">
                                                <div class="payment-method-options p-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                                                        <label class="form-check-label fw-bold" for="cod">
                                                            Cash on Delivery (COD)
                                                        </label>
                                                        <p class="text-muted small">Pay with cash upon delivery of your gift item.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="shop-cart-summary mt-0">
                                <h5>Order Summary</h5>
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cart as $item): ?>
                                                <tr>
                                                    <td class="small">
                                                        <?= esc($item['name']) ?>
                                                        <?php if (!empty($item['delivery_date'])): ?>
                                                            <div class="text-success small fw-bold mt-1">
                                                                <i class="far fa-calendar-alt me-1"></i> Delivery: <?= date('d M Y', strtotime($item['delivery_date'])) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $item['qty'] ?></td>
                                                    <td>₹<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <ul>
                                    <li><strong>Sub Total:</strong> <span>₹<?= number_format($subtotal, 2) ?></span></li>
                                    <?php if ($discount > 0): ?>
                                        <li><strong>Discount Applied:</strong> <span>-₹<?= number_format($discount, 2) ?></span></li>
                                    <?php endif; ?>
                                    <li><strong>Shipping:</strong> <span>Free</span></li>
                                    <li class="shop-cart-total"><strong>Total:</strong> <span>₹<?= number_format($total, 2) ?></span></li>
                                </ul>
                                <div class="text-end mt-40">
                                    <button type="submit" class="theme-btn w-100 py-3">Place Order <i class="fas fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- shop checkout end -->

</main>
<?= $this->endSection() ?>
