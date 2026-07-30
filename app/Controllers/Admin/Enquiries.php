<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnquiryModel;

class Enquiries extends BaseController
{
    protected $enquiryModel;

    public function __construct()
    {
        $this->enquiryModel = new EnquiryModel();
    }

    /**
     * List all customer enquiries in Admin Panel
     */
    public function index()
    {
        $status = $this->request->getGet('status');
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $this->enquiryModel->orderBy('id', 'DESC');

        if ($status && in_array($status, ['unread', 'read', 'replied'])) {
            $builder->where('status', $status);
        }

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('email', $search)
                    ->orLike('phone', $search)
                    ->orLike('subject', $search)
                    ->groupEnd();
        }

        $data['enquiries'] = $builder->findAll();
        $data['selectedStatus'] = $status ?: 'all';
        $data['search'] = $search;

        // Counts
        $data['totalCount'] = $this->enquiryModel->countAllResults(false);
        $data['unreadCount'] = $this->enquiryModel->where('status', 'unread')->countAllResults();

        return view('admin/enquiries/index', $data);
    }

    /**
     * View Enquiry Details Partial (Modal)
     */
    public function view_partial($id)
    {
        $enquiry = $this->enquiryModel->find((int)$id);
        if (!$enquiry) {
            return $this->response->setStatusCode(404)->setBody('Enquiry not found.');
        }

        // Auto mark as read if currently unread
        if ($enquiry['status'] === 'unread') {
            $this->enquiryModel->update($id, ['status' => 'read']);
            $enquiry['status'] = 'read';
        }

        return view('admin/enquiries/view_partial', ['enquiry' => $enquiry]);
    }

    /**
     * Update Enquiry Status
     */
    public function update_status($id)
    {
        $enquiry = $this->enquiryModel->find((int)$id);
        if (!$enquiry) {
            return redirect()->to(base_url('admin/enquiries'))->with('error', 'Enquiry not found.');
        }

        $status = $this->request->getPost('status');
        if (in_array($status, ['unread', 'read', 'replied'])) {
            $this->enquiryModel->update($id, ['status' => $status]);
            return redirect()->to(base_url('admin/enquiries'))->with('success', 'Enquiry status updated!');
        }

        return redirect()->to(base_url('admin/enquiries'))->with('error', 'Invalid status.');
    }

    /**
     * Delete Enquiry
     */
    public function delete($id)
    {
        $enquiry = $this->enquiryModel->find((int)$id);
        if ($enquiry) {
            $this->enquiryModel->delete($id);
            return redirect()->to(base_url('admin/enquiries'))->with('success', 'Enquiry deleted successfully!');
        }
        return redirect()->to(base_url('admin/enquiries'))->with('error', 'Enquiry not found.');
    }
}
