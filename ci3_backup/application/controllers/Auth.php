<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Order_model');
    }

    /**
     * Display login page and process login.
     */
    public function login() {
        if ($this->auth_lib->is_logged_in()) {
            redirect('');
        }

        if ($this->input->method() === 'post') {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            // Basic validation
            if (empty($email) || empty($password)) {
                $this->session->set_flashdata('error', 'Please fill in all fields.');
                redirect('login');
            }

            // Get user
            $user = $this->User_model->get_by_email($email);

            if ($user && $this->auth_lib->verify_password($password, $user['password'])) {
                if ($this->auth_lib->login($user)) {
                    $this->session->set_flashdata('success', 'Welcome back, ' . $user['name'] . '!');
                    
                    // If admin, redirect to admin dashboard, else to home
                    if ($this->auth_lib->is_admin()) {
                        redirect('admin');
                    }
                    
                    redirect('');
                } else {
                    $this->session->set_flashdata('error', 'Your account is inactive.');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid email or password.');
            }
            redirect('login');
        }

        $data['meta_title'] = 'Login | GiftShop';
        $this->load->view('partials/header', $data);
        $this->load->view('auth/login');
        $this->load->view('partials/footer');
    }

    /**
     * Display register page and process registration.
     */
    public function register() {
        if ($this->auth_lib->is_logged_in()) {
            redirect('');
        }

        if ($this->input->method() === 'post') {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $mobile = $this->input->post('mobile');
            $password = $this->input->post('password');
            $confirm_password = $this->input->post('confirm_password');

            // Validation
            if (empty($name) || empty($email) || empty($mobile) || empty($password)) {
                $this->session->set_flashdata('error', 'All fields are required.');
                redirect('register');
            }

            if ($password !== $confirm_password) {
                $this->session->set_flashdata('error', 'Passwords do not match.');
                redirect('register');
            }

            // Check if email already exists
            if ($this->User_model->get_by_email($email)) {
                $this->session->set_flashdata('error', 'Email is already registered.');
                redirect('register');
            }

            // Insert user
            $user_data = array(
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'password' => $this->auth_lib->hash_password($password),
                'is_active' => 1
            );

            if ($this->User_model->register($user_data)) {
                $this->session->set_flashdata('success', 'Registration successful! Please login.');
                redirect('login');
            } else {
                $this->session->set_flashdata('error', 'Registration failed. Try again.');
                redirect('register');
            }
        }

        $data['meta_title'] = 'Register | GiftShop';
        $this->load->view('partials/header', $data);
        $this->load->view('auth/register');
        $this->load->view('partials/footer');
    }

    /**
     * Log the user out.
     */
    public function logout() {
        $this->auth_lib->logout();
        $this->session->set_flashdata('success', 'Logged out successfully.');
        redirect('login');
    }

    /**
     * Display user profile dashboard.
     */
    public function account() {
        $this->auth_lib->require_login();
        
        $user_id = $this->auth_lib->get_user_id();
        $data['user'] = $this->auth_lib->get_user();
        
        // Load order count
        $orders = $this->Order_model->get_user_orders($user_id);
        $data['order_count'] = count($orders);
        
        // Wishlist items count
        $wishlist = $this->session->userdata('wishlist') ?: array();
        $data['wishlist_count'] = count($wishlist);

        $data['meta_title'] = 'My Account | GiftShop';
        
        $this->load->view('partials/header', $data);
        $this->load->view('auth/account', $data);
        $this->load->view('partials/footer');
    }

    /**
     * Handle password changes from account page.
     */
    public function change_password() {
        $this->auth_lib->require_login();

        if ($this->input->method() === 'post') {
            $user_id = $this->auth_lib->get_user_id();
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password');
            $confirm_password = $this->input->post('confirm_password');

            $user = $this->User_model->get_by_id($user_id);

            if ($this->auth_lib->verify_password($current_password, $user['password'])) {
                if ($new_password === $confirm_password) {
                    $hashed = $this->auth_lib->hash_password($new_password);
                    $this->User_model->update_password($user_id, $hashed);
                    $this->session->set_flashdata('success', 'Password updated successfully!');
                } else {
                    $this->session->set_flashdata('error', 'New passwords do not match.');
                }
            } else {
                $this->session->set_flashdata('error', 'Incorrect current password.');
            }
        }
        redirect('account');
    }
}
