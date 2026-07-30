<?php
$productModel = new \App\Models\ProductModel();
$limit = 8;
$title = 'Popular Items';
$subtitle = 'Popular';
$sidebarImage = 'assets/img/banner/side-banner.jpg';
$view_more_link = 'shop';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $limit = $content['limit'] ?? $limit;
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
    $sidebarImage = $content['sidebar_image'] ?? $sidebarImage;
    $view_more_link = $content['view_more_link'] ?? $view_more_link;
}

$bestSellers = $productModel->getBestSellers($limit);
$onSale = $productModel->getOnSale($limit);
$topRated = $productModel->getTopRated($limit);
$trending = $productModel->getTrending($limit);
?>
<!-- popular item -->
<div class="product-area pb-100">
    <div class="container">
        <div class="row g-4">
            <!-- Left Side Banner -->
            <div class="col-lg-3">
                <div class="small-banner mt-2 wow fadeInLeft" data-wow-delay=".25s" style="height: 100%;">
                    <div class="banner-item" style="border-radius: 10px; overflow: hidden; height: 100%;">
                        <img src="<?= base_url(esc($sidebarImage)) ?>" alt="Popular Bestsellers" style="height: 100%; object-fit: cover; width: 100%; border-radius: 10px;">
                    </div>
                </div>
            </div>
            
            <!-- Right Side Tabs & Products Grid -->
            <div class="col-lg-9">
                <!-- Flex Header -->
                <div class="row align-items-center mb-4">
                    <div class="col-md-5 text-start">
                        <div class="site-heading mb-0 text-start" style="text-align: left !important;">
                            <span class="site-title-tagline" style="margin-left: 0; justify-content: flex-start;"><?= esc($subtitle) ?></span>
                            <h2 class="site-title" style="text-align: left !important; font-size: 1.8rem;"><?= esc($title) ?></h2>
                        </div>
                    </div>
                    <div class="col-md-7 text-end d-flex align-items-center justify-content-end gap-3 flex-wrap">
                        <ul class="nav nav-pills" id="item-tab-popular" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pop-tab1" data-bs-toggle="pill"
                                    data-bs-target="#pill-pop-tab1" type="button" role="tab"
                                    aria-controls="pill-pop-tab1" aria-selected="true" style="border-radius: 10px;">Best Sellers</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pop-tab2" data-bs-toggle="pill"
                                    data-bs-target="#pill-pop-tab2" type="button" role="tab"
                                    aria-controls="pill-pop-tab2" aria-selected="false" style="border-radius: 10px;">On Sale</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pop-tab3" data-bs-toggle="pill"
                                    data-bs-target="#pill-pop-tab3" type="button" role="tab"
                                    aria-controls="pill-pop-tab3" aria-selected="false" style="border-radius: 10px;">Top Rated</button>
                            </li>
                        </ul>
                        <?php if (!empty($view_more_link)): ?>
                            <a href="<?= base_url(esc($view_more_link)) ?>" class="theme-btn btn-sm text-nowrap" style="background-color: #ff3366; color: white; border: none; border-radius: 10px; padding: 8px 20px; font-weight: bold; text-decoration: none;">View More <i class="fas fa-arrow-right ms-1"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-content wow fadeInUp" data-wow-delay=".25s" id="item-tabContent-popular">
                    <!-- Tab 1: Best Sellers -->
                    <div class="tab-pane show active" id="pill-pop-tab1" role="tabpanel" aria-labelledby="pop-tab1" tabindex="0">
                        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3">
                            <?php if (empty($bestSellers)): ?>
                                <div class="col-12"><p class="text-muted">No best sellers found.</p></div>
                            <?php else: ?>
                                <?php foreach ($bestSellers as $product): ?>
                                    <div class="col d-flex align-items-stretch">
                                        <?= view('frontend/sections/_product_card_single', ['product' => $product]) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Tab 2: On Sale -->
                    <div class="tab-pane" id="pill-pop-tab2" role="tabpanel" aria-labelledby="pop-tab2" tabindex="0">
                        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3">
                            <?php if (empty($onSale)): ?>
                                <div class="col-12"><p class="text-muted">No on-sale products found.</p></div>
                            <?php else: ?>
                                <?php foreach ($onSale as $product): ?>
                                    <div class="col d-flex align-items-stretch">
                                        <?= view('frontend/sections/_product_card_single', ['product' => $product]) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Tab 3: Top Rated -->
                    <div class="tab-pane" id="pill-pop-tab3" role="tabpanel" aria-labelledby="pop-tab3" tabindex="0">
                        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3">
                            <?php if (empty($topRated)): ?>
                                <div class="col-12"><p class="text-muted">No top-rated products found.</p></div>
                            <?php else: ?>
                                <?php foreach ($topRated as $product): ?>
                                    <div class="col d-flex align-items-stretch">
                                        <?= view('frontend/sections/_product_card_single', ['product' => $product]) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- popular item end -->

<style>
#item-tab-popular .nav-link {
    color: #4a5568 !important;
    background-color: transparent !important;
    font-weight: 600 !important;
    border: 1px solid transparent !important;
    padding: 8px 18px !important;
    transition: all 0.2s ease-in-out !important;
    border-radius: 10px !important;
}
#item-tab-popular .nav-link.active {
    color: #ffffff !important;
    background-color: #e76f51 !important; /* Brand Coral */
    border-color: #e76f51 !important;
}
#item-tab-popular .nav-link:hover:not(.active) {
    color: #e76f51 !important;
}
</style>
