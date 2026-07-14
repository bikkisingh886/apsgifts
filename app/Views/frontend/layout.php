<?php
$authLib = new \App\Libraries\AuthLib();
$cartLib = new \App\Libraries\CartLib();
$categoryModel = new \App\Models\CategoryModel();
$cityModel = new \App\Models\CityModel();

$cartItems = $cartLib->contents();
$cartCount = $cartLib->totalItems();
$wishlistCount = count(session()->get('wishlist') ?: []);
$categories = $categoryModel->getWithProductCounts(true);
$popularCities = $cityModel->where('is_active', 1)->where('is_popular', 1)->orderBy('name', 'ASC')->findAll();
$allCities = $cityModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
$categoryTree = cache()->get('category_tree_menu');
if (!$categoryTree) {
    $categoryTree = $categoryModel->getCategoryTree(true);
    cache()->save('category_tree_menu', $categoryTree, 86400); // Cache for 24 hours
}

//dynamic menu tree
$menuModel = new \App\Models\MenuModel();
$menuItemModel = new \App\Models\MenuItemModel();
$frontendMenu = cache()->get('frontend_main_menu');
if (!$frontendMenu) {
    $menu = $menuModel->where('slug', 'main-menu')->first();
    if ($menu) {
        $items = $menuItemModel->where('menu_id', $menu['id'])->orderBy('sort_order', 'ASC')->findAll();
        
        $frontendMenu = [];
        $indexed = [];
        foreach ($items as $it) {
            $it['children'] = [];
            $indexed[$it['id']] = $it;
        }
        
        foreach ($indexed as $id => &$it) {
            if ($it['parent_id'] === null) {
                $frontendMenu[] = &$it;
            } else {
                $parentId = $it['parent_id'];
                if (isset($indexed[$parentId])) {
                    $indexed[$parentId]['children'][] = &$it;
                }
            }
        }
        unset($it);
        cache()->save('frontend_main_menu', $frontendMenu, 86400);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($meta_title ?? get_setting('company_name', 'OyeGifts') . ' - Gifts Shop') ?></title>
    <meta name="description" content="<?= esc($meta_desc ?? '') ?>">
    <meta name="keywords" content="gifts, cake, flowers, online delivery">

    <!-- Open Graph / Facebook SEO Tags -->
    <meta property="og:type" content="<?= esc($og_type ?? 'website') ?>">
    <meta property="og:title" content="<?= esc($og_title ?? $meta_title ?? get_setting('company_name', 'OyeGifts')) ?>">
    <meta property="og:description" content="<?= esc($og_desc ?? $meta_desc ?? '') ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <?php if (!empty($og_image)): ?>
        <meta property="og:image" content="<?= base_url(esc($og_image)) ?>">
    <?php elseif ($logo = get_setting('company_logo')): ?>
        <meta property="og:image" content="<?= base_url(esc($logo)) ?>">
    <?php endif; ?>

    <!-- Twitter Card SEO Tags -->
    <meta name="twitter:card" content="<?= esc($twitter_card ?? 'summary_large_image') ?>">
    <meta name="twitter:title" content="<?= esc($twitter_title ?? $meta_title ?? get_setting('company_name', 'OyeGifts')) ?>">
    <meta name="twitter:description" content="<?= esc($twitter_desc ?? $meta_desc ?? '') ?>">
    <?php if (!empty($twitter_image)): ?>
        <meta name="twitter:image" content="<?= base_url(esc($twitter_image)) ?>">
    <?php elseif (!empty($og_image)): ?>
        <meta name="twitter:image" content="<?= base_url(esc($og_image)) ?>">
    <?php elseif ($logo = get_setting('company_logo')): ?>
        <meta name="twitter:image" content="<?= base_url(esc($logo)) ?>">
    <?php endif; ?>

    <!-- Schema Markup JSON-LD -->
    <?php if (!empty($schema_markup)): ?>
        <?php if (strpos($schema_markup, '<script') !== false): ?>
            <?= $schema_markup ?>
        <?php else: ?>
            <script type="application/ld+json">
            <?= $schema_markup ?>
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/logo/favicon.png') ?>">

    <!-- css -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/all-fontawesome.min.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/magnific-popup.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/nice-select.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/flex-slider.min.css') ?>">
    
    <style>
        /* Premium Mega Menu Styling */
        @media (min-width: 992px) {
            .navbar-nav .dropdown.position-static {
                position: static !important;
            }
            .navbar-nav .mega-dropdown-menu {
                width: 100%;
                left: 0;
                right: 0;
                top: 100%;
                padding: 30px;
                border: 0;
                border-radius: 0 0 15px 15px;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
                display: none;
                background-color: #ffffff;
                border-top: 3px solid #e76f51;
            }
            .navbar-nav .dropdown:hover .mega-dropdown-menu {
                display: flex !important;
                flex-wrap: wrap;
            }
            .mega-menu-column {
                flex: 1;
                min-width: 180px;
                padding: 0 15px;
                margin-bottom: 20px;
            }
            .mega-menu-column h6 {
                font-weight: 700;
                color: #e76f51;
                text-transform: uppercase;
                font-size: 0.9rem;
                border-bottom: 2px solid #f0f2f5;
                padding-bottom: 8px;
                margin-bottom: 12px;
            }
            .mega-menu-column ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            .mega-menu-column ul li {
                margin-bottom: 8px;
            }
            .mega-menu-column ul li a {
                color: #4a5568 !important;
                text-decoration: none;
                font-size: 0.88rem;
                font-weight: 500;
                transition: all 0.2s ease;
                padding: 0 !important;
                display: inline-block;
            }
            .mega-menu-column ul li a:hover {
                color: #e76f51 !important;
                padding-left: 4px !important;
                background: transparent !important;
            }
        }

        /* Custom OyeGifts Header & Live Search styles */
        .oyegifts-header {
            width: 100%;
            background-color: #fff;
            position: relative;
            z-index: 1040;
        }
        .oyegifts-header-top {
            background-color: #fdf0eb; /* warm light peach-beige background */
            transition: background-color 0.3s;
        }
        .oyegifts-header-top.search-active {
            background-color: #ffffff !important;
            border-bottom: 1px solid #dee2e6;
        }
        .oyegifts-logo {
            font-family: 'Outfit', 'Inter', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #333;
            display: flex;
            align-items: center;
        }
        .oyegifts-logo-gifts {
            color: #333;
        }
        .oyegifts-logo-heart {
            font-size: 1.2rem;
            margin-left: 2px;
            color: #e76f51; /* orange-red heart */
            display: inline-block;
            transform: translateY(-2px);
        }
        .oyegifts-search-input {
            background-color: #ffffff;
            font-size: 0.95rem;
            color: #333;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }
        .oyegifts-search-input:focus {
            box-shadow: 0 0 12px rgba(231, 111, 81, 0.18) !important;
            border-color: #e76f51 !important;
        }
        .text-coral {
            color: #e76f51 !important;
        }
        .oyegifts-menu-link {
            transition: color 0.2s;
            font-size: 0.88rem;
        }
        .oyegifts-menu-link:hover {
            color: #e76f51 !important;
        }
        
        /* Dropdowns hover animations */
        .oyegifts-menu-bar .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
            transition: transform 0.2s;
        }
        .oyegifts-menu-bar .dropdown:hover .dropdown-toggle::after {
            transform: rotate(180deg);
        }
        .oyegifts-menu-bar .dropdown:hover > .dropdown-menu {
            display: block !important;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .oyegifts-menu-bar .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            min-width: 11rem;
            padding: 0.5rem 0;
            margin: 0;
            background-color: #fff;
            border: 0;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        /* Cart button circular */
        .oyegifts-cart-btn {
            border: 1px solid #eee;
            transition: all 0.2s;
        }
        .oyegifts-cart-btn:hover {
            background-color: #e76f51 !important;
        }
        .oyegifts-cart-btn:hover i {
            color: #ffffff !important;
        }

        /* Top Announcement Bar */
        .oyegifts-announcement-bar {
            background: linear-gradient(90deg, #e76f51, #f4a261);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            z-index: 1050;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .oyegifts-announcement-bar a {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Premium Pill Cart Button */
        .oyegifts-cart-btn-new {
            background: linear-gradient(135deg, #e76f51, #f4a261) !important;
            border: none;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .oyegifts-cart-btn-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(231, 111, 81, 0.3) !important;
        }
        .oyegifts-cart-btn-new:active {
            transform: translateY(0);
        }

        /* Wishlist Button */
        .oyegifts-wishlist-btn {
            border: 1px solid #eee;
            background-color: #ffffff;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .oyegifts-wishlist-btn:hover {
            background-color: #e76f51 !important;
            border-color: #e76f51 !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(231, 111, 81, 0.2) !important;
        }
        .oyegifts-wishlist-btn:hover i {
            color: #ffffff !important;
        }
        .oyegifts-wishlist-btn:active {
            transform: translateY(0);
        }

        /* Mobile Action Buttons */
        .mobile-action-btn {
            width: 38px;
            height: 38px;
            background-color: #fdf0eb;
            color: #e76f51;
            transition: all 0.2s ease;
        }
        .mobile-action-btn:hover {
            background-color: #e76f51;
            color: #ffffff;
        }

        /* Sticky Header */
        .oyegifts-header.sticky {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            animation: stickySlideDown 0.3s ease-out;
            z-index: 1080;
        }
        @keyframes stickySlideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        /* Force Show Owl Carousel Navigation Arrows */
        .product-slider .owl-nav, 
        .product-slider .owl-nav.disabled {
            display: block !important;
        }

        /* Live Suggestions Overlay Styling */
        .search-suggestions-overlay {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            z-index: 1050;
            border-top: 1px solid #dee2e6;
            min-height: 450px;
            max-height: 600px;
            overflow-y: auto;
            animation: slideDown 0.25s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .suggestions-product-card {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 10px;
            transition: all 0.2s;
            background-color: #ffffff;
        }
        .suggestions-product-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border-color: #e76f51;
            transform: translateY(-2px);
        }
        .suggestions-product-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .suggestions-product-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 34px;
        }
        .suggestions-product-price {
            font-size: 0.9rem;
            font-weight: 700;
            color: #e76f51;
        }

        /* Suggestions list links */
        .suggestion-item-link {
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            background-color: #f8f9fa;
        }
        .suggestion-item-link:hover {
            background-color: #fdf0eb;
            color: #e76f51;
            padding-left: 16px;
        }

        /* Mobile full screen overlay */
        .mobile-suggestions-overlay {
            position: fixed;
            top: 105px;
            left: 0;
            right: 0;
            bottom: 0;
            background: #ffffff;
            z-index: 2000;
            overflow-y: auto;
            padding-bottom: 80px;
        }
        
        .small-link {
            color: #555;
            font-size: 0.85rem;
            padding: 4px 0;
            display: block;
        }
        .small-link:hover {
            color: #e76f51;
            padding-left: 4px;
        }

        /* Custom Pagination styles */
        .pagination {
            display: flex;
            padding-left: 0;
            list-style: none;
        }
        .pagination .page-link {
            color: #e76f51;
            border: 1px solid #dee2e6;
            border-radius: 8px !important;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            background-color: #fff;
        }
        .pagination .page-item.active .page-link {
            background-color: #e76f51;
            border-color: #e76f51;
            color: #fff;
        }
        .pagination .page-link:hover {
            background-color: #fdf0eb;
            color: #d65d40;
            border-color: #e76f51;
        }

        /* Wishlist button inside custom card */
        .custom-card-item .card-wishlist-btn {
            position: absolute;
            left: 12px;
            top: 12px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e76f51;
            font-size: 0.9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 10;
            transition: transform 0.2s ease, background 0.2s ease;
            text-decoration: none;
        }
        .custom-card-item .card-wishlist-btn:hover {
            transform: scale(1.1);
            background: #ffffff;
            color: #d65d40;
        }
        
        /* Offcanvas sidebar styling */
         @media (max-width: 991px) {
            .offcanvas-lg.offcanvas-start {
                width: 300px;
                padding: 20px;
                background-color: #ffffff;
            }
            .offcanvas-header {
                border-bottom: 1px solid #f0f0f0;
                padding-bottom: 15px;
                margin-bottom: 15px;
            }
            .offcanvas-body {
                padding: 0;
            }
        }

        /* Sticky Bottom Tab Bar for Mobile */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #ffffff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.08);
            z-index: 2000;
            border-top: 1px solid #eee;
        }
        .mobile-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #718096;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            width: 20%;
            height: 100%;
            transition: all 0.2s ease;
        }
        .mobile-nav-link i {
            font-size: 1.2rem;
            margin-bottom: 3px;
            color: #718096;
        }
        .mobile-nav-link.active,
        .mobile-nav-link.active i {
            color: #e76f51 !important;
        }
        .mobile-nav-link:hover,
        .mobile-nav-link:hover i {
            color: #e76f51 !important;
        }
        
        /* Ensure mobile menu offcanvas stops exactly above the bottom tab bar and scrolls natively */
        #offcanvasNavbar {
            bottom: 60px !important;
            height: calc(100vh - 60px) !important;
            overflow: hidden !important; /* Prevent double scrollbars */
        }
        #offcanvasNavbar .offcanvas-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important; /* Enable smooth momentum scroll on iOS */
            padding-bottom: 30px !important;
        }
        /* Extra body padding on mobile to not clip behind the sticky nav */
        @media (max-width: 991px) {
            body {
                padding-bottom: 65px !important;
            }
        }

        /* Force all footer text to be white, preventing black text */
        .footer-area,
        .footer-area p,
        .footer-area span,
        .footer-area li,
        .footer-area li a,
        .footer-area a,
        .footer-area .footer-widget-box p,
        .footer-area .footer-contact li,
        .footer-area .footer-contact li a,
        .footer-area .copyright-text,
        .footer-area .copyright-text a,
        .footer-area .footer-social span {
            color: #ffffff !important;
        }
        .footer-area a:hover,
        .footer-area li a:hover,
        .footer-area .copyright-text a:hover {
            color: var(--theme-color) !important;
        }
    </style>
</head>

<body>

    <!-- preloader -->
    <div class="preloader">
        <div class="loader-ripple">
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- preloader end -->


    <!-- Top Announcement Bar -->
    <?php 
    $announcement = get_setting('announcement_text');
    if (!empty($announcement)): 
    ?>
    <div class="oyegifts-announcement-bar py-2 text-center text-white">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-center gap-2">
            <i class="fas fa-gift animate__animated animate__bounce animate__infinite animate__slower" style="font-size: 1rem;"></i>
            <span class="m-0 text-center" style="font-size: 0.88rem; font-weight: 600;"><?= esc($announcement) ?></span>
            <i class="fas fa-gift animate__animated animate__bounce animate__infinite animate__slower" style="font-size: 1rem;"></i>
        </div>
    </div>
    <?php endif; ?>

    <!-- header area -->
    <header class="oyegifts-header">
        <!-- Desktop Header Top Bar -->
        <div class="oyegifts-header-top d-none d-lg-block">
            <div class="container-fluid px-4 d-flex align-items-center justify-content-between py-3">
                <!-- Logo -->
                <a href="<?= base_url() ?>" class="oyegifts-logo d-flex align-items-center text-decoration-none">
                    <?php if ($logo = get_setting('company_logo')): ?>
                        <img src="<?= base_url($logo) ?>" alt="<?= esc(get_setting('company_name', 'OyeGifts')) ?>" style="max-height: 45px; object-fit: contain;">
                    <?php else: ?>
                        <span class="oyegifts-logo-text">
                            <?php 
                                $cName = get_setting('company_name', 'OyeGifts');
                                if (stripos($cName, 'gifts') !== false) {
                                    $parts = preg_split('/(?=gifts)/i', $cName, 2);
                                    echo esc($parts[0]) . '<span class="oyegifts-logo-gifts">' . esc($parts[1] ?? '') . '</span>';
                                } else {
                                    echo esc($cName);
                                }
                            ?>
                        </span>
                        <span class="oyegifts-logo-heart"><i class="fas fa-heart text-danger"></i></span>
                    <?php endif; ?>
                </a>

                <!-- Search Bar Wrapper -->
                <div class="oyegifts-search-wrapper position-relative flex-grow-1 mx-5" style="max-width: 800px;">
                    <form action="<?= base_url('search') ?>" method="get" class="m-0 w-100">
                        <input type="text" name="q" id="desktop-search-input" class="form-control oyegifts-search-input py-2 px-4 rounded-pill border-0 shadow-sm" placeholder="Search cakes, flowers, chocolates & gifts..." autocomplete="off">
                        <button type="submit" class="oyegifts-search-btn border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-3">
                            <i class="far fa-search text-dark" style="font-size: 1.2rem;"></i>
                        </button>
                        <!-- Clear button inside input -->
                        <button type="button" id="desktop-search-clear" class="btn-close d-none position-absolute end-0 top-50 translate-middle-y me-5" style="font-size: 0.8rem;" aria-label="Clear"></button>
                    </form>
                </div>

                <!-- Header Actions -->
                <div class="oyegifts-header-actions d-flex align-items-center gap-4">
                    <!-- Select City -->
                    <a href="#" class="oyegifts-action-item d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#citySelectorModal">
                        <i class="far fa-map-marker-alt me-2 text-dark font-weight-bold" style="font-size: 1.2rem;"></i>
                        <div class="d-flex flex-column text-start">
                            <span class="oyegifts-action-label text-muted small" style="font-size: 0.75rem;">Deliver to:</span>
                            <span class="oyegifts-action-value fw-bold text-dark" style="font-size: 0.9rem;"><?= session('selected_city_name') ?: 'Select City' ?></span>
                        </div>
                    </a>

                    <!-- Account -->
                    <?php if ($authLib->isLoggedIn()): ?>
                        <a href="<?= base_url('user/dashboard') ?>" class="oyegifts-action-item d-flex align-items-center text-decoration-none text-dark">
                            <i class="far fa-user me-2" style="font-size: 1.2rem;"></i>
                            <span class="oyegifts-action-value fw-bold" style="font-size: 0.9rem;">Hi, <?= esc(explode(' ', $authLib->getUser()['name'])[0]) ?></span>
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="oyegifts-action-item d-flex align-items-center text-decoration-none text-dark">
                            <i class="far fa-user me-2" style="font-size: 1.2rem;"></i>
                            <span class="oyegifts-action-value fw-bold" style="font-size: 0.9rem;">Sign in/ Register</span>
                        </a>
                    <?php endif; ?>

                    <!-- Wishlist -->
                    <a href="<?= base_url('user/wishlist') ?>" class="oyegifts-wishlist-btn text-decoration-none position-relative d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 45px; height: 45px;">
                        <i class="far fa-heart text-dark" style="font-size: 1.2rem;"></i>
                        <?php if ($wishlistCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.7rem;"><?= $wishlistCount ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- Cart -->
                    <a href="<?= base_url('cart') ?>" class="oyegifts-cart-btn-new text-decoration-none position-relative d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="height: 42px;">
                        <i class="fas fa-shopping-cart text-white" style="font-size: 1.1rem;"></i>
                        <span class="text-white fw-bold d-none d-xl-inline" style="font-size: 0.85rem;">Cart</span>
                        <span class="badge bg-white text-coral rounded-pill fw-bold cart-count-badge" style="font-size: 0.75rem; padding: 0.35em 0.6em; color: #e76f51 !important;">
                            <?= $cartCount ?>
                        </span>
                    </a>
                </div>
            </div>
            
            <!-- Desktop Suggestions dropdown container -->
            <div id="search-suggestions-overlay" class="search-suggestions-overlay d-none">
                <div class="container-fluid px-5 py-4 h-100">
                    <div class="row h-100">
                        <!-- Left Side: Related Products Grid (Col-8) -->
                        <div class="col-lg-8 border-end pe-lg-4">
                            <h5 class="fw-bold mb-3 text-dark">Related Products</h5>
                            <div class="row row-cols-2 row-cols-md-4 g-3" id="suggestions-products-grid">
                                <!-- Dynamic Product Cards Go Here -->
                            </div>
                            <div class="mt-4 text-start">
                                <a href="#" id="see-all-results-btn" class="btn btn-dark rounded-pill px-4 py-2 fw-bold text-white text-decoration-none" style="font-size: 0.9rem;">
                                    See All Results For "<span id="see-all-keyword"></span>" &rarr;
                                </a>
                            </div>
                        </div>
                        
                        <!-- Right Side: Suggestions & Collections (Col-4) -->
                        <div class="col-lg-4 ps-lg-4">
                            <!-- Suggestions -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3 text-dark">Suggestions</h5>
                                <ul class="list-unstyled d-flex flex-column gap-2 m-0 p-0" id="suggestions-terms-list">
                                    <!-- Dynamic Suggestions Terms Go Here -->
                                </ul>
                            </div>
                            
                            <!-- Collections -->
                            <div>
                                <h5 class="fw-bold mb-3 text-dark">Collections</h5>
                                <ul class="list-unstyled d-flex flex-column gap-2 m-0 p-0" id="suggestions-collections-list">
                                    <!-- Dynamic Categories Go Here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Header -->
        <div class="oyegifts-header-mobile d-block d-lg-none py-2 px-3 bg-white border-bottom shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <!-- Hamburger menu button -->
                    <button class="navbar-toggler p-0 border-0 bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                        <i class="far fa-bars text-dark" style="font-size: 1.5rem;"></i>
                    </button>
                    <!-- Logo -->
                    <a href="<?= base_url() ?>" class="oyegifts-logo d-flex align-items-center text-decoration-none">
                        <?php if ($logo = get_setting('company_logo')): ?>
                            <img src="<?= base_url($logo) ?>" alt="<?= esc(get_setting('company_name', 'OyeGifts')) ?>" style="max-height: 35px; object-fit: contain;">
                        <?php else: ?>
                            <span class="oyegifts-logo-text" style="font-size: 1.3rem;">
                                <?php 
                                    $cName = get_setting('company_name', 'OyeGifts');
                                    if (stripos($cName, 'gifts') !== false) {
                                        $parts = preg_split('/(?=gifts)/i', $cName, 2);
                                        echo esc($parts[0]) . '<span class="oyegifts-logo-gifts">' . esc($parts[1] ?? '') . '</span>';
                                    } else {
                                        echo esc($cName);
                                    }
                                ?>
                            </span>
                            <span class="oyegifts-logo-heart" style="font-size: 1rem;"><i class="fas fa-heart text-danger"></i></span>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Select City -->
                    <a href="#" class="text-decoration-none text-dark d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#citySelectorModal">
                        <i class="far fa-map-marker-alt" style="font-size: 1.2rem;"></i>
                    </a>
                    <!-- Account -->
                    <?php if ($authLib->isLoggedIn()): ?>
                        <a href="<?= base_url('user/dashboard') ?>" class="text-decoration-none text-dark d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px;">
                            <i class="far fa-user" style="font-size: 1.2rem;"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="text-decoration-none text-dark d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px;">
                            <i class="far fa-user" style="font-size: 1.2rem;"></i>
                        </a>
                    <?php endif; ?>
                    <!-- Wishlist -->
                    <a href="<?= base_url('user/wishlist') ?>" class="mobile-action-btn text-decoration-none position-relative d-flex align-items-center justify-content-center rounded-circle">
                        <i class="far fa-heart" style="font-size: 1.1rem;"></i>
                        <?php if ($wishlistCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25em 0.4em;"><?= $wishlistCount ?></span>
                        <?php endif; ?>
                    </a>
                    <!-- Cart -->
                    <a href="<?= base_url('cart') ?>" class="mobile-action-btn text-decoration-none position-relative d-flex align-items-center justify-content-center rounded-circle">
                        <i class="fas fa-shopping-cart" style="font-size: 1.1rem;"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger animate__animated animate__pulse animate__infinite cart-count-badge" style="font-size: 0.6rem; padding: 0.25em 0.4em; display: <?= $cartCount > 0 ? 'inline-block' : 'none' ?>;"><?= $cartCount ?></span>
                    </a>
                </div>
            </div>

            <!-- Mobile Search Input row -->
            <div class="oyegifts-search-wrapper position-relative mt-2">
                <form action="<?= base_url('search') ?>" method="get" class="m-0">
                    <input type="text" name="q" id="mobile-search-input" class="form-control oyegifts-search-input py-2 px-3 rounded-pill border-0 shadow-sm" placeholder="Search cakes, flowers, chocolates & gifts..." autocomplete="off">
                    <button type="submit" class="oyegifts-search-btn border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y me-3">
                        <i class="far fa-search text-dark" style="font-size: 1.1rem;"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Suggestions Full-Screen overlay -->
        <div id="mobile-suggestions-overlay" class="mobile-suggestions-overlay d-none">
            <div class="p-3">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <span class="fw-bold text-dark fs-5">Search Results</span>
                    <button type="button" id="mobile-suggestions-close" class="btn-close" aria-label="Close"></button>
                </div>
                
                <!-- Collections List -->
                <div class="mb-4">
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.1rem;">Collections</h5>
                    <div class="d-flex flex-column gap-2" id="mobile-collections-list">
                        <!-- Dynamic Mobile Collections -->
                    </div>
                </div>
                
                <!-- Related Products Grid -->
                <div>
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 1.1rem;">Related Products</h5>
                    <div class="row row-cols-2 g-3" id="mobile-products-grid">
                        <!-- Dynamic Mobile Products -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Secondary Navigation Menu Bar -->
        <div class="oyegifts-menu-bar d-none d-lg-block bg-white shadow-sm border-top border-bottom">
            <div class="container-fluid px-4">
                <ul class="nav justify-content-start align-items-center py-2 gap-4">
                    <?php if (!empty($frontendMenu)): ?>
                        <?php foreach ($frontendMenu as $item): 
                            $itemUrl = (strpos($item['url'], 'http') === 0 || strpos($item['url'], '//') === 0) ? $item['url'] : base_url($item['url']);
                            $hasChildren = !empty($item['children']);
                            $isSameDay = (stripos($item['title'], 'same day') !== false);
                        ?>
                            <li class="nav-item dropdown <?= $item['is_mega_menu'] ? 'position-static' : '' ?>">
                                <a class="nav-link dropdown-toggle oyegifts-menu-link text-uppercase fw-bold p-0 text-dark <?= $isSameDay ? 'text-coral d-flex align-items-center' : '' ?>" href="<?= $itemUrl ?>" <?= $hasChildren ? 'data-bs-toggle="dropdown"' : '' ?> style="font-size: 0.82rem; letter-spacing: 0.5px;">
                                    <?php if ($isSameDay): ?>
                                        <i class="fas fa-bolt me-1 text-coral animate__animated animate__flash animate__infinite animate__slower" style="color: #e76f51;"></i>
                                    <?php endif; ?>
                                    <?= esc($item['title']) ?>
                                </a>
                                
                                <?php if ($hasChildren): ?>
                                    <?php if ($item['is_mega_menu']): ?>
                                        <!-- Mega Menu -->
                                        <div class="dropdown-menu mega-dropdown-menu fade-down p-4 rounded-3 border-0 shadow-lg" style="margin-top: 10px; width: 100%; max-width: 1200px; left: 50% !important; transform: translateX(-50%) !important;">
                                            <div class="row w-100 g-4">
                                                <?php foreach ($item['children'] as $col): 
                                                    $colUrl = (strpos($col['url'], 'http') === 0 || strpos($col['url'], '//') === 0) ? $col['url'] : base_url($col['url']);
                                                ?>
                                                    <div class="col-md-3 text-start">
                                                        <h6 class="fw-bold text-coral text-uppercase border-bottom pb-2 mb-3" style="font-size: 0.88rem;">
                                                            <a href="<?= $colUrl ?>" class="text-decoration-none text-coral"><?= esc($col['title']) ?></a>
                                                        </h6>
                                                        <?php if (!empty($col['children'])): ?>
                                                            <ul class="list-unstyled d-flex flex-column gap-2 m-0 p-0">
                                                                <?php foreach ($col['children'] as $link): 
                                                                    $linkUrl = (strpos($link['url'], 'http') === 0 || strpos($link['url'], '//') === 0) ? $link['url'] : base_url($link['url']);
                                                                ?>
                                                                    <li>
                                                                        <a href="<?= $linkUrl ?>" class="text-decoration-none text-muted small-link" style="font-size: 0.85rem; transition: color 0.2s;"><?= esc($link['title']) ?></a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Standard Dropdown -->
                                        <ul class="dropdown-menu fade-down border-0 shadow-lg" style="margin-top: 10px;">
                                            <?php foreach ($item['children'] as $child): 
                                                $childUrl = (strpos($child['url'], 'http') === 0 || strpos($child['url'], '//') === 0) ? $child['url'] : base_url($child['url']);
                                            ?>
                                                <li><a class="dropdown-item fw-bold text-muted text-start" href="<?= $childUrl ?>" style="font-size: 0.85rem;"><?= esc($child['title']) ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Offcanvas Mobile Drawer Menu -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                <a href="<?= base_url() ?>" class="offcanvas-brand text-decoration-none oyegifts-logo d-flex align-items-center" id="offcanvasNavbarLabel">
                    <?php if ($logo = get_setting('company_logo')): ?>
                        <img src="<?= base_url($logo) ?>" alt="<?= esc(get_setting('company_name', 'OyeGifts')) ?>" style="max-height: 40px; object-fit: contain;">
                    <?php else: ?>
                        <span class="oyegifts-logo-text" style="font-size: 1.4rem;">
                            <?php 
                                $cName = get_setting('company_name', 'OyeGifts');
                                if (stripos($cName, 'gifts') !== false) {
                                    $parts = preg_split('/(?=gifts)/i', $cName, 2);
                                    echo esc($parts[0]) . '<span class="oyegifts-logo-gifts">' . esc($parts[1] ?? '') . '</span>';
                                } else {
                                    echo esc($cName);
                                }
                            ?>
                        </span>
                        <span class="oyegifts-logo-heart" style="font-size: 1.1rem;"><i class="fas fa-heart text-danger"></i></span>
                    <?php endif; ?>
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="padding: 20px !important;">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3" style="font-family: 'Outfit', sans-serif;">
                    <?php if (!empty($frontendMenu)): ?>
                        <?php foreach ($frontendMenu as $item): 
                            $cleanTitle = trim(str_replace('?', '', $item['title']));
                            $itemUrl = (strpos($item['url'], 'http') === 0 || strpos($item['url'], '//') === 0) ? $item['url'] : base_url($item['url']);
                            $hasChildren = !empty($item['children']);
                        ?>
                            <?php if ($hasChildren): 
                                $collapseId = 'mobile-menu-collapse-' . $item['id'];
                            ?>
                                <li class="nav-item mb-2 text-start" style="border-bottom: 1px solid #f0f0f0;">
                                    <a class="nav-link fw-bold text-dark text-uppercase py-2 px-0 d-flex justify-content-between align-items-center" href="#" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>" 
                                       style="font-size: 0.9rem; transition: all 0.2s;">
                                        <span><?= esc($cleanTitle) ?></span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
                                    </a>
                                    <div class="collapse ps-3 ms-2 mt-2" id="<?= $collapseId ?>">
                                        <ul class="list-unstyled">
                                            <?php foreach ($item['children'] as $child): 
                                                $cleanChildTitle = trim(str_replace('?', '', $child['title']));
                                                $childUrl = (strpos($child['url'], 'http') === 0 || strpos($child['url'], '//') === 0) ? $child['url'] : base_url($child['url']);
                                            ?>
                                                <li class="mb-3">
                                                    <a class="fw-bold text-decoration-none d-block mb-1 text-coral" href="<?= $childUrl ?>" style="font-size: 0.85rem; color: #e76f51 !important;"><?= esc($cleanChildTitle) ?></a>
                                                    <?php if (!empty($child['children'])): ?>
                                                        <ul class="list-unstyled ps-2 d-flex flex-column gap-2 mt-1" style="border-left: 1px solid #e2e8f0;">
                                                            <?php foreach ($child['children'] as $subchild): 
                                                                $cleanSubTitle = trim(str_replace('?', '', $subchild['title']));
                                                                $subUrl = (strpos($subchild['url'], 'http') === 0 || strpos($subchild['url'], '//') === 0) ? $subchild['url'] : base_url($subchild['url']);
                                                            ?>
                                                                <li>
                                                                    <a href="<?= $subUrl ?>" class="text-decoration-none text-muted d-block ps-2" style="font-size: 0.8rem; transition: color 0.2s;"><?= esc($cleanSubTitle) ?></a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                            <?php else: ?>
                                <li class="nav-item mb-2 text-start" style="border-bottom: 1px solid #f0f0f0;">
                                    <a class="nav-link fw-bold text-dark text-uppercase py-2 px-0 d-block" href="<?= $itemUrl ?>" 
                                       style="font-size: 0.9rem; transition: all 0.2s;">
                                        <?= esc($cleanTitle) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>
    <!-- header area end -->
    <!-- popup search end -->

    <!-- Flash messages wrapper -->
    <div class="container mt-3">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Area -->
    <main class="main">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="footer-area ft-bg">
        <div class="footer-widget">
            <div class="container">
                <div class="row footer-widget-wrapper pt-100 pb-40">
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-widget-box about-us">
                            <a href="<?= base_url() ?>" class="footer-logo text-decoration-none d-flex align-items-center">
                                <?php if ($logo = get_setting('company_logo')): ?>
                                    <img src="<?= base_url($logo) ?>" alt="<?= esc(get_setting('company_name', 'OyeGifts')) ?>" style="max-height: 45px; object-fit: contain;">
                                <?php else: ?>
                                    <span class="oyegifts-logo-text text-white fw-bold" style="font-size: 1.6rem; font-family: 'Outfit', sans-serif;">
                                        <?php 
                                            $cName = get_setting('company_name', 'OyeGifts');
                                            if (stripos($cName, 'gifts') !== false) {
                                                $parts = preg_split('/(?=gifts)/i', $cName, 2);
                                                echo esc($parts[0]) . '<span style="color: #e76f51;">' . esc($parts[1] ?? '') . '</span>';
                                            } else {
                                                echo esc($cName);
                                            }
                                        ?>
                                    </span>
                                    <span class="oyegifts-logo-heart ms-2" style="font-size: 1.1rem;"><i class="fas fa-heart text-danger"></i></span>
                                <?php endif; ?>
                            </a>
                            <p class="mb-3">
                                We offer premium cakes, flowers, and customized gifts for your special moments with instant same-day express delivery.
                            </p>
                            <ul class="footer-contact">
                                <?php if ($phone = get_setting('company_phone')): ?>
                                    <li><a href="tel:<?= esc(preg_replace('/\s+/', '', $phone)) ?>"><i class="far fa-phone"></i><?= esc($phone) ?></a></li>
                                <?php endif; ?>
                                <?php if ($address = get_setting('company_address')): ?>
                                    <li><i class="far fa-map-marker-alt"></i><?= esc($address) ?></li>
                                <?php endif; ?>
                                <?php if ($email = get_setting('company_email')): ?>
                                    <li><a href="mailto:<?= esc($email) ?>"><i class="far fa-envelope"></i><?= esc($email) ?></a></li>
                                <?php endif; ?>
                                <?php if ($hours = get_setting('company_working_hours')): ?>
                                    <li><i class="far fa-clock"></i><?= esc($hours) ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <div class="footer-widget-box list">
                            <h4 class="footer-widget-title">Quick Links</h4>
                            <ul class="footer-list">
                                <li><a href="<?= base_url('about') ?>">About Us</a></li>
                                <li><a href="<?= base_url('contact') ?>">Contact Us</a></li>
                                <li><a href="<?= base_url('faq') ?>">FAQ</a></li>
                                <li><a href="<?= base_url('terms') ?>">Terms of Service</a></li>
                                <li><a href="<?= base_url('privacy') ?>">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="footer-widget-box list">
                            <h4 class="footer-widget-title">Browse Categories</h4>
                            <ul class="footer-list">
                                <?php foreach (array_slice($categories, 0, 5) as $cat): ?>
                                    <li><a href="<?= get_category_url($cat) ?>"><?= esc($cat['name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="footer-widget-box list">
                            <h4 class="footer-widget-title">Production Level Security</h4>
                            <p>All transactions are fully secured. We support cards, net banking, and unified payment interfaces.</p>
                            <div class="footer-payment mt-20">
                                <span>We Accept:</span>
                                <img src="<?= base_url('assets/img/payment/visa.svg') ?>" alt="">
                                <img src="<?= base_url('assets/img/payment/mastercard.svg') ?>" alt="">
                                <img src="<?= base_url('assets/img/payment/paypal.svg') ?>" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="copyright-wrap">
                    <div class="row">
                        <div class="col-12 col-lg-6 align-self-center">
                            <p class="copyright-text">
                                &copy; Copyright <?= date('Y') ?> <a href="<?= base_url() ?>"> <?= esc(get_setting('company_name', 'GiftShop')) ?> </a>. All Rights Reserved. | Developed by <a href="https://codepractice.in/" target="_blank" style="color: #e76f51;">CodePractice Technologies.</a>

                            </p>
                        </div>
                        <div class="col-12 col-lg-6 align-self-center">
                            <div class="footer-social">
                                <span>Follow Us:</span>
                                <?php if ($fb = get_setting('facebook_url')): ?>
                                    <a href="<?= esc($fb) ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <?php endif; ?>
                                <?php if ($tw = get_setting('twitter_url')): ?>
                                    <a href="<?= esc($tw) ?>" target="_blank"><i class="fab fa-x-twitter"></i></a>
                                <?php endif; ?>
                                <?php if ($inst = get_setting('instagram_url')): ?>
                                    <a href="<?= esc($inst) ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                                <?php endif; ?>
                                <?php if ($yt = get_setting('youtube_url')): ?>
                                    <a href="<?= esc($yt) ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                                <?php endif; ?>
                                <?php if ($li = get_setting('linkedin_url')): ?>
                                    <a href="<?= esc($li) ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                                <?php if ($pin = get_setting('pinterest_url')): ?>
                                    <a href="<?= esc($pin) ?>" target="_blank"><i class="fab fa-pinterest"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer area end -->

    <!-- Mobile Sticky Bottom Navigation Tab Bar -->
    <div class="mobile-bottom-nav d-flex d-lg-none">
        <div class="d-flex justify-content-around align-items-center w-100 h-100">
            <a href="<?= base_url() ?>" class="mobile-nav-link <?= url_is('') ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="<?= base_url('shop') ?>" class="mobile-nav-link <?= (url_is('shop*') || url_is('category*')) ? 'active' : '' ?>">
                <i class="fas fa-box-open"></i>
                <span>Products</span>
            </a>
            <a href="<?= base_url('user/orders') ?>" class="mobile-nav-link <?= url_is('user/orders*') ? 'active' : '' ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Your Orders</span>
            </a>
            <a href="<?= base_url('cart') ?>" class="mobile-nav-link <?= url_is('cart*') ? 'active' : '' ?> position-relative">
                <i class="fas fa-shopping-cart"></i>
                <span class="badge bg-danger position-absolute top-0 start-50 translate-middle-y rounded-pill cart-count-badge" style="font-size: 0.65rem; display: <?= $cartCount > 0 ? 'inline-block' : 'none' ?>;"><?= $cartCount ?></span>
                <span>Cart</span>
            </a>
            <a href="<?= base_url('user/dashboard') ?>" class="mobile-nav-link <?= url_is('user/dashboard*') ? 'active' : '' ?>">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        </div>
    </div>

    <!-- scroll-top -->
    <a href="#" id="scroll-top"><i class="far fa-arrow-up-from-arc"></i></a>
    <!-- scroll-top end -->

    <!-- js -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/modernizr.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/imagesloaded.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.magnific-popup.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/isotope.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.appear.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.easing.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/owl.carousel.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/counter-up.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery-ui.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.nice-select.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/countdown.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/wow.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/flex-slider.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>?v=<?= time() ?>"></script>

    <!-- modal quick shop-->
    <div class="modal quickview fade" id="quickview" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="quickviewLabel" aria-hidden="true" style="color: #000000 !important; z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; z-index: 1050; border: none; background: transparent; font-size: 1.5rem;"><i class="far fa-xmark"></i></button>
                <div class="modal-body p-4">
                    <div class="row" id="quickview-modal-loader">
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Loading product details...</p>
                        </div>
                    </div>
                    <div class="row d-none" id="quickview-modal-content">
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 text-center align-self-center">
                            <img id="qv-product-image" src="" alt="#" class="img-fluid rounded shadow-sm" style="max-height: 350px; object-fit: cover; width: 100%;">
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="quickview-content">
                                <h4 class="quickview-title font-weight-bold" id="qv-product-name">Special Gift Box</h4>
                                <div class="quickview-rating mb-2">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <span class="rating-count text-muted"> (5.0 rating)</span>
                                </div>
                                <div class="quickview-price mb-3">
                                    <h5 id="qv-price-display"><del>₹860</del><span>₹740</span></h5>
                                </div>
                                <p id="qv-description" class="text-muted small mb-3">Product description...</p>
                                <ul class="quickview-list list-unstyled mb-3">
                                    <li>SKU Code: <strong id="qv-sku">789FGSA</strong></li>
                                </ul>
                                
                                <form id="qv-cart-form" action="<?= base_url('cart/add') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" id="qv-product-id">
                                    
                                    <div class="mb-3" id="qv-delivery-date-container">
                                        <label class="form-label font-weight-bold small mb-1" id="qv-delivery-date-label">Delivery Date</label>
                                        <div id="qv-delivery-date-field">
                                            <!-- populated dynamically -->
                                        </div>
                                    </div>
                                    
                                    <div class="row align-items-center g-2 mb-3">
                                        <div class="col-4">
                                            <input type="number" name="qty" class="form-control" value="1" min="1" required style="height: 45px;">
                                        </div>
                                        <div class="col-8">
                                            <button type="submit" class="theme-btn w-100" style="height: 45px; line-height: 45px; padding: 0; border: none;">Add to Cart</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // 1. Wishlist toggle
        $(document).on('click', '.wishlist-toggle-btn', function(e) {
            e.preventDefault();
            var btn = $(this);
            var productId = btn.attr('data-product-id');
            var heartIcon = btn.find('i');
            
            $.ajax({
                url: "<?= base_url('user/wishlist/toggle') ?>",
                type: "POST",
                data: { product_id: productId },
                dataType: "json",
                success: function(data) {
                    if (data.success) {
                        if (data.action === "added") {
                            heartIcon.removeClass("far").addClass("fas");
                        } else {
                            heartIcon.removeClass("fas").addClass("far");
                        }
                        // Update counters globally
                        $(".list-link span, .nav-right-link span").each(function() {
                            var el = $(this);
                            if (el.parent().find('.fa-heart').length || el.siblings('.fa-heart').length) {
                                el.text(data.count);
                            }
                        });
                    } else {
                        alert("Please log in to add items to your wishlist.");
                        window.location.href = "<?= base_url('login') ?>";
                    }
                },
                error: function(xhr) {
                    alert("Please log in to manage your wishlist.");
                    window.location.href = "<?= base_url('login') ?>";
                }
            });
        });

        // 2. Quick view fetch and popup
        $(document).on('click', 'a[data-bs-target="#quickview"], .btn-quickview', function(e) {
            e.preventDefault();
            var btn = $(this);
            var productId = btn.attr('data-product-id');
            if (!productId) {
                var form = btn.closest('.product-item').find('form');
                if (form.length) {
                    productId = form.find('input[name="product_id"]').val();
                }
            }
            
            if (!productId) return;
            
            // Show loader, hide content
            $('#quickview-modal-loader').removeClass('d-none');
            $('#quickview-modal-content').addClass('d-none');
            
            // Move modal to body to prevent stacking context or overflow bugs
            $('#quickview').appendTo('body');

            // Show modal if not already opened by data-bs-toggle
            var modalEl = document.getElementById('quickview');
            if (!btn.attr('data-bs-toggle')) {
                var instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                instance.show();
            }
            
            $.ajax({
                url: "<?= base_url('product/quickview') ?>/" + productId,
                type: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.success) {
                        var prod = res.product;
                        $('#qv-product-id').val(prod.id);
                        $('#qv-product-name').text(prod.name);
                        $('#qv-sku').text(prod.sku);
                        var cleanDesc = $('<div>').html(prod.description || '').text();
                        $('#qv-description').text(cleanDesc.substring(0, 180) + '...');
                        
                        var imgPath = prod.image_path ? res.base_url + prod.image_path : res.base_url + 'assets/img/product/default.png';
                        $('#qv-product-image').attr('src', imgPath);
                        
                        var price = parseFloat(prod.price);
                        var offerVal = parseFloat(prod.offer_value || 0);
                        var offerType = prod.offer_type;
                        var finalPrice = price;
                        
                        if (offerVal > 0) {
                            if (offerType === 'percent') {
                                finalPrice = price * (1 - offerVal / 100);
                            } else {
                                finalPrice = price - offerVal;
                            }
                            $('#qv-price-display').html('<del>₹' + price.toFixed(2) + '</del> <span>₹' + finalPrice.toFixed(2) + '</span>');
                        } else {
                            $('#qv-price-display').html('<span>₹' + price.toFixed(2) + '</span>');
                        }
                        
                        var dateField = $('#qv-delivery-date-field');
                        if (prod.delivery_type === 'Express') {
                            $('#qv-delivery-date-label').text('Select Delivery Date');
                            var selectHtml = '<select class="form-select" name="delivery_date" required><option value="">-- Choose Date --</option>';
                            var today = new Date();
                            for (var i = 0; i < 4; i++) {
                                var d = new Date(today);
                                d.setDate(today.getDate() + i);
                                if (d.getDay() === 0) continue;
                                var val = d.toISOString().split('T')[0];
                                var label = d.toLocaleDateString('en-US', { day: 'numeric', month: 'short', weekday: 'short' });
                                selectHtml += '<option value="' + val + '">' + label + '</option>';
                            }
                            selectHtml += '</select><small class="text-danger d-block mt-1">* Order before 6:00 PM IST for same-day delivery.</small>';
                            dateField.html(selectHtml);
                        } else {
                            $('#qv-delivery-date-label').text('Estimated Delivery Date');
                            var dateStr = res.delivery_date;
                            var formattedDate = new Date(dateStr).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric', weekday: 'short' });
                            dateField.html('<div class="alert alert-info py-2 px-3 m-0"><i class="far fa-truck"></i> <strong>' + formattedDate + '</strong></div><input type="hidden" name="delivery_date" value="' + dateStr + '">');
                        }
                        
                        $('#quickview-modal-loader').addClass('d-none');
                        $('#quickview-modal-content').removeClass('d-none');
                    } else {
                        $('#quickview-modal-loader').html('<div class="alert alert-danger m-3">' + res.message + '</div>');
                    }
                },
                error: function() {
                    $('#quickview-modal-loader').html('<div class="alert alert-danger m-3">Error fetching product details.</div>');
                }
            });
        });
    });
    </script>

    <!-- City Selection Modal -->
    <div class="modal fade" id="citySelectorModal" tabindex="-1" aria-labelledby="citySelectorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3 bg-white text-dark">
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="citySelectorModalLabel">
                        <i class="far fa-map-marker-alt text-danger me-2"></i> Select Delivery City
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-4 text-center">To see available cakes, flowers, and same-day delivery combos, please select your delivery city:</p>
                    
                    <!-- Popular Cities Grid -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 text-uppercase small text-muted text-center tracking-wider" style="letter-spacing: 1px;">Popular Cities</h6>
                        <div class="row g-2 justify-content-center">
                            <?php foreach ($popularCities as $pc): ?>
                                <div class="col-4 col-md-2 text-center">
                                    <button class="btn btn-outline-danger w-100 py-3 rounded-3 city-select-item" data-city-id="<?= $pc['id'] ?>">
                                        <i class="far fa-building mb-1 d-block" style="font-size: 1.5rem;"></i>
                                        <span class="small fw-bold d-block text-truncate"><?= esc($pc['name']) ?></span>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- All Cities List (with Filter Input) -->
                    <div>
                        <h6 class="fw-bold mb-2 text-uppercase small text-muted text-center tracking-wider" style="letter-spacing: 1px;">Or Search Other Cities</h6>
                        <div class="mb-3 mx-auto" style="max-width: 400px;">
                            <input type="text" id="all-cities-search" class="form-control" placeholder="Type city name...">
                        </div>
                        <div class="row g-2 border rounded p-3 bg-light overflow-auto mx-1" style="max-height: 180px;" id="all-cities-container">
                            <?php foreach ($allCities as $ac): ?>
                                <div class="col-6 col-md-3 all-city-item-row" data-name="<?= esc(strtolower($ac['name'])) ?>">
                                    <button class="btn btn-light w-100 text-start py-2 px-3 border city-select-item" data-city-id="<?= $ac['id'] ?>">
                                        <i class="far fa-map-marker-alt text-muted me-2"></i> <?= esc($ac['name']) ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // =====================================
        // Live Search Suggestions Script
        // =====================================
        var searchTimer = null;

        function fetchSuggestions(query, isMobile) {
            if (query.length < 2) {
                if (isMobile) {
                    $('#mobile-suggestions-overlay').addClass('d-none');
                } else {
                    $('#search-suggestions-overlay').addClass('d-none');
                    $('.oyegifts-header-top').removeClass('search-active');
                    $('#desktop-search-clear').addClass('d-none');
                }
                return;
            }

            $.ajax({
                url: "<?= base_url('search/suggestions') ?>",
                type: "GET",
                data: { q: query },
                dataType: "json",
                success: function(data) {
                    if (isMobile) {
                        // Populate mobile collections
                        var colHtml = '';
                        if (data.collections && data.collections.length > 0) {
                            data.collections.forEach(function(col) {
                                colHtml += '<a href="' + col.url + '" class="suggestion-item-link py-2 px-3 mb-1"><i class="far fa-tags me-2 text-muted"></i> ' + col.name + '</a>';
                            });
                        } else {
                            colHtml = '<div class="text-muted small p-2">No matching collections</div>';
                        }
                        $('#mobile-collections-list').html(colHtml);

                        // Populate mobile products
                        var prodHtml = '';
                        if (data.products && data.products.length > 0) {
                            data.products.forEach(function(prod) {
                                prodHtml += '<div class="col">' +
                                    '<a href="' + prod.url + '" class="text-decoration-none text-dark">' +
                                        '<div class="suggestions-product-card h-100">' +
                                            '<img src="' + prod.image + '" alt="' + prod.name + '">' +
                                            '<div class="suggestions-product-title">' + prod.name + '</div>' +
                                            '<div class="suggestions-product-price">₹' + prod.price + '</div>' +
                                        '</div>' +
                                    '</a>' +
                                '</div>';
                            });
                        } else {
                            prodHtml = '<div class="col-12 text-muted small p-2">No products found</div>';
                        }
                        $('#mobile-products-grid').html(prodHtml);

                        // Show mobile suggestions overlay and dynamically calculate top offset to avoid covering the search input
                        var announcementHeight = $('.oyegifts-announcement-bar').length ? $('.oyegifts-announcement-bar').outerHeight() : 0;
                        var mobileHeaderHeight = $('.oyegifts-header-mobile').length ? $('.oyegifts-header-mobile').outerHeight() : 0;
                        var totalOffset = announcementHeight + mobileHeaderHeight;
                        $('#mobile-suggestions-overlay').css('top', totalOffset + 'px').removeClass('d-none');
                    } else {
                        // Desktop
                        $('#see-all-keyword').text(query);
                        $('#see-all-results-btn').attr('href', '<?= base_url('search') ?>?q=' + encodeURIComponent(query));

                        // Populate products
                        var prodHtml = '';
                        if (data.products && data.products.length > 0) {
                            data.products.forEach(function(prod) {
                                prodHtml += '<div class="col">' +
                                    '<a href="' + prod.url + '" class="text-decoration-none text-dark">' +
                                        '<div class="suggestions-product-card h-100">' +
                                            '<img src="' + prod.image + '" alt="' + prod.name + '">' +
                                            '<div class="suggestions-product-title">' + prod.name + '</div>' +
                                            '<div class="suggestions-product-price">₹' + prod.price + '</div>' +
                                        '</div>' +
                                    '</a>' +
                                '</div>';
                            });
                        } else {
                            prodHtml = '<div class="col-12 text-muted text-center py-4">No matching products found</div>';
                        }
                        $('#suggestions-products-grid').html(prodHtml);

                        // Populate suggestions
                        var sugHtml = '';
                        if (data.suggestions && data.suggestions.length > 0) {
                            data.suggestions.forEach(function(sug) {
                                sugHtml += '<li><a href="' + sug.url + '" class="suggestion-item-link"><i class="far fa-search me-2 text-muted"></i> ' + sug.text + '</a></li>';
                            });
                        } else {
                            sugHtml = '<li class="text-muted small p-2">No matching suggestions</li>';
                        }
                        $('#suggestions-terms-list').html(sugHtml);

                        // Populate collections
                        var collHtml = '';
                        if (data.collections && data.collections.length > 0) {
                            data.collections.forEach(function(col) {
                                collHtml += '<li><a href="' + col.url + '" class="suggestion-item-link"><i class="far fa-tags me-2 text-muted"></i> ' + col.name + '</a></li>';
                            });
                        } else {
                            collHtml = '<li class="text-muted small p-2">No matching collections</li>';
                        }
                        $('#suggestions-collections-list').html(collHtml);

                        // Show suggestions overlay
                        $('#search-suggestions-overlay').removeClass('d-none');
                        $('.oyegifts-header-top').addClass('search-active');
                        $('#desktop-search-clear').removeClass('d-none');
                    }
                }
            });
        }

        // Desktop Search Inputs Listener
        $('#desktop-search-input').on('input', function() {
            var query = $(this).val().trim();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                fetchSuggestions(query, false);
            }, 250);
        });

        // Mobile Search Inputs Listener
        $('#mobile-search-input').on('input', function() {
            var query = $(this).val().trim();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                fetchSuggestions(query, true);
            }, 250);
        });

        // Clear buttons
        $('#desktop-search-clear').on('click', function() {
            $('#desktop-search-input').val('');
            $('#search-suggestions-overlay').addClass('d-none');
            $('.oyegifts-header-top').removeClass('search-active');
            $(this).addClass('d-none');
        });

        $('#mobile-suggestions-close').on('click', function() {
            $('#mobile-search-input').val('');
            $('#mobile-suggestions-overlay').addClass('d-none');
        });

        // Close desktop overlay when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.oyegifts-search-wrapper').length && !$(e.target).closest('#search-suggestions-overlay').length) {
                $('#search-suggestions-overlay').addClass('d-none');
                $('.oyegifts-header-top').removeClass('search-active');
                $('#desktop-search-clear').addClass('d-none');
            }
        });

        // Helper to check cookies
        function getCookie(name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }

        // 1. Show Modal on Page Load if no City Selected and not dismissed
        <?php if (!session()->has('selected_city_id')): ?>
            if (!getCookie('city_dismissed')) {
                var selectModal = new bootstrap.Modal(document.getElementById('citySelectorModal'));
                selectModal.show();
            }
        <?php endif; ?>

        // 2. Set cookie when modal is closed to make it optional
        $('#citySelectorModal').on('hidden.bs.modal', function () {
            document.cookie = "city_dismissed=1; path=/; max-age=" + (24 * 60 * 60);
        });

        // 3. City Filter Search Box
        $('#all-cities-search').on('input', function() {
            var val = $(this).val().toLowerCase().trim();
            $('.all-city-item-row').each(function() {
                var el = $(this);
                var name = el.attr('data-name');
                if (name.includes(val)) {
                    el.show();
                } else {
                    el.hide();
                }
            });
        });

        // 3. Selection Post Ajax
        $('.city-select-item').on('click', function(e) {
            e.preventDefault();
            var cityId = $(this).attr('data-city-id');
            
            $.ajax({
                url: "<?= base_url('select-city') ?>",
                type: "POST",
                data: {
                    city_id: cityId,
                    <?= csrf_token() ?>: "<?= csrf_hash() ?>"
                },
                dataType: "json",
                success: function(data) {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error);
                    }
                },
                error: function() {
                    alert("Error saving selected city selection. Please try again.");
                }
            });
        });

        // Search Bar Typewriter Placeholder Animation
        (function() {
            var placeholders = [
                "Search cakes for birthday & anniversary...",
                "Search fresh red roses, lilies & orchids...",
                "Search premium chocolates & gift hampers...",
                "Search personalized photo mugs & cushions...",
                "Search corporate gifts & custom hampers...",
                "Search plants, cards & balloons..."
            ];
            
            var currentPhraseIndex = 0;
            var currentCharIndex = 0;
            var isDeleting = false;
            var delay = 100;
            
            var desktopInput = document.getElementById('desktop-search-input');
            var mobileInput = document.getElementById('mobile-search-input');
            
            function typeEffect() {
                var currentPhrase = placeholders[currentPhraseIndex];
                
                if (isDeleting) {
                    currentCharIndex--;
                    delay = 45;
                } else {
                    currentCharIndex++;
                    delay = 90;
                }
                
                var displayedText = currentPhrase.substring(0, currentCharIndex);
                
                if (desktopInput) {
                    desktopInput.setAttribute('placeholder', displayedText);
                }
                if (mobileInput) {
                    mobileInput.setAttribute('placeholder', displayedText);
                }
                
                if (!isDeleting && currentCharIndex === currentPhrase.length) {
                    delay = 2000;
                    isDeleting = true;
                } else if (isDeleting && currentCharIndex === 0) {
                    isDeleting = false;
                    currentPhraseIndex = (currentPhraseIndex + 1) % placeholders.length;
                    delay = 500;
                }
                
                setTimeout(typeEffect, delay);
            }
            
            if (desktopInput || mobileInput) {
                setTimeout(typeEffect, 1000);
            }
        })();

        // AJAX Add to Cart Global Interceptor
        $(document).on('submit', 'form[action*="cart/add"]', function(e) {
            // Skip AJAX for Buy Now direct checkout submissions
            if (e.originalEvent && e.originalEvent.submitter && $(e.originalEvent.submitter).hasClass('buy-now-btn')) {
                return;
            }
            var form = $(this);
            
            // Check if the submission should show an alert (e.g. from homepage lists or list cards)
            // Show alert for listings (home, category, shop, search, etc.)
            var isListing = form.closest('.custom-card-item').length > 0 || 
                            form.find('.card-action-arrow').length > 0 || 
                            form.closest('.product-content').length > 0;
            
            if (isListing) {
                e.preventDefault();
                
                var submitBtn = form.find('button[type="submit"]');
                var originalHtml = submitBtn.html();
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            // Show dynamic alert toast
                            showCartToast('success', response.message);
                            
                            // Update badge counts
                            $('.cart-count-badge').text(response.cart_count).show();
                        } else {
                            showCartToast('danger', response.message || 'Failed to add item to cart.');
                        }
                    },
                    error: function() {
                        submitBtn.prop('disabled', false).html(originalHtml);
                        showCartToast('danger', 'Failed to add product to cart. Please try again.');
                    }
                });
            }
        });

        function showCartToast(type, message) {
            // Ensure toast container exists
            if ($('#oyegifts-toast-container').length === 0) {
                $('body').append('<div id="oyegifts-toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; pointer-events: none;"></div>');
            }
            
            var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            var bg = type === 'success' ? '#2ec4b6' : '#e71d36';
            
            var toastHtml = '<div class="alert alert-dismissible fade show cart-ajax-alert animate__animated animate__fadeInRight" role="alert" ' +
                            'style="min-width: 420px; max-width: 500px; pointer-events: auto; margin-bottom: 0; ' +
                            'background: #ffffff; border-left: 5px solid ' + bg + '; color: #333; ' +
                            'box-shadow: 0 10px 30px rgba(0,0,0,0.12); border-top: none; border-right: none; border-bottom: none; padding: 16px 20px; border-radius: 8px;">' +
                            '<div class="d-flex align-items-center gap-2">' +
                            '<i class="fas ' + icon + '" style="color: ' + bg + '; font-size: 1.25rem;"></i>' +
                            '<div style="font-size: 0.9rem; font-weight: 600; padding-right: 20px;">' + message + '</div>' +
                            '</div>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 1.25rem;"></button>' +
                            '</div>';
            
            var $toast = $(toastHtml);
            $('#oyegifts-toast-container').append($toast);
            
            // Auto dismiss this specific toast after 4 seconds
            setTimeout(function() {
                $toast.removeClass('animate__fadeInRight').addClass('animate__fadeOutRight');
                setTimeout(function() {
                    $toast.remove();
                }, 500);
            }, 4000);
        }

        // Dynamic CSRF Token Synchronizer for AJAX requests
        $(document).ajaxComplete(function(event, xhr, settings) {
            var cookieName = 'csrf_cookie_name';
            var csrfName = 'csrf_test_name';
            var newHash = (function(name) {
                var value = "; " + document.cookie;
                var parts = value.split("; " + name + "=");
                if (parts.length === 2) return parts.pop().split(";").shift();
            })(cookieName);
            
            if (newHash) {
                $('input[name="' + csrfName + '"]').val(newHash);
            }
        });

        // Sticky Header scroll handler
        $(window).on('scroll', function() {
            var header = $('.oyegifts-header');
            var topBar = $('.oyegifts-announcement-bar');
            var threshold = (topBar.outerHeight() || 0) + 10;
            
            if ($(window).scrollTop() > threshold) {
                if (!header.hasClass('sticky')) {
                    header.addClass('sticky');
                    $('body').css('padding-top', header.outerHeight() + 'px');
                }
            } else {
                if (header.hasClass('sticky')) {
                    header.removeClass('sticky');
                    $('body').css('padding-top', '0');
                }
            }
        });
    });
    </script>

    <script src="<?= base_url('assets/js/security.js') ?>"></script>
</body>
</html>
