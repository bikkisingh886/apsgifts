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

        $subtotal = $this->cartLib->subtotal();
        $globalDiscount = $this->cartLib->getGlobalDiscount();
        $couponDiscount = $this->cartLib->getCouponDiscount();
        
        $totalDiscount = $globalDiscount + $couponDiscount;
        if ($totalDiscount > $subtotal) {
            $totalDiscount = $subtotal;
        }
        $total = $subtotal - $totalDiscount;

        $coupon = $this->session->get('applied_coupon');
        $couponCode = $coupon['code'] ?? null;

        // Standardize delivery date to be the earliest date chosen, or default today/tomorrow
        $earliest_date = null;
        foreach ($cart as $item) {
            if (isset($item['delivery_date']) && !empty($item['delivery_date'])) {
                if ($earliest_date === null || $item['delivery_date'] < $earliest_date) {
                    $earliest_date = $item['delivery_date'];
                }
            }
        }

        // If no express date chosen, calculate courier delivery date
        if ($earliest_date === null) {
            $earliest_date = date('Y-m-d', strtotime(calculate_courier_eta()));
        }

        $address_data = [
            'name'    => $name,
            'mobile'  => $mobile,
            'address' => $address,
            'city'    => $city,
            'pin'     => $pin
        ];

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

            // Clear cart session
            $this->cartLib->destroy();
            
            $this->session->setFlashdata('success', 'Order placed successfully!');
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
}
