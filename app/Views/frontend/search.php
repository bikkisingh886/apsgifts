<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="site-breadcrumb">
        <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
        <div class="container">
            <div class="site-breadcrumb-wrap">
                <h4 class="breadcrumb-title">Search Results</h4>
                <ul class="breadcrumb-menu">
                    <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                    <li class="active">Search results for "<?= esc($keyword) ?>"</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- shop-area -->
    <div class="shop-area py-90">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="site-heading mb-40">
                        <h2 class="site-title">Search Results for <span>"<?= esc($keyword) ?>"</span></h2>
                        <p class="mt-2 text-muted">Showing <?= count($products) ?> matching products</p>
                    </div>
                    
                    <div class="shop-item-wrap">
                        <div class="row g-4">
                            <?php if (empty($products)): ?>
                                <div class="col-12 text-center py-5">
                                    <h4 class="text-muted">No products found matching your search.</h4>
                                    <a href="<?= base_url('shop') ?>" class="theme-btn mt-3">Browse Shop <i class="fas fa-arrow-right"></i></a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $imageUrl = $product['image_path'] ? base_url($product['image_path']) : base_url('assets/img/product/default.png');
                                    $originalPrice = (float)$product['price'];
                                    $discountPrice = $originalPrice;
                                    $hasDiscount = false;
                                    $badgeText = '';
                                    
                                    if ($product['offer_value'] > 0) {
                                        $hasDiscount = true;
                                        if ($product['offer_type'] === 'percent') {
                                            $discountPrice = $originalPrice * (1 - $product['offer_value'] / 100);
                                            $badgeText = '-' . (int)$product['offer_value'] . '%';
                                        } else {
                                            $discountPrice = $originalPrice - $product['offer_value'];
                                            $badgeText = '-₹' . (int)$product['offer_value'];
                                        }
                                    }
                                    
                                    $wishlist = session()->get('wishlist') ?: [];
                                    $inWishlist = in_array($product['id'], $wishlist);
                                    ?>
                                    <div class="col-sm-6 col-md-4 col-lg-3">
                                        <div class="product-item">
                                            <div class="product-img">
                                                <?php if ($hasDiscount): ?>
                                                    <span class="type discount"><?= $badgeText ?></span>
                                                <?php elseif ($product['delivery_type'] === 'Express'): ?>
                                                    <span class="type hot">Express</span>
                                                <?php else: ?>
                                                    <span class="type new">Courier</span>
                                                <?php endif; ?>
                                                
                                                <a href="<?= base_url('product/' . $product['slug']) ?>"><img src="<?= $imageUrl ?>" alt="<?= esc($product['name']) ?>" style="height: 250px; object-fit: cover;"></a>
                                                
                                                <div class="product-action-wrap">
                                                    <div class="product-action">
                                                        <a href="#" class="btn-quickview" data-product-id="<?= $product['id'] ?>" data-tooltip="tooltip" title="Quick View"><i class="far fa-eye"></i></a>
                                                        <a href="#" class="wishlist-toggle-btn" data-product-id="<?= $product['id'] ?>" data-tooltip="tooltip" title="Add To Wishlist">
                                                            <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart text-danger"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="product-content">
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="<?= base_url('product/' . $product['slug']) ?>"><?= esc($product['name']) ?></a></h3>
                                                    <div class="product-rate">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                    <div class="product-price">
                                                        <?php if ($hasDiscount): ?>
                                                            <h5><del>₹<?= number_format($originalPrice, 2) ?></del><span>₹<?= number_format($discountPrice, 2) ?></span></h5>
                                                        <?php else: ?>
                                                            <span>₹<?= number_format($originalPrice, 2) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <form action="<?= base_url('cart/add') ?>" method="post">
                                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                    <input type="hidden" name="qty" value="1">
                                                    <button type="submit" class="product-cart-btn" data-bs-placement="left" data-tooltip="tooltip" title="Add To Cart">
                                                        <i class="far fa-shopping-bag"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- shop-area end -->

</main>
<?= $this->endSection() ?>
