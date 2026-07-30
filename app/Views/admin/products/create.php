<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card-custom mb-4">
            <h4 class="mb-4 text-white"><i class="far fa-plus-circle me-2 text-cyan"></i> Add New Product</h4>
            <form action="<?= base_url('admin/products/create') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <!-- Left Column: Core Fields -->
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name-input" class="form-control" placeholder="e.g. Red Velvet Valentine Cake" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug / URL Handle <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="slug-input" class="form-control" placeholder="e.g. red-velvet-valentine-cake" required>
                                <div id="slug-feedback" class="mt-1" style="display:none;"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Product Type <span class="text-danger">*</span></label>
                                <select name="product_type" id="product_type" class="form-select" required>
                                    <option value="simple">Simple Product</option>
                                    <option value="combo">Combo / Bundle Pack</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" name="sku" class="form-control" placeholder="e.g. CAKE-RV-1KG" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Base Price (INR) <span class="text-danger">*</span></label>
                                <input type="number" name="price" step="0.01" class="form-control" placeholder="Original Price" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Product Colors</label>
                                <select name="color_ids[]" class="form-select select2-colors-multiple" multiple="multiple">
                                    <?php foreach ($colors as $col): ?>
                                        <option value="<?= $col['id'] ?>"><?= esc($col['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link Offer / Discount</label>
                            <select name="offer_id" class="form-select">
                                <option value="">None (No Discount)</option>
                                <?php foreach ($offers as $off): ?>
                                    <option value="<?= $off['id'] ?>"><?= esc($off['name']) ?> (<?= $off['type'] === 'percent' ? (int)$off['value'] . '%' : 'Flat ₹' . number_format($off['value']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="short_description_editor" class="form-control" rows="3" placeholder="Brief summary of the product (shows next to price)..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="editor" class="form-control" rows="8" placeholder="Detailed product details..."></textarea>
                        </div>

                        <!-- Combo Bundle Configuration Card -->
                        <div class="card border-0 shadow-sm p-4 mb-4" id="combo-items-container" style="background: #ffffff; border-radius: 12px; display: none;">
                            <h5 class="text-cyan mb-3" style="font-weight: 600;"><i class="far fa-boxes me-2"></i> Combo Constituent Items</h5>
                            <p class="small text-muted mb-3">Search and select the individual products that make up this combo pack.</p>
                            
                            <div id="combo-rows">
                                <div class="row mb-2 align-items-center combo-item-row-edit">
                                    <div class="col-8">
                                        <select name="combo_product_ids[]" class="form-select combo-product-select">
                                            <option value="">-- Select Product --</option>
                                            <?php foreach ($all_products as $ap): ?>
                                                <option value="<?= $ap['id'] ?>"><?= esc($ap['name']) ?> (₹<?= $ap['price'] ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" name="combo_qtys[]" class="form-control combo-qty-input" placeholder="Qty" value="1" min="1">
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-combo-row"><i class="far fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-combo-row-btn"><i class="far fa-plus"></i> Add Item</button>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold text-cyan d-block">Upload Product Images & Alt Tags</label>
                            <div id="product-images-container">
                                <div class="row g-2 mb-2 align-items-center image-upload-row">
                                    <div class="col-md-7">
                                        <input type="file" name="images[]" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="image_alts[]" class="form-control" placeholder="Alt text (SEO description)">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-image-row-btn" disabled><i class="far fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-cyan btn-sm mt-2" id="add-image-row-btn"><i class="far fa-plus"></i> Add Another Image</button>
                            <small class="text-muted d-block mt-2">Allowed types: jpg, jpeg, png, webp. Maximum size: 2MB per file.</small>
                        </div>

                        <!-- SEO Metadata -->
                        <div class="card border-0 shadow-sm p-3 mb-4" style="background: #ffffff; border-radius: 12px;">
                            <h5 class="text-cyan mb-3" style="font-weight: 600;">SEO Metadata</h5>
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" placeholder="SEO Title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_desc" class="form-control" rows="3" placeholder="SEO Description"></textarea>
                            </div>
                            <!-- Advanced Social SEO -->
                            <hr class="my-4">
                            <div class="row">
                                <!-- Left Column: Open Graph Tags -->
                                <div class="col-md-6 border-end">
                                    <h6 class="text-dark fw-bold mb-3">Open Graph (Facebook / WhatsApp)</h6>
                                    <div class="mb-3">
                                        <label class="form-label">OG Title</label>
                                        <input type="text" name="og_title" class="form-control" placeholder="Fallback: Meta Title">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">OG Description</label>
                                        <textarea name="og_desc" class="form-control" rows="2" placeholder="Fallback: Meta Description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">OG Share Image</label>
                                        <input type="file" name="og_image_file" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">OG Type</label>
                                        <select name="og_type" class="form-select">
                                            <option value="product">Product</option>
                                            <option value="website">Website</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Right Column: Twitter Cards -->
                                <div class="col-md-6">
                                    <h6 class="text-dark fw-bold mb-3">Twitter Cards</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Twitter Card Type</label>
                                        <select name="twitter_card" class="form-select">
                                            <option value="summary_large_image">Summary Card with Large Image</option>
                                            <option value="summary">Standard Summary Card</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Twitter Title</label>
                                        <input type="text" name="twitter_title" class="form-control" placeholder="Fallback: OG Title">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Twitter Description</label>
                                        <textarea name="twitter_desc" class="form-control" rows="2" placeholder="Fallback: OG Description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Twitter Share Image</label>
                                        <input type="file" name="twitter_image_file" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="text-dark fw-bold mb-3">Schema Markup (JSON-LD)</h6>
                            <div class="mb-2">
                                <textarea name="schema_markup" class="form-control font-monospace" rows="5" style="font-size: 0.8rem;" placeholder='{&#10;  "@context": "https://schema.org",&#10;  "@type": "Product",&#10;  ...&#10;}'></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings, Customizations & SEO -->
                    <div class="col-lg-4">
                        <!-- Categories Multiselect styled checkbox list -->
                        <div class="card border-0 shadow-sm p-3 mb-4" style="background: #ffffff; border-radius: 12px;">
                            <h5 class="text-cyan mb-2" style="font-weight: 600;">Product Categories <span class="text-danger">*</span></h5>
                            <div class="mb-2">
                                <input type="text" id="category-search" class="form-control form-control-sm" placeholder="Search categories...">
                            </div>
                            <div style="max-height: 180px; overflow-y: auto;" class="border rounded p-2 bg-light" id="categories-list-container">
                                <?php foreach ($categories as $cat): ?>
                                    <div class="form-check mb-2 category-item-row" data-id="<?= $cat['id'] ?>" data-name="<?= esc(strtolower($cat['name'])) ?>" style="margin-left: <?= ($cat['depth'] ?? 0) * 24 ?>px;">
                                        <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>">
                                        <label class="form-check-label text-dark" for="cat_<?= $cat['id'] ?>" style="cursor: pointer;">
                                            <?php if (($cat['depth'] ?? 0) > 0): ?>
                                                <span class="text-muted"><?= str_repeat('—', $cat['depth']) ?></span> 
                                            <?php endif; ?>
                                            <?= esc($cat['name']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Delivery details -->
                        <div class="card border-0 shadow-sm p-3 mb-4" style="background: #ffffff; border-radius: 12px;">
                            <h5 class="text-cyan mb-3" style="font-weight: 600;">Delivery Options</h5>
                            <div class="mb-3">
                                <label class="form-label">Delivery Type <span class="text-danger">*</span></label>
                                <select name="delivery_type" id="delivery_type" class="form-select" required>
                                    <option value="Express">Express (Same-Day Delivery)</option>
                                    <option value="Courier">Courier (7 Days Delivery)</option>
                                </select>
                            </div>
                        </div>

                        <!-- City Visibility and Custom Pricing Card -->
                        <div class="card border-0 shadow-sm p-3 mb-4" id="city-mapping-card" style="background: #ffffff; border-radius: 12px;">
                            <h5 class="text-cyan mb-3" style="font-weight: 600;">City Mapping & Pricing</h5>
                            <p class="small text-muted mb-2">Available for Express same-day delivery in selected cities:</p>
                            <div style="max-height: 250px; overflow-y: auto;" class="border rounded p-2 bg-light">
                                <?php foreach ($cities as $ct): ?>
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input city-enable-check" type="checkbox" name="city_ids[]" value="<?= $ct['id'] ?>" id="city_<?= $ct['id'] ?>" checked>
                                            <label class="form-check-label text-dark fw-bold" for="city_<?= $ct['id'] ?>"><?= esc($ct['name']) ?></label>
                                        </div>
                                        <div class="ps-4 city-price-container" id="price_container_<?= $ct['id'] ?>">
                                            <label class="small text-muted">Price Override (₹)</label>
                                            <input type="number" name="city_prices[<?= $ct['id'] ?>]" step="0.01" class="form-control form-control-sm" placeholder="Base Price will apply">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Customization Settings -->
                        <div class="card border-0 shadow-sm p-3 mb-4" style="background: #ffffff; border-radius: 12px;">
                            <h5 class="text-cyan mb-3" style="font-weight: 600;">Customization Toggles</h5>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_customizable" value="1" id="is_customizable">
                                <label class="form-check-label text-dark fw-bold" for="is_customizable">Is Customizable?</label>
                            </div>
                            <div class="mb-2" id="customization_type_container" style="display:none;">
                                <label class="form-label">Customization Type (e.g. Card Message, Photo Upload)</label>
                                <select name="customization_type" class="form-select">
                                    <option value="text">Card Message (Text Input)</option>
                                    <option value="image">Photo Upload (Image Upload)</option>
                                    <option value="both">Both Message & Photo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Custom Flags -->
                        <div class="card border-0 shadow-sm p-3 mb-4" style="background: #ffffff; border-radius: 12px;">
                            <h5 class="text-cyan mb-3" style="font-weight: 600;">Product Badges / Flags</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_bestseller" value="1" id="is_bestseller">
                                <label class="form-check-label text-dark" for="is_bestseller">Best Seller</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_onsale" value="1" id="is_onsale">
                                <label class="form-check-label text-dark" for="is_onsale">On Sale</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_toprated" value="1" id="is_toprated">
                                <label class="form-check-label text-dark" for="is_toprated">Top Rated</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_trending" value="1" id="is_trending">
                                <label class="form-check-label text-dark" for="is_trending">Trending</label>
                            </div>
                        </div>

                        <!-- Visibility Option -->
                        <div class="card border-0 shadow-sm p-3 mb-4" style="background: #ffffff; border-radius: 12px;">
                            <h5 class="text-cyan mb-3" style="font-weight: 600;">Website Visibility</h5>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="hide_from_frontend" value="1" id="hide_from_frontend">
                                <label class="form-check-label text-dark fw-bold" for="hide_from_frontend">Hide from listings</label>
                                <small class="text-muted d-block">If checked, this product will not show up on category listings, shop listing, or the homepage, but remains active for direct link access.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/products') ?>" class="btn btn-outline-secondary"><i class="far fa-arrow-left"></i> Cancel</a>
                    <button type="submit" class="btn-cyan px-4">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize CKEditor on Description and Short Description
    initAppCKEditor('#editor');
    initAppCKEditor('#short_description_editor');

    // Initialize Select2 colors multiple
    $('.select2-colors-multiple').select2({ placeholder: "Choose colors...", allowClear: true });

    // 2. Editable Auto-Slug Generation Logic + Real-time Duplicate Check
    const nameInput = document.getElementById('name-input');
    const slugInput = document.getElementById('slug-input');
    const slugFeedback = document.getElementById('slug-feedback');
    let autoSlug = true;
    let slugCheckTimer = null;

    function checkProductSlug(slug) {
        if (!slug || slug.length < 2) { slugFeedback.style.display = 'none'; return; }
        clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(function() {
            var url = '<?= base_url('admin/products/check-slug') ?>?slug=' + encodeURIComponent(slug);
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
            checkProductSlug(slug);
        }
    });

    slugInput.addEventListener('input', function() {
        autoSlug = (this.value === "");
        checkProductSlug(this.value);
    });

    // 3. Toggle Customization fields
    const customizableCheckbox = document.getElementById('is_customizable');
    const customizationContainer = document.getElementById('customization_type_container');

    if (customizableCheckbox && customizationContainer) {
        customizableCheckbox.addEventListener('change', function() {
            customizationContainer.style.display = this.checked ? 'block' : 'none';
        });
    }

    // 4. Category search with sort to top logic
    const catSearchInput = document.getElementById('category-search');
    const catContainer = document.getElementById('categories-list-container');
    if (catSearchInput && catContainer) {
        catSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = Array.from(catContainer.getElementsByClassName('category-item-row'));
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                if (name.includes(query)) {
                    row.style.setProperty('display', 'block', 'important');
                    if (query !== '') {
                        catContainer.prepend(row);
                    }
                } else {
                    row.style.setProperty('display', 'none', 'important');
                }
            });
            
            if (query === '') {
                rows.sort((a, b) => {
                    return parseInt(a.getAttribute('data-id')) - parseInt(b.getAttribute('data-id'));
                }).forEach(row => catContainer.appendChild(row));
            }
        });
    }

    // 5. Product Type Toggle (Simple vs Combo)
    const productTypeSelect = document.getElementById('product_type');
    const comboContainer = document.getElementById('combo-items-container');
    if (productTypeSelect && comboContainer) {
        productTypeSelect.addEventListener('change', function() {
            comboContainer.style.display = this.value === 'combo' ? 'block' : 'none';
        });
    }

    // 6. Delivery Type Toggle (Express vs Courier)
    const deliveryTypeSelect = document.getElementById('delivery_type');
    const cityMappingCard = document.getElementById('city-mapping-card');
    if (deliveryTypeSelect && cityMappingCard) {
        const toggleCityMapping = () => {
            cityMappingCard.style.display = deliveryTypeSelect.value === 'Express' ? 'block' : 'none';
        };
        deliveryTypeSelect.addEventListener('change', toggleCityMapping);
        toggleCityMapping(); // Run initially
    }

    // 7. City Checkbox Toggle Input State
    document.querySelectorAll('.city-enable-check').forEach(chk => {
        const updatePriceInputState = (checkbox) => {
            const container = document.getElementById('price_container_' + checkbox.value);
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
        updatePriceInputState(chk); // Run initially
    });

    // 8. Combo Rows Dynamic Management
    const comboRows = document.getElementById('combo-rows');
    const addComboBtn = document.getElementById('add-combo-row-btn');
    if (comboRows && addComboBtn) {
        // Initialize initial select2
        $('.combo-product-select').select2({ placeholder: "-- Select Product --", allowClear: true });

        addComboBtn.addEventListener('click', function() {
            const firstRow = comboRows.querySelector('.combo-item-row-edit');
            if (firstRow) {
                // Temporarily destroy select2 on firstRow to clone it cleanly
                try {
                    $(firstRow.querySelector('select')).select2('destroy');
                } catch(e) {}

                const newRow = firstRow.cloneNode(true);
                
                // Re-initialize select2 on firstRow
                $(firstRow.querySelector('select')).select2({ placeholder: "-- Select Product --", allowClear: true });

                // Reset select and input values
                const newSelect = newRow.querySelector('select');
                newSelect.value = '';
                newRow.querySelector('input').value = '1';
                
                // Add event listener to delete button of new row
                newRow.querySelector('.remove-combo-row').addEventListener('click', function() {
                    if (comboRows.querySelectorAll('.combo-item-row-edit').length > 1) {
                        // Destroy select2 instance first
                        try {
                            $(newSelect).select2('destroy');
                        } catch(e) {}
                        newRow.remove();
                    } else {
                        alert('At least one item is required for combo products.');
                    }
                });
                
                comboRows.appendChild(newRow);

                // Initialize select2 on the new row's select
                $(newSelect).select2({ placeholder: "-- Select Product --", allowClear: true });
            }
        });

        // Delegate delete event for initial row
        comboRows.querySelectorAll('.remove-combo-row').forEach(btn => {
            btn.addEventListener('click', function() {
                if (comboRows.querySelectorAll('.combo-item-row-edit').length > 1) {
                    try {
                        $(this.closest('.combo-item-row-edit').querySelector('select')).select2('destroy');
                    } catch(e) {}
                    this.closest('.combo-item-row-edit').remove();
                } else {
                    alert('At least one item is required for combo products.');
                }
            });
        });
    }

    // 9. Product Images Rows Dynamic Management
    const imagesContainer = document.getElementById('product-images-container');
    const addImageBtn = document.getElementById('add-image-row-btn');
    if (imagesContainer && addImageBtn) {
        addImageBtn.addEventListener('click', function() {
            const firstRow = imagesContainer.querySelector('.image-upload-row');
            if (firstRow) {
                const newRow = firstRow.cloneNode(true);
                // Reset file and alt inputs
                newRow.querySelector('input[type="file"]').value = '';
                newRow.querySelector('input[type="file"]').removeAttribute('required');
                newRow.querySelector('input[type="text"]').value = '';
                
                // Enable delete button
                const delBtn = newRow.querySelector('.remove-image-row-btn');
                delBtn.removeAttribute('disabled');
                delBtn.addEventListener('click', function() {
                    newRow.remove();
                });
                
                imagesContainer.appendChild(newRow);
            }
        });

        // Add event listener to initial delete button just in case
        imagesContainer.querySelectorAll('.remove-image-row-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (imagesContainer.querySelectorAll('.image-upload-row').length > 1) {
                    this.closest('.image-upload-row').remove();
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
