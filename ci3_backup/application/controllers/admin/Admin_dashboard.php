<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth_lib->require_admin(); // Secure this controller for admins only
        $this->load->model('Order_model');
    }

    /**
     * Dashboard Overview.
     */
    public function index() {
        // Fetch dashboard statistics
        $data['stats'] = $this->Order_model->get_dashboard_stats();
        
        // Fetch recent orders (limit to last 10)
        $all_orders = $this->Order_model->get_all_orders();
        $data['recent_orders'] = array_slice($all_orders, 0, 10);
        
        $data['subview'] = 'admin/dashboard';
        $data['meta_title'] = 'Admin Dashboard | GiftShop';

        $this->load->view('admin/layout', $data);
    }
}
