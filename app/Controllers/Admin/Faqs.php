<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FaqModel;

class Faqs extends BaseController
{
    protected $faqModel;

    public function __construct()
    {
        $this->faqModel = new FaqModel();
    }

    /**
     * List all FAQs in Admin Panel
     */
    public function index()
    {
        $data['faqs'] = $this->faqModel->orderBy('sort_order', 'ASC')->orderBy('id', 'DESC')->findAll();
        $data['categories'] = $this->faqModel->getCategories();
        return view('admin/faqs/index', $data);
    }

    /**
     * Store new FAQ
     */
    public function store()
    {
        $rules = [
            'question' => 'required|min_length[3]|max_length[255]',
            'answer'   => 'required|min_length[3]',
            'category' => 'required|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->faqModel->insert([
            'category'   => trim($this->request->getPost('category')),
            'question'   => trim($this->request->getPost('question')),
            'answer'     => trim($this->request->getPost('answer')),
            'sort_order' => (int)$this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0
        ]);

        return redirect()->to(base_url('admin/faqs'))->with('success', 'FAQ added successfully!');
    }

    /**
     * Get Edit Partial HTML for Modal
     */
    public function edit_partial($id)
    {
        $faq = $this->faqModel->find((int)$id);
        if (!$faq) {
            return $this->response->setStatusCode(404)->setBody('FAQ not found.');
        }

        $categories = $this->faqModel->getCategories();
        return view('admin/faqs/edit_partial', ['faq' => $faq, 'categories' => $categories]);
    }

    /**
     * Update FAQ record
     */
    public function update($id)
    {
        $faq = $this->faqModel->find((int)$id);
        if (!$faq) {
            return redirect()->to(base_url('admin/faqs'))->with('error', 'FAQ not found.');
        }

        $rules = [
            'question' => 'required|min_length[3]|max_length[255]',
            'answer'   => 'required|min_length[3]',
            'category' => 'required|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $this->faqModel->update($id, [
            'category'   => trim($this->request->getPost('category')),
            'question'   => trim($this->request->getPost('question')),
            'answer'     => trim($this->request->getPost('answer')),
            'sort_order' => (int)$this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0
        ]);

        return redirect()->to(base_url('admin/faqs'))->with('success', 'FAQ updated successfully!');
    }

    /**
     * Toggle Active Status via AJAX / GET
     */
    public function toggle($id)
    {
        $faq = $this->faqModel->find((int)$id);
        if ($faq) {
            $newStatus = $faq['is_active'] ? 0 : 1;
            $this->faqModel->update($id, ['is_active' => $newStatus]);
            return redirect()->to(base_url('admin/faqs'))->with('success', 'FAQ status updated!');
        }
        return redirect()->to(base_url('admin/faqs'))->with('error', 'FAQ not found.');
    }

    /**
     * Delete FAQ record
     */
    public function delete($id)
    {
        $faq = $this->faqModel->find((int)$id);
        if ($faq) {
            $this->faqModel->delete($id);
            return redirect()->to(base_url('admin/faqs'))->with('success', 'FAQ deleted successfully!');
        }
        return redirect()->to(base_url('admin/faqs'))->with('error', 'FAQ not found.');
    }
}
