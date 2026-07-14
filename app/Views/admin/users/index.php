<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card-custom">
            <h4 class="mb-4 text-white"><i class="far fa-users me-2 text-cyan"></i> Registered Customers & Admins</h4>
            <div class="table-responsive">
                <table id="users-table" class="table table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $usr): ?>
                                <?php $role = ($usr['email'] === 'admin@giftshop.in') ? 'admin' : 'customer'; ?>
                                <tr>
                                    <td><?= $usr['id'] ?></td>
                                    <td><strong class="text-white"><?= esc($usr['name']) ?></strong></td>
                                    <td><?= esc($usr['email']) ?></td>
                                    <td><?= esc($usr['mobile']) ?></td>
                                    <td>
                                        <span class="badge <?= $role === 'admin' ? 'bg-danger text-white' : 'bg-secondary text-white' ?>">
                                            <?= strtoupper($role) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($usr['id'] == session()->get('user_id')): ?>
                                            <span class="badge bg-success text-white">Active (You)</span>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/users/toggle/' . $usr['id']) ?>" class="badge badge-status <?= $usr['is_active'] ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                                                <?= $usr['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($usr['id'] != session()->get('user_id')): ?>
                                            <a href="<?= base_url('admin/users/delete/' . $usr['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')"><i class="far fa-trash-alt"></i> Delete</a>
                                        <?php else: ?>
                                            <span class="text-muted small">No Actions</span>
                                        <?php endif; ?>
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
    $('#users-table').DataTable({
        dom: "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend:'csv',   text:'<i class="far fa-file-csv me-1"></i> CSV',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4,5]} },
            { extend:'excel', text:'<i class="far fa-file-excel me-1"></i> Excel', className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4,5]} },
            { extend:'pdf',   text:'<i class="far fa-file-pdf me-1"></i> PDF',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4,5]} }
        ],
        pageLength: 15,
        lengthMenu: [10,15,25,50,100],
        language: { search:'Search:', lengthMenu:'_MENU_ per page' }
    });
});
</script>
<?= $this->endSection() ?>
