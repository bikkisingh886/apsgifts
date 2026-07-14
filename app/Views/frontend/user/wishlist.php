<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- Minimalist Breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;">My Wishlist</li>
            </ol>
        </nav>
    </div>

    <!-- wishlist area -->
    <div class="user-area py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <?= $this->include('frontend/user/sidebar_partial') ?>
                </div>
                <div class="col-lg-9">
                    <div class="user-wrapper">
                        <div class="user-card">
                            <h4 class="user-card-title">My Wishlist Items</h4>
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle text-nowrap">
                                    <thead>
                                        <tr class="table-light">
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Stock Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($products)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">Your wishlist is empty.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($products as $product): ?>
                                                <?php
                                                $imageUrl = $product['image_path'] ? base_url($product['image_path']) : base_url('assets/img/product/default.png');
                                                $originalPrice = (float)$product['price'];
                                                $discountPrice = $originalPrice;
                                                if ($product['offer_value'] > 0) {
                                                    if ($product['offer_type'] === 'percent') {
                                                        $discountPrice = $originalPrice * (1 - $product['offer_value'] / 100);
                                                    } else {
                                                        $discountPrice = $originalPrice - $product['offer_value'];
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= $imageUrl ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px;" class="rounded">
                                                            <span><a href="<?= base_url('product/' . $product['sku']) ?>" class="text-dark fw-bold"><?= esc($product['name']) ?></a></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong class="text-primary">₹<?= number_format($discountPrice, 2) ?></strong>
                                                        <?php if ($product['offer_value'] > 0): ?>
                                                            <del class="text-muted small ms-2">₹<?= number_format($originalPrice, 2) ?></del>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success text-white">In Stock</span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('product/' . $product['sku']) ?>" class="btn btn-primary btn-sm rounded-2 me-2">View</a>
                                                        <a href="<?= base_url('cart/remove/' . $product['id']) ?>" class="btn btn-danger btn-sm rounded-2 wishlist-remove-btn" data-product-id="<?= $product['id'] ?>"><i class="far fa-trash-alt"></i></a>
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
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Handle dynamic removal from wishlist using fetch
    const removeButtons = document.querySelectorAll(".wishlist-remove-btn");
    removeButtons.forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault();
            const productId = this.getAttribute("data-product-id");
            const row = this.closest("tr");
            
            fetch("<?= base_url('user/wishlist/toggle') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: `product_id=${productId}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    // Update header counters
                    document.querySelectorAll(".list-link span, .nav-right-link span").forEach(el => {
                        if (el.previousElementSibling && el.previousElementSibling.classList.contains("fa-heart")) {
                            el.textContent = data.count;
                        }
                    });
                    
                    if (data.count === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
<?= $this->endSection() ?>
