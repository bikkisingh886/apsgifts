<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    protected $settingModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->settingModel = new SettingModel();
    }

    /**
     * Show site settings form.
     */
    public function index()
    {
        $this->checkPermission('settings', 'view');
        $settings = [];
        foreach ($this->settingModel->findAll() as $row) {
            $settings[$row['key']] = $row['value'];
        }
        
        $data['settings'] = $settings;
        $data['title'] = 'Site Settings';
        
        return view('admin/settings/index', $data);
    }

    /**
     * Update settings.
     */
    public function update()
    {
        $this->checkPermission('settings', 'edit');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $fields = [
                'company_name',
                'company_phone',
                'company_email',
                'company_address',
                'company_working_hours',
                'announcement_text',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'youtube_url',
                'linkedin_url',
                'pinterest_url',
                'express_shipping_info',
                'courier_shipping_info',
                'global_discount_active',
                'global_discount_threshold',
                'global_discount_value',
                'global_discount_type'
            ];

            $currentUserId = $this->authLib->getUserId();

            foreach ($fields as $field) {
                $val = $this->request->getPost($field);
                $this->settingModel->save([
                    'key'   => $field,
                    'value' => $val !== null ? trim($val) : '',
                    'updated_by' => $currentUserId
                ]);
            }

            // Handle Logo Upload
            $logoFile = $this->request->getFile('company_logo');
            if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
                // Get existing logo to delete it
                $existingLogo = get_setting('company_logo');
                
                $newName = $logoFile->getRandomName();
                if ($logoFile->move(FCPATH . 'uploads', $newName)) {
                    $logoPath = 'uploads/' . $newName;
                    $this->settingModel->save([
                        'key'   => 'company_logo',
                        'value' => $logoPath,
                        'updated_by' => $currentUserId
                    ]);

                    // Remove old logo file if it exists and is under uploads
                    if (!empty($existingLogo) && file_exists(FCPATH . $existingLogo)) {
                        @unlink(FCPATH . $existingLogo);
                    }
                }
            }

            // Check if logo should be deleted
            if ($this->request->getPost('delete_logo') == 1) {
                $existingLogo = get_setting('company_logo');
                $this->settingModel->save([
                    'key'   => 'company_logo',
                    'value' => '',
                    'updated_by' => $currentUserId
                ]);
                if (!empty($existingLogo) && file_exists(FCPATH . $existingLogo)) {
                    @unlink(FCPATH . $existingLogo);
                }
            }

            // Clear cache to reflect dynamic updates instantly
            cache()->clean();

            $this->logActivity('settings', 'edit', "Updated global site configurations");
            return redirect()->to(base_url('admin/settings'))->with('success', 'Site settings updated successfully.');
        }

        return redirect()->to(base_url('admin/settings'));
    }
}
