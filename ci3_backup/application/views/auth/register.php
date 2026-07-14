<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="register-area py-5">
    <div class="container">
        <div class="col-md-6 col-lg-5 mx-auto">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-block bg-primary-subtle text-primary rounded-circle p-3 mb-2">
                            <i class="far fa-user-plus fa-2x"></i>
                        </div>
                        <h3 class="fw-bold">Create Account</h3>
                        <p class="text-muted">Register to place orders and manage details</p>
                    </div>

                    <form action="<?= base_url('register') ?>" method="POST">
                        <!-- CSRF Token -->
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

                        <div class="form-group mb-3">
                            <label for="name" class="form-label text-secondary small fw-semibold">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-user text-muted"></i></span>
                                <input type="text" name="name" id="name" class="form-control bg-light border-start-0" placeholder="Enter full name" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label text-secondary small fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email" class="form-control bg-light border-start-0" placeholder="Enter email" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="mobile" class="form-label text-secondary small fw-semibold">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-phone text-muted"></i></span>
                                <input type="tel" name="mobile" id="mobile" class="form-control bg-light border-start-0" placeholder="Enter mobile number" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label text-secondary small fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control bg-light border-start-0" placeholder="Create password" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="confirm_password" class="form-label text-secondary small fw-semibold">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="far fa-lock text-muted"></i></span>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control bg-light border-start-0" placeholder="Confirm password" required>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label text-muted small" for="terms">By registering, you agree to our Terms & Privacy Policy</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-semibold">Create Account →</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted small">Already have an account? <a href="<?= base_url('login') ?>" class="text-primary text-decoration-none fw-semibold">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
