<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($meta_title) ? $meta_title : 'Admin Dashboard - GiftShop' ?></title>
    <!-- CSS imports -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/all-fontawesome.min.css') ?>">
    
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #212529;
            color: #fff;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: #adb5bd;
            padding: 0.75rem 1.5rem;
            border-radius: 0;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }
        .navbar-brand-admin {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding-left: 1.5rem;
        }
        .admin-main {
            margin-top: 56px;
        }
    </style>
</head>
<body>
    
    <!-- Top admin navbar -->
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand-admin col-md-3 col-lg-2 me-0" href="<?= base_url('admin') ?>"><i class="far fa-gift"></i> GiftAdmin</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav w-100 d-flex flex-row justify-content-end px-3">
            <span class="navbar-text text-white-50 me-3 d-none d-sm-inline-block">Logged in as: <strong>admin@giftshop.in</strong></span>
            <div class="nav-item text-nowrap">
                <a class="nav-link text-white px-3 bg-danger rounded-pill small fw-semibold" href="<?= base_url('logout') ?>"><i class="far fa-sign-out-alt"></i> Sign out</a>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(2) === 'Admin_dashboard' || $this->uri->segment(1) === 'admin' && !$this->uri->segment(2) ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                                <i class="far fa-tachometer-alt me-2"></i> Dashboard Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(2) === 'Admin_categories' ? 'active' : '' ?>" href="<?= base_url('admin/categories') ?>">
                                <i class="far fa-tags me-2"></i> Categories (CRUD)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(2) === 'Admin_products' ? 'active' : '' ?>" href="<?= base_url('admin/products') ?>">
                                <i class="far fa-boxes me-2"></i> Products (CRUD)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(2) === 'Admin_offers' ? 'active' : '' ?>" href="<?= base_url('admin/offers') ?>">
                                <i class="far fa-percent me-2"></i> Offers & Discounts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(2) === 'Admin_orders' ? 'active' : '' ?>" href="<?= base_url('admin/orders') ?>">
                                <i class="far fa-shopping-cart me-2"></i> Orders & Tracking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $this->uri->segment(2) === 'Admin_users' ? 'active' : '' ?>" href="<?= base_url('admin/users') ?>">
                                <i class="far fa-users me-2"></i> User Directory
                            </a>
                        </li>
                    </ul>
                    
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">
                        <span>Shortcuts</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url() ?>" target="_blank">
                                <i class="far fa-external-link me-2"></i> View Live Store
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-main pt-4">
                <!-- Flash messages in admin -->
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

                <!-- Load Page Subview -->
                <?php $this->load->view($subview); ?>
            </main>
        </div>
    </div>

    <!-- JS Imports -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
