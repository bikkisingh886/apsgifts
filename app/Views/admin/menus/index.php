<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <!-- Left Column: Add Menu Items -->
    <div class="col-lg-4 col-md-5 mb-4">
        <h5 class="text-dark fw-bold mb-3"><i class="far fa-plus-circle text-cyan me-2"></i> Add Menu Items</h5>
        
        <div class="accordion accordion-custom shadow-sm border-0" id="menuItemsAccordion" style="border-radius: 12px; overflow: hidden;">
            <!-- Accordion: System Pages -->
            <div class="accordion-item mb-2 border border-light" style="background: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2 class="accordion-header" id="headingPages">
                    <button class="accordion-button collapsed bg-light text-dark fw-bold border-bottom" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages" style="border-radius: 8px 8px 0 0;">
                        <i class="far fa-file-alt text-cyan me-2"></i> System Pages
                    </button>
                </h2>
                <div id="collapsePages" class="accordion-collapse collapse" aria-labelledby="headingPages" data-bs-parent="#menuItemsAccordion">
                    <div class="accordion-body text-dark p-3">
                        <div style="max-height: 200px; overflow-y: auto;" class="border border-light rounded p-2 mb-3 bg-white">
                            <?php foreach ($systemPages as $idx => $sp): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input page-checkbox" type="checkbox" value="<?= $idx ?>" id="page_<?= $idx ?>" data-title="<?= esc($sp['title']) ?>" data-url="<?= esc($sp['url']) ?>">
                                    <label class="form-check-label text-dark" for="page_<?= $idx ?>">
                                        <?= esc($sp['title']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-cyan btn-sm" id="btn-add-pages"><i class="far fa-plus me-1"></i> Add to Menu</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accordion: Categories -->
            <div class="accordion-item mb-2 border border-light" style="background: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2 class="accordion-header" id="headingCats">
                    <button class="accordion-button collapsed bg-light text-dark fw-bold border-bottom" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCats" aria-expanded="false" aria-controls="collapseCats">
                        <i class="far fa-folder text-cyan me-2"></i> Product Categories
                    </button>
                </h2>
                <div id="collapseCats" class="accordion-collapse collapse" aria-labelledby="headingCats" data-bs-parent="#menuItemsAccordion">
                    <div class="accordion-body text-dark p-3">
                        <div class="mb-2">
                            <input type="text" id="search-cat-builder" class="form-control form-control-sm border-secondary" placeholder="Search categories...">
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;" class="border border-light rounded p-2 mb-3 bg-white" id="categories-builder-list">
                            <?php foreach ($categories as $cat): ?>
                                <div class="form-check mb-2 category-builder-item" data-name="<?= esc(strtolower($cat['name'])) ?>" style="margin-left: <?= ($cat['depth'] ?? 0) * 20 ?>px;">
                                    <input class="form-check-input cat-checkbox" type="checkbox" value="<?= $cat['id'] ?>" id="builder_cat_<?= $cat['id'] ?>" data-title="<?= esc($cat['name']) ?>" data-slug="<?= esc($cat['slug_path'] ?? $cat['slug']) ?>">
                                    <label class="form-check-label text-dark" for="builder_cat_<?= $cat['id'] ?>" style="cursor: pointer;">
                                        <?php if (($cat['depth'] ?? 0) > 0): ?>
                                            <span class="text-muted"><?= str_repeat('—', $cat['depth']) ?></span> 
                                        <?php endif; ?>
                                        <?= esc($cat['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-cyan btn-sm" id="btn-add-cats"><i class="far fa-plus me-1"></i> Add to Menu</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accordion: Products -->
            <div class="accordion-item mb-2 border border-light" style="background: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2 class="accordion-header" id="headingProducts">
                    <button class="accordion-button collapsed bg-light text-dark fw-bold border-bottom" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProducts" aria-expanded="false" aria-controls="collapseProducts">
                        <i class="far fa-box-open text-cyan me-2"></i> Products
                    </button>
                </h2>
                <div id="collapseProducts" class="accordion-collapse collapse" aria-labelledby="headingProducts" data-bs-parent="#menuItemsAccordion">
                    <div class="accordion-body text-dark p-3">
                        <div class="mb-2">
                            <input type="text" id="search-prod-builder" class="form-control form-control-sm border-secondary" placeholder="Search products...">
                        </div>
                        <div style="max-height: 200px; overflow-y: auto;" class="border border-light rounded p-2 mb-3 bg-white" id="products-builder-list">
                            <?php foreach ($products as $prod): ?>
                                <div class="form-check mb-2 product-builder-item" data-name="<?= esc(strtolower($prod['name'])) ?>">
                                    <input class="form-check-input prod-checkbox" type="checkbox" value="<?= $prod['id'] ?>" id="builder_prod_<?= $prod['id'] ?>" data-title="<?= esc($prod['name']) ?>" data-slug="<?= esc($prod['slug']) ?>">
                                    <label class="form-check-label text-dark" for="builder_prod_<?= $prod['id'] ?>">
                                        <?= esc($prod['name']) ?> (₹<?= number_format($prod['price']) ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-cyan btn-sm" id="btn-add-prods"><i class="far fa-plus me-1"></i> Add to Menu</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accordion: Custom Links -->
            <div class="accordion-item mb-2 border border-light" style="background: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2 class="accordion-header" id="headingLinks">
                    <button class="accordion-button collapsed bg-light text-dark fw-bold border-bottom" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLinks" aria-expanded="false" aria-controls="collapseLinks">
                        <i class="far fa-link text-cyan me-2"></i> Custom Links
                    </button>
                </h2>
                <div id="collapseLinks" class="accordion-collapse collapse" aria-labelledby="headingLinks" data-bs-parent="#menuItemsAccordion">
                    <div class="accordion-body text-dark p-3">
                        <div class="mb-3">
                            <label class="form-label small text-dark">URL</label>
                            <input type="text" id="custom-link-url" class="form-control form-control-sm border-secondary" value="https://">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-dark">Link Text</label>
                            <input type="text" id="custom-link-text" class="form-control form-control-sm border-secondary" placeholder="e.g. Special Offer">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-cyan btn-sm" id="btn-add-custom"><i class="far fa-plus me-1"></i> Add to Menu</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Menu Structure -->
    <div class="col-lg-8 col-md-7">
        <div class="card-custom mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <h4 class="m-0 text-dark fw-bold"><i class="far fa-bars text-cyan me-2"></i> Menu Structure</h4>
                
                <form action="<?= base_url('admin/menus') ?>" method="get" class="d-flex align-items-center gap-2">
                    <label class="text-dark small m-0 whitespace-nowrap">Select a menu to edit:</label>
                    <select name="menu_id" class="form-select form-select-sm border-secondary w-auto" onchange="this.form.submit()">
                        <?php foreach ($menus as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= (isset($selectedMenu) && $selectedMenu['id'] == $m['id']) ? 'selected' : '' ?>><?= esc($m['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- Create New Menu Row -->
            <div class="p-3 mb-4 rounded border bg-light">
                <form action="<?= base_url('admin/menus/create') ?>" method="post" class="row g-2 align-items-center">
                    <?= csrf_field() ?>
                    <div class="col-auto">
                        <span class="text-dark small">Or create a new menu:</span>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control form-control-sm border-secondary" placeholder="Menu Name (e.g. Top Navigation)" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-cyan btn-sm">Create Menu</button>
                    </div>
                </form>
            </div>

            <?php if (!isset($selectedMenu)): ?>
                <div class="text-center py-5 text-muted border border-dashed rounded" style="border-style: dashed !important;">
                    <i class="far fa-bars mb-3" style="font-size: 3rem;"></i>
                    <p>No menus exist. Please create a new menu using the option above.</p>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <span class="text-cyan fw-bold">Menu Name:</span> <span class="text-dark fw-bold"><?= esc($selectedMenu['name']) ?></span>
                        </div>
                        <div>
                            <?php if ($selectedMenu['is_active']): ?>
                                <span class="badge bg-success text-white px-3 py-1"><i class="far fa-check-circle me-1"></i> Active Header Menu</span>
                            <?php else: ?>
                                <a href="<?= base_url('admin/menus/activate/' . $selectedMenu['id']) ?>" class="btn btn-cyan btn-sm py-1"><i class="far fa-power-off me-1"></i> Set as Active Menu</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <a href="<?= base_url('admin/menus/delete/' . $selectedMenu['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this entire menu?')"><i class="far fa-trash-alt me-1"></i> Delete Menu</a>
                    </div>
                </div>

                <!-- Custom instructions for columns creation -->
                <div class="p-3 mb-3 rounded border" style="background-color: #f7fafc; border-color: #e2e8f0 !important;">
                    <h6 class="text-dark fw-bold mb-2"><i class="far fa-info-circle text-cyan me-1"></i> How to build a Mega Menu with Dynamic Columns:</h6>
                    <p class="small text-muted mb-2">Create custom column layouts (e.g. 3 or 4 columns) dynamically by nesting your list items:</p>
                    <ul class="small text-muted mb-0 ps-3">
                        <li><strong>Level 1 (Top Level, Depth 0)</strong>: Add a main link and check <strong>"Is Mega Menu?"</strong> in its Settings drawer.</li>
                        <li><strong>Level 2 (Columns, Depth 1)</strong>: Indent items directly under it. Each of these acts as a <strong>Column Header</strong> (e.g. "By Recipient").</li>
                        <li><strong>Level 3 (Links, Depth 2)</strong>: Indent items further under the Column Header. These act as the clickable links inside that column.</li>
                    </ul>
                </div>

                <div class="p-3 mb-4 rounded bg-light border border-light-subtle">
                    <h6 class="text-dark fw-bold mb-2">Editor Instructions</h6>
                    <p class="small text-muted mb-0">Add items from the left side panel. Use <strong>Move Up/Down</strong> to change list order, and <strong>Indent / Outdent</strong> buttons to set parent-child nesting structure. Click <strong>Save Menu Changes</strong> to update.</p>
                </div>

                <!-- Live Menu List -->
                <div id="menu-items-list" class="mb-4" style="min-height: 200px;">
                    <!-- Rendered dynamically by JavaScript -->
                </div>

                <div class="d-flex justify-content-end border-top pt-3">
                    <button type="button" class="btn btn-cyan px-4" id="btn-save-menu-structure"><i class="far fa-save me-1"></i> Save Menu Changes</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Styles for indentation levels -->
<style>
.menu-item-node {
    margin-bottom: 8px;
    transition: margin 0.2s ease;
}
.menu-item-node.depth-0 { margin-left: 0; }
.menu-item-node.depth-1 { margin-left: 35px; }
.menu-item-node.depth-2 { margin-left: 70px; }

.menu-item-card {
    background: #ffffff;
    border: 1px solid #cbd5e0;
    border-radius: 8px;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #2d3748;
}

.menu-item-node.depth-1 .menu-item-card {
    background: #f7fafc;
    border-color: #cbd5e0;
    border-left: 4px solid var(--primary-coral, #e76f51);
}

.menu-item-node.depth-2 .menu-item-card {
    background: #edf2f7;
    border-color: #cbd5e0;
    border-left: 4px solid #319795;
}

.menu-item-title {
    font-weight: 600;
}

.menu-item-type-badge {
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
    background: #e2e8f0;
    color: #4a5568;
}

.menu-item-controls .btn {
    padding: 2px 6px;
    font-size: 0.8rem;
}
.menu-item-drawer {
    background: #fcfcfc;
    border: 1px solid #cbd5e0;
    border-top: 0;
    border-radius: 0 0 8px 8px;
    padding: 15px;
    margin-top: -3px;
    display: none;
}
.whitespace-nowrap {
    white-space: nowrap;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search Categories list on left accordion
    const catSearch = document.getElementById('search-cat-builder');
    if (catSearch) {
        catSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.category-builder-item').forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(query) ? 'block' : 'none';
            });
        });
    }

    // Search Products list on left accordion
    const prodSearch = document.getElementById('search-prod-builder');
    if (prodSearch) {
        prodSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.product-builder-item').forEach(item => {
                const name = item.getAttribute('data-name');
                item.style.display = name.includes(query) ? 'block' : 'none';
            });
        });
    }

    <?php if (isset($selectedMenu)): ?>
    // -------------------------------------------------------------
    // Menu Builder State & Rendering Engine
    // -------------------------------------------------------------
    const menuId = <?= (int)$selectedMenu['id'] ?>;
    
    // Initialize items from PHP
    let menuItems = [
        <?php foreach ($menuItems as $item): ?>
        {
            id: '<?= $item['id'] ?>',
            title: <?= json_encode($item['title']) ?>,
            type: '<?= $item['type'] ?>',
            object_id: '<?= $item['object_id'] ?>',
            url: <?= json_encode($item['url']) ?>,
            is_mega_menu: <?= $item['is_mega_menu'] ? 1 : 0 ?>,
            depth: <?= (int)$item['depth'] ?>
        },
        <?php endforeach; ?>
    ];

    const listContainer = document.getElementById('menu-items-list');

    // Render the flat menu list items based on state
    function renderMenu() {
        listContainer.innerHTML = '';
        if (menuItems.length === 0) {
            listContainer.innerHTML = '<div class="text-center py-5 text-muted border border-secondary border-dashed rounded" style="border-style: dashed !important;"><p class="m-0">No links added to this menu yet. Select items from the left sidebar.</p></div>';
            return;
        }

        menuItems.forEach((item, index) => {
            const node = document.createElement('div');
            node.className = `menu-item-node depth-${item.depth}`;
            node.setAttribute('data-index', index);

            // Determine display label for Type
            let typeLabel = item.type;
            if (item.type === 'custom') typeLabel = 'Custom Link';
            else if (item.type === 'category') typeLabel = 'Category';
            else if (item.type === 'product') typeLabel = 'Product';
            else if (item.type === 'page') typeLabel = 'Page';

            const card = document.createElement('div');
            card.className = 'menu-item-card';
            card.innerHTML = `
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3 handle" style="cursor: move;"><i class="fas fa-grip-vertical"></i></span>
                    <div>
                        <span class="menu-item-title me-2">${escapeHtml(item.title)}</span>
                        <span class="menu-item-type-badge">${typeLabel}</span>
                        ${item.is_mega_menu ? '<span class="badge bg-danger ms-2" style="font-size:0.65rem;">Mega Menu</span>' : ''}
                    </div>
                </div>
                <div class="menu-item-controls d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-light text-cyan btn-sm btn-indent-out" title="Move Left (Outdent)"><i class="far fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-light text-cyan btn-sm btn-indent-in" title="Move Right (Indent)"><i class="far fa-chevron-right"></i></button>
                    <button type="button" class="btn btn-light text-dark btn-sm btn-move-up" title="Move Up"><i class="far fa-chevron-up"></i></button>
                    <button type="button" class="btn btn-light text-dark btn-sm btn-move-down" title="Move Down"><i class="far fa-chevron-down"></i></button>
                    <button type="button" class="btn btn-cyan btn-sm btn-toggle-drawer ms-2" title="Edit Settings"><i class="far fa-cog"></i> Edit</button>
                </div>
            `;

            const drawer = document.createElement('div');
            drawer.className = 'menu-item-drawer text-dark';
            drawer.id = `drawer-${index}`;
            drawer.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Navigation Label</label>
                        <input type="text" class="form-control form-control-sm input-title text-dark border-secondary" value="${escapeHtml(item.title)}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted">Link URL</label>
                        <input type="text" class="form-control form-control-sm input-url text-dark border-secondary" value="${escapeHtml(item.url || '')}" ${item.type !== 'custom' ? 'disabled' : ''}>
                        ${item.type !== 'custom' ? '<small class="text-muted">Managed automatically</small>' : ''}
                    </div>
                    <div class="col-12 d-flex align-items-center justify-content-between pt-2">
                        <div>
                            ${item.depth === 0 ? `
                                <div class="form-check form-switch">
                                    <input class="form-check-input check-mega" type="checkbox" id="mega_${index}" ${item.is_mega_menu ? 'checked' : ''}>
                                    <label class="form-check-label text-dark small" for="mega_${index}">Is Mega Menu?</label>
                                </div>
                            ` : '<span class="text-muted small">Mega Menu toggles are only available for top-level links.</span>'}
                        </div>
                        <button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item"><i class="far fa-trash-alt me-1"></i> Remove Link</button>
                    </div>
                </div>
            `;

            // Bind drawer toggle
            card.querySelector('.btn-toggle-drawer').addEventListener('click', function() {
                const isVisible = drawer.style.display === 'block';
                drawer.style.display = isVisible ? 'none' : 'block';
            });

            // Bind outdent / indent
            card.querySelector('.btn-indent-out').addEventListener('click', () => outdentItem(index));
            card.querySelector('.btn-indent-in').addEventListener('click', () => indentItem(index));

            // Bind move up / down
            card.querySelector('.btn-move-up').addEventListener('click', () => moveUp(index));
            card.querySelector('.btn-move-down').addEventListener('click', () => moveDown(index));

            // Bind inputs inside drawer
            drawer.querySelector('.input-title').addEventListener('change', function() {
                itemItemsUpdate(index, { title: this.value });
            });
            if (item.type === 'custom') {
                drawer.querySelector('.input-url').addEventListener('change', function() {
                    itemItemsUpdate(index, { url: this.value });
                });
            }
            const megaCheck = drawer.querySelector('.check-mega');
            if (megaCheck) {
                megaCheck.addEventListener('change', function() {
                    itemItemsUpdate(index, { is_mega_menu: this.checked ? 1 : 0 });
                });
            }

            // Bind remove
            drawer.querySelector('.btn-remove-item').addEventListener('click', () => removeItem(index));

            node.appendChild(card);
            node.appendChild(drawer);
            listContainer.appendChild(node);
        });
    }

    // Helper functions for state modification
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function itemItemsUpdate(index, updates) {
        menuItems[index] = { ...menuItems[index], ...updates };
        // If changed depth or title, re-render to reflect changes
        renderMenu();
        // Keep the edited drawer open
        const d = document.getElementById(`drawer-${index}`);
        if (d) d.style.display = 'block';
    }

    // Normalizes hierarchy constraints
    function fixDepths() {
        menuItems.forEach((item, index) => {
            if (index === 0) {
                item.depth = 0;
            } else {
                const prev = menuItems[index - 1];
                if (item.depth > prev.depth + 1) {
                    item.depth = prev.depth + 1;
                }
            }
            // If item has depth > 0, it cannot be a Mega Menu direct toggle
            if (item.depth > 0) {
                item.is_mega_menu = 0;
            }
        });
    }

    function removeItem(index) {
        menuItems.splice(index, 1);
        renderMenu();
    }

    function moveUp(index) {
        if (index === 0) return;
        const temp = menuItems[index];
        menuItems[index] = menuItems[index - 1];
        menuItems[index - 1] = temp;
        
        // Validation check: ensure child cannot have depth > previous item + 1
        fixDepths();
        renderMenu();
    }

    function moveDown(index) {
        if (index === menuItems.length - 1) return;
        const temp = menuItems[index];
        menuItems[index] = menuItems[index + 1];
        menuItems[index + 1] = temp;
        
        fixDepths();
        renderMenu();
    }

    function indentItem(index) {
        if (index === 0) return; // Top item cannot be indented
        const prevItem = menuItems[index - 1];
        if (menuItems[index].depth <= prevItem.depth) {
            menuItems[index].depth += 1;
            // Cap at depth level 2 (three levels hierarchy)
            if (menuItems[index].depth > 2) menuItems[index].depth = 2;
            renderMenu();
        }
    }

    function outdentItem(index) {
        if (menuItems[index].depth > 0) {
            menuItems[index].depth -= 1;
            fixDepths();
            renderMenu();
        }
    }

    // -------------------------------------------------------------
    // Add Item Event Handlers
    // -------------------------------------------------------------
    
    // Add System Pages
    document.getElementById('btn-add-pages').addEventListener('click', function() {
        document.querySelectorAll('.page-checkbox:checked').forEach(chk => {
            const title = chk.getAttribute('data-title');
            const url = chk.getAttribute('data-url');
            
            menuItems.push({
                id: 'new_' + Math.random().toString(36).substr(2, 9),
                title: title,
                type: 'page',
                object_id: chk.value,
                url: url,
                is_mega_menu: 0,
                depth: 0
            });
            chk.checked = false;
        });
        renderMenu();
    });

    // Add Product Categories
    document.getElementById('btn-add-cats').addEventListener('click', function() {
        document.querySelectorAll('.cat-checkbox:checked').forEach(chk => {
            const title = chk.getAttribute('data-title');
            const slug = chk.getAttribute('data-slug');
            
            menuItems.push({
                id: 'new_' + Math.random().toString(36).substr(2, 9),
                title: title,
                type: 'category',
                object_id: chk.value,
                url: slug,
                is_mega_menu: 0,
                depth: 0
            });
            chk.checked = false;
        });
        renderMenu();
    });

    // Add Products
    document.getElementById('btn-add-prods').addEventListener('click', function() {
        document.querySelectorAll('.prod-checkbox:checked').forEach(chk => {
            const title = chk.getAttribute('data-title');
            const slug = chk.getAttribute('data-slug');
            
            menuItems.push({
                id: 'new_' + Math.random().toString(36).substr(2, 9),
                title: title,
                type: 'product',
                object_id: chk.value,
                url: 'product/' + slug,
                is_mega_menu: 0,
                depth: 0
            });
            chk.checked = false;
        });
        renderMenu();
    });

    // Add Custom Links
    document.getElementById('btn-add-custom').addEventListener('click', function() {
        const urlInput = document.getElementById('custom-link-url');
        const textInput = document.getElementById('custom-link-text');
        
        const url = urlInput.value.trim();
        const text = textInput.value.trim();
        
        if (text === '' || url === '' || url === 'https://') {
            Swal.fire({
                icon: 'warning',
                title: 'Required Fields',
                text: 'Please specify both custom URL and link label.'
            });
            return;
        }

        menuItems.push({
            id: 'new_' + Math.random().toString(36).substr(2, 9),
            title: text,
            type: 'custom',
            object_id: null,
            url: url,
            is_mega_menu: 0,
            depth: 0
        });

        // Reset inputs
        urlInput.value = 'https://';
        textInput.value = '';
        renderMenu();
    });

    // -------------------------------------------------------------
    // Save Structure Action
    // -------------------------------------------------------------
    document.getElementById('btn-save-menu-structure').addEventListener('click', function() {
        const saveBtn = $(this);
        const originalBtnHtml = saveBtn.html();
        saveBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').prop('disabled', true);
        
        $.ajax({
            url: '<?= base_url('admin/menus/update-structure') ?>',
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                menu_id: menuId,
                structure: menuItems
            },
            success: function(response) {
                saveBtn.html(originalBtnHtml).prop('disabled', false);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved Successfully!',
                        text: 'Your menu structure changes have been saved.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.error || 'Failed to save menu structure.'
                    });
                }
            },
            error: function() {
                saveBtn.html(originalBtnHtml).prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An AJAX error occurred. Please try again.'
                });
            }
        });
    });

    // Initial load
    renderMenu();
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
