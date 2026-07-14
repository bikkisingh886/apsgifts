<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- Minimalist Breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;">Shopping Cart</li>
            </ol>
        </nav>
    </div>

    <!-- Shop Cart -->
    <div class="shop-cart py-4">
        <div class="container">
            <div class="shop-cart-wrap">
                <?php if (empty($cart_items)): ?>
                    <div class="card border rounded bg-white shadow-sm p-5 text-center">
                        <div class="py-5">
                            <i class="far fa-shopping-bag fa-4x text-muted mb-3 d-block"></i>
                            <h4 class="text-muted mb-4">Your shopping cart is empty!</h4>
                            <a href="<?= base_url('/shop') ?>" class="theme-btn"><i class="fas fa-arrow-left me-2"></i> Start Shopping</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <!-- Left Side: Cart Items Card -->
                        <div class="col-lg-8 mb-4">
                            <div class="card border rounded bg-white shadow-sm p-3 p-md-4">
                                <h5 class="fw-bold mb-4 text-dark border-bottom pb-3">
                                    <i class="far fa-shopping-cart text-primary me-2"></i> Cart Items (<?= count($cart_items) ?>)
                                </h5>
                                
                                <div class="cart-items-list">
                                    <?php foreach ($cart_items as $item): ?>
                                        <div class="row align-items-center py-3 border-bottom position-relative g-3">
                                            <!-- Product Image -->
                                            <div class="col-3 col-md-2">
                                                <div class="border rounded p-1 text-center" style="width: 80px; height: 80px; overflow: hidden; margin: 0 auto;">
                                                    <a href="<?= base_url('product/' . $item['sku']) ?>">
                                                        <img src="<?= base_url($item['image'] ?: 'assets/img/product/default.png') ?>" alt="<?= esc($item['name']) ?>" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <!-- Product Details -->
                                            <div class="col-9 col-md-5">
                                                <h6 class="fw-bold mb-1" style="font-size: 0.95rem;">
                                                    <a href="<?= base_url('product/' . $item['sku']) ?>" class="text-decoration-none text-dark"><?= esc($item['name']) ?></a>
                                                </h6>
                                                <div class="text-muted small mb-2" style="font-size: 0.75rem;">SKU: <?= esc($item['sku']) ?></div>
                                                
                                                <!-- Personalization Details -->
                                                <?php if (!empty($item['customization_text']) || !empty($item['customization_image'])): ?>
                                                    <div class="p-2 bg-light border rounded mb-2" style="font-size: 0.8rem;">
                                                        <span class="fw-bold text-primary"><i class="fas fa-magic me-1"></i> Personalization:</span>
                                                        <?php if (!empty($item['customization_text'])): ?>
                                                            <div class="text-dark">Text: "<?= esc($item['customization_text']) ?>"</div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['customization_image'])): ?>
                                                            <div class="mt-1">
                                                                Image: <a href="<?= base_url($item['customization_image']) ?>" target="_blank" class="text-decoration-underline text-info">View Upload</a>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Delivery Date -->
                                                <div>
                                                    <span class="badge bg-light text-muted border py-1" style="font-size: 0.75rem; font-weight: 500;">
                                                        <i class="far fa-calendar-alt text-success me-1"></i> <?= isset($item['delivery_date']) ? date('d M Y', strtotime($item['delivery_date'])) : 'Standard Courier' ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Quantity Selector (Amazon Style) -->
                                            <div class="col-6 col-md-3">
                                                <div class="d-flex align-items-center gap-1 justify-content-center justify-content-md-start">
                                                    <form action="<?= base_url('cart/update') ?>" method="post" class="d-flex align-items-center gap-1 m-0">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                        <button type="button" class="btn btn-qty-minus border p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 4px;"><i class="fas fa-minus text-dark" style="font-size: 0.7rem;"></i></button>
                                                        <input type="number" name="qty" value="<?= $item['qty'] ?>" min="1" class="form-control form-control-sm text-center border-0 qty-input p-0" style="width: 35px; height: 32px; font-weight: 700; background: #fff;" readonly>
                                                        <button type="button" class="btn btn-qty-plus border p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #f8f9fa; border-radius: 4px;"><i class="fas fa-plus text-dark" style="font-size: 0.7rem;"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <!-- Sub Total & Remove -->
                                            <div class="col-6 col-md-2 text-end">
                                                <div class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">₹<?= number_format($item['price'] * $item['qty'], 2) ?></div>
                                                <div class="text-muted small mb-2" style="font-size: 0.8rem;">₹<?= number_format($item['price'], 2) ?> each</div>
                                                <a href="<?= base_url('cart/remove/' . $item['id']) ?>" class="text-danger small text-decoration-none"><i class="far fa-trash-alt me-1"></i> Remove</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="text-start mt-4">
                                    <a href="<?= base_url('/shop') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Continue Shopping</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Side: Checkout / Summary Card -->
                        <div class="col-lg-4">
                            <!-- Coupon Card -->
                            <div class="card border rounded bg-white shadow-sm p-4 mb-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="far fa-tag text-primary me-2"></i> Apply Coupon Code</h6>
                                <form action="<?= base_url('cart/apply-coupon') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="input-group">
                                        <input type="text" name="coupon_code" class="form-control" placeholder="Enter coupon" value="<?= esc($applied_coupon['code'] ?? '') ?>" required>
                                        <button class="btn btn-primary" type="submit">Apply</button>
                                    </div>
                                </form>
                                <?php if (!empty($applied_coupon)): ?>
                                    <div class="mt-2 text-success small d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                        <span><i class="fas fa-check-circle me-1"></i> Coupon <strong><?= esc($applied_coupon['code']) ?></strong> applied!</span>
                                        <a href="<?= base_url('cart/remove-coupon') ?>" class="text-danger fw-bold text-decoration-none">Remove</a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Cart Summary Card -->
                            <div class="card border rounded bg-white shadow-sm p-4">
                                <h5 class="fw-bold mb-4 text-dark border-bottom pb-3">Order Summary</h5>
                                <ul class="list-unstyled p-0 m-0">
                                    <li class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Sub Total:</span>
                                        <span class="fw-bold text-dark">₹<?= number_format($subtotal, 2) ?></span>
                                    </li>
                                    
                                    <!-- Global Additional Discount -->
                                    <?php if ($global_discount > 0): ?>
                                        <li class="d-flex justify-content-between mb-3 text-success">
                                            <span>Global Discount (<?= esc(get_setting('global_discount_value')) ?><?= get_setting('global_discount_type') === 'percentage' ? '%' : '' ?> Off):</span>
                                            <span>-₹<?= number_format($global_discount, 2) ?></span>
                                        </li>
                                    <?php elseif (get_setting('global_discount_active') === '1'): ?>
                                        <?php 
                                        $threshold = (float)get_setting('global_discount_threshold');
                                        $diff = $threshold - $subtotal;
                                        ?>
                                        <?php if ($diff > 0): ?>
                                            <li class="mb-3">
                                                <div class="alert alert-warning small p-2 mb-0 border-0">
                                                    <i class="far fa-gift me-1"></i> Add <strong>₹<?= number_format($diff, 2) ?></strong> more to get additional <?= esc(get_setting('global_discount_value')) ?><?= get_setting('global_discount_type') === 'percentage' ? '%' : '' ?> discount!
                                                </div>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- Coupon Discount -->
                                    <?php if ($coupon_discount > 0): ?>
                                        <li class="d-flex justify-content-between mb-3 text-success">
                                            <span>Coupon Discount (<?= esc($applied_coupon['code']) ?>):</span>
                                            <span>-₹<?= number_format($coupon_discount, 2) ?></span>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <li class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Shipping:</span>
                                        <span class="text-success fw-bold">Free</span>
                                    </li>
                                    <hr class="my-3">
                                    <li class="d-flex justify-content-between mb-4">
                                        <span class="fs-5 fw-bold text-dark">Total:</span>
                                        <span class="fs-5 fw-bold text-primary">₹<?= number_format($total, 2) ?></span>
                                    </li>
                                </ul>
                                
                                <div class="mt-4">
                                    <a href="<?= base_url('checkout') ?>" class="theme-btn w-100 py-3 text-center d-block" style="border-radius: 6px;"><i class="fas fa-lock me-1"></i> Checkout Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- shop cart end -->

    <script>
        window.addEventListener('load', function() {
            $(document).on('click', '.btn-qty-minus', function(e) {
                e.preventDefault();
                var input = $(this).siblings('.qty-input');
                var val = parseInt(input.val()) || 1;
                if (val > 1) {
                    input.val(val - 1);
                    $(this).closest('form').submit();
                }
            });

            $(document).on('click', '.btn-qty-plus', function(e) {
                e.preventDefault();
                var input = $(this).siblings('.qty-input');
                var val = parseInt(input.val()) || 1;
                input.val(val + 1);
                $(this).closest('form').submit();
            });
        });
    </script>
</main>
<?= $this->endSection() ?>
