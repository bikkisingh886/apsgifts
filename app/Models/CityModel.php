<?php

namespace App\Models;

use CodeIgniter\Model;

class CityModel extends Model
{
    protected $table            = 'cities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'slug', 'is_popular', 'is_active', 'created_at', 'created_by', 'updated_by', 'updated_at'];

    protected $useTimestamps = false;

    /**
     * Get active cities sorted by name.
     */
    public function getActiveCities()
    {
        return $this->where('is_active', 1)
                    ->orderBy('is_popular', 'DESC')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
