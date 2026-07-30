<?php

namespace App\Models;

use CodeIgniter\Model;

class EnquiryModel extends Model
{
    protected $table = 'enquiries';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'phone', 'subject', 'message', 'status', 'ip_address', 'created_at'];
    protected $useTimestamps = false;
}
