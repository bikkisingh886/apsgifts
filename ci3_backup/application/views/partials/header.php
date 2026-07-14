<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Get active categories for navigation
$CI =& get_instance();
$CI->load->model('Category_model');
$nav_categories = $CI->Category_model->get_active();

// Get cart and wishlist counts
$cart_count = 0;
if ($CI->session->userdata('cart')) {
    $cart_count = count($CI->session->userdata('cart'));
}
$wishlist_count = 0;
if ($CI->session->userdata('wishlist')) {
    $wishlist_count = count($CI->session->userdata('wishlist'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO meta tags -->
    <title><?= isset($meta_title) ? $meta_title : 'GiftShop - Complete Gift E-commerce Platform' ?></title>
    <meta name="description" content="<?= isset($meta_desc) ? $meta_desc : 'Buy fresh flower bouquets, photo frames, cakes, and gifts online.' ?>">
    <link rel="canonical" href="<?= current_url() ?>">
    
    <!-- Open Graph (Facebook/SEO) -->
    <meta property="og:title" content="<?= isset($meta_title) ? $meta_title : 'GiftShop' ?>">
    <meta property="og:description" content="<?= isset($meta_desc) ? $meta_desc : 'Buy fresh flowers and gifts' ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <?php if (isset($product_img)): ?>
        <meta property="og:image" content="<?= base_url($product_img) ?>">
    <?php endif; ?>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/logo/favicon.png') ?>">

    <!-- CSS stylesheets -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/all-fontawesome.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/magnific-popup.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/nice-select.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    
    <!-- Custom styling override for Express/Courier banners, UI tweaks -->
    <style>
        .badge-express {
            background-color: #ffc107;
            color: #000;
            font-weight: 600;
        }
        .badge-courier {
            background-color: #17a2b8;
            color: #fff;
            font-weight: 600;
        }
        .admin-dashboard-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .admin-dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <!-- Header area -->
    <header class="header">
        <!-- Header Top -->
        <div class="header-top">
            <div class="container">
                <div class="header-top-wrapper">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6">
                            <div class="header-top-left">
                                <ul class="header-top-list">
                                    <li><a href="mailto:support@giftshop.in"><i class="far fa-envelope"></i> support@giftshop.in</a></li>
                                    <li><a href="tel:+919876543210"><i class="far fa-phone"></i> +91 98765 43210</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="header-top-right text-md-end">
                                <ul class="header-top-list justify-content-md-end">
                                    <?php if ($this->auth_lib->is_logged_in()): ?>
                                        <li><a href="<?= base_url('account') ?>"><i class="far fa-user"></i> My Account (<?= htmlspecialchars($this->session->userdata('user_name')) ?>)</a></li>
                                        <?php if ($this->auth_lib->is_admin()): ?>
                                            <li><a href="<?= base_url('admin') ?>" class="text-danger fw-bold"><i class="far fa-tachometer-alt"></i> Admin Panel</a></li>
                                        <?php endif; ?>
                                        <li><a href="<?= base_url('orders') ?>"><i class="far fa-shopping-bag"></i> My Orders</a></li>
                                        <li><a href="<?= base_url('logout') ?>"><i class="far fa-sign-out"></i> Logout</a></li>
                                    <?php else: ?>
                                        <li><a href="<?= base_url('login') ?>"><i class="far fa-sign-in"></i> Login</a></li>
                                        <li><a href="<?= base_url('register') ?>"><i class="far fa-user-plus"></i> Register</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header Top End -->

        <?php $this->load->view('partials/nav'); ?>
    </header>
    <!-- Header End -->

    <!-- Breadcrumb or Search container for mobile -->
    <div class="search-wrap d-lg-none py-2 bg-light border-bottom">
        <div class="container">
            <form action="<?= base_url('search') ?>" method="GET">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search gifts..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>
                    <button class="btn btn-primary" type="submit"><i class="far fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flash message alerts -->
    <div class="container my-3">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
