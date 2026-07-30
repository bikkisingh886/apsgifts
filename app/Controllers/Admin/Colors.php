<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ColorModel;

class Colors extends BaseController
{
    protected $colorModel;
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->colorModel = new ColorModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display colors management page.
     */
    public function index()
    {
        $this->checkPermission('colors', 'view');
        
        $data['colors'] = $this->colorModel->orderBy('name', 'ASC')->findAll();
        $data['title'] = 'Manage Product Colors';
        return view('admin/colors/index', $data);
    }

    /**
     * Create new product color.
     */
    public function create()
    {
        $this->checkPermission('colors', 'create');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = trim($this->request->getPost('name') ?? '');
            $color_code = trim($this->request->getPost('color_code') ?? '');
            $isActive = (int)($this->request->getPost('is_active') ?? 1);

            if (empty($name)) {
                $this->session->setFlashdata('error', 'Color name is required.');
                return redirect()->to(base_url('admin/colors'));
            }

            // Double check uniqueness of name
            $existing = $this->colorModel->where('name', $name)->first();
            if ($existing) {
                $this->session->setFlashdata('error', 'Color "' . $name . '" already exists.');
                return redirect()->to(base_url('admin/colors'));
            }

            $saveData = [
                'name'       => $name,
                'color_code' => !empty($color_code) ? $color_code : null,
                'is_active'  => $isActive
            ];

            if ($this->colorModel->insert($saveData)) {
                $this->logActivity('colors', 'create', "Added product color: $name");
                $this->session->setFlashdata('success', 'Color added successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to add color.');
            }
        }
        return redirect()->to(base_url('admin/colors'));
    }

    /**
     * Edit color details.
     */
    public function edit($id = null)
    {
        $this->checkPermission('colors', 'edit');
        if ($id === null) {
            return redirect()->to(base_url('admin/colors'));
        }

        $color = $this->colorModel->find($id);
        if (!$color) {
            $this->session->setFlashdata('error', 'Color not found.');
            return redirect()->to(base_url('admin/colors'));
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = trim($this->request->getPost('name') ?? '');
            $color_code = trim($this->request->getPost('color_code') ?? '');
            $isActive = (int)$this->request->getPost('is_active');

            if (empty($name)) {
                $this->session->setFlashdata('error', 'Color name is required.');
                return redirect()->to(base_url('admin/colors/edit/' . $id));
            }

            // Double check uniqueness of name (excluding current color)
            $existing = $this->colorModel->where('name', $name)->where('id !=', $id)->first();
            if ($existing) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Color "' . $name . '" already exists.']);
                }
                $this->session->setFlashdata('error', 'Color "' . $name . '" already exists.');
                return redirect()->to(base_url('admin/colors/edit/' . $id));
            }

            $updateData = [
                'name'       => $name,
                'color_code' => !empty($color_code) ? $color_code : null,
                'is_active'  => $isActive
            ];

            if ($this->colorModel->update($id, $updateData)) {
                $this->logActivity('colors', 'edit', "Updated color: $name");
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true]);
                }
                $this->session->setFlashdata('success', 'Color updated successfully.');
                return redirect()->to(base_url('admin/colors'));
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Failed to update color.']);
                }
                $this->session->setFlashdata('error', 'Failed to update color.');
            }
        }

        $data['color'] = $color;
        $data['title'] = 'Edit Color: ' . $color['name'];

        if ($this->request->isAJAX()) {
            return view('admin/colors/edit_partial', $data);
        }
        return view('admin/colors/edit', $data);
    }

    /**
     * Toggle active status.
     */
    public function toggle($id = null)
    {
        $this->checkPermission('colors', 'edit');
        if ($id !== null) {
            $color = $this->colorModel->find($id);
            if ($color) {
                $newStatus = $color['is_active'] ? 0 : 1;
                $this->colorModel->update($id, [
                    'is_active' => $newStatus
                ]);
                $this->logActivity('colors', 'edit', "Toggled active status of color: {$color['name']} (ID: $id) to " . ($newStatus ? 'Active' : 'Inactive'));
                $this->session->setFlashdata('success', 'Color status updated successfully.');
            }
        }
        return redirect()->to(base_url('admin/colors'));
    }

    /**
     * Delete color.
     */
    public function delete($id = null)
    {
        $this->checkPermission('colors', 'delete');
        if ($id !== null) {
            $color = $this->colorModel->find($id);
            if ($color) {
                $this->colorModel->delete($id);
                $this->logActivity('colors', 'delete', "Deleted color: {$color['name']} (ID: $id)");
                $this->session->setFlashdata('success', 'Color deleted successfully.');
            }
        }
        return redirect()->to(base_url('admin/colors'));
    }
}
