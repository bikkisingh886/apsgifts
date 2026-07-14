<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Employees extends BaseController
{
    protected $userModel;
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->checkPermission('employees', 'view');
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * List all employees (users with role_id IS NOT NULL)
     */
    public function index()
    {
        $builder = $this->db->table('users u')
            ->select('u.*, r.name as role_name, creator.name as creator_name, updater.name as updater_name')
            ->join('roles r', 'r.id = u.role_id')
            ->join('users creator', 'creator.id = u.created_by', 'left')
            ->join('users updater', 'updater.id = u.updated_by', 'left')
            ->orderBy('u.id', 'DESC');

        $data['employees'] = $builder->get()->getResultArray();
        $data['title'] = 'Employees Management';

        return view('admin/employees/index', $data);
    }

    /**
     * Create a new employee
     */
    public function create()
    {
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $this->checkPermission('employees', 'create');

            $name = trim($this->request->getPost('name') ?? '');
            $email = trim($this->request->getPost('email') ?? '');
            $mobile = trim($this->request->getPost('mobile') ?? '');
            $password = $this->request->getPost('password');
            $roleId = $this->request->getPost('role_id');
            $isActive = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;

            if (empty($name) || empty($email) || empty($mobile) || empty($password) || empty($roleId)) {
                $this->session->setFlashdata('error', 'Please fill in all required fields.');
                return redirect()->back()->withInput();
            }

            // Check duplicate email
            $existing = $this->userModel->getByEmail($email);
            if ($existing) {
                $this->session->setFlashdata('error', 'A user with this email address already exists.');
                return redirect()->back()->withInput();
            }

            $currentUserId = $this->authLib->getUserId();

            $saveData = [
                'name'       => $name,
                'email'      => $email,
                'mobile'     => $mobile,
                'password'   => $this->authLib->hashPassword($password),
                'role_id'    => (int)$roleId,
                'is_active'  => $isActive,
                'created_by' => $currentUserId,
                'updated_by' => $currentUserId
            ];

            if ($this->userModel->insert($saveData)) {
                $this->logActivity('employees', 'create', "Created employee account: $email");
                $this->session->setFlashdata('success', 'Employee account created successfully.');
                return redirect()->to(base_url('admin/employees'));
            } else {
                $this->session->setFlashdata('error', 'Failed to create employee account.');
                return redirect()->back()->withInput();
            }
        }

        $data['roles'] = $this->db->table('roles')->orderBy('name', 'ASC')->get()->getResultArray();
        $data['title'] = 'Create Employee Account';

        return view('admin/employees/create', $data);
    }

    /**
     * Edit employee account
     */
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to(base_url('admin/employees'));
        }

        $employee = $this->userModel->find($id);
        if (!$employee || empty($employee['role_id'])) {
            $this->session->setFlashdata('error', 'Employee not found.');
            return redirect()->to(base_url('admin/employees'));
        }

        // Prevent modification of main superadmin account via this CRUD to prevent lockouts
        if ($employee['email'] === 'admin@giftshop.in') {
            $this->session->setFlashdata('error', 'The default administrator account cannot be modified here.');
            return redirect()->to(base_url('admin/employees'));
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $this->checkPermission('employees', 'edit');

            $name = trim($this->request->getPost('name') ?? '');
            $email = trim($this->request->getPost('email') ?? '');
            $mobile = trim($this->request->getPost('mobile') ?? '');
            $password = $this->request->getPost('password');
            $roleId = $this->request->getPost('role_id');
            $isActive = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;

            if (empty($name) || empty($email) || empty($mobile) || empty($roleId)) {
                $this->session->setFlashdata('error', 'Please fill in all required fields.');
                return redirect()->back();
            }

            // Check duplicate email
            $existing = $this->db->table('users')->where('email', $email)->where('id !=', $id)->get()->getRowArray();
            if ($existing) {
                $this->session->setFlashdata('error', 'A user with this email address already exists.');
                return redirect()->back();
            }

            $currentUserId = $this->authLib->getUserId();

            $updateData = [
                'name'       => $name,
                'email'      => $email,
                'mobile'     => $mobile,
                'role_id'    => (int)$roleId,
                'is_active'  => $isActive,
                'updated_by' => $currentUserId
            ];

            if (!empty($password)) {
                $updateData['password'] = $this->authLib->hashPassword($password);
            }

            if ($this->userModel->update($id, $updateData)) {
                $this->logActivity('employees', 'edit', "Updated employee account: $email");
                $this->session->setFlashdata('success', 'Employee account updated successfully.');
                return redirect()->to(base_url('admin/employees'));
            } else {
                $this->session->setFlashdata('error', 'Failed to update employee account.');
                return redirect()->back();
            }
        }

        $data['roles'] = $this->db->table('roles')->orderBy('name', 'ASC')->get()->getResultArray();
        $data['employee'] = $employee;
        $data['title'] = 'Edit Employee: ' . esc($employee['name']);

        return view('admin/employees/edit', $data);
    }

    /**
     * Toggle active status
     */
    public function toggle($id = null)
    {
        if ($id !== null) {
            $this->checkPermission('employees', 'edit');

            $employee = $this->userModel->find($id);
            if ($employee && !empty($employee['role_id']) && $employee['email'] !== 'admin@giftshop.in') {
                $newStatus = $employee['is_active'] ? 0 : 1;
                $currentUserId = $this->authLib->getUserId();
                
                $this->userModel->update($id, [
                    'is_active' => $newStatus,
                    'updated_by' => $currentUserId
                ]);
                
                $actionName = $newStatus ? 'activated' : 'deactivated';
                $this->logActivity('employees', 'edit', "Toggled status of employee {$employee['email']} to $actionName");
                $this->session->setFlashdata('success', "Employee account $actionName successfully.");
            }
        }
        return redirect()->to(base_url('admin/employees'));
    }

    /**
     * Delete employee account
     */
    public function delete($id = null)
    {
        if ($id !== null) {
            $this->checkPermission('employees', 'delete');

            $employee = $this->userModel->find($id);
            if ($employee && !empty($employee['role_id'])) {
                if ($employee['email'] === 'admin@giftshop.in') {
                    $this->session->setFlashdata('error', 'The default administrator account cannot be deleted.');
                    return redirect()->to(base_url('admin/employees'));
                }

                // Delete employee activity logs first if needed, or mysql handles it if we don't have constraints,
                // but let's delete them to avoid foreign key issues just in case
                $this->db->table('employee_activity_logs')->where('user_id', $id)->delete();

                if ($this->userModel->delete($id)) {
                    $this->logActivity('employees', 'delete', "Deleted employee account: {$employee['email']}");
                    $this->session->setFlashdata('success', 'Employee account deleted successfully.');
                } else {
                    $this->session->setFlashdata('error', 'Failed to delete employee account.');
                }
            }
        }
        return redirect()->to(base_url('admin/employees'));
    }
}
