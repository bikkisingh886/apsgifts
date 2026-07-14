<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Order Management</h1>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Order Number</th>
                        <th class="py-3">Customer</th>
                        <th class="py-3">Date</th>
                        <th class="py-3 text-center">Items Qty</th>
                        <th class="py-3">Total Paid</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3">Tracking Code</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No orders found in database.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $ord): ?>
                            <tr class="border-bottom">
                                <td class="ps-4 py-3 fw-bold">#<?= htmlspecialchars($ord['order_number']) ?></td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($ord['customer_name'] ?: 'Guest / Deleted') ?></div>
                                </td>
                                <td class="py-3 text-secondary small"><?= date('d M Y, h:i A', strtotime($ord['created_at'])) ?></td>
                                <td class="py-3 text-center font-monospace small">
                                    <?php 
                                    $qty = 0;
                                    foreach ($ord['items'] as $it) $qty += $it['qty'];
                                    echo $qty;
                                    ?>
                                </td>
                                <td class="py-3 font-monospace fw-bold text-primary">₹<?= number_format($ord['total'], 2) ?></td>
                                <td class="py-3 text-center">
                                    <?php if ($ord['status'] === 'Processing'): ?>
                                        <span class="badge bg-warning text-warning-emphasis">Processing</span>
                                    <?php elseif ($ord['status'] === 'Shipped'): ?>
                                        <span class="badge bg-info text-info-emphasis">Shipped</span>
                                    <?php elseif ($ord['status'] === 'Delivered'): ?>
                                        <span class="badge bg-success text-success-emphasis">Delivered</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-danger-emphasis">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-secondary small font-monospace">
                                    <?= !empty($ord['tracking_code']) ? htmlspecialchars($ord['tracking_code']) : '<span class="text-muted small">None</span>' ?>
                                </td>
                                <td class="py-3 text-end pe-4">
                                    <a href="<?= base_url('admin/orders/view/' . $ord['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">View Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
