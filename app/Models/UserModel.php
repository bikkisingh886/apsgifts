<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'email', 'mobile', 'password', 'is_active', 'created_at', 'role_id', 'created_by', 'updated_by', 'updated_at', 'reset_token', 'reset_expires_at'];

    protected $useTimestamps = false;

    /**
     * Get user by email.
     */
    public function getByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
