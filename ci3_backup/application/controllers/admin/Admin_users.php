<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth_lib->require_admin();
        $this->load->model('User_model');
    }

    /**
     * List all users.
     */
    public function index() {
        $data['users'] = $this->User_model->get_all_users();
        
        $data['subview'] = 'admin/users/list';
        $data['meta_title'] = 'User Directory | GiftShop';

        $this->load->view('admin/layout', $data);
    }
}
