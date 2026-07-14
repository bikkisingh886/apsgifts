<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="user-account-area py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Left Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm text-center p-4 rounded-3 bg-white">
                    <div class="card-body">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle fs-1 fw-bold mb-3 shadow-sm" style="width: 90px; height: 90px;">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                        <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?> &middot; +91 <?= htmlspecialchars($user['mobile']) ?></p>
                        
                        <hr class="my-4">
                        
                        <div class="list-group list-group-flush text-start account-menu border-0">
                            <a href="<?= base_url('orders') ?>" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center rounded-3 mb-2">
                                <span><i class="far fa-shopping-bag text-primary me-2"></i> My Orders</span>
                                <span class="badge bg-primary rounded-pill"><?= $order_count ?></span>
                            </a>
                            <a href="<?= base_url('wishlist') ?>" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center rounded-3 mb-2">
                                <span><i class="far fa-heart text-danger me-2"></i> Wishlist</span>
                                <span class="badge bg-danger rounded-pill"><?= $wishlist_count ?></span>
                            </a>
                            <a href="#saved-addresses" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center rounded-3 mb-2 active" data-bs-toggle="tab">
                                <span><i class="far fa-map-marker-alt text-success me-2"></i> Saved Addresses</span>
                                <i class="far fa-chevron-right text-muted"></i>
                            </a>
                            <a href="#change-password-tab" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center rounded-3 mb-2" data-bs-toggle="tab">
                                <span><i class="far fa-key text-warning me-2"></i> Change Password</span>
                                <i class="far fa-chevron-right text-muted"></i>
                            </a>
                            <a href="<?= base_url('logout') ?>" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center rounded-3 text-danger">
                                <span><i class="far fa-sign-out-alt me-2"></i> Logout</span>
                                <i class="far fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content Panels -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-3 bg-white">
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Saved Addresses Pane -->
                            <div class="tab-pane fade show active" id="saved-addresses">
                                <h4 class="fw-bold mb-4"><i class="far fa-map-marker-alt text-success me-2"></i> Saved Addresses</h4>
                                <div class="alert alert-info border-0 rounded-3 small">
                                    Your addresses will be saved dynamically when you place an order.
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-2 border-primary-subtle rounded-3 p-3">
                                            <h6 class="fw-bold mb-2">Default Billing/Shipping</h6>
                                            <p class="mb-1 text-muted fw-semibold small"><?= htmlspecialchars($user['name']) ?></p>
                                            <p class="mb-1 text-muted small">+91 <?= htmlspecialchars($user['mobile']) ?></p>
                                            <p class="mb-0 text-muted small">Patna, Bihar, India - 800001</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Change Password Pane -->
                            <div class="tab-pane fade" id="change-password-tab">
                                <h4 class="fw-bold mb-4"><i class="far fa-key text-warning me-2"></i> Change Password</h4>
                                <form action="<?= base_url('account/change-password') ?>" method="POST" class="col-md-8">
                                    <!-- CSRF Token -->
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                                    <div class="mb-3">
                                        <label for="current_password" class="form-label text-secondary small fw-semibold">Current Password</label>
                                        <input type="password" name="current_password" id="current_password" class="form-control bg-light" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label text-secondary small fw-semibold">New Password</label>
                                        <input type="password" name="new_password" id="new_password" class="form-control bg-light" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label text-secondary small fw-semibold">Confirm New Password</label>
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control bg-light" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold mt-2">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
