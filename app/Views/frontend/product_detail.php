<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

        <!-- minimalist breadcrumb -->
        <div class="container mt-4 mb-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                    <?php if (!empty($product['categories'])): ?>
                        <?php 
                        $cat = $product['categories'][0]; 
                        if (!empty($cat['parent_id']) && !empty($cat['parent_slug'])):
                        ?>
                            <li class="breadcrumb-item"><a href="<?= get_category_url($cat['parent_id']) ?>" class="text-muted" style="text-decoration: none;"><?= esc($cat['parent_name']) ?></a></li>
                            <li class="breadcrumb-item"><a href="<?= get_category_url($cat) ?>" class="text-muted" style="text-decoration: none;"><?= esc($cat['name']) ?></a></li>
                        <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?= get_category_url($cat) ?>" class="text-muted" style="text-decoration: none;"><?= esc($cat['name']) ?></a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;"><?= esc($product['name']) ?></li>
                </ol>
            </nav>
        </div>

        <!-- shop single -->
        <div class="shop-single pt-70 pb-5">
            <div class="container">
                <div class="row">
                    <!-- Product Gallery -->
                    <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                        <div class="shop-single-gallery">
                            <div class="flexslider-thumbnails">
                                <ul class="slides">
                                    <?php if (empty($product['images'])): ?>
                                        <li data-thumb="<?= base_url('assets/img/product/default.png') ?>">
                                            <img src="<?= base_url('assets/img/product/default.png') ?>" alt="#">
                                        </li>
                                    <?php else: ?>
                                        <?php foreach ($product['images'] as $img): ?>
                                            <li data-thumb="<?= base_url($img['image_path']) ?>">
                                                <img src="<?= base_url($img['image_path']) ?>" alt="#">
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Product Info Details -->
                    <div class="col-12 col-lg-6">
                        <div class="shop-single-info">
                            <h4 class="shop-single-title"><?= esc($product['name']) ?></h4>
                            <div class="shop-single-rating mb-2">
                                <?php
                                $fullStars = floor($avgRating);
                                $halfStar = ($avgRating - $fullStars) >= 0.5 ? 1 : 0;
                                $emptyStars = 5 - $fullStars - $halfStar;
                                
                                for ($i = 0; $i < $fullStars; $i++) echo '<i class="fas fa-star text-warning"></i>';
                                if ($halfStar) echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                for ($i = 0; $i < $emptyStars; $i++) echo '<i class="far fa-star text-warning"></i>';
                                ?>
                                <span class="rating-count text-muted ms-1"> (<?= $avgRating ?> rating - <?= $totalReviews ?> reviews)</span>
                            </div>

                            <!-- Pricing -->
                            <div class="shop-single-price mb-3">
                                <?php
                                $originalPrice = (float)$product['price'];
                                $discountPrice = $originalPrice;
                                $hasDiscount = false;
                                $badgeText = '';
                                if ($product['offer_value'] > 0) {
                                    $hasDiscount = true;
                                    if ($product['offer_type'] === 'percent') {
                                        $discountPrice = $originalPrice * (1 - $product['offer_value'] / 100);
                                        $badgeText = (int)$product['offer_value'] . '% Off';
                                    } else {
                                        $discountPrice = $originalPrice - $product['offer_value'];
                                        $badgeText = '₹' . (int)$product['offer_value'] . ' Off';
                                    }
                                }
                                ?>
                                <?php if ($hasDiscount): ?>
                                    <del>₹<?= number_format($originalPrice, 2) ?></del>
                                    <span class="amount">₹<?= number_format($discountPrice, 2) ?></span>
                                    <span class="discount-percentage"><?= $badgeText ?></span>
                                <?php else: ?>
                                    <span class="amount">₹<?= number_format($originalPrice, 2) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Short Description -->
                            <div class="mb-3 text-dark fw-normal product-short-desc" style="font-size: 0.95rem; line-height: 1.6;">
                                <?= html_entity_decode(htmlspecialchars_decode($product['short_description'] ?? '')) ?>
                            </div>

                            <!-- Form & Selectors -->
                            <form action="<?= base_url('cart/add') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                                <!-- Delivery Date Widget Directly Under Price & Short Description -->
                                <div class="shop-single-cs mt-3 p-3 border rounded bg-light" style="border-radius: 12px;">
                                    <?php if ($product['delivery_type'] === 'Express'): ?>
                                        <div class="delivery-selector-wrap">
                                            <h6 class="mb-2 fw-bold text-dark"><i class="far fa-truck-moving me-2 text-primary"></i> Select Delivery Date:</h6>
                                            <div class="d-flex gap-2 mb-3 mt-4">
                                                <label class="delivery-btn-label flex-fill text-center m-0">
                                                    <input type="radio" name="delivery_option" value="today" checked style="display:none;">
                                                    <div class="delivery-btn-pill py-2 border rounded">Today</div>
                                                </label>
                                                <label class="delivery-btn-label flex-fill text-center m-0">
                                                    <input type="radio" name="delivery_option" value="tomorrow" style="display:none;">
                                                    <div class="delivery-btn-pill py-2 border rounded">Tomorrow</div>
                                                </label>
                                                <label class="delivery-btn-label flex-fill text-center m-0" id="schedule-pill-label">
                                                    <input type="radio" name="delivery_option" value="schedule" style="display:none;" id="delivery-option-schedule">
                                                    <div class="delivery-btn-pill py-2 border rounded" id="schedule-pill-text">Schedule Date</div>
                                                </label>
                                            </div>
                                            
                                            <input type="hidden" name="delivery_date" id="delivery_date_hidden" value="<?= date('Y-m-d') ?>">
                                            <!-- <small class="text-danger d-block mt-2">* Order before 6:00 PM IST for today's delivery. Sundays excluded.</small> -->
                                        </div>
                                    <?php else: ?>
                                        <div class="delivery-selector-wrap">
                                            <h6 class="mb-1 fw-bold text-dark"><i class="far fa-truck me-2 text-primary"></i> Delivery Option: Courier</h6>
                                            <div class="text-dark fw-bold" style="font-size: 1.05rem; color: #2b9348 !important;">
                                                <i class="fas fa-calendar-check me-1"></i> Estimated Delivery by <?= date('d M Y (D)', strtotime('+7 days')) ?>
                                            </div>
                                            <input type="hidden" name="delivery_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Combo constituent products if any -->
                                <?php if ($product['product_type'] === 'combo' && !empty($product['combo_items'])): ?>
                                    <div class="combo-products-wrap mt-4 p-4 border rounded mb-4 shadow-sm" style="background-color: #fffaf8; border: 1px solid #ffd8cc !important; border-radius: 12px !important;">
                                        <h5 class="fw-bold mb-3" style="color: #e76f51; font-size: 1.05rem;"><i class="fas fa-gift me-2"></i> What's included in this Combo:</h5>
                                        <div class="row g-3">
                                            <?php foreach ($product['combo_items'] as $cItem): ?>
                                                <?php $itemImg = !empty($cItem['image_path']) ? base_url($cItem['image_path']) : base_url('assets/img/product/default.png'); ?>
                                                <div class="col-sm-6">
                                                    <div class="d-flex align-items-center bg-white p-2 border rounded" style="border-radius: 10px !important; border-color: #ffe6e0 !important;">
                                                        <img src="<?= $itemImg ?>" alt="<?= esc($cItem['name']) ?>" class="img-fluid rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <div>
                                                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #333;"><?= esc($cItem['name']) ?></h6>
                                                            <span class="badge" style="background-color: #e76f51; color: #fff; font-size: 0.75rem; padding: 3px 6px;">Qty: <?= $cItem['qty'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>



                                <!-- Quantity Selector -->
                                <div class="shop-single-qty-wrap my-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="m-0 fw-bold text-dark">Quantity:</h6>
                                        <input type="number" name="qty" class="form-control" value="1" min="1" max="10" style="width: 80px; border-radius: 6px;" required>
                                    </div>
                                </div>

                                <!-- Stock Status Badges & SKU (No prefix text, styled badges) -->
                                <div class="shop-single-sortinfo mb-3">
                                    <ul class="list-unstyled p-0 m-0 d-flex align-items-center gap-3">
                                        <li>
                                            <?php if ($product['is_active']): ?>
                                                <span class="badge bg-success" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 30px;"><i class="fas fa-check-circle me-1"></i> In Stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger" style="font-size: 0.85rem; padding: 6px 12px; border-radius: 30px;"><i class="fas fa-times-circle me-1"></i> Out of Stock</span>
                                            <?php endif; ?>
                                        </li>
                                        <li class="text-dark">SKU: <span class="fw-bold"><?= esc($product['sku']) ?></span></li>
                                    </ul>
                                </div>

                                <!-- Actions Block (Add to Cart + Buy Now) -->
                                <div class="shop-single-action mt-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="submit" class="theme-btn" <?= !$product['is_active'] ? 'disabled' : '' ?> style="padding: 12px 30px; font-weight: 600;"><span class="far fa-shopping-bag me-1"></span> Add To Cart</button>
                                        <button type="submit" name="buy_now" value="1" class="theme-btn buy-now-btn" style="background-color: #2b9348 !important; border-color: #2b9348 !important; padding: 12px 30px; font-weight: 600;" <?= !$product['is_active'] ? 'disabled' : '' ?>><span class="far fa-bolt me-1"></span> Buy Now</button>
                                        
                                        <?php
                                        $wishlist = session()->get('wishlist') ?: [];
                                        $inWishlist = in_array($product['id'], $wishlist);
                                        ?>
                                        <a href="#" class="theme-btn theme-btn2 wishlist-toggle-btn m-0 d-flex align-items-center justify-content-center" data-product-id="<?= $product['id'] ?>" data-tooltip="tooltip" title="Add To Wishlist" style="width: 50px; height: 50px; padding: 0;">
                                            <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- shop single details tabs -->
                <div class="shop-single-details mt-5">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-tab1" data-bs-toggle="tab" data-bs-target="#tab1"
                                type="button" role="tab" aria-controls="tab1" aria-selected="true">Description</button>
                            <button class="nav-link" id="nav-tab4" data-bs-toggle="tab" data-bs-target="#tab4"
                                type="button" role="tab" aria-controls="tab4" aria-selected="false">Shipping Info</button>
                            <button class="nav-link" id="nav-tab3" data-bs-toggle="tab" data-bs-target="#tab3"
                                type="button" role="tab" aria-controls="tab3" aria-selected="false">Reviews (<?= $totalReviews ?>)</button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="nav-tab1">
                            <div class="shop-single-desc">
                                <div><?= html_entity_decode(htmlspecialchars_decode($product['description'] ?? '')) ?></div>
                            </div>
                        </div>

                        <!-- Shipping Information Tab -->
                        <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="nav-tab4">
                            <div class="shop-single-additional">
                                <h5 class="fw-bold mb-3"><i class="far fa-truck me-2"></i> Shipping & Delivery Information</h5>
                                <div class="shipping-info-content text-dark" style="font-size: 0.95rem; line-height: 1.6; color: #000000 !important;">
                                    <?php if (($product['delivery_type'] ?? '') === 'Express'): ?>
                                        <?= html_entity_decode(htmlspecialchars_decode(get_setting('express_shipping_info') ?: '<p>Same-day express delivery is available for this product.</p>')) ?>
                                    <?php else: ?>
                                        <?= html_entity_decode(htmlspecialchars_decode(get_setting('courier_shipping_info') ?: '<p>Standard delivery in 5-7 business days via courier.</p>')) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="nav-tab3">
                            <div class="shop-single-review">
                                <div class="row">
                                    <!-- Left Column: Rating breakdown graph -->
                                    <div class="col-12 col-lg-4 mb-4 mb-lg-0">
                                        <div class="p-4 border rounded bg-white shadow-sm">
                                            <div class="text-center border-bottom pb-3 mb-3">
                                                <h1 class="display-3 fw-bold text-dark mb-1"><?= number_format($avgRating, 1) ?></h1>
                                                <div class="review-rating text-warning fs-4 mb-2">
                                                    <?php
                                                    $fullStars = floor($avgRating);
                                                    $halfStar = ($avgRating - $fullStars) >= 0.5 ? 1 : 0;
                                                    $emptyStars = 5 - $fullStars - $halfStar;
                                                    
                                                    for ($i = 0; $i < $fullStars; $i++) echo '<i class="fas fa-star text-warning"></i>';
                                                    if ($halfStar) echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                                    for ($i = 0; $i < $emptyStars; $i++) echo '<i class="far fa-star text-warning"></i>';
                                                    ?>
                                                </div>
                                                <p class="text-muted mb-0">Product Rating (<?= $totalReviews ?> Reviews)</p>
                                            </div>
                                            
                                            <h5 class="fw-bold mb-3 text-dark text-center">Rating Breakdown</h5>
                                            
                                            <?php for ($star = 5; $star >= 1; $star--): ?>
                                                <?php 
                                                $count = $ratingCounts[$star] ?? 0;
                                                $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                                ?>
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="text-dark me-2" style="width: 50px; font-size: 0.9rem;"><?= $star ?> star</span>
                                                    <div class="progress flex-fill" style="height: 12px; border-radius: 6px;">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent ?>%; border-radius: 6px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <span class="text-dark ms-2" style="width: 40px; font-size: 0.9rem; text-align: right;"><?= $percent ?>%</span>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Column: Reviews List & Form -->
                                    <div class="col-12 col-lg-8 ps-lg-4">
                                        <div class="blog-comments mt-0">
                                            <h5>Reviews (<?= $totalReviews ?>)</h5>
                                            <div class="blog-comments-wrap">
                                                <?php if (empty($reviews)): ?>
                                                    <div class="alert alert-light border text-center py-4">
                                                        <i class="far fa-comments fs-3 text-muted mb-2 d-block"></i>
                                                        <p class="mb-0 text-muted">No reviews yet. Be the first to write a review!</p>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($reviews as $rev): ?>
                                                        <div class="blog-comments-item mt-0 p-3 border rounded bg-white mb-3">
                                                            <img src="<?= !empty($rev['profile_photo']) ? base_url($rev['profile_photo']) : base_url('assets/img/product/default.png') ?>" alt="thumb" style="width: 55px; height: 55px; object-fit: cover; border-radius: 50%;">
                                                            <div class="blog-comments-content">
                                                                <h5><?= esc($rev['name']) ?></h5>
                                                                <span><i class="far fa-clock"></i> <?= date('d M Y, h:i A', strtotime($rev['created_at'])) ?></span>
                                                                <div class="review-rating text-warning mb-2">
                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                        <i class="<?= $i <= $rev['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                                                    <?php endfor; ?>
                                                                </div>
                                                                <p><?= esc($rev['review_text']) ?></p>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="blog-comments-form mt-4">
                                                <h4 class="mb-4">Leave A Review</h4>
                                                <?php if (session()->get('user_id')): ?>
                                                    <form action="<?= base_url('reviews/submit') ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                        <div class="row">
                                                            <div class="col-md-12 mb-3">
                                                                <div class="form-group">
                                                                    <label class="form-label text-dark fw-bold mb-1">Your Rating*</label>
                                                                    <select name="rating" class="form-control form-select" required>
                                                                        <option value="5">5 Stars</option>
                                                                        <option value="4">4 Stars</option>
                                                                        <option value="3">3 Stars</option>
                                                                        <option value="2">2 Stars</option>
                                                                        <option value="1">1 Star</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <div class="form-group">
                                                                    <label class="form-label text-dark fw-bold mb-1">Your Review*</label>
                                                                    <textarea name="review_text" class="form-control" rows="5" placeholder="Your Review*" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <button type="submit" class="theme-btn"><span class="far fa-paper-plane"></span> Submit Review</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                <?php else: ?>
                                                    <div class="alert alert-warning border text-center py-4">
                                                        <p class="mb-2">Only logged-in customers can submit reviews.</p>
                                                        <a href="<?= base_url('login') ?>" class="theme-btn btn-sm text-white">Login to Review</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- related item (1 row, 5 columns, total 10 items) -->
                <div class="product-area related-item pt-40">
                    <div class="container px-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="site-heading-inline">
                                    <h2 class="site-title">Related Items</h2>
                                </div>
                            </div>
                        </div>
                        <div class="related-items-row">
                            <?php if (empty($related_products)): ?>
                                <div class="col-12 text-center">
                                    <p class="text-muted">No related items found.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($related_products as $rp): ?>
                                    <?php
                                    $temp_product = $product;
                                    $product = $rp;
                                    include APPPATH . 'Views/frontend/sections/_card_vars.php';
                                    
                                    $wishlist = session()->get('wishlist') ?: [];
                                    $inWishlist = in_array($product['id'], $wishlist);
                                    ?>
                                    <div class="related-col mb-4">
                                        <div class="custom-card-item h-100">
                                            <!-- Image -->
                                            <div class="card-img-wrap">
                                                <a href="#" class="card-wishlist-btn wishlist-toggle-btn" data-product-id="<?= $product['id'] ?>" title="Add To Wishlist">
                                                    <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
                                                </a>
                                                <?php if ($hasDiscount): ?>
                                                    <span class="card-discount-badge">Sale</span>
                                                <?php endif; ?>
                                                <a href="<?= base_url('product/' . $product['slug']) ?>" class="d-block">
                                                    <img src="<?= $imageUrl ?>" alt="<?= esc($product['name']) ?>">
                                                </a>
                                            </div>

                                            <!-- Body -->
                                            <div class="card-body-content">
                                                <div class="card-icon-badge" style="background:<?= $badgeBg ?>;"><?= $iconHtml ?></div>

                                                <h4 class="card-title-text">
                                                    <a href="<?= base_url('product/' . $product['slug']) ?>"><?= esc($product['name']) ?></a>
                                                </h4>
                                                <p class="card-desc-text"><?= esc(mb_strimwidth($descText, 0, 75, '...')) ?></p>

                                                <div class="card-footer-info">
                                                    <!-- Price -->
                                                    <div class="card-price-value">
                                                        <?php if ($hasDiscount): ?>
                                                            <del>₹<?= number_format($originalPrice, 0) ?></del>
                                                            <span>₹<?= number_format($discountPrice, 0) ?></span>
                                                        <?php else: ?>
                                                            <span>₹<?= number_format($originalPrice, 0) ?></span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Add to cart button -->
                                                    <form action="<?= base_url('cart/add') ?>" method="post" class="m-0">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                        <input type="hidden" name="qty" value="1">
                                                        <button type="submit" class="card-action-arrow" title="Add To Cart">
                                                            <i class="fas fa-shopping-cart"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                    $product = $temp_product;
                                    ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- related item end -->
            </div>
        </div>
        <!-- shop single end -->

    </main>

    <!-- Custom CSS Overrides for Gallery Columns, Font Colors, Delivery Selector & Hover Zoom -->
    <style>
        /* Color Override to Black for Details & Content */
        .shop-single-desc, 
        .shop-single-additional, 
        .shop-single-info, 
        .shop-single-desc p, 
        .shop-single-additional p, 
        .product-short-desc, 
        p, 
        li, 
        .blog-comments-content p {
            color: #000000 !important;
        }

        /* FlexSlider Gallery: Thumbnails on the Left, Big image on the Right */
        @media (min-width: 768px) {
            .flexslider-thumbnails {
                display: flex !important;
                flex-direction: row-reverse !important;
                align-items: flex-start;
                gap: 15px;
            }
            .flexslider-thumbnails .flex-viewport {
                flex: 1;
                margin: 0 !important;
            }
            .flexslider-thumbnails .flex-control-thumbs {
                position: static !important;
                width: 80px !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 10px;
                margin: 0 !important;
                padding: 0 !important;
            }
            .flexslider-thumbnails .flex-control-thumbs li {
                width: 100% !important;
                margin: 0 !important;
                float: none !important;
            }
            .flexslider-thumbnails .flex-prev {
                left: 100px !important;
            }
            .flexslider-thumbnails .flex-control-thumbs li img {
                width: 70px !important;
                height: 70px !important;
                object-fit: cover;
                border-radius: 8px !important;
                border: 1px solid #ddd !important;
                cursor: pointer;
                padding: 2px;
                display: block;
                transition: all 0.2s ease;
            }
            .flexslider-thumbnails .flex-control-thumbs li img.flex-active {
                border-color: #0d6efd !important;
                border-width: 2px !important;
            }
        }

        /* Amazon Hover Zoom Styling */
        .shop-single-gallery .slides li {
            overflow: hidden !important;
            position: relative;
            border-radius: 8px;
        }
        .shop-single-gallery .slides li img {
            transition: transform 0.1s ease-out;
            cursor: zoom-in;
        }

        /* Delivery selector pills */
        .delivery-btn-label {
            cursor: pointer;
        }
        .delivery-btn-pill {
            transition: all 0.2s ease;
            background-color: #f8f9fa;
            border-color: #ddd !important;
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }
        .delivery-btn-label input:checked + .delivery-btn-pill {
            background-color: #e76f51;
            border-color: #e76f51 !important;
            color: #fff;
        }

        /* Related Items: 1 Row, 5 Columns layout grid */
        .related-items-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -10px;
            margin-left: -10px;
        }
        .related-items-row .related-col {
            flex: 0 0 20%;
            max-width: 20%;
            padding-right: 10px;
            padding-left: 10px;
        }
        @media (max-width: 991px) {
            .related-items-row .related-col {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }
        }
        @media (max-width: 575px) {
            .related-items-row .related-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        /* Custom Calendar Modal Styles matching the screenshot */
        #calendarModal .modal-content {
            border-radius: 20px !important;
            border: none !important;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15) !important;
        }
        #calendarModal .modal-header {
            padding: 20px 24px 10px !important;
            border-bottom: none !important;
        }
        #calendarModal .modal-body {
            padding: 20px 24px 24px !important;
        }
        .custom-calendar {
            width: 100%;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .calendar-header span {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
        }
        .calendar-weekdays {
            display: flex;
            margin-bottom: 10px;
        }
        .calendar-weekdays div {
            width: 14.28% !important;
            text-align: center;
            font-weight: 600;
            color: #888;
            font-size: 0.85rem;
        }
        .calendar-days {
            display: flex;
            flex-wrap: wrap;
        }
        .calendar-day {
            width: 14.28% !important;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            color: #333;
            border-radius: 50%;
            margin-bottom: 6px;
            transition: all 0.2s ease;
        }
        .calendar-day:hover:not(.disabled) {
            background-color: #f0f2f5;
        }
        .calendar-day.disabled {
            color: #ccc !important;
            text-decoration: line-through;
            cursor: not-allowed;
            opacity: 0.4;
        }
        .calendar-day.selected-date {
            background-color: #e76f51 !important;
            color: #fff !important;
            font-weight: 700;
        }

        @media (max-width: 991px) {
            .shop-single-action {
                position: fixed;
                bottom: 60px; /* 60px above bottom screen edge (above mobile tab bar) */
                left: 0;
                right: 0;
                background-color: #ffffff !important;
                padding: 12px 15px !important;
                box-shadow: 0 -4px 15px rgba(0,0,0,0.08) !important;
                z-index: 1020 !important;
                margin-top: 0 !important;
                border-top: 1px solid #eee !important;
            }
            .shop-single-action .d-flex {
                justify-content: space-between !important;
                width: 100% !important;
                gap: 10px !important;
            }
            .shop-single-action button {
                flex: 1 !important;
                margin: 0 !important;
                text-align: center !important;
                justify-content: center !important;
                height: 48px !important;
                padding: 0 !important;
                line-height: 48px !important;
                font-size: 0.9rem !important;
            }
            .shop-single-action .wishlist-toggle-btn {
                flex: 0 0 48px !important;
                height: 48px !important;
                width: 48px !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            /* Extra body padding on mobile to prevent bottom menu overlay */
            body {
                padding-bottom: 135px !important;
            }
        }
    </style>
    <!-- Custom Calendar Modal -->
    <div class="modal fade text-dark" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true" style="z-index: 10050;">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.15);">
                <div class="modal-header border-bottom-0 pb-0 justify-content-between align-items-center" style="padding: 20px 24px 10px;">
                    <h5 class="modal-title fw-bold text-center w-100" id="calendarModalLabel" style="font-size: 1.1rem; letter-spacing: 0.5px; text-transform: uppercase;">Select Delivery Date</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="margin-left: -30px;"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-2">
                    <div class="custom-calendar">
                        <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                            <button type="button" id="prev-month-btn" class="btn btn-sm btn-light rounded-circle" style="width:32px; height:32px; padding:0;"><i class="fas fa-chevron-left"></i></button>
                            <span id="calendar-month-year" class="fw-bold text-dark" style="font-size: 1.05rem;">July 2026</span>
                            <button type="button" id="next-month-btn" class="btn btn-sm btn-light rounded-circle" style="width:32px; height:32px; padding:0;"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="calendar-weekdays d-flex text-muted text-center fw-bold mb-2" style="font-size: 0.8rem;">
                            <div class="flex-fill">S</div>
                            <div class="flex-fill">M</div>
                            <div class="flex-fill">T</div>
                            <div class="flex-fill">W</div>
                            <div class="flex-fill">Th</div>
                            <div class="flex-fill">F</div>
                            <div class="flex-fill">S</div>
                        </div>
                        <div id="calendar-days" class="calendar-days d-flex flex-wrap text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JS for Express Date selection & Amazon Hover Zoom -->
    <script>
        window.addEventListener('load', function() {
            // 1. Amazon Hover Zoom implementation
            $(document).on('mousemove', '.flexslider-thumbnails .slides li img', function(e) {
                const rect = this.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                $(this).css({
                    'transform-origin': `${x}% ${y}%`,
                    'transform': 'scale(2.2)',
                    'position': 'relative',
                    'z-index': '10'
                });
            });
            
            $(document).on('mouseleave', '.flexslider-thumbnails .slides li img', function() {
                $(this).css({
                    'transform': 'scale(1)',
                    'transform-origin': 'center center',
                    'z-index': '1'
                });
            });

            // 2. Custom Calendar Logic
            let calendarDate = new Date();
            const today = new Date();
            today.setHours(0,0,0,0);

            function renderCalendar() {
                const year = calendarDate.getFullYear();
                const month = calendarDate.getMonth();
                
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                $('#calendar-month-year').text(`${monthNames[month]} ${year}`);
                
                const daysContainer = $('#calendar-days');
                daysContainer.empty();
                
                const firstDayIndex = new Date(year, month, 1).getDay();
                const lastDay = new Date(year, month + 1, 0).getDate();
                const prevLastDay = new Date(year, month, 0).getDate();
                
                // Prev month padding
                for (let x = firstDayIndex; x > 0; x--) {
                    daysContainer.append(`<div class="calendar-day disabled" style="opacity:0.2;">${prevLastDay - x + 1}</div>`);
                }
                
                // Current month days
                for (let i = 1; i <= lastDay; i++) {
                    const thisDate = new Date(year, month, i);
                    thisDate.setHours(0,0,0,0);
                    
                    const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
                    
                    if (thisDate < today) {
                        daysContainer.append(`<div class="calendar-day disabled">${i}</div>`);
                    } else {
                        const isToday = thisDate.getTime() === today.getTime() ? 'style="border: 1px solid #e76f51;"' : '';
                        const dayEl = $(`<div class="calendar-day" data-date="${dateStr}" ${isToday}>${i}</div>`);
                        
                        dayEl.on('click', function() {
                            $('.calendar-day').removeClass('selected-date');
                            $(this).addClass('selected-date');
                            
                            const selectedDateVal = $(this).data('date');
                            $('#delivery_date_hidden').val(selectedDateVal);
                            
                            // Format display date
                            const parsedDate = new Date(selectedDateVal);
                            const options = { day: 'numeric', month: 'short' };
                            const formatted = parsedDate.toLocaleDateString('en-US', options);
                            
                            $('#schedule-pill-text').text(`Scheduled: ${formatted}`);
                            $('#delivery-option-schedule').prop('checked', true).trigger('change');
                            
                            $('#calendarModal').modal('hide');
                        });
                        
                        daysContainer.append(dayEl);
                    }
                }
            }

            $('#prev-month-btn').on('click', function() {
                calendarDate.setMonth(calendarDate.getMonth() - 1);
                renderCalendar();
            });

            $('#next-month-btn').on('click', function() {
                calendarDate.setMonth(calendarDate.getMonth() + 1);
                renderCalendar();
            });

            // Open Modal on Schedule button click
            $(document).on('click', '#schedule-pill-label', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Initialize calendar days
                renderCalendar();
                
                // Show Bootstrap 5 Modal
                var calendarModalEl = document.getElementById('calendarModal');
                if (calendarModalEl) {
                    var modal = bootstrap.Modal.getOrCreateInstance(calendarModalEl);
                    modal.show();
                } else {
                    console.error("Calendar modal element not found!");
                }
            });

            // Delivery option pill change handlers for Today & Tomorrow
            $('input[name="delivery_option"]').on('change', function() {
                const option = $(this).val();
                if (option === 'today') {
                    $('#delivery_date_hidden').val('<?= date('Y-m-d') ?>');
                    $('#schedule-pill-text').text('Schedule Date');
                } else if (option === 'tomorrow') {
                    $('#delivery_date_hidden').val('<?= date('Y-m-d', strtotime('+1 day')) ?>');
                    $('#schedule-pill-text').text('Schedule Date');
                }
            });
        });
    </script>

    <style>
    .shop-single-gallery .flex-viewport img {
        width: 100% !important;
        padding: 0 !important;
        margin: 0 auto !important;
        object-fit: cover !important;
        border-radius: 12px !important;
    }
    .shop-single-gallery .flex-viewport {
        border: 1px solid rgba(0,0,0,0.06) !important;
        border-radius: 12px !important;
    }
    .shop-single-gallery .flex-control-thumbs {
        padding: 0 !important;
        margin: 15px 0 0 0 !important;
        list-style: none !important;
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: start !important;
        gap: 12px !important;
    }
    .shop-single-gallery .flex-control-thumbs li {
        margin: 0 !important;
        padding: 0 !important;
        width: 75px !important;
        height: 75px !important;
        float: none !important;
        display: block !important;
    }
    .shop-single-gallery .flex-control-thumbs li img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 8px !important;
        border: 1px solid rgba(0,0,0,0.08) !important;
        padding: 2px !important;
        transition: all 0.2s ease;
    }
    .shop-single-gallery .flex-control-thumbs li img.flex-active {
        border-color: #e76f51 !important;
        box-shadow: 0 0 0 2px rgba(231, 111, 81, 0.2) !important;
    }
    /* Fix absolute position overlap of rating stars on detail page tabs */
    .shop-single-review .review-rating {
        position: static !important;
        right: auto !important;
        top: auto !important;
    }
    </style>
<?= $this->endSection() ?>
