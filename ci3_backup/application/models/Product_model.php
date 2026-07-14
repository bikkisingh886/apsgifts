<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get all products for Admin listing (including categories and primary image).
     *
     * @return array
     */
    public function get_all() {
        $this->db->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path');
        $this->db->from('products p');
        $this->db->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left');
        $this->db->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left');
        $this->db->order_by('p.id', 'DESC');
        
        $query = $this->db->get();
        $products = $query->result_array();

        // Attach category names to each product
        foreach ($products as &$product) {
            $product['categories'] = $this->get_product_categories($product['id']);
        }

        return $products;
    }

    /**
     * Get active trending products for Homepage.
     *
     * @param int $limit
     * @return array
     */
    public function get_trending($limit = 8) {
        $this->db->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path');
        $this->db->from('products p');
        $this->db->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left');
        $this->db->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left');
        $this->db->where('p.is_active', 1);
        $this->db->limit($limit);
        $this->db->order_by('p.id', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get active products by Category Slug.
     *
     * @param string $category_slug
     * @return array
     */
    public function get_by_category_slug($category_slug) {
        $this->db->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path');
        $this->db->from('products p');
        $this->db->join('product_categories pc', 'pc.product_id = p.id');
        $this->db->join('categories c', 'c.id = pc.category_id');
        $this->db->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left');
        $this->db->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left');
        $this->db->where('c.slug', $category_slug);
        $this->db->where('p.is_active', 1);
        $this->db->order_by('p.id', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get single product details by Slug.
     *
     * @param string $slug
     * @return array|null
     */
    public function get_by_slug($slug) {
        $this->db->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, o.applies_to as offer_applies_to');
        $this->db->from('products p');
        $this->db->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left');
        $this->db->where('p.slug', $slug);
        $this->db->where('p.is_active', 1);
        
        $query = $this->db->get();
        $product = $query->row_array();

        if ($product) {
            $product['images'] = $this->get_product_images($product['id']);
            $product['categories'] = $this->get_product_categories($product['id']);
        }

        return $product;
    }

    /**
     * Get product by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function get_by_id($id) {
        $this->db->select('p.*, pi.image_path');
        $this->db->from('products p');
        $this->db->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left');
        $this->db->where('p.id', $id);
        
        $query = $this->db->get();
        $product = $query->row_array();

        if ($product) {
            $product['images'] = $this->get_product_images($id);
            
            // Get category IDs
            $this->db->select('category_id');
            $cat_query = $this->db->get_where('product_categories', array('product_id' => $id));
            $product['category_ids'] = array_column($cat_query->result_array(), 'category_id');
        }

        return $product;
    }

    /**
     * Search products by keyword in name or description.
     *
     * @param string $keyword
     * @return array
     */
    public function search_products($keyword) {
        $this->db->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path');
        $this->db->from('products p');
        $this->db->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left');
        $this->db->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left');
        $this->db->where('p.is_active', 1);
        $this->db->group_start();
        $this->db->like('p.name', $keyword);
        $this->db->or_like('p.description', $keyword);
        $this->db->group_end();
        $this->db->order_by('p.id', 'DESC');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get categories associated with a product.
     *
     * @param int $product_id
     * @return array
     */
    public function get_product_categories($product_id) {
        $this->db->select('c.*');
        $this->db->from('categories c');
        $this->db->join('product_categories pc', 'pc.category_id = c.id');
        $this->db->where('pc.product_id', $product_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get image gallery for a product.
     *
     * @param int $product_id
     * @return array
     */
    public function get_product_images($product_id) {
        $this->db->order_by('sort_order', 'ASC');
        $query = $this->db->get_where('product_images', array('product_id' => $product_id));
        return $query->result_array();
    }

    /**
     * Save product (Admin).
     *
     * @param array $product_data
     * @param array $category_ids
     * @param array $uploaded_images (paths)
     * @return int|bool
     */
    public function insert_product($product_data, $category_ids, $uploaded_images = array()) {
        $this->db->trans_start();

        // 1. Insert product
        $this->db->insert('products', $product_data);
        $product_id = $this->db->insert_id();

        // 2. Insert category links
        if (!empty($category_ids)) {
            foreach ($category_ids as $cat_id) {
                $this->db->insert('product_categories', array(
                    'product_id' => $product_id,
                    'category_id' => $cat_id
                ));
            }
        }

        // 3. Insert product images
        if (!empty($uploaded_images)) {
            $is_first = TRUE;
            foreach ($uploaded_images as $index => $path) {
                $this->db->insert('product_images', array(
                    'product_id' => $product_id,
                    'image_path' => $path,
                    'is_primary' => $is_first ? 1 : 0,
                    'sort_order' => $index
                ));
                $is_first = FALSE;
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status() ? $product_id : FALSE;
    }

    /**
     * Update product (Admin).
     *
     * @param int $id
     * @param array $product_data
     * @param array $category_ids
     * @param array $new_images (paths)
     * @param array $existing_to_delete (ids)
     * @return bool
     */
    public function update_product($id, $product_data, $category_ids, $new_images = array(), $existing_to_delete = array()) {
        $this->db->trans_start();

        // 1. Update product base data
        $this->db->where('id', $id);
        $this->db->update('products', $product_data);

        // 2. Refresh categories
        $this->db->delete('product_categories', array('product_id' => $id));
        if (!empty($category_ids)) {
            foreach ($category_ids as $cat_id) {
                $this->db->insert('product_categories', array(
                    'product_id' => $id,
                    'category_id' => $cat_id
                ));
            }
        }

        // 3. Delete requested images
        if (!empty($existing_to_delete)) {
            $this->db->where_in('id', $existing_to_delete);
            $this->db->delete('product_images');
        }

        // 4. Add new images
        if (!empty($new_images)) {
            // Check if there is already a primary image
            $query = $this->db->get_where('product_images', array('product_id' => $id, 'is_primary' => 1));
            $has_primary = ($query->num_rows() > 0);
            
            $is_first = !$has_primary;
            foreach ($new_images as $path) {
                $this->db->insert('product_images', array(
                    'product_id' => $id,
                    'image_path' => $path,
                    'is_primary' => $is_first ? 1 : 0,
                    'sort_order' => 10
                ));
                $is_first = FALSE;
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Delete product.
     *
     * @param int $id
     * @return bool
     */
    public function delete_product($id) {
        $this->db->where('id', $id);
        return $this->db->delete('products');
    }
}
