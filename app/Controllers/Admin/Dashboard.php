<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\OfferModel;
use App\Models\OrderModel;

class Dashboard extends BaseController
{
    protected $categoryModel;
    protected $productModel;
    protected $offerModel;
    protected $orderModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
        $this->offerModel = new OfferModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Administration Main Dashboard.
     */
    public function index()
    {
        $data['categories_count'] = $this->categoryModel->countAllResults();
        $data['products_count'] = $this->productModel->where('is_active', 1)->countAllResults();
        $data['offers_count'] = $this->offerModel->where('is_active', 1)->countAllResults();
        $data['orders_count'] = $this->orderModel->countAllResults();

        // Get 10 recent orders
        $data['recent_orders'] = $this->orderModel->orderBy('created_at', 'DESC')->findAll(10);
        $data['title'] = 'Overview Dashboard';

        return view('admin/dashboard', $data);
    }
}
