<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card-custom shadow-sm">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-sliders-h me-2 text-cyan"></i> Site Configurations</h4>

            <form action="<?= base_url('admin/settings') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <!-- Bootstrap Tab Navigation -->
                <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                            <i class="far fa-building me-2"></i> General Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="logo-tab" data-bs-toggle="tab" data-bs-target="#logo" type="button" role="tab" aria-controls="logo" aria-selected="false">
                            <i class="far fa-image me-2"></i> Logo Settings
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false">
                            <i class="far fa-share-alt me-2"></i> Social Media Icons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">
                            <i class="far fa-truck me-2"></i> Shipping Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="discount-tab" data-bs-toggle="tab" data-bs-target="#discount" type="button" role="tab" aria-controls="discount" aria-selected="false">
                            <i class="far fa-percentage me-2"></i> Global Discounts
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content border p-4 rounded bg-white" id="settingsTabContent">
                    
                    <!-- General Settings Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="<?= esc($settings['company_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="company_phone" class="form-label">Company Phone</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?= esc($settings['company_phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="company_email" class="form-label">Company Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" value="<?= esc($settings['company_email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="company_working_hours" class="form-label">Working Hours</label>
                                <input type="text" class="form-control" id="company_working_hours" name="company_working_hours" value="<?= esc($settings['company_working_hours'] ?? '') ?>" placeholder="e.g., Mon-Sun (9.00AM - 9.00PM)">
                            </div>
                            <div class="col-12">
                                <label for="company_address" class="form-label">Company Address</label>
                                <textarea class="form-control" id="company_address" name="company_address" rows="3"><?= esc($settings['company_address'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="announcement_text" class="form-label fw-bold text-dark">Header Announcement / Offer text</label>
                                <input type="text" class="form-control" id="announcement_text" name="announcement_text" value="<?= esc($settings['announcement_text'] ?? '') ?>" placeholder="e.g., 🎉 Special Offer: Get 10% off on your first order. Use Code: WELCOME10">
                                <div class="form-text text-muted">This announcement will display as a single line at the very top of the frontend header. Leave blank to hide.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Settings Tab -->
                    <div class="tab-pane fade" id="logo" role="tabpanel" aria-labelledby="logo-tab">
                        <div class="row g-4">
                            <div class="col-md-6 align-self-center">
                                <label for="company_logo" class="form-label fw-bold">Upload New Logo</label>
                                <input class="form-control" type="file" id="company_logo" name="company_logo" accept="image/*" onchange="previewLogo(event)">
                                <div class="form-text text-muted mt-2">Upload high-resolution transparent PNG/SVG logo for best display.</div>
                                
                                <?php if (!empty($settings['company_logo'])): ?>
                                    <div class="form-check mt-3 border p-2 rounded border-danger bg-light-danger d-inline-block">
                                        <input class="form-check-input ms-0" type="checkbox" name="delete_logo" value="1" id="delete_logo">
                                        <label class="form-check-label text-danger fw-bold ms-2" for="delete_logo">
                                            Remove current logo (falls back to styled brand text)
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-center">
                                <label class="form-label d-block fw-bold">Logo Preview</label>
                                <div class="border rounded p-4 bg-light d-flex align-items-center justify-content-center" style="min-height: 150px;">
                                    <?php if (!empty($settings['company_logo'])): ?>
                                        <img id="logo-preview-image" src="<?= base_url($settings['company_logo']) ?>" alt="Site Logo" style="max-height: 80px; object-fit: contain;">
                                    <?php else: ?>
                                        <img id="logo-preview-image" src="" alt="No Logo Uploaded" class="d-none" style="max-height: 80px; object-fit: contain;">
                                        <span id="logo-preview-text" class="text-muted d-block">No logo uploaded. Standard text logo will be active.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Settings Tab -->
                    <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="facebook_url" class="form-label"><i class="fab fa-facebook-f text-primary me-2"></i> Facebook URL</label>
                                <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?= esc($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/yourpage">
                            </div>
                            <div class="col-md-6">
                                <label for="instagram_url" class="form-label"><i class="fab fa-instagram text-danger me-2"></i> Instagram URL</label>
                                <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?= esc($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/yourprofile">
                            </div>
                            <div class="col-md-6">
                                <label for="twitter_url" class="form-label"><i class="fab fa-x-twitter text-dark me-2"></i> Twitter/X URL</label>
                                <input type="url" class="form-control" id="twitter_url" name="twitter_url" value="<?= esc($settings['twitter_url'] ?? '') ?>" placeholder="https://twitter.com/yourprofile">
                            </div>
                            <div class="col-md-6">
                                <label for="youtube_url" class="form-label"><i class="fab fa-youtube text-danger me-2"></i> YouTube Channel URL</label>
                                <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="<?= esc($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/c/yourchannel">
                            </div>
                            <div class="col-md-6">
                                <label for="linkedin_url" class="form-label"><i class="fab fa-linkedin-in text-primary me-2"></i> LinkedIn Page URL</label>
                                <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" value="<?= esc($settings['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/company/yourpage">
                            </div>
                            <div class="col-md-6">
                                <label for="pinterest_url" class="form-label"><i class="fab fa-pinterest text-danger me-2"></i> Pinterest URL</label>
                                <input type="url" class="form-control" id="pinterest_url" name="pinterest_url" value="<?= esc($settings['pinterest_url'] ?? '') ?>" placeholder="https://pinterest.com/yourprofile">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Settings Tab -->
                    <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="express_shipping_info" class="form-label fw-bold text-dark">Express Delivery Shipping Info</label>
                                <textarea class="form-control" id="express_shipping_info" name="express_shipping_info" rows="6"><?= esc($settings['express_shipping_info'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <label for="courier_shipping_info" class="form-label fw-bold text-dark">Courier Delivery Shipping Info</label>
                                <textarea class="form-control" id="courier_shipping_info" name="courier_shipping_info" rows="6"><?= esc($settings['courier_shipping_info'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Discount Settings Tab -->
                    <div class="tab-pane fade" id="discount" role="tabpanel" aria-labelledby="discount-tab">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="global_discount_active" class="form-label fw-bold">Enable Additional Discount?</label>
                                <select class="form-select" id="global_discount_active" name="global_discount_active">
                                    <option value="0" <?= (isset($settings['global_discount_active']) && $settings['global_discount_active'] == '0') ? 'selected' : '' ?>>No (Disabled)</option>
                                    <option value="1" <?= (isset($settings['global_discount_active']) && $settings['global_discount_active'] == '1') ? 'selected' : '' ?>>Yes (Enabled)</option>
                                </select>
                                <div class="form-text text-muted">Toggle whether additional threshold discount should apply at checkout.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="global_discount_threshold" class="form-label fw-bold">Discount Threshold (₹) *</label>
                                <input type="number" class="form-control" id="global_discount_threshold" name="global_discount_threshold" value="<?= esc($settings['global_discount_threshold'] ?? '1000') ?>" required>
                                <div class="form-text text-muted">Discount will apply when subtotal is greater than this amount.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="global_discount_value" class="form-label fw-bold">Discount Value *</label>
                                <input type="number" step="0.01" class="form-control" id="global_discount_value" name="global_discount_value" value="<?= esc($settings['global_discount_value'] ?? '10') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="global_discount_type" class="form-label fw-bold">Discount Type *</label>
                                <select class="form-select" id="global_discount_type" name="global_discount_type">
                                    <option value="percentage" <?= (isset($settings['global_discount_type']) && $settings['global_discount_type'] == 'percentage') ? 'selected' : '' ?>>Percentage (%)</option>
                                    <option value="fixed" <?= (isset($settings['global_discount_type']) && $settings['global_discount_type'] == 'fixed') ? 'selected' : '' ?>>Fixed Amount (₹)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-4 text-start">
                    <button type="submit" class="btn btn-cyan btn-lg"><i class="far fa-save me-2"></i> Save Configurations</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    ClassicEditor.create(document.querySelector('#express_shipping_info'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
    }).catch(error => { console.error(error); });

    ClassicEditor.create(document.querySelector('#courier_shipping_info'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
    }).catch(error => { console.error(error); });
});

function previewLogo(event) {
    var input = event.target;
    var reader = new FileReader();
    reader.onload = function(){
        var preview = document.getElementById('logo-preview-image');
        preview.src = reader.result;
        preview.classList.remove('d-none');
        
        var textNode = document.getElementById('logo-preview-text');
        if (textNode) {
            textNode.classList.add('d-none');
        }
    };
    if (input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>
