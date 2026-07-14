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
            ->select('c.*, pcat.name as parent_name, pcat.slug as parent_slug, COUNT(pc.product_id) as product_count, creator.name as creator_name, updater.name as updater_name')
            ->join('categories pcat', 'pcat.id = c.parent_id', 'left')
            ->join('product_categories pc', 'pc.category_id = c.id', 'left')
            ->join('products p', 'p.id = pc.product_id AND p.is_active = 1', 'left')
            ->join('users creator', 'creator.id = c.created_by', 'left')
            ->join('users updater', 'updater.id = c.updated_by', 'left');

        if ($activeOnly) {
            $builder->where('c.is_active', 1);
        }

        return $builder->groupBy('c.id')
            ->orderBy('c.parent_id', 'ASC')
            ->orderBy('c.sort_order', 'ASC')
            ->orderBy('c.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get category by slug.
     */
    public function getBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
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
}
