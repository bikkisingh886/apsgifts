<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'key';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['key', 'value', 'updated_by', 'updated_at'];

    protected $useTimestamps = false;

    public function __construct()
    {
        parent::__construct();
        $this->initializeTable();
    }

    /**
     * Initializes the settings table if it doesn't exist.
     */
    protected function initializeTable()
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists($this->table)) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'value' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
            $forge->addPrimaryKey('key');
            $forge->createTable($this->table);

            // Insert default site settings
            $defaultSettings = [
                ['key' => 'company_name', 'value' => 'OyeGifts'],
                ['key' => 'company_phone', 'value' => '+91 98765 43210'],
                ['key' => 'company_email', 'value' => 'info@giftshop.in'],
                ['key' => 'company_address', 'value' => 'Bailey Road, Patna, Bihar, India'],
                ['key' => 'company_working_hours', 'value' => 'Mon-Sun (9.00AM - 9.00PM)'],
                ['key' => 'company_logo', 'value' => ''],
                ['key' => 'facebook_url', 'value' => 'https://facebook.com'],
                ['key' => 'instagram_url', 'value' => 'https://instagram.com'],
                ['key' => 'twitter_url', 'value' => 'https://twitter.com'],
                ['key' => 'youtube_url', 'value' => ''],
                ['key' => 'linkedin_url', 'value' => ''],
                ['key' => 'pinterest_url', 'value' => ''],
            ];

            $db->table($this->table)->insertBatch($defaultSettings);
        }
    }
}
