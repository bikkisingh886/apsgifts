<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Order Details - #<?= htmlspecialchars($order['order_number']) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/orders') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold"><i class="far fa-arrow-left me-1"></i> Back to Orders</a>
    </div>
</div>

<div class="row">
    <!-- Left Panel: Customer, Address & Items list -->
    <div class="col-lg-8 mb-4">
        <!-- Info Cards -->
        <div class="row g-3 mb-4">
            <!-- Customer Box -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3.5 rounded-4 bg-white h-100">
                    <h6 class="text-secondary small fw-bold text-uppercase mb-2"><i class="far fa-user me-1 text-primary"></i> Customer Details</h6>
                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($order['customer_name'] ?: 'Guest') ?></h5>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($order['customer_email']) ?></p>
                    <p class="text-muted small mb-0">+91 <?= htmlspecialchars($order['customer_mobile']) ?></p>
                </div>
            </div>
            
            <!-- Delivery Address Box -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-3.5 rounded-4 bg-white h-100">
                    <h6 class="text-secondary small fw-bold text-uppercase mb-2"><i class="far fa-map-marker-alt me-1 text-success"></i> Delivery Address</h6>
                    <p class="fw-bold text-dark mb-1"><?= htmlspecialchars($address['name']) ?></p>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($address['address']) ?>, <?= htmlspecialchars($address['city']) ?> - <?= htmlspecialchars($address['pin']) ?></p>
                    <p class="text-muted small mb-0 mt-1">Est. Delivery: <strong><?= $order['delivery_date'] ? date('d M Y', strtotime($order['delivery_date'])) : 'Pending' ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
            <div class="card-header bg-white border-0 pt-3 px-4 pb-0">
                <h6 class="fw-bold m-0 text-dark"><i class="far fa-list-alt text-primary me-1"></i> Items Ordered</h6>
            </div>
            <div class="card-body p-0 mt-2">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 80px;">Img</th>
                                <th class="py-3">Product</th>
                                <th class="py-3 text-center">Type</th>
                                <th class="py-3 text-center">Qty</th>
                                <th class="py-3 text-end pe-4">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3">
                                        <img src="<?= $item['image_path'] ? base_url($item['image_path']) : base_url('assets/img/product/18.png') ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: contain;">
                                    </td>
                                    <td class="py-3 fw-bold text-dark small"><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td class="py-3 text-center">
                                        <?php if ($item['delivery_type'] === 'Express'): ?>
                                            <span class="badge badge-express"><i class="far fa-bolt"></i> Express</span>
                                        <?php else: ?>
                                            <span class="badge badge-courier"><i class="far fa-truck"></i> Courier</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-center small"><?= $item['qty'] ?></td>
                                    <td class="py-3 text-end pe-4 fw-bold font-monospace text-secondary">₹<?= number_format($item['unit_price'] * $item['qty'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Tracking & Status Updates -->
    <div class="col-lg-4">
        <!-- Add/Update Tracking Panel (Page 12) -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h5 class="fw-bold mb-3"><i class="far fa-shipping-fast text-primary me-2"></i> Add / Update Tracking Info</h5>
            <form action="<?= base_url('admin/orders/add-tracking') ?>" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                <div class="mb-3">
                    <label for="tracking_url" class="form-label text-secondary small fw-semibold">Courier Partner Tracking URL</label>
                    <input type="url" name="tracking_url" id="tracking_url" class="form-control bg-light" value="<?= htmlspecialchars($order['tracking_url']) ?>" placeholder="e.g. https://www.indiapost.gov.in/track/...">
                </div>

                <div class="mb-3">
                    <label for="tracking_code" class="form-label text-secondary small fw-semibold">Tracking / AWB Number *</label>
                    <input type="text" name="tracking_code" id="tracking_code" class="form-control bg-light" value="<?= htmlspecialchars($order['tracking_code']) ?>" placeholder="e.g. EP123456789IN" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold py-2"><i class="far fa-save me-1"></i> Save Tracking</button>
                </div>
            </form>
            
            <?php if ($order['status'] !== 'Delivered'): ?>
                <form action="<?= base_url('admin/orders/update-status') ?>" method="POST" class="mt-2">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="status" value="Delivered">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success rounded-pill fw-bold py-2"><i class="far fa-check-circle me-1"></i> Mark Delivered</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- Invoice / Order Summary -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h5 class="fw-bold mb-4">Order Invoice Summary</h5>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary small">Subtotal</span>
                <span class="fw-semibold text-dark">₹<?= number_format($order['subtotal'], 2) ?></span>
            </div>
            <?php if ($order['discount'] > 0): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary small text-danger">Discount</span>
                    <span class="fw-semibold text-danger">-₹<?= number_format($order['discount'], 2) ?></span>
                </div>
            <?php endif; ?>
            <hr class="my-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold">Total Paid</span>
                <h4 class="fw-extrabold text-primary m-0">₹<?= number_format($order['total'], 2) ?></h4>
            </div>
        </div>

        <!-- Status Tray Changer -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <h5 class="fw-bold mb-3"><i class="far fa-cog text-secondary me-2"></i> Update Order Status</h5>
            <form action="<?= base_url('admin/orders/update-status') ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                
                <div class="mb-3">
                    <select name="status" class="form-select bg-light">
                        <option value="Processing" <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="Shipped" <?= $order['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option value="Delivered" <?= $order['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-secondary rounded-pill fw-bold">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
