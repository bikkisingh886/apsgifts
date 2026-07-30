<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'slug', 'summary', 'footer_content', 'meta_title', 'meta_desc', 'is_active', 'image_path', 'image_alt', 'parent_id', 'show_in_menu', 'sort_order', 'twitter_card', 'twitter_title', 'twitter_desc', 'twitter_image', 'og_title', 'og_desc', 'og_image', 'og_type', 'schema_markup', 'created_by', 'updated_by', 'updated_at'];

    protected $useTimestamps = false;

    /**
     * Get categories with product counts.
     */
    public function getWithProductCounts(bool $activeOnly = false)
    {
        $builder = $this->db->table('categories c')
            ->select('c.*, creator.name as creator_name, updater.name as updater_name')
            ->select('(SELECT COUNT(DISTINCT pc_sub.product_id) FROM product_categories pc_sub JOIN products p_sub ON p_sub.id = pc_sub.product_id AND p_sub.is_active = 1 WHERE pc_sub.category_id = c.id OR pc_sub.category_id IN (SELECT id FROM categories WHERE parent_id = c.id) OR pc_sub.category_id IN (SELECT id FROM categories WHERE parent_id IN (SELECT id FROM categories WHERE parent_id = c.id))) as product_count')
            ->join('users creator', 'creator.id = c.created_by', 'left')
            ->join('users updater', 'updater.id = c.updated_by', 'left');

        if ($activeOnly) {
            $builder->where('c.is_active', 1);
        }

        $categories = $builder->groupBy('c.id')
            ->orderBy('c.parent_id', 'ASC')
            ->orderBy('c.sort_order', 'ASC')
            ->orderBy('c.name', 'ASC')
            ->get()
            ->getResultArray();

        // Build full parent names path (e.g. "Gifts > Delhi") and full slug paths in PHP
        $mapped = [];
        foreach ($categories as $cat) {
            $mapped[$cat['id']] = $cat;
        }

        foreach ($categories as &$cat) {
            $slugPath = [$cat['slug']];
            $namePath = [];
            
            $currParentId = $cat['parent_id'];
            while ($currParentId && isset($mapped[$currParentId])) {
                $parent = $mapped[$currParentId];
                array_unshift($slugPath, $parent['slug']);
                array_unshift($namePath, $parent['name']);
                $currParentId = $parent['parent_id'];
            }
            
            $cat['slug_path'] = implode('/', $slugPath);
            $cat['parent_name'] = !empty($namePath) ? implode(' > ', $namePath) : '';
        }

        return $categories;
    }

    /**
     * Get category by slug.
     */
    public function getBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get parent IDs for a category.
     */
    public function getParentIds(int $categoryId): array
    {
        $db = \Config\Database::connect();
        $query = $db->table('category_parents')
            ->select('parent_id')
            ->where('category_id', $categoryId)
            ->get()
            ->getResultArray();
        return array_column($query, 'parent_id');
    }

    /**
     * Save parent IDs for a category.
     */
    public function saveParentIds(int $categoryId, array $parentIds): void
    {
        $db = \Config\Database::connect();
        $db->table('category_parents')->where('category_id', $categoryId)->delete();
        
        if (!empty($parentIds)) {
            $insertData = [];
            foreach ($parentIds as $parentId) {
                if ($parentId > 0 && $parentId != $categoryId) {
                    $insertData[] = [
                        'category_id' => $categoryId,
                        'parent_id'   => (int)$parentId
                    ];
                }
            }
            if (!empty($insertData)) {
                $db->table('category_parents')->insertBatch($insertData);
            }
        }
    }

    /**
     * Get categories in a tree hierarchy.
     */
    public function getCategoryTree(bool $activeOnly = false)
    {
        $builder = $this->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        $categories = $builder->findAll();

        $tree = [];
        $mapped = [];

        foreach ($categories as $cat) {
            $cat['children'] = [];
            $mapped[$cat['id']] = $cat;
        }

        foreach ($mapped as $id => &$cat) {
            if ($cat['parent_id'] === null || $cat['parent_id'] == 0 || !isset($mapped[$cat['parent_id']])) {
                $tree[] = &$cat;
            } else {
                $mapped[$cat['parent_id']]['children'][] = &$cat;
            }
        }

        return $tree;
    }

    /**
     * Get descendant category IDs (recursive up to 3 levels: self, child, grandchild).
     */
    public function getDescendantIds(int $categoryId): array
    {
        $ids = [$categoryId];
        
        $children = $this->select('id')->where('parent_id', $categoryId)->findAll();
        if (!empty($children)) {
            $childIds = array_column($children, 'id');
            $ids = array_merge($ids, $childIds);
            
            $grandchildren = $this->select('id')->whereIn('parent_id', $childIds)->findAll();
            if (!empty($grandchildren)) {
                $grandchildIds = array_column($grandchildren, 'id');
                $ids = array_merge($ids, $grandchildIds);
                
                $greatGrandchildren = $this->select('id')->whereIn('parent_id', $grandchildIds)->findAll();
                if (!empty($greatGrandchildren)) {
                    $ids = array_merge($ids, array_column($greatGrandchildren, 'id'));
                }
            }
        }
        
        return $ids;
    }

    /**
     * Get categories in a flat list ordered hierarchically with depth indicator.
     */
    public function getHierarchicalFlatList(bool $activeOnly = false): array
    {
        $categories = $this->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');
        if ($activeOnly) {
            $categories->where('is_active', 1);
        }
        $list = $categories->findAll();

        $tree = [];
        $mapped = [];

        foreach ($list as $cat) {
            $cat['children'] = [];
            $mapped[$cat['id']] = $cat;
        }

        // Build slug paths in memory
        $categoryPathsMap = [];
        foreach ($list as $cat) {
            $slugPath = [$cat['slug']];
            $currParentId = $cat['parent_id'];
            while ($currParentId && isset($mapped[$currParentId])) {
                $parent = $mapped[$currParentId];
                array_unshift($slugPath, $parent['slug']);
                $currParentId = $parent['parent_id'];
            }
            $categoryPathsMap[$cat['id']] = implode('/', $slugPath);
        }

        foreach ($mapped as $id => &$cat) {
            if ($cat['parent_id'] === null || $cat['parent_id'] == 0 || !isset($mapped[$cat['parent_id']])) {
                $tree[] = &$cat;
            } else {
                $mapped[$cat['parent_id']]['children'][] = &$cat;
            }
        }

        $flat = [];
        $this->flattenTree($tree, $flat, 0, $categoryPathsMap);
        return $flat;
    }

    private function flattenTree(array $nodes, array &$flat, int $depth, array $categoryPathsMap): void
    {
        foreach ($nodes as $node) {
            $children = $node['children'];
            unset($node['children']);
            $node['depth'] = $depth;
            $node['slug_path'] = $categoryPathsMap[$node['id']] ?? $node['slug'];
            $flat[] = $node;
            if (!empty($children)) {
                $this->flattenTree($children, $flat, $depth + 1, $categoryPathsMap);
            }
        }
    }
}
