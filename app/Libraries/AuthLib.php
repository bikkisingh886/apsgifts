<?php

namespace App\Libraries;

class AuthLib
{
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    /**
     * Hash password using bcrypt.
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verify password against hash.
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Log in a user.
     */
    public function login(array $user): bool
    {
        if (empty($user) || !$user['is_active']) {
            return false;
        }

        $sessionData = [
            'user_id'     => (int)$user['id'],
            'user_name'   => $user['name'],
            'user_email'  => $user['email'],
            'user_mobile' => $user['mobile'],
            'logged_in'   => true,
            'is_admin'    => ($user['email'] === 'admin@giftshop.in' || !empty($user['role_id']))
        ];

        $this->session->set($sessionData);
        return true;
    }

    /**
     * Check if current user has permission for a module and action.
     */
    public function hasPermission(string $module, string $action): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userId = $this->getUserId();
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();

        if (!$user || !$user['is_active']) {
            return false;
        }

        // Superadmin bypasses all permission checks
        if ($user['email'] === 'admin@giftshop.in') {
            return true;
        }

        // If no role_id, no permissions
        if (empty($user['role_id'])) {
            return false;
        }

        // Fetch permission for role and module
        $perm = $db->table('role_permissions')
            ->where('role_id', $user['role_id'])
            ->where('module', $module)
            ->get()
            ->getRowArray();

        if (!$perm) {
            return false;
        }

        $column = 'can_' . strtolower($action);
        return isset($perm[$column]) && $perm[$column] == 1;
    }

    /**
     * Check if any user is logged in.
     */
    public function isLoggedIn(): bool
    {
        return $this->session->get('logged_in') === true;
    }

    /**
     * Check if the logged-in user is an admin.
     */
    public function isAdmin(): bool
    {
        return ($this->session->get('logged_in') === true && $this->session->get('is_admin') === true);
    }

    /**
     * Require user login. Redirect to login page if not authenticated.
     */
    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->session->setFlashdata('error', 'Login is required.');
            header('Location: ' . base_url('login'));
            exit;
        }
    }

    /**
     * Require admin login. Redirect to login page if not admin.
     */
    public function requireAdmin()
    {
        if (!$this->isAdmin()) {
            $this->session->setFlashdata('error', 'Access denied. Admin privileges required.');
            header('Location: ' . base_url('login'));
            exit;
        }
    }

    /**
     * Get current user ID.
     */
    public function getUserId(): ?int
    {
        return $this->session->get('user_id');
    }

    public function getUser(): array
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return [];
        }
        $db = \Config\Database::connect();
        return $db->table('users')->where('id', $userId)->get()->getRowArray() ?: [];
    }

    /**
     * Log out the user.
     */
    public function logout()
    {
        $this->session->destroy();
    }
}
