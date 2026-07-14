<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'slug', 'sku', 'price', 'description', 'short_description', 'delivery_type', 'offer_id', 'meta_title', 'meta_desc', 'is_active', 'created_at', 'product_type', 'color', 'hide_from_frontend', 'twitter_card', 'twitter_title', 'twitter_desc', 'twitter_image', 'og_title', 'og_desc', 'og_image', 'og_type', 'schema_markup', 'created_by', 'updated_by', 'updated_at'];

    protected $useTimestamps = false;

    /**
     * Get all products for Admin listing (including categories and primary image).
     */
    public function getAll()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path, creator.name as creator_name, updater.name as updater_name')
            ->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->join('users creator', 'creator.id = p.created_by', 'left')
            ->join('users updater', 'updater.id = p.updated_by', 'left')
            ->orderBy('p.id', 'DESC');

        $products = $builder->get()->getResultArray();

        // Attach category names to each product
        foreach ($products as &$product) {
            $product['categories'] = $this->getProductCategories($product['id']);
        }

        return $products;
    }



    /**
     * Get active products by Category Slug.
     */
    public function getByCategorySlug(string $category_slug)
    {
        $db = \Config\Database::connect();
        return $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
            ->join('product_categories pc', 'pc.product_id = p.id')
            ->join('categories c', 'c.id = pc.category_id')
            ->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('c.slug', $category_slug)
            ->where('p.is_active', 1)
            ->where('p.hide_from_frontend', 0)
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get single product details by Slug.
     */
    public function getBySlug(string $slug)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, o.applies_to as offer_applies_to');

        $cityId = session('selected_city_id');
        if ($cityId) {
            $builder->select('COALESCE(pc.price_override, p.price) as price')
                ->join('product_cities pc', 'pc.product_id = p.id AND pc.city_id = ' . (int)$cityId, 'left');
        }

        $product = $builder->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->groupStart()
                ->where('p.slug', $slug)
                ->orWhere('p.sku', $slug)
            ->groupEnd()
            ->where('p.is_active', 1)
            ->get()
            ->getRowArray();

        if ($product) {
            $product['images'] = $this->getProductImages((int)$product['id']);
            $product['categories'] = $this->getProductCategories((int)$product['id']);
            
            // Get combo items
            $product['combo_items'] = [];
            if ($product['product_type'] === 'combo') {
                $product['combo_items'] = $db->table('combo_items ci')
                    ->select('ci.*, p.name, p.sku, p.price, pi.image_path')
                    ->join('products p', 'p.id = ci.child_product_id')
                    ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
                    ->where('ci.combo_product_id', (int)$product['id'])
                    ->get()
                    ->getResultArray();
            }
        }

        return $product;
    }

    /**
     * Get product by ID.
     */
    public function getById(int $id)
    {
        $db = \Config\Database::connect();
        $product = $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
            ->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();

        if ($product) {
            $product['images'] = $this->getProductImages($id);
            
            // Get category IDs
            $catQuery = $db->table('product_categories')
                ->select('category_id')
                ->where('product_id', $id)
                ->get()
                ->getResultArray();
            $product['category_ids'] = array_column($catQuery, 'category_id');

            // Get city mappings
            $cityQuery = $db->table('product_cities')
                ->where('product_id', $id)
                ->get()
                ->getResultArray();
            $product['city_mappings'] = [];
            foreach ($cityQuery as $cq) {
                $product['city_mappings'][$cq['city_id']] = $cq['price_override'];
            }

            // Get combo items
            $comboQuery = $db->table('combo_items ci')
                ->select('ci.*, p.name, p.sku, p.price, pi.image_path')
                ->join('products p', 'p.id = ci.child_product_id')
                ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
                ->where('ci.combo_product_id', $id)
                ->get()
                ->getResultArray();
            $product['combo_items'] = $comboQuery;
        }

        return $product;
    }

    /**
     * Search products by keyword in name or description.
     */
    public function searchProducts(string $keyword)
    {
        $db = \Config\Database::connect();
        return $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
            ->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('p.is_active', 1)
            ->where('p.hide_from_frontend', 0)
            ->groupStart()
                ->like('p.name', $keyword)
                ->orLike('p.description', $keyword)
            ->groupEnd()
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get categories associated with a product.
     */
    public function getProductCategories(int $product_id)
    {
        $db = \Config\Database::connect();
        return $db->table('categories c')
            ->select('c.*, p.name as parent_name, p.slug as parent_slug')
            ->join('product_categories pc', 'pc.category_id = c.id')
            ->join('categories p', 'p.id = c.parent_id', 'left')
            ->where('pc.product_id', $product_id)
            ->get()
            ->getResultArray();
    }

    /**
     * Get related products of same categories, excluding the current product.
     */
    public function getRelatedProducts(int $current_product_id, array $category_ids, int $limit = 4)
    {
        if (empty($category_ids)) {
            return [];
        }

        $db = \Config\Database::connect();
        $builder = $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
            ->join('product_categories pc', 'pc.product_id = p.id')
            ->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->whereIn('pc.category_id', $category_ids)
            ->where('p.id !=', $current_product_id)
            ->where('p.is_active', 1)
            ->where('p.hide_from_frontend', 0)
            ->groupBy('p.id')
            ->orderBy('RAND()')
            ->limit($limit);

        $cityId = session('selected_city_id');
        if ($cityId) {
            $builder->select('COALESCE(pc2.price_override, p.price) as price')
                ->join('product_cities pc2', 'pc2.product_id = p.id AND pc2.city_id = ' . (int)$cityId, 'left');
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get image gallery for a product.
     */
    public function getProductImages(int $product_id)
    {
        $db = \Config\Database::connect();
        return $db->table('product_images')
            ->where('product_id', $product_id)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Save product (Admin).
     */
    public function insertProduct(array $product_data, array $category_ids, array $uploaded_images = [], array $city_mappings = [], array $combo_items = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Insert product
        $db->table('products')->insert($product_data);
        $productId = $db->insertID();

        // 2. Insert category links
        if (!empty($category_ids)) {
            foreach ($category_ids as $catId) {
                $db->table('product_categories')->insert([
                    'product_id'  => $productId,
                    'category_id' => $catId
                ]);
            }
        }

        // 3. Insert product images
        if (!empty($uploaded_images)) {
            $isFirst = true;
            foreach ($uploaded_images as $index => $image_data) {
                $path = is_array($image_data) ? $image_data['path'] : $image_data;
                $alt = is_array($image_data) ? ($image_data['alt'] ?? '') : '';
                $db->table('product_images')->insert([
                    'product_id' => $productId,
                    'image_path' => $path,
                    'alt'        => $alt,
                    'is_primary' => $isFirst ? 1 : 0,
                    'sort_order' => $index
                ]);
                $isFirst = false;
            }
        }

        // 4. Insert city mappings & overrides
        if (!empty($city_mappings)) {
            foreach ($city_mappings as $cityId => $priceOverride) {
                $db->table('product_cities')->insert([
                    'product_id'     => $productId,
                    'city_id'        => $cityId,
                    'price_override' => ($priceOverride !== '' && $priceOverride !== null) ? (float)$priceOverride : null,
                    'is_available'   => 1
                ]);
            }
        }

        // 5. Insert combo items
        if ($product_data['product_type'] === 'combo' && !empty($combo_items)) {
            foreach ($combo_items as $childId => $qty) {
                $db->table('combo_items')->insert([
                    'combo_product_id' => $productId,
                    'child_product_id' => $childId,
                    'qty'              => (int)$qty
                ]);
            }
        }

        $db->transComplete();
        return $db->transStatus() ? $productId : false;
    }

    /**
     * Update product (Admin).
     */
    public function updateProduct(int $id, array $product_data, array $category_ids, array $new_images = [], array $existing_to_delete = [], array $city_mappings = [], array $combo_items = [], array $existing_image_alts = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update product base data
        $db->table('products')->where('id', $id)->update($product_data);

        // 2. Refresh categories
        $db->table('product_categories')->where('product_id', $id)->delete();
        if (!empty($category_ids)) {
            foreach ($category_ids as $catId) {
                $db->table('product_categories')->insert([
                    'product_id'  => $id,
                    'category_id' => $catId
                ]);
            }
        }

        // 3. Delete requested images
        if (!empty($existing_to_delete)) {
            $db->table('product_images')->whereIn('id', $existing_to_delete)->delete();
        }

        // 4. Add new images
        if (!empty($new_images)) {
            $hasPrimary = $db->table('product_images')->where(['product_id' => $id, 'is_primary' => 1])->countAllResults() > 0;
            
            $isFirst = !$hasPrimary;
            foreach ($new_images as $image_data) {
                $path = is_array($image_data) ? $image_data['path'] : $image_data;
                $alt = is_array($image_data) ? ($image_data['alt'] ?? '') : '';
                $db->table('product_images')->insert([
                    'product_id' => $id,
                    'image_path' => $path,
                    'alt'        => $alt,
                    'is_primary' => $isFirst ? 1 : 0,
                    'sort_order' => 10
                ]);
                $isFirst = false;
            }
        }

        // 4.5. Update existing image alt texts
        if (!empty($existing_image_alts)) {
            foreach ($existing_image_alts as $imgId => $altText) {
                $db->table('product_images')->where('id', $imgId)->update(['alt' => $altText]);
            }
        }

        // 5. Refresh city mappings
        $db->table('product_cities')->where('product_id', $id)->delete();
        if (!empty($city_mappings)) {
            foreach ($city_mappings as $cityId => $priceOverride) {
                $db->table('product_cities')->insert([
                    'product_id'     => $id,
                    'city_id'        => $cityId,
                    'price_override' => ($priceOverride !== '' && $priceOverride !== null) ? (float)$priceOverride : null,
                    'is_available'   => 1
                ]);
            }
        }

        // 6. Refresh combo items
        $db->table('combo_items')->where('combo_product_id', $id)->delete();
        if (isset($product_data['product_type']) && $product_data['product_type'] === 'combo' && !empty($combo_items)) {
            foreach ($combo_items as $childId => $qty) {
                $db->table('combo_items')->insert([
                    'combo_product_id' => $id,
                    'child_product_id' => $childId,
                    'qty'              => (int)$qty
                ]);
            }
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Delete product.
     */
    public function deleteProduct(int $id)
    {
        return $this->delete($id);
    }

    /**
     * Get single product with active offer by ID.
     */
    public function getByIdWithOffer(int $id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path');

        $cityId = session('selected_city_id');
        if ($cityId) {
            $builder->select('COALESCE(pc.price_override, p.price) as price')
                ->join('product_cities pc', 'pc.product_id = p.id AND pc.city_id = ' . (int)$cityId, 'left');
        }

        $product = $builder->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();

        if ($product) {
            $product['images'] = $this->getProductImages($id);
            $product['categories'] = $this->getProductCategories($id);
        }

        return $product;
    }

    /**
     * Helper for flag filtered query
     */
    private function getFilteredProducts(string $flagField, int $limit)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products p')
            ->select('p.*, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path');

        $cityId = session('selected_city_id');
        if ($cityId) {
            $builder->select('COALESCE(pc.price_override, p.price) as price')
                ->join('product_cities pc', 'pc.product_id = p.id AND pc.city_id = ' . (int)$cityId, 'left')
                ->groupStart()
                    ->where('p.delivery_type', 'Courier')
                    ->orGroupStart()
                        ->where('p.delivery_type', 'Express')
                        ->where('pc.is_available', 1)
                    ->groupEnd()
                ->groupEnd();
        }

        return $builder->join('offers o', 'o.id = p.offer_id AND o.is_active = 1', 'left')
            ->join('product_images pi', 'pi.product_id = p.id AND pi.is_primary = 1', 'left')
            ->where('p.is_active', 1)
            ->where('p.hide_from_frontend', 0)
            ->where('p.' . $flagField, 1)
            ->limit($limit)
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getBestSellers(int $limit = 8)
    {
        return $this->getFilteredProducts('is_bestseller', $limit);
    }

    public function getOnSale(int $limit = 8)
    {
        return $this->getFilteredProducts('is_onsale', $limit);
    }

    public function getTopRated(int $limit = 8)
    {
        return $this->getFilteredProducts('is_toprated', $limit);
    }

    public function getTrending(int $limit = 8)
    {
        return $this->getFilteredProducts('is_trending', $limit);
    }

    /**
     * Get paginated products for shop
     */
    public function getShopProductsPaginated(string $search = '', string $min_price = '', string $max_price = '', int $perPage = 9, string $sort = '', array $colors = [])
    {
        $cityId = session('selected_city_id');

        $this->select('products.id, products.name, products.slug, products.sku, products.description, products.short_description, products.delivery_type, products.offer_id, products.meta_title, products.meta_desc, products.is_active, products.created_at, products.product_type, products.color, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
             ->join('offers o', 'o.id = products.offer_id AND o.is_active = 1', 'left')
             ->join('product_images pi', 'pi.product_id = products.id AND pi.is_primary = 1', 'left')
             ->where('products.is_active', 1)
             ->where('products.hide_from_frontend', 0);

        if ($cityId) {
            $this->select('COALESCE(pc.price_override, products.price) as price')
                 ->join('product_cities pc', 'pc.product_id = products.id AND pc.city_id = ' . (int)$cityId, 'left')
                  ->groupStart()
                      ->where('products.delivery_type', 'Courier')
                      ->orGroupStart()
                          ->where('products.delivery_type', 'Express')
                          ->groupStart()
                              ->where('pc.is_available', 1)
                              ->orWhere('pc.is_available', null)
                          ->groupEnd()
                      ->groupEnd()
                  ->groupEnd();
        } else {
            $this->select('products.price as price');
        }

        if (!empty($search)) {
            $this->groupStart()
                 ->like('products.name', $search)
                 ->orLike('products.description', $search)
                 ->groupEnd();
        }

        if ($min_price !== '') {
            $this->where('products.price >=', (float)$min_price);
        }

        if ($max_price !== '') {
            $this->where('products.price <=', (float)$max_price);
        }

        if (!empty($colors)) {
            $this->groupStart();
            $first = true;
            foreach ($colors as $color) {
                $color = trim($color);
                if ($first) {
                    $this->like('products.color', $color);
                    $first = false;
                } else {
                    $this->orLike('products.color', $color);
                }
            }
            $this->groupEnd();
        }

        if ($sort === 'price_low_high') {
            $this->orderBy('price', 'ASC');
        } elseif ($sort === 'price_high_low') {
            $this->orderBy('price', 'DESC');
        } else {
            $this->orderBy('products.id', 'DESC');
        }

        return [
            'products' => $this->paginate($perPage),
            'pager'    => $this->pager
        ];
    }

    /**
     * Get paginated products for category
     */
    public function getCategoryProductsPaginated(string $category_slug, string $search = '', string $min_price = '', string $max_price = '', int $perPage = 9, string $sort = '', array $colors = [])
    {
        $cityId = session('selected_city_id');

        $this->select('products.id, products.name, products.slug, products.sku, products.description, products.short_description, products.delivery_type, products.offer_id, products.meta_title, products.meta_desc, products.is_active, products.created_at, products.product_type, products.color, o.name as offer_name, o.type as offer_type, o.value as offer_value, pi.image_path')
             ->join('product_categories pc', 'pc.product_id = products.id')
             ->join('categories c', 'c.id = pc.category_id')
             ->join('offers o', 'o.id = products.offer_id AND o.is_active = 1', 'left')
             ->join('product_images pi', 'pi.product_id = products.id AND pi.is_primary = 1', 'left')
             ->groupStart()
                 ->where('c.slug', $category_slug)
                 ->orWhere('c.parent_id IN (SELECT id FROM categories WHERE slug = ' . $this->db->escape($category_slug) . ')')
             ->groupEnd()
             ->where('products.is_active', 1)
             ->where('products.hide_from_frontend', 0)
             ->groupBy('products.id');

        if ($cityId) {
            $this->select('COALESCE(pc_city.price_override, products.price) as price')
                 ->join('product_cities pc_city', 'pc_city.product_id = products.id AND pc_city.city_id = ' . (int)$cityId, 'left')
                 ->groupStart()
                     ->where('products.delivery_type', 'Courier')
                     ->orGroupStart()
                         ->where('products.delivery_type', 'Express')
                         ->groupStart()
                             ->where('pc_city.is_available', 1)
                             ->orWhere('pc_city.is_available', null)
                         ->groupEnd()
                     ->groupEnd()
                 ->groupEnd();
        } else {
            $this->select('products.price as price');
        }

        if (!empty($search)) {
            $this->groupStart()
                 ->like('products.name', $search)
                 ->orLike('products.description', $search)
                 ->groupEnd();
        }

        if ($min_price !== '') {
            $this->where('products.price >=', (float)$min_price);
        }

        if ($max_price !== '') {
            $this->where('products.price <=', (float)$max_price);
        }

        if (!empty($colors)) {
            $this->groupStart();
            $first = true;
            foreach ($colors as $color) {
                $color = trim($color);
                if ($first) {
                    $this->like('products.color', $color);
                    $first = false;
                } else {
                    $this->orLike('products.color', $color);
                }
            }
            $this->groupEnd();
        }

        if ($sort === 'price_low_high') {
            $this->orderBy('price', 'ASC');
        } elseif ($sort === 'price_high_low') {
            $this->orderBy('price', 'DESC');
        } else {
            $this->orderBy('products.id', 'DESC');
        }

        return [
            'products' => $this->paginate($perPage),
            'pager'    => $this->pager
        ];
    }
}
