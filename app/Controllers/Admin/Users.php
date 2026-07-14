<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->userModel = new UserModel();
    }

    /**
     * Display users page.
     */
    public function index()
    {
        $this->checkPermission('users', 'view');
        $data['users'] = $this->userModel->findAll();
        $data['title'] = 'Manage Users';
        return view('admin/users/index', $data);
    }

    /**
     * Toggle active status.
     */
    public function toggle($id = null)
    {
        $this->checkPermission('users', 'edit');
        if ($id !== null && $id != $this->authLib->getUserId()) {
            $user = $this->userModel->find($id);
            if ($user) {
                $newStatus = $user['is_active'] ? 0 : 1;
                $currentUserId = $this->authLib->getUserId();
                $this->userModel->update($id, [
                    'is_active' => $newStatus,
                    'updated_by' => $currentUserId
                ]);
                $this->logActivity('users', 'edit', "Toggled active status of user: {$user['email']} to " . ($newStatus ? 'Active' : 'Inactive'));
                $this->session->setFlashdata('success', 'User status updated successfully.');
            }
        }
        return redirect()->to(base_url('admin/users'));
    }

    /**
     * Delete user record.
     */
    public function delete($id = null)
    {
        $this->checkPermission('users', 'delete');
        if ($id !== null && $id != $this->authLib->getUserId()) {
            $user = $this->userModel->find($id);
            $userEmail = $user ? $user['email'] : 'ID: ' . $id;

            if ($this->userModel->delete($id)) {
                $this->logActivity('users', 'delete', "Deleted user record: $userEmail (ID: $id)");
                $this->session->setFlashdata('success', 'User deleted successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete user.');
            }
        }
        return redirect()->to(base_url('admin/users'));
    }
}
