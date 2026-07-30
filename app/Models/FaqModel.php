<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category', 'question', 'answer', 'sort_order', 'is_active', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get active FAQs ordered by sort_order
     */
    public function getActiveFaqs(string $category = null)
    {
        $builder = $this->where('is_active', 1);
        if ($category && $category !== 'all') {
            $builder->where('category', $category);
        }
        return $builder->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
    }

    /**
     * Get unique active categories list
     */
    public function getCategories()
    {
        $rows = $this->select('category')
                     ->where('is_active', 1)
                     ->groupBy('category')
                     ->orderBy('category', 'ASC')
                     ->findAll();
        return array_column($rows, 'category');
    }
}
