<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth_lib->require_login(); // Force login for all order activities
        $this->load->model('Order_model');
    }

    /**
     * Checkout form and order placement.
     */
    public function checkout() {
        $cart = $this->session->userdata('cart') ?: array();
        if (empty($cart)) {
            $this->session->set_flashdata('error', 'Your cart is empty. Add products to place an order.');
            redirect('cart');
        }

        $subtotal = 0.00;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        // Apply 10% discount if subtotal exceeds 999
        $discount = $subtotal > 999 ? round($subtotal * 0.10) : 0.00;
        $total = $subtotal - $discount;

        if ($this->input->method() === 'post') {
            $name = $this->input->post('name');
            $mobile = $this->input->post('mobile');
            $address = $this->input->post('address');
            $city = $this->input->post('city');
            $pin = $this->input->post('pin');

            if (empty($name) || empty($mobile) || empty($address) || empty($city) || empty($pin)) {
                $this->session->set_flashdata('error', 'Please fill in all address fields.');
                redirect('checkout');
            }

            // Standardize delivery date to be the earliest date chosen, or default
            $earliest_date = NULL;
            foreach ($cart as $item) {
                if (isset($item['delivery_date'])) {
                    if ($earliest_date === NULL || $item['delivery_date'] < $earliest_date) {
                        $earliest_date = $item['delivery_date'];
                    }
                }
            }

            // Create Address JSON
            $address_data = array(
                'name' => $name,
                'mobile' => $mobile,
                'address' => $address,
                'city' => $city,
                'pin' => $pin
            );

            $order_data = array(
                'user_id' => $this->auth_lib->get_user_id(),
                'status' => 'Processing',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'delivery_date' => $earliest_date,
                'address_json' => json_encode($address_data)
            );

            // Insert into DB
            $order_number = $this->Order_model->place_order($order_data, $cart);

            if ($order_number) {
                // Clear cart session
                $this->session->unset_userdata('cart');
                
                // Set flash message and redirect to confirmation
                $this->session->set_flashdata('success', 'Order placed successfully!');
                redirect('order/confirmation/' . $order_number);
            } else {
                $this->session->set_flashdata('error', 'Order placement failed. Please try again.');
                redirect('checkout');
            }
        }

        $data['cart'] = $cart;
        $data['subtotal'] = $subtotal;
        $data['discount'] = $discount;
        $data['total'] = $total;
        
        // Populate default user address details
        $user_session = $this->auth_lib->get_user();
        $data['user_name'] = $user_session['name'];
        $data['user_mobile'] = $user_session['mobile'];
        
        $data['meta_title'] = 'Checkout | GiftShop';

        $this->load->view('partials/header', $data);
        $this->load->view('order/checkout', $data);
        $this->load->view('partials/footer');
    }

    /**
     * Display order confirmation/success screen.
     */
    public function confirmation($order_number = NULL) {
        if ($order_number === NULL) {
            redirect('');
        }

        $order = $this->Order_model->get_by_number($order_number);
        if (!$order || $order['user_id'] != $this->auth_lib->get_user_id()) {
            show_404();
        }

        $data['order'] = $order;
        $data['address'] = json_decode($order['address_json'], TRUE);
        $data['meta_title'] = 'Order Confirmed - #' . $order_number . ' | GiftShop';

        $this->load->view('partials/header', $data);
        $this->load->view('order/confirmation', $data);
        $this->load->view('partials/footer');
    }

    /**
     * Display order history list for customer.
     */
    public function my_orders() {
        $user_id = $this->auth_lib->get_user_id();
        $data['orders'] = $this->Order_model->get_user_orders($user_id);
        $data['meta_title'] = 'My Orders | GiftShop';

        $this->load->view('partials/header', $data);
        $this->load->view('order/my_orders', $data);
        $this->load->view('partials/footer');
    }

    /**
     * View single order details.
     */
    public function view($order_number = NULL) {
        if ($order_number === NULL) {
            redirect('orders');
        }

        $order = $this->Order_model->get_by_number($order_number);
        if (!$order || $order['user_id'] != $this->auth_lib->get_user_id()) {
            show_404();
        }

        $data['order'] = $order;
        $data['address'] = json_decode($order['address_json'], TRUE);
        $data['meta_title'] = 'Order Detail - #' . $order_number . ' | GiftShop';

        $this->load->view('partials/header', $data);
        $this->load->view('order/confirmation', $data); // Uses same layout structure as confirmation view
        $this->load->view('partials/footer');
    }
}
