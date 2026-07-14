<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="my-orders-page py-5 bg-light">
    <div class="container">
        <!-- Header -->
        <div class="mb-4">
            <a href="<?= base_url('account') ?>" class="text-decoration-none text-secondary fw-semibold">
                <i class="far fa-arrow-left me-1"></i> Back to Account
            </a>
            <h3 class="fw-bold mt-2"><i class="far fa-shopping-bag text-primary me-2"></i> My Orders</h3>
            <p class="text-muted">Manage your order history and track shipments</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="card border-0 shadow-sm p-5 text-center rounded-3">
                <div class="card-body">
                    <div class="d-inline-flex bg-primary-subtle text-primary rounded-circle p-4 mb-3">
                        <i class="far fa-shopping-bag fa-3x"></i>
                    </div>
                    <h4>No Orders Yet</h4>
                    <p class="text-muted mb-4">You haven't placed any orders yet. Select a perfect gift to place your first order!</p>
                    <a href="<?= base_url() ?>" class="btn btn-primary rounded-pill px-4">Shop Gifts Now</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <?php foreach ($orders as $order): ?>
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                            <!-- Card Header -->
                            <div class="card-header bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-secondary small">Order ID</span>
                                    <h6 class="fw-bold m-0 text-dark">#<?= htmlspecialchars($order['order_number']) ?></h6>
                                </div>
                                <div class="text-end">
                                    <span class="text-secondary small d-block">Placed on: <?= date('d M Y', strtotime($order['created_at'])) ?></span>
                                    <!-- Order status badge -->
                                    <?php if ($order['status'] === 'Processing'): ?>
                                        <span class="badge bg-warning text-warning-emphasis">Processing</span>
                                    <?php elseif ($order['status'] === 'Shipped'): ?>
                                        <span class="badge bg-info text-info-emphasis">Shipped</span>
                                    <?php elseif ($order['status'] === 'Delivered'): ?>
                                        <span class="badge bg-success text-success-emphasis">Delivered</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-danger-emphasis">Cancelled</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Card Body (Items inside) -->
                            <div class="card-body p-4 border-bottom">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="d-flex align-items-center mb-3 last-mb-0">
                                        <img src="<?= $item['image_path'] ? base_url($item['image_path']) : base_url('assets/img/product/18.png') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-fluid rounded border p-1" style="width: 60px; height: 60px; object-fit: contain;">
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="fw-bold mb-1 small"><?= htmlspecialchars($item['product_name']) ?></h6>
                                            <div class="d-flex flex-wrap gap-2 align-items-center small text-muted">
                                                <?php if ($item['delivery_type'] === 'Express'): ?>
                                                    <span class="badge badge-express"><i class="far fa-bolt"></i> Express</span>
                                                    <span>Delivery: <?= $item['delivery_date'] ? date('d M', strtotime($item['delivery_date'])) : 'Today' ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-courier"><i class="far fa-truck"></i> Courier</span>
                                                    <span>Est: <?= $item['delivery_date'] ? date('d M', strtotime($item['delivery_date'])) : '5-7 days' ?></span>
                                                <?php endif; ?>
                                                <span class="text-secondary">&middot; Qty: <?= $item['qty'] ?> &middot; ₹<?= number_format($item['unit_price'], 2) ?></span>
                                            </div>
                                        </div>
                                        <div class="text-end fw-bold text-dark">
                                            ₹<?= number_format($item['unit_price'] * $item['qty'], 2) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Card Footer Action Buttons -->
                            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <span class="text-secondary small">Total Paid: </span>
                                    <span class="fw-extrabold text-primary fs-5">₹<?= number_format($order['total'], 2) ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('orders/view/' . $order['order_number']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">View Detail</a>
                                    
                                    <?php if ($order['status'] === 'Processing'): ?>
                                        <button class="btn btn-secondary btn-sm rounded-pill px-3 fw-semibold" disabled>Tracking N/A</button>
                                    <?php elseif ($order['status'] === 'Shipped'): ?>
                                        <?php if (!empty($order['tracking_url'])): ?>
                                            <a href="<?= htmlspecialchars($order['tracking_url']) ?>" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold"><i class="far fa-shipping-fast me-1"></i> Track Order</a>
                                        <?php else: ?>
                                            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" disabled><i class="far fa-shipping-fast me-1"></i> Tracking Code Pending</button>
                                        <?php endif; ?>
                                    <?php elseif ($order['status'] === 'Delivered'): ?>
                                        <!-- Reorder button triggers adding items back to cart -->
                                        <form action="<?= base_url('cart/add') ?>" method="POST" class="d-inline-block">
                                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                            <?php foreach ($order['items'] as $item): ?>
                                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                <input type="hidden" name="qty" value="<?= $item['qty'] ?>">
                                            <?php endforeach; ?>
                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold">Reorder</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
