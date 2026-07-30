<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="container mt-3 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.88rem; background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page"><?= esc($category['name']) ?></li>
            </ol>
        </nav>
    </div>
    <!-- breadcrumb end -->

    <!-- category header short content (Clean View, No Card Box) -->
    <div class="container mb-3">
        <div class="category-header-clean py-1">
            <h1 class="fw-bold text-dark mb-2 category-title-heading" style="font-family: 'Outfit', sans-serif; font-size: 1.5rem;"><?= esc($category['name']) ?></h1>
            <?php if (!empty($category['summary'])): ?>
                <div class="category-summary-wrapper">
                    <div class="category-summary-text text-muted mb-1" id="categorySummaryText">
                        <?= html_entity_decode(htmlspecialchars_decode($category['summary'])) ?>
                    </div>
                    <button type="button" class="btn btn-link p-0 text-coral fw-bold text-decoration-none d-none" id="categoryReadMoreBtn" style="color: #e76f51; font-size: 0.85rem;">
                        <span class="btn-text">View More</span> <i class="far fa-chevron-down ms-1"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Offcanvas Filter Drawer (Desktop & Mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel"><i class="far fa-filter text-coral me-2" style="color:#e76f51;"></i>Filter Products</h5>
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
                            $uniqueColorsList = $db->table('colors')
                                ->where('is_active', 1)
                                ->orderBy('name', 'ASC')
                                ->get()
                                ->getResultArray();
                            
                            foreach ($uniqueColorsList as $colorRow):
                                $colorVal = $colorRow['name'];
                                $colorHex = $colorRow['color_code'] ?: strtolower($colorVal);
                                $isChecked = in_array($colorVal, $selected_colors);
                            ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input color-filter-checkbox" type="checkbox" name="colors[]" value="<?= esc($colorVal) ?>" id="color-<?= esc($colorVal) ?>" <?= $isChecked ? 'checked' : '' ?>>
                                    <label class="form-check-label d-flex align-items-center" for="color-<?= esc($colorVal) ?>" style="font-size: 0.9rem; cursor: pointer; text-transform: capitalize;">
                                        <span class="d-inline-block rounded-circle me-2" style="width: 14px; height: 14px; border: 1px solid #ddd; background-color: <?= esc($colorHex) ?>;"></span>
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

    <!-- shop-area -->
    <div class="shop-area pt-10 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="shop-sort mb-4">
                        <div class="shop-sort-box d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <button class="btn theme-btn btn-sm d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas" aria-controls="filterOffcanvas">
                                    <i class="far fa-filter"></i> <span>Filter</span>
                                </button>
                                <div class="shop-sort-show text-muted fw-medium" style="font-size: 0.9rem;">
                                    Showing <span id="showing-count"><?= count($products) ?></span> Results
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="sort-by-select d-flex align-items-center">
                                    <span class="me-2 text-muted" style="font-size: 0.9rem; white-space: nowrap;">Sort By:</span>
                                    <select class="form-select form-select-sm" id="sort-select" style="min-width: 160px; border-radius: 30px;">
                                        <option value="new" <?= ($sort === 'new' || empty($sort)) ? 'selected' : '' ?>>New</option>
                                        <option value="price_low_high" <?= ($sort === 'price_low_high') ? 'selected' : '' ?>>Price: Low to High</option>
                                        <option value="price_high_low" <?= ($sort === 'price_high_low') ? 'selected' : '' ?>>Price: High to Low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="shop-item-wrap">
                        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-2 g-md-3" id="product-grid-container">
                            <?php if (empty($products)): ?>
                                <div class="col-12 text-center py-5">
                                    <h4 class="text-muted">Products not found</h4>
                                </div>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <?= view('frontend/sections/_product_card_col', ['product' => $product]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Infinite Scroll Loader -->
                    <div id="infinite-scroll-loader" class="text-center py-4 d-none">
                        <div class="spinner-border text-coral" role="status" style="color: #e76f51;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Loading more products...</p>
                    </div>
                    
                    <div id="infinite-scroll-end" class="text-center py-4 text-muted d-none">
                        <p class="mb-0 fw-medium">✓ You've viewed all products</p>
                    </div>

                    <!-- category footer content (Clean View, No Card Box) -->
                    <?php if (!empty($category['footer_content'])): ?>
                        <div class="mt-5 text-dark category-footer-rich-text border-top pt-4">
                            <?= html_entity_decode(htmlspecialchars_decode($category['footer_content'])) ?>
                        </div>
                    <?php endif; ?>
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

    // Category Summary Read More / View More Toggle on Mobile
    var summaryText = document.getElementById('categorySummaryText');
    var readMoreBtn = document.getElementById('categoryReadMoreBtn');

    if (summaryText && readMoreBtn) {
        function checkSummaryLength() {
            if (window.innerWidth < 768) {
                if (summaryText.scrollHeight > 45) { // text longer than ~2 lines
                    summaryText.classList.add('clamped');
                    readMoreBtn.classList.remove('d-none');
                } else {
                    summaryText.classList.remove('clamped');
                    readMoreBtn.classList.add('d-none');
                }
            } else {
                summaryText.classList.remove('clamped');
                readMoreBtn.classList.add('d-none');
            }
        }

        checkSummaryLength();
        window.addEventListener('resize', checkSummaryLength);

        readMoreBtn.addEventListener('click', function() {
            if (summaryText.classList.contains('clamped')) {
                summaryText.classList.remove('clamped');
                this.querySelector('.btn-text').textContent = 'View Less';
                this.querySelector('i').className = 'far fa-chevron-up ms-1';
            } else {
                summaryText.classList.add('clamped');
                this.querySelector('.btn-text').textContent = 'View More';
                this.querySelector('i').className = 'far fa-chevron-down ms-1';
            }
        });
    }

    // Infinite Scroll Implementation
    let currentPage = 1;
    let totalPages = <?= $pager ? $pager->getPageCount() : 1 ?>;
    let isLoading = false;

    if (currentPage >= totalPages) {
        // If initial load already fetched all products
        const endMsg = document.getElementById('infinite-scroll-end');
        if (endMsg && <?= count($products) ?> > 0) {
            endMsg.classList.remove('d-none');
        }
    }

    function loadMoreProducts() {
        if (isLoading || currentPage >= totalPages) return;
        
        isLoading = true;
        const loader = document.getElementById('infinite-scroll-loader');
        if (loader) loader.classList.remove('d-none');
        
        currentPage++;
        
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('page', currentPage);
        
        fetch(currentUrl.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.html) {
                const container = document.getElementById('product-grid-container');
                container.insertAdjacentHTML('beforeend', data.html);
                
                const countEl = document.getElementById('showing-count');
                if (countEl) {
                    countEl.textContent = container.children.length;
                }
                
                totalPages = data.page_count;
                if (!data.has_more || currentPage >= totalPages) {
                    const endMsg = document.getElementById('infinite-scroll-end');
                    if (endMsg) endMsg.classList.remove('d-none');
                }
            } else {
                const endMsg = document.getElementById('infinite-scroll-end');
                if (endMsg) endMsg.classList.remove('d-none');
            }
        })
        .catch(err => console.error('Error loading products:', err))
        .finally(() => {
            isLoading = false;
            if (loader) loader.classList.add('d-none');
        });
    }

    window.addEventListener('scroll', function() {
        if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 800)) {
            loadMoreProducts();
        }
    });
});
</script>

<?= $this->endSection() ?>
