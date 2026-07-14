<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row g-4 mb-4">
    <!-- Stat Widgets matching Page 4 of the blueprint -->
    <div class="col-md-6 col-lg-3">
        <div class="stat-card-gray shadow-sm">
            <div class="stat-label">Total Orders</div>
            <div class="stat-val-gray"><?= $orders_count ?></div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="stat-card-green shadow-sm">
            <div class="stat-label">Products Live</div>
            <div class="stat-val-green"><?= $products_count ?></div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="stat-card-orange shadow-sm">
            <div class="stat-label">Pending Orders</div>
            <div class="stat-val-orange"><?= count(array_filter($recent_orders, function($o){ return $o['status'] === 'Processing'; })) ?></div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="stat-card-blue shadow-sm">
            <div class="stat-label">Revenue MTD</div>
            <?php
            $revenue = 0;
            foreach ($recent_orders as $ord) {
                if ($ord['status'] === 'Completed') {
                    $revenue += $ord['total'];
                }
            }
            ?>
            <div class="stat-val-blue">₹<?= $revenue > 1000 ? round($revenue / 1000, 1) . 'k' : number_format($revenue) ?></div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders Table -->
    <div class="col-lg-12">
        <div class="card-custom">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-clock me-2 text-cyan"></i> Recent Orders Processing</h4>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product Details</th>
                            <th>Type</th>
                            <th>Del. Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $ord): ?>
                                <?php
                                $status_badge = 'bg-warning text-dark';
                                if ($ord['status'] === 'Completed') $status_badge = 'bg-success text-white';
                                elseif ($ord['status'] === 'Processing') $status_badge = 'bg-primary text-white';
                                elseif ($ord['status'] === 'Cancelled') $status_badge = 'bg-danger text-white';
                                
                                $address = json_decode($ord['address_json'], true);
                                ?>
                                <tr>
                                    <td><strong class="text-cyan">#<?= esc($ord['order_number']) ?></strong></td>
                                    <td><?= esc($address['name'] ?? 'User') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/orders/view/' . $ord['id']) ?>" class="text-decoration-none text-dark">
                                            View Order Items
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge <?= $ord['delivery_date'] ? 'bg-info text-dark' : 'bg-secondary text-white' ?>">
                                            <?= $ord['delivery_date'] ? '⚡ Express' : '📦 Courier' ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y', strtotime($ord['delivery_date'])) ?></td>
                                    <td><span class="badge badge-status <?= $status_badge ?>"><?= esc($ord['status']) ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <a href="<?= base_url('admin/orders/view/' . $ord['id']) ?>" class="btn btn-outline-cyan btn-sm me-2 text-nowrap"><i class="far fa-eye"></i> View</a>
                                            <form action="<?= base_url('admin/orders/update-status') ?>" method="post" class="m-0 p-0">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                <select name="status" class="form-select form-select-sm" style="min-width: 125px;" onchange="this.form.submit()">
                                                    <option value="Processing" <?= ($ord['status'] === 'Processing') ? 'selected' : '' ?>>Processing</option>
                                                    <option value="Shipped" <?= ($ord['status'] === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                                                    <option value="Delivered" <?= ($ord['status'] === 'Delivered') ? 'selected' : '' ?>>Delivered</option>
                                                    <option value="Cancelled" <?= ($ord['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </div>
                                    </td>
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
