<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Category extends BaseController
{
    protected $categoryModel;
    protected $productModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Category listing.
     */
    public function index($slug = null)
    {
        if (empty($slug)) {
            return redirect()->to(base_url());
        }

        // Redirect category/ prefixed URLs to clean direct slug URLs
        $uri = service('uri');
        if ($uri->getTotalSegments() > 0 && $uri->getSegment(1) === 'category') {
            $segments = $uri->getSegments();
            array_shift($segments);
            return redirect()->to(base_url(implode('/', $segments)), 301);
        }

        $slugParts = explode('/', trim($slug, '/'));
        if (count($slugParts) > 5) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Invalid path depth.");
        }
        $lastSlug = end($slugParts);

        $category = $this->categoryModel->getBySlug($lastSlug);
        if (!$category || !$category['is_active']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Category not found.");
        }

        $search = $this->request->getGet('search') ?? '';
        $min_price = $this->request->getGet('min_price') ?? '';
        $max_price = $this->request->getGet('max_price') ?? '';
        $sort = $this->request->getGet('sort') ?? '';
        $colors = $this->request->getGet('colors') ?? [];
        if (!is_array($colors)) {
            $colors = [$colors];
        }

        $result = $this->productModel->getCategoryProductsPaginated($lastSlug, $search, $min_price, $max_price, 40, $sort, $colors);

        $data['category'] = $category;
        $data['products'] = $result['products'];
        $data['pager'] = $result['pager'];
        
        $data['search'] = $search;
        $data['min_price'] = $min_price;
        $data['max_price'] = $max_price;
        $data['sort'] = $sort;
        $data['selected_colors'] = $colors;
        
        $data['meta_title'] = $category['meta_title'] ?: $category['name'] . ' | GiftShop';
        $data['meta_desc'] = $category['meta_desc'] ?: $category['summary'];
        $data['twitter_card'] = $category['twitter_card'] ?: 'summary_large_image';
        $data['twitter_title'] = $category['twitter_title'] ?: $data['meta_title'];
        $data['twitter_desc'] = $category['twitter_desc'] ?: $data['meta_desc'];
        $data['twitter_image'] = $category['twitter_image'] ?: ($category['image_path'] ?? '');
        $data['og_title'] = $category['og_title'] ?: $data['meta_title'];
        $data['og_desc'] = $category['og_desc'] ?: $data['meta_desc'];
        $data['og_image'] = $category['og_image'] ?: ($category['image_path'] ?? '');
        $data['og_type'] = $category['og_type'] ?: 'website';
        $data['schema_markup'] = $category['schema_markup'] ?: '';

        return view('frontend/category_products', $data);
    }
}
