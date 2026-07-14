<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Place order in database (Header + Items).
     * Uses transactions for safety.
     *
     * @param array $order_data
     * @param array $items
     * @return string|bool Order Number on success, FALSE on failure
     */
    public function place_order($order_data, $items) {
        $this->db->trans_start();

        // 1. Generate Unique Order Number
        $order_number = 'GS' . strtoupper(substr(uniqid(), -6)) . rand(10, 99);
        $order_data['order_number'] = $order_number;

        // 2. Insert Order Header
        $this->db->insert('orders', $order_data);
        $order_id = $this->db->insert_id();

        // 3. Insert Order Items
        foreach ($items as $item) {
            $item_data = array(
                'order_id' => $order_id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'delivery_type' => $item['delivery_type'],
                'delivery_date' => isset($item['delivery_date']) ? $item['delivery_date'] : NULL,
                'qty' => $item['qty'],
                'unit_price' => $item['price']
            );
            $this->db->insert('order_items', $item_data);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() ? $order_number : FALSE;
    }

    /**
     * Get orders for a specific user.
     *
     * @param int $user_id
     * @return array
     */
    public function get_user_orders($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('orders');
        $orders = $query->result_array();

        // Attach items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->get_order_items($order['id']);
        }

        return $orders;
    }

    /**
     * Get all orders for Admin panel.
     *
     * @return array
     */
    public function get_all_orders() {
        $this->db->select('o.*, u.name as customer_name');
        $this->db->from('orders o');
        $this->db->join('users u', 'u.id = o.user_id', 'left');
        $this->db->order_by('o.id', 'DESC');
        $query = $this->db->get();
        $orders = $query->result_array();

        // Attach items to each order
        foreach ($orders as &$order) {
            $order['items'] = $this->get_order_items($order['id']);
        }

        return $orders;
    }

    /**
     * Get details of a single order by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function get_by_id($id) {
        $this->db->select('o.*, u.name as customer_name, u.email as customer_email, u.mobile as customer_mobile');
        $this->db->from('orders o');
        $this->db->join('users u', 'u.id = o.user_id', 'left');
        $this->db->where('o.id', $id);
        $query = $this->db->get();
        $order = $query->row_array();

        if ($order) {
            $order['items'] = $this->get_order_items($id);
        }

        return $order;
    }

    /**
     * Get details of a single order by Order Number.
     *
     * @param string $order_number
     * @return array|null
     */
    public function get_by_number($order_number) {
        $this->db->select('o.*, u.name as customer_name, u.email as customer_email, u.mobile as customer_mobile');
        $this->db->from('orders o');
        $this->db->join('users u', 'u.id = o.user_id', 'left');
        $this->db->where('o.order_number', $order_number);
        $query = $this->db->get();
        $order = $query->row_array();

        if ($order) {
            $order['items'] = $this->get_order_items($order['id']);
        }

        return $order;
    }

    /**
     * Get items of an order.
     *
     * @param int $order_id
     * @return array
     */
    public function get_order_items($order_id) {
        $this->db->select('oi.*, pi.image_path');
        $this->db->from('order_items oi');
        $this->db->join('product_images pi', 'pi.product_id = oi.product_id AND pi.is_primary = 1', 'left');
        $this->db->where('oi.order_id', $order_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Update order status.
     *
     * @param int $order_id
     * @param string $status
     * @return bool
     */
    public function update_status($order_id, $status) {
        $this->db->where('id', $order_id);
        return $this->db->update('orders', array('status' => $status));
    }

    /**
     * Add tracking details to order.
     *
     * @param int $order_id
     * @param string $tracking_url
     * @param string $tracking_code
     * @return bool
     */
    public function add_tracking($order_id, $tracking_url, $tracking_code) {
        $this->db->where('id', $order_id);
        return $this->db->update('orders', array(
            'tracking_url' => $tracking_url,
            'tracking_code' => $tracking_code,
            'status' => 'Shipped' // Automatically update status to Shipped when tracking is added
        ));
    }

    /**
     * Get statistics for the Admin Dashboard.
     *
     * @return array
     */
    public function get_dashboard_stats() {
        // 1. Total Orders
        $total_orders = $this->db->count_all('orders');

        // 2. Products Live
        $this->db->where('is_active', 1);
        $products_live = $this->db->count_all_results('products');

        // 3. Pending Orders (status = 'Processing')
        $this->db->where('status', 'Processing');
        $pending_orders = $this->db->count_all_results('orders');

        // 4. Revenue MTD (Month to date)
        $first_day_of_month = date('Y-m-01 00:00:00');
        $this->db->select_sum('total');
        $this->db->where('created_at >=', $first_day_of_month);
        $this->db->where('status !=', 'Cancelled');
        $revenue_query = $this->db->get('orders');
        $revenue_res = $revenue_query->row_array();
        $revenue_mtd = isset($revenue_res['total']) ? (float)$revenue_res['total'] : 0.00;

        return array(
            'total_orders' => $total_orders,
            'products_live' => $products_live,
            'pending_orders' => $pending_orders,
            'revenue_mtd' => $revenue_mtd
        );
    }
}
