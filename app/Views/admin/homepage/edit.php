<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card-custom mb-4">
            <div class="d-flex align-items-center mb-4">
                <a href="<?= base_url('admin/homepage') ?>" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="far fa-arrow-left"></i> Back
                </a>
                <h4 class="text-dark fw-bold mb-0">
                    <i class="far fa-edit me-2 text-cyan"></i> Edit Homepage Section: <span class="text-cyan"><?= esc($section['title']) ?></span>
                </h4>
            </div>

            <form action="<?= base_url('admin/homepage/edit/' . $section['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="sort_order" value="<?= (int)$section['sort_order'] ?>">

                <!-- General Configuration -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Section Display Title</label>
                        <input type="text" name="title" class="form-control" value="<?= esc($section['title']) ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">Section Subtitle / Tagline</label>
                        <input type="text" name="subtitle" class="form-control" value="<?= esc($section['subtitle']) ?>">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" <?= $section['is_active'] ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= !$section['is_active'] ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <hr class="mb-4">

                <!-- Section-Specific Forms -->
                <div class="section-content-editor mb-4">
                    <h5 class="text-cyan fw-bold mb-3"><i class="far fa-cog me-1"></i> Section Content Settings</h5>

                    <?php $key = $section['section_key']; ?>

                    <!-- 1. HERO SLIDER -->
                    <?php if ($key === 'hero_slider'): ?>
                        <?php
                        $slides = [];
                        $sidebar = [
                            'image' => 'assets/img/banner/hs-1-banner.jpg',
                            'link' => 'shop'
                        ];
                        if (isset($content['slides'])) {
                            $slides = $content['slides'];
                            $sidebar = $content['sidebar_banner'] ?? $sidebar;
                        } else {
                            $slides = $content ?: [];
                        }
                        ?>
                        <!-- Left Ad Banner Configuration -->
                        <div class="card border p-3 mb-4 bg-light">
                            <h6 class="fw-bold mb-3 text-cyan"><i class="far fa-image me-1"></i> Left Side Ad Banner Settings</h6>
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small font-weight-bold">Upload Ad Banner Image</label>
                                    <input type="file" name="sidebar_banner[image]" class="form-control mb-2">
                                    <?php if (!empty($sidebar['image'])): ?>
                                        <div class="mt-1">
                                            <img src="<?= base_url($sidebar['image']) ?>" style="height: 60px; border-radius: 4px;" alt="sidebar ad banner">
                                            <input type="hidden" name="sidebar_banner[existing_image]" value="<?= esc($sidebar['image']) ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small font-weight-bold">Ad Banner Redirect Link</label>
                                    <input type="text" name="sidebar_banner[link]" class="form-control" value="<?= esc($sidebar['link'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="form-label small font-weight-bold">Ad Banner Alt Text</label>
                                    <input type="text" name="sidebar_banner[alt]" class="form-control" value="<?= esc($sidebar['alt'] ?? '') ?>" placeholder="Alt text (SEO)">
                                </div>

                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 text-cyan"><i class="far fa-images me-1"></i> Carousel Slides (Image & Link Only)</h6>
                        <div id="slides-container">
                            <?php foreach ($slides as $index => $slide): ?>
                                <div class="card border p-3 mb-3 bg-light position-relative slide-item">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                                    <div class="row align-items-center">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small font-weight-bold">Upload Slide Image (Replaces Current)</label>
                                            <input type="file" name="slides[<?= $index ?>][image]" class="form-control mb-2">
                                            <?php if (!empty($slide['image'])): ?>
                                                <div class="mt-1">
                                                    <img src="<?= base_url($slide['image']) ?>" style="height: 60px; border-radius: 4px;" alt="current slide">
                                                    <input type="hidden" name="slides[<?= $index ?>][existing_image]" value="<?= esc($slide['image']) ?>">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small font-weight-bold">Slide Redirect Link</label>
                                            <input type="text" name="slides[<?= $index ?>][link]" class="form-control" value="<?= esc($slide['link'] ?? '') ?>" placeholder="e.g. shop or category/flowers">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small font-weight-bold">Slide Image Alt Text</label>
                                            <input type="text" name="slides[<?= $index ?>][alt]" class="form-control" value="<?= esc($slide['alt'] ?? '') ?>" placeholder="Alt text (SEO)">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-slide-btn"><i class="far fa-plus"></i> Add Slide</button>

                    <!-- 2. FEATURES -->
                    <?php elseif ($key === 'features'): ?>
                        <div class="row">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <?php $feat = $content[$i] ?? ['icon' => 'flaticon-delivery-truck', 'title' => '', 'description' => '']; ?>
                                <div class="col-md-6 mb-3">
                                    <div class="border p-3 rounded bg-light">
                                        <h6 class="fw-bold mb-3 text-cyan">Feature Block <?= $i + 1 ?></h6>
                                        <div class="mb-2">
                                            <label class="form-label small">Icon Class (e.g. flaticon-support)</label>
                                            <input type="text" name="features[<?= $i ?>][icon]" class="form-control" value="<?= esc($feat['icon']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Title</label>
                                            <input type="text" name="features[<?= $i ?>][title]" class="form-control" value="<?= esc($feat['title']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Description / Value</label>
                                            <input type="text" name="features[<?= $i ?>][description]" class="form-control" value="<?= esc($feat['description']) ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                    <!-- 3. SHOP BY OCCASION / RECIPIENT / CATEGORIES -->
                    <?php elseif ($key === 'shop_by_occasion' || $key === 'shop_by_recipient' || $key === 'home_categories'): ?>
                        <div class="border p-3 rounded bg-light">
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">View More Redirect Link</label>
                                <input type="text" name="view_more_link" class="form-control" value="<?= esc($content['view_more_link'] ?? 'shop') ?>">
                            </div>
                            <label class="form-label font-weight-bold d-block mb-3">Select Categories to display upfront</label>
                            <div class="row">
                                <?php 
                                $selectedIds = $content['category_ids'] ?? [];
                                foreach ($categories as $cat): 
                                ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= $cat['id'] ?>" id="cat_chk_<?= $cat['id'] ?>" <?= in_array($cat['id'], $selectedIds) ? 'checked' : '' ?>>
                                            <label class="form-check-label text-dark" for="cat_chk_<?= $cat['id'] ?>">
                                                <?= esc($cat['name']) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    <!-- 4. GIFT FINDER -->
                    <?php elseif ($key === 'gift_finder'): ?>
                        <div class="border p-3 rounded bg-light">
                            <p class="text-muted mb-0">This widget runs automatically by querying active categories from the database. Changes to title and subtitle can be edited in the configuration fields above.</p>
                        </div>

                    <!-- 5. CATEGORY BANNER COLS -->
                    <?php elseif ($key === 'category_promotional_banners'): ?>
                        <div id="banners-container">
                            <?php foreach ($content as $index => $banner): ?>
                                <div class="card border p-3 mb-3 bg-light position-relative banner-item">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <label class="form-label small">Upload Banner Image</label>
                                            <input type="file" name="banners[<?= $index ?>][image]" class="form-control mb-2">
                                            <?php if (!empty($banner['image'])): ?>
                                                <img src="<?= base_url($banner['image']) ?>" style="height: 65px; border-radius: 4px;" alt="current">
                                                <input type="hidden" name="banners[<?= $index ?>][existing_image]" value="<?= esc($banner['image']) ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-2">
                                                <label class="form-label small">Banner Title</label>
                                                <input type="text" name="banners[<?= $index ?>][title]" class="form-control" value="<?= esc($banner['title'] ?? '') ?>">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Redirect Link (slug or absolute)</label>
                                                <input type="text" name="banners[<?= $index ?>][link]" class="form-control" value="<?= esc($banner['link'] ?? '') ?>">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">Image Alt Text</label>
                                                <input type="text" name="banners[<?= $index ?>][alt]" class="form-control" value="<?= esc($banner['alt'] ?? '') ?>" placeholder="Alt text (SEO)">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-banner-btn"><i class="far fa-plus"></i> Add Banner</button>

                    <!-- 6. SAME DAY DELIVERY BANNER -->
                    <?php elseif ($key === 'delivery_banner'): ?>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small font-weight-bold">Background Image</label>
                                    <input type="file" name="banner_image" class="form-control mb-2">
                                    <?php if (!empty($content['image'])): ?>
                                        <img src="<?= base_url($content['image']) ?>" style="height: 80px; border-radius: 4px;" alt="banner background">
                                        <input type="hidden" name="existing_image" value="<?= esc($content['image']) ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-2">
                                        <label class="form-label small">Redirect Link</label>
                                        <input type="text" name="banner[link]" class="form-control" value="<?= esc($content['link'] ?? '') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Button Text</label>
                                        <input type="text" name="banner[button_text]" class="form-control" value="<?= esc($content['button_text'] ?? 'Order Now') ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Image Alt Text</label>
                                        <input type="text" name="banner[alt]" class="form-control" value="<?= esc($content['alt'] ?? '') ?>" placeholder="Alt text (SEO)">
                                    </div>
                                    <!-- Propagate config titles -->
                                    <input type="hidden" name="banner[title]" value="<?= esc($section['title']) ?>">
                                    <input type="hidden" name="banner[subtitle]" value="<?= esc($section['subtitle']) ?>">
                                </div>

                            </div>
                        </div>

                    <!-- 7. PRODUCTS GRIDS WITH DYNAMIC CATEGORY FILTERS -->
                    <?php elseif ($key === 'trending_items' || $key === 'popular_items' || $key === 'weekly_deals'): ?>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">Query Limit (Number of Products)</label>
                                    <input type="number" name="limit" class="form-control" value="<?= (int)($content['limit'] ?? 8) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">View More Redirect Link</label>
                                    <input type="text" name="view_more_link" class="form-control" value="<?= esc($content['view_more_link'] ?? 'shop') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">Filter By Category (Optional)</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Query standard flag filtered products (Default)</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (isset($content['category_id']) && $content['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if ($key === 'weekly_deals'): ?>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label font-weight-bold">Deal Countdown Expiry Date</label>
                                        <input type="text" name="countdown_date" class="form-control" placeholder="e.g. 2026/12/30" value="<?= esc($content['countdown_date'] ?? '2026/12/30') ?>">
                                        <small class="text-muted">Format: YYYY/MM/DD</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <!-- 8. PERSONALIZED GIFTS -->
                    <?php elseif ($key === 'personalized_gifts'): ?>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Category representing personalized gifts</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">Fetch Bestsellers (Default)</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (isset($content['category_id']) && $content['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label font-weight-bold">Limit Products count</label>
                                    <input type="number" name="limit" class="form-control" value="<?= (int)($content['limit'] ?? 15) ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label font-weight-bold">View More Redirect Link</label>
                                    <input type="text" name="view_more_link" class="form-control" value="<?= esc($content['view_more_link'] ?? 'category/personalised-gifts') ?>">
                                </div>
                            </div>
                        </div>

                    <!-- 9. PROMO VIDEO -->
                    <?php elseif ($key === 'promo_video'): ?>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">Background Thumbnail Image</label>
                                    <input type="file" name="video_image" class="form-control mb-2">
                                    <?php if (!empty($content['image'])): ?>
                                        <img src="<?= base_url($content['image']) ?>" style="height: 80px; border-radius: 4px;" alt="video thumbnail">
                                        <input type="hidden" name="existing_image" value="<?= esc($content['image']) ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Youtube Video URL</label>
                                        <input type="url" name="video[video_url]" class="form-control" value="<?= esc($content['video_url'] ?? '') ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Video Description Text</label>
                                        <textarea name="video[description]" class="form-control" rows="3"><?= esc($content['description'] ?? '') ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Image Alt Text</label>
                                        <input type="text" name="video[alt]" class="form-control" value="<?= esc($content['alt'] ?? '') ?>" placeholder="Alt text (SEO)">
                                    </div>
                                    <!-- Propagate titles -->
                                    <input type="hidden" name="video[title]" value="<?= esc($section['title']) ?>">
                                    <input type="hidden" name="video[subtitle]" value="<?= esc($section['subtitle']) ?>">
                                </div>

                            </div>
                        </div>

                    <!-- 10. ABOUT US -->
                    <?php elseif ($key === 'about_us'): ?>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label font-weight-bold">Primary Image</label>
                                    <input type="file" name="about_image" class="form-control mb-2">
                                    <?php if (!empty($content['image'])): ?>
                                        <img src="<?= base_url($content['image']) ?>" style="height: 85px; border-radius: 4px;" alt="about">
                                        <input type="hidden" name="existing_image" value="<?= esc($content['image']) ?>">
                                    <?php endif; ?>
                                    <div class="mt-2">
                                        <label class="form-label small font-weight-bold">Image Alt Text</label>
                                        <input type="text" name="about[alt]" class="form-control" value="<?= esc($content['alt'] ?? '') ?>" placeholder="Alt text (SEO)">
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label font-weight-bold">About Summary Text</label>
                                            <textarea name="about[about_text]" class="form-control" rows="4"><?= esc($content['about_text'] ?? '') ?></textarea>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label font-weight-bold">Experience Years Badge</label>
                                            <input type="number" name="about[experience_years]" class="form-control" value="<?= (int)($content['experience_years'] ?? 30) ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-2">
                                    <label class="form-label font-weight-bold">About Features / Checklist Points</label>
                                    <div id="about-features-container">
                                        <?php 
                                        $feats = $content['features'] ?? [];
                                        foreach ($feats as $index => $featText): 
                                        ?>
                                            <div class="input-group mb-2 about-feature-item">
                                                <input type="text" name="about[features][]" class="form-control" value="<?= esc($featText) ?>">
                                                <button type="button" class="btn btn-outline-danger remove-feature-btn"><i class="far fa-trash-alt"></i></button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-cyan mt-1" id="add-about-feature-btn"><i class="far fa-plus"></i> Add Point</button>
                                </div>
                            </div>
                            <input type="hidden" name="about[title]" value="<?= esc($section['title']) ?>">
                            <input type="hidden" name="about[subtitle]" value="<?= esc($section['subtitle']) ?>">
                        </div>

                    <!-- 11. WHY CHOOSE US -->
                    <?php elseif ($key === 'why_choose_us'): ?>
                        <div class="row">
                            <?php 
                            $reasons = $content['reasons'] ?? [];
                            for ($i = 0; $i < 3; $i++): 
                                $reason = $reasons[$i] ?? ['icon' => '', 'title' => '', 'description' => ''];
                            ?>
                                <div class="col-md-4 mb-3">
                                    <div class="border p-3 rounded bg-light">
                                        <h6 class="fw-bold mb-3 text-cyan">Reason Item <?= $i + 1 ?></h6>
                                        <div class="mb-2">
                                            <label class="form-label small">Title</label>
                                            <input type="text" name="reasons[<?= $i ?>][title]" class="form-control" value="<?= esc($reason['title']) ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Description</label>
                                            <textarea name="reasons[<?= $i ?>][description]" class="form-control" rows="3"><?= esc($reason['description']) ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                    <!-- 12. TESTIMONIALS -->
                    <?php elseif ($key === 'testimonials'): ?>
                        <div id="testimonials-container">
                            <?php foreach ($testimonials as $index => $testi): ?>
                                <div class="card border p-3 mb-3 bg-light position-relative testi-item">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label small font-weight-bold">Client Photo</label>
                                            <input type="file" name="testimonials[<?= $index ?>][image]" class="form-control mb-2">
                                            <?php if (!empty($testi['image'])): ?>
                                                <img src="<?= base_url($testi['image']) ?>" style="height: 55px; border-radius: 50%;" alt="current">
                                                <input type="hidden" name="testimonials[<?= $index ?>][existing_image]" value="<?= esc($testi['image']) ?>">
                                            <?php endif; ?>
                                            <div class="mt-2">
                                                <label class="form-label small">Image Alt Text</label>
                                                <input type="text" name="testimonials[<?= $index ?>][alt]" class="form-control form-control-sm" value="<?= esc($testi['alt'] ?? '') ?>" placeholder="Alt text">
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Name</label>
                                                    <input type="text" name="testimonials[<?= $index ?>][name]" class="form-control" value="<?= esc($testi['name'] ?? '') ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label small">Role / Badge</label>
                                                    <input type="text" name="testimonials[<?= $index ?>][role]" class="form-control" value="<?= esc($testi['role'] ?? 'Customer') ?>">
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <label class="form-label small">Feedback Text</label>
                                                    <textarea name="testimonials[<?= $index ?>][text]" class="form-control" rows="2"><?= esc($testi['text'] ?? '') ?></textarea>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label small">Rating Score</label>
                                                    <select name="testimonials[<?= $index ?>][rating]" class="form-select">
                                                        <?php for ($r = 1; $r <= 5; $r++): ?>
                                                            <option value="<?= $r ?>" <?= ($testi['rating'] ?? 5) == $r ? 'selected' : '' ?>><?= $r ?> Stars</option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-testi-btn"><i class="far fa-plus"></i> Add Testimonial</button>

                    <!-- 13. PHOTO GALLERY -->
                    <?php elseif ($key === 'photo_gallery'): ?>
                        <div id="gallery-container">
                            <?php foreach ($images as $index => $imgItem): ?>
                                <div class="card border p-3 mb-3 bg-light position-relative gallery-item-form">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <label class="form-label small font-weight-bold">Photo File</label>
                                            <input type="file" name="gallery[<?= $index ?>][image]" class="form-control mb-2">
                                            <?php if (!empty($imgItem['image'])): ?>
                                                <img src="<?= base_url($imgItem['image']) ?>" style="height: 60px; border-radius: 4px;" alt="current">
                                                <input type="hidden" name="gallery[<?= $index ?>][existing_image]" value="<?= esc($imgItem['image']) ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Gallery redirect link / hashtags</label>
                                            <input type="text" name="gallery[<?= $index ?>][link]" class="form-control" value="<?= esc($imgItem['link'] ?? '#') ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Image Alt Text</label>
                                            <input type="text" name="gallery[<?= $index ?>][alt]" class="form-control" value="<?= esc($imgItem['alt'] ?? '') ?>" placeholder="Alt text">
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-gallery-btn"><i class="far fa-plus"></i> Add Photo</button>

                    <!-- 14. LATEST NEWS & BLOGS -->
                    <?php elseif ($key === 'blog'): ?>
                        <?php $articles = $content['articles'] ?? []; ?>
                        <div id="blogs-container">
                            <?php foreach ($articles as $index => $art): ?>
                                <div class="card border p-3 mb-3 bg-light position-relative blog-item-form">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small font-weight-bold">Article Cover Image</label>
                                            <input type="file" name="blog[articles][<?= $index ?>][image]" class="form-control mb-2">
                                            <?php if (!empty($art['image'])): ?>
                                                <img src="<?= base_url($art['image']) ?>" style="height: 60px; border-radius: 4px;" alt="current blog cover">
                                                <input type="hidden" name="blog[articles][<?= $index ?>][existing_image]" value="<?= esc($art['image']) ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-8 mb-2">
                                                    <label class="form-label small">Article Title</label>
                                                    <input type="text" name="blog[articles][<?= $index ?>][title]" class="form-control" value="<?= esc($art['title'] ?? '') ?>" required>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label small">Post Date</label>
                                                    <input type="text" name="blog[articles][<?= $index ?>][date]" class="form-control" value="<?= esc($art['date'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-8 mb-2">
                                                    <label class="form-label small">Summary Preview Text</label>
                                                    <input type="text" name="blog[articles][<?= $index ?>][summary]" class="form-control" value="<?= esc($art['summary'] ?? '') ?>">
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label small">Link</label>
                                                    <input type="text" name="blog[articles][<?= $index ?>][link]" class="form-control" value="<?= esc($art['link'] ?? 'blog') ?>">
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <label class="form-label small">Cover Image Alt Text</label>
                                                    <input type="text" name="blog[articles][<?= $index ?>][alt]" class="form-control" value="<?= esc($art['alt'] ?? '') ?>" placeholder="Alt text">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-blog-btn"><i class="far fa-plus"></i> Add Article</button>

                    <!-- 15. FAQ ACCORDION -->
                    <?php elseif ($key === 'faq'): ?>
                        <div id="faqs-container">
                            <?php foreach ($content as $index => $faq): ?>
                                <div class="card border p-3 mb-3 bg-light position-relative faq-item-form">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                                    <div class="mb-2">
                                        <label class="form-label small font-weight-bold">Question</label>
                                        <input type="text" name="faqs[<?= $index ?>][question]" class="form-control" value="<?= esc($faq['question']) ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small font-weight-bold">Answer Content</label>
                                        <textarea name="faqs[<?= $index ?>][answer]" class="form-control" rows="3" required><?= esc($faq['answer']) ?></textarea>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-faq-btn"><i class="far fa-plus"></i> Add Question</button>

                    <!-- 16. TWO COLUMN BANNERS -->
                    <?php elseif ($key === 'two_column_banners'): ?>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card p-3 border">
                                        <h6 class="fw-bold text-cyan mb-3">Left Side Banner</h6>
                                        <label class="form-label small">Upload Banner Image</label>
                                        <input type="file" name="banner_1[image]" class="form-control mb-2">
                                        <?php if (!empty($content['banner_1']['image'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= base_url($content['banner_1']['image']) ?>" style="height: 60px; border-radius: 4px;" alt="banner 1">
                                                <input type="hidden" name="two_banners[banner_1][existing_image]" value="<?= esc($content['banner_1']['image']) ?>">
                                            </div>
                                        <?php endif; ?>
                                        <label class="form-label small mt-2">Redirect Link</label>
                                        <input type="text" name="two_banners[banner_1][link]" class="form-control" value="<?= esc($content['banner_1']['link'] ?? 'shop') ?>">
                                        <label class="form-label small mt-2">Image Alt Text</label>
                                        <input type="text" name="two_banners[banner_1][alt]" class="form-control" value="<?= esc($content['banner_1']['alt'] ?? '') ?>" placeholder="Alt text">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card p-3 border">
                                        <h6 class="fw-bold text-cyan mb-3">Right Side Banner</h6>
                                        <label class="form-label small">Upload Banner Image</label>
                                        <input type="file" name="banner_2[image]" class="form-control mb-2">
                                        <?php if (!empty($content['banner_2']['image'])): ?>
                                            <div class="mb-2">
                                                <img src="<?= base_url($content['banner_2']['image']) ?>" style="height: 60px; border-radius: 4px;" alt="banner 2">
                                                <input type="hidden" name="two_banners[banner_2][existing_image]" value="<?= esc($content['banner_2']['image']) ?>">
                                            </div>
                                        <?php endif; ?>
                                        <label class="form-label small mt-2">Redirect Link</label>
                                        <input type="text" name="two_banners[banner_2][link]" class="form-control" value="<?= esc($content['banner_2']['link'] ?? 'shop') ?>">
                                        <label class="form-label small mt-2">Image Alt Text</label>
                                        <input type="text" name="two_banners[banner_2][alt]" class="form-control" value="<?= esc($content['banner_2']['alt'] ?? '') ?>" placeholder="Alt text">
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($key === 'custom_text'): ?>
                        <div class="border p-3 rounded bg-light">
                            <h6 class="fw-bold text-cyan mb-3">Custom HTML/Text Content</h6>
                            <label class="form-label small">Content (HTML allowed)</label>
                            <textarea name="custom_text" id="editor" class="form-control" rows="10"><?= htmlspecialchars($content['html'] ?? '') ?></textarea>
                            <small class="text-muted mt-2 d-block">You can use the rich text editor to format this section.</small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <button type="submit" class="btn btn-cyan text-white px-4 py-2" style="background-color: #00bcd4; border: none;">Save Content Changes</button>
                    <a href="<?= base_url('admin/homepage') ?>" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Remove Item Helper (dynamic bindings)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const containerItem = e.target.closest('.slide-item, .banner-item, .testi-item, .gallery-item-form, .blog-item-form, .faq-item-form');
            if (containerItem) {
                if (confirm('Are you sure you want to delete this block?')) {
                    containerItem.remove();
                }
            }
        }
        
        if (e.target.closest('.remove-feature-btn')) {
            e.target.closest('.about-feature-item').remove();
        }
    });

    // 2. Add Slide Generator
    const addSlideBtn = document.getElementById('add-slide-btn');
    if (addSlideBtn) {
        addSlideBtn.addEventListener('click', function() {
            const container = document.getElementById('slides-container');
            const index = container.querySelectorAll('.slide-item').length;
            const block = document.createElement('div');
            block.className = 'card border p-3 mb-3 bg-light position-relative slide-item';
            block.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2">
                        <label class="form-label small font-weight-bold">Upload Slide Image *</label>
                        <input type="file" name="slides[${index}][image]" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small font-weight-bold">Slide Redirect Link</label>
                        <input type="text" name="slides[${index}][link]" class="form-control" placeholder="e.g. shop or category/flowers" value="shop">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small font-weight-bold">Slide Image Alt Text</label>
                        <input type="text" name="slides[${index}][alt]" class="form-control" placeholder="Alt text description" value="">
                    </div>
                </div>
            `;

            container.appendChild(block);
        });
    }

    // 3. Add Promotional Banner Generator
    const addBannerBtn = document.getElementById('add-banner-btn');
    if (addBannerBtn) {
        addBannerBtn.addEventListener('click', function() {
            const container = document.getElementById('banners-container');
            const index = container.querySelectorAll('.banner-item').length;
            const block = document.createElement('div');
            block.className = 'card border p-3 mb-3 bg-light position-relative banner-item';
            block.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                <div class="row align-items-center">
                    <div class="col-md-4">
                         <label class="form-label small">Upload Banner Image *</label>
                         <input type="file" name="banners[${index}][image]" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                         <div class="mb-2">
                             <label class="form-label small">Banner Title</label>
                             <input type="text" name="banners[${index}][title]" class="form-control" placeholder="Promo Title">
                         </div>
                         <div class="mb-2">
                             <label class="form-label small">Redirect Link</label>
                             <input type="text" name="banners[${index}][link]" class="form-control" value="shop">
                         </div>
                         <div class="mb-2">
                             <label class="form-label small">Image Alt Text</label>
                             <input type="text" name="banners[${index}][alt]" class="form-control" placeholder="Alt text description" value="">
                         </div>
                    </div>
                </div>

            `;
            container.appendChild(block);
        });
    }

    // 4. Add About Us checklist points
    const addAboutFeatureBtn = document.getElementById('add-about-feature-btn');
    if (addAboutFeatureBtn) {
        addAboutFeatureBtn.addEventListener('click', function() {
            const container = document.getElementById('about-features-container');
            const block = document.createElement('div');
            block.className = 'input-group mb-2 about-feature-item';
            block.innerHTML = `
                <input type="text" name="about[features][]" class="form-control" placeholder="Feature Point">
                <button type="button" class="btn btn-outline-danger remove-feature-btn"><i class="far fa-trash-alt"></i></button>
            `;
            container.appendChild(block);
        });
    }

    // 5. Add Testimonial Generator
    const addTestiBtn = document.getElementById('add-testi-btn');
    if (addTestiBtn) {
        addTestiBtn.addEventListener('click', function() {
            const container = document.getElementById('testimonials-container');
            const index = container.querySelectorAll('.testi-item').length;
            const block = document.createElement('div');
            block.className = 'card border p-3 mb-3 bg-light position-relative testi-item';
             block.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold">Client Photo</label>
                        <input type="file" name="testimonials[${index}][image]" class="form-control mb-2">
                        <label class="form-label small">Image Alt Text</label>
                        <input type="text" name="testimonials[${index}][alt]" class="form-control form-control-sm" placeholder="Alt text" value="">
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Name</label>
                                <input type="text" name="testimonials[${index}][name]" class="form-control" placeholder="Client Name" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Role</label>
                                <input type="text" name="testimonials[${index}][role]" class="form-control" value="Customer">
                            </div>
                            <div class="col-md-8 mb-2">
                                <label class="form-label small">Feedback Text</label>
                                <textarea name="testimonials[${index}][text]" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small">Rating Score</label>
                                <select name="testimonials[${index}][rating]" class="form-select">
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(block);
        });
    }

    // 6. Add Gallery Image
    const addGalleryBtn = document.getElementById('add-gallery-btn');
    if (addGalleryBtn) {
        addGalleryBtn.addEventListener('click', function() {
            const container = document.getElementById('gallery-container');
            const index = container.querySelectorAll('.gallery-item-form').length;
            const block = document.createElement('div');
            block.className = 'card border p-3 mb-3 bg-light position-relative gallery-item-form';
            block.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label class="form-label small font-weight-bold">Photo File *</label>
                        <input type="file" name="gallery[${index}][image]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Gallery redirect link / hashtags</label>
                        <input type="text" name="gallery[${index}][link]" class="form-control" value="#">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Image Alt Text</label>
                        <input type="text" name="gallery[${index}][alt]" class="form-control" placeholder="Alt text" value="">
                    </div>
                </div>

            `;
            container.appendChild(block);
        });
    }

    // 7. Add Blog Post
    const addBlogBtn = document.getElementById('add-blog-btn');
    if (addBlogBtn) {
        addBlogBtn.addEventListener('click', function() {
            const container = document.getElementById('blogs-container');
            const index = container.querySelectorAll('.blog-item-form').length;
            const block = document.createElement('div');
            block.className = 'card border p-3 mb-3 bg-light position-relative blog-item-form';
            block.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label small font-weight-bold">Article Cover Image *</label>
                        <input type="file" name="blog[articles][${index}][image]" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8 mb-2">
                                <label class="form-label small">Article Title</label>
                                <input type="text" name="blog[articles][${index}][title]" class="form-control" placeholder="Article Title" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small">Post Date</label>
                                <input type="text" name="blog[articles][${index}][date]" class="form-control" placeholder="07 July 2026">
                            </div>
                            <div class="col-md-8 mb-2">
                                <label class="form-label small">Summary Preview Text</label>
                                <input type="text" name="blog[articles][${index}][summary]" class="form-control">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small">Link</label>
                                <input type="text" name="blog[articles][${index}][link]" class="form-control" value="blog">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label small">Cover Image Alt Text</label>
                                <input type="text" name="blog[articles][${index}][alt]" class="form-control" placeholder="Alt text" value="">
                            </div>
                        </div>

                    </div>
                </div>
            `;
            container.appendChild(block);
        });
    }

    // 8. Add FAQ Accordion
    const addFaqBtn = document.getElementById('add-faq-btn');
    if (addFaqBtn) {
        addFaqBtn.addEventListener('click', function() {
            const container = document.getElementById('faqs-container');
            const index = container.querySelectorAll('.faq-item-form').length;
            const block = document.createElement('div');
            block.className = 'card border p-3 mb-3 bg-light position-relative faq-item-form';
            block.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 remove-item-btn"><i class="far fa-trash-alt"></i></button>
                <div class="mb-2">
                    <label class="form-label small font-weight-bold">Question</label>
                    <input type="text" name="faqs[${index}][question]" class="form-control" placeholder="e.g. Do you support midnight delivery?" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small font-weight-bold">Answer Content</label>
                    <textarea name="faqs[${index}][answer]" class="form-control" rows="3" placeholder="Answer..." required></textarea>
                </div>
            `;
            container.appendChild(block);
        });
    }
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const editorEl = document.querySelector('#editor');
    if (editorEl) {
        initAppCKEditor(editorEl).catch(error => {
            console.error(error);
        });
    }
});
</script>
<?= $this->endSection() ?>
