<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-cog me-2 text-cyan"></i> Configure SEO: <?= esc($page['page_name']) ?></h4>
            <a href="<?= base_url('admin/seo-pages') ?>" class="btn btn-outline-secondary btn-sm"><i class="far fa-arrow-left me-1"></i> Back to Page List</a>
        </div>

        <?php if ($page['page_key'] === 'faq'): ?>
            <div class="alert alert-info border-info mb-4 shadow-sm" style="background-color: #f0f8ff; border-left: 4px solid #00a8cc;">
                <h6 class="fw-bold text-dark mb-1"><i class="far fa-question-circle me-2 text-cyan"></i> Structured FAQ Questions & Answers</h6>
                <p class="mb-2 small text-muted">To add, edit, re-order, or delete structured Question & Answer items displayed in the frontend FAQ page accordion, manage them directly in the <strong>FAQ Manager</strong>.</p>
                <a href="<?= base_url('admin/faqs') ?>" class="btn btn-cyan btn-sm"><i class="far fa-tasks me-1"></i> Open FAQ Manager</a>
            </div>
        <?php elseif ($page['page_key'] === 'home'): ?>
            <div class="alert alert-info border-info mb-4 shadow-sm" style="background-color: #f0f8ff; border-left: 4px solid #00a8cc;">
                <h6 class="fw-bold text-dark mb-1"><i class="far fa-home me-2 text-cyan"></i> Dynamic Homepage Sections</h6>
                <p class="mb-2 small text-muted">Homepage sections, sliders, banners, and product grids are managed visually in the <strong>Homepage Manager</strong>.</p>
                <a href="<?= base_url('admin/homepage') ?>" class="btn btn-cyan btn-sm"><i class="far fa-sliders-h me-1"></i> Open Homepage Manager</a>
            </div>
        <?php elseif ($page['page_key'] === 'contact'): ?>
            <div class="alert alert-info border-info mb-4 shadow-sm" style="background-color: #f0f8ff; border-left: 4px solid #00a8cc;">
                <h6 class="fw-bold text-dark mb-1"><i class="far fa-address-book me-2 text-cyan"></i> Contact Information & Settings</h6>
                <p class="mb-2 small text-muted">Company address, phone number, email address, and working hours are managed in <strong>Settings</strong>.</p>
                <a href="<?= base_url('admin/settings') ?>" class="btn btn-cyan btn-sm"><i class="far fa-cog me-1"></i> Open Settings</a>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/seo-pages/edit/' . $page['id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <?php if (!in_array($page['page_key'], ['home', 'shop', 'faq', 'contact'])): ?>
                <!-- Page Body Content Card (CKEditor with Source HTML mode) -->
                <div class="card-custom mb-4">
                    <h5 class="text-dark mb-2"><i class="far fa-file-edit me-2 text-cyan"></i> Page Body Content</h5>
                    <p class="text-muted small mb-3">Edit the rich HTML content for this page. Use the <strong>Source</strong> button in the editor toolbar to view and edit raw HTML code directly.</p>
                    <textarea name="content" id="page_content_editor" class="form-control" rows="12"><?= old('content', $page['content'] ?? '') ?></textarea>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Left Column (Standard SEO & Schema) -->
                <div class="col-lg-7">
                    <!-- Standard SEO Metadata -->
                    <div class="card-custom mb-4">
                        <h5 class="text-dark mb-4"><i class="far fa-globe me-2 text-cyan"></i> Standard Google SEO</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Title *</label>
                            <input type="text" name="meta_title" class="form-control text-dark" value="<?= esc(old('meta_title', $page['meta_title'])) ?>" placeholder="e.g. Fresh Flowers Online - Same Day Delivery" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Meta Description *</label>
                            <textarea name="meta_desc" class="form-control text-dark" rows="4" placeholder="Enter meta description..." required><?= esc(old('meta_desc', $page['meta_desc'])) ?></textarea>
                        </div>
                    </div>

                    <!-- Schema Markup -->
                    <div class="card-custom mb-4">
                        <h5 class="text-dark mb-4"><i class="far fa-code me-2 text-cyan"></i> JSON-LD Schema Markup</h5>
                        <div class="mb-3">
                            <label class="form-label">Schema Markup Code (Script block or raw JSON)</label>
                            <textarea name="schema_markup" class="form-control text-dark font-monospace" rows="10" style="font-size: 0.85rem;" placeholder='{&#10;  "@context": "https://schema.org",&#10;  "@type": "WebSite",&#10;  ...&#10;}'><?= esc(old('schema_markup', $page['schema_markup'])) ?></textarea>
                            <small class="text-muted d-block mt-2">Paste your structured metadata script block. It will be loaded automatically in the page head.</small>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Social tags - OG & Twitter) -->
                <div class="col-lg-5">
                    <!-- Open Graph -->
                    <div class="card-custom mb-4">
                        <h5 class="text-dark mb-4"><i class="fab fa-facebook-square me-2 text-cyan"></i> Open Graph Tags (Facebook / WhatsApp)</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">OG Title</label>
                            <input type="text" name="og_title" class="form-control text-dark" value="<?= esc(old('og_title', $page['og_title'])) ?>" placeholder="Fallback: Meta Title">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">OG Description</label>
                            <textarea name="og_desc" class="form-control text-dark" rows="3" placeholder="Fallback: Meta Description"><?= esc(old('og_desc', $page['og_desc'])) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">OG Share Image</label>
                            <input type="file" name="og_image_file" class="form-control text-dark mb-2">
                            <?php if ($page['og_image']): ?>
                                <div class="mt-2">
                                    <span class="text-muted small d-block mb-1">Current Image:</span>
                                    <img src="<?= base_url($page['og_image']) ?>" alt="OG Share" style="max-height: 100px; border-radius: 6px;" class="border">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">OG Type</label>
                            <select name="og_type" class="form-select text-dark">
                                <option value="website" <?= old('og_type', $page['og_type']) == 'website' ? 'selected' : '' ?>>Website</option>
                                <option value="article" <?= old('og_type', $page['og_type']) == 'article' ? 'selected' : '' ?>>Article</option>
                                <option value="product" <?= old('og_type', $page['og_type']) == 'product' ? 'selected' : '' ?>>Product</option>
                            </select>
                        </div>
                    </div>

                    <!-- Twitter Card -->
                    <div class="card-custom mb-4">
                        <h5 class="text-dark mb-4"><i class="fab fa-twitter me-2 text-cyan"></i> Twitter Card Tags</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Twitter Card Type</label>
                            <select name="twitter_card" class="form-select text-dark">
                                <option value="summary_large_image" <?= old('twitter_card', $page['twitter_card']) == 'summary_large_image' ? 'selected' : '' ?>>Summary Card with Large Image</option>
                                <option value="summary" <?= old('twitter_card', $page['twitter_card']) == 'summary' ? 'selected' : '' ?>>Standard Summary Card</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Twitter Title</label>
                            <input type="text" name="twitter_title" class="form-control text-dark" value="<?= esc(old('twitter_title', $page['twitter_title'])) ?>" placeholder="Fallback: OG Title">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Twitter Description</label>
                            <textarea name="twitter_desc" class="form-control text-dark" rows="3" placeholder="Fallback: OG Description"><?= esc(old('twitter_desc', $page['twitter_desc'])) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Twitter Share Image</label>
                            <input type="file" name="twitter_image_file" class="form-control text-dark mb-2">
                            <?php if ($page['twitter_image']): ?>
                                <div class="mt-2">
                                    <span class="text-muted small d-block mb-1">Current Image:</span>
                                    <img src="<?= base_url($page['twitter_image']) ?>" alt="Twitter Share" style="max-height: 100px; border-radius: 6px;" class="border">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('admin/seo-pages') ?>" class="btn btn-outline-secondary"><i class="far fa-times me-1"></i> Cancel</a>
                <button type="submit" class="btn-cyan px-5"><i class="far fa-save me-1"></i> Update SEO Config</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    initAppCKEditor('#page_content_editor');
});
</script>
<?= $this->endSection() ?>
