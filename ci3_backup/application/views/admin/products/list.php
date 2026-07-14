<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Product Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/products/add') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold"><i class="far fa-plus me-1"></i> Add New Product</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3" style="width: 80px;">Img</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">SKU</th>
                        <th class="py-3">Categories</th>
                        <th class="py-3">Price</th>
                        <th class="py-3">Type</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No products found in database.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $prod): ?>
                            <tr class="border-bottom">
                                <td class="ps-4 py-3">
                                    <img src="<?= $prod['image_path'] ? base_url($prod['image_path']) : base_url('assets/img/product/18.png') ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: contain;">
                                </td>
                                <td class="py-3">
                                    <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($prod['name']) ?></h6>
                                    <a href="<?= base_url($prod['slug']) ?>" target="_blank" class="small text-muted text-decoration-none">View Live <i class="far fa-external-link fs-7"></i></a>
                                </td>
                                <td class="py-3 text-secondary font-monospace small"><?= htmlspecialchars($prod['sku']) ?></td>
                                <td class="py-3 text-secondary small">
                                    <?php 
                                    $cat_names = array_column($prod['categories'], 'name');
                                    echo htmlspecialchars(implode(', ', $cat_names));
                                    ?>
                                </td>
                                <td class="py-3 font-monospace fw-bold">₹<?= number_format($prod['price'], 2) ?></td>
                                <td class="py-3">
                                    <?php if ($prod['delivery_type'] === 'Express'): ?>
                                        <span class="badge badge-express"><i class="far fa-bolt"></i> Express</span>
                                    <?php else: ?>
                                        <span class="badge badge-courier"><i class="far fa-truck"></i> Courier</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-center">
                                    <?php if ($prod['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-end pe-4">
                                    <a href="<?= base_url('admin/products/edit/' . $prod['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold me-1">Edit</a>
                                    <a href="<?= base_url('admin/products/delete/' . $prod['id']) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold" onclick="return confirm('Are you sure you want to delete this product? All orders reference it.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
