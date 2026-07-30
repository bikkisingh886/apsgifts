<?php

namespace App\Models;

use CodeIgniter\Model;

class ColorModel extends Model
{
    protected $table            = 'colors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'color_code', 'is_active', 'created_at'];

    protected $useTimestamps = false;

    /**
     * Get active colors.
     */
    public function getActiveColors()
    {
        return $this->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
