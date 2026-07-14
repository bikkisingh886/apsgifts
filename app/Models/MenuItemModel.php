<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuItemModel extends Model
{
    protected $table            = 'menu_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'menu_id', 
        'parent_id', 
        'title', 
        'type', 
        'object_id', 
        'url', 
        'target', 
        'is_mega_menu', 
        'sort_order',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at'
    ];

    protected $useTimestamps = false;

    /**
     * Get menu items for a specific menu, sorted.
     */
    public function getMenuItems(int $menuId)
    {
        return $this->where('menu_id', $menuId)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }
}
