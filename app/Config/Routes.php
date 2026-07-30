<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->set404Override('App\Controllers\Home::notFound');
$routes->get('404', 'Home::notFound');

$routes->get('/', 'Home::index');
$routes->post('select-city', 'Home::selectCity');
$routes->get('search', 'Home::search');
$routes->get('search/suggestions', 'Home::suggestions');
$routes->get('about-us', 'Home::about');
$routes->get('about', 'Home::about');
$routes->get('contact-us', 'Home::contact');
$routes->post('contact-us', 'Home::submitContact');
$routes->get('contact', 'Home::contact');
$routes->post('contact', 'Home::submitContact');
$routes->get('faq', 'Home::faq');
$routes->get('privacy-policy', 'Home::privacy');
$routes->get('privacy', 'Home::privacy');
$routes->get('terms-of-service', 'Home::terms');
$routes->get('terms', 'Home::terms');
$routes->get('cancellation-policy', 'Home::cancellation');
$routes->get('cancellation', 'Home::cancellation');
$routes->get('shipping-policy', 'Home::shipping');
$routes->get('shipping', 'Home::shipping');

$routes->get('shop', 'Home::shop');
$routes->get('category/(:any)', 'Category::index/$1');
$routes->get('product/quickview/(:num)', 'Product::quickview/$1');
$routes->get('product/(:segment)', 'Product::index/$1');

$routes->get('captcha', 'Auth::captcha');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');
$routes->get('forgot-password', 'Auth::forgot_password');
$routes->post('forgot-password', 'Auth::forgot_password');
$routes->get('reset-password/(:segment)', 'Auth::reset_password/$1');
$routes->post('reset-password/(:segment)', 'Auth::reset_password/$1');

$routes->get('cart', 'Cart::index');
$routes->post('cart/add', 'Cart::add');
$routes->post('cart/update', 'Cart::update');
$routes->get('cart/remove/(:num)', 'Cart::remove/$1');
$routes->post('cart/clear', 'Cart::clear');
$routes->post('cart/apply-coupon', 'Cart::apply_coupon');
$routes->get('cart/remove-coupon', 'Cart::remove_coupon');

$routes->get('checkout', 'Checkout::index');
$routes->post('checkout/process', 'Checkout::process');
$routes->get('checkout/complete/(:segment)', 'Checkout::success/$1');
$routes->get('checkout/personalize', 'Checkout::personalize');
$routes->post('checkout/personalize/submit/(:segment)', 'Checkout::personalize_submit/$1');
$routes->get('checkout/personalize/complete', 'Checkout::complete_personalization');

$routes->post('reviews/submit', 'Reviews::submit');

$routes->group('user', function($routes) {
    $routes->get('dashboard', 'User::dashboard');
    $routes->get('orders', 'User::orders');
    $routes->get('orders/(:segment)', 'User::order_detail/$1');
    $routes->get('wishlist', 'User::wishlist');
    $routes->post('wishlist/toggle', 'User::wishlist_toggle');
    $routes->get('settings', 'User::settings');
    $routes->post('settings/update', 'User::settings_update');
    $routes->post('order-item/personalize/(:num)', 'User::personalize_item/$1');
});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('/', 'Dashboard::index');
    
    // Homepage Manager
    $routes->get('homepage', 'Homepage::index');
    $routes->get('homepage/edit/(:num)', 'Homepage::edit/$1');
    $routes->post('homepage/edit/(:num)', 'Homepage::update/$1');
    $routes->get('homepage/toggle/(:num)', 'Homepage::toggle/$1');
    $routes->post('homepage/update-order', 'Homepage::updateOrder');
    
    // Categories CRUD
    $routes->get('categories', 'Categories::index');
    $routes->post('categories/create', 'Categories::create');
    $routes->get('categories/edit/(:num)', 'Categories::edit/$1');
    $routes->post('categories/edit/(:num)', 'Categories::edit/$1');
    $routes->get('categories/toggle/(:num)', 'Categories::toggle/$1');
    $routes->get('categories/delete/(:num)', 'Categories::delete/$1');
    $routes->post('categories/bulk-delete', 'Categories::bulkDelete');
    $routes->get('categories/check-slug', 'Categories::checkSlug');
    
    // Cities CRUD
    $routes->get('cities', 'Cities::index');
    $routes->post('cities/create', 'Cities::create');
    $routes->get('cities/edit/(:num)', 'Cities::edit/$1');
    $routes->post('cities/edit/(:num)', 'Cities::edit/$1');
    $routes->get('cities/toggle/(:num)', 'Cities::toggle/$1');
    $routes->get('cities/delete/(:num)', 'Cities::delete/$1');
    $routes->get('cities/check-slug', 'Cities::checkSlug');
    
    // Colors CRUD
    $routes->get('colors', 'Colors::index');
    $routes->post('colors/create', 'Colors::create');
    $routes->get('colors/edit/(:num)', 'Colors::edit/$1');
    $routes->post('colors/edit/(:num)', 'Colors::edit/$1');
    $routes->get('colors/toggle/(:num)', 'Colors::toggle/$1');
    $routes->get('colors/delete/(:num)', 'Colors::delete/$1');
    
    // FAQs CRUD
    $routes->get('faqs', 'Faqs::index');
    $routes->post('faqs/store', 'Faqs::store');
    $routes->get('faqs/edit_partial/(:num)', 'Faqs::edit_partial/$1');
    $routes->post('faqs/update/(:num)', 'Faqs::update/$1');
    $routes->get('faqs/toggle/(:num)', 'Faqs::toggle/$1');
    $routes->get('faqs/delete/(:num)', 'Faqs::delete/$1');
    
    // Enquiries Management
    $routes->get('enquiries', 'Enquiries::index');
    $routes->get('enquiries/view_partial/(:num)', 'Enquiries::view_partial/$1');
    $routes->post('enquiries/update_status/(:num)', 'Enquiries::update_status/$1');
    $routes->get('enquiries/delete/(:num)', 'Enquiries::delete/$1');
    
    // Products CRUD
    $routes->get('products', 'Products::index');
    $routes->get('products/create', 'Products::create');
    $routes->post('products/create', 'Products::create');
    $routes->get('products/edit/(:num)', 'Products::edit/$1');
    $routes->post('products/edit/(:num)', 'Products::edit/$1');
    $routes->get('products/toggle/(:num)', 'Products::toggle/$1');
    $routes->get('products/delete/(:num)', 'Products::delete/$1');
    $routes->get('products/delete-image/(:num)', 'Products::delete_image/$1');
    $routes->post('products/bulk-delete', 'Products::bulkDelete');
    $routes->get('products/check-slug', 'Products::checkSlug');
    
    // Offers CRUD
    $routes->get('offers', 'Offers::index');
    $routes->post('offers/create', 'Offers::create');
    $routes->get('offers/toggle/(:num)', 'Offers::toggle/$1');
    $routes->get('offers/delete/(:num)', 'Offers::delete/$1');
    
    // Menus CRUD
    $routes->get('menus', 'Menus::index');
    $routes->post('menus/create', 'Menus::create');
    $routes->get('menus/delete/(:num)', 'Menus::delete/$1');
    $routes->get('menus/activate/(:num)', 'Menus::activate/$1');
    $routes->post('menus/update-structure', 'Menus::updateStructure');
    
    // Orders Processing
    $routes->get('orders', 'Orders::index');
    $routes->get('orders/view/(:num)', 'Orders::view/$1');
    $routes->post('orders/update-status', 'Orders::update_status');
    $routes->post('orders/update-tracking', 'Orders::update_tracking');

    
    // Roles & Permissions CRUD
    $routes->get('roles', 'Roles::index');
    $routes->get('roles/create', 'Roles::create');
    $routes->post('roles/create', 'Roles::create');
    $routes->get('roles/edit/(:num)', 'Roles::edit/$1');
    $routes->post('roles/edit/(:num)', 'Roles::edit/$1');
    $routes->get('roles/delete/(:num)', 'Roles::delete/$1');

    // Employees CRUD
    $routes->get('employees', 'Employees::index');
    $routes->get('employees/create', 'Employees::create');
    $routes->post('employees/create', 'Employees::create');
    $routes->get('employees/edit/(:num)', 'Employees::edit/$1');
    $routes->post('employees/edit/(:num)', 'Employees::edit/$1');
    $routes->get('employees/toggle/(:num)', 'Employees::toggle/$1');
    $routes->get('employees/delete/(:num)', 'Employees::delete/$1');

    // Activity Logs Viewer
    $routes->get('activities', 'Activities::index');

    // SEO Pages Configuration
    $routes->get('seo-pages', 'SeoPages::index');
    $routes->get('seo-pages/edit/(:num)', 'SeoPages::edit/$1');
    $routes->post('seo-pages/edit/(:num)', 'SeoPages::edit/$1');
    
    // Users CRUD
    $routes->get('users', 'Users::index');
    $routes->get('users/toggle/(:num)', 'Users::toggle/$1');
    $routes->get('users/delete/(:num)', 'Users::delete/$1');

    // Settings
    $routes->get('settings', 'Settings::index');
    $routes->post('settings', 'Settings::update');

    // Coupons CRUD
    $routes->get('coupons', 'Coupons::index');
    $routes->post('coupons/create', 'Coupons::create');
    $routes->get('coupons/toggle/(:num)', 'Coupons::toggle/$1');
    $routes->get('coupons/delete/(:num)', 'Coupons::delete/$1');

    // Reviews Moderation & Custom Entries
    $routes->get('reviews', 'Reviews::index');
    $routes->get('reviews/search-products', 'Reviews::search_products');
    $routes->post('reviews/create', 'Reviews::create');
    $routes->get('reviews/approve/(:num)', 'Reviews::approve/$1');
    $routes->get('reviews/disapprove/(:num)', 'Reviews::disapprove/$1');
    $routes->get('reviews/delete/(:num)', 'Reviews::delete/$1');
});

// Fallback Category catch-all route (supports up to 3 levels: parent/child/grandchild)
$routes->get('(:any)', 'Category::index/$1');

