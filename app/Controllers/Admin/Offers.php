<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OfferModel;

class Offers extends BaseController
{
    protected $offerModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->offerModel = new OfferModel();
    }

    /**
     * Display offers page.
     */
    public function index()
    {
        $this->checkPermission('offers', 'view');
        $db = \Config\Database::connect();
        $builder = $db->table('offers o')
            ->select('o.*, creator.name as creator_name, updater.name as updater_name')
            ->join('users creator', 'creator.id = o.created_by', 'left')
            ->join('users updater', 'updater.id = o.updated_by', 'left')
            ->orderBy('o.id', 'DESC');

        $data['offers'] = $builder->get()->getResultArray();
        $data['title'] = 'Manage Offers';
        return view('admin/offers/index', $data);
    }

    /**
     * Create offer.
     */
    public function create()
    {
        $this->checkPermission('offers', 'create');
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $type = $this->request->getPost('type');
            $value = (float)$this->request->getPost('value');
            $appliesTo = $this->request->getPost('applies_to');

            if (empty($name) || empty($value)) {
                $this->session->setFlashdata('error', 'Offer name and value are required.');
                return redirect()->to(base_url('admin/offers'));
            }

            $currentUserId = $this->authLib->getUserId();

            $saveData = [
                'name'       => $name,
                'type'       => $type,
                'value'      => $value,
                'applies_to' => $appliesTo,
                'is_active'  => 1,
                'created_by' => $currentUserId,
                'updated_by' => $currentUserId
            ];

            if ($this->offerModel->insert($saveData)) {
                $this->logActivity('offers', 'create', "Created offer: $name");
                $this->session->setFlashdata('success', 'Offer created successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to create offer.');
            }
        }
        return redirect()->to(base_url('admin/offers'));
    }

    /**
     * Toggle active status.
     */
    public function toggle($id = null)
    {
        $this->checkPermission('offers', 'edit');
        if ($id !== null) {
            $offer = $this->offerModel->find($id);
            if ($offer) {
                $newStatus = $offer['is_active'] ? 0 : 1;
                $currentUserId = $this->authLib->getUserId();
                $this->offerModel->update($id, [
                    'is_active' => $newStatus,
                    'updated_by' => $currentUserId
                ]);
                $this->logActivity('offers', 'edit', "Toggled active status of offer: {$offer['name']} (ID: $id) to " . ($newStatus ? 'Active' : 'Inactive'));
                $this->session->setFlashdata('success', 'Offer status updated.');
            }
        }
        return redirect()->to(base_url('admin/offers'));
    }

    /**
     * Delete offer.
     */
    public function delete($id = null)
    {
        $this->checkPermission('offers', 'delete');
        if ($id !== null) {
            $offer = $this->offerModel->find($id);
            $offerName = $offer ? $offer['name'] : 'ID: ' . $id;

            if ($this->offerModel->delete($id)) {
                $this->logActivity('offers', 'delete', "Deleted offer: $offerName (ID: $id)");
                $this->session->setFlashdata('success', 'Offer deleted successfully.');
            } else {
                $this->session->setFlashdata('error', 'Failed to delete offer.');
            }
        }
        return redirect()->to(base_url('admin/offers'));
    }
}
