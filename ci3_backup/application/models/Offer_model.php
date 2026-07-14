<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offer_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all offers.
     *
     * @return array
     */
    public function get_all() {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('offers');
        return $query->result_array();
    }

    /**
     * Get active offers.
     *
     * @return array
     */
    public function get_active() {
        $query = $this->db->get_where('offers', array('is_active' => 1));
        return $query->result_array();
    }

    /**
     * Get offer by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function get_by_id($id) {
        $query = $this->db->get_where('offers', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Add new offer.
     *
     * @param array $data
     * @return int|bool
     */
    public function insert($data) {
        if ($this->db->insert('offers', $data)) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    /**
     * Update offer.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('offers', $data);
    }

    /**
     * Delete offer.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('offers');
    }
}
