<form action="<?= base_url('admin/categories/edit/' . $category['id']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="row text-dark">
        <!-- Left Column -->
        <div class="col-lg-7">
            <div class="mb-3">
                <label class="form-label font-weight-bold">Category Name *</label>
                <input type="text" name="name" id="modal-cat-name-input" class="form-control" value="<?= esc($category['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label font-weight-bold">URL Slug (auto-generated + editable)</label>
                <input type="text" name="slug" id="modal-cat-slug-input" class="form-control" value="<?= esc($category['slug']) ?>" required>
                <div id="modal-cat-slug-feedback" class="mt-1" style="display:none;"></div>
            </div>
            <div class="mb-3">
                <label class="form-label font-weight-bold">Parent Category</label>
                <select name="parent_id" class="form-select">
                    <option value="0">-- None (Root Category) --</option>
                    <?php if (!empty($categories_list)): ?>
                        <?php foreach ($categories_list as $cl): ?>
                            <option value="<?= $cl['id'] ?>" <?= $cl['id'] == $category['parent_id'] ? 'selected' : '' ?>><?= esc($cl['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label font-weight-bold">Top Summary (shown below category title on page)</label>
                <textarea name="summary" class="form-control" rows="2"><?= esc($category['summary']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label font-weight-bold">SEO Footer Content (long-form, shown at bottom)</label>
                <!-- Target id description-editor so layout.php initializes CKEditor on it! -->
                <textarea name="footer_content" id="description-editor" class="form-control" rows="4"><?= esc($category['footer_content']) ?></textarea>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-5">
            <div class="mb-3">
                <label class="form-label font-weight-bold">Meta Title (for Google)</label>
                <input type="text" name="meta_title" class="form-control" value="<?= esc($category['meta_title']) ?>">
                <small class="text-muted d-block mt-1">Recommended: max 60 characters</small>
            </div>
            <div class="mb-3">
                <label class="form-label font-weight-bold">Meta Description</label>
                <textarea name="meta_desc" class="form-control" rows="3"><?= esc($category['meta_desc']) ?></textarea>
                <small class="text-muted d-block mt-1">Recommended: max 160 characters</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label font-weight-bold">Image Alt Tag (for SEO)</label>
                <input type="text" name="image_alt" class="form-control" value="<?= esc($category['image_alt'] ?? '') ?>" placeholder="e.g. Birthday Gifts Category Banner">
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Category Banner Image</label>
                <div class="category-banner-upload-wrapper border border-dashed rounded p-3 text-center bg-light" style="cursor: pointer; position: relative; border-style: dashed !important; border-width: 2px !important; border-color: #ced4da !important;">
                    <input type="file" name="banner_image" id="modal-cat-image-input" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                    <?php if (!empty($category['image_path'])): ?>
                        <img id="modal-cat-image-preview" src="<?= base_url($category['image_path']) ?>" alt="banner" style="max-height: 80px; object-fit: cover;" class="mb-2 rounded mx-auto d-block">
                        <div id="modal-cat-image-placeholder" style="display: none;">
                            <i class="far fa-image mb-2" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            <p class="small text-muted mb-0">Click to change image</p>
                        </div>
                    <?php else: ?>
                        <img id="modal-cat-image-preview" src="#" alt="banner" style="display: none; max-height: 80px; object-fit: cover;" class="mb-2 rounded mx-auto">
                        <div id="modal-cat-image-placeholder">
                            <i class="far fa-image mb-2" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            <p class="small text-muted mb-0">Click to change image</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label font-weight-bold d-block">Status</label>
                <div class="d-flex gap-2">
                    <input type="radio" class="btn-check" name="is_active" id="modal-status-active" value="1" <?= $category['is_active'] ? 'checked' : '' ?> autocomplete="off">
                    <label class="btn btn-outline-success w-50 py-2 d-flex align-items-center justify-content-center" for="modal-status-active">
                        <i class="far fa-check-square me-2"></i> Active
                    </label>

                    <input type="radio" class="btn-check" name="is_active" id="modal-status-inactive" value="0" <?= !$category['is_active'] ? 'checked' : '' ?> autocomplete="off">
                    <label class="btn btn-outline-secondary w-50 py-2 d-flex align-items-center justify-content-center" for="modal-status-inactive">
                        Inactive
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-3 border-top pt-3">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>

<script>
(function() {
    const CAT_EDIT_ID = <?= (int)$category['id'] ?>;

    // 1. Auto-Slug Generation Logic in Modal
    const nameInput = document.getElementById('modal-cat-name-input');
    const slugInput = document.getElementById('modal-cat-slug-input');
    const slugFeedback = document.getElementById('modal-cat-slug-feedback');
    let autoSlug = false;
    let slugCheckTimer = null;

    function checkModalCatSlug(slug) {
        if (!slug || slug.length < 2) { slugFeedback.style.display = 'none'; return; }
        clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(function() {
            var url = '<?= base_url('admin/categories/check-slug') ?>?slug=' + encodeURIComponent(slug) + '&id=' + CAT_EDIT_ID;
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
                checkModalCatSlug(slug);
            }
        });

        slugInput.addEventListener('input', function() {
            autoSlug = (this.value === "");
            checkModalCatSlug(this.value);
        });
    }

    // 2. Image File Upload Preview Logic in Modal
    const imgInput = document.getElementById('modal-cat-image-input');
    const imgPreview = document.getElementById('modal-cat-image-preview');
    const imgPlaceholder = document.getElementById('modal-cat-image-placeholder');

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
})();
</script>
