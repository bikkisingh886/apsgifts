<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;

class User extends BaseController
{
    protected $orderModel;
    protected $productModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $router = service('router');
        $method = $router->methodName();
        if ($method !== 'wishlist' && $method !== 'wishlist_toggle') {
            $this->authLib->requireLogin();
        }
        
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Customer Dashboard.
     */
    public function dashboard()
    {
        $data['meta_title'] = 'My Dashboard | GiftShop';
        return view('frontend/user/dashboard', $data);
    }

    /**
     * Customer Orders list.
     */
    public function orders()
    {
        $userId = $this->authLib->getUserId();
        $data['orders'] = $this->orderModel->getUserOrders($userId);
        $data['meta_title'] = 'My Orders | GiftShop';

        return view('frontend/user/orders', $data);
    }

    /**
     * Customer Order detail.
     */
    public function order_detail($orderNumber = null)
    {
        if ($orderNumber === null) {
            return redirect()->to(base_url('user/orders'));
        }

        $order = $this->orderModel->getByNumber($orderNumber);
        if (!$order || $order['user_id'] != $this->authLib->getUserId()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Order not found.");
        }

        $data['order'] = $order;
        $data['address'] = json_decode($order['address_json'], true);
        $data['meta_title'] = 'Order Detail - #' . $orderNumber . ' | GiftShop';

        return view('frontend/user/order_detail', $data);
    }

    /**
     * Customer Wishlist.
     */
    public function wishlist()
    {
        $wishlistIds = $this->session->get('wishlist') ?: [];
        $products = [];
        
        if (!empty($wishlistIds)) {
            foreach ($wishlistIds as $id) {
                $prod = $this->productModel->getById($id);
                if ($prod && $prod['is_active']) {
                    $products[] = $prod;
                }
            }
        }

        $data['products'] = $products;
        $data['meta_title'] = 'My Wishlist | GiftShop';

        return view('frontend/user/wishlist', $data);
    }

    /**
     * Toggle wishlist item via AJAX.
     */
    public function wishlist_toggle()
    {
        $productId = (int)$this->request->getPost('product_id');
        if (!$productId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid product.']);
        }

        $wishlist = $this->session->get('wishlist') ?: [];
        
        if (in_array($productId, $wishlist)) {
            // Remove
            $wishlist = array_values(array_diff($wishlist, [$productId]));
            $action = 'removed';
        } else {
            // Add
            $wishlist[] = $productId;
            $action = 'added';
        }

        $this->session->set('wishlist', $wishlist);

        return $this->response->setJSON([
            'success' => true,
            'action'  => $action,
            'count'   => count($wishlist)
        ]);
    }

    /**
     * Customer Profile Settings.
     */
    public function settings()
    {
        $data['user'] = $this->authLib->getUser();
        $data['meta_title'] = 'Profile Settings | GiftShop';
        return view('frontend/user/settings', $data);
    }

    /**
     * Process Profile Settings Update.
     */
    public function settings_update()
    {
        $userId = $this->authLib->getUserId();
        $name = trim($this->request->getPost('name') ?? '');
        $mobile = trim($this->request->getPost('mobile') ?? '');
        $password = trim($this->request->getPost('password') ?? '');

        if (empty($name) || empty($mobile)) {
            $this->session->setFlashdata('error', 'Name and mobile number are required.');
            return redirect()->to(base_url('user/settings'));
        }

        $db = \Config\Database::connect();
        
        $updateData = [
            'name' => $name,
            'mobile' => $mobile
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Handle profile photo upload
        $files = $this->request->getFiles();
        if (isset($files['profile_photo']) && $files['profile_photo']->isValid()) {
            $photo = $files['profile_photo'];
            $newName = $photo->getRandomName();
            if (!is_dir(FCPATH . 'uploads/profile')) {
                mkdir(FCPATH . 'uploads/profile', 0777, true);
            }
            if ($photo->move(FCPATH . 'uploads/profile', $newName)) {
                $updateData['profile_photo'] = 'uploads/profile/' . $newName;
            }
        }

        $db->table('users')->where('id', $userId)->update($updateData);
        $this->session->set([
            'user_name' => $name,
            'user_mobile' => $mobile
        ]);
        $this->session->setFlashdata('success', 'Profile updated successfully.');
        return redirect()->to(base_url('user/settings'));
    }

    /**
     * Handle customization submission for an order item.
     */
    public function personalize_item($itemId = null)
    {
        if ($itemId === null) {
            return redirect()->to(base_url('user/orders'));
        }

        $db = \Config\Database::connect();
        
        // Fetch order item and check ownership
        $item = $db->table('order_items oi')
            ->select('oi.*, o.user_id, o.order_number')
            ->join('orders o', 'o.id = oi.order_id')
            ->where('oi.id', $itemId)
            ->get()
            ->getRowArray();

        if (empty($item) || $item['user_id'] != $this->authLib->getUserId()) {
            $this->session->setFlashdata('error', 'Access denied.');
            return redirect()->to(base_url('user/orders'));
        }

        $custData = [];
        
        // Handle custom text
        $text = trim($this->request->getPost('customization_text') ?? '');
        if ($this->request->getPost('customization_text') !== null) {
            $custData['text'] = $text;
        }

        // Handle custom image upload
        $files = $this->request->getFiles();
        if (isset($files['customization_image']) && $files['customization_image']->isValid() && !$files['customization_image']->hasMoved()) {
            $img = $files['customization_image'];
            $newName = $img->getRandomName();
            if (!is_dir(FCPATH . 'uploads/customization')) {
                mkdir(FCPATH . 'uploads/customization', 0777, true);
            }
            if ($img->move(FCPATH . 'uploads/customization', $newName)) {
                $custData['image'] = 'uploads/customization/' . $newName;
            }
        }

        $existing = json_decode($item['customization_data'] ?? '{}', true);
        if (!is_array($existing)) {
            $existing = [];
        }
        
        $merged = array_merge($existing, $custData);
        
        $db->table('order_items')
            ->where('id', $itemId)
            ->update(['customization_data' => json_encode($merged)]);

        $this->session->setFlashdata('success', 'Personalization details saved successfully.');
        return redirect()->to(base_url('user/orders/' . $item['order_number']));
    }
}
