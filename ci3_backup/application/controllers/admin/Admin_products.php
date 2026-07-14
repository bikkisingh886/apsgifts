<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_products extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth_lib->require_admin();
        $this->load->model('Product_model');
        $this->load->model('Category_model');
        $this->load->model('Offer_model');
    }

    /**
     * List all products.
     */
    public function index() {
        $data['products'] = $this->Product_model->get_all();
        
        $data['subview'] = 'admin/products/list';
        $data['meta_title'] = 'Product Management | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Add new product form.
     */
    public function add() {
        $data['product'] = NULL;
        $data['categories'] = $this->Category_model->get_active();
        $data['offers'] = $this->Offer_model->get_active();
        
        $data['subview'] = 'admin/products/form';
        $data['meta_title'] = 'Add New Product | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Edit product form.
     *
     * @param int $id Product ID
     */
    public function edit($id = NULL) {
        if ($id === NULL) {
            redirect('admin/products');
        }

        $product = $this->Product_model->get_by_id($id);
        if (!$product) {
            $this->session->set_flashdata('error', 'Product not found.');
            redirect('admin/products');
        }

        $data['product'] = $product;
        $data['categories'] = $this->Category_model->get_active();
        $data['offers'] = $this->Offer_model->get_active();
        
        $data['subview'] = 'admin/products/form';
        $data['meta_title'] = 'Edit Product - ' . $product['name'] . ' | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Save Product details (Create or Update).
     */
    public function save() {
        if ($this->input->method() !== 'post') {
            redirect('admin/products');
        }

        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $sku = $this->input->post('sku');
        $price = (float)$this->input->post('price');
        $description = $this->input->post('description');
        $delivery_type = $this->input->post('delivery_type');
        $offer_id = $this->input->post('offer_id') ?: NULL;
        $meta_title = $this->input->post('meta_title');
        $meta_desc = $this->input->post('meta_desc');
        $category_ids = $this->input->post('categories') ?: array();
        $is_active = $this->input->post('is_active') !== NULL ? 1 : 0;

        if (empty($name) || empty($price)) {
            $this->session->set_flashdata('error', 'Product name and price are required.');
            redirect($id ? 'admin/products/edit/' . $id : 'admin/products/add');
        }

        // Generate Slug and SKU
        $slug = generate_slug($name);
        
        if (empty($sku)) {
            $sku = 'GS-' . strtoupper(substr(uniqid(), -5)) . rand(10, 99);
        }

        // Check unique slug and SKU
        $this->db->where('slug', $slug);
        if ($id) $this->db->where('id !=', $id);
        $slug_check = $this->db->get('products')->row_array();
        if ($slug_check) {
            $slug = $slug . '-' . rand(100, 999);
        }

        // Multi-image upload parsing (as defined in directory tree page 15)
        $uploaded_paths = array();
        if (!empty($_FILES['product_images']['name'][0])) {
            $files = $_FILES['product_images'];
            $count = count($files['name']);
            
            $config['upload_path']   = './uploads/products/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
            $config['max_size']      = 3072; // 3MB
            $config['encrypt_name']  = TRUE;
            
            $this->load->library('upload');

            for ($i = 0; $i < $count; $i++) {
                if (empty($files['name'][$i])) continue;
                
                $_FILES['userfile']['name']     = $files['name'][$i];
                $_FILES['userfile']['type']     = $files['type'][$i];
                $_FILES['userfile']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['userfile']['error']    = $files['error'][$i];
                $_FILES['userfile']['size']     = $files['size'][$i];
                
                $this->upload->initialize($config);
                
                if ($this->upload->do_upload('userfile')) {
                    $upload_data = $this->upload->data();
                    $uploaded_paths[] = 'uploads/products/' . $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('error', 'Image upload failed: ' . $this->upload->display_errors());
                    redirect($id ? 'admin/products/edit/' . $id : 'admin/products/add');
                }
            }
        }

        $product_data = array(
            'name' => $name,
            'slug' => $slug,
            'sku' => $sku,
            'price' => $price,
            'description' => $description,
            'delivery_type' => $delivery_type,
            'offer_id' => $offer_id,
            'meta_title' => $meta_title ?: $name,
            'meta_desc' => $meta_desc,
            'is_active' => $is_active
        );

        if ($id) {
            // Delete marked images
            $delete_images = $this->input->post('delete_images') ?: array();
            
            // Update product
            if ($this->Product_model->update_product($id, $product_data, $category_ids, $uploaded_paths, $delete_images)) {
                $this->session->set_flashdata('success', 'Product updated successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to update product.');
            }
        } else {
            // Insert product
            if ($this->Product_model->insert_product($product_data, $category_ids, $uploaded_paths)) {
                $this->session->set_flashdata('success', 'Product created successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to create product.');
            }
        }

        redirect('admin/products');
    }

    /**
     * Delete product.
     *
     * @param int $id Product ID
     */
    public function delete($id = NULL) {
        if ($id === NULL) {
            redirect('admin/products');
        }

        // Delete images files from folder before deletion
        $images = $this->Product_model->get_product_images($id);
        foreach ($images as $img) {
            $filepath = './' . $img['image_path'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        if ($this->Product_model->delete_product($id)) {
            $this->session->set_flashdata('success', 'Product deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete product.');
        }

        redirect('admin/products');
    }
}
