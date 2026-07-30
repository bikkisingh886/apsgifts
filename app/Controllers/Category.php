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
    public function index(...$slugSegments)
    {
        if (empty($slugSegments)) {
            return redirect()->to(base_url());
        }

        // Redirect category/ prefixed URLs to clean direct slug URLs
        $uri = service('uri');
        if ($uri->getTotalSegments() > 0 && $uri->getSegment(1) === 'category') {
            $segments = $uri->getSegments();
            array_shift($segments);
            return redirect()->to(base_url(implode('/', $segments)), 301);
        }

        $slug = implode('/', $slugSegments);
        $slugParts = explode('/', trim($slug, '/'));
        if (count($slugParts) > 5) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Invalid path depth.");
        }
        $lastSlug = end($slugParts);

        $selectedCity = null;
        $previousCategoryId = null;
        $category = null;
        
        foreach ($slugParts as $index => $partSlug) {
            // Find active category with slug and parent ID
            $query = $this->categoryModel->where('slug', $partSlug)->where('is_active', 1);
            if ($index === 0) {
                $query->groupStart()
                      ->where('parent_id', null)
                      ->orWhere('parent_id', 0)
                      ->groupEnd();
            } else {
                $query->where('parent_id', $previousCategoryId);
            }
            
            $cat = $query->first();
            if (!$cat) {
                // If this is the last segment, check if it's an active city slug (fallback dynamic city logic)
                if ($index === count($slugParts) - 1 && $index > 0) {
                    $cityModel = new \App\Models\CityModel();
                    $city = $cityModel->where('slug', $partSlug)->where('is_active', 1)->first();
                    if ($city) {
                        // Set the city in the session & cookies to synchronize user context
                        session()->set('selected_city_id', (int)$city['id']);
                        session()->set('selected_city_name', $city['name']);
                        session()->set('selected_city_slug', $city['slug']);
                        
                        set_cookie('selected_city_id', $city['id'], 2592000);
                        set_cookie('selected_city_name', $city['name'], 2592000);
                        set_cookie('selected_city_slug', $city['slug'], 2592000);

                        $selectedCity = $city;
                        break; // Stop resolution: category is the one resolved in the previous loop iteration
                    }
                }

                // If not resolved as category or city, throw 404
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Category path not found.");
            }

            $previousCategoryId = (int)$cat['id'];
            $category = $cat;
        }

        $search = $this->request->getGet('search') ?? '';
        $min_price = $this->request->getGet('min_price') ?? '';
        $max_price = $this->request->getGet('max_price') ?? '';
        $sort = $this->request->getGet('sort') ?? '';
        $colors = $this->request->getGet('colors') ?? [];
        if (!is_array($colors)) {
            $colors = [$colors];
        }

        // Get recursive list of descendant category IDs (self + child + grandchild)
        $categoryIds = $this->categoryModel->getDescendantIds((int)$category['id']);
        $result = $this->productModel->getCategoryProductsPaginated($categoryIds, $search, $min_price, $max_price, 20, $sort, $colors);

        if ($selectedCity) {
            $category['name'] = $category['name'] . ' to ' . $selectedCity['name'];
        }

        $data['category'] = $category;
        $data['products'] = $result['products'];
        $data['pager'] = $result['pager'];
        
        $data['search'] = $search;
        $data['min_price'] = $min_price;
        $data['max_price'] = $max_price;
        $data['sort'] = $sort;
        $data['selected_colors'] = $colors;

        if ($this->request->isAJAX()) {
            $html = '';
            if (!empty($data['products'])) {
                foreach ($data['products'] as $product) {
                    $html .= view('frontend/sections/_product_card_col', ['product' => $product]);
                }
            }
            $currentPage = $data['pager']->getCurrentPage();
            $pageCount = $data['pager']->getPageCount();
            return $this->response->setJSON([
                'success' => true,
                'html' => $html,
                'has_more' => $currentPage < $pageCount,
                'current_page' => $currentPage,
                'page_count' => $pageCount,
                'count' => count($data['products'])
            ]);
        }
        
        if ($selectedCity) {
            $data['meta_title'] = "Send " . $category['name'] . " Online | Same Day Delivery - GiftShop";
            $data['meta_desc'] = "Buy and send " . strtolower($category['name']) . " online with guaranteed same-day delivery. Choose from premium options.";
        } else {
            $data['meta_title'] = $category['meta_title'] ?: $category['name'] . ' | GiftShop';
            $data['meta_desc'] = $category['meta_desc'] ?: $category['summary'];
        }

        $data['twitter_card'] = $category['twitter_card'] ?: 'summary_large_image';
        $data['twitter_title'] = $selectedCity ? $data['meta_title'] : ($category['twitter_title'] ?: $data['meta_title']);
        $data['twitter_desc'] = $selectedCity ? $data['meta_desc'] : ($category['twitter_desc'] ?: $data['meta_desc']);
        $data['twitter_image'] = $category['twitter_image'] ?: ($category['image_path'] ?? '');
        $data['og_title'] = $selectedCity ? $data['meta_title'] : ($category['og_title'] ?: $data['meta_title']);
        $data['og_desc'] = $selectedCity ? $data['meta_desc'] : ($category['og_desc'] ?: $data['meta_desc']);
        $data['og_image'] = $category['og_image'] ?: ($category['image_path'] ?? '');
        $data['og_type'] = $category['og_type'] ?: 'website';
        $data['schema_markup'] = $category['schema_markup'] ?: '';

        return view('frontend/category_products', $data);
    }
}
