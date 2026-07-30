<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card-custom">
            <h4 class="mb-4 text-white"><i class="far fa-edit me-2 text-cyan"></i> Edit Category: <?= esc($category['name']) ?></h4>
            <form action="<?= base_url('admin/categories/edit/' . $category['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row text-white">
                    <!-- Left Column -->
                    <div class="col-lg-7">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Category Name *</label>
                            <input type="text" name="name" id="edit-cat-name-input" class="form-control text-dark" value="<?= esc($category['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">URL Slug (auto-generated + editable)</label>
                            <input type="text" name="slug" id="edit-cat-slug-input" class="form-control text-dark" value="<?= esc($category['slug']) ?>" required>
                            <div id="edit-cat-slug-feedback" class="mt-1" style="display:none;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Parent Category</label>
                            <select name="parent_id" class="form-select text-dark" id="edit-parent-category-select">
                                <option value="">None (Root Category)</option>
                                <?php if (!empty($categories_list)): ?>
                                    <?php foreach ($categories_list as $cl): ?>
                                        <option value="<?= $cl['id'] ?>" <?= ($category['parent_id'] == $cl['id']) ? 'selected' : '' ?>>
                                            <?= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $cl['depth'] ?? 0) ?><?= (($cl['depth'] ?? 0) > 0 ? '↳ ' : '') ?><?= esc($cl['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted d-block mt-1">Select the parent category. Select 'None' for a Root Category.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Top Summary (shown below category title on page)</label>
                            <textarea name="summary" id="edit-cat-summary-editor" class="form-control text-dark" rows="3"><?= esc($category['summary']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">SEO Footer Content (long-form, shown at bottom)</label>
                            <textarea name="footer_content" id="edit-cat-footer-editor" class="form-control text-dark" rows="5"><?= esc($category['footer_content']) ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-lg-5">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Meta Title (for Google)</label>
                            <input type="text" name="meta_title" class="form-control text-dark" value="<?= esc($category['meta_title']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Meta Description</label>
                            <textarea name="meta_desc" class="form-control text-dark" rows="3"><?= esc($category['meta_desc']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Category Banner Image</label>
                            <div class="category-banner-upload-wrapper border border-dashed rounded p-3 text-center bg-light" style="cursor: pointer; position: relative; border-style: dashed !important; border-width: 2px !important; border-color: #ced4da !important;">
                                <input type="file" name="banner_image" id="edit-cat-image-input" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                                <?php if (!empty($category['image_path'])): ?>
                                    <img id="edit-cat-image-preview" src="<?= base_url($category['image_path']) ?>" alt="banner" style="max-height: 80px; object-fit: cover;" class="mb-2 rounded mx-auto d-block">
                                    <div id="edit-cat-image-placeholder" style="display: none;">
                                        <i class="far fa-image mb-2" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                        <p class="small text-muted mb-0">Click to change image</p>
                                    </div>
                                <?php else: ?>
                                    <img id="edit-cat-image-preview" src="#" alt="banner" style="display: none; max-height: 80px; object-fit: cover;" class="mb-2 rounded mx-auto">
                                    <div id="edit-cat-image-placeholder">
                                        <i class="far fa-image mb-2" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                        <p class="small text-muted mb-0">Click to change image</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold text-white">Image Alt Tag (for SEO)</label>
                            <input type="text" name="image_alt" class="form-control text-dark" value="<?= esc($category['image_alt'] ?? '') ?>" placeholder="Alt text description">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label font-weight-bold d-block">Status</label>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="is_active" id="edit-status-active" value="1" <?= $category['is_active'] ? 'checked' : '' ?> autocomplete="off">
                                <label class="btn btn-outline-success w-50 py-2 d-flex align-items-center justify-content-center" for="edit-status-active">
                                    <i class="far fa-check-square me-2"></i> Active
                                </label>

                                <input type="radio" class="btn-check" name="is_active" id="edit-status-inactive" value="0" <?= !$category['is_active'] ? 'checked' : '' ?> autocomplete="off">
                                <label class="btn btn-outline-secondary w-50 py-2 d-flex align-items-center justify-content-center" for="edit-status-inactive">
                                    Inactive
                                </label>
                            </div>
                        </div>

                        <!-- Creator/Updater Details -->
                        <?php 
                        $db = \Config\Database::connect();
                        $creator = !empty($category['created_by']) ? $db->table('users')->where('id', $category['created_by'])->get()->getRowArray() : null;
                        $updater = !empty($category['updated_by']) ? $db->table('users')->where('id', $category['updated_by'])->get()->getRowArray() : null;
                        ?>
                        <div class="card-custom mt-4 p-3 text-white small" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                            <h6 class="text-cyan mb-2" style="font-size: 0.9rem;">Audit Info</h6>
                            <?php if ($creator): ?>
                                <div><strong>Created by:</strong> <?= esc($creator['name']) ?></div>
                            <?php endif; ?>
                            <?php if ($category['created_at']): ?>
                                <div><strong>Created at:</strong> <?= date('d M Y, h:i A', strtotime($category['created_at'])) ?></div>
                            <?php endif; ?>
                            <?php if ($updater): ?>
                                <div class="mt-1"><strong>Last updated by:</strong> <?= esc($updater['name']) ?></div>
                            <?php endif; ?>
                            <?php if ($category['updated_at']): ?>
                                <div><strong>Last updated at:</strong> <?= date('d M Y, h:i A', strtotime($category['updated_at'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-4 border-top pt-3">
                    <a href="<?= base_url('admin/categories') ?>" class="btn btn-outline-secondary px-4 py-2"><i class="far fa-arrow-left me-1"></i> Cancel</a>
                    <button type="submit" class="btn-cyan px-4 py-2">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize CKEditor on Top Summary and Footer Content
    initAppCKEditor('#edit-cat-summary-editor');
    initAppCKEditor('#edit-cat-footer-editor');

    const CAT_EDIT_ID = <?= (int)$category['id'] ?>;

    // 2. Auto-Slug Generation Logic + Real-time Duplicate Check
    const nameInput = document.getElementById('edit-cat-name-input');
    const slugInput = document.getElementById('edit-cat-slug-input');
    const slugFeedback = document.getElementById('edit-cat-slug-feedback');
    let autoSlug = false;
    let slugCheckTimer = null;

    function checkEditCatSlug(slug) {
        if (!slug || slug.length < 2) { slugFeedback.style.display = 'none'; return; }
        clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(function() {
            const parentSelect = document.getElementById('edit-parent-category-select');
            const parentId = parentSelect ? parentSelect.value : '';
            var url = '<?= base_url('admin/categories/check-slug') ?>?slug=' + encodeURIComponent(slug) + '&id=' + CAT_EDIT_ID + '&parent_id=' + parentId;
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    slugFeedback.style.display = 'block';
                    if (data.available) {
                        slugFeedback.innerHTML = '<small class="text-success"><i class="far fa-check-circle me-1"></i>Slug is available</small>';
                        slugInput.classList.remove('is-invalid'); slugInput.classList.add('is-valid');
                    } else {
                        slugFeedback.innerHTML = '<small class="text-danger"><i class="far fa-exclamation-circle me-1"></i>Slug <strong>"' + data.slug + '"</strong> is already in use! Please change it.</small>';
                        slugInput.classList.remove('is-valid'); slugInput.classList.add('is-invalid');
                    }
                });
        }, 450);
    }

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (autoSlug) {
                let slug = this.value.toLowerCase()
                                     .replace(/[^a-z0-9\s-]/g, '')
                                     .replace(/\s+/g, '-')
                                     .replace(/-+/g, '-');
                slugInput.value = slug;
                checkEditCatSlug(slug);
            }
        });

        slugInput.addEventListener('input', function() {
            autoSlug = (this.value === "");
            checkEditCatSlug(this.value);
        });

        const parentSelect = document.getElementById('edit-parent-category-select');
        if (parentSelect) {
            parentSelect.addEventListener('change', function() {
                if (slugInput.value !== '') {
                    checkEditCatSlug(slugInput.value);
                }
            });
        }
    }

    // 3. Image File Upload Preview Logic
    const imgInput = document.getElementById('edit-cat-image-input');
    const imgPreview = document.getElementById('edit-cat-image-preview');
    const imgPlaceholder = document.getElementById('edit-cat-image-placeholder');

    if (imgInput && imgPreview && imgPlaceholder) {
        imgInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                    imgPlaceholder.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
    }
    // Search parent categories in checkboxes list
    const parentCategorySearch = document.getElementById('parent-category-search');
    if (parentCategorySearch) {
        parentCategorySearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const parentRows = document.querySelectorAll('.parent-category-item-row');
            parentRows.forEach(row => {
                const name = row.getAttribute('data-name');
                if (name.includes(query)) {
                    row.style.setProperty('display', 'block', 'important');
                } else {
                    row.style.setProperty('display', 'none', 'important');
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
