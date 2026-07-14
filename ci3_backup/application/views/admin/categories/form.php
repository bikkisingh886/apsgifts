<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$is_edit = ($category !== NULL);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><?= $is_edit ? 'Edit Category' : 'Add New Category' ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/categories') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold"><i class="far fa-arrow-left me-1"></i> Back to Categories</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5 mb-5">
    <form action="<?= base_url('admin/categories/save') ?>" method="POST" enctype="multipart/form-data">
        <!-- CSRF Token -->
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
        
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $category['id'] ?>">
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Side: Basic Info -->
            <div class="col-lg-7">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Basic Information</h5>
                
                <div class="mb-3">
                    <label for="name" class="form-label small fw-bold text-secondary">Category Name *</label>
                    <input type="text" name="name" id="name" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($category['name']) : '' ?>" placeholder="e.g. Birthday Gifts" required>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label small fw-bold text-secondary">URL Slug (Auto-generated if empty)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted small">/category/</span>
                        <input type="text" name="slug" id="slug" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($category['slug']) : '' ?>" placeholder="e.g. birthday-gifts">
                    </div>
                    <span class="text-muted small fs-7">Only lowercase letters, numbers, and hyphens.</span>
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label small fw-bold text-secondary">Top Summary (Shown below category title)</label>
                    <textarea name="summary" id="summary" rows="3" class="form-control bg-light" placeholder="Brief summary of category..."><?= $is_edit ? htmlspecialchars($category['summary']) : '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="footer_content" class="form-label small fw-bold text-secondary">SEO Footer Content (Long-form, shown at page bottom)</label>
                    <textarea name="footer_content" id="footer_content" rows="6" class="form-control bg-light" placeholder="Location-specific SEO descriptions with keywords (Patna, Bihar, etc.)..."><?= $is_edit ? htmlspecialchars($category['footer_content']) : '' ?></textarea>
                </div>
            </div>

            <!-- Right Side: Banner, SEO, Status -->
            <div class="col-lg-5">
                <h5 class="fw-bold mb-3 border-bottom pb-2">SEO & Settings</h5>

                <div class="mb-3">
                    <label for="meta_title" class="form-label small fw-bold text-secondary">Meta Title (for Google)</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($category['meta_title']) : '' ?>" placeholder="Enter meta title">
                </div>

                <div class="mb-3">
                    <label for="meta_desc" class="form-label small fw-bold text-secondary">Meta Description</label>
                    <textarea name="meta_desc" id="meta_desc" rows="3" class="form-control bg-light" placeholder="Enter meta description"><?= $is_edit ? htmlspecialchars($category['meta_desc']) : '' ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="banner_image" class="form-label small fw-bold text-secondary">Category Banner Image</label>
                    <input type="file" name="banner_image" id="banner_image" class="form-control bg-light">
                    <?php if ($is_edit && isset($category['banner_image']) && !empty($category['banner_image'])): ?>
                        <div class="mt-2">
                            <span class="text-secondary small d-block">Current Banner:</span>
                            <img src="<?= base_url($category['banner_image']) ?>" class="img-thumbnail mt-1" style="max-height: 80px;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary d-block">Category Status</label>
                    <div class="form-check form-switch form-check-inline mt-1">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= !$is_edit || $category['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_active">Active (Visible in menus and listing)</label>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">
        
        <div class="d-flex gap-2 justify-content-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Category</button>
            <a href="<?= base_url('admin/categories') ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">Cancel</a>
        </div>
    </form>
</div>

<!-- Autogenerate slug script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        nameInput.addEventListener('keyup', function() {
            <?php if (!$is_edit): ?>
            let name = this.value;
            let slug = name.toLowerCase()
                           .replace(/[^a-z0-9\s-]/g, '') // remove special characters
                           .replace(/\s+/g, '-')         // replace spaces with hyphens
                           .replace(/-+/g, '-');         // remove duplicates
            slugInput.value = slug;
            <?php endif; ?>
        });
    });
</script>
