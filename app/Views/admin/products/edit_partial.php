<form action="<?= base_url('admin/products/edit/' . $product['id']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="row">
        <!-- Left Column: Core Fields -->
        <div class="col-lg-8">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="modal-name-input" class="form-control" value="<?= esc($product['name']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Slug / URL Handle <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="modal-slug-input" class="form-control" value="<?= esc($product['slug']) ?>" required>
                    <div id="modal-slug-feedback" class="mt-1" style="display:none;"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Product Type <span class="text-danger">*</span></label>
                    <select name="product_type" id="modal_product_type" class="form-select" required>
                        <option value="simple" <?= ($product['product_type'] === 'simple') ? 'selected' : '' ?>>Simple Product</option>
                        <option value="combo" <?= ($product['product_type'] === 'combo') ? 'selected' : '' ?>>Combo / Bundle Pack</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" class="form-control" value="<?= esc($product['sku']) ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Base Price (INR) <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" class="form-control" value="<?= esc($product['price']) ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Product Color</label>
                    <input type="text" name="color" class="form-control" value="<?= esc($product['color'] ?? '') ?>" placeholder="e.g. Red, Black, Pink">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Link Offer / Discount</label>
                <select name="offer_id" class="form-select">
                    <option value="">None (No Discount)</option>
                    <?php foreach ($offers as $off): ?>
                        <option value="<?= $off['id'] ?>" <?= ($off['id'] == $product['offer_id']) ? 'selected' : '' ?>><?= esc($off['name']) ?> (<?= $off['type'] === 'percent' ? (int)$off['value'] . '%' : 'Flat ₹' . number_format($off['value']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Short Description</label>
                <textarea name="short_description" class="form-control" rows="3" placeholder="Brief summary of the product (shows next to price)..."><?= esc($product['short_description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <!-- Target id description-editor so layout.php initializes CKEditor on it! -->
                <textarea name="description" id="description-editor" class="form-control" rows="6"><?= esc($product['description']) ?></textarea>
            </div>

            <!-- Combo Bundle Configuration Card inside Modal -->
            <div class="card border-0 shadow-sm p-3 mb-3" id="modal-combo-items-container" style="background: #ffffff; border-radius: 10px; display: <?= $product['product_type'] === 'combo' ? 'block' : 'none' ?>;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;"><i class="far fa-boxes me-2"></i> Combo Constituent Items</h6>
                <p class="small text-muted mb-2">Search and select the individual products that make up this combo pack.</p>
                
                <div id="modal-combo-rows">
                    <?php if (!empty($product['combo_items'])): ?>
                        <?php foreach ($product['combo_items'] as $item): ?>
                            <div class="row mb-2 align-items-center modal-combo-item-row-edit">
                                <div class="col-8">
                                    <select name="combo_product_ids[]" class="form-select combo-product-select form-select-sm">
                                        <option value="">-- Select Product --</option>
                                        <?php foreach ($all_products as $ap): ?>
                                            <option value="<?= $ap['id'] ?>" <?= ($ap['id'] == $item['child_product_id']) ? 'selected' : '' ?>><?= esc($ap['name']) ?> (₹<?= $ap['price'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" name="combo_qtys[]" class="form-control combo-qty-input form-control-sm" placeholder="Qty" value="<?= $item['qty'] ?>" min="1">
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-combo-row"><i class="far fa-trash-alt"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row mb-2 align-items-center modal-combo-item-row-edit">
                            <div class="col-8">
                                <select name="combo_product_ids[]" class="form-select combo-product-select form-select-sm">
                                    <option value="">-- Select Product --</option>
                                    <?php foreach ($all_products as $ap): ?>
                                        <option value="<?= $ap['id'] ?>"><?= esc($ap['name']) ?> (₹<?= $ap['price'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="number" name="combo_qtys[]" class="form-control combo-qty-input form-control-sm" placeholder="Qty" value="1" min="1">
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-combo-row"><i class="far fa-trash-alt"></i></button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-outline-cyan btn-sm mt-1" id="modal-add-combo-row-btn"><i class="far fa-plus"></i> Add Item</button>
            </div>

            <h6 class="text-cyan mb-2">Existing Images & Alt Tags</h6>
            <div class="row mb-3 g-2">
                <?php if (empty($product['images'])): ?>
                    <div class="col-12 text-muted">No images uploaded for this product.</div>
                <?php else: ?>
                    <?php foreach ($product['images'] as $img): ?>
                        <div class="col-md-3 text-center mb-2">
                            <div class="p-1 border rounded bg-light">
                                <img src="<?= base_url($img['image_path']) ?>" alt="<?= esc($img['alt'] ?? '') ?>" style="width: 60px; height: 60px; object-fit: cover;" class="rounded border mb-1 d-block mx-auto">
                                <input type="text" name="existing_image_alts[<?= $img['id'] ?>]" value="<?= esc($img['alt'] ?? '') ?>" class="form-control form-control-xs mb-1 py-0 px-1 text-center" style="font-size:0.75rem;" placeholder="Alt text">
                                <a href="<?= base_url('admin/products/delete-image/' . $img['id']) ?>" class="btn btn-danger btn-xs d-block mx-auto py-0" style="font-size: 0.6rem;" onclick="return confirm('Are you sure you want to delete this image?')"><i class="far fa-trash-alt"></i> Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">Upload Additional Images & Alts</label>
                <div id="modal-product-images-container">
                    <div class="row g-1 mb-1 align-items-center modal-image-upload-row">
                        <div class="col-md-7">
                            <input type="file" name="images[]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="image_alts[]" class="form-control form-control-sm" placeholder="Alt text">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-image-row-btn" disabled><i class="far fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-cyan btn-sm mt-1" id="modal-add-image-row-btn"><i class="far fa-plus"></i> Add Image</button>
            </div>
        </div>

        <!-- Right Column: Settings, Customizations & SEO -->
        <div class="col-lg-4">
            <!-- Categories Multiselect styled checkbox list -->
            <div class="card border-0 shadow-sm p-3 mb-3" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;">Categories <span class="text-danger">*</span></h6>
                <div class="mb-2">
                    <input type="text" id="modal-category-search" class="form-control form-control-sm" placeholder="Search categories...">
                </div>
                <div style="max-height: 120px; overflow-y: auto;" class="border rounded p-2 bg-light" id="modal-categories-list-container">
                    <?php foreach ($categories as $cat): ?>
                        <div class="form-check mb-1 category-item-row" data-id="<?= $cat['id'] ?>" data-name="<?= esc(strtolower($cat['name'])) ?>">
                            <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= $cat['id'] ?>" id="modal_cat_<?= $cat['id'] ?>" <?= in_array($cat['id'], $product['category_ids'] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label text-dark small" for="modal_cat_<?= $cat['id'] ?>">
                                <?= esc($cat['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Delivery options -->
            <div class="card border-0 shadow-sm p-3 mb-3" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;">Delivery Option</h6>
                <select name="delivery_type" id="modal_delivery_type" class="form-select form-select-sm" required>
                    <option value="Express" <?= ($product['delivery_type'] === 'Express') ? 'selected' : '' ?>>Express (Same-Day)</option>
                    <option value="Courier" <?= ($product['delivery_type'] === 'Courier') ? 'selected' : '' ?>>Courier (7 Days)</option>
                </select>
            </div>

            <!-- City Visibility and Custom Pricing Card -->
            <div class="card border-0 shadow-sm p-3 mb-3" id="modal-city-mapping-card" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;">City Mapping & Pricing</h6>
                <div style="max-height: 150px; overflow-y: auto;" class="border rounded p-2 bg-light">
                    <?php foreach ($cities as $ct): 
                        $isMapped = isset($product['city_mappings'][$ct['id']]);
                        $priceOverride = $isMapped ? $product['city_mappings'][$ct['id']] : '';
                    ?>
                        <div class="mb-2 border-bottom pb-1">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input modal-city-enable-check" type="checkbox" name="city_ids[]" value="<?= $ct['id'] ?>" id="modal_city_<?= $ct['id'] ?>" <?= $isMapped ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark fw-bold small" for="modal_city_<?= $ct['id'] ?>"><?= esc($ct['name']) ?></label>
                            </div>
                            <div class="ps-3 modal-city-price-container" id="modal_price_container_<?= $ct['id'] ?>">
                                <input type="number" name="city_prices[<?= $ct['id'] ?>]" step="0.01" class="form-control form-control-xs py-0" value="<?= esc($priceOverride) ?>" style="font-size:0.75rem;" placeholder="Base Price will apply">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Customization Settings -->
            <div class="card border-0 shadow-sm p-3 mb-3" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;">Customization</h6>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_customizable" value="1" id="modal_is_customizable" <?= $product['is_customizable'] ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark fw-bold small" for="modal_is_customizable">Is Customizable?</label>
                </div>
                <div class="mb-1" id="modal_customization_type_container" style="<?= $product['is_customizable'] ? '' : 'display:none;' ?>">
                    <select name="customization_type" class="form-select form-select-sm">
                        <option value="text" <?= ($product['customization_type'] === 'text') ? 'selected' : '' ?>>Card Message</option>
                        <option value="image" <?= ($product['customization_type'] === 'image') ? 'selected' : '' ?>>Photo Upload</option>
                        <option value="both" <?= ($product['customization_type'] === 'both') ? 'selected' : '' ?>>Both</option>
                    </select>
                </div>
            </div>

            <!-- Custom Flags -->
            <div class="card border-0 shadow-sm p-3 mb-3" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;">Product Flags</h6>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="modal_is_bestseller" <?= $product['is_bestseller'] ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark small" for="modal_is_bestseller">Best Seller</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="is_onsale" value="1" id="modal_is_onsale" <?= $product['is_onsale'] ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark small" for="modal_is_onsale">On Sale</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="is_toprated" value="1" id="modal_is_toprated" <?= $product['is_toprated'] ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark small" for="modal_is_toprated">Top Rated</label>
                </div>
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="is_trending" value="1" id="modal_is_trending" <?= $product['is_trending'] ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark small" for="modal_is_trending">Trending</label>
                </div>
            </div>

            <!-- Website Visibility -->
            <div class="card border-0 shadow-sm p-3 mb-3" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;">Website Visibility</h6>
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="hide_from_frontend" value="1" id="modal_hide_from_frontend" <?= $product['hide_from_frontend'] ? 'checked' : '' ?>>
                    <label class="form-check-label text-dark fw-bold small" for="modal_hide_from_frontend">Hide from listings</label>
                </div>
            </div>

            <!-- Availability -->
            <div class="card border-0 shadow-sm p-3" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-2" style="font-weight: 600;">Availability</h6>
                <select name="is_active" class="form-select form-select-sm">
                    <option value="1" <?= $product['is_active'] ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= !$product['is_active'] ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SEO Metadata (Full Width) -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm p-3 mb-3" style="background: #ffffff; border-radius: 10px;">
                <h6 class="text-cyan mb-3" style="font-weight: 600;">SEO Metadata</h6>
                <div class="mb-3">
                    <label class="form-label small mb-1">Meta Title</label>
                    <input type="text" name="meta_title" class="form-control form-control-sm" value="<?= esc($product['meta_title']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small mb-1">Meta Description</label>
                    <textarea name="meta_desc" class="form-control form-control-sm" rows="3"><?= esc($product['meta_desc']) ?></textarea>
                </div>
                
                <!-- Advanced Social SEO -->
                <hr class="my-3">
                <div class="row">
                    <!-- Left Column: Open Graph Tags -->
                    <div class="col-md-6 border-end">
                        <h6 class="text-dark fw-bold small mb-2">Open Graph (Facebook / WhatsApp)</h6>
                        <div class="mb-2">
                            <label class="form-label small mb-1">OG Title</label>
                            <input type="text" name="og_title" class="form-control form-control-sm" value="<?= esc($product['og_title'] ?? '') ?>" placeholder="Fallback: Meta Title">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">OG Description</label>
                            <textarea name="og_desc" class="form-control form-control-sm" rows="2" placeholder="Fallback: Meta Description"><?= esc($product['og_desc'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">OG Share Image</label>
                            <input type="file" name="og_image_file" class="form-control form-control-sm mb-1">
                            <?php if (!empty($product['og_image'])): ?>
                                <img src="<?= base_url($product['og_image']) ?>" alt="OG Share Image" style="max-height: 60px; border-radius: 4px;" class="border mt-1">
                            <?php endif; ?>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">OG Type</label>
                            <select name="og_type" class="form-select form-select-sm">
                                <option value="product" <?= ($product['og_type'] ?? 'product') === 'product' ? 'selected' : '' ?>>Product</option>
                                <option value="website" <?= ($product['og_type'] ?? '') === 'website' ? 'selected' : '' ?>>Website</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column: Twitter Cards -->
                    <div class="col-md-6">
                        <h6 class="text-dark fw-bold small mb-2">Twitter Cards</h6>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Twitter Card Type</label>
                            <select name="twitter_card" class="form-select form-select-sm">
                                <option value="summary_large_image" <?= ($product['twitter_card'] ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' ?>>Summary Card with Large Image</option>
                                <option value="summary" <?= ($product['twitter_card'] ?? '') === 'summary' ? 'selected' : '' ?>>Standard Summary Card</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Twitter Title</label>
                            <input type="text" name="twitter_title" class="form-control form-control-sm" value="<?= esc($product['twitter_title'] ?? '') ?>" placeholder="Fallback: OG Title">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Twitter Description</label>
                            <textarea name="twitter_desc" class="form-control form-control-sm" rows="2" placeholder="Fallback: OG Description"><?= esc($product['twitter_desc'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1">Twitter Share Image</label>
                            <input type="file" name="twitter_image_file" class="form-control form-control-sm mb-1">
                            <?php if (!empty($product['twitter_image'])): ?>
                                <img src="<?= base_url($product['twitter_image']) ?>" alt="Twitter Share Image" style="max-height: 60px; border-radius: 4px;" class="border mt-1">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <hr class="my-3">
                <h6 class="text-dark fw-bold small mb-2">Schema Markup (JSON-LD)</h6>
                <div class="mb-0">
                    <textarea name="schema_markup" class="form-control form-control-sm font-monospace" rows="4" style="font-size: 0.75rem;" placeholder='{"@context": "https://schema.org", ...}'><?= esc($product['schema_markup'] ?? '') ?></textarea>
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
    const PRODUCT_EDIT_ID = <?= (int)$product['id'] ?>;

    // 1. Auto-Slug Generation Logic in Modal + Real-time Duplicate Check
    const nameInput = document.getElementById('modal-name-input');
    const slugInput = document.getElementById('modal-slug-input');
    const slugFeedback = document.getElementById('modal-slug-feedback');
    let autoSlug = false;
    let slugCheckTimer = null;

    function checkModalProductSlug(slug) {
        if (!slug || slug.length < 2) { slugFeedback.style.display = 'none'; return; }
        clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(function() {
            var url = '<?= base_url('admin/products/check-slug') ?>?slug=' + encodeURIComponent(slug) + '&id=' + PRODUCT_EDIT_ID;
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

    nameInput.addEventListener('input', function() {
        if (autoSlug) {
            let slug = this.value.toLowerCase()
                                 .replace(/[^a-z0-9\s-]/g, '')
                                 .replace(/\s+/g, '-')
                                 .replace(/-+/g, '-');
            slugInput.value = slug;
            checkModalProductSlug(slug);
        }
    });

    slugInput.addEventListener('input', function() {
        autoSlug = (this.value === "");
        checkModalProductSlug(this.value);
    });

    // 2. Toggle Customization fields in Modal
    const customizableCheckbox = document.getElementById('modal_is_customizable');
    const customizationContainer = document.getElementById('modal_customization_type_container');

    if (customizableCheckbox && customizationContainer) {
        customizableCheckbox.addEventListener('change', function() {
            customizationContainer.style.display = this.checked ? 'block' : 'none';
        });
    }

    // 3. Category search with sort to top logic inside Modal
    const modalCatSearch = document.getElementById('modal-category-search');
    const modalCatContainer = document.getElementById('modal-categories-list-container');
    if (modalCatSearch && modalCatContainer) {
        modalCatSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = Array.from(modalCatContainer.getElementsByClassName('category-item-row'));
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                if (name.includes(query)) {
                    row.style.setProperty('display', 'block', 'important');
                    if (query !== '') {
                        modalCatContainer.prepend(row);
                    }
                } else {
                    row.style.setProperty('display', 'none', 'important');
                }
            });
            
            if (query === '') {
                rows.sort((a, b) => {
                    return parseInt(a.getAttribute('data-id')) - parseInt(b.getAttribute('data-id'));
                }).forEach(row => modalCatContainer.appendChild(row));
            }
        });
    }

    // 4. Product Type Toggle in Modal
    const modalProductType = document.getElementById('modal_product_type');
    const modalComboContainer = document.getElementById('modal-combo-items-container');
    if (modalProductType && modalComboContainer) {
        modalProductType.addEventListener('change', function() {
            modalComboContainer.style.display = this.value === 'combo' ? 'block' : 'none';
        });
    }

    // 5. Combo Rows Dynamic Management in Modal
    const modalComboRows = document.getElementById('modal-combo-rows');
    const modalAddComboBtn = document.getElementById('modal-add-combo-row-btn');
    if (modalComboRows && modalAddComboBtn) {
        // Initialize initial select2
        $(modalComboRows).find('.combo-product-select').select2({ placeholder: "-- Select Product --", allowClear: true });

        modalAddComboBtn.addEventListener('click', function() {
            const rowsList = modalComboRows.querySelectorAll('.modal-combo-item-row-edit');
            let firstRow = rowsList.length > 0 ? rowsList[0] : null;
            if (firstRow) {
                // Temporarily destroy select2 on firstRow to clone it cleanly
                const firstSelect = firstRow.querySelector('select');
                try {
                    $(firstSelect).select2('destroy');
                } catch(e) {}

                const newRow = firstRow.cloneNode(true);
                
                // Re-initialize select2 on firstRow
                $(firstSelect).select2({ placeholder: "-- Select Product --", allowClear: true });

                const newSelect = newRow.querySelector('select');
                newSelect.value = '';
                newRow.querySelector('input').value = '1';
                
                newRow.querySelector('.remove-combo-row').addEventListener('click', function() {
                    if (modalComboRows.querySelectorAll('.modal-combo-item-row-edit').length > 1) {
                        try {
                            $(newSelect).select2('destroy');
                        } catch(e) {}
                        newRow.remove();
                    } else {
                        alert('At least one item is required for combo products.');
                    }
                });
                
                modalComboRows.appendChild(newRow);

                // Initialize select2 on the new row's select
                $(newSelect).select2({ placeholder: "-- Select Product --", allowClear: true });
            }
        });

        modalComboRows.querySelectorAll('.remove-combo-row').forEach(btn => {
            btn.addEventListener('click', function() {
                if (modalComboRows.querySelectorAll('.modal-combo-item-row-edit').length > 1) {
                    const rowSelect = this.closest('.modal-combo-item-row-edit').querySelector('select');
                    try {
                        $(rowSelect).select2('destroy');
                    } catch(e) {}
                    this.closest('.modal-combo-item-row-edit').remove();
                } else {
                    alert('At least one item is required for combo products.');
                }
            });
        });
    }

    // 6. Delivery Type Toggle in Modal
    const modalDeliveryType = document.getElementById('modal_delivery_type');
    const modalCityMappingCard = document.getElementById('modal-city-mapping-card');
    if (modalDeliveryType && modalCityMappingCard) {
        const toggleModalCityMapping = () => {
            modalCityMappingCard.style.display = modalDeliveryType.value === 'Express' ? 'block' : 'none';
        };
        modalDeliveryType.addEventListener('change', toggleModalCityMapping);
        toggleModalCityMapping();
    }

    // 7. City Checkbox Input State in Modal
    document.querySelectorAll('.modal-city-enable-check').forEach(chk => {
        const updatePriceInputState = (checkbox) => {
            const container = document.getElementById('modal_price_container_' + checkbox.value);
            if (container) {
                const input = container.querySelector('input');
                if (input) {
                    input.disabled = !checkbox.checked;
                    if (!checkbox.checked) input.value = '';
                }
            }
        };
        chk.addEventListener('change', function() {
            updatePriceInputState(this);
        });
        updatePriceInputState(chk);
    });

    // 8. Product Images Rows Dynamic Management in Modal
    const modalImagesContainer = document.getElementById('modal-product-images-container');
    const modalAddImageBtn = document.getElementById('modal-add-image-row-btn');
    if (modalImagesContainer && modalAddImageBtn) {
        modalAddImageBtn.addEventListener('click', function() {
            const firstRow = modalImagesContainer.querySelector('.modal-image-upload-row');
            if (firstRow) {
                const newRow = firstRow.cloneNode(true);
                newRow.querySelector('input[type="file"]').value = '';
                newRow.querySelector('input[type="text"]').value = '';
                
                const delBtn = newRow.querySelector('.remove-image-row-btn');
                delBtn.removeAttribute('disabled');
                delBtn.addEventListener('click', function() {
                    newRow.remove();
                });
                
                modalImagesContainer.appendChild(newRow);
            }
        });

        modalImagesContainer.querySelectorAll('.remove-image-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (modalImagesContainer.querySelectorAll('.modal-image-upload-row').length > 1) {
                    this.closest('.modal-image-upload-row').remove();
                }
            });
        });
    }
})();
</script>
