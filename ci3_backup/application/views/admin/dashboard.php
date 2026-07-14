<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Dashboard Overview</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/products/add') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold"><i class="far fa-plus me-1"></i> Add New Product</a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Total Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white admin-dashboard-card">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3">
                    <i class="far fa-shopping-cart fa-2x"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold m-0 text-dark"><?= $stats['total_orders'] ?></h3>
                    <span class="text-secondary small fw-semibold">Total Orders</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Products Live -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white admin-dashboard-card">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success-subtle text-success rounded-circle p-3 me-3">
                    <i class="far fa-boxes fa-2x"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold m-0 text-dark"><?= $stats['products_live'] ?></h3>
                    <span class="text-secondary small fw-semibold">Products Live</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white admin-dashboard-card">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning-subtle text-warning rounded-circle p-3 me-3">
                    <i class="far fa-clock fa-2x"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold m-0 text-dark"><?= $stats['pending_orders'] ?></h3>
                    <span class="text-secondary small fw-semibold">Pending Orders</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue MTD -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white admin-dashboard-card">
            <div class="card-body d-flex align-items-center">
                <div class="bg-info-subtle text-info rounded-circle p-3 me-3">
                    <i class="far fa-indian-rupee-sign fa-2x"></i>
                </div>
                <div>
                    <h3 class="fw-extrabold m-0 text-dark">₹<?= number_format($stats['revenue_mtd'] / 1000, 1) ?>k</h3>
                    <span class="text-secondary small fw-semibold">Revenue MTD</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table Section -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-5">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold m-0 text-dark"><i class="far fa-shopping-bag me-1 text-primary"></i> Recent Orders</h5>
        <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">View All Orders</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Order ID</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3">Product(s)</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Del. Date</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Tracking</th>
                        <th class="py-3 text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr class="border-bottom">
                                <td class="ps-4 py-3 fw-bold">#<?= htmlspecialchars($order['order_number']) ?></td>
                                <td class="py-3 text-secondary small"><?= htmlspecialchars($order['customer_name'] ?: 'Guest/Deleted') ?></td>
                                <td class="py-3 text-secondary small">
                                    <?php 
                                    $item_names = array_column($order['items'], 'product_name');
                                    echo htmlspecialchars(implode(', ', $item_names));
                                    ?>
                                </td>
                                <td class="py-3">
                                    <?php 
                                    // Use first item's delivery type
                                    $type = !empty($order['items']) ? $order['items'][0]['delivery_type'] : 'Express';
                                    if ($type === 'Express'): ?>
                                        <span class="badge badge-express"><i class="far fa-bolt"></i> Express</span>
                                    <?php else: ?>
                                        <span class="badge badge-courier"><i class="far fa-truck"></i> Courier</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-secondary small"><?= $order['delivery_date'] ? date('d M', strtotime($order['delivery_date'])) : 'N/A' ?></td>
                                <td class="py-3">
                                    <?php if ($order['status'] === 'Processing'): ?>
                                        <span class="badge bg-warning text-warning-emphasis">Processing</span>
                                    <?php elseif ($order['status'] === 'Shipped'): ?>
                                        <span class="badge bg-info text-info-emphasis">Shipped</span>
                                    <?php elseif ($order['status'] === 'Delivered'): ?>
                                        <span class="badge bg-success text-success-emphasis">Delivered</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-danger-emphasis">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 small text-secondary">
                                    <?php if ($order['status'] === 'Processing'): ?>
                                        <a href="<?= base_url('admin/orders/view/' . $order['id']) ?>" class="text-decoration-none text-primary fw-semibold">+ Add URL</a>
                                    <?php elseif ($order['status'] === 'Shipped'): ?>
                                        <span class="text-info"><i class="far fa-truck-moving me-1"></i> Track</span>
                                    <?php elseif ($order['status'] === 'Delivered'): ?>
                                        <span class="text-success"><i class="far fa-check me-1"></i> Done</span>
                                    <?php else: ?>
                                        <span class="text-muted">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-end pe-4">
                                    <a href="<?= base_url('admin/orders/view/' . $order['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
