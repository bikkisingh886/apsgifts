<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
        $this->load->model('Product_model');
    }

    /**
     * Homepage. Loads categories and trending products.
     */
    public function index() {
        // Fetch active categories with product count
        $data['categories'] = $this->Category_model->get_with_product_counts(TRUE);
        
        // Fetch trending products (limit 8)
        $data['trending_products'] = $this->Product_model->get_trending(8);
        
        $data['meta_title'] = 'GiftShop - Send Cakes, Flowers & Gifts Online';
        $data['meta_desc'] = 'Complete blueprint gift e-commerce shop with express delivery and courier options.';

        $this->load->view('partials/header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('partials/footer');
    }

    /**
     * Search results page.
     */
    public function search() {
        $keyword = $this->input->get('q', TRUE);
        
        if (empty($keyword)) {
            redirect('');
        }

        $data['keyword'] = $keyword;
        $data['products'] = $this->Product_model->search_products($keyword);
        $data['meta_title'] = 'Search results for "' . htmlspecialchars($keyword) . '" | GiftShop';

        $this->load->view('partials/header', $data);
        $this->load->view('home/search', $data);
        $this->load->view('partials/footer');
    }

    /**
     * Wishlist list view page.
     */
    public function wishlist() {
        $wishlist_ids = $this->session->userdata('wishlist') ?: array();
        
        $data['products'] = array();
        if (!empty($wishlist_ids)) {
            foreach ($wishlist_ids as $pid) {
                $product = $this->Product_model->get_by_id($pid);
                if ($product) {
                    // Check if it has a discount/offer
                    $full_product = $this->Product_model->get_by_slug($product['slug']);
                    $data['products'][] = $full_product;
                }
            }
        }
        
        $data['meta_title'] = 'My Wishlist | GiftShop';

        $this->load->view('partials/header', $data);
        $this->load->view('home/wishlist', $data);
        $this->load->view('partials/footer');
    }

    /**
     * Toggle a product ID in the session-based wishlist.
     * Accessible via AJAX POST.
     */
    public function wishlist_toggle() {
        if ($this->input->method() !== 'post') {
            show_error('Method not allowed', 405);
        }

        $product_id = (int)$this->input->post('product_id');
        if (!$product_id) {
            echo json_encode(array('success' => FALSE, 'message' => 'Invalid product ID'));
            return;
        }

        $wishlist = $this->session->userdata('wishlist') ?: array();

        if (in_array($product_id, $wishlist)) {
            // Remove from wishlist
            $wishlist = array_values(array_diff($wishlist, array($product_id)));
            $action = 'removed';
        } else {
            // Add to wishlist
            $wishlist[] = $product_id;
            $action = 'added';
        }

        $this->session->set_userdata('wishlist', $wishlist);

        echo json_encode(array(
            'success' => TRUE,
            'action' => $action,
            'count' => count($wishlist),
            'message' => 'Product ' . $action . ' successfully.'
        ));
    }
}
