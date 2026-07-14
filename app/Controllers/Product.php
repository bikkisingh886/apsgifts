<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Product extends BaseController
{
    protected $productModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->productModel = new ProductModel();
    }

    /**
     * Product details page.
     */
    public function index($slug = null)
    {
        if (empty($slug)) {
            return redirect()->to(base_url());
        }

        $product = $this->productModel->getBySlug($slug);
        if (!$product || !$product['is_active']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Product not found.");
        }

        $data['product'] = $product;
        $data['is_product_detail_page'] = true;

        if ($product['delivery_type'] === 'Express') {
            $data['delivery_dates'] = get_express_dates();
        } else {
            $data['courier_eta'] = calculate_courier_eta();
        }

        $data['meta_title'] = $product['meta_title'] ?: $product['name'] . ' | GiftShop';
        $data['meta_desc'] = $product['meta_desc'] ?: substr(strip_tags($product['description']), 0, 160);
        $data['twitter_card'] = $product['twitter_card'] ?: 'summary_large_image';
        $data['twitter_title'] = $product['twitter_title'] ?: $data['meta_title'];
        $data['twitter_desc'] = $product['twitter_desc'] ?: $data['meta_desc'];
        
        $primaryImg = '';
        if (!empty($product['images'])) {
            foreach ($product['images'] as $img) {
                if (isset($img['is_primary']) && $img['is_primary'] == 1) {
                    $primaryImg = $img['image_path'];
                    break;
                }
            }
            if (empty($primaryImg) && isset($product['images'][0])) {
                $primaryImg = $product['images'][0]['image_path'];
            }
        }
        
        $data['twitter_image'] = $product['twitter_image'] ?: $primaryImg;
        $data['og_title'] = $product['og_title'] ?: $data['meta_title'];
        $data['og_desc'] = $product['og_desc'] ?: $data['meta_desc'];
        $data['og_image'] = $product['og_image'] ?: $primaryImg;
        $data['og_type'] = $product['og_type'] ?: 'product';
        $data['schema_markup'] = $product['schema_markup'] ?: '';

        $categoryIds = array_column($product['categories'], 'id');
        $data['related_products'] = $this->productModel->getRelatedProducts((int)$product['id'], $categoryIds, 10);

        $db = \Config\Database::connect();
        $reviews = $db->table('product_reviews')
                      ->select('product_reviews.*, COALESCE(users.name, product_reviews.customer_name) as name, users.profile_photo')
                      ->join('users', 'users.id = product_reviews.user_id', 'left')
                      ->where('product_reviews.product_id', $product['id'])
                      ->where('product_reviews.status', 'approved')
                      ->orderBy('product_reviews.created_at', 'DESC')
                      ->get()->getResultArray();
                      
        $ratingCounts = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
        $totalRating = 0;
        foreach($reviews as $r) {
            if (isset($ratingCounts[(int)$r['rating']])) {
                $ratingCounts[(int)$r['rating']]++;
                $totalRating += (int)$r['rating'];
            }
        }
        $avgRating = count($reviews) > 0 ? round($totalRating / count($reviews), 1) : 0;

        $data['reviews'] = $reviews;
        $data['ratingCounts'] = $ratingCounts;
        $data['avgRating'] = $avgRating;
        $data['totalReviews'] = count($reviews);

        return view('frontend/product_detail', $data);
    }

    /**
     * AJAX quick view of product details.
     */
    public function quickview($id = null)
    {
        if (empty($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product ID is missing']);
        }

        $product = $this->productModel->getById((int)$id);
        if (!$product || !$product['is_active']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product not found']);
        }

        // Calculate dynamic delivery date
        $delivery_date = '';
        if ($product['delivery_type'] === 'Express') {
            helper('delivery');
            $dates = get_express_dates();
            $delivery_date = !empty($dates) ? $dates[0]['value'] : date('Y-m-d');
        } else {
            $delivery_date = date('Y-m-d', strtotime('+7 days'));
        }

        return $this->response->setJSON([
            'success' => true,
            'product' => $product,
            'delivery_date' => $delivery_date,
            'base_url' => base_url()
        ]);
    }
}
