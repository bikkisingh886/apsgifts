<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.9rem; background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page"><?= esc($category['name']) ?></li>
            </ol>
        </nav>
    </div>
    <!-- breadcrumb end -->

    <!-- shop-area -->
    <div class="shop-area pt-20 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
                        <div class="offcanvas-header d-lg-none">
                            <h5 class="offcanvas-title" id="filterOffcanvasLabel">Filter Products</h5>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <form id="filter-form" action="" method="get" class="w-100">
                                <input type="hidden" name="sort" id="sort-hidden" value="<?= esc($sort) ?>">
                                <div class="shop-sidebar">
                                    <div class="shop-widget">
                                        <div class="shop-search-form">
                                            <h4 class="shop-widget-title">Search</h4>
                                            <div class="form-group">
                                                <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= esc($search) ?>">
                                                <button type="submit"><i class="far fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="shop-widget">
                                        <h4 class="shop-widget-title">Category</h4>
                                        <ul class="shop-category-list">
                                            <?php 
                                            $db = \Config\Database::connect();
                                            $allCats = $db->table('categories c')
                                                ->select('c.*, p.slug as parent_slug')
                                                ->join('categories p', 'p.id = c.parent_id', 'left')
                                                ->where('c.is_active', 1)
                                                ->get()
                                                ->getResultArray();
                                            foreach ($allCats as $c): 
                                                $isActive = ($c['slug'] === $category['slug']);
                                            ?>
                                                <li class="<?= $isActive ? 'active' : '' ?>">
                                                    <a href="<?= get_category_url($c) ?>">
                                                        <?php if ($isActive): ?>
                                                            <i class="far fa-check-square me-2 text-coral" style="color: #e76f51;"></i>
                                                        <?php else: ?>
                                                            <i class="far fa-square me-2 text-muted"></i>
                                                        <?php endif; ?>
                                                        <?= esc($c['name']) ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    
                                    <div class="shop-widget">
                                        <h4 class="shop-widget-title">Price Range</h4>
                                        <div class="price-range-box">
                                            <div class="price-range-input">
                                                <input type="text" id="price-amount" readonly>
                                                <input type="hidden" name="min_price" id="min_price" value="<?= esc($min_price) ?>">
                                                <input type="hidden" name="max_price" id="max_price" value="<?= esc($max_price) ?>">
                                            </div>
                                            <div class="price-range"></div>
                                        </div>
                                    </div>

                                    <div class="shop-widget">
                                        <h4 class="shop-widget-title">Colors</h4>
                                        <div class="shop-color-list" style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                                            <?php
                                            $distinctColorsQuery = $db->table('products')
                                                ->select('color')
                                                ->where('color IS NOT NULL')
                                                ->where('color !=', '')
                                                ->where('is_active', 1)
                                                ->where('hide_from_frontend', 0)
                                                ->get()
                                                ->getResultArray();
                                            
                                            $allColors = [];
                                            foreach ($distinctColorsQuery as $col) {
                                                $parts = explode(',', $col['color']);
                                                foreach ($parts as $part) {
                                                    $trimmed = trim($part);
                                                    if ($trimmed !== '') {
                                                        $allColors[] = $trimmed;
                                                    }
                                                }
                                            }
                                            $uniqueColors = array_unique($allColors);
                                            sort($uniqueColors);
                                            
                                            foreach ($uniqueColors as $colorVal):
                                                $isChecked = in_array($colorVal, $selected_colors);
                                            ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input color-filter-checkbox" type="checkbox" name="colors[]" value="<?= esc($colorVal) ?>" id="color-<?= esc($colorVal) ?>" <?= $isChecked ? 'checked' : '' ?>>
                                                    <label class="form-check-label d-flex align-items-center" for="color-<?= esc($colorVal) ?>" style="font-size: 0.9rem; cursor: pointer; text-transform: capitalize;">
                                                        <span class="d-inline-block rounded-circle me-2" style="width: 14px; height: 14px; border: 1px solid #ddd; background-color: <?= strtolower($colorVal) ?>;"></span>
                                                        <?= esc($colorVal) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="col-md-12">
                        <div class="shop-sort">
                            <div class="shop-sort-box d-flex align-items-center justify-content-between w-100">
                                <div class="shop-sort-show">Showing <?= count($products) ?> Results</div>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sort-by-select d-flex align-items-center">
                                        <span class="me-2 text-muted" style="font-size: 0.9rem; white-space: nowrap;">Sort By:</span>
                                        <select class="form-select form-select-sm" id="sort-select" style="min-width: 160px; border-radius: 30px;">
                                            <option value="new" <?= ($sort === 'new' || empty($sort)) ? 'selected' : '' ?>>New</option>
                                            <option value="price_low_high" <?= ($sort === 'price_low_high') ? 'selected' : '' ?>>Price: Low to High</option>
                                            <option value="price_high_low" <?= ($sort === 'price_high_low') ? 'selected' : '' ?>>Price: High to Low</option>
                                        </select>
                                    </div>
                                    <button class="btn theme-btn btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
                                        <i class="far fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="shop-item-wrap">
                        <div class="row g-4">
                            <?php if (empty($products)): ?>
                                <div class="col-12 text-center py-5">
                                    <h4 class="text-muted">No products found in this category.</h4>
                                </div>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    include APPPATH . 'Views/frontend/sections/_card_vars.php';
                                    
                                    $wishlist = session()->get('wishlist') ?: [];
                                    $inWishlist = in_array($product['id'], $wishlist);
                                    ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="custom-card-item">
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
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- pagination -->
                    <?php if ($pager): ?>
                        <div class="pagination-area mt-50">
                            <?= $pager->links('default', 'bootstrap_pagination') ?>
                        </div>
                    <?php endif; ?>
                    <!-- pagination end -->
                </div>
            </div>
        </div>
    </div>
    <!-- shop-area end -->

</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var minVal = <?= !empty($min_price) ? (int)$min_price : 0 ?>;
    var maxVal = <?= !empty($max_price) ? (int)$max_price : 2000 ?>;
    
    // Check if jQuery and UI Slider are available
    if (typeof jQuery !== 'undefined' && jQuery.fn.slider) {
        jQuery(".price-range").slider({
            range: true,
            min: 0,
            max: 2000,
            values: [minVal, maxVal],
            slide: function (event, ui) {
                jQuery("#price-amount").val("₹" + ui.values[0] + " - ₹" + ui.values[1]);
                jQuery("#min_price").val(ui.values[0]);
                jQuery("#max_price").val(ui.values[1]);
            },
            stop: function(event, ui) {
                jQuery("#filter-form").submit();
            }
        });
        jQuery("#price-amount").val("₹" + minVal + " - ₹" + maxVal);
        jQuery("#min_price").val(minVal);
        jQuery("#max_price").val(maxVal);
    }

    // Auto submit color filter checkboxes on check/uncheck
    var colorCheckboxes = document.querySelectorAll('.color-filter-checkbox');
    colorCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    });

    // Handle Sorting dropdown selection
    var sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            var url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }
});
</script>

<?= $this->endSection() ?>
