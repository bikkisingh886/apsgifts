<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CityModel;

class Cities extends BaseController
{
    protected $cityModel;
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->cityModel = new CityModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display cities management page.
     */
    public function index()
    {
        $this->checkPermission('cities', 'view');
        $builder = $this->db->table('cities c')
            ->select('c.*, creator.name as creator_name, updater.name as updater_name')
            ->join('users creator', 'creator.id = c.created_by', 'left')
            ->join('users updater', 'updater.id = c.updated_by', 'left')
            ->orderBy('c.is_popular', 'DESC')
            ->orderBy('c.name', 'ASC');

        $data['cities'] = $builder->get()->getResultArray();
        $data['title'] = 'Manage Delivery Cities';
        return view('admin/cities/index', $data);
    }

    /**
     * Create new delivery city.
     */
    public function create()
    {
        $this->checkPermission('cities', 'create');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $slug = $this->request->getPost('slug');
            $isPopular = (int)($this->request->getPost('is_popular') ?? 0);
            $isActive = (int)($this->request->getPost('is_active') ?? 1);

            if (empty($name)) {
                $this->session->setFlashdata('error', 'City name is required.');
                return redirect()->to(base_url('admin/cities'));
            }

            $slug = !empty($slug) ? generate_slug($slug) : generate_slug($name);

            // Double check uniqueness of slug
            $existing = $this->cityModel->where('slug', $slug)->first();
            if ($existing) {
                $this->session->setFlashdata('error', 'Slug "' . $slug . '" is already in use by another city.');
                return redirect()->to(base_url('admin/cities'));
            }

            $currentUserId = $this->authLib->getUserId();

            $saveData = [
                'name'       => $name,
                'slug'       => $slug,
                'is_popular' => $isPopular,
                'is_active'  => $isActive,
                'created_by' => $currentUserId,
                'updated_by' => $currentUserId
            ];

            if ($this->cityModel->insert($saveData)) {
                $this->logActivity('cities', 'create', "Added delivery city: $name");
                $this->session->setFlashdata('success', 'City added successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to add city.');
            }
        }
        return redirect()->to(base_url('admin/cities'));
    }

    /**
     * Edit delivery city details.
     */
    public function edit($id = null)
    {
        $this->checkPermission('cities', 'edit');
        if ($id === null) {
            return redirect()->to(base_url('admin/cities'));
        }

        $city = $this->cityModel->find($id);
        if (!$city) {
            $this->session->setFlashdata('error', 'City not found.');
            return redirect()->to(base_url('admin/cities'));
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $slug = $this->request->getPost('slug');
            $isPopular = (int)$this->request->getPost('is_popular');
            $isActive = (int)$this->request->getPost('is_active');

            if (empty($name)) {
                $this->session->setFlashdata('error', 'City name is required.');
                return redirect()->to(base_url('admin/cities/edit/' . $id));
            }

            $slug = !empty($slug) ? generate_slug($slug) : generate_slug($name);

            // Double check uniqueness of slug (excluding current city)
            $existing = $this->cityModel->where('slug', $slug)->where('id !=', $id)->first();
            if ($existing) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Slug "' . $slug . '" is already in use by another city.']);
                }
                $this->session->setFlashdata('error', 'Slug "' . $slug . '" is already in use by another city.');
                return redirect()->to(base_url('admin/cities/edit/' . $id));
            }

            $currentUserId = $this->authLib->getUserId();

            $updateData = [
                'name'       => $name,
                'slug'       => $slug,
                'is_popular' => $isPopular,
                'is_active'  => $isActive,
                'updated_by' => $currentUserId
            ];

            if (empty($city['created_by'])) {
                $updateData['created_by'] = $currentUserId;
            }

            if ($this->cityModel->update($id, $updateData)) {
                $this->logActivity('cities', 'edit', "Updated delivery city: $name");
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true]);
                }
                $this->session->setFlashdata('success', 'City updated successfully.');
                return redirect()->to(base_url('admin/cities'));
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'error' => 'Failed to update city.']);
                }
                $this->session->setFlashdata('error', 'Failed to update city.');
            }
        }

        $data['city'] = $city;
        $data['title'] = 'Edit City: ' . $city['name'];

        if ($this->request->isAJAX()) {
            return view('admin/cities/edit_partial', $data);
        }
        return view('admin/cities/edit', $data);
    }

    /**
     * Toggle active status.
     */
    public function toggle($id = null)
    {
        $this->checkPermission('cities', 'edit');
        if ($id !== null) {
            $city = $this->cityModel->find($id);
            if ($city) {
                $newStatus = $city['is_active'] ? 0 : 1;
                $currentUserId = $this->authLib->getUserId();
                $this->cityModel->update($id, [
                    'is_active' => $newStatus,
                    'updated_by' => $currentUserId
                ]);
                $this->logActivity('cities', 'edit', "Toggled active status of city: {$city['name']} (ID: $id) to " . ($newStatus ? 'Active' : 'Inactive'));
                $this->session->setFlashdata('success', 'City status updated successfully.');
            }
        }
        return redirect()->to(base_url('admin/cities'));
    }

    /**
     * Delete delivery city.
     */
    public function delete($id = null)
    {
        $this->checkPermission('cities', 'delete');
        if ($id !== null) {
            $city = $this->cityModel->find($id);
            $cityName = $city ? $city['name'] : 'ID: ' . $id;

            if ($this->cityModel->delete($id)) {
                $this->logActivity('cities', 'delete', "Deleted city: $cityName (ID: $id)");
                $this->session->setFlashdata('success', 'City deleted successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete city.');
            }
        }
        return redirect()->to(base_url('admin/cities'));
    }

    /**
     * AJAX: Check if a city slug is already taken.
     */
    public function checkSlug()
    {
        $this->checkPermission('cities', 'view');
        $slug = generate_slug($this->request->getGet('slug') ?? '');
        $id   = (int)($this->request->getGet('id') ?? 0);

        if (empty($slug)) {
            return $this->response->setJSON(['available' => true]);
        }

        $query = $this->cityModel->where('slug', $slug);
        if ($id > 0) {
            $query->where('id !=', $id);
        }
        $exists = $query->first();

        return $this->response->setJSON([
            'available' => $exists === null,
            'slug'      => $slug
        ]);
    }
}
