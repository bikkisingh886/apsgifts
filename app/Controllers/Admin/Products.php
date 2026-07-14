<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\OfferModel;
use App\Models\CityModel;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $productImageModel;
    protected $offerModel;
    protected $cityModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->productImageModel = new ProductImageModel();
        $this->offerModel = new OfferModel();
        $this->cityModel = new CityModel();
    }

    /**
     * List all products.
     */
    public function index()
    {
        $this->checkPermission('products', 'view');
        $data['products'] = $this->productModel->getAll();
        $data['title'] = 'Manage Products';
        return view('admin/products/index', $data);
    }

    /**
     * Create product.
     */
    public function create()
    {
        $this->checkPermission('products', 'create');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $slug = $this->request->getPost('slug') ?? '';
            $sku = $this->request->getPost('sku');
            $categoryIds = $this->request->getPost('category_ids') ?? [];
            $deliveryType = $this->request->getPost('delivery_type');
            $price = (float)$this->request->getPost('price');
            $offerId = $this->request->getPost('offer_id');
            $description = $this->request->getPost('description');
            $shortDescription = $this->request->getPost('short_description');
            $metaTitle = $this->request->getPost('meta_title');
            $metaDesc = $this->request->getPost('meta_desc');
            $color = $this->request->getPost('color');

            // Flags
            $isBestseller = (int)$this->request->getPost('is_bestseller');
            $isOnsale = (int)$this->request->getPost('is_onsale');
            $isToprated = (int)$this->request->getPost('is_toprated');
            $isTrending = (int)$this->request->getPost('is_trending');
            $isCustomizable = (int)$this->request->getPost('is_customizable');
            $productType = $this->request->getPost('product_type') ?? 'simple';
            $hideFromFrontend = (int)$this->request->getPost('hide_from_frontend');

            // SEO Social Media Fields
            $twitterCard = $this->request->getPost('twitter_card') ?? 'summary_large_image';
            $twitterTitle = $this->request->getPost('twitter_title');
            $twitterDesc = $this->request->getPost('twitter_desc');
            $ogTitle = $this->request->getPost('og_title');
            $ogDesc = $this->request->getPost('og_desc');
            $ogType = $this->request->getPost('og_type') ?? 'product';
            $schemaMarkup = $this->request->getPost('schema_markup');
            
            // City mappings
            $cityIds = $this->request->getPost('city_ids') ?? [];
            $cityPrices = $this->request->getPost('city_prices') ?? [];
            $cityMappings = [];
            foreach ($cityIds as $cityId) {
                if (!empty($cityId)) {
                    $cityMappings[$cityId] = $cityPrices[$cityId] !== '' ? $cityPrices[$cityId] : null;
                }
            }

            // Combo items
            $comboProductIds = $this->request->getPost('combo_product_ids') ?? [];
            $comboQtys = $this->request->getPost('combo_qtys') ?? [];
            $comboItems = [];
            foreach ($comboProductIds as $index => $childId) {
                if (!empty($childId)) {
                    $comboItems[$childId] = $comboQtys[$index] ?? 1;
                }
            }

            if (empty($name) || empty($sku) || empty($categoryIds) || empty($description)) {
                $this->session->setFlashdata('error', 'Please fill in all required fields.');
                return redirect()->to(base_url('admin/products/create'));
            }

            if (empty($slug)) {
                $slug = generate_slug($name);
            } else {
                $slug = generate_slug($slug);
            }

            // Check for duplicate slug
            $existingProd = $this->productModel->where('slug', $slug)->first();
            if ($existingProd) {
                $this->session->setFlashdata('error', 'Slug "' . $slug . '" is already in use by another product. Please choose a different slug.');
                return redirect()->to(base_url('admin/products/create'));
            }

            $customizationType = $this->request->getPost('customization_type');
            $currentUserId = $this->authLib->getUserId();

            // Handle Twitter Image upload
            $twitterImage = null;
            $twImgFile = $this->request->getFile('twitter_image_file');
            if ($twImgFile && $twImgFile->isValid() && !$twImgFile->hasMoved()) {
                $newName = $twImgFile->getRandomName();
                if ($twImgFile->move(FCPATH . 'uploads/seo', $newName)) {
                    $twitterImage = 'uploads/seo/' . $newName;
                }
            }

            // Handle OG Image upload
            $ogImage = null;
            $ogImgFile = $this->request->getFile('og_image_file');
            if ($ogImgFile && $ogImgFile->isValid() && !$ogImgFile->hasMoved()) {
                $newName = $ogImgFile->getRandomName();
                if ($ogImgFile->move(FCPATH . 'uploads/seo', $newName)) {
                    $ogImage = 'uploads/seo/' . $newName;
                }
            }

            $saveData = [
                'name'               => $name,
                'slug'               => $slug,
                'sku'                => $sku,
                'product_type'       => $productType,
                'delivery_type'      => $deliveryType,
                'price'              => $price,
                'offer_id'           => !empty($offerId) ? (int)$offerId : null,
                'description'        => $description,
                'short_description'  => $shortDescription,
                'meta_title'         => $metaTitle,
                'meta_desc'          => $metaDesc,
                'is_bestseller'      => $isBestseller,
                'is_onsale'          => $isOnsale,
                'is_toprated'        => $isToprated,
                'is_trending'        => $isTrending,
                'is_customizable'    => $isCustomizable,
                'customization_type' => $customizationType,
                'color'              => !empty($color) ? $color : null,
                'is_active'          => 1,
                'hide_from_frontend' => $hideFromFrontend,
                'twitter_card'       => $twitterCard,
                'twitter_title'      => $twitterTitle,
                'twitter_desc'       => $twitterDesc,
                'twitter_image'      => $twitterImage,
                'og_title'           => $ogTitle,
                'og_desc'            => $ogDesc,
                'og_image'           => $ogImage,
                'og_type'            => $ogType,
                'schema_markup'      => $schemaMarkup,
                'created_by'         => $currentUserId,
                'updated_by'         => $currentUserId
            ];

            // Move uploaded images
            $uploadedImages = [];
            $images = $this->request->getFileMultiple('images');
            $imageAlts = $this->request->getPost('image_alts') ?? [];
            if ($images) {
                foreach ($images as $index => $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                        $newName = $img->getRandomName();
                        if ($img->move(FCPATH . 'uploads', $newName)) {
                            $uploadedImages[] = [
                                'path' => 'uploads/' . $newName,
                                'alt'  => $imageAlts[$index] ?? ''
                            ];
                        }
                    }
                }
            }

            $productId = $this->productModel->insertProduct($saveData, $categoryIds, $uploadedImages, $cityMappings, $comboItems);

            if ($productId) {
                $this->logActivity('products', 'create', "Created product: $name ($sku)");
                $this->session->setFlashdata('success', 'Product created successfully.');
                return redirect()->to(base_url('admin/products'));
            } else {
                $this->session->setFlashdata('error', 'Failed to create product.');
            }
        }

        $data['categories'] = $this->categoryModel->findAll();
        $data['offers'] = $this->offerModel->findAll();
        $data['cities'] = $this->cityModel->orderBy('name', 'ASC')->findAll();
        $data['all_products'] = $this->productModel->where('product_type', 'simple')->orderBy('name', 'ASC')->findAll();
        $data['title'] = 'Add New Product';
        return view('admin/products/create', $data);
    }

    public function edit($id = null)
    {
        $this->checkPermission('products', 'edit');
        if ($id === null) {
            return redirect()->to(base_url('admin/products'));
        }

        $product = $this->productModel->getById((int)$id);
        if (!$product) {
            $this->session->setFlashdata('error', 'Product not found.');
            return redirect()->to(base_url('admin/products'));
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $slug = $this->request->getPost('slug') ?? '';
            $sku = $this->request->getPost('sku');
            $categoryIds = $this->request->getPost('category_ids') ?? [];
            $deliveryType = $this->request->getPost('delivery_type');
            $price = (float)$this->request->getPost('price');
            $offerId = $this->request->getPost('offer_id');
            $description = $this->request->getPost('description');
            $shortDescription = $this->request->getPost('short_description');
            $metaTitle = $this->request->getPost('meta_title');
            $metaDesc = $this->request->getPost('meta_desc');
            $isActive = (int)$this->request->getPost('is_active');
            $color = $this->request->getPost('color');

            // Flags
            $isBestseller = (int)$this->request->getPost('is_bestseller');
            $isOnsale = (int)$this->request->getPost('is_onsale');
            $isToprated = (int)$this->request->getPost('is_toprated');
            $isTrending = (int)$this->request->getPost('is_trending');
            $isCustomizable = (int)$this->request->getPost('is_customizable');
            $productType = $this->request->getPost('product_type') ?? 'simple';
            $hideFromFrontend = (int)$this->request->getPost('hide_from_frontend');

            // SEO Social Media Fields
            $twitterCard = $this->request->getPost('twitter_card') ?? 'summary_large_image';
            $twitterTitle = $this->request->getPost('twitter_title');
            $twitterDesc = $this->request->getPost('twitter_desc');
            $ogTitle = $this->request->getPost('og_title');
            $ogDesc = $this->request->getPost('og_desc');
            $ogType = $this->request->getPost('og_type') ?? 'product';
            $schemaMarkup = $this->request->getPost('schema_markup');
            
            // City mappings
            $cityIds = $this->request->getPost('city_ids') ?? [];
            $cityPrices = $this->request->getPost('city_prices') ?? [];
            $cityMappings = [];
            foreach ($cityIds as $cityId) {
                if (!empty($cityId)) {
                    $cityMappings[$cityId] = $cityPrices[$cityId] !== '' ? $cityPrices[$cityId] : null;
                }
            }

            // Combo items
            $comboProductIds = $this->request->getPost('combo_product_ids') ?? [];
            $comboQtys = $this->request->getPost('combo_qtys') ?? [];
            $comboItems = [];
            foreach ($comboProductIds as $index => $childId) {
                if (!empty($childId)) {
                    $comboItems[$childId] = $comboQtys[$index] ?? 1;
                }
            }

            if (empty($name) || empty($sku) || empty($categoryIds) || empty($description)) {
                $this->session->setFlashdata('error', 'Please fill in all required fields.');
                return redirect()->to(base_url('admin/products/edit/' . $id));
            }

            if (empty($slug)) {
                $slug = generate_slug($name);
            } else {
                $slug = generate_slug($slug);
            }

            // Check for duplicate slug (excluding current product)
            $existingProd = $this->productModel->where('slug', $slug)->where('id !=', $id)->first();
            if ($existingProd) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Slug "' . $slug . '" is already in use by another product.']);
                }
                $this->session->setFlashdata('error', 'Slug "' . $slug . '" is already in use. Please choose a different slug.');
                return redirect()->to(base_url('admin/products/edit/' . $id));
            }

            $customizationType = $this->request->getPost('customization_type');
            $currentUserId = $this->authLib->getUserId();

            // Handle Twitter Image upload
            $twitterImage = $product['twitter_image'];
            $twImgFile = $this->request->getFile('twitter_image_file');
            if ($twImgFile && $twImgFile->isValid() && !$twImgFile->hasMoved()) {
                $newName = $twImgFile->getRandomName();
                if ($twImgFile->move(FCPATH . 'uploads/seo', $newName)) {
                    $twitterImage = 'uploads/seo/' . $newName;
                }
            }

            // Handle OG Image upload
            $ogImage = $product['og_image'];
            $ogImgFile = $this->request->getFile('og_image_file');
            if ($ogImgFile && $ogImgFile->isValid() && !$ogImgFile->hasMoved()) {
                $newName = $ogImgFile->getRandomName();
                if ($ogImgFile->move(FCPATH . 'uploads/seo', $newName)) {
                    $ogImage = 'uploads/seo/' . $newName;
                }
            }

            $updateData = [
                'name'               => $name,
                'slug'               => $slug,
                'sku'                => $sku,
                'product_type'       => $productType,
                'delivery_type'      => $deliveryType,
                'price'              => $price,
                'offer_id'           => !empty($offerId) ? (int)$offerId : null,
                'description'        => $description,
                'short_description'  => $shortDescription,
                'meta_title'         => $metaTitle,
                'meta_desc'          => $metaDesc,
                'is_bestseller'      => $isBestseller,
                'is_onsale'          => $isOnsale,
                'is_toprated'        => $isToprated,
                'is_trending'        => $isTrending,
                'is_customizable'    => $isCustomizable,
                'customization_type' => $customizationType,
                'color'              => !empty($color) ? $color : null,
                'is_active'          => $isActive,
                'hide_from_frontend' => $hideFromFrontend,
                'twitter_card'       => $twitterCard,
                'twitter_title'      => $twitterTitle,
                'twitter_desc'       => $twitterDesc,
                'twitter_image'      => $twitterImage,
                'og_title'           => $ogTitle,
                'og_desc'            => $ogDesc,
                'og_image'           => $ogImage,
                'og_type'            => $ogType,
                'schema_markup'      => $schemaMarkup,
                'updated_by'         => $currentUserId
            ];

            // Set creator if empty
            if (empty($product['created_by'])) {
                $updateData['created_by'] = $currentUserId;
            }

            // Move uploaded new images
            $newImages = [];
            $images = $this->request->getFileMultiple('images');
            $imageAlts = $this->request->getPost('image_alts') ?? [];
            if ($images) {
                foreach ($images as $index => $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                        $newName = $img->getRandomName();
                        if ($img->move(FCPATH . 'uploads', $newName)) {
                            $newImages[] = [
                                'path' => 'uploads/' . $newName,
                                'alt'  => $imageAlts[$index] ?? ''
                            ];
                        }
                    }
                }
            }

            $existingImageAlts = $this->request->getPost('existing_image_alts') ?? [];

            if ($this->productModel->updateProduct((int)$id, $updateData, $categoryIds, $newImages, [], $cityMappings, $comboItems, $existingImageAlts)) {
                $this->logActivity('products', 'edit', "Updated product: $name ($sku)");
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true]);
                }
                $this->session->setFlashdata('success', 'Product updated successfully.');
                return redirect()->to(base_url('admin/products'));
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Failed to update product.']);
                }
                $this->session->setFlashdata('error', 'Failed to update product.');
            }
        }

        $data['product'] = $product;
        $data['categories'] = $this->categoryModel->findAll();
        $data['offers'] = $this->offerModel->findAll();
        $data['cities'] = $this->cityModel->orderBy('name', 'ASC')->findAll();
        $data['all_products'] = $this->productModel->where('product_type', 'simple')->where('id !=', $id)->orderBy('name', 'ASC')->findAll();
        $data['title'] = 'Edit Product: ' . $product['name'];
        
        if ($this->request->isAJAX()) {
            return view('admin/products/edit_partial', $data);
        }
        return view('admin/products/edit', $data);
    }

    /**
     * Toggle active status.
     */
    public function toggle($id = null)
    {
        $this->checkPermission('products', 'edit');
        if ($id !== null) {
            $product = $this->productModel->find($id);
            if ($product) {
                $newStatus = $product['is_active'] ? 0 : 1;
                $currentUserId = $this->authLib->getUserId();
                $this->productModel->update($id, [
                    'is_active' => $newStatus,
                    'updated_by' => $currentUserId
                ]);
                $this->logActivity('products', 'edit', "Toggled active status of product: {$product['name']} (ID: $id) to " . ($newStatus ? 'Active' : 'Inactive'));
                $this->session->setFlashdata('success', 'Product status updated.');
            }
        }
        return redirect()->to(base_url('admin/products'));
    }

    /**
     * Helper to verify if product can be safely deleted.
     */
    private function canDeleteProduct($id, &$reason = '')
    {
        $db = \Config\Database::connect();
        
        // Check if product is in any orders
        $orderCount = $db->table('order_items')
            ->where('product_id', $id)
            ->countAllResults();
            
        if ($orderCount > 0) {
            $reason = 'It has existing customer orders associated with it.';
            return false;
        }
        
        return true;
    }

    /**
     * Delete product.
     */
    public function delete($id = null)
    {
        $this->checkPermission('products', 'delete');
        if ($id !== null) {
            $reason = '';
            if (!$this->canDeleteProduct($id, $reason)) {
                $this->session->setFlashdata('error', 'Cannot delete product: ' . $reason);
                return redirect()->to(base_url('admin/products'));
            }

            $product = $this->productModel->find($id);
            $productName = $product ? $product['name'] : 'ID: ' . $id;

            if ($this->productModel->delete((int)$id)) {
                $this->productImageModel->where('product_id', $id)->delete();
                $this->logActivity('products', 'delete', "Deleted product: $productName (ID: $id)");
                $this->session->setFlashdata('success', 'Product deleted successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete product.');
            }
        }
        return redirect()->to(base_url('admin/products'));
    }

    /**
     * Delete specific product image.
     */
    public function delete_image($img_id = null)
    {
        $this->checkPermission('products', 'edit');
        if ($img_id !== null) {
            $img = $this->productImageModel->find($img_id);
            if ($img) {
                @unlink(FCPATH . $img['image_path']);
                $this->productImageModel->delete($img_id);
                $this->logActivity('products', 'edit', "Deleted image ID: $img_id from product ID: {$img['product_id']}");
                $this->session->setFlashdata('success', 'Image deleted successfully.');
                return redirect()->back();
            }
        }
        $this->session->setFlashdata('error', 'Image not found.');
        return redirect()->back();
    }

    /**
     * Bulk delete products.
     */
    public function bulkDelete()
    {
        $this->checkPermission('products', 'delete');
        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No products selected.']);
        }

        $ids = array_map('intval', $ids);
        $deleted = 0;
        $errors  = 0;
        $skipped = [];

        foreach ($ids as $id) {
            if ($id > 0) {
                $reason = '';
                if ($this->canDeleteProduct($id, $reason)) {
                    $prod = $this->productModel->find($id);
                    $productName = $prod ? $prod['name'] : 'ID: ' . $id;
                    if ($this->productModel->delete($id)) {
                        // Also clean up associated images
                        $images = $this->productImageModel->where('product_id', $id)->findAll();
                        foreach ($images as $img) {
                            @unlink(FCPATH . $img['image_path']);
                        }
                        $this->productImageModel->where('product_id', $id)->delete();
                        $this->logActivity('products', 'delete', "Bulk deleted product: $productName (ID: $id)");
                        $deleted++;
                    } else {
                        $errors++;
                    }
                } else {
                    $prod = $this->productModel->find($id);
                    $name = $prod ? $prod['name'] : 'ID: ' . $id;
                    $skipped[] = '"' . $name . '" (' . $reason . ')';
                }
            }
        }

        if ($deleted > 0) {
            $message = "$deleted " . ($deleted === 1 ? 'product' : 'products') . " deleted successfully.";
            if (!empty($skipped)) {
                $message .= " Skipped: " . implode(', ', $skipped);
            }
            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete selected products. Details: ' . implode('; ', $skipped)
        ]);
    }

    /**
     * AJAX: Check if a product slug is already taken.
     * GET admin/products/check-slug?slug=xxx&id=yyy (id optional for edit)
     */
    public function checkSlug()
    {
        $this->checkPermission('products', 'view');
        $slug = generate_slug($this->request->getGet('slug') ?? '');
        $id   = (int)($this->request->getGet('id') ?? 0);

        if (empty($slug)) {
            return $this->response->setJSON(['available' => true]);
        }

        $query = $this->productModel->where('slug', $slug);
        if ($id > 0) {
            $query->where('id !=', $id);
        }
        $exists = $query->first();

        return $this->response->setJSON([
            'available' => $exists === null,
            'slug'      => $slug
        ]);
    }
}
