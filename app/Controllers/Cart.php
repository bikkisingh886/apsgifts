<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Cart extends BaseController
{
    protected $productModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->productModel = new ProductModel();
    }

    /**
     * View shopping cart.
     */
    public function index()
    {
        $data['cart_items'] = $this->cartLib->contents();
        $data['subtotal'] = $this->cartLib->subtotal();
        
        // Calculate discounts using CartLib
        $data['discount'] = $this->cartLib->discount();
        $data['global_discount'] = $this->cartLib->getGlobalDiscount();
        $data['coupon_discount'] = $this->cartLib->getCouponDiscount();
        $data['applied_coupon'] = $this->session->get('applied_coupon');
        $data['total'] = $this->cartLib->total();

        $data['meta_title'] = 'Shopping Cart | GiftShop';
        $data['meta_desc'] = 'View your shopping cart items, update quantities, and calculate discounts.';

        return view('frontend/cart', $data);
    }

    /**
     * Add item to cart.
     */
    public function add()
    {
        $productId = $this->request->getPost('product_id');
        $qty = (int)$this->request->getPost('qty');
        $deliveryDate = $this->request->getPost('delivery_date');
        $customizationText = $this->request->getPost('customization_text');
        $customizationImage = null;

        if ($qty <= 0) {
            $qty = 1;
        }

        $product = $this->productModel->getByIdWithOffer((int)$productId);
        if (!$product || !$product['is_active']) {
            $this->session->setFlashdata('error', 'Product not found or inactive.');
            return redirect()->back();
        }

        // Handle image upload if product type allows image customization
        if (isset($product['customization_type']) && in_array($product['customization_type'], ['image', 'both'])) {
            $files = $this->request->getFiles();
            if (isset($files['customization_image']) && $files['customization_image']->isValid()) {
                $img = $files['customization_image'];
                $newName = $img->getRandomName();
                // Ensure directory exists
                if (!is_dir(FCPATH . 'uploads/customization')) {
                    mkdir(FCPATH . 'uploads/customization', 0777, true);
                }
                if ($img->move(FCPATH . 'uploads/customization', $newName)) {
                    $customizationImage = 'uploads/customization/' . $newName;
                }
            }
        }

        $success = $this->cartLib->add((int)$productId, $qty, $deliveryDate, $customizationText, $customizationImage);

        if ($this->request->isAJAX()) {
            if ($success) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => esc($product['name']) . ' added to cart successfully!',
                    'cart_count' => $this->cartLib->totalItems()
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add item to cart.'
                ]);
            }
        }

        if ($this->request->getPost('buy_now') === '1') {
            if ($success) {
                return redirect()->to(base_url('checkout'));
            }
        }

        if ($success) {
            $this->session->setFlashdata('success', esc($product['name']) . ' added to cart successfully!');
        } else {
            $this->session->setFlashdata('error', 'Failed to add item to cart.');
        }

        return redirect()->to(base_url('cart'));
    }

    /**
     * Update quantity of a cart item.
     */
    public function update()
    {
        $id = $this->request->getPost('id');
        $qty = (int)$this->request->getPost('qty');

        if ($qty > 0) {
            $this->cartLib->update($id, $qty);
            $this->session->setFlashdata('success', 'Cart updated successfully.');
        } else {
            $this->cartLib->remove($id);
            $this->session->setFlashdata('success', 'Item removed from cart.');
        }

        return redirect()->to(base_url('cart'));
    }

    /**
     * Remove item from cart.
     */
    public function remove($id = null)
    {
        if ($id !== null) {
            $this->cartLib->remove($id);
            $this->session->setFlashdata('success', 'Item removed from cart.');
        }
        return redirect()->to(base_url('cart'));
    }

    /**
     * Clear all items from cart.
     */
    public function clear()
    {
        $this->cartLib->destroy();
        $this->session->setFlashdata('success', 'Cart cleared successfully.');
        return redirect()->to(base_url('cart'));
    }

    /**
     * Apply Coupon Code.
     */
    public function apply_coupon()
    {
        $code = trim($this->request->getPost('coupon_code') ?? '');
        if (empty($code)) {
            $this->session->setFlashdata('error', 'Please enter a coupon code.');
            return redirect()->to(base_url('cart'));
        }

        $res = $this->cartLib->applyCoupon($code);
        if ($res['success']) {
            $this->session->setFlashdata('success', $res['message']);
        } else {
            $this->session->setFlashdata('error', $res['message']);
        }

        return redirect()->to(base_url('cart'));
    }

    /**
     * Remove Coupon Code.
     */
    public function remove_coupon()
    {
        $this->cartLib->removeCoupon();
        $this->session->setFlashdata('success', 'Coupon removed successfully.');
        return redirect()->to(base_url('cart'));
    }
}
