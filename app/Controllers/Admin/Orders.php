<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class Orders extends BaseController
{
    protected $orderModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->orderModel = new OrderModel();
    }

    /**
     * Orders list.
     */
    public function index()
    {
        $this->checkPermission('orders', 'view');
        $data['orders'] = $this->orderModel->getAllOrders();
        $data['title'] = 'Manage Orders';
        return view('admin/orders/index', $data);
    }

    /**
     * View order detail.
     */
    public function view($id = null)
    {
        $this->checkPermission('orders', 'view');
        if ($id === null) {
            return redirect()->to(base_url('admin/orders'));
        }

        $order = $this->orderModel->getById($id);
        if (!$order) {
            $this->session->setFlashdata('error', 'Order not found.');
            return redirect()->to(base_url('admin/orders'));
        }

        $data['order'] = $order;
        $data['address'] = json_decode($order['address_json'], true);
        $data['title'] = 'View Order: #' . $order['order_number'];
        return view('admin/orders/view', $data);
    }

    /**
     * Update order status.
     */
    public function update_status()
    {
        $this->checkPermission('orders', 'edit');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $orderId = (int)$this->request->getPost('order_id');
            $status = $this->request->getPost('status');

            if ($orderId && $status) {
                if ($this->orderModel->update($orderId, ['status' => $status])) {
                    $this->logActivity('orders', 'edit', "Updated status of order ID: $orderId to $status");
                    $this->session->setFlashdata('success', 'Order status updated successfully.');
                } else {
                    $this->session->setFlashdata('error', 'Failed to update order status.');
                }
            }
        }
        return redirect()->back();
    }

    /**
     * Update order tracking details.
     */
    public function update_tracking()
    {
        $this->checkPermission('orders', 'edit');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $orderId = (int)$this->request->getPost('order_id');
            $trackingUrl = $this->request->getPost('tracking_url');
            $trackingCode = $this->request->getPost('tracking_code');

            if ($orderId) {
                $updateData = [
                    'tracking_url'  => $trackingUrl !== null ? trim($trackingUrl) : '',
                    'tracking_code' => $trackingCode !== null ? trim($trackingCode) : ''
                ];
                if ($this->orderModel->update($orderId, $updateData)) {
                    $this->logActivity('orders', 'edit', "Updated tracking info for order ID: $orderId");
                    $this->session->setFlashdata('success', 'Tracking details updated successfully.');
                } else {
                    $this->session->setFlashdata('error', 'Failed to update tracking details.');
                }
            }
        }
        return redirect()->back();
    }
}

