<?php

namespace App\Models;

use CodeIgniter\Model;

class OfferModel extends Model
{
    protected $table            = 'offers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'type', 'value', 'applies_to', 'is_active', 'created_at', 'created_by', 'updated_by', 'updated_at'];

    protected $useTimestamps = false;

    /**
     * Get all active offers.
     */
    public function getActive()
    {
        return $this->where('is_active', 1)->findAll();
    }
}
