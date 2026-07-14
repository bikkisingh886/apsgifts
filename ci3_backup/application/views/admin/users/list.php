<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">User Directory</h1>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Mobile Number</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-end pe-4">Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $usr): ?>
                            <tr class="border-bottom">
                                <td class="ps-4 py-3 text-secondary small">#<?= $usr['id'] ?></td>
                                <td class="py-3 fw-bold text-dark">
                                    <i class="far fa-user me-1 text-muted"></i> <?= htmlspecialchars($usr['name']) ?>
                                    <?php if ($usr['email'] === 'admin@giftshop.in'): ?>
                                        <span class="badge bg-danger rounded-pill ms-1 small">SuperAdmin</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-secondary small"><?= htmlspecialchars($usr['email']) ?></td>
                                <td class="py-3 text-secondary font-monospace small">+91 <?= htmlspecialchars($usr['mobile']) ?></td>
                                <td class="py-3 text-center">
                                    <?php if ($usr['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-end pe-4 text-secondary small"><?= date('d M Y, h:i A', strtotime($usr['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
