<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'order_number', 'user_id', 'status', 'subtotal', 'discount', 
        'coupon_code', 'coupon_discount', 'total', 'delivery_date', 
        'tracking_url', 'tracking_code', 'address_json', 'created_at'
    ];

    protected $useTimestamps = false;

    /**
     * Place order in database (Header + Items).
     */
    public function placeOrder(array $order_data, array $items)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Temporary placeholder order number
        $order_data['order_number'] = 'TEMP_' . time() . '_' . rand(100, 999);

        // 2. Insert Order Header
        $db->table('orders')->insert($order_data);
        $order_id = $db->insertID();

        // 3. Format Exact Order Number (APS00001, APS00002, ..., APS99999, APS100000)
        $order_number = sprintf('APS%05d', $order_id);
        $db->table('orders')->where('id', $order_id)->update(['order_number' => $order_number]);

        // 4. Insert Order Items
        foreach ($items as $item) {
            $custData = [];
            if (!empty($item['customization_text'])) {
                $custData['text'] = $item['customization_text'];
            }
            if (!empty($item['customization_image'])) {
                $custData['image'] = $item['customization_image'];
            }

            $item_data = [
                'order_id'           => $order_id,
                'product_id'         => $item['product_id'] ?? ($item['id'] ?: null),
                'product_name'       => $item['name'],
                'color'              => $item['color'] ?? null,
                'delivery_type'      => $item['delivery_type'],
                'delivery_date'      => $item['delivery_date'] ?: null,
                'qty'                => $item['qty'],
                'unit_price'         => $item['price'],
                'customization_data' => !empty($custData) ? json_encode($custData) : null
            ];
            $db->table('order_items')->insert($item_data);
        }

        $db->transComplete();

        return $db->transStatus() ? $order_number : false;
    }

    /**
     * Get orders for a specific user.
     */
    public function getUserOrders(int $user_id)
    {
        $orders = $this->where('user_id', $user_id)->orderBy('id', 'DESC')->findAll();

        // Attach items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems((int)$order['id']);
        }

        return $orders;
    }

    /**
     * Get all orders for Admin panel.
     */
    public function getAllOrders()
    {
        $db = \Config\Database::connect();
        $orders = $db->table('orders o')
            ->select('o.*, u.name as customer_name')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->orderBy('o.id', 'DESC')
            ->get()
            ->getResultArray();

        // Attach items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems((int)$order['id']);
        }

        return $orders;
    }

    /**
     * Get details of a single order by ID.
     */
    public function getById(int $id)
    {
        $db = \Config\Database::connect();
        $order = $db->table('orders o')
            ->select('o.*, u.name as customer_name, u.email as customer_email, u.mobile as customer_mobile')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->where('o.id', $id)
            ->get()
            ->getRowArray();

        if ($order) {
            $order['items'] = $this->getOrderItems($id);
        }

        return $order;
    }

    /**
     * Get details of a single order by Order Number.
     */
    public function getByNumber(string $order_number)
    {
        $db = \Config\Database::connect();
        $order = $db->table('orders o')
            ->select('o.*, u.name as customer_name, u.email as customer_email, u.mobile as customer_mobile')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->where('o.order_number', $order_number)
            ->get()
            ->getRowArray();

        if ($order) {
            $order['items'] = $this->getOrderItems((int)$order['id']);
        }

        return $order;
    }

    /**
     * Get items of an order.
     */
    public function getOrderItems(int $order_id)
    {
        $db = \Config\Database::connect();
        return $db->table('order_items oi')
            ->select('oi.*, oi.unit_price as price, pi.image_path, p.sku, p.is_customizable, p.customization_type')
            ->join('products p', 'p.id = oi.product_id', 'left')
            ->join('product_images pi', 'pi.product_id = oi.product_id AND pi.is_primary = 1', 'left')
            ->where('oi.order_id', $order_id)
            ->get()
            ->getResultArray();
    }



    /**
     * Get statistics for the Admin Dashboard.
     */
    public function getDashboardStats()
    {
        $db = \Config\Database::connect();

        // 1. Total Orders
        $total_orders = $db->table('orders')->countAllResults();

        // 2. Products Live
        $products_live = $db->table('products')->where('is_active', 1)->countAllResults();

        // 3. Pending Orders (status = 'Processing')
        $pending_orders = $db->table('orders')->where('status', 'Processing')->countAllResults();

        // 4. Revenue MTD (Month to date)
        $first_day_of_month = date('Y-m-01 00:00:00');
        $revenue_res = $db->table('orders')
            ->selectSum('total')
            ->where('created_at >=', $first_day_of_month)
            ->where('status !=', 'Cancelled')
            ->get()
            ->getRowArray();
        $revenue_mtd = isset($revenue_res['total']) ? (float)$revenue_res['total'] : 0.00;

        return [
            'total_orders'   => $total_orders,
            'products_live'  => $products_live,
            'pending_orders' => $pending_orders,
            'revenue_mtd'    => $revenue_mtd
        ];
    }
}
