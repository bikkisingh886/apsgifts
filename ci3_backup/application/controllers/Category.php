<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
        $this->load->model('Product_model');
    }

    /**
     * Category Listing Page.
     *
     * @param string $slug URL slug of the category
     */
    public function listing($slug = NULL) {
        if (empty($slug)) {
            redirect('');
        }

        // Get category detail
        $category = $this->Category_model->get_by_slug($slug);
        if (!$category || !$category['is_active']) {
            show_404();
        }

        // Get products in this category
        $products = $this->Product_model->get_by_category_slug($slug);

        $data['category'] = $category;
        $data['products'] = $products;
        
        // Pass SEO variables
        $data['meta_title'] = $category['meta_title'] ?: $category['name'] . ' - GiftShop';
        $data['meta_desc'] = $category['meta_desc'] ?: $category['summary'];

        $this->load->view('partials/header', $data);
        $this->load->view('category/listing', $data);
        $this->load->view('partials/footer');
    }
}
