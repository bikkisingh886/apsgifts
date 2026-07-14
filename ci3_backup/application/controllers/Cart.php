<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
    }

    /**
     * View shopping cart.
     */
    public function index() {
        $cart = $this->session->userdata('cart') ?: array();
        
        $subtotal = 0.00;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        // Apply 10% discount if subtotal exceeds ₹999
        $discount = 0.00;
        if ($subtotal > 999) {
            $discount = round($subtotal * 0.10); // Round off discount as shown in PDF (130 for 1298 subtotal)
        }

        $total = $subtotal - $discount;

        $data['cart'] = $cart;
        $data['subtotal'] = $subtotal;
        $data['discount'] = $discount;
        $data['total'] = $total;
        
        $data['meta_title'] = 'My Cart | GiftShop';

        $this->load->view('partials/header', $data);
        $this->load->view('cart/index', $data);
        $this->load->view('partials/footer');
    }

    /**
     * Add product to cart.
     */
    public function add() {
        if ($this->input->method() !== 'post') {
            redirect('cart');
        }

        // Check if adding all wishlist items
        if ($this->input->post('add_all_wishlist')) {
            $wishlist = $this->session->userdata('wishlist') ?: array();
            if (!empty($wishlist)) {
                $cart = $this->session->userdata('cart') ?: array();
                
                foreach ($wishlist as $pid) {
                    $product = $this->Product_model->get_by_id($pid);
                    if ($product && $product['is_active']) {
                        // Calculate price with active offer
                        $full_product = $this->Product_model->get_by_slug($product['slug']);
                        $price = (float)$full_product['price'];
                        if ($full_product['offer_value'] > 0) {
                            $price = $full_product['offer_type'] === 'percent' 
                                ? $price * (1 - $full_product['offer_value']/100) 
                                : $price - $full_product['offer_value'];
                        }

                        // Determine default delivery date
                        $delivery_date = date('Y-m-d');
                        if ($product['delivery_type'] === 'Courier') {
                            $delivery_date = date('Y-m-d', strtotime('+7 weekdays'));
                        } else {
                            $express_dates = get_express_dates();
                            $delivery_date = !empty($express_dates) ? $express_dates[0]['value'] : date('Y-m-d');
                        }

                        // Add to cart array
                        $cart[$product['id']] = array(
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'sku' => $product['sku'],
                            'price' => $price,
                            'qty' => 1,
                            'image' => $product['image_path'],
                            'delivery_type' => $product['delivery_type'],
                            'delivery_date' => $delivery_date
                        );
                    }
                }
                
                $this->session->set_userdata('cart', $cart);
                $this->session->unset_userdata('wishlist'); // Clear wishlist after moving
                $this->session->set_flashdata('success', 'Moved all wishlist items to cart!');
            }
            redirect('cart');
        }

        $product_id = (int)$this->input->post('product_id');
        $qty = (int)$this->input->post('qty');
        $delivery_date = $this->input->post('delivery_date');

        if (!$product_id || $qty < 1) {
            $this->session->set_flashdata('error', 'Invalid product addition details.');
            redirect('');
        }

        $product = $this->Product_model->get_by_id($product_id);
        if (!$product || !$product['is_active']) {
            $this->session->set_flashdata('error', 'Product not available.');
            redirect('');
        }

        // Fetch full product to check active offers
        $full_product = $this->Product_model->get_by_slug($product['slug']);
        $price = (float)$full_product['price'];
        if ($full_product['offer_value'] > 0) {
            $price = $full_product['offer_type'] === 'percent' 
                ? $price * (1 - $full_product['offer_value']/100) 
                : $price - $full_product['offer_value'];
        }

        // Standardize delivery date
        if (empty($delivery_date)) {
            if ($product['delivery_type'] === 'Courier') {
                $delivery_date = date('Y-m-d', strtotime('+7 weekdays'));
            } else {
                $express_dates = get_express_dates();
                $delivery_date = !empty($express_dates) ? $express_dates[0]['value'] : date('Y-m-d');
            }
        }

        $cart = $this->session->userdata('cart') ?: array();

        // If product already in cart, update quantity
        if (isset($cart[$product_id])) {
            $cart[$product_id]['qty'] += $qty;
            $cart[$product_id]['delivery_date'] = $delivery_date; // Update to chosen date
        } else {
            $cart[$product_id] = array(
                'id' => $product_id,
                'name' => $product['name'],
                'sku' => $product['sku'],
                'price' => $price,
                'qty' => $qty,
                'image' => $product['image_path'],
                'delivery_type' => $product['delivery_type'],
                'delivery_date' => $delivery_date
            );
        }

        $this->session->set_userdata('cart', $cart);
        $this->session->set_flashdata('success', 'Product added to cart.');
        redirect('cart');
    }

    /**
     * Update cart quantities.
     */
    public function update() {
        if ($this->input->method() !== 'post') {
            redirect('cart');
        }

        $product_id = (int)$this->input->post('product_id');
        $qty = (int)$this->input->post('qty');

        $cart = $this->session->userdata('cart') ?: array();

        if (isset($cart[$product_id]) && $qty > 0) {
            $cart[$product_id]['qty'] = $qty;
            $this->session->set_userdata('cart', $cart);
            
            // Calculate new summary for AJAX responses
            $subtotal = 0.00;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }
            $discount = $subtotal > 999 ? round($subtotal * 0.10) : 0.00;
            $total = $subtotal - $discount;

            echo json_encode(array(
                'success' => TRUE,
                'subtotal' => number_format($subtotal, 2),
                'discount' => number_format($discount, 2),
                'total' => number_format($total, 2),
                'item_total' => number_format($cart[$product_id]['price'] * $qty, 2)
            ));
            return;
        }

        echo json_encode(array('success' => FALSE, 'message' => 'Failed to update quantity.'));
    }

    /**
     * Remove item from cart.
     */
    public function remove($product_id = NULL) {
        if ($product_id === NULL) {
            redirect('cart');
        }

        $cart = $this->session->userdata('cart') ?: array();

        if (isset($cart[$product_id])) {
            unset($cart[$product_id]);
            $this->session->set_userdata('cart', $cart);
            $this->session->set_flashdata('success', 'Item removed from cart.');
        }

        redirect('cart');
    }

    /**
     * Clear all cart items.
     */
    public function clear() {
        $this->session->unset_userdata('cart');
        $this->session->set_flashdata('success', 'Cart cleared.');
        redirect('cart');
    }
}
