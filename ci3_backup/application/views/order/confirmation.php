<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="confirmation-page py-5 bg-light">
    <div class="container">
        <!-- Confirmed header banner -->
        <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white text-center mb-5">
            <div class="card-body">
                <div class="d-inline-flex bg-success-subtle text-success rounded-circle p-4 mb-3">
                    <i class="far fa-check-circle fa-4x"></i>
                </div>
                <h2 class="fw-bold text-success mb-2">Order Confirmed!</h2>
                <p class="text-muted lead mb-0">Hi <?= htmlspecialchars($address['name']) ?>, your order has been placed and is currently being processed.</p>
                <div class="badge bg-secondary-subtle text-secondary-emphasis fs-6 mt-3 px-3 py-2">Order Number: #<?= htmlspecialchars($order['order_number']) ?></div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Order Details & Address -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold m-0"><i class="far fa-list-alt text-primary me-2"></i> Items Ordered</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-4 py-3" style="width: 100px;">Product</th>
                                        <th class="py-3">Name</th>
                                        <th class="py-3 text-center">Delivery Type</th>
                                        <th class="py-3 text-center">Delivery Date</th>
                                        <th class="py-3 text-center">Qty</th>
                                        <th class="py-3 text-end pe-4">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr class="border-bottom">
                                            <td class="ps-4 py-3">
                                                <img src="<?= $item['image_path'] ? base_url($item['image_path']) : base_url('assets/img/product/18.png') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: contain;">
                                            </td>
                                            <td class="py-3">
                                                <h6 class="fw-bold mb-0 small"><?= htmlspecialchars($item['product_name']) ?></h6>
                                            </td>
                                            <td class="py-3 text-center">
                                                <?php if ($item['delivery_type'] === 'Express'): ?>
                                                    <span class="badge badge-express"><i class="far fa-bolt"></i> Express</span>
                                                <?php else: ?>
                                                    <span class="badge badge-courier"><i class="far fa-truck"></i> Courier</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 text-center small text-muted">
                                                <?= $item['delivery_date'] ? date('d M Y', strtotime($item['delivery_date'])) : 'Pending' ?>
                                            </td>
                                            <td class="py-3 text-center small">
                                                <?= $item['qty'] ?>
                                            </td>
                                            <td class="py-3 text-end pe-4 fw-semibold text-dark">
                                                ₹<?= number_format($item['unit_price'] * $item['qty'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold mb-3"><i class="far fa-map-marker-alt text-success me-2"></i> Shipping Address</h5>
                            <p class="mb-1 fw-bold text-dark"><?= htmlspecialchars($address['name']) ?></p>
                            <p class="mb-1 text-muted small"><i class="far fa-phone me-1"></i> +91 <?= htmlspecialchars($address['mobile']) ?></p>
                            <p class="mb-0 text-muted small"><i class="far fa-map me-1"></i> <?= htmlspecialchars($address['address']) ?>, <?= htmlspecialchars($address['city']) ?> - <?= htmlspecialchars($address['pin']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Tracking info if shipped -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                            <h5 class="fw-bold mb-3"><i class="far fa-shipping-fast text-info me-2"></i> Shipping & Tracking</h5>
                            <p class="mb-2">Status: 
                                <?php if ($order['status'] === 'Processing'): ?>
                                    <span class="badge bg-warning text-warning-emphasis">Processing</span>
                                <?php elseif ($order['status'] === 'Shipped'): ?>
                                    <span class="badge bg-info text-info-emphasis">Shipped</span>
                                <?php elseif ($order['status'] === 'Delivered'): ?>
                                    <span class="badge bg-success text-success-emphasis">Delivered</span>
                                <?php else: ?>
                                    <span class="badge bg-danger text-danger-emphasis">Cancelled</span>
                                <?php endif; ?>
                            </p>
                            
                            <?php if ($order['status'] === 'Shipped' && !empty($order['tracking_code'])): ?>
                                <div class="mt-3 p-3 bg-light rounded-3">
                                    <p class="mb-1 small text-muted">Tracking Code/AWB:</p>
                                    <h6 class="fw-bold text-dark mb-2"><?= htmlspecialchars($order['tracking_code']) ?></h6>
                                    <?php if (!empty($order['tracking_url'])): ?>
                                        <a href="<?= htmlspecialchars($order['tracking_url']) ?>" target="_blank" class="btn btn-primary btn-sm rounded-pill fw-semibold"><i class="far fa-external-link me-1"></i> Track package</a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted small mt-3 mb-0">Tracking details will appear here once the order is shipped by the administrator.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Payment Summary Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-4">Invoice Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary small">Subtotal</span>
                        <span class="fw-semibold text-dark">₹<?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <?php if ($order['discount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary small text-danger">Discount</span>
                            <span class="fw-semibold text-danger">-₹<?= number_format($order['discount'], 2) ?></span>
                        </div>
                    <?php endif; ?>
                    <hr class="my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Total Paid</span>
                        <h4 class="fw-extrabold text-primary m-0">₹<?= number_format($order['total'], 2) ?></h4>
                    </div>
                    <div class="text-muted small mt-2">
                        <span>Payment Mode: <strong>Cash on Delivery (COD) / UPI</strong></span>
                    </div>
                </div>
                
                <a href="<?= base_url() ?>" class="btn btn-outline-primary btn-lg w-100 rounded-pill fw-bold shadow-sm py-3"><i class="far fa-arrow-left me-2"></i> Go to Homepage</a>
            </div>
        </div>
    </div>
</div>
