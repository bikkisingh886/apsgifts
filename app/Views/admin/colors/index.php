<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <!-- Form at the top -->
    <div class="col-lg-12">
        <div class="card-custom mb-4">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-plus-circle me-2 text-cyan"></i> Add Product Color</h4>
            <form action="<?= base_url('admin/colors/create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label font-weight-bold">Color Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Red, Black, Royal Blue" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label font-weight-bold">Color HEX/Code (Optional)</label>
                        <input type="text" name="color_code" class="form-control" placeholder="e.g. #FF0000">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex gap-2 justify-content-end mt-2">
                    <button type="submit" class="btn-cyan px-4 py-2">Save Color</button>
                    <a href="<?= base_url('admin/colors') ?>" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Table below -->
    <div class="col-lg-12">
        <div class="card-custom">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-palette me-2 text-cyan"></i> PRODUCT COLORS</h4>
            <div class="table-responsive">
                <table id="colors-table" class="table table-custom">
                    <thead>
                        <tr>
                            <th>Color Name</th>
                            <th>HEX / Color Preview</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($colors)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No colors found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($colors as $color): ?>
                                <tr>
                                    <td><strong class="text-dark"><?= esc($color['name']) ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($color['color_code'])): ?>
                                                <span class="d-inline-block rounded-circle" style="width: 20px; height: 20px; border: 1px solid #ddd; background-color: <?= esc($color['color_code']) ?>;"></span>
                                                <span class="text-muted font-monospace"><?= esc($color['color_code']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/colors/toggle/' . $color['id']) ?>" class="badge badge-status <?= $color['is_active'] ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                                            <?= $color['is_active'] ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <a href="<?= base_url('admin/colors/edit/' . $color['id']) ?>" class="btn btn-outline-cyan btn-sm me-2 btn-edit-popup"><i class="far fa-edit"></i> Edit</a>
                                        <a href="<?= base_url('admin/colors/delete/' . $color['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this color?')"><i class="far fa-trash-alt"></i> Delete</a>
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

<!-- DataTables CSS & Buttons -->
<style>
.dt-buttons .btn { background:#fff !important; color:#333 !important; border:1px solid #dee2e6 !important; margin-right:4px; border-radius:8px !important; font-weight:600; padding:5px 12px; font-size:.82rem; transition:all .2s; }
.dt-buttons .btn:hover { background:var(--primary-coral,#e76f51) !important; color:#fff !important; border-color:var(--primary-coral,#e76f51) !important; }
.dataTables_paginate .paginate_button.active .page-link { background:var(--primary-coral,#e76f51) !important; border-color:var(--primary-coral,#e76f51) !important; color:#fff !important; }
.dataTables_paginate .page-link { color:#333; border-radius:6px; margin:0 2px; }
</style>
<script>
$(document).ready(function() {
    $('#colors-table').DataTable({
        dom: "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend:'csv',   text:'<i class="far fa-file-csv me-1"></i> CSV',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2]} },
            { extend:'excel', text:'<i class="far fa-file-excel me-1"></i> Excel', className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2]} },
            { extend:'pdf',   text:'<i class="far fa-file-pdf me-1"></i> PDF',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2]} }
        ],
        pageLength: 15,
        lengthMenu: [10,15,25,50,100],
        language: { search:'Search:', lengthMenu:'_MENU_ per page' }
    });
});
</script>
<?= $this->endSection() ?>
