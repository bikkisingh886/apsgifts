<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_offers extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth_lib->require_admin();
        $this->load->model('Offer_model');
    }

    /**
     * List offers and display add/edit form.
     */
    public function index($edit_id = NULL) {
        // Fetch all offers
        $data['offers'] = $this->Offer_model->get_all();
        
        // Fetch offer to edit if ID is provided
        $data['edit_offer'] = NULL;
        if ($edit_id !== NULL) {
            $data['edit_offer'] = $this->Offer_model->get_by_id($edit_id);
        }

        $data['subview'] = 'admin/offers/list';
        $data['meta_title'] = 'Manage Offers | GiftShop';

        $this->load->view('admin/layout', $data);
    }

    /**
     * Save offer (Insert or Update).
     */
    public function save() {
        if ($this->input->method() !== 'post') {
            redirect('admin/offers');
        }

        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $type = $this->input->post('type');
        $value = (float)$this->input->post('value');
        $applies_to = $this->input->post('applies_to');
        $is_active = $this->input->post('is_active') !== NULL ? 1 : 0;

        if (empty($name) || empty($value)) {
            $this->session->set_flashdata('error', 'Offer name and value are required.');
            redirect('admin/offers' . ($id ? '/index/' . $id : ''));
        }

        $offer_data = array(
            'name' => $name,
            'type' => $type,
            'value' => $value,
            'applies_to' => $applies_to,
            'is_active' => $is_active
        );

        if ($id) {
            $this->Offer_model->update($id, $offer_data);
            $this->session->set_flashdata('success', 'Offer updated successfully.');
        } else {
            $this->Offer_model->insert($offer_data);
            $this->session->set_flashdata('success', 'Offer created successfully.');
        }

        redirect('admin/offers');
    }

    /**
     * Route handler for Edit link.
     */
    public function edit($id = NULL) {
        if ($id === NULL) {
            redirect('admin/offers');
        }
        $this->index($id);
    }

    /**
     * Delete offer.
     */
    public function delete($id = NULL) {
        if ($id === NULL) {
            redirect('admin/offers');
        }

        if ($this->Offer_model->delete($id)) {
            $this->session->set_flashdata('success', 'Offer deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete offer.');
        }

        redirect('admin/offers');
    }
}
