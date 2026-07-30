<?php

namespace App\Libraries;

use App\Models\ProductModel;

class CartLib
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    /**
     * Add product to cart.
     */
    public function add(int $productId, int $qty, string $deliveryDate = null, string $customizationText = null, string $customizationImage = null, string $color = null): bool
    {
        if ($qty < 1) {
            return false;
        }

        $productModel = new ProductModel();
        $product = $productModel->getById($productId);
        if (!$product || !$product['is_active']) {
            return false;
        }

        // Fetch full product details (including offer discount if any)
        $fullProduct = $productModel->getBySlug($product['slug']);
        $price = (float)$fullProduct['price'];
        if ($fullProduct['offer_value'] > 0) {
            $price = $fullProduct['offer_type'] === 'percent'
                ? $price * (1 - $fullProduct['offer_value'] / 100)
                : $price - $fullProduct['offer_value'];
        }

        helper('delivery');
        if (empty($deliveryDate)) {
            if ($product['delivery_type'] === 'Courier') {
                $deliveryDate = date('Y-m-d', strtotime('+7 weekdays'));
            } else {
                $expressDates = get_express_dates();
                $deliveryDate = !empty($expressDates) ? $expressDates[0]['value'] : date('Y-m-d');
            }
        }

        $cart = $this->session->get('cart') ?: [];

        $itemKey = (string)$productId;
        if (!empty($color)) {
            $itemKey .= '_' . strtolower(str_replace(' ', '-', $color));
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['qty'] += $qty;
            $cart[$itemKey]['delivery_date'] = $deliveryDate;
            if ($customizationText) $cart[$itemKey]['customization_text'] = $customizationText;
            if ($customizationImage) $cart[$itemKey]['customization_image'] = $customizationImage;
            if ($color) $cart[$itemKey]['color'] = $color;
        } else {
            $cart[$itemKey] = [
                'id'                  => $itemKey,
                'product_id'          => $productId,
                'name'                => $product['name'],
                'sku'                 => $product['sku'],
                'price'               => $price,
                'qty'                 => $qty,
                'image'               => $product['image_path'],
                'delivery_type'       => $product['delivery_type'],
                'delivery_date'       => $deliveryDate,
                'customization_text'  => $customizationText,
                'customization_image' => $customizationImage,
                'is_customizable'     => $product['is_customizable'] ?? 0,
                'customization_type'  => $product['customization_type'] ?? null,
                'color'               => $color
            ];
        }

        $this->session->set('cart', $cart);
        return true;
    }

    /**
     * Update cart quantities.
     */
    public function update(string $id, int $qty): bool
    {
        $cart = $this->session->get('cart') ?: [];

        if (isset($cart[$id])) {
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['qty'] = $qty;
            }
            $this->session->set('cart', $cart);
            return true;
        }

        return false;
    }

    /**
     * Remove item from cart.
     */
    public function remove(string $id): bool
    {
        $cart = $this->session->get('cart') ?: [];

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->session->set('cart', $cart);
            return true;
        }

        return false;
    }

    /**
     * Get cart contents.
     */
    public function contents(): array
    {
        return $this->session->get('cart') ?: [];
    }

    /**
     * Get subtotal.
     */
    public function subtotal(): float
    {
        $subtotal = 0.00;
        $cart = $this->contents();
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
        return $subtotal;
    }

    /**
     * Get global additional discount.
     */
    public function getGlobalDiscount(): float
    {
        $active = get_setting('global_discount_active') ?? '0';
        if ($active != '1') {
            return 0.00;
        }
        
        $threshold = (float)(get_setting('global_discount_threshold') ?? 1000);
        $subtotal = $this->subtotal();
        if ($subtotal < $threshold) {
            return 0.00;
        }
        
        $value = (float)(get_setting('global_discount_value') ?? 0);
        $type = get_setting('global_discount_type') ?? 'percentage';
        
        if ($type === 'percentage') {
            return round($subtotal * ($value / 100), 2);
        } else {
            return round($value, 2);
        }
    }

    /**
     * Get applied coupon discount.
     */
    public function getCouponDiscount(): float
    {
        $coupon = $this->session->get('applied_coupon');
        if (empty($coupon)) {
            return 0.00;
        }
        
        $subtotal = $this->subtotal();
        if ($subtotal < (float)$coupon['min_cart_amount']) {
            return 0.00;
        }
        
        if ($coupon['discount_type'] === 'percentage') {
            return round($subtotal * ((float)$coupon['discount_value'] / 100), 2);
        } else {
            return round((float)$coupon['discount_value'], 2);
        }
    }

    /**
     * Apply a coupon.
     */
    public function applyCoupon(string $code): array
    {
        $db = \Config\Database::connect();
        $coupon = $db->table('coupons')
                     ->where('code', $code)
                     ->where('is_active', 1)
                     ->get()->getRowArray();
        
        if (empty($coupon)) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code.'];
        }
        
        $subtotal = $this->subtotal();
        if ($subtotal < (float)$coupon['min_cart_amount']) {
            return [
                'success' => false, 
                'message' => 'Minimum order amount of ₹' . number_format($coupon['min_cart_amount'], 2) . ' is required to use this coupon.'
            ];
        }
        
        $this->session->set('applied_coupon', $coupon);
        return ['success' => true, 'message' => 'Coupon code applied successfully!'];
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon(): void
    {
        $this->session->remove('applied_coupon');
    }

    /**
     * Get total discount.
     */
    public function discount(): float
    {
        $totalDiscount = $this->getGlobalDiscount() + $this->getCouponDiscount();
        $subtotal = $this->subtotal();
        return $totalDiscount > $subtotal ? $subtotal : $totalDiscount;
    }

    /**
     * Get total.
     */
    public function total(): float
    {
        return $this->subtotal() - $this->discount();
    }

    /**
     * Get total items.
     */
    public function totalItems(): int
    {
        $count = 0;
        $cart = $this->contents();
        foreach ($cart as $item) {
            $count += $item['qty'];
        }
        return $count;
    }

    /**
     * Clear cart.
     */
    public function destroy()
    {
        $this->session->remove('cart');
        $this->session->remove('applied_coupon');
    }
}
