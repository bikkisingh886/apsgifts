<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- Minimalist Breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;">My Orders</li>
            </ol>
        </nav>
    </div>

    <!-- user orders -->
    <div class="user-area py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <?= $this->include('frontend/user/sidebar_partial') ?>
                </div>
                <div class="col-lg-9">
                    <div class="user-wrapper">
                        <div class="user-card p-4 border rounded bg-white shadow-sm">
                            <h4 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="far fa-shopping-bag text-primary me-2"></i> Order History</h4>
                            
                            <?php if (empty($orders)): ?>
                                <div class="alert alert-light text-center border py-5">
                                    <i class="far fa-shopping-bag fs-1 text-muted mb-2 d-block"></i>
                                    <p class="mb-0 text-muted">You haven't placed any orders yet.</p>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($orders as $ord): ?>
                                        <?php
                                        $status_class = 'bg-info text-dark';
                                        if ($ord['status'] === 'Delivered') $status_class = 'bg-success text-white';
                                        elseif ($ord['status'] === 'Shipped') $status_class = 'bg-info text-white';
                                        elseif ($ord['status'] === 'Processing') $status_class = 'bg-primary text-white';
                                        elseif ($ord['status'] === 'Cancelled') $status_class = 'bg-danger text-white';
                                        ?>
                                        
                                        <!-- 3 Columns on Desktop, 1 on Mobile -->
                                        <div class="col-lg-4 col-md-6 mb-4">
                                            <a href="<?= base_url('user/orders/' . $ord['order_number']) ?>" class="text-decoration-none order-card-link">
                                                <div class="card h-100 border rounded shadow-sm overflow-hidden bg-white hover-card" style="border-radius: 12px !important; transition: all 0.2s ease-in-out;">
                                                    <!-- Card Header -->
                                                    <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                                        <span class="text-muted small fw-bold">#<?= esc($ord['order_number']) ?></span>
                                                        <span class="badge <?= $status_class ?> px-2 py-0.5" style="font-size: 0.75rem;"><?= esc($ord['status']) ?></span>
                                                    </div>
                                                    
                                                    <!-- Card Body -->
                                                    <div class="card-body p-3">
                                                        <div class="order-items-list">
                                                            <?php foreach ($ord['items'] as $item): ?>
                                                                <div class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom last-no-border">
                                                                    <img src="<?= base_url($item['image_path'] ?: 'assets/img/product/default.png') ?>" alt="<?= esc($item['product_name']) ?>" style="width: 55px; height: 55px; object-fit: cover;" class="rounded border">
                                                                    <div class="min-w-0">
                                                                        <div class="fw-bold text-dark text-truncate-2 small" style="line-height: 1.3;"><?= esc($item['product_name']) ?></div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- user orders end -->

</main>

<style>
.order-card-link:hover .hover-card {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
    border-color: #e76f51 !important;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;  
    overflow: hidden;
}
.last-no-border:last-child {
    border-bottom: 0 !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>

<?= $this->endSection() ?>
