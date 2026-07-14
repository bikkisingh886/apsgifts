<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    protected $session;
    protected $authLib;
    protected $cartLib;

    protected $helpers = ['form', 'url', 'html', 'delivery', 'slug', 'cookie', 'setting'];

    /**
     * @return void
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
        $this->authLib = new \App\Libraries\AuthLib();
        $this->cartLib = new \App\Libraries\CartLib();

        // Load selected city from cookie if session is empty
        if (!session()->has('selected_city_id')) {
            $cookieCityId = get_cookie('selected_city_id');
            if ($cookieCityId) {
                session()->set('selected_city_id', (int)$cookieCityId);
                session()->set('selected_city_name', get_cookie('selected_city_name'));
                session()->set('selected_city_slug', get_cookie('selected_city_slug'));
            }
        }
    }

    /**
     * Helper to verify permission or redirect if denied
     */
    protected function checkPermission(string $module, string $action)
    {
        if (!$this->authLib->hasPermission($module, $action)) {
            $this->session->setFlashdata('error', 'Access denied. You do not have permission to ' . $action . ' ' . $module . '.');
            header('Location: ' . base_url('admin/dashboard'));
            exit;
        }
    }

    /**
     * Helper to log employee activity in the backend
     */
    protected function logActivity(string $module, string $action, string $details = null)
    {
        $userId = $this->authLib->getUserId();
        if (!$userId) {
            return;
        }

        $db = \Config\Database::connect();
        $db->table('employee_activity_logs')->insert([
            'user_id'    => $userId,
            'module'     => $module,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => (string)$this->request->getUserAgent()
        ]);
    }
}
