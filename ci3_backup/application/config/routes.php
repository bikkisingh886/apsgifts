<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';

// Auth routes
$route['login'] = 'auth/login';
$route['register'] = 'auth/register';
$route['logout'] = 'auth/logout';
$route['account'] = 'auth/account';
$route['account/change-password'] = 'auth/change_password';

// Cart & Order routes
$route['cart'] = 'cart/index';
$route['cart/add'] = 'cart/add';
$route['cart/update'] = 'cart/update';
$route['cart/remove/(:any)'] = 'cart/remove/$1';
$route['cart/clear'] = 'cart/clear';

$route['checkout'] = 'order/checkout';
$route['order/place'] = 'order/place';
$route['order/confirmation/(:any)'] = 'order/confirmation/$1';
$route['orders'] = 'order/my_orders';
$route['orders/view/(:any)'] = 'order/view/$1';
$route['wishlist'] = 'home/wishlist';
$route['wishlist/toggle'] = 'home/wishlist_toggle';
$route['search'] = 'home/search';

// Admin Dashboard
$route['admin'] = 'admin/Admin_dashboard/index';
$route['admin/dashboard'] = 'admin/Admin_dashboard/index';

// Admin Category CRUD
$route['admin/categories'] = 'admin/Admin_categories/index';
$route['admin/categories/add'] = 'admin/Admin_categories/add';
$route['admin/categories/save'] = 'admin/Admin_categories/save';
$route['admin/categories/edit/(:num)'] = 'admin/Admin_categories/edit/$1';
$route['admin/categories/delete/(:num)'] = 'admin/Admin_categories/delete/$1';

// Admin Product CRUD
$route['admin/products'] = 'admin/Admin_products/index';
$route['admin/products/add'] = 'admin/Admin_products/add';
$route['admin/products/save'] = 'admin/Admin_products/save';
$route['admin/products/edit/(:num)'] = 'admin/Admin_products/edit/$1';
$route['admin/products/delete/(:num)'] = 'admin/Admin_products/delete/$1';

// Admin Offers CRUD
$route['admin/offers'] = 'admin/Admin_offers/index';
$route['admin/offers/add'] = 'admin/Admin_offers/add';
$route['admin/offers/save'] = 'admin/Admin_offers/save';
$route['admin/offers/edit/(:num)'] = 'admin/Admin_offers/edit/$1';
$route['admin/offers/delete/(:num)'] = 'admin/Admin_offers/delete/$1';

// Admin Orders CRUD
$route['admin/orders'] = 'admin/Admin_orders/index';
$route['admin/orders/view/(:any)'] = 'admin/Admin_orders/view/$1';
$route['admin/orders/update-status'] = 'admin/Admin_orders/update_status';
$route['admin/orders/add-tracking'] = 'admin/Admin_orders/add_tracking';

// Admin Users
$route['admin/users'] = 'admin/Admin_users/index';

// Wildcard dynamic routes (must be at the bottom)
$route['category/(:any)'] = 'category/listing/$1';
$route['(:any)'] = 'product/detail/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
