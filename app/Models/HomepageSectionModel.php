<?php

namespace App\Models;

use CodeIgniter\Model;

class HomepageSectionModel extends Model
{
    protected $table            = 'homepage_sections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['section_key', 'title', 'subtitle', 'content_json', 'sort_order', 'is_active', 'created_by', 'updated_by', 'updated_at'];

    protected $useTimestamps = false;

    /**
     * Get all active homepage sections ordered by sort_order
     */
    public function getActiveSections()
    {
        return $this->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }
}
