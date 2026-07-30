<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Home extends BaseController
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
     * Homepage.
     */
    public function index()
    {
        $data['categories'] = $this->categoryModel->getWithProductCounts(true);
        
        $homepageSectionModel = new \App\Models\HomepageSectionModel();
        $data['sections'] = $homepageSectionModel->getActiveSections();

        $data['meta_title'] = 'GiftShop - Send Cakes, Flowers & Gifts Online';
        $data['meta_desc'] = 'Complete gift e-commerce shop with same-day express delivery and courier options across Patna, Bihar.';
        $this->loadPageSeo('home', $data);

        return view('frontend/home', $data);
    }

    /**
     * Shop listing page.
     */
    public function shop()
    {
        $search = $this->request->getGet('search') ?? '';
        $min_price = $this->request->getGet('min_price') ?? '';
        $max_price = $this->request->getGet('max_price') ?? '';
        $sort = $this->request->getGet('sort') ?? '';
        $colors = $this->request->getGet('colors') ?? [];
        if (!is_array($colors)) {
            $colors = [$colors];
        }

        $result = $this->productModel->getShopProductsPaginated($search, $min_price, $max_price, 20, $sort, $colors);

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
        
        $data['meta_title'] = 'All Products | GiftShop';
        $data['meta_desc'] = 'Browse all our premium cakes, flowers, combos, and custom gifts online.';
        $this->loadPageSeo('shop', $data);

        return view('frontend/shop_grid', $data);
    }

    /**
     * 404 Not Found Page.
     */
    public function notFound()
    {
        $this->response->setStatusCode(404);

        $data['categories'] = $this->categoryModel->getWithProductCounts(true);
        $data['meta_title'] = '404 - Page Not Found | GiftShop';
        $data['meta_desc'] = 'The page you were looking for could not be found.';

        return view('frontend/404', $data);
    }

    /**
     * Search results.
     */
    public function search()
    {
        $keyword = $this->request->getGet('q') ?? '';
        
        if (empty($keyword)) {
            return redirect()->to(base_url());
        }

        $data['keyword'] = $keyword;
        $data['products'] = $this->productModel->searchProducts($keyword);
        $data['meta_title'] = 'Search results for "' . esc($keyword) . '" | GiftShop';
        $data['meta_desc'] = 'Search results for ' . esc($keyword);

        return view('frontend/search', $data);
    }

    /**
     * AJAX Live Search Suggestions.
     */
    public function suggestions()
    {
        $keyword = $this->request->getGet('q') ?? '';
        if (strlen($keyword) < 2) {
            return $this->response->setJSON([
                'products' => [],
                'suggestions' => [],
                'collections' => []
            ]);
        }

        // 1. Fetch matching active products (limit 8)
        $db = \Config\Database::connect();
        $cityId = session('selected_city_id');
        
        $prodBuilder = $db->table('products p')
            ->select('p.id, p.name, p.sku, p.slug, pi.image_path')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('p.is_active', 1)
            ->where('p.hide_from_frontend', 0)
            ->groupStart()
                ->like('p.name', $keyword)
                ->orLike('p.description', $keyword)
            ->groupEnd()
            ->limit(8);

        if ($cityId) {
            $prodBuilder->select('COALESCE(pc.price_override, p.price) as price')
                ->join('product_cities pc', 'pc.product_id = p.id AND pc.city_id = ' . (int)$cityId, 'left');
        } else {
            $prodBuilder->select('p.price');
        }

        $products = $prodBuilder->get()->getResultArray();

        // 2. Collections (matching categories - limit 8)
        $categories = $db->table('categories c')
            ->select('c.id, c.name, c.slug, c.parent_id, p.slug as parent_slug')
            ->join('categories p', 'p.id = c.parent_id', 'left')
            ->where('c.is_active', 1)
            ->like('c.name', $keyword)
            ->limit(8)
            ->get()
            ->getResultArray();

        $collections = [];
        foreach ($categories as $cat) {
            $collections[] = [
                'name' => $cat['name'],
                'url'  => get_category_url($cat)
            ];
        }


        // 3. Search Suggestions strings (limit 8)
        $suggestionsList = [];
        foreach ($categories as $cat) {
            $suggestionsList[] = $cat['name'];
        }
        foreach ($products as $prod) {
            $suggestionsList[] = $prod['name'];
        }
        $suggestionsList = array_unique($suggestionsList);
        $suggestionsList = array_slice($suggestionsList, 0, 8);
        
        $suggestions = [];
        foreach ($suggestionsList as $sug) {
            $suggestions[] = [
                'text' => $sug,
                'url'  => base_url('search?q=' . urlencode($sug))
            ];
        }

        // Standardize product array
        $formattedProducts = [];
        foreach ($products as $p) {
            $formattedProducts[] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'url' => base_url('product/' . $p['slug']),
                'price' => number_format($p['price'], 0),
                'image' => $p['image_path'] ? base_url($p['image_path']) : base_url('assets/img/product/default.png')
            ];
        }

        return $this->response->setJSON([
            'products' => $formattedProducts,
            'suggestions' => $suggestions,
            'collections' => $collections
        ]);
    }

    /**
     * Static About Us Page.
     */
    public function about()
    {
        $data['meta_title'] = 'About Us | GiftShop';
        $data['meta_desc'] = 'Learn more about GiftShop, our journey, and same day delivery services.';
        $this->loadPageSeo('about', $data);
        return view('frontend/about', $data);
    }

    /**
     * Static FAQs Page.
     */
    public function faq()
    {
        $data['meta_title'] = 'Frequently Asked Questions | APSgifts';
        $data['meta_desc'] = 'Check the FAQs regarding order delivery times, express shipping, and payment options.';
        $this->loadPageSeo('faq', $data);

        $faqModel = new \App\Models\FaqModel();
        $selectedCat = $this->request->getGet('category');
        $data['faqs'] = $faqModel->getActiveFaqs($selectedCat);
        $data['categories'] = $faqModel->getCategories();
        $data['selectedCategory'] = $selectedCat ?: 'all';

        return view('frontend/faq', $data);
    }

    /**
     * Static Terms of Service.
     */
    public function terms()
    {
        $data['meta_title'] = 'Terms of Service | APSgifts';
        $data['meta_desc'] = 'Terms of service and rules for using the APSgifts platform.';
        $this->loadPageSeo('terms', $data);
        return view('frontend/terms', $data);
    }

    /**
     * Static Privacy Policy.
     */
    public function privacy()
    {
        $data['meta_title'] = 'Privacy Policy | APSgifts';
        $data['meta_desc'] = 'Privacy policy and user data safety details.';
        $this->loadPageSeo('privacy', $data);
        return view('frontend/privacy', $data);
    }

    /**
     * Static Contact Page.
     */
    public function contact()
    {
        $data['meta_title'] = 'Contact Us | APSgifts';
        $data['meta_desc'] = 'Get in touch with APSgifts customer support for orders, delivery, and questions.';
        $this->loadPageSeo('contact', $data);
        return view('frontend/contact', $data);
    }

    /**
     * Submit Contact / Enquiry Form with Validation
     */
    public function submitContact()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'name'    => [
                'rules'  => 'required|min_length[2]|max_length[100]',
                'errors' => ['required' => 'Please enter your full name.']
            ],
            'email'   => [
                'rules'  => 'required|valid_email|max_length[150]',
                'errors' => [
                    'required'    => 'Please enter your email address.',
                    'valid_email' => 'Please enter a valid email address.'
                ]
            ],
            'phone'   => [
                'rules'  => 'permit_empty|min_length[7]|max_length[20]',
                'errors' => ['min_length' => 'Please enter a valid phone number.']
            ],
            'subject' => [
                'rules'  => 'required|min_length[3]|max_length[200]',
                'errors' => ['required' => 'Please enter a subject.']
            ],
            'message' => [
                'rules'  => 'required|min_length[5]|max_length[2000]',
                'errors' => [
                    'required'   => 'Please write your message.',
                    'min_length' => 'Message must be at least 5 characters long.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors'  => $validation->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $enquiryModel = new \App\Models\EnquiryModel();
        $enquiryModel->insert([
            'name'       => trim($this->request->getPost('name')),
            'email'      => trim($this->request->getPost('email')),
            'phone'      => trim($this->request->getPost('phone')),
            'subject'    => trim($this->request->getPost('subject')),
            'message'    => trim($this->request->getPost('message')),
            'status'     => 'unread',
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Thank you! Your message has been received successfully. Our support team will get back to you shortly.'
            ]);
        }

        return redirect()->to(base_url('contact-us'))->with('success_message', 'Thank you! Your enquiry has been sent successfully.');
    }

    /**
     * Cancellation & Refund Policy Page.
     */
    public function cancellation()
    {
        $data['meta_title'] = 'Cancellation & Refund Policy | APSgifts';
        $data['meta_desc'] = 'Cancellation and refund guidelines for orders at APSgifts.';
        $this->loadPageSeo('cancellation', $data);
        return view('frontend/cancellation', $data);
    }

    /**
     * Shipping Policy Page.
     */
    public function shipping()
    {
        $data['meta_title'] = 'Shipping & Delivery Policy | APSgifts';
        $data['meta_desc'] = 'Shipping options, express delivery, and delivery timelines at APSgifts.';
        $this->loadPageSeo('shipping', $data);
        return view('frontend/shipping', $data);
    }

    /**
     * AJAX/Post handler to select a city.
     */
    public function selectCity()
    {
        $cityId = (int)$this->request->getPost('city_id');
        $cityModel = new \App\Models\CityModel();
        $city = $cityModel->find($cityId);

        if ($city) {
            session()->set('selected_city_id', $city['id']);
            session()->set('selected_city_name', $city['name']);
            session()->set('selected_city_slug', $city['slug']);
            
            // Set cookie for 30 days
            set_cookie('selected_city_id', $city['id'], 2592000);
            set_cookie('selected_city_name', $city['name'], 2592000);
            set_cookie('selected_city_slug', $city['slug'], 2592000);
            
            return $this->response->setJSON([
                'success' => true, 
                'city_name' => $city['name'],
                'city_slug' => $city['slug']
            ]);
        }

        return $this->response->setJSON(['success' => false, 'error' => 'Invalid City.']);
    }

    /**
     * Helper to load SEO settings & content for static pages
     */
    private function loadPageSeo(string $pageKey, array &$data)
    {
        $db = \Config\Database::connect();
        $seo = $db->table('seo_pages')->where('page_key', $pageKey)->get()->getRowArray();
        if ($seo) {
            $data['page_title'] = $seo['page_name'] ?? ($data['page_title'] ?? '');
            $data['page_content'] = $seo['content'] ?? ($data['page_content'] ?? '');
            $data['meta_title'] = $seo['meta_title'] ?: ($data['meta_title'] ?? '');
            $data['meta_desc'] = $seo['meta_desc'] ?: ($data['meta_desc'] ?? '');
            $data['twitter_card'] = $seo['twitter_card'] ?: 'summary_large_image';
            $data['twitter_title'] = $seo['twitter_title'] ?: $data['meta_title'];
            $data['twitter_desc'] = $seo['twitter_desc'] ?: $data['meta_desc'];
            $data['twitter_image'] = $seo['twitter_image'] ?: ($seo['og_image'] ?? '');
            $data['og_title'] = $seo['og_title'] ?: $data['meta_title'];
            $data['og_desc'] = $seo['og_desc'] ?: $data['meta_desc'];
            $data['og_image'] = $seo['og_image'] ?: '';
            $data['og_type'] = $seo['og_type'] ?: 'website';
            $data['schema_markup'] = $seo['schema_markup'] ?: '';
        }
    }
}
