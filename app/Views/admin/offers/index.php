<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <!-- Offers manager container -->
    <div class="col-lg-12">
        <!-- Create Offer Card -->
        <div class="card-custom mb-4">
            <h4 class="mb-4 text-white"><i class="far fa-plus-circle me-2 text-cyan"></i> Create New Offer</h4>
            <form action="<?= base_url('admin/offers/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Offer Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Summer Sale 10%" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Discount Type</label>
                        <select name="type" class="form-select" required>
                            <option value="percent">% Percent</option>
                            <option value="flat">₹ Flat</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Value</label>
                        <input type="number" name="value" step="0.01" class="form-control" placeholder="10" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Applies To</label>
                        <select name="applies_to" class="form-select" required>
                            <option value="product">Product</option>
                            <option value="category">Category</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn-cyan w-100"><i class="far fa-save me-1"></i> Save Offer</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Offers list -->
        <div class="card-custom">
            <h4 class="mb-4 text-white"><i class="far fa-percentage me-2 text-cyan"></i> Manage Offers</h4>
            <div class="table-responsive">
                <table id="offers-table" class="table table-custom">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Applies To</th>
                            <th>Audit Info</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($offers)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No offers found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($offers as $off): ?>
                                <tr>
                                    <td><strong class="text-white"><?= esc($off['name']) ?></strong></td>
                                    <td><?= $off['type'] === 'percent' ? '% Percent' : '₹ Flat' ?></td>
                                    <td>
                                        <strong class="text-cyan">
                                            <?= $off['type'] === 'percent' ? (int)$off['value'] . '%' : '₹' . number_format($off['value'], 2) ?>
                                        </strong>
                                    </td>
                                    <td><span class="text-capitalize"><?= esc($off['applies_to']) ?></span></td>
                                    <td class="small text-muted" style="line-height: 1.4;">
                                        <?php if (!empty($off['creator_name'])): ?>
                                            <div>Created by: <span class="text-cyan"><?= esc($off['creator_name']) ?></span></div>
                                        <?php endif; ?>
                                        <?php if ($off['created_at']): ?>
                                            <div>Created at: <?= date('d M Y', strtotime($off['created_at'])) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($off['updater_name'])): ?>
                                            <div class="mt-1">Updated by: <span class="text-cyan"><?= esc($off['updater_name']) ?></span></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/offers/toggle/' . $off['id']) ?>" class="badge badge-status <?= $off['is_active'] ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                                            <?= $off['is_active'] ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/offers/delete/' . $off['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this offer?')"><i class="far fa-trash-alt"></i> Delete</a>
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
    $('#offers-table').DataTable({
        dom: "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend:'csv',   text:'<i class="far fa-file-csv me-1"></i> CSV',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4]} },
            { extend:'excel', text:'<i class="far fa-file-excel me-1"></i> Excel', className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4]} },
            { extend:'pdf',   text:'<i class="far fa-file-pdf me-1"></i> PDF',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4]} }
        ],
        pageLength: 15,
        lengthMenu: [10,15,25,50,100],
        language: { search:'Search:', lengthMenu:'_MENU_ per page' }
    });
});
</script>
<?= $this->endSection() ?>

