<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Reviews extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
    }

    /**
     * List all reviews and display creation form.
     */
    public function index()
    {
        $this->checkPermission('reviews', 'view');
        $db = \Config\Database::connect();
        
        // Fetch reviews with product details and user name fallback
        $data['reviews'] = $db->table('product_reviews pr')
            ->select('pr.*, p.name as product_name, COALESCE(u.name, pr.customer_name) as reviewer_name, u.email as reviewer_email')
            ->join('products p', 'p.id = pr.product_id', 'left')
            ->join('users u', 'u.id = pr.user_id', 'left')
            ->orderBy('pr.id', 'DESC')
            ->get()
            ->getResultArray();
            
        $data['title'] = 'Product Reviews Moderation';
        
        return view('admin/reviews/index', $data);
    }

    /**
     * Search products for Select2.
     */
    public function search_products()
    {
        $this->checkPermission('reviews', 'view');
        $term = $this->request->getGet('q') ?? '';
        $db = \Config\Database::connect();
        $products = $db->table('products')
            ->select('id, name as text')
            ->like('name', $term)
            ->where('is_active', 1)
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['results' => $products]);
    }

    /**
     * Submit an admin manual review.
     */
    public function create()
    {
        $this->checkPermission('reviews', 'create');
        $productId = (int)$this->request->getPost('product_id');
        $rating = (int)$this->request->getPost('rating');
        $reviewText = trim($this->request->getPost('review_text') ?? '');
        $customerName = trim($this->request->getPost('customer_name') ?? '');

        if (!$productId || $rating < 1 || $rating > 5 || empty($customerName)) {
            $this->session->setFlashdata('error', 'Product, Rating, and Customer Name are required.');
            return redirect()->to(base_url('admin/reviews'));
        }

        $db = \Config\Database::connect();
        $currentUserId = $this->authLib->getUserId();
        
        $db->table('product_reviews')->insert([
            'product_id'    => $productId,
            'user_id'       => null,
            'customer_name' => $customerName,
            'rating'        => $rating,
            'review_text'   => $reviewText,
            'status'        => 'approved', // Admin entries are auto-approved
            'created_at'    => date('Y-m-d H:i:s'),
            'created_by'    => $currentUserId,
            'updated_by'    => $currentUserId
        ]);

        $this->logActivity('reviews', 'create', "Manually created approved review for product ID: $productId");
        $this->session->setFlashdata('success', 'Review added and approved successfully.');
        return redirect()->to(base_url('admin/reviews'));
    }

    /**
     * Approve a review.
     */
    public function approve($id = null)
    {
        $this->checkPermission('reviews', 'edit');
        if ($id === null) {
            return redirect()->to(base_url('admin/reviews'));
        }

        $db = \Config\Database::connect();
        $currentUserId = $this->authLib->getUserId();
        $db->table('product_reviews')->where('id', $id)->update([
            'status' => 'approved',
            'updated_by' => $currentUserId
        ]);
        $this->logActivity('reviews', 'edit', "Approved product review ID: $id");
        $this->session->setFlashdata('success', 'Review approved successfully.');
        return redirect()->to(base_url('admin/reviews'));
    }

    /**
     * Set a review status back to pending.
     */
    public function disapprove($id = null)
    {
        $this->checkPermission('reviews', 'edit');
        if ($id === null) {
            return redirect()->to(base_url('admin/reviews'));
        }

        $db = \Config\Database::connect();
        $currentUserId = $this->authLib->getUserId();
        $db->table('product_reviews')->where('id', $id)->update([
            'status' => 'pending',
            'updated_by' => $currentUserId
        ]);
        $this->logActivity('reviews', 'edit', "Disapproved/Set-to-pending product review ID: $id");
        $this->session->setFlashdata('success', 'Review set back to pending.');
        return redirect()->to(base_url('admin/reviews'));
    }

    /**
     * Delete a review.
     */
    public function delete($id = null)
    {
        $this->checkPermission('reviews', 'delete');
        if ($id === null) {
            return redirect()->to(base_url('admin/reviews'));
        }

        $db = \Config\Database::connect();
        $db->table('product_reviews')->where('id', $id)->delete();
        $this->logActivity('reviews', 'delete', "Deleted product review ID: $id");
        $this->session->setFlashdata('success', 'Review deleted successfully.');
        return redirect()->to(base_url('admin/reviews'));
    }
}
