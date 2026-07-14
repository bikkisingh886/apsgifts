<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SeoPages extends BaseController
{
    protected $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->checkPermission('seo_pages', 'view');
        $this->db = \Config\Database::connect();
    }

    /**
     * List all static pages for SEO config
     */
    public function index()
    {
        $builder = $this->db->table('seo_pages p')
            ->select('p.*, creator.name as creator_name, updater.name as updater_name')
            ->join('users creator', 'creator.id = p.created_by', 'left')
            ->join('users updater', 'updater.id = p.updated_by', 'left')
            ->orderBy('p.id', 'ASC');

        $data['pages'] = $builder->get()->getResultArray();
        $data['title'] = 'Static Pages SEO Manager';

        return view('admin/seo_pages/index', $data);
    }

    /**
     * Edit SEO tags for a static page
     */
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to(base_url('admin/seo-pages'));
        }

        $page = $this->db->table('seo_pages')->where('id', $id)->get()->getRowArray();
        if (!$page) {
            $this->session->setFlashdata('error', 'Page not found.');
            return redirect()->to(base_url('admin/seo-pages'));
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $this->checkPermission('seo_pages', 'edit');

            $metaTitle = $this->request->getPost('meta_title');
            $metaDesc = $this->request->getPost('meta_desc');
            $twitterCard = $this->request->getPost('twitter_card') ?? 'summary_large_image';
            $twitterTitle = $this->request->getPost('twitter_title');
            $twitterDesc = $this->request->getPost('twitter_desc');
            $ogTitle = $this->request->getPost('og_title');
            $ogDesc = $this->request->getPost('og_desc');
            $ogType = $this->request->getPost('og_type') ?? 'website';
            $schemaMarkup = $this->request->getPost('schema_markup');

            $currentUserId = $this->authLib->getUserId();

            // Handle Twitter Image upload
            $twitterImage = $page['twitter_image'];
            $twImgFile = $this->request->getFile('twitter_image_file');
            if ($twImgFile && $twImgFile->isValid() && !$twImgFile->hasMoved()) {
                $newName = $twImgFile->getRandomName();
                if ($twImgFile->move(FCPATH . 'uploads/seo', $newName)) {
                    $twitterImage = 'uploads/seo/' . $newName;
                }
            }

            // Handle OG Image upload
            $ogImage = $page['og_image'];
            $ogImgFile = $this->request->getFile('og_image_file');
            if ($ogImgFile && $ogImgFile->isValid() && !$ogImgFile->hasMoved()) {
                $newName = $ogImgFile->getRandomName();
                if ($ogImgFile->move(FCPATH . 'uploads/seo', $newName)) {
                    $ogImage = 'uploads/seo/' . $newName;
                }
            }

            $updateData = [
                'meta_title'    => $metaTitle,
                'meta_desc'     => $metaDesc,
                'twitter_card'  => $twitterCard,
                'twitter_title' => $twitterTitle,
                'twitter_desc'  => $twitterDesc,
                'twitter_image' => $twitterImage,
                'og_title'      => $ogTitle,
                'og_desc'       => $ogDesc,
                'og_image'      => $ogImage,
                'og_type'       => $ogType,
                'schema_markup' => $schemaMarkup,
                'updated_by'    => $currentUserId
            ];

            // If page had no creator, set it
            if (empty($page['created_by'])) {
                $updateData['created_by'] = $currentUserId;
            }

            $this->db->table('seo_pages')->where('id', $id)->update($updateData);

            // Clean cache to reload SEO tags instantly
            cache()->clean();

            $this->logActivity('seo_pages', 'edit', "Updated SEO configurations for page: {$page['page_name']}");
            $this->session->setFlashdata('success', 'SEO tags updated successfully.');

            return redirect()->to(base_url('admin/seo-pages'));
        }

        $data['page'] = $page;
        $data['title'] = 'Edit SEO Settings - ' . esc($page['page_name']);

        return view('admin/seo_pages/edit', $data);
    }
}
