<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="m-0 text-dark"><i class="far fa-shopping-bag me-2 text-cyan"></i> Order Details: #<?= esc($order['order_number']) ?></h4>
            <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-cyan btn-sm"><i class="far fa-arrow-left me-1"></i> Back to Orders</a>
        </div>
        
        <div class="card-custom mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="text-cyan mb-2">Order Summary</h5>
                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-warning text-dark"><?= esc($order['status']) ?></span></p>
                    <p class="mb-1"><strong>Scheduled Delivery Date:</strong> <span class="badge bg-info text-dark"><?= date('d F, Y', strtotime($order['delivery_date'])) ?></span></p>
                    <p class="mb-0"><strong>Placed At:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <h5 class="text-cyan mb-2">Process Order Status</h5>
                    <form action="<?= base_url('admin/orders/update-status') ?>" method="post" class="d-inline-block">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <div class="d-flex align-items-center justify-content-end">
                            <select name="status" class="form-select me-2" style="min-width: 135px;">
                                <option value="Processing" <?= ($order['status'] === 'Processing') ? 'selected' : '' ?>>Processing</option>
                                <option value="Shipped" <?= ($order['status'] === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                                <option value="Delivered" <?= ($order['status'] === 'Delivered') ? 'selected' : '' ?>>Delivered</option>
                                <option value="Cancelled" <?= ($order['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn-cyan">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card-custom h-100 mb-0">
                    <h5 class="text-cyan mb-3"><i class="far fa-map-marker-alt me-2"></i> Recipient Address Details</h5>
                    <p class="mb-1"><strong>Name:</strong> <?= esc($address['name']) ?></p>
                    <p class="mb-1"><strong>Mobile:</strong> <?= esc($address['mobile']) ?></p>
                    <p class="mb-1"><strong>Street Address:</strong> <?= esc($address['address']) ?></p>
                    <p class="mb-0"><strong>City & Pincode:</strong> <?= esc($address['city']) ?> - <?= esc($address['pin']) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom h-100 mb-0">
                    <h5 class="text-cyan mb-3"><i class="far fa-receipt me-2"></i> Financial Invoice Details</h5>
                    <p class="mb-1"><strong>Subtotal:</strong> ₹<?= number_format($order['subtotal'], 2) ?></p>
                    <p class="mb-1"><strong>Discount:</strong> -₹<?= number_format($order['discount'], 2) ?></p>
                    <p class="mb-1"><strong>Shipping Cost:</strong> Free</p>
                    <hr style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <p class="mb-0"><strong>Grand Total Amount:</strong> <strong class="text-primary" style="font-size: 1.25rem;">₹<?= number_format($order['total'], 2) ?></strong></p>
                </div>
            </div>
        </div>

        <div class="card-custom mb-4">
            <h5 class="text-cyan mb-3"><i class="far fa-truck me-2"></i> Add / Update Tracking Info</h5>
            <form action="<?= base_url('admin/orders/update-tracking') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <div class="row align-items-end g-3">
                    <div class="col-md-5">
                        <label class="form-label text-dark fw-bold">Courier Partner Tracking URL</label>
                        <input type="url" name="tracking_url" class="form-control" value="<?= esc($order['tracking_url'] ?? '') ?>" placeholder="e.g. https://www.delhivery.com/track/or-awb">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-dark fw-bold">Tracking / AWB Number</label>
                        <input type="text" name="tracking_code" class="form-control" value="<?= esc($order['tracking_code'] ?? '') ?>" placeholder="e.g. AWB123456789">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-cyan w-100 py-2"><i class="far fa-save me-2"></i> Save Tracking</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-custom">

            <h5 class="text-cyan mb-4"><i class="far fa-box me-2"></i> Order Items List</h5>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Product Image</th>
                            <th>SKU & Name</th>
                            <th>Item Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?= base_url($item['image_path'] ?: 'assets/img/product/default.png') ?>" alt="" style="width: 55px; height: 55px; object-fit: cover;" class="rounded border">
                                </td>
                                <td>
                                    <span class="text-white fw-bold"><?= esc($item['product_name']) ?></span><br>
                                    <small class="text-muted">SKU: <?= esc($item['sku'] ?? 'N/A') ?></small>
                                    <?php if (!empty($item['color'])): ?>
                                        <br><small class="badge bg-secondary text-white" style="background-color: #6c757d !important; font-size: 0.72rem; padding: 3px 6px;">Color: <?= esc($item['color']) ?></small>
                                    <?php endif; ?>
                                    <?php 
                                    $cust = json_decode($item['customization_data'] ?? '{}', true);
                                    if (!empty($cust['text']) || !empty($cust['image'])): 
                                    ?>
                                        <div class="mt-2 p-2 rounded border border-secondary" style="font-size: 0.8rem; background-color: rgba(255, 255, 255, 0.05);">
                                            <strong style="color: #e76f51;"><i class="fas fa-magic"></i> Personalization:</strong>
                                            <?php if (!empty($cust['text'])): ?>
                                                <div class="text-white mt-1">Text: <span class="fw-bold"><?= esc($cust['text']) ?></span></div>
                                            <?php endif; ?>
                                            <?php if (!empty($cust['image'])): ?>
                                                <div class="text-white mt-1">
                                                    Image: <a href="<?= base_url($cust['image']) ?>" target="_blank" class="fw-bold text-decoration-underline" style="color: #e76f51;">View Image</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>₹<?= number_format($item['price'] ?? 0, 2) ?></td>
                                <td><?= $item['qty'] ?></td>
                                <td><strong class="text-white">₹<?= number_format(($item['price'] ?? 0) * $item['qty'], 2) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
