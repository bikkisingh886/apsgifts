<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get user by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function get_by_id($id) {
        $query = $this->db->get_where('users', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Get user by Email.
     *
     * @param string $email
     * @return array|null
     */
    public function get_by_email($email) {
        $query = $this->db->get_where('users', array('email' => $email));
        return $query->row_array();
    }

    /**
     * Insert a new user (Register).
     *
     * @param array $data
     * @return int|bool
     */
    public function register($data) {
        if ($this->db->insert('users', $data)) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    /**
     * Update user password.
     *
     * @param int $id
     * @param string $hashed_password
     * @return bool
     */
    public function update_password($id, $hashed_password) {
        $this->db->where('id', $id);
        return $this->db->update('users', array('password' => $hashed_password));
    }

    /**
     * Get all users (for Admin panel).
     *
     * @return array
     */
    public function get_all_users() {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('users');
        return $query->result_array();
    }
}
