<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <!-- Form at the top -->
    <div class="col-lg-12">
        <div class="card-custom mb-4">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-plus-circle me-2 text-cyan"></i> Add Delivery City</h4>
            <form action="<?= base_url('admin/cities/create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">City Name *</label>
                        <input type="text" name="name" id="city-name-input" class="form-control" placeholder="e.g. Patna" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">URL Slug</label>
                        <input type="text" name="slug" id="city-slug-input" class="form-control" placeholder="e.g. patna" required>
                        <div id="city-slug-feedback" class="mt-1" style="display:none;"></div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">Is Popular?</label>
                        <select name="is_popular" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Yes (Show at Top)</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex gap-2 justify-content-end mt-2">
                    <button type="submit" class="btn-cyan px-4 py-2">Save City</button>
                    <a href="<?= base_url('admin/cities') ?>" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Table below -->
    <div class="col-lg-12">
        <div class="card-custom">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-city me-2 text-cyan"></i> ACTIVE DELIVERY CITIES</h4>
            <div class="table-responsive">
                <table id="cities-table" class="table table-custom">
                    <thead>
                        <tr>
                            <th>City Name</th>
                            <th>Slug</th>
                            <th>Popular Status</th>
                            <th>Active Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cities)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No cities found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cities as $city): ?>
                                <tr>
                                    <td><strong class="text-dark"><i class="far fa-map-marker-alt text-danger me-2"></i><?= esc($city['name']) ?></strong></td>
                                    <td><span class="text-cyan">/gifts/<?= esc($city['slug']) ?></span></td>
                                    <td>
                                        <span class="badge <?= $city['is_popular'] ? 'bg-warning text-dark' : 'bg-light text-dark' ?>">
                                            <?= $city['is_popular'] ? 'Popular' : 'Regular' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/cities/toggle/' . $city['id']) ?>" class="badge badge-status <?= $city['is_active'] ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                                            <?= $city['is_active'] ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <a href="<?= base_url('admin/cities/edit/' . $city['id']) ?>" class="btn btn-outline-cyan btn-sm me-2 btn-edit-popup"><i class="far fa-edit"></i> Edit</a>
                                        <a href="<?= base_url('admin/cities/delete/' . $city['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this city?')"><i class="far fa-trash-alt"></i> Delete</a>
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
    // 1. Auto-Slug Generator + Real-time Duplicate Check
    const nameInput = document.getElementById('city-name-input');
    const slugInput = document.getElementById('city-slug-input');
    const slugFeedback = document.getElementById('city-slug-feedback');
    let autoSlug = true;
    let slugCheckTimer = null;

    function checkCitySlug(slug) {
        if (!slug || slug.length < 2) { slugFeedback.style.display = 'none'; return; }
        clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(function() {
            var url = '<?= base_url('admin/cities/check-slug') ?>?slug=' + encodeURIComponent(slug);
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
                checkCitySlug(slug);
            }
        });

        slugInput.addEventListener('input', function() {
            autoSlug = (this.value === "");
            checkCitySlug(this.value);
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
</style>
<script>
$(document).ready(function() {
    $('#cities-table').DataTable({
        dom: "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend:'csv',   text:'<i class="far fa-file-csv me-1"></i> CSV',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3]} },
            { extend:'excel', text:'<i class="far fa-file-excel me-1"></i> Excel', className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3]} },
            { extend:'pdf',   text:'<i class="far fa-file-pdf me-1"></i> PDF',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3]} }
        ],
        pageLength: 15,
        lengthMenu: [10,15,25,50,100],
        language: { search:'Search:', lengthMenu:'_MENU_ per page' }
    });
});
</script>
<?= $this->endSection() ?>
