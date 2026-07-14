<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$is_edit = ($edit_offer !== NULL);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Offer & Discount Management</h1>
</div>

<!-- Add/Edit Offer Form -->
<div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
    <h5 class="fw-bold mb-3 text-dark"><?= $is_edit ? 'Edit Promo Offer' : 'Create New Offer' ?></h5>
    <form action="<?= base_url('admin/offers/save') ?>" method="POST">
        <!-- CSRF Token -->
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $edit_offer['id'] ?>">
        <?php endif; ?>

        <div class="row g-3 align-items-end">
            <!-- Offer Name -->
            <div class="col-md-3">
                <label for="name" class="form-label small fw-bold text-secondary">Offer Name *</label>
                <input type="text" name="name" id="name" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($edit_offer['name']) : '' ?>" placeholder="e.g. Summer Sale 10%" required>
            </div>
            
            <!-- Type (Flat vs Percent) -->
            <div class="col-md-2">
                <label for="type" class="form-label small fw-bold text-secondary">Discount Type *</label>
                <select name="type" id="type" class="form-select bg-light" required>
                    <option value="percent" <?= $is_edit && $edit_offer['type'] === 'percent' ? 'selected' : '' ?>>% Percent</option>
                    <option value="flat" <?= $is_edit && $edit_offer['type'] === 'flat' ? 'selected' : '' ?>>₹ Flat Rate</option>
                </select>
            </div>

            <!-- Value -->
            <div class="col-md-2">
                <label for="value" class="form-label small fw-bold text-secondary">Value *</label>
                <input type="number" name="value" id="value" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($edit_offer['value']) : '' ?>" placeholder="e.g. 10" step="0.01" required>
            </div>

            <!-- Applies To (Product vs Category) -->
            <div class="col-md-2">
                <label for="applies_to" class="form-label small fw-bold text-secondary">Applies To *</label>
                <select name="applies_to" id="applies_to" class="form-select bg-light" required>
                    <option value="product" <?= $is_edit && $edit_offer['applies_to'] === 'product' ? 'selected' : '' ?>>Product</option>
                    <option value="category" <?= $is_edit && $edit_offer['applies_to'] === 'category' ? 'selected' : '' ?>>Category</option>
                </select>
            </div>

            <!-- Status Checkbox -->
            <div class="col-md-1.5 align-self-center pb-2">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= !$is_edit || $edit_offer['is_active'] ? 'checked' : '' ?>>
                    <label class="form-check-label fw-semibold small text-secondary" for="is_active">Active</label>
                </div>
            </div>

            <!-- Actions -->
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2 small">Save Offer</button>
                <?php if ($is_edit): ?>
                    <a href="<?= base_url('admin/offers') ?>" class="btn btn-outline-secondary rounded-pill w-100 fw-semibold py-2 small">Cancel</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- Offers Table List -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-5">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="fw-bold m-0 text-dark">Active & Inactive Discount Rules</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Value</th>
                        <th class="py-3">Applies To</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($offers)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No campaigns created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($offers as $off): ?>
                            <tr class="border-bottom">
                                <td class="ps-4 py-3 text-secondary small">#<?= $off['id'] ?></td>
                                <td class="py-3 fw-bold text-dark"><?= htmlspecialchars($off['name']) ?></td>
                                <td class="py-3 text-secondary small"><?= $off['type'] === 'percent' ? '% Percent' : '₹ Flat' ?></td>
                                <td class="py-3 font-monospace fw-bold"><?= $off['type'] === 'percent' ? (int)$off['value'] . '%' : '₹' . number_format($off['value'], 2) ?></td>
                                <td class="py-3 text-secondary small"><?= ucfirst($off['applies_to']) ?></td>
                                <td class="py-3 text-center">
                                    <?php if ($off['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-end pe-4">
                                    <a href="<?= base_url('admin/offers/edit/' . $off['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold me-1">Edit</a>
                                    <a href="<?= base_url('admin/offers/delete/' . $off['id']) ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold" onclick="return confirm('Are you sure you want to delete this offer? Product associations will clear.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
