<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected $categoryModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Category list & manager page.
     */
    public function index()
    {
        $this->checkPermission('categories', 'view');
        $data['categories'] = $this->categoryModel->getWithProductCounts();
        $data['categories_list'] = $this->categoryModel->orderBy('name', 'ASC')->findAll();
        $data['title'] = 'Manage Categories';
        return view('admin/categories/index', $data);
    }

    /**
     * Create category.
     */
    public function create()
    {
        $this->checkPermission('categories', 'create');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $slug = $this->request->getPost('slug');
            $parentId = $this->request->getPost('parent_id');
            $parentId = (!empty($parentId) && $parentId != 0) ? (int)$parentId : null;
            $summary = $this->request->getPost('summary');
            $footerContent = $this->request->getPost('footer_content');
            $metaTitle = $this->request->getPost('meta_title');
            $metaDesc = $this->request->getPost('meta_desc');
            $isActive = (int)($this->request->getPost('is_active') ?? 1);
            $imageAlt = $this->request->getPost('image_alt');

            if (empty($name)) {
                $this->session->setFlashdata('error', 'Category name is required.');
                return redirect()->to(base_url('admin/categories'));
            }

            $slug = !empty($slug) ? generate_slug($slug) : generate_slug($name);

            // Check for duplicate slug
            $existingCat = $this->categoryModel->where('slug', $slug)->first();
            if ($existingCat) {
                $this->session->setFlashdata('error', 'Slug "' . $slug . '" is already in use. Please choose a different slug.');
                return redirect()->to(base_url('admin/categories'));
            }

            // Handle image upload
            $imagePath = null;
            $img = $this->request->getFile('banner_image');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                $uploadDir = FCPATH . 'uploads/categories/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $newName = $img->getRandomName();
                $img->move($uploadDir, $newName);
                $imagePath = 'uploads/categories/' . $newName;
            }

            $currentUserId = $this->authLib->getUserId();

            $saveData = [
                'name'           => $name,
                'slug'           => $slug,
                'parent_id'      => $parentId,
                'summary'        => $summary,
                'footer_content' => $footerContent,
                'meta_title'     => $metaTitle,
                'meta_desc'      => $metaDesc,
                'is_active'      => $isActive,
                'image_path'     => $imagePath,
                'image_alt'      => $imageAlt,
                'created_by'     => $currentUserId,
                'updated_by'     => $currentUserId
            ];

            if ($this->categoryModel->insert($saveData)) {
                cache()->delete('category_tree_menu');
                $this->logActivity('categories', 'create', "Created category: $name ($slug)");
                $this->session->setFlashdata('success', 'Category created successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to create category.');
            }
        }
        return redirect()->to(base_url('admin/categories'));
    }

    /**
     * Edit category.
     */
    public function edit($id = null)
    {
        $this->checkPermission('categories', 'edit');
        if ($id === null) {
            return redirect()->to(base_url('admin/categories'));
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            $this->session->setFlashdata('error', 'Category not found.');
            return redirect()->to(base_url('admin/categories'));
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $slug = $this->request->getPost('slug');
            $parentId = $this->request->getPost('parent_id');
            $parentId = (!empty($parentId) && $parentId != 0) ? (int)$parentId : null;
            if ($parentId == $id) {
                $parentId = null; // Prevent circular referencing
            }
            $summary = $this->request->getPost('summary');
            $footerContent = $this->request->getPost('footer_content');
            $metaTitle = $this->request->getPost('meta_title');
            $metaDesc = $this->request->getPost('meta_desc');
            $isActive = (int)$this->request->getPost('is_active');
            $imageAlt = $this->request->getPost('image_alt');

            if (empty($name)) {
                $this->session->setFlashdata('error', 'Category name is required.');
                return redirect()->to(base_url('admin/categories/edit/' . $id));
            }

            $slug = !empty($slug) ? generate_slug($slug) : generate_slug($name);

            // Check for duplicate slug (excluding current category)
            $existingCat = $this->categoryModel->where('slug', $slug)->where('id !=', $id)->first();
            if ($existingCat) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Slug "' . $slug . '" is already in use by another category.']);
                }
                $this->session->setFlashdata('error', 'Slug "' . $slug . '" is already in use. Please choose a different slug.');
                return redirect()->to(base_url('admin/categories/edit/' . $id));
            }

            // Handle image upload
            $imagePath = $category['image_path'];
            $img = $this->request->getFile('banner_image');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                $uploadDir = FCPATH . 'uploads/categories/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                // Delete old image
                if ($imagePath && file_exists(FCPATH . $imagePath)) {
                    @unlink(FCPATH . $imagePath);
                }
                $newName = $img->getRandomName();
                $img->move($uploadDir, $newName);
                $imagePath = 'uploads/categories/' . $newName;
            }

            $currentUserId = $this->authLib->getUserId();

            $updateData = [
                'name'           => $name,
                'slug'           => $slug,
                'parent_id'      => $parentId,
                'summary'        => $summary,
                'footer_content' => $footerContent,
                'meta_title'     => $metaTitle,
                'meta_desc'      => $metaDesc,
                'is_active'      => $isActive,
                'image_path'     => $imagePath,
                'image_alt'      => $imageAlt,
                'updated_by'     => $currentUserId
            ];

            // Set creator if empty
            if (empty($category['created_by'])) {
                $updateData['created_by'] = $currentUserId;
            }

            if ($this->categoryModel->update($id, $updateData)) {
                cache()->delete('category_tree_menu');
                $this->logActivity('categories', 'edit', "Updated category: $name ($slug)");
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true]);
                }
                $this->session->setFlashdata('success', 'Category updated successfully.');
                return redirect()->to(base_url('admin/categories'));
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Failed to update category.']);
                }
                $this->session->setFlashdata('error', 'Failed to update category.');
            }
        }

        $data['category'] = $category;
        $data['categories_list'] = $this->categoryModel->where('id !=', $id)->orderBy('name', 'ASC')->findAll();
        $data['title'] = 'Edit Category: ' . $category['name'];
        
        if ($this->request->isAJAX()) {
            return view('admin/categories/edit_partial', $data);
        }
        return view('admin/categories/edit', $data);
    }

    /**
     * Toggle active status.
     */
    public function toggle($id = null)
    {
        $this->checkPermission('categories', 'edit');
        if ($id !== null) {
            $category = $this->categoryModel->find($id);
            if ($category) {
                $newStatus = $category['is_active'] ? 0 : 1;
                $currentUserId = $this->authLib->getUserId();
                $this->categoryModel->update($id, [
                    'is_active' => $newStatus,
                    'updated_by' => $currentUserId
                ]);
                cache()->delete('category_tree_menu');
                $this->logActivity('categories', 'edit', "Toggled active status of category: {$category['name']} (ID: $id) to " . ($newStatus ? 'Active' : 'Inactive'));
                $this->session->setFlashdata('success', 'Category status updated.');
            }
        }
        return redirect()->to(base_url('admin/categories'));
    }

    /**
     * Helper to verify if category can be safely deleted.
     */
    private function canDeleteCategory($id, &$reason = '')
    {
        $db = \Config\Database::connect();
        
        // 1. Check if category has any products mapped
        $productCount = $db->table('product_categories')
            ->join('products', 'products.id = product_categories.product_id')
            ->where('product_categories.category_id', $id)
            ->countAllResults();
            
        if ($productCount > 0) {
            $reason = 'It has ' . $productCount . ' product(s) mapped to it.';
            return false;
        }
        
        // 2. Check if any order is related to products of this category
        $orderCount = $db->table('product_categories')
            ->join('order_items', 'order_items.product_id = product_categories.product_id')
            ->where('product_categories.category_id', $id)
            ->countAllResults();
            
        if ($orderCount > 0) {
            $reason = 'Products in this category have existing customer orders.';
            return false;
        }

        // 3. Check if it has subcategories
        $subcategoriesCount = $this->categoryModel->where('parent_id', $id)->countAllResults();
        if ($subcategoriesCount > 0) {
            $reason = 'It has subcategories mapped under it.';
            return false;
        }
        
        return true;
    }

    /**
     * Delete category.
     */
    public function delete($id = null)
    {
        $this->checkPermission('categories', 'delete');
        if ($id !== null) {
            $reason = '';
            if (!$this->canDeleteCategory($id, $reason)) {
                $this->session->setFlashdata('error', 'Cannot delete category: ' . $reason);
                return redirect()->to(base_url('admin/categories'));
            }

            $category = $this->categoryModel->find($id);
            $categoryName = $category ? $category['name'] : 'ID: ' . $id;

            if ($this->categoryModel->delete($id)) {
                cache()->delete('category_tree_menu');
                $this->logActivity('categories', 'delete', "Deleted category: $categoryName (ID: $id)");
                $this->session->setFlashdata('success', 'Category deleted successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete category.');
            }
        }
        return redirect()->to(base_url('admin/categories'));
    }

    /**
     * Bulk delete categories.
     */
    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No categories selected.']);
        }

        $ids = array_map('intval', $ids);
        $deleted = 0;
        $errors  = 0;
        $skipped = [];

        foreach ($ids as $id) {
            if ($id > 0) {
                $reason = '';
                if ($this->canDeleteCategory($id, $reason)) {
                    if ($this->categoryModel->delete($id)) {
                        $deleted++;
                    } else {
                        $errors++;
                    }
                } else {
                    $cat = $this->categoryModel->find($id);
                    $name = $cat ? $cat['name'] : 'ID: ' . $id;
                    $skipped[] = '"' . $name . '" (' . $reason . ')';
                }
            }
        }

        cache()->delete('category_tree_menu');

        if ($deleted > 0) {
            $message = "$deleted " . ($deleted === 1 ? 'category' : 'categories') . " deleted successfully.";
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
            'message' => 'Failed to delete selected categories. Details: ' . implode('; ', $skipped)
        ]);
    }

    /**
     * AJAX: Check if a slug is already taken.
     * GET admin/categories/check-slug?slug=xxx&id=yyy (id optional for edit)
     */
    public function checkSlug()
    {
        $slug = generate_slug($this->request->getGet('slug') ?? '');
        $id   = (int)($this->request->getGet('id') ?? 0);

        if (empty($slug)) {
            return $this->response->setJSON(['available' => true]);
        }

        $query = $this->categoryModel->where('slug', $slug);
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
