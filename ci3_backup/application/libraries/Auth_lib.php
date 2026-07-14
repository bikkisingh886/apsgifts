<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_lib {

    protected $CI;

    public function __construct() {
        // Get CodeIgniter instance
        $this->CI =& get_instance();
    }

    /**
     * Hash password using bcrypt.
     *
     * @param string $password
     * @return string
     */
    public function hash_password($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify password against hash.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify_password($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Log in a user.
     *
     * @param array $user
     * @return bool
     */
    public function login($user) {
        if (empty($user) || !$user['is_active']) {
            return FALSE;
        }

        $session_data = array(
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_mobile' => $user['mobile'],
            'logged_in' => TRUE,
            'is_admin' => ($user['email'] === 'admin@giftshop.in')
        );

        $this->CI->session->set_userdata($session_data);
        return TRUE;
    }

    /**
     * Check if any user is logged in.
     *
     * @return bool
     */
    public function is_logged_in() {
        return ($this->CI->session->userdata('logged_in') === TRUE);
    }

    /**
     * Check if the logged-in user is an admin.
     *
     * @return bool
     */
    public function is_admin() {
        return ($this->CI->session->userdata('logged_in') === TRUE && $this->CI->session->userdata('is_admin') === TRUE);
    }

    /**
     * Require user login. Redirect to login page if not authenticated.
     */
    public function require_login() {
        if (!$this->is_logged_in()) {
            $this->CI->session->set_flashdata('error', 'Login is required before placing an order.');
            redirect('login');
        }
    }

    /**
     * Require admin login. Redirect to login page if not admin.
     */
    public function require_admin() {
        if (!$this->is_admin()) {
            $this->CI->session->set_flashdata('error', 'Access denied. Admin privileges required.');
            redirect('login');
        }
    }

    /**
     * Get current user ID.
     *
     * @return int|null
     */
    public function get_user_id() {
        return $this->CI->session->userdata('user_id');
    }

    /**
     * Get current user session data.
     *
     * @return array
     */
    public function get_user() {
        return array(
            'id' => $this->CI->session->userdata('user_id'),
            'name' => $this->CI->session->userdata('user_name'),
            'email' => $this->CI->session->userdata('user_email'),
            'mobile' => $this->CI->session->userdata('user_mobile')
        );
    }

    /**
     * Log out the user.
     */
    public function logout() {
        $array_items = array('user_id', 'user_name', 'user_email', 'user_mobile', 'logged_in', 'is_admin');
        $this->CI->session->unset_userdata($array_items);
        $this->CI->session->sess_destroy();
    }
}
