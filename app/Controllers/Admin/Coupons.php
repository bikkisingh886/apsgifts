<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Coupons extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
    }

    /**
     * Display all coupons and usage logs.
     */
    public function index()
    {
        $this->checkPermission('coupons', 'view');
        $db = \Config\Database::connect();
        
        // Fetch all coupons
        $builder = $db->table('coupons c')
            ->select('c.*, creator.name as creator_name, updater.name as updater_name')
            ->join('users creator', 'creator.id = c.created_by', 'left')
            ->join('users updater', 'updater.id = c.updated_by', 'left')
            ->orderBy('c.id', 'DESC');

        $data['coupons'] = $builder->get()->getResultArray();
        
        // Fetch coupon usage accounting records
        $data['usage_logs'] = $db->table('orders o')
            ->select('o.order_number, o.created_at, o.subtotal, o.coupon_code, o.coupon_discount, u.name as customer_name')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->where('o.coupon_code IS NOT NULL')
            ->where('o.coupon_code !=', '')
            ->orderBy('o.id', 'DESC')
            ->get()
            ->getResultArray();
            
        $data['title'] = 'Coupon Codes Management';
        
        return view('admin/coupons/index', $data);
    }

    /**
     * Create a new coupon.
     */
    public function create()
    {
        $this->checkPermission('coupons', 'create');
        $code = strtoupper(trim($this->request->getPost('code') ?? ''));
        $discount_type = $this->request->getPost('discount_type');
        $discount_value = (float)$this->request->getPost('discount_value');
        $min_cart_amount = (float)$this->request->getPost('min_cart_amount');

        if (empty($code) || $discount_value <= 0) {
            $this->session->setFlashdata('error', 'Coupon code and a valid discount value are required.');
            return redirect()->to(base_url('admin/coupons'));
        }

        $db = \Config\Database::connect();
        
        // Check if coupon code already exists
        $existing = $db->table('coupons')->where('code', $code)->get()->getRowArray();
        if ($existing) {
            $this->session->setFlashdata('error', 'A coupon with code "' . $code . '" already exists.');
            return redirect()->to(base_url('admin/coupons'));
        }

        $currentUserId = $this->authLib->getUserId();

        $db->table('coupons')->insert([
            'code' => $code,
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'min_cart_amount' => $min_cart_amount,
            'is_active' => 1,
            'created_by' => $currentUserId,
            'updated_by' => $currentUserId
        ]);

        $this->logActivity('coupons', 'create', "Created coupon code: $code");
        $this->session->setFlashdata('success', 'Coupon "' . $code . '" created successfully.');
        return redirect()->to(base_url('admin/coupons'));
    }

    /**
     * Toggle active status of a coupon.
     */
    public function toggle($id = null)
    {
        $this->checkPermission('coupons', 'edit');
        if ($id === null) {
            return redirect()->to(base_url('admin/coupons'));
        }

        $db = \Config\Database::connect();
        $coupon = $db->table('coupons')->where('id', $id)->get()->getRowArray();
        if ($coupon) {
            $newStatus = $coupon['is_active'] == 1 ? 0 : 1;
            $currentUserId = $this->authLib->getUserId();
            $db->table('coupons')->where('id', $id)->update([
                'is_active' => $newStatus,
                'updated_by' => $currentUserId
            ]);
            $this->logActivity('coupons', 'edit', "Toggled active status of coupon: {$coupon['code']} to " . ($newStatus ? 'Active' : 'Inactive'));
            $this->session->setFlashdata('success', 'Coupon status updated.');
        }

        return redirect()->to(base_url('admin/coupons'));
    }

    /**
     * Delete a coupon.
     */
    public function delete($id = null)
    {
        $this->checkPermission('coupons', 'delete');
        if ($id === null) {
            return redirect()->to(base_url('admin/coupons'));
        }

        $db = \Config\Database::connect();
        $coupon = $db->table('coupons')->where('id', $id)->get()->getRowArray();
        $code = $coupon ? $coupon['code'] : 'ID: ' . $id;

        $db->table('coupons')->where('id', $id)->delete();
        $this->logActivity('coupons', 'delete', "Deleted coupon code: $code");
        $this->session->setFlashdata('success', 'Coupon deleted successfully.');
        
        return redirect()->to(base_url('admin/coupons'));
    }
}
