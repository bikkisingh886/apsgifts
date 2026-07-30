<?php $authLib = new \App\Libraries\AuthLib(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> - <?= esc(get_setting('company_name', 'GiftShop')) ?></title>

    
    <!-- favicon -->
    <?php if ($fav = get_setting('company_favicon')): ?>
        <link rel="icon" type="image/x-icon" href="<?= base_url(esc($fav)) ?>">
        <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(esc($fav)) ?>">
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/logo/favicon.png') ?>">
        <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('assets/img/logo/favicon.png') ?>">
    <?php endif; ?>
    
    <!-- bootstrap and icons -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/all-fontawesome.min.css') ?>">
    <!-- Google Font Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Select2 CSS Dependency -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Premium light blueprint theme styles -->
    <style>
        /* CKEditor Custom Enhancements */
        .cke_chrome {
            border-radius: 8px !important;
            border: 1px solid var(--border-color, #eaedf1) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
            overflow: hidden !important;
        }
        .cke_top {
            background: #f8f9fa !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 8px !important;
        }
        .cke_source {
            font-family: 'Courier New', Consolas, Monaco, monospace !important;
            font-size: 13px !important;
            background-color: #1e1e1e !important;
            color: #4ec9b0 !important;
            padding: 12px !important;
        }
        .cke_button__source_label {
            display: inline-block !important;
            font-weight: 700 !important;
            color: #0d6efd !important;
        }
        .cke_notification, .cke_notification_warning {
            display: none !important;
        }
        /* Select2 Theme Custom Styling overrides */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            border: 1px solid var(--border-color, #eaedf1) !important;
            border-radius: 8px !important;
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
            background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-dark, #2d3748) !important;
            font-size: 0.9rem !important;
            padding-left: 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-dropdown {
            border: 1px solid var(--border-color, #eaedf1) !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            z-index: 9999 !important; /* Ensure it floats over Bootstrap modals and components */
            background-color: #ffffff !important;
        }
        .select2-search__field {
            border: 1px solid var(--border-color, #eaedf1) !important;
            border-radius: 6px !important;
            outline: none !important;
            color: #333 !important;
            background-color: #fff !important;
        }
        .select2-results__option {
            padding: 8px 12px !important;
            font-size: 0.9rem !important;
            color: #333 !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-coral, #e76f51) !important;
            color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #718096 !important;
        }

        :root {
            --bg-light: #f8f9fc;
            --bg-sidebar: #ffffff;
            --border-color: #eaedf1;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --primary-coral: #e76f51;
            --primary-hover: #d65a3e;
            --transition: all 0.25s ease;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Outfit', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Text color overrides for card custom text-white bug */
        .card-custom h4.text-white,
        .card-custom td strong.text-white,
        .card-custom td span.text-white,
        .card-custom td.text-white,
        .card-custom .text-white,
        .card-custom strong.text-white,
        .card-custom span.text-white,
        .card-custom h5.text-white,
        .card-custom h5.text-cyan {
            color: var(--text-dark) !important;
        }
        /* Ensure status/danger/success badges inside cards remain white */
        .card-custom .badge.text-white,
        .card-custom .badge-status.text-white,
        .card-custom .bg-success.text-white,
        .card-custom .bg-danger.text-white,
        .card-custom .bg-primary.text-white {
            color: #ffffff !important;
        }

        /* Top Header Navigation Bar */
        .top-navbar {
            height: 60px;
            background-color: #1a1a1a;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1010;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .top-navbar-brand {
            display: flex;
            align-items: center;
            color: #ffffff;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }

        .top-navbar-brand i {
            color: var(--primary-coral);
            margin-right: 8px;
            font-size: 1.35rem;
        }

        .top-navbar-user {
            color: #cbd5e0;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 240px;
            background-color: var(--bg-sidebar);
            height: calc(100vh - 60px);
            position: fixed;
            left: 0;
            top: 60px;
            z-index: 1000;
            border-right: 1px solid var(--border-color);
            transition: var(--transition);
            padding-top: 15px;
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--primary-coral);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 2px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
            border-left: 4px solid transparent;
        }

        .sidebar-menu li a i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .sidebar-menu li a:hover {
            color: var(--primary-coral);
            background-color: #f7fafc;
        }

        .sidebar-menu li.active a {
            color: var(--primary-coral);
            background-color: #fdf2f0;
            border-left-color: var(--primary-coral);
        }

        .sidebar-menu li.active a i {
            color: var(--primary-coral);
        }

        /* Submenu sidebar styling */
        .sidebar-menu .collapse li a {
            padding: 8px 12px 8px 40px !important;
            font-size: 0.88rem !important;
            font-weight: 500;
            border-left: none !important;
            background-color: transparent !important;
        }
        .sidebar-menu .collapse li.active a {
            color: var(--primary-coral) !important;
            font-weight: 600;
        }
        .sidebar-menu li a.dropdown-toggle::after {
            display: inline-block;
            margin-left: auto;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
            transition: transform 0.2s ease;
        }
        .sidebar-menu li a.dropdown-toggle:not(.collapsed)::after {
            transform: rotate(180deg);
        }

        /* Content Wrapper Styling */
        .content-wrapper {
            margin-left: 240px;
            margin-top: 60px;
            min-height: calc(100vh - 60px);
            padding: 30px;
            transition: var(--transition);
        }

        /* Card custom panels */
        .card-custom {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            margin-bottom: 30px;
            transition: var(--transition);
        }

        /* Stat widget elements matching blueprint */
        .stat-card-gray {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }
        
        .stat-card-green {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }
        
        .stat-card-orange {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }
        
        .stat-card-blue {
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-val-gray {
            font-size: 2.25rem;
            font-weight: 800;
            color: #4a5568;
        }

        .stat-val-green {
            font-size: 2.25rem;
            font-weight: 800;
            color: #2e7d32;
        }

        .stat-val-orange {
            font-size: 2.25rem;
            font-weight: 800;
            color: #e65100;
        }

        .stat-val-blue {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1565c0;
        }

        /* Form elements styling */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            background-color: #ffffff;
            border: 1px solid #ced4da;
            color: var(--text-dark);
            border-radius: 8px;
            padding: 10px 14px;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-coral);
            box-shadow: 0 0 0 3px rgba(231, 111, 81, 0.15);
            outline: none;
        }

        /* Buttons styling */
        .btn-cyan {
            background-color: var(--primary-coral);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            transition: var(--transition);
            box-shadow: 0 2px 6px rgba(231, 111, 81, 0.2);
        }

        .btn-cyan:hover {
            background-color: var(--primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(231, 111, 81, 0.35);
        }

        .btn-outline-cyan {
            background-color: transparent;
            color: var(--primary-coral);
            border: 1px solid var(--primary-coral);
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-outline-cyan:hover {
            background-color: var(--primary-coral);
            color: #ffffff;
        }

        /* Tables styling matching blueprint */
        .table-custom {
            color: var(--text-dark);
            background-color: transparent;
            margin: 0;
        }

        .table-custom th {
            color: var(--text-dark);
            background-color: #f8f9fc;
            border-bottom: 2px solid var(--border-color);
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
        }

        .table-custom td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .table-custom tbody tr:hover {
            background-color: #f7fafc;
        }

        .badge-status {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .text-cyan {
            color: var(--primary-coral) !important;
        }

        /* Responsive Mobile Layout adjustments */
        #sidebarToggle {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #ffffff;
            font-size: 1.25rem;
            display: none;
        }
        
        #sidebarToggle:focus {
            outline: none;
            box-shadow: none;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 60px;
            left: 0;
            width: 100%;
            height: calc(100vh - 60px);
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }

        @media (max-width: 991.98px) {
            #sidebarToggle {
                display: block;
            }
            .sidebar {
                left: -240px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }
            .content-wrapper {
                margin-left: 0 !important;
                padding: 20px 15px;
            }
            body.show-sidebar {
                overflow: hidden;
            }
            body.show-sidebar .sidebar {
                left: 0;
            }
            body.show-sidebar .sidebar-overlay {
                display: block;
            }
        }
    </style>
    <!-- load jquery in head to support child view inline scripts -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <!-- DataTables CSS & Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
</head>
<body>

    <!-- Header Navigation -->
    <div class="top-navbar">
        <div class="d-flex align-items-center">
            <button id="sidebarToggle" class="btn text-white me-2">
                <i class="fas fa-bars"></i>
            </button>
            <a href="<?= base_url('admin/dashboard') ?>" class="top-navbar-brand" style="display: flex; align-items: center; gap: 8px;">
                <?php 
                $logo = get_setting('company_logo');
                if (!empty($logo)):
                ?>
                    <img src="<?= base_url($logo) ?>" alt="<?= esc(get_setting('company_name', 'GiftShop')) ?>" style="max-height: 35px; object-fit: contain; filter: brightness(0) invert(1);">
                <?php else: ?>
                    <i class="fas fa-gift" style="color: var(--primary-coral);"></i>
                <?php endif; ?>
                <span class="brand-text"><?= esc(get_setting('company_name', 'GiftShop')) ?></span>
            </a>

        </div>
        <div class="top-navbar-user">
            <i class="far fa-user-circle me-1"></i> <?= esc(session()->get('user_email')) ?>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <div class="sidebar">
        <ul class="sidebar-menu">
            <li class="<?= (url_is('admin/dashboard') || url_is('admin')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/dashboard') ?>"><i class="far fa-chart-line"></i> Dashboard</a>
            </li>
            <?php if ($authLib->hasPermission('products', 'view')): ?>
            <li class="<?= (url_is('admin/products*') || url_is('admin/colors*')) ? 'active' : '' ?>">
                <a href="#productsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?= (url_is('admin/products*') || url_is('admin/colors*')) ? 'true' : 'false' ?>" aria-controls="productsSubmenu" class="dropdown-toggle <?= (url_is('admin/products*') || url_is('admin/colors*')) ? '' : 'collapsed' ?>">
                    <i class="far fa-box-open"></i> Products
                </a>
                <ul class="collapse list-unstyled <?= (url_is('admin/products*') || url_is('admin/colors*')) ? 'show' : '' ?>" id="productsSubmenu">
                    <li class="<?= (url_is('admin/products*') && !url_is('admin/products/create')) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/products') ?>"><i class="far fa-list-ol"></i> Products List</a>
                    </li>
                    <?php if ($authLib->hasPermission('colors', 'view')): ?>
                    <li class="<?= (url_is('admin/colors*')) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/colors') ?>"><i class="far fa-palette"></i> Product Colors</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('categories', 'view')): ?>
            <li class="<?= (url_is('admin/categories*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/categories') ?>"><i class="far fa-folder"></i> Categories</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('menus', 'view')): ?>
            <li class="<?= (url_is('admin/menus*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/menus') ?>"><i class="far fa-bars"></i> Menu Manager</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('homepage', 'view')): ?>
            <li class="<?= (url_is('admin/homepage*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/homepage') ?>"><i class="far fa-home"></i> Homepage Manager</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('cities', 'view')): ?>
            <li class="<?= (url_is('admin/cities*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/cities') ?>"><i class="far fa-map-pin"></i> Delivery Cities</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('orders', 'view')): ?>
            <li class="<?= (url_is('admin/orders*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/orders') ?>"><i class="far fa-shopping-bag"></i> Orders</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('users', 'view')): ?>
            <li class="<?= (url_is('admin/users*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/users') ?>"><i class="far fa-user"></i> Users</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('offers', 'view')): ?>
            <li class="<?= (url_is('admin/offers*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/offers') ?>"><i class="far fa-percentage"></i> Offers</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('coupons', 'view')): ?>
            <li class="<?= (url_is('admin/coupons*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/coupons') ?>"><i class="far fa-tags"></i> Coupon Codes</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('reviews', 'view')): ?>
            <li class="<?= (url_is('admin/reviews*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/reviews') ?>"><i class="far fa-comments"></i> Product Reviews</a>
            </li>
            <?php endif; ?>
            <li class="<?= (url_is('admin/faqs*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/faqs') ?>"><i class="far fa-question-circle"></i> FAQ Manager</a>
            </li>
            <li class="<?= (url_is('admin/enquiries*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/enquiries') ?>" class="d-flex justify-content-between align-items-center">
                    <span><i class="far fa-envelope-open-text me-2"></i> Enquiries</span>
                    <?php
                        $enqModel = new \App\Models\EnquiryModel();
                        $unreadEnq = $enqModel->where('status', 'unread')->countAllResults();
                        if ($unreadEnq > 0):
                    ?>
                        <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.75rem;"><?= $unreadEnq ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if ($authLib->hasPermission('seo_pages', 'view')): ?>
            <li class="<?= (url_is('admin/seo-pages*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/seo-pages') ?>"><i class="far fa-search"></i> SEO Pages</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('employees', 'view')): ?>
            <li class="<?= (url_is('admin/employees*') || url_is('admin/roles*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/employees') ?>"><i class="far fa-users-cog"></i> Employees</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('activities', 'view')): ?>
            <li class="<?= (url_is('admin/activities*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/activities') ?>"><i class="far fa-history"></i> Activity Logs</a>
            </li>
            <?php endif; ?>
            <?php if ($authLib->hasPermission('settings', 'view')): ?>
            <li class="<?= (url_is('admin/settings*')) ? 'active' : '' ?>">
                <a href="<?= base_url('admin/settings') ?>"><i class="far fa-cog"></i> Settings</a>
            </li>
            <?php endif; ?>
            <li class="mt-4">
                <a href="<?= base_url() ?>" target="_blank"><i class="far fa-external-link"></i> Front Website</a>
            </li>
            <li>
                <a href="<?= base_url('logout') ?>"><i class="far fa-sign-out"></i> Logout</a>
            </li>
        </ul>
    </div>
    
    <!-- Sidebar mobile overlay -->
    <div class="sidebar-overlay"></div>

    <!-- Main Content Wrapper -->
    <div class="content-wrapper">
        <!-- Flash messages replaced by SweetAlert2 -->

        <!-- Render View Section -->
        <?= $this->renderSection('admin_content') ?>

        <!-- Admin footer -->
        <footer class="admin-footer mt-5 pt-3 border-top text-center text-muted" style="font-size: 0.85rem; border-color: rgba(0,0,0,0.05) !important;">
            <p class="mb-0">
                &copy; <?= date('Y') ?> <strong><?= esc(get_setting('company_name', 'GiftShop')) ?></strong>. All Rights Reserved. | Developed by <a href="https://codepractice.in/" target="_blank" style="color: #e76f51;">CodePractice Technologies</a>.
            </p>
        </footer>
    </div>


    <!-- Generic Edit Modal (No tabindex="-1" so CKEditor dialog inputs receive focus naturally) -->
    <div class="modal fade text-dark" id="editModal" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="editModalLabel" style="color:#ffffff !important;">Edit Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="editModalBody">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Loading details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- javascript -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <!-- CKEditor 4 Full Build CDN (native Source HTML code editor + full rich editing) -->
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal && bootstrap.Modal.Default) {
            bootstrap.Modal.Default.focus = false;
        }
        if (typeof CKEDITOR !== 'undefined') {
            CKEDITOR.config.versionCheck = false;
        }

        // Helper function to initialize CKEditor with full features and native Source code button
        function initAppCKEditor(target) {
            var el = (typeof target === 'string') ? document.querySelector(target) : target;
            if (!el) return Promise.resolve(null);
            
            var id = el.id;
            if (!id) {
                id = 'editor_' + Math.random().toString(36).substring(2, 9);
                el.id = id;
            }

            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.replace) {
                CKEDITOR.config.versionCheck = false;
                if (CKEDITOR.instances[id]) {
                    try { CKEDITOR.instances[id].destroy(true); } catch(e){}
                }

                var instance = CKEDITOR.replace(id, {
                    height: 320,
                    versionCheck: false,
                    allowedContent: true, // Preserve all HTML tags, styles, and attributes
                    extraAllowedContent: '*(*);*{*}',
                    toolbar: [
                        { name: 'document', items: [ 'Source', '-', 'Maximize', 'ShowBlocks', 'Preview', 'Print' ] },
                        { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                        { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
                        '/',
                        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                        { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'Iframe' ] },
                        '/',
                        { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                        { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
                    ]
                });

                return Promise.resolve(instance);
            }
            return Promise.reject('CKEDITOR script not loaded');
        }

        // Global form submit hook to sync all CKEditor instances with underlying textareas
        document.addEventListener('submit', function(e) {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances) {
                for (var key in CKEDITOR.instances) {
                    if (CKEDITOR.instances.hasOwnProperty(key)) {
                        CKEDITOR.instances[key].updateElement();
                    }
                }
            }
        }, true);
    </script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables JS & Extensions -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
    // Suppress DataTables warning alerts globally
    if (typeof $.fn !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
        $.fn.dataTable.ext.errMode = 'none';
    }
    $(document).on('error.dt', function(e, settings, techNote, message) {
        console.warn('DataTables warning: ', message);
        return true; // Prevents the alert window
    });

    $(document).ready(function() {
        window.popupEditor = null;

        // Intercept Edit button clicks
        $(document).on('click', '.btn-edit-popup', function(e) {
            e.preventDefault();
            var editUrl = $(this).attr('href');
            
            // Clear modal body and show loading spinner
            $('#editModalBody').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading form...</p></div>');
            var editModalEl = document.getElementById('editModal');
            if (editModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(editModalEl, { focus: false }).show();
            } else {
                $('#editModal').modal({ focus: false });
            }
            window.popupEditor = null;
            
            // Load the form via AJAX
            $.get(editUrl, function(html) {
                $('#editModalBody').html(html);
                
                // Re-initialize CKEditor on description, short description, and summary fields if loaded in modal
                var modalEditors = document.querySelectorAll('#editModalBody #description-editor, #editModalBody #short-description-editor, #editModalBody #summary-editor, #editModalBody textarea.rich-editor');
                modalEditors.forEach(function(el) {
                    initAppCKEditor(el);
                });
            }).fail(function() {
                $('#editModalBody').html('<div class="alert alert-danger">Failed to load the form. Please try again.</div>');
            });
        });

        // Intercept Modal form submit
        $(document).on('submit', '#editModalBody form', function(e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = form.attr('action');
            
            // Synchronize all CKEditor instances with their textareas before AJAX send
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances) {
                for (var key in CKEDITOR.instances) {
                    if (CKEDITOR.instances.hasOwnProperty(key)) {
                        CKEDITOR.instances[key].updateElement();
                    }
                }
            }
            
            // Handle form data including uploads
            var formData = new FormData(this);
            
            // Show loading state in the button
            var submitBtn = form.find('button[type="submit"]');
            var originalBtnHtml = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
            
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Parse response if it comes as string
                    var data = response;
                    if (typeof response === 'string') {
                        try { data = JSON.parse(response); } catch(e) {}
                    }

                    if (data && data.success === false) {
                        submitBtn.html(originalBtnHtml).prop('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.error || data.message || 'An error occurred while saving.',
                            confirmButtonColor: '#e76f51'
                        });
                        return;
                    }

                    $('#editModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Successfully!',
                        text: 'The record has been updated.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        // Reload wrapper
                        if ($('#main-content-table-wrapper').length) {
                            $('#main-content-table-wrapper').load(location.href + ' #main-content-table-wrapper > *', function() {
                                convertNativeConfirms(); // Re-apply to reloaded items
                            });
                        } else {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    submitBtn.html(originalBtnHtml).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An error occurred while saving. Please check fields and try again.'
                    });
                }
            });
        });

        // 3. Convert native browser confirm prompts to SweetAlert2
        window.convertNativeConfirms = function() {
            $('a[onclick*="confirm"], button[onclick*="confirm"]').each(function() {
                var $el = $(this);
                var onclickAttr = $el.attr('onclick');
                if (onclickAttr && !$el.data('swal-bound')) {
                    var match = onclickAttr.match(/confirm\(['"](.+?)['"]\)/);
                    var message = match ? match[1] : "Are you sure you want to proceed?";
                    
                    // Remove native onclick to prevent standard popup
                    $el.removeAttr('onclick');
                    $el.data('swal-bound', true);
                    
                    $el.on('click', function(e) {
                        e.preventDefault();
                        var href = $el.attr('href');
                        
                        Swal.fire({
                            title: 'Are you sure?',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e76f51',
                            cancelButtonColor: '#a0aec0',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (href && href !== '#' && href !== 'javascript:void(0);') {
                                    window.location.href = href;
                                } else {
                                    // Submit closest form if it's a form action button
                                    $el.closest('form').submit();
                                }
                            }
                        });
                    });
                }
            });
        };

        // Initialize conversions
        convertNativeConfirms();

        // Convert inside Modal when loaded
        $('#editModal').on('shown.bs.modal', function() {
            convertNativeConfirms();
        });

        // Convert on AJAX complete
        $(document).ajaxComplete(function() {
            convertNativeConfirms();
        });

        // Show Flash Messages via SweetAlert2
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: <?= json_encode(session()->getFlashdata('success')) ?>,
                timer: 3000,
                showConfirmButton: false
            });
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: <?= json_encode(session()->getFlashdata('error')) ?>
            });
        <?php endif; ?>

        // Sidebar responsive toggle
        $('#sidebarToggle').on('click', function(e) {
            e.stopPropagation();
            $('body').toggleClass('show-sidebar');
        });

        // Close sidebar when clicking overlay
        $('.sidebar-overlay').on('click', function() {
            $('body').removeClass('show-sidebar');
        });

        // Close sidebar when clicking menu links on mobile
        $('.sidebar-menu li a').on('click', function() {
            if ($(window).width() < 992) {
                $('body').removeClass('show-sidebar');
            }
        });
    });
    </script>

    <!-- Select2 JS Dependency -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>
</html>
