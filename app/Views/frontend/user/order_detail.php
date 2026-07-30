<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- Minimalist Breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('user/orders') ?>" class="text-muted" style="text-decoration: none;">My Orders</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;">Order #<?= esc($order['order_number']) ?></li>
            </ol>
        </nav>
    </div>

    <!-- order detail -->
    <div class="user-area py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <?= $this->include('frontend/user/sidebar_partial') ?>
                </div>
                <div class="col-lg-9">
                    <div class="user-wrapper">
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <div class="user-card mb-4">
                            <div class="user-card-header d-flex justify-content-between align-items-center">
                                <h4 class="user-card-title mb-0">Order Status: <span class="badge bg-warning text-dark"><?= esc($order['status']) ?></span></h4>
                                <span class="text-muted">Placed on: <?= date('d F, Y h:i A', strtotime($order['created_at'])) ?></span>
                            </div>
                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <h5 class="fw-bold mb-2">Delivery Address Details</h5>
                                    <div class="p-3 bg-light rounded-3">
                                        <p class="mb-1"><strong>Name:</strong> <?= esc($address['name']) ?></p>
                                        <p class="mb-1"><strong>Mobile:</strong> <?= esc($address['mobile']) ?></p>
                                        <p class="mb-1"><strong>Address:</strong> <?= esc($address['address']) ?></p>
                                        <p class="mb-0"><strong>City:</strong> <?= esc($address['city']) ?> - <?= esc($address['pin']) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="fw-bold mb-2">Order Summary</h5>
                                    <div class="p-3 bg-light rounded-3">
                                        <p class="mb-1"><strong>Payment Method:</strong> Cash on Delivery (COD)</p>
                                        <p class="mb-1"><strong>Earliest Delivery / Schedule:</strong> <span class="badge bg-success text-white"><?= date('d F, Y', strtotime($order['delivery_date'])) ?></span></p>
                                        <p class="mb-1"><strong>Subtotal:</strong> ₹<?= number_format($order['subtotal'], 2) ?></p>
                                        <p class="mb-1"><strong>Discount:</strong> -₹<?= number_format($order['discount'], 2) ?></p>
                                        <p class="mb-0"><strong>Total Amount Paid:</strong> <strong class="text-primary">₹<?= number_format($order['total'], 2) ?></strong></p>
                                        <?php if (!empty($order['tracking_code'])): ?>
                                            <hr class="my-2" style="opacity: 0.1;">
                                            <p class="mb-1"><strong>AWB Number:</strong> <code class="text-dark fw-bold" style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px;"><?= esc($order['tracking_code']) ?></code></p>
                                            <?php if (!empty($order['tracking_url'])): ?>
                                                <div class="mt-2">
                                                    <a href="<?= esc($order['tracking_url']) ?>" target="_blank" class="btn btn-sm text-white py-1 px-3" style="background-color: #e76f51; border-radius: 8px; font-weight: 600; font-size: 0.82rem;"><i class="far fa-map-marker-alt me-1"></i> Track My Order &rarr;</a>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="user-card">
                            <h4 class="user-card-title">Order Items</h4>
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle text-nowrap">
                                    <thead>
                                        <tr class="table-light">
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= base_url($item['image_path'] ?: 'assets/img/product/default.png') ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px;" class="rounded">
                                                        <div>
                                                            <span class="d-block fw-bold"><?= esc($item['product_name']) ?></span>
                                                            <?php if (!empty($item['color'])): ?>
                                                                <span class="badge bg-secondary text-white small" style="background-color: #6c757d !important; font-size: 0.72rem;">Color: <?= esc($item['color']) ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= esc($item['sku']) ?></td>
                                                <td>₹<?= number_format($item['price'], 2) ?></td>
                                                <td><?= $item['qty'] ?></td>
                                                <td>₹<?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                            </tr>
                                            <?php if (!empty($item['is_customizable'])): ?>
                                                <?php 
                                                $cust = json_decode($item['customization_data'] ?? '{}', true);
                                                if (!is_array($cust)) {
                                                    $cust = [];
                                                }
                                                ?>
                                                <tr class="table-light-danger border-bottom">
                                                    <td colspan="5" class="ps-5 py-3">
                                                        <div class="p-3 bg-light border rounded shadow-sm">
                                                            <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-magic text-primary me-2"></i> Product Personalization Option</h6>
                                                            
                                                            <!-- Display existing details if any -->
                                                            <?php if (!empty($cust['text']) || !empty($cust['image'])): ?>
                                                                <div class="mb-3 p-3 bg-white rounded border">
                                                                    <span class="d-block fw-bold text-muted small mb-1">Submitted Personalization:</span>
                                                                    <?php if (!empty($cust['text'])): ?>
                                                                        <div class="text-dark small"><strong>Custom Text:</strong> <?= esc($cust['text']) ?></div>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($cust['image'])): ?>
                                                                        <div class="mt-2 text-dark small">
                                                                            <strong>Custom Photo:</strong><br>
                                                                            <img src="<?= base_url($cust['image']) ?>" alt="Custom Image" class="img-thumbnail mt-1" style="max-height: 100px; object-fit: contain;">
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- Personalization input form -->
                                                            <form action="<?= base_url('user/order-item/personalize/' . $item['id']) ?>" method="post" enctype="multipart/form-data">
                                                                <?= csrf_field() ?>
                                                                <div class="row g-2 align-items-end">
                                                                    <?php if (in_array($item['customization_type'], ['text', 'both'])): ?>
                                                                        <div class="col-md-5">
                                                                            <label class="form-label small fw-bold mb-1">Enter Name or Message *</label>
                                                                            <input type="text" name="customization_text" class="form-control form-control-sm" placeholder="Text for personalization" value="<?= esc($cust['text'] ?? '') ?>" required>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <?php if (in_array($item['customization_type'], ['image', 'both'])): ?>
                                                                        <div class="col-md-5">
                                                                            <label class="form-label small fw-bold mb-1">Upload Photo *</label>
                                                                            <input type="file" name="customization_image" class="form-control form-control-sm" accept="image/*" <?= empty($cust['image']) ? 'required' : '' ?>>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div class="col-md-2">
                                                                        <button type="submit" class="theme-btn py-2 px-3 w-100 text-center" style="font-size: 0.82rem; height: 38px; line-height: 1;"><i class="fas fa-check me-1"></i> Save</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- order detail end -->

</main>
<?= $this->endSection() ?>
