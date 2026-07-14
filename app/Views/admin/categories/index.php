<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <!-- Form at the top -->
    <div class="col-lg-12">
        <div class="card-custom mb-4">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-plus-circle me-2 text-cyan"></i> Add Category</h4>
            <form action="<?= base_url('admin/categories/create') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-7">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Category Name *</label>
                            <input type="text" name="name" id="cat-name-input" class="form-control" placeholder="e.g. Birthday Gifts" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">URL Slug (auto-generated + editable)</label>
                            <input type="text" name="slug" id="cat-slug-input" class="form-control" placeholder="e.g. birthday-gifts" required>
                            <div id="cat-slug-feedback" class="mt-1" style="display:none;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Parent Category</label>
                            <select name="parent_id" class="form-select">
                                <option value="0">-- None (Root Category) --</option>
                                <?php if (!empty($categories_list)): ?>
                                    <?php foreach ($categories_list as $cl): ?>
                                        <option value="<?= $cl['id'] ?>"><?= esc($cl['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Top Summary (shown below category title on page)</label>
                            <textarea name="summary" class="form-control" rows="3" placeholder="Send Birthday Gifts Online with Same Day Delivery across India..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">SEO Footer Content (long-form, shown at bottom)</label>
                            <textarea name="footer_content" id="cat-footer-editor" class="form-control" rows="5" placeholder="Detailed footer text..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-lg-5">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Meta Title (for Google)</label>
                            <input type="text" name="meta_title" class="form-control" placeholder="SEO Title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Meta Description</label>
                            <textarea name="meta_desc" class="form-control" rows="3" placeholder="SEO Description"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Image Alt Tag (for SEO)</label>
                            <input type="text" name="image_alt" class="form-control" placeholder="e.g. Birthday Gifts Category Banner">
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Category Banner Image</label>
                            <div class="category-banner-upload-wrapper border border-dashed rounded p-3 text-center bg-light" style="cursor: pointer; position: relative; border-style: dashed !important; border-width: 2px !important; border-color: #ced4da !important;">
                                <input type="file" name="banner_image" id="cat-image-input" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                                <img id="cat-image-preview" src="#" alt="banner" style="display: none; max-height: 80px; object-fit: cover;" class="mb-2 rounded mx-auto">
                                <div id="cat-image-placeholder">
                                    <i class="far fa-image mb-2" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                                    <p class="small text-muted mb-0">Click to change image</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label font-weight-bold d-block">Status</label>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="is_active" id="status-active" value="1" checked autocomplete="off">
                                <label class="btn btn-outline-success w-50 py-2 d-flex align-items-center justify-content-center" for="status-active">
                                    <i class="far fa-check-square me-2"></i> Active
                                </label>

                                <input type="radio" class="btn-check" name="is_active" id="status-inactive" value="0" autocomplete="off">
                                <label class="btn btn-outline-secondary w-50 py-2 d-flex align-items-center justify-content-center" for="status-inactive">
                                    Inactive
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-cyan w-50 py-2">Save Category</button>
                            <a href="<?= base_url('admin/categories') ?>" class="btn btn-outline-secondary w-50 py-2 d-flex align-items-center justify-content-center">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Table below -->
    <div class="col-lg-12">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="m-0 text-dark fw-bold"><i class="far fa-tags me-2 text-cyan"></i> ALL CATEGORIES</h4>
                <div class="d-flex gap-2 align-items-center">
                    <!-- Bulk Delete Bar -->
                    <div id="bulk-action-bar-cat" class="d-none align-items-center gap-2">
                        <span id="selected-count-cat" class="text-dark fw-bold small"></span>
                        <button id="bulk-delete-btn-cat" class="btn btn-danger btn-sm px-3">
                            <i class="far fa-trash-alt me-1"></i> Delete Selected
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="categories-table" class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" id="select-all-cats" class="form-check-input" title="Select All">
                            </th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No categories found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input cat-checkbox" value="<?= $cat['id'] ?>">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($cat['image_path'])): ?>
                                                <img src="<?= base_url($cat['image_path']) ?>" alt="<?= esc($cat['image_alt'] ?? '') ?>" style="width: 40px; height: 40px; object-fit: cover;" class="rounded border me-2">
                                            <?php else: ?>
                                                <div class="rounded border me-2 bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="far fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong class="text-dark"><?= esc($cat['name']) ?></strong>
                                                <?php if (!empty($cat['parent_name'])): ?>
                                                    <br><small class="text-muted"><i class="far fa-level-up fa-rotate-90 me-1"></i> Parent: <?= esc($cat['parent_name']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-cyan">/<?= esc($cat['slug']) ?></span></td>
                                    <td><span class="badge bg-secondary"><?= $cat['product_count'] ?></span></td>
                                    <td>
                                        <a href="<?= base_url('admin/categories/toggle/' . $cat['id']) ?>" class="badge badge-status <?= $cat['is_active'] ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                                            <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <a href="<?= base_url('admin/categories/edit/' . $cat['id']) ?>" class="btn btn-outline-cyan btn-sm me-2 btn-edit-popup"><i class="far fa-edit"></i> Edit</a>
                                        <a href="<?= base_url('admin/categories/delete/' . $cat['id']) ?>" class="btn btn-outline-danger btn-sm btn-cat-single-delete" data-name="<?= esc($cat['name']) ?>"><i class="far fa-trash-alt"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Initialize CKEditor 5 on category footer
    ClassicEditor
        .create(document.querySelector('#cat-footer-editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });

    // 2. Auto-Slug Generation + Real-time Duplicate Check
    const nameInput = document.getElementById('cat-name-input');
    const slugInput = document.getElementById('cat-slug-input');
    const slugFeedback = document.getElementById('cat-slug-feedback');
    let autoSlug = true;
    let slugCheckTimer = null;

    function checkCatSlug(slug, editId) {
        if (!slug || slug.length < 2) { slugFeedback.style.display = 'none'; return; }
        clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(function() {
            var url = '<?= base_url('admin/categories/check-slug') ?>?slug=' + encodeURIComponent(slug) + (editId ? '&id=' + editId : '');
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
                checkCatSlug(slug, null);
            }
        });

        slugInput.addEventListener('input', function() {
            autoSlug = (this.value === "");
            checkCatSlug(this.value, null);
        });
    }

    // 3. Image File Upload Preview Logic
    const imgInput = document.getElementById('cat-image-input');
    const imgPreview = document.getElementById('cat-image-preview');
    const imgPlaceholder = document.getElementById('cat-image-placeholder');

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
});
</script>
<!-- DataTables CSS & Buttons -->
<style>
.dt-buttons .btn { background:#fff !important; color:#333 !important; border:1px solid #dee2e6 !important; margin-right:4px; border-radius:8px !important; font-weight:600; padding:5px 12px; font-size:.82rem; transition:all .2s; }
.dt-buttons .btn:hover { background:var(--primary-coral,#e76f51) !important; color:#fff !important; border-color:var(--primary-coral,#e76f51) !important; }
.dataTables_paginate .paginate_button.active .page-link { background:var(--primary-coral,#e76f51) !important; border-color:var(--primary-coral,#e76f51) !important; color:#fff !important; }
.dataTables_paginate .page-link { color:#333; border-radius:6px; margin:0 2px; }
/* Checkbox column not sortable visually */
#categories-table thead th:first-child::after,
#categories-table thead th:first-child::before { display: none !important; }
</style>
<script>
$(document).ready(function() {
    // Suppress DataTables browser alert on empty/error
    $.fn.dataTable.ext.errMode = 'none';

    var catTable = $('#categories-table').DataTable({
        dom: "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend:'csv',   text:'<i class="far fa-file-csv me-1"></i> CSV',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[1,2,3,4]} },
            { extend:'excel', text:'<i class="far fa-file-excel me-1"></i> Excel', className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[1,2,3,4]} },
            { extend:'pdf',   text:'<i class="far fa-file-pdf me-1"></i> PDF',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[1,2,3,4]} }
        ],
        pageLength: 15,
        lengthMenu: [10,15,25,50,100],
        language: { search:'Search:', lengthMenu:'_MENU_ per page' },
        columnDefs: [
            { orderable: false, targets: [0, 5] } // Checkbox & Actions not sortable
        ]
    });

    // =========================
    // Select All / Deselect
    // =========================
    $('#select-all-cats').on('change', function() {
        // Only rows visible on current page
        catTable.rows({ page: 'current' }).nodes().to$().find('.cat-checkbox').prop('checked', this.checked);
        updateCatBulkBar();
    });

    $(document).on('change', '.cat-checkbox', function() {
        updateCatBulkBar();
        var total   = catTable.rows({ page: 'current' }).nodes().to$().find('.cat-checkbox').length;
        var checked = catTable.rows({ page: 'current' }).nodes().to$().find('.cat-checkbox:checked').length;
        $('#select-all-cats').prop('checked', total === checked && total > 0);
    });

    function updateCatBulkBar() {
        var count = catTable.rows({ page: 'current' }).nodes().to$().find('.cat-checkbox:checked').length;
        if (count > 0) {
            $('#bulk-action-bar-cat').removeClass('d-none').addClass('d-flex');
            $('#selected-count-cat').text(count + ' selected');
        } else {
            $('#bulk-action-bar-cat').removeClass('d-flex').addClass('d-none');
            $('#select-all-cats').prop('checked', false);
        }
    }

    // =========================
    // Bulk Delete
    // =========================
    $('#bulk-delete-btn-cat').on('click', function() {
        var ids = [];
        catTable.rows().nodes().to$().find('.cat-checkbox:checked').each(function() { ids.push($(this).val()); });
        if (!ids.length) return;

        Swal.fire({
            title: 'Delete ' + ids.length + ' Categor' + (ids.length > 1 ? 'ies' : 'y') + '?',
            html: 'This will permanently delete <strong>' + ids.length + '</strong> selected categor' + (ids.length > 1 ? 'ies' : 'y') + '. Products will <strong>not</strong> be deleted but will lose their category association.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Yes, Delete All',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: '<?= base_url('admin/categories/bulk-delete') ?>',
                    type: 'POST',
                    data: { ids: ids, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 2000, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Server error. Please try again.' });
                    }
                });
            }
        });
    });

    // =========================
    // Single Delete via SweetAlert
    // =========================
    $(document).on('click', '.btn-cat-single-delete', function(e) {
        e.preventDefault();
        var url  = $(this).attr('href');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Delete "' + name + '"?',
            text: 'This category will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
