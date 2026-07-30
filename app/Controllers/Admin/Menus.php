<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuModel;
use App\Models\MenuItemModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Menus extends BaseController
{
    protected $menuModel;
    protected $menuItemModel;
    protected $categoryModel;
    protected $productModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->menuModel = new MenuModel();
        $this->menuItemModel = new MenuItemModel();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Display Menu Designer.
     */
    public function index()
    {
        $this->checkPermission('menus', 'view');
        $selectedMenuId = $this->request->getGet('menu_id');
        
        // Load all menus
        $menus = $this->menuModel->findAll();
        
        if (empty($selectedMenuId) && !empty($menus)) {
            $selectedMenuId = $menus[0]['id'];
        }
        
        $selectedMenu = null;
        $menuItems = [];
        if (!empty($selectedMenuId)) {
            $selectedMenu = $this->menuModel->find($selectedMenuId);
            if ($selectedMenu) {
                // Get menu items in flat list but sorted by sort_order
                $rawItems = $this->menuItemModel->getMenuItems((int)$selectedMenuId);
                
                // We need to attach depth level for the flat list output.
                // We reconstruct depth levels by traversing parent IDs.
                $menuItems = $this->buildFlatTreeWithDepth($rawItems);
            }
        }

        // Load Categories hierarchically
        $categories = $this->categoryModel->getHierarchicalFlatList();

        // Load Products (only simple products, max 100 for builder search)
        $products = $this->productModel->where('product_type', 'simple')->orderBy('name', 'ASC')->limit(100)->findAll();

        // System static pages
        $systemPages = [
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'Shop All', 'url' => '/shop'],
            ['title' => 'About Us', 'url' => '/about'],
            ['title' => 'FAQs', 'url' => '/faq'],
            ['title' => 'Privacy Policy', 'url' => '/privacy'],
            ['title' => 'Terms of Service', 'url' => '/terms'],
            ['title' => 'Contact Us', 'url' => '/contact']
        ];

        $data = [
            'title'        => 'Menu Manager',
            'menus'        => $menus,
            'selectedMenu' => $selectedMenu,
            'menuItems'    => $menuItems,
            'categories'   => $categories,
            'products'     => $products,
            'systemPages'  => $systemPages
        ];

        return view('admin/menus/index', $data);
    }

    /**
     * Create a new menu profile.
     */
    public function create()
    {
        $this->checkPermission('menus', 'create');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            if (empty($name)) {
                $this->session->setFlashdata('error', 'Menu name is required.');
                return redirect()->to(base_url('admin/menus'));
            }

            $slug = generate_slug($name);

            // Ensure unique slug
            $existing = $this->menuModel->where('slug', $slug)->first();
            if ($existing) {
                $slug = $slug . '-' . time();
            }

            $currentUserId = $this->authLib->getUserId();

            $this->menuModel->insert([
                'name' => $name,
                'slug' => $slug,
                'created_by' => $currentUserId,
                'updated_by' => $currentUserId
            ]);

            $this->logActivity('menus', 'create', "Created menu profile: $name");
            $this->session->setFlashdata('success', 'Menu created successfully.');
            return redirect()->to(base_url('admin/menus?menu_id=' . $this->menuModel->getInsertID()));
        }

        return redirect()->to(base_url('admin/menus'));
    }

    /**
     * Delete menu.
     */
    public function delete($id = null)
    {
        $this->checkPermission('menus', 'delete');
        if ($id !== null) {
            $menu = $this->menuModel->find($id);
            if ($menu) {
                // Delete menu items first
                $this->menuItemModel->where('menu_id', $id)->delete();
                $this->menuModel->delete($id);
                $this->logActivity('menus', 'delete', "Deleted menu profile: {$menu['name']} (ID: $id)");
                $this->session->setFlashdata('success', 'Menu deleted successfully.');
            }
        }
        return redirect()->to(base_url('admin/menus'));
    }

    /**
     * Update menu structure via AJAX.
     */
    public function updateStructure()
    {
        $this->checkPermission('menus', 'edit');
        $menuId = $this->request->getPost('menu_id');
        $structure = $this->request->getPost('structure'); // Array of items
        
        if (empty($menuId) || !is_array($structure)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Invalid data.']);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        // 1. Get existing item IDs to identify deletions
        $existingItems = $this->menuItemModel->where('menu_id', $menuId)->findAll();
        $existingIds = array_column($existingItems, 'id');
        
        $submittedIds = [];
        $tempToDbIdMap = [];
        $currentUserId = $this->authLib->getUserId();
        
        // Pass 1: Insert new items and update basic info (ignoring parent_id temporarily)
        foreach ($structure as $index => $item) {
            $itemId = $item['id'];
            
            $saveData = [
                'menu_id'      => $menuId,
                'title'        => $item['title'],
                'type'         => $item['type'],
                'object_id'    => !empty($item['object_id']) ? (int)$item['object_id'] : null,
                'url'          => !empty($item['url']) ? $item['url'] : null,
                'is_mega_menu' => !empty($item['is_mega_menu']) && $item['is_mega_menu'] == '1' ? 1 : 0,
                'sort_order'   => $index + 1,
                'updated_by'   => $currentUserId
            ];
            
            if (strpos($itemId, 'new_') === 0) {
                // New item, insert
                $saveData['created_by'] = $currentUserId;
                $this->menuItemModel->insert($saveData);
                $dbId = $this->menuItemModel->getInsertID();
                $tempToDbIdMap[$itemId] = $dbId;
                $submittedIds[] = $dbId;
            } else {
                // Existing item, update
                $dbId = (int)$itemId;
                // If existing has no creator, set it
                $dbItem = $this->menuItemModel->find($dbId);
                if ($dbItem && empty($dbItem['created_by'])) {
                    $saveData['created_by'] = $currentUserId;
                }
                $this->menuItemModel->update($dbId, $saveData);
                $tempToDbIdMap[$dbId] = $dbId;
                $submittedIds[] = $dbId;
            }
        }
        
        // Pass 2: Set parent_id correctly using depth logic.
        $parentDbIdByDepth = [
            -1 => null // root
        ];
        
        foreach ($structure as $index => $item) {
            $depth = (int)$item['depth'];
            
            if ($depth < 0) $depth = 0;
            if ($depth > 2) $depth = 2; // Mega menu tree usually max 3 levels (0, 1, 2)
            
            $parentDbId = $parentDbIdByDepth[$depth - 1] ?? null;
            
            $frontendId = $item['id'];
            $dbId = $tempToDbIdMap[$frontendId] ?? null;
            
            if ($dbId) {
                $this->menuItemModel->update($dbId, ['parent_id' => $parentDbId]);
                $parentDbIdByDepth[$depth] = $dbId;
            }
        }
        
        // 3. Delete items that were not submitted
        $toDelete = array_diff($existingIds, $submittedIds);
        if (!empty($toDelete)) {
            $this->menuItemModel->whereIn('id', $toDelete)->delete();
        }
        
        $db->transComplete();

        // Clear caching for category menu just in case menu changed
        cache()->delete('category_tree_menu');
        cache()->delete('frontend_main_menu');
        
        if ($db->transStatus()) {
            $menu = $this->menuModel->find($menuId);
            $menuName = $menu ? $menu['name'] : 'ID: ' . $menuId;
            $this->logActivity('menus', 'edit', "Updated structure of menu: $menuName");
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to save menu structure.']);
        }
    }

    /**
     * Traverses raw menu items list to attach depth level for linear display.
     */
    private function buildFlatTreeWithDepth(array $items)
    {
        $indexed = [];
        foreach ($items as $item) {
            $indexed[$item['id']] = $item;
        }

        $tree = [];
        $this->traverseAndFlatten($indexed, null, 0, $tree);
        return $tree;
    }

    private function traverseAndFlatten(array $indexed, $parentId, int $depth, array &$tree)
    {
        foreach ($indexed as $id => $item) {
            if ($item['parent_id'] == $parentId) {
                $item['depth'] = $depth;
                $tree[] = $item;
                $this->traverseAndFlatten($indexed, $id, $depth + 1, $tree);
            }
        }
    }

    /**
     * Set the selected menu as active for the frontend header navigation.
     */
    public function activate($id = null)
    {
        $this->checkPermission('menus', 'edit');
        if ($id !== null) {
            $menu = $this->menuModel->find($id);
            if ($menu) {
                // Set all other menus' is_active = 0
                $this->menuModel->where('id !=', $id)->set(['is_active' => 0])->update();
                // Set this menu's is_active = 1
                $this->menuModel->update($id, ['is_active' => 1]);

                // Clear frontend menu cache!
                cache()->delete('frontend_main_menu');

                $this->logActivity('menus', 'edit', "Activated menu: {$menu['name']} (ID: $id)");
                $this->session->setFlashdata('success', 'Menu activated successfully and set as primary header navigation.');
            }
        }
        return redirect()->to(base_url('admin/menus?menu_id=' . $id));
    }
}
