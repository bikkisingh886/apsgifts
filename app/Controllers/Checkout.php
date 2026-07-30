<?php

namespace App\Controllers;

use App\Models\OrderModel;

class Checkout extends BaseController
{
    protected $orderModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireLogin();
        $this->orderModel = new OrderModel();
    }

    /**
     * Display checkout page.
     */
    public function index()
    {
        $cart = $this->cartLib->contents();
        if (empty($cart)) {
            $this->session->setFlashdata('error', 'Your cart is empty. Add products to place an order.');
            return redirect()->to(base_url('cart'));
        }

        $subtotal = $this->cartLib->subtotal();
        $discount = $this->cartLib->discount();
        $total = $this->cartLib->total();

        $user = $this->authLib->getUser();

        $data['cart'] = $cart;
        $data['subtotal'] = $subtotal;
        $data['discount'] = $discount;
        $data['total'] = $total;
        $data['user_name'] = $user['name'] ?? '';
        $data['user_mobile'] = $user['mobile'] ?? '';

        $data['meta_title'] = 'Checkout | GiftShop';
        $data['meta_desc'] = 'Secure checkout for your gift order.';

        return view('frontend/checkout', $data);
    }

    /**
     * Process checkout form submit.
     */
    public function process()
    {
        $cart = $this->cartLib->contents();
        if (empty($cart)) {
            $this->session->setFlashdata('error', 'Your cart is empty.');
            return redirect()->to(base_url('cart'));
        }

        $name = $this->request->getPost('name');
        $mobile = $this->request->getPost('mobile');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city');
        $pin = $this->request->getPost('pin');

        if (empty($name) || empty($mobile) || empty($address) || empty($city) || empty($pin)) {
            $this->session->setFlashdata('error', 'Please fill in all required address fields.');
            return redirect()->to(base_url('checkout'));
        }

        $address_data = [
            'name'    => $name,
            'mobile'  => $mobile,
            'address' => $address,
            'city'    => $city,
            'pin'     => $pin
        ];
        $this->session->set('pending_address', $address_data);

        // Check if cart contains customizable items
        $cart = $this->cartLib->contents();
        $hasCustomizable = false;
        $productModel = new \App\Models\ProductModel();
        foreach ($cart as $key => $item) {
            $prod = $productModel->find($item['product_id']);
            if ($prod && $prod['is_customizable'] == 1) {
                $hasCustomizable = true;
                // Sync customization details back to session cart items just in case
                $cart[$key]['is_customizable'] = 1;
                $cart[$key]['customization_type'] = $prod['customization_type'];
            }
        }
        $this->session->set('cart', $cart);

        if ($hasCustomizable) {
            return redirect()->to(base_url('checkout/personalize'));
        }

        // Save order immediately if no customizable items
        $order_number = $this->place_pending_order();
        if ($order_number) {
            return redirect()->to(base_url('checkout/complete/' . $order_number));
        } else {
            $this->session->setFlashdata('error', 'Order placement failed. Please try again.');
            return redirect()->to(base_url('checkout'));
        }
    }

    /**
     * Display order success confirmation page.
     */
    public function success($order_number = null)
    {
        if ($order_number === null) {
            return redirect()->to(base_url());
        }

        $order = $this->orderModel->getByNumber($order_number);
        if (!$order || $order['user_id'] != $this->authLib->getUserId()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Order not found.");
        }

        $data['order'] = $order;
        $data['address'] = json_decode($order['address_json'], true);
        $data['meta_title'] = 'Order Confirmed - #' . $order_number . ' | GiftShop';

        return view('frontend/checkout_complete', $data);
    }

    /**
     * Display order personalization page during checkout.
     */
    public function personalize()
    {
        $address = $this->session->get('pending_address');
        if (empty($address)) {
            return redirect()->to(base_url('checkout'));
        }

        $cart = $this->cartLib->contents();
        if (empty($cart)) {
            return redirect()->to(base_url('cart'));
        }

        // Filter out non-customizable items
        $customizableItems = [];
        foreach ($cart as $item) {
            if (isset($item['is_customizable']) && $item['is_customizable'] == 1) {
                $customizableItems[] = $item;
            }
        }

        if (empty($customizableItems)) {
            // No customizable items? Finalize order directly
            $order_number = $this->place_pending_order();
            if ($order_number) {
                return redirect()->to(base_url('checkout/complete/' . $order_number));
            } else {
                return redirect()->to(base_url('checkout'));
            }
        }

        $data['items'] = $customizableItems;
        $data['meta_title'] = 'Personalize Your Order | GiftShop';

        return view('frontend/checkout_personalize', $data);
    }

    /**
     * Save personalization details during checkout to the cart session.
     */
    public function personalize_submit($itemKey = null)
    {
        if ($itemKey === null) {
            return redirect()->to(base_url());
        }

        $cart = $this->session->get('cart') ?: [];
        if (!isset($cart[$itemKey])) {
            $this->session->setFlashdata('error', 'Item not found in cart.');
            return redirect()->to(base_url('checkout/personalize'));
        }

        $custData = [];
        
        // Handle custom text
        $text = trim($this->request->getPost('customization_text') ?? '');
        if ($this->request->getPost('customization_text') !== null) {
            $cart[$itemKey]['customization_text'] = $text;
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
                $cart[$itemKey]['customization_image'] = 'uploads/customization/' . $newName;
            }
        }

        $this->session->set('cart', $cart);

        $this->session->setFlashdata('success', 'Personalization details saved successfully.');
        return redirect()->to(base_url('checkout/personalize'));
    }

    /**
     * Finalize the order from session data.
     */
    public function complete_personalization()
    {
        $address = $this->session->get('pending_address');
        if (empty($address)) {
            $this->session->setFlashdata('error', 'Address details missing.');
            return redirect()->to(base_url('checkout'));
        }

        $cart = $this->cartLib->contents();
        if (empty($cart)) {
            $this->session->setFlashdata('error', 'Your cart is empty.');
            return redirect()->to(base_url('cart'));
        }

        // Validate that personalization is done for all customizable items
        foreach ($cart as $item) {
            if (isset($item['is_customizable']) && $item['is_customizable'] == 1) {
                $hasText = !empty($item['customization_text']);
                $hasImg = !empty($item['customization_image']);
                
                if ($item['customization_type'] === 'text' && !$hasText) {
                    $this->session->setFlashdata('error', 'Please enter custom text for: ' . esc($item['name']));
                    return redirect()->to(base_url('checkout/personalize'));
                }
                if ($item['customization_type'] === 'image' && !$hasImg) {
                    $this->session->setFlashdata('error', 'Please upload a photo for: ' . esc($item['name']));
                    return redirect()->to(base_url('checkout/personalize'));
                }
                if ($item['customization_type'] === 'both' && (!$hasText || !$hasImg)) {
                    $this->session->setFlashdata('error', 'Please provide both photo and custom text for: ' . esc($item['name']));
                    return redirect()->to(base_url('checkout/personalize'));
                }
            }
        }

        $order_number = $this->place_pending_order();
        if ($order_number) {
            return redirect()->to(base_url('checkout/complete/' . $order_number));
        } else {
            $this->session->setFlashdata('error', 'Order placement failed. Please try again.');
            return redirect()->to(base_url('checkout/personalize'));
        }
    }

    /**
     * Internal helper to create the actual database order records.
     */
    private function place_pending_order()
    {
        $address_data = $this->session->get('pending_address');
        if (empty($address_data)) {
            return false;
        }

        $cart = $this->cartLib->contents();
        if (empty($cart)) {
            return false;
        }

        $subtotal = $this->cartLib->subtotal();
        $globalDiscount = $this->cartLib->getGlobalDiscount();
        $couponCode = '';
        $couponDiscount = 0.00;
        $appliedCoupon = $this->session->get('applied_coupon');
        if ($appliedCoupon) {
            $couponCode = $appliedCoupon['code'];
            $couponDiscount = $this->cartLib->getCouponDiscount();
        }
        $total = $this->cartLib->total();

        // Calculate delivery date
        $earliest_date = null;
        foreach ($cart as $item) {
            if (!empty($item['delivery_date'])) {
                if ($earliest_date === null || strtotime($item['delivery_date']) < strtotime($earliest_date)) {
                    $earliest_date = $item['delivery_date'];
                }
            }
        }
        if ($earliest_date === null) {
            helper('delivery');
            $earliest_date = date('Y-m-d', strtotime(calculate_courier_eta()));
        }

        $order_data = [
            'user_id'         => $this->authLib->getUserId(),
            'status'          => 'Processing',
            'subtotal'        => $subtotal,
            'discount'        => $globalDiscount,
            'coupon_code'     => $couponCode,
            'coupon_discount' => $couponDiscount,
            'total'           => $total,
            'delivery_date'   => $earliest_date,
            'address_json'    => json_encode($address_data)
        ];

        $order_number = $this->orderModel->placeOrder($order_data, $cart);

        if ($order_number) {
            // Update coupon usage count
            if (!empty($couponCode)) {
                $db = \Config\Database::connect();
                $db->table('coupons')
                   ->where('code', $couponCode)
                   ->increment('usage_count', 1);
            }

            // Clear session order details
            $this->session->remove('pending_address');
            $this->session->remove('applied_coupon');
            $this->cartLib->destroy();
        }

        return $order_number;
    }
}
