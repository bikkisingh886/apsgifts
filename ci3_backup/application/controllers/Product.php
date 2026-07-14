<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
    }

    /**
     * Product Detail Page.
     *
     * @param string $slug URL slug of the product
     */
    public function detail($slug = NULL) {
        if (empty($slug)) {
            redirect('');
        }

        // Fetch product details
        $product = $this->Product_model->get_by_slug($slug);
        if (!$product || !$product['is_active']) {
            show_404();
        }

        $data['product'] = $product;

        // Delivery type logic (as defined on page 9 of the PDF)
        if ($product['delivery_type'] === 'Express') {
            // Load available express delivery dates (Today, Tomorrow, and next 5 working days, excluding Sundays)
            $data['delivery_dates'] = get_express_dates();
        } else {
            // Calculate Courier ETA (Order date + 7 working days, excluding Sundays)
            $data['courier_eta'] = calculate_courier_eta();
        }

        // Pass SEO variables
        $data['meta_title'] = $product['meta_title'] ?: $product['name'] . ' - GiftShop';
        $data['meta_desc'] = $product['meta_desc'] ?: substr(strip_tags($product['description']), 0, 160);
        
        // Pass first product image for Open Graph meta
        if (!empty($product['images'])) {
            $data['product_img'] = $product['images'][0]['image_path'];
        }

        $this->load->view('partials/header', $data);
        $this->load->view('product/detail', $data);
        $this->load->view('partials/footer');
    }
}
