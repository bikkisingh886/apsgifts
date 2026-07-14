<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all categories.
     *
     * @return array
     */
    public function get_all() {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('categories');
        return $query->result_array();
    }

    /**
     * Get active categories.
     *
     * @return array
     */
    public function get_active() {
        $query = $this->db->get_where('categories', array('is_active' => 1));
        return $query->result_array();
    }

    /**
     * Get categories with active product counts.
     * Useful for Category Listings and admin tables.
     *
     * @param bool $active_only
     * @return array
     */
    public function get_with_product_counts($active_only = FALSE) {
        $this->db->select('c.*, COUNT(pc.product_id) as product_count');
        $this->db->from('categories c');
        $this->db->join('product_categories pc', 'pc.category_id = c.id', 'left');
        $this->db->join('products p', 'p.id = pc.product_id AND p.is_active = 1', 'left');
        
        if ($active_only) {
            $this->db->where('c.is_active', 1);
        }
        
        $this->db->group_by('c.id');
        $this->db->order_by('c.name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get category by slug.
     *
     * @param string $slug
     * @return array|null
     */
    public function get_by_slug($slug) {
        $query = $this->db->get_where('categories', array('slug' => $slug));
        return $query->row_array();
    }

    /**
     * Get category by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function get_by_id($id) {
        $query = $this->db->get_where('categories', array('id' => $id));
        return $query->row_array();
    }

    /**
     * Add category.
     *
     * @param array $data
     * @return int|bool
     */
    public function insert($data) {
        if ($this->db->insert('categories', $data)) {
            return $this->db->insert_id();
        }
        return FALSE;
    }

    /**
     * Update category.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('categories', $data);
    }

    /**
     * Delete category.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('categories');
    }
}
