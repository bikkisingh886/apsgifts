<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Roles extends BaseController
{
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->checkPermission('employees', 'view'); // Role permission belongs to employees management
        $this->db = \Config\Database::connect();
    }

    /**
     * List all roles
     */
    public function index()
    {
        $data['roles'] = $this->db->table('roles')->orderBy('id', 'ASC')->get()->getResultArray();
        $data['title'] = 'Roles & Permissions';

        // Join to see how many employees belong to each role
        foreach ($data['roles'] as &$role) {
            $role['employee_count'] = $this->db->table('users')->where('role_id', $role['id'])->countAllResults();
        }

        return view('admin/roles/index', $data);
    }

    /**
     * Create a new role
     */
    public function create()
    {
        $modules = [
            'products' => 'Products',
            'categories' => 'Categories',
            'cities' => 'Delivery Cities',
            'offers' => 'Offers',
            'menus' => 'Menu Manager',
            'homepage' => 'Homepage Manager',
            'orders' => 'Orders',
            'users' => 'Users / Customers',
            'settings' => 'Settings',
            'coupons' => 'Coupon Codes',
            'reviews' => 'Product Reviews',
            'employees' => 'Employees Manager',
            'activities' => 'Activity Logs',
            'seo_pages' => 'SEO Pages Manager'
        ];

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $this->checkPermission('employees', 'create');

            $name = trim($this->request->getPost('name') ?? '');
            $description = trim($this->request->getPost('description') ?? '');

            if (empty($name)) {
                $this->session->setFlashdata('error', 'Role name is required.');
                return redirect()->back()->withInput();
            }

            // Check duplicate
            $existing = $this->db->table('roles')->where('name', $name)->get()->getRowArray();
            if ($existing) {
                $this->session->setFlashdata('error', 'Role with this name already exists.');
                return redirect()->back()->withInput();
            }

            $this->db->transStart();

            // Insert role
            $this->db->table('roles')->insert([
                'name' => $name,
                'description' => $description
            ]);
            $roleId = $this->db->insertID();

            // Insert permissions
            $perms = $this->request->getPost('perms') ?? [];
            foreach (array_keys($modules) as $mod) {
                $canView = isset($perms[$mod]['view']) ? 1 : 0;
                $canCreate = isset($perms[$mod]['create']) ? 1 : 0;
                $canEdit = isset($perms[$mod]['edit']) ? 1 : 0;
                $canDelete = isset($perms[$mod]['delete']) ? 1 : 0;

                $this->db->table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'module' => $mod,
                    'can_view' => $canView,
                    'can_create' => $canCreate,
                    'can_edit' => $canEdit,
                    'can_delete' => $canDelete
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->session->setFlashdata('error', 'Failed to create role.');
            } else {
                $this->logActivity('employees', 'create', "Created role: $name");
                $this->session->setFlashdata('success', 'Role and permissions created successfully.');
            }

            return redirect()->to(base_url('admin/roles'));
        }

        $data['modules'] = $modules;
        $data['title'] = 'Create New Role';

        return view('admin/roles/create', $data);
    }

    /**
     * Edit a role and its permissions
     */
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to(base_url('admin/roles'));
        }

        $role = $this->db->table('roles')->where('id', $id)->get()->getRowArray();
        if (!$role) {
            $this->session->setFlashdata('error', 'Role not found.');
            return redirect()->to(base_url('admin/roles'));
        }

        // Prevent modification of default Admin role to avoid lockout
        if ($role['name'] === 'Admin') {
            $this->session->setFlashdata('error', 'Default Admin role cannot be edited.');
            return redirect()->to(base_url('admin/roles'));
        }

        $modules = [
            'products' => 'Products',
            'categories' => 'Categories',
            'cities' => 'Delivery Cities',
            'offers' => 'Offers',
            'menus' => 'Menu Manager',
            'homepage' => 'Homepage Manager',
            'orders' => 'Orders',
            'users' => 'Users / Customers',
            'settings' => 'Settings',
            'coupons' => 'Coupon Codes',
            'reviews' => 'Product Reviews',
            'employees' => 'Employees Manager',
            'activities' => 'Activity Logs',
            'seo_pages' => 'SEO Pages Manager'
        ];

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $this->checkPermission('employees', 'edit');

            $name = trim($this->request->getPost('name') ?? '');
            $description = trim($this->request->getPost('description') ?? '');

            if (empty($name)) {
                $this->session->setFlashdata('error', 'Role name is required.');
                return redirect()->back();
            }

            // Check duplicate excluding self
            $existing = $this->db->table('roles')->where('name', $name)->where('id !=', $id)->get()->getRowArray();
            if ($existing) {
                $this->session->setFlashdata('error', 'Role with this name already exists.');
                return redirect()->back();
            }

            $this->db->transStart();

            // Update role
            $this->db->table('roles')->where('id', $id)->update([
                'name' => $name,
                'description' => $description
            ]);

            // Delete old permissions and insert new ones
            $this->db->table('role_permissions')->where('role_id', $id)->delete();

            $perms = $this->request->getPost('perms') ?? [];
            foreach (array_keys($modules) as $mod) {
                $canView = isset($perms[$mod]['view']) ? 1 : 0;
                $canCreate = isset($perms[$mod]['create']) ? 1 : 0;
                $canEdit = isset($perms[$mod]['edit']) ? 1 : 0;
                $canDelete = isset($perms[$mod]['delete']) ? 1 : 0;

                $this->db->table('role_permissions')->insert([
                    'role_id' => $id,
                    'module' => $mod,
                    'can_view' => $canView,
                    'can_create' => $canCreate,
                    'can_edit' => $canEdit,
                    'can_delete' => $canDelete
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $this->session->setFlashdata('error', 'Failed to update role.');
            } else {
                $this->logActivity('employees', 'edit', "Updated role: $name");
                $this->session->setFlashdata('success', 'Role and permissions updated successfully.');
            }

            return redirect()->to(base_url('admin/roles'));
        }

        $permsList = $this->db->table('role_permissions')->where('role_id', $id)->get()->getResultArray();
        $perms = [];
        foreach ($permsList as $p) {
            $perms[$p['module']] = $p;
        }

        $data['role'] = $role;
        $data['perms'] = $perms;
        $data['modules'] = $modules;
        $data['title'] = 'Edit Role: ' . esc($role['name']);

        return view('admin/roles/edit', $data);
    }

    /**
     * Delete a role
     */
    public function delete($id = null)
    {
        if ($id === null) {
            return redirect()->to(base_url('admin/roles'));
        }

        $role = $this->db->table('roles')->where('id', $id)->get()->getRowArray();
        if (!$role) {
            $this->session->setFlashdata('error', 'Role not found.');
            return redirect()->to(base_url('admin/roles'));
        }

        // Prevent delete Admin or Manager default roles
        if (in_array($role['name'], ['Admin', 'Manager'])) {
            $this->session->setFlashdata('error', 'Default roles (Admin, Manager) cannot be deleted.');
            return redirect()->to(base_url('admin/roles'));
        }

        // Prevent delete if assigned to users
        $inUse = $this->db->table('users')->where('role_id', $id)->countAllResults();
        if ($inUse > 0) {
            $this->session->setFlashdata('error', 'This role is currently assigned to ' . $inUse . ' employee(s) and cannot be deleted.');
            return redirect()->to(base_url('admin/roles'));
        }

        $this->db->transStart();
        $this->db->table('role_permissions')->where('role_id', $id)->delete();
        $this->db->table('roles')->where('id', $id)->delete();
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            $this->session->setFlashdata('error', 'Failed to delete role.');
        } else {
            $this->logActivity('employees', 'delete', "Deleted role: {$role['name']}");
            $this->session->setFlashdata('success', 'Role deleted successfully.');
        }

        return redirect()->to(base_url('admin/roles'));
    }
}
