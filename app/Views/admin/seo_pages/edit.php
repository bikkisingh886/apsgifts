<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-cog me-2 text-cyan"></i> Configure SEO: <?= esc($page['page_name']) ?></h4>
            <a href="<?= base_url('admin/seo-pages') ?>" class="btn btn-outline-secondary btn-sm"><i class="far fa-arrow-left me-1"></i> Back to Page List</a>
        </div>

        <form action="<?= base_url('admin/seo-pages/edit/' . $page['id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

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
<?= $this->endSection() ?>
