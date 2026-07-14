<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="login-area py-5">
    <div class="container">
        <div class="col-md-6 col-lg-5 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-block bg-primary-subtle text-primary rounded-circle p-3 mb-2">
                            <i class="far fa-gift fa-2x"></i>
                        </div>
                        <h3 class="fw-bold">Welcome Back</h3>
                        <p class="text-muted">Login to your GiftShop account</p>
                    </div>

                    <div class="d-flex mb-4">
                        <a href="<?= base_url('login') ?>" class="btn btn-primary w-50 me-2 rounded-pill fw-semibold">Login</a>
                        <a href="<?= base_url('register') ?>" class="btn btn-outline-secondary w-50 rounded-pill fw-semibold">Register</a>
                    </div>

                    <form action="<?= base_url('login') ?>" method="POST">
                        <!-- CSRF Token -->
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                        <div class="form-group mb-3">
                            <label for="email" class="form-label text-secondary small fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email" class="form-control bg-light border-start-0" placeholder="Enter email" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label text-secondary small fw-semibold">Password</label>
                                <a href="#" class="text-decoration-none small text-primary">Forgot password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control bg-light border-start-0" placeholder="Enter password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-semibold mt-3">Login →</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted small">Don't have an account? <a href="<?= base_url('register') ?>" class="text-primary text-decoration-none fw-semibold">Register</a></p>
                    </div>

                    <div class="alert alert-info border-0 mt-4 mb-0 small rounded-3">
                        <i class="far fa-info-circle me-1 text-primary"></i> Login is required before placing an order. You can browse and add to cart without login.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
