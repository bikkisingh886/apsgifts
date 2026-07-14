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
<!-- Navbar -->
<div class="main-navigation">
    <nav class="navbar navbar-expand-lg">
        <div class="container position-relative">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <h3 class="m-0 text-primary fw-bold"><i class="far fa-gift"></i> GiftShop</h3>
            </a>
            
            <div class="mobile-menu-right">
                <div class="mobile-menu-btn">
                    <a href="#" class="nav-right-link search-box-outer"><i class="far fa-search"></i></a>
                    <a href="<?= base_url('wishlist') ?>" class="nav-right-link"><i class="far fa-heart"></i><span class="wishlist-count-badge"><?= $wishlist_count ?></span></a>
                    <a href="<?= base_url('cart') ?>" class="nav-right-link"><i class="far fa-shopping-bag"></i><span class="cart-count-badge"><?= $cart_count ?></span></a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="main_nav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categories</a>
                        <ul class="dropdown-menu">
                            <?php foreach ($nav_categories as $cat): ?>
                                <li><a class="dropdown-item" href="<?= base_url('category/' . $cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('cart') ?>">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('wishlist') ?>">Wishlist</a></li>
                </ul>
                
                <div class="nav-right-content ms-lg-4 d-none d-lg-flex">
                    <form action="<?= base_url('search') ?>" method="GET" class="d-flex align-items-center">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search gifts..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>
                            <button class="btn btn-primary" type="submit"><i class="far fa-search"></i></button>
                        </div>
                    </form>
                    <div class="ms-3 d-flex align-items-center">
                        <a href="<?= base_url('wishlist') ?>" class="nav-right-link position-relative me-3">
                            <i class="far fa-heart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger wishlist-count-badge"><?= $wishlist_count ?></span>
                        </a>
                        <a href="<?= base_url('cart') ?>" class="nav-right-link position-relative">
                            <i class="far fa-shopping-bag fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary cart-count-badge"><?= $cart_count ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
<!-- Navbar End -->
