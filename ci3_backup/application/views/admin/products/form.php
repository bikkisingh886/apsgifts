<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$is_edit = ($product !== NULL);
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><?= $is_edit ? 'Edit Product' : 'Add New Product' ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= base_url('admin/products') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold"><i class="far fa-arrow-left me-1"></i> Back to Products</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5 mb-5">
    <form action="<?= base_url('admin/products/save') ?>" method="POST" enctype="multipart/form-data">
        <!-- CSRF Token -->
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: Basic Information -->
            <div class="col-lg-7">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Basic Information</h5>
                
                <div class="mb-3">
                    <label for="name" class="form-label small fw-bold text-secondary">Product Name *</label>
                    <input type="text" name="name" id="name" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($product['name']) : '' ?>" placeholder="e.g. Red Rose Bouquet - 20 Stems" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label for="sku" class="form-label small fw-bold text-secondary">SKU Code (leave empty for auto-gen)</label>
                        <input type="text" name="sku" id="sku" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($product['sku']) : '' ?>" placeholder="Auto: RRB-001">
                    </div>
                    <div class="col-sm-6">
                        <label for="price" class="form-label small fw-bold text-secondary">Price (₹) *</label>
                        <input type="number" name="price" id="price" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($product['price']) : '' ?>" placeholder="e.g. 499" step="0.01" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label small fw-bold text-secondary">Description *</label>
                    <textarea name="description" id="description" rows="5" class="form-control bg-light" placeholder="Detailed product description..." required><?= $is_edit ? htmlspecialchars($product['description']) : '' ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="product_images" class="form-label small fw-bold text-secondary">Upload Product Images (multiple allowed)</label>
                    <input type="file" name="product_images[]" id="product_images" class="form-control bg-light" multiple>
                    <span class="text-muted small fs-7">Hold Ctrl to select multiple images. Rec. format: WebP or JPEG.</span>

                    <!-- Existing gallery management -->
                    <?php if ($is_edit && !empty($product['images'])): ?>
                        <div class="mt-3">
                            <span class="text-secondary small d-block mb-2">Existing Gallery (Check to Delete):</span>
                            <div class="row row-cols-3 g-2">
                                <?php foreach ($product['images'] as $img): ?>
                                    <div class="col">
                                        <div class="card border rounded p-1 text-center position-relative">
                                            <img src="<?= base_url($img['image_path']) ?>" class="img-fluid rounded" style="height: 60px; object-fit: contain;">
                                            <div class="form-check justify-content-center d-flex mt-1">
                                                <input class="form-check-input" type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" id="del_img_<?= $img['id'] ?>">
                                                <label class="form-check-label text-danger small ms-1" for="del_img_<?= $img['id'] ?>">Delete</label>
                                            </div>
                                            <?php if ($img['is_primary']): ?>
                                                <span class="badge bg-primary position-absolute top-0 start-0 m-1">Cover</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Delivery, Categorization, Offers & SEO -->
            <div class="col-lg-5">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Delivery & Categorization</h5>
                
                <!-- Delivery Type (Radio) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary d-block">Delivery Type *</label>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="delivery_type" id="del_express" value="Express" <?= !$is_edit || $product['delivery_type'] === 'Express' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="del_express">⚡ Express Delivery</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="delivery_type" id="del_courier" value="Courier" <?= $is_edit && $product['delivery_type'] === 'Courier' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="del_courier">📦 Courier (7 days)</label>
                    </div>
                </div>

                <!-- Categories Checklist -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary d-block">Categories</label>
                    <div class="p-3 bg-light rounded-3 border" style="max-height: 150px; overflow-y: auto;">
                        <?php foreach ($categories as $cat): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>" <?= $is_edit && in_array($cat['id'], $product['category_ids']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Offer Select List -->
                <div class="mb-3">
                    <label for="offer_id" class="form-label small fw-bold text-secondary">Active Offer / Discount Rule</label>
                    <select name="offer_id" id="offer_id" class="form-select bg-light">
                        <option value="">None (No Discount)</option>
                        <?php foreach ($offers as $off): ?>
                            <option value="<?= $off['id'] ?>" <?= $is_edit && $product['offer_id'] == $off['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($off['name']) ?> (<?= $off['type'] === 'percent' ? (int)$off['value'] . '%' : '₹' . (int)$off['value'] ?> off)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SEO settings -->
                <h5 class="fw-bold mt-4 mb-3 border-bottom pb-2">SEO Variables</h5>
                
                <div class="mb-3">
                    <label for="meta_title" class="form-label small fw-bold text-secondary">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control bg-light" value="<?= $is_edit ? htmlspecialchars($product['meta_title']) : '' ?>" placeholder="SEO title tag">
                </div>

                <div class="mb-3">
                    <label for="meta_desc" class="form-label small fw-bold text-secondary">Meta Description</label>
                    <textarea name="meta_desc" id="meta_desc" rows="3" class="form-control bg-light" placeholder="SEO description tag"><?= $is_edit ? htmlspecialchars($product['meta_desc']) : '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary d-block">Status</label>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= !$is_edit || $product['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Publish (Make visible in store)</label>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">
        
        <div class="d-flex gap-2 justify-content-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Product</button>
            <a href="<?= base_url('admin/products') ?>" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">Cancel</a>
        </div>
    </form>
</div>
