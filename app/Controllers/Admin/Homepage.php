<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\HomepageSectionModel;
use App\Models\CategoryModel;

class Homepage extends BaseController
{
    protected $sectionModel;
    protected $categoryModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->authLib->requireAdmin();
        $this->sectionModel = new HomepageSectionModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * List all homepage sections
     */
    public function index()
    {
        $this->checkPermission('homepage', 'view');
        $data['sections'] = $this->sectionModel->orderBy('sort_order', 'ASC')->findAll();
        $data['meta_title'] = 'Homepage Manager | Admin Dashboard';
        return view('admin/homepage/index', $data);
    }

    /**
     * Toggle section status (Active/Inactive)
     */
    public function toggle($id)
    {
        $this->checkPermission('homepage', 'edit');
        $section = $this->sectionModel->find($id);
        if ($section) {
            $newStatus = $section['is_active'] ? 0 : 1;
            $currentUserId = $this->authLib->getUserId();
            $this->sectionModel->update($id, [
                'is_active' => $newStatus,
                'updated_by' => $currentUserId
            ]);
            $this->logActivity('homepage', 'edit', "Toggled active status of homepage section: {$section['title']} (Key: {$section['section_key']}) to " . ($newStatus ? 'Active' : 'Inactive'));
            return redirect()->to(base_url('admin/homepage'))->with('success', 'Section status updated successfully.');
        }
        return redirect()->to(base_url('admin/homepage'))->with('error', 'Section not found.');
    }

    /**
     * Update section order (AJAX)
     */
    public function updateOrder()
    {
        $this->checkPermission('homepage', 'edit');
        $ids = $this->request->getPost('ids');
        if (!empty($ids) && is_array($ids)) {
            $currentUserId = $this->authLib->getUserId();
            foreach ($ids as $index => $id) {
                $this->sectionModel->update($id, [
                    'sort_order' => ($index + 1) * 10,
                    'updated_by' => $currentUserId
                ]);
            }
            $this->logActivity('homepage', 'edit', "Reordered homepage sections");
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'error' => 'Invalid order data.']);
    }

    /**
     * Edit section details
     */
    public function edit($id)
    {
        $this->checkPermission('homepage', 'edit');
        $section = $this->sectionModel->find($id);
        if (!$section) {
            return redirect()->to(base_url('admin/homepage'))->with('error', 'Section not found.');
        }

        $data['section'] = $section;
        $data['content'] = json_decode($section['content_json'] ?? '{}', true);
        $data['categories'] = $this->categoryModel->where('is_active', 1)->orderBy('name', 'ASC')->findAll();
        $data['meta_title'] = 'Edit Homepage Section - ' . esc($section['title']);
        
        return view('admin/homepage/edit', $data);
    }

    /**
     * Save/Update section details
     */
    public function update($id)
    {
        $this->checkPermission('homepage', 'edit');
        $section = $this->sectionModel->find($id);
        if (!$section) {
            return redirect()->to(base_url('admin/homepage'))->with('error', 'Section not found.');
        }

        $title = $this->request->getPost('title');
        $subtitle = $this->request->getPost('subtitle');
        $is_active = $this->request->getPost('is_active') ? 1 : 0;
        $sort_order = (int)$this->request->getPost('sort_order');

        // Extract and process content based on key
        $content = json_decode($section['content_json'] ?? '{}', true);
        $key = $section['section_key'];

        if ($key === 'hero_slider') {
            // Process sidebar banner
            $sidebar = $this->request->getPost('sidebar_banner') ?: [];
            $files = $this->request->getFiles();
            
            if (isset($files['sidebar_banner']['image']) && $files['sidebar_banner']['image']->isValid()) {
                $img = $files['sidebar_banner']['image'];
                $newName = $img->getRandomName();
                if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                    $sidebar['image'] = 'uploads/homepage/' . $newName;
                }
            } else {
                $sidebar['image'] = $sidebar['existing_image'] ?? 'assets/img/banner/hs-1-banner.jpg';
            }
            unset($sidebar['existing_image']);

            // Process slides
            $slides = $this->request->getPost('slides') ?: [];
            
            foreach ($slides as $index => &$slide) {
                // If a new file is uploaded for this slide
                if (isset($files['slides'][$index]['image']) && $files['slides'][$index]['image']->isValid()) {
                    $img = $files['slides'][$index]['image'];
                    $newName = $img->getRandomName();
                    if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                        $slide['image'] = 'uploads/homepage/' . $newName;
                    }
                } else {
                    // Fallback to existing path passed via hidden input
                    $slide['image'] = $slide['existing_image'] ?? '';
                }
                unset($slide['existing_image']);
            }
            
            $finalContent = [
                'sidebar_banner' => $sidebar,
                'slides' => $slides
            ];

        } elseif ($key === 'features') {
            $finalContent = $this->request->getPost('features') ?: [];

        } elseif ($key === 'shop_by_occasion' || $key === 'shop_by_recipient' || $key === 'home_categories') {
            $finalContent = [
                'title'        => $title,
                'subtitle'     => $subtitle,
                'category_ids' => array_map('intval', $this->request->getPost('category_ids') ?: []),
                'view_more_link' => $this->request->getPost('view_more_link')
            ];

        } elseif ($key === 'category_promotional_banners') {
            $banners = $this->request->getPost('banners') ?: [];
            $files = $this->request->getFiles();
            
            foreach ($banners as $index => &$banner) {
                if (isset($files['banners'][$index]['image']) && $files['banners'][$index]['image']->isValid()) {
                    $img = $files['banners'][$index]['image'];
                    $newName = $img->getRandomName();
                    if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                        $banner['image'] = 'uploads/homepage/' . $newName;
                    }
                } else {
                    $banner['image'] = $banner['existing_image'] ?? '';
                }
                unset($banner['existing_image']);
            }
            $finalContent = $banners;

        } elseif ($key === 'delivery_banner') {
            $banner = $this->request->getPost('banner') ?: [];
            $files = $this->request->getFiles();
            
            if (isset($files['banner_image']) && $files['banner_image']->isValid()) {
                $img = $files['banner_image'];
                $newName = $img->getRandomName();
                if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                    $banner['image'] = 'uploads/homepage/' . $newName;
                }
            } else {
                $banner['image'] = $this->request->getPost('existing_image') ?? '';
            }
            $finalContent = $banner;

        } elseif ($key === 'two_column_banners') {
            $banners = $this->request->getPost('two_banners') ?: [];
            $files = $this->request->getFiles();
            
            // Process Banner 1
            if (isset($files['banner_1']['image']) && $files['banner_1']['image']->isValid()) {
                $img = $files['banner_1']['image'];
                $newName = $img->getRandomName();
                if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                    $banners['banner_1']['image'] = 'uploads/homepage/' . $newName;
                }
            } else {
                $banners['banner_1']['image'] = $banners['banner_1']['existing_image'] ?? '';
            }
            unset($banners['banner_1']['existing_image']);

            // Process Banner 2
            if (isset($files['banner_2']['image']) && $files['banner_2']['image']->isValid()) {
                $img = $files['banner_2']['image'];
                $newName = $img->getRandomName();
                if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                    $banners['banner_2']['image'] = 'uploads/homepage/' . $newName;
                }
            } else {
                $banners['banner_2']['image'] = $banners['banner_2']['existing_image'] ?? '';
            }
            unset($banners['banner_2']['existing_image']);

            $finalContent = $banners;

        } elseif ($key === 'trending_items' || $key === 'popular_items' || $key === 'weekly_deals') {
            $finalContent = [
                'title'          => $title,
                'subtitle'       => $subtitle,
                'limit'          => (int)$this->request->getPost('limit'),
                'category_id'    => $this->request->getPost('category_id') ? (int)$this->request->getPost('category_id') : null,
                'countdown_date' => $this->request->getPost('countdown_date'),
                'view_more_link' => $this->request->getPost('view_more_link')
            ];

        } elseif ($key === 'personalized_gifts') {
            $finalContent = [
                'title'       => $title,
                'subtitle'    => $subtitle,
                'category_id' => $this->request->getPost('category_id') ? (int)$this->request->getPost('category_id') : null,
                'limit'       => (int)$this->request->getPost('limit'),
                'view_more_link' => $this->request->getPost('view_more_link')
            ];

        } elseif ($key === 'promo_video') {
            $video = $this->request->getPost('video') ?: [];
            $files = $this->request->getFiles();
            
            if (isset($files['video_image']) && $files['video_image']->isValid()) {
                $img = $files['video_image'];
                $newName = $img->getRandomName();
                if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                    $video['image'] = 'uploads/homepage/' . $newName;
                }
            } else {
                $video['image'] = $this->request->getPost('existing_image') ?? '';
            }
            $finalContent = $video;

        } elseif ($key === 'about_us') {
            $about = $this->request->getPost('about') ?: [];
            $files = $this->request->getFiles();
            
            if (isset($files['about_image']) && $files['about_image']->isValid()) {
                $img = $files['about_image'];
                $newName = $img->getRandomName();
                if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                    $about['image'] = 'uploads/homepage/' . $newName;
                }
            } else {
                $about['image'] = $this->request->getPost('existing_image') ?? '';
            }
            $finalContent = $about;

        } elseif ($key === 'why_choose_us') {
            $finalContent = [
                'title'   => $title,
                'subtitle'=> $subtitle,
                'reasons' => $this->request->getPost('reasons') ?: []
            ];

        } elseif ($key === 'testimonials') {
            $testimonials = $this->request->getPost('testimonials') ?: [];
            $files = $this->request->getFiles();
            
            foreach ($testimonials as $index => &$testi) {
                if (isset($files['testimonials'][$index]['image']) && $files['testimonials'][$index]['image']->isValid()) {
                    $img = $files['testimonials'][$index]['image'];
                    $newName = $img->getRandomName();
                    if ($img->move(FCPATH . 'uploads/homepage', $newName)) {
                        $testi['image'] = 'uploads/homepage/' . $newName;
                    }
                } else {
                    $testi['image'] = $testi['existing_image'] ?? '';
                }
                unset($testi['existing_image']);
            }
            $finalContent = $testimonials;

        } elseif ($key === 'photo_gallery') {
            $images = $this->request->getPost('gallery') ?: [];
            $files = $this->request->getFiles();
            
            foreach ($images as $index => &$imgItem) {
                if (isset($files['gallery'][$index]['image']) && $files['gallery'][$index]['image']->isValid()) {
                    $imgFile = $files['gallery'][$index]['image'];
                    $newName = $imgFile->getRandomName();
                    if ($imgFile->move(FCPATH . 'uploads/homepage', $newName)) {
                        $imgItem['image'] = 'uploads/homepage/' . $newName;
                    }
                } else {
                    $imgItem['image'] = $imgItem['existing_image'] ?? '';
                }
                unset($imgItem['existing_image']);
            }
            $finalContent = $images;

        } elseif ($key === 'blog') {
            $blog = $this->request->getPost('blog') ?: [];
            $files = $this->request->getFiles();
            $articles = $blog['articles'] ?? [];
            
            foreach ($articles as $index => &$art) {
                if (isset($files['blog']['articles'][$index]['image']) && $files['blog']['articles'][$index]['image']->isValid()) {
                    $imgFile = $files['blog']['articles'][$index]['image'];
                    $newName = $imgFile->getRandomName();
                    if ($imgFile->move(FCPATH . 'uploads/homepage', $newName)) {
                        $art['image'] = 'uploads/homepage/' . $newName;
                    }
                } else {
                    $art['image'] = $art['existing_image'] ?? '';
                }
                unset($art['existing_image']);
            }
            $blog['articles'] = $articles;
            $blog['title'] = $title;
            $blog['subtitle'] = $subtitle;
            $finalContent = $blog;

        } elseif ($key === 'faq') {
            $finalContent = $this->request->getPost('faqs') ?: [];

        } elseif ($key === 'custom_text') {
            $finalContent = [
                'html' => $this->request->getPost('custom_text')
            ];

        } else {
            $finalContent = $content;
        }

        // Save
        $currentUserId = $this->authLib->getUserId();
        
        $saveData = [
            'title'        => $title,
            'subtitle'     => $subtitle,
            'is_active'    => $is_active,
            'sort_order'   => $sort_order,
            'content_json' => json_encode($finalContent, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_by'   => $currentUserId
        ];

        $this->sectionModel->update($id, $saveData);
        $this->logActivity('homepage', 'edit', "Updated homepage section content for: {$section['title']} (Key: {$section['section_key']})");

        return redirect()->to(base_url('admin/homepage'))->with('success', 'Section updated successfully.');
    }
}
