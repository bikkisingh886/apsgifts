<?php
$uri = service('uri');
$activeSegment = $uri->getSegment(2);
$authLib = new \App\Libraries\AuthLib();
$user = $authLib->getUser();

$orderModel = new \App\Models\OrderModel();
$ordersCount = count($orderModel->getUserOrders($authLib->getUserId()));
$wishlistCount = count(session()->get('wishlist') ?: []);
?>
<div class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-profile-img">
            <img src="<?= !empty($user['profile_photo']) ? base_url($user['profile_photo']) : base_url('assets/img/account/02.jpg') ?>" alt="Profile Photo" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
        </div>
        <h5><?= esc($user['name'] ?? 'User') ?></h5>
        <p><?= esc($user['email'] ?? '') ?></p>
    </div>
    <ul class="sidebar-list">
        <li><a class="<?= ($activeSegment === 'dashboard') ? 'active' : '' ?>" href="<?= base_url('user/dashboard') ?>"><i class="far fa-gauge-high"></i> Dashboard</a></li>
        <li><a class="<?= ($activeSegment === 'orders') ? 'active' : '' ?>" href="<?= base_url('user/orders') ?>"><i class="far fa-shopping-bag"></i> My Orders <span class="badge badge-danger"><?= $ordersCount ?></span></a></li>
        <li><a class="<?= ($activeSegment === 'wishlist') ? 'active' : '' ?>" href="<?= base_url('user/wishlist') ?>"><i class="far fa-heart"></i> My Wishlist <span class="badge badge-danger"><?= $wishlistCount ?></span></a></li>
        <li><a class="<?= ($activeSegment === 'settings') ? 'active' : '' ?>" href="<?= base_url('user/settings') ?>"><i class="far fa-user-cog"></i> Profile Settings</a></li>
        <li><a href="<?= base_url('logout') ?>"><i class="far fa-sign-out"></i> Logout</a></li>
    </ul>
</div>
