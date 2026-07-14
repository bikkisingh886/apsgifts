<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Category Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/categories/add') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold"><i class="far fa-plus me-1"></i> Add New Category</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Slug</th>
                        <th class="py-3 text-center">Products Count</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No categories created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr class="border-bottom">
                                <td class="ps-4 py-3 text-secondary small">#<?= $cat['id'] ?></td>
                                <td class="py-3 fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></td>
                                <td class="py-3 text-muted small">/category/<?= htmlspecialchars($cat['slug']) ?></td>
                                <td class="py-3 text-center font-monospace small"><?= $cat['product_count'] ?></td>
                                <td class="py-3 text-center">
                                    <?php if ($cat['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-end pe-4">
                                    <a href="<?= base_url('admin/categories/edit/' . $cat['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold me-1">Edit</a>
                                    <a href="<?= base_url('admin/categories/delete/' . $cat['id']) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold" onclick="return confirm('Are you sure you want to delete this category? All product associations will be removed.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
