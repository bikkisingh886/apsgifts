<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_orders extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth_lib->require_admin();
        $this->load->model('Order_model');
    }

    /**
     * List all orders.
     */
    public function index() {
        $data['orders'] = $this->Order_model->get_all_orders();
        
        $data['subview'] = 'admin/orders/list';
        $data['meta_title'] = 'Order Management | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * View order details and update status/tracking.
     *
     * @param int $id Order ID
     */
    public function view($id = NULL) {
        if ($id === NULL) {
            redirect('admin/orders');
        }

        $order = $this->Order_model->get_by_id($id);
        if (!$order) {
            $this->session->set_flashdata('error', 'Order not found.');
            redirect('admin/orders');
        }

        $data['order'] = $order;
        $data['address'] = json_decode($order['address_json'], TRUE);
        
        $data['subview'] = 'admin/orders/detail';
        $data['meta_title'] = 'Order Detail - #' . $order['order_number'] . ' | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Update order status.
     */
    public function update_status() {
        if ($this->input->method() !== 'post') {
            redirect('admin/orders');
        }

        $order_id = $this->input->post('order_id');
        $status = $this->input->post('status');

        if ($order_id && $status) {
            $this->Order_model->update_status($order_id, $status);
            $this->session->set_flashdata('success', 'Order status updated to ' . $status . '.');
            redirect('admin/orders/view/' . $order_id);
        }

        $this->session->set_flashdata('error', 'Failed to update order status.');
        redirect('admin/orders');
    }

    /**
     * Save/Update Tracking details.
     */
    public function add_tracking() {
        if ($this->input->method() !== 'post') {
            redirect('admin/orders');
        }

        $order_id = $this->input->post('order_id');
        $tracking_url = $this->input->post('tracking_url');
        $tracking_code = $this->input->post('tracking_code');

        if (empty($tracking_code)) {
            $this->session->set_flashdata('error', 'Tracking / AWB Number is required.');
            redirect('admin/orders/view/' . $order_id);
        }

        if ($order_id) {
            $this->Order_model->add_tracking($order_id, $tracking_url, $tracking_code);
            $this->session->set_flashdata('success', 'Tracking details saved and order status updated to Shipped.');
            redirect('admin/orders/view/' . $order_id);
        }

        $this->session->set_flashdata('error', 'Failed to save tracking details.');
        redirect('admin/orders');
    }
}
