<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_categories extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth_lib->require_admin();
        $this->load->model('Category_model');
    }

    /**
     * List all categories.
     */
    public function index() {
        // Fetch categories with product counts (include active and inactive)
        $data['categories'] = $this->Category_model->get_with_product_counts(FALSE);
        
        $data['subview'] = 'admin/categories/list';
        $data['meta_title'] = 'Category Management | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Add category form.
     */
    public function add() {
        $data['category'] = NULL; // Empty for form creation
        
        $data['subview'] = 'admin/categories/form';
        $data['meta_title'] = 'Add New Category | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Edit category form.
     *
     * @param int $id Category ID
     */
    public function edit($id = NULL) {
        if ($id === NULL) {
            redirect('admin/categories');
        }

        $category = $this->Category_model->get_by_id($id);
        if (!$category) {
            $this->session->set_flashdata('error', 'Category not found.');
            redirect('admin/categories');
        }

        $data['category'] = $category;
        
        $data['subview'] = 'admin/categories/form';
        $data['meta_title'] = 'Edit Category - ' . $category['name'] . ' | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Save Category details (Create or Update).
     */
    public function save() {
        if ($this->input->method() !== 'post') {
            redirect('admin/categories');
        }

        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $slug = $this->input->post('slug');
        $summary = $this->input->post('summary');
        $footer_content = $this->input->post('footer_content');
        $meta_title = $this->input->post('meta_title');
        $meta_desc = $this->input->post('meta_desc');
        $is_active = $this->input->post('is_active') !== NULL ? 1 : 0;

        if (empty($name)) {
            $this->session->set_flashdata('error', 'Category name is required.');
            redirect($id ? 'admin/categories/edit/' . $id : 'admin/categories/add');
        }

        // Generate slug if empty
        if (empty($slug)) {
            $slug = generate_slug($name);
        } else {
            $slug = generate_slug($slug);
        }

        // Check if slug is unique
        $existing = $this->Category_model->get_by_slug($slug);
        if ($existing && (!$id || $existing['id'] != $id)) {
            $this->session->set_flashdata('error', 'Category slug already exists. Please choose a different slug.');
            redirect($id ? 'admin/categories/edit/' . $id : 'admin/categories/add');
        }

        // Handle category banner upload
        $image_path = NULL;
        if (!empty($_FILES['banner_image']['name'])) {
            $config['upload_path']   = './uploads/products/'; // Save all uploads here
            $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
            $config['max_size']      = 2048; // 2MB max
            $config['encrypt_name']  = TRUE; // Encrypt file name for security

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('banner_image')) {
                $upload_data = $this->upload->data();
                $image_path = 'uploads/products/' . $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error', 'Image upload failed: ' . $this->upload->display_errors());
                redirect($id ? 'admin/categories/edit/' . $id : 'admin/categories/add');
            }
        }

        $category_data = array(
            'name' => $name,
            'slug' => $slug,
            'summary' => $summary,
            'footer_content' => $footer_content,
            'meta_title' => $meta_title ?: $name,
            'meta_desc' => $meta_desc,
            'is_active' => $is_active
        );

        // If a new banner was uploaded, add it to data
        if ($image_path) {
            $category_data['banner_image'] = $image_path; // We can store this or handle it if we want
        }

        if ($id) {
            // Update
            $this->Category_model->update($id, $category_data);
            $this->session->set_flashdata('success', 'Category updated successfully.');
        } else {
            // Insert
            $this->Category_model->insert($category_data);
            $this->session->set_flashdata('success', 'Category created successfully.');
        }

        redirect('admin/categories');
    }

    /**
     * Delete Category.
     *
     * @param int $id Category ID
     */
    public function delete($id = NULL) {
        if ($id === NULL) {
            redirect('admin/categories');
        }

        if ($this->Category_model->delete($id)) {
            $this->session->set_flashdata('success', 'Category deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete category.');
        }

        redirect('admin/categories');
    }
}
