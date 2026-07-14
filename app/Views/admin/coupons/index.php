<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <!-- Left Column: Add New Coupon -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom shadow-sm bg-white p-4 border rounded">
            <h4 class="mb-4 text-dark fw-bold border-bottom pb-3"><i class="far fa-plus me-2 text-cyan"></i> Add New Coupon</h4>
            
            <form action="<?= base_url('admin/coupons/create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="code" class="form-label fw-bold">Coupon Code *</label>
                    <input type="text" class="form-control text-uppercase" id="code" name="code" placeholder="e.g. SAVE20" required>
                    <div class="form-text">Alphabetic codes and numbers only. (Auto capitalized)</div>
                </div>
                
                <div class="mb-3">
                    <label for="discount_type" class="form-label fw-bold">Discount Type *</label>
                    <select class="form-select" id="discount_type" name="discount_type">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (₹)</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="discount_value" class="form-label fw-bold">Discount Value *</label>
                    <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" placeholder="e.g. 10 or 250" required>
                </div>
                
                <div class="mb-3">
                    <label for="min_cart_amount" class="form-label fw-bold">Minimum Cart Amount (₹)</label>
                    <input type="number" step="0.01" class="form-control" id="min_cart_amount" name="min_cart_amount" value="0.00">
                    <div class="form-text">Coupon will only apply if subtotal is at least this value.</div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-cyan w-100 py-2"><i class="far fa-save me-1"></i> Create Coupon</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Coupons List -->
    <div class="col-lg-8">
        <div class="card-custom shadow-sm bg-white p-4 border rounded mb-4">
            <h4 class="mb-4 text-dark fw-bold border-bottom pb-3"><i class="far fa-tags me-2 text-cyan"></i> Active Coupon Codes</h4>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Min. Spend</th>
                            <th>Usage Count</th>
                            <th>Audit Info</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($coupons)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No coupons created yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($coupons as $c): ?>
                                <tr>
                                    <td><strong class="text-dark"><?= esc($c['code']) ?></strong></td>
                                    <td>
                                        <?= esc($c['discount_value']) ?> <?= $c['discount_type'] === 'percentage' ? '%' : '₹' ?>
                                    </td>
                                    <td>₹<?= number_format($c['min_cart_amount'], 2) ?></td>
                                    <td><?= $c['usage_count'] ?> times</td>
                                    <td class="small text-muted" style="line-height: 1.4;">
                                        <?php if (!empty($c['creator_name'])): ?>
                                            <div>Created by: <span class="text-cyan"><?= esc($c['creator_name']) ?></span></div>
                                        <?php endif; ?>
                                        <?php if ($c['created_at']): ?>
                                            <div>Created at: <?= date('d M Y', strtotime($c['created_at'])) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($c['updater_name'])): ?>
                                            <div class="mt-1">Updated by: <span class="text-cyan"><?= esc($c['updater_name']) ?></span></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/coupons/toggle/' . $c['id']) ?>" class="badge <?= $c['is_active'] == 1 ? 'bg-success' : 'bg-secondary' ?> text-decoration-none">
                                            <?= $c['is_active'] == 1 ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/coupons/delete/' . $c['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this coupon?');"><i class="far fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Coupon Usage Accounting Section -->
        <div class="card-custom shadow-sm bg-white p-4 border rounded">
            <h4 class="mb-4 text-dark fw-bold border-bottom pb-3"><i class="far fa-calculator me-2 text-cyan"></i> Coupon Accounting & Tracking</h4>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Order No</th>
                            <th>Customer</th>
                            <th>Coupon Used</th>
                            <th>Discount Saved</th>
                            <th>Subtotal</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usage_logs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No coupon transactions recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usage_logs as $log): ?>
                                <tr>
                                    <td><strong class="text-dark">#<?= esc($log['order_number']) ?></strong></td>
                                    <td><?= esc($log['customer_name'] ?? 'Guest') ?></td>
                                    <td><span class="badge bg-info text-dark font-monospace"><?= esc($log['coupon_code']) ?></span></td>
                                    <td class="text-danger fw-bold">-₹<?= number_format($log['coupon_discount'], 2) ?></td>
                                    <td>₹<?= number_format($log['subtotal'], 2) ?></td>
                                    <td><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
