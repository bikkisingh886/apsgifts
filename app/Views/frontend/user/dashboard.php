<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<?php
$orderModel = new \App\Models\OrderModel();
$userOrders = $orderModel->getUserOrders(session()->get('user_id'));
$pendingCount = 0;
$completedCount = 0;
foreach ($userOrders as $ord) {
    if ($ord['status'] === 'Processing' || $ord['status'] === 'Pending' || $ord['status'] === 'Shipped') {
        $pendingCount++;
    } elseif ($ord['status'] === 'Completed' || $ord['status'] === 'Delivered') {
        $completedCount++;
    }

}
$wishlistCount = count(session()->get('wishlist') ?: []);
$recentOrders = array_slice($userOrders, 0, 5);
?>
<main class="main">

    <!-- Minimalist Breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;">User Dashboard</li>
            </ol>
        </nav>
    </div>

    <!-- user dashboard -->
    <div class="user-area py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <?= $this->include('frontend/user/sidebar_partial') ?>
                </div>
                <div class="col-lg-9">
                    <div class="user-wrapper">
                        <div class="user-card">
                            <h4 class="user-card-title">Summary</h4>
                            <div class="row">
                                <div class="col-md-6 col-lg-4">
                                    <div class="dashboard-widget color-1">
                                        <div class="dashboard-widget-info">
                                            <h1><?= $pendingCount ?></h1>
                                            <span>Pending / Processing</span>
                                        </div>
                                        <div class="dashboard-widget-icon">
                                            <i class="fal fa-list"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="dashboard-widget color-2">
                                        <div class="dashboard-widget-info">
                                            <h1><?= $completedCount ?></h1>
                                            <span>Completed Orders</span>
                                        </div>
                                        <div class="dashboard-widget-icon">
                                            <i class="fal fa-layer-group"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="dashboard-widget color-3">
                                        <div class="dashboard-widget-info">
                                            <h1><?= $wishlistCount ?></h1>
                                            <span>My Wishlist Items</span>
                                        </div>
                                        <div class="dashboard-widget-icon">
                                            <i class="fal fa-heart"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="user-card">
                                    <div class="user-card-header">
                                        <h4 class="user-card-title">Recent Orders</h4>
                                        <div class="user-card-header-right">
                                            <a href="<?= base_url('user/orders') ?>" class="theme-btn">View All Orders</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>#Order No</th>
                                                    <th>Purchased Date</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($recentOrders)): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">No recent orders found.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($recentOrders as $ord): ?>
                                                        <?php
                                                         $status_class = 'badge-info';
                                                         if ($ord['status'] === 'Delivered') $status_class = 'badge-success';
                                                         elseif ($ord['status'] === 'Shipped') $status_class = 'badge-info';
                                                         elseif ($ord['status'] === 'Processing') $status_class = 'badge-primary';
                                                         elseif ($ord['status'] === 'Cancelled') $status_class = 'badge-danger';
                                                        ?>
                                                        <tr>
                                                            <td><span class="table-list-code">#<?= esc($ord['order_number']) ?></span></td>
                                                            <td><?= date('d F, Y', strtotime($ord['created_at'])) ?></td>
                                                            <td>₹<?= number_format($ord['total'], 2) ?></td>
                                                            <td><span class="badge <?= $status_class ?>"><?= esc($ord['status']) ?></span></td>
                                                            <td>
                                                                <a href="<?= base_url('user/orders/' . $ord['order_number']) ?>" class="btn btn-outline-secondary btn-sm rounded-2" data-tooltip="tooltip" title="Details"><i class="far fa-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- user dashboard end -->

</main>
<?= $this->endSection() ?>
