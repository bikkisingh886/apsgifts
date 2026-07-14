<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Activities extends BaseController
{
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->checkPermission('activities', 'view');
        $this->db = \Config\Database::connect();
    }

    /**
     * Display activity log listing
     */
    public function index()
    {
        $employeeId = $this->request->getGet('employee_id');
        $module = $this->request->getGet('module');

        $builder = $this->db->table('employee_activity_logs l')
            ->select('l.*, u.name as employee_name, u.email as employee_email')
            ->join('users u', 'u.id = l.user_id')
            ->orderBy('l.id', 'DESC');

        if (!empty($employeeId)) {
            $builder->where('l.user_id', (int)$employeeId);
        }

        if (!empty($module)) {
            $builder->where('l.module', $module);
        }

        // Paginate using custom pagination/limit for logs as they can grow large
        // We'll fetch the last 200 logs for display to avoid slowdowns
        $data['logs'] = $builder->limit(200)->get()->getResultArray();

        // Get list of employees for filter dropdown
        $data['employees'] = $this->db->table('users')
            ->where('role_id IS NOT NULL')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get unique modules logged for filter dropdown
        $data['modules'] = [
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

        $data['selected_employee'] = $employeeId;
        $data['selected_module'] = $module;
        $data['title'] = 'Employee Activity Logs';

        return view('admin/activities/index', $data);
    }
}
