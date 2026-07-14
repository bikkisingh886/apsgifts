<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-plus-circle me-2 text-cyan"></i> Create New Role</h4>
            <a href="<?= base_url('admin/roles') ?>" class="btn btn-outline-secondary btn-sm"><i class="far fa-arrow-left me-1"></i> Back to Roles</a>
        </div>

        <form action="<?= base_url('admin/roles/create') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Basic details -->
            <div class="card-custom mb-4">
                <h5 class="text-dark mb-4" style="font-weight: 600;">Role Details</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-dark fw-bold">Role Name *</label>
                        <input type="text" name="name" class="form-control text-dark" placeholder="e.g. Content Writer, Support Staff" value="<?= old('name') ?>" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label text-dark fw-bold">Description</label>
                        <input type="text" name="description" class="form-control text-dark" placeholder="Specify role responsibilities..." value="<?= old('description') ?>">
                    </div>
                </div>
            </div>

            <!-- Permissions grid -->
            <div class="card-custom mb-4">
                <h5 class="text-dark mb-4" style="font-weight: 600;">Assign Permissions</h5>
                
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Module / Feature</th>
                                <th class="text-center" style="width: 120px;">View</th>
                                <th class="text-center" style="width: 120px;">Create</th>
                                <th class="text-center" style="width: 120px;">Edit</th>
                                <th class="text-center" style="width: 120px;">Delete</th>
                                <th class="text-center" style="width: 120px;">Select All</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $key => $name): ?>
                                <tr class="module-row" data-module="<?= $key ?>">
                                    <td>
                                        <strong class="text-white"><?= esc($name) ?></strong>
                                        <small class="d-block text-muted">Manage <?= strtolower($name) ?> permissions</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input perm-checkbox view-checkbox" type="checkbox" name="perms[<?= $key ?>][view]" value="1">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input perm-checkbox create-checkbox" type="checkbox" name="perms[<?= $key ?>][create]" value="1">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input perm-checkbox edit-checkbox" type="checkbox" name="perms[<?= $key ?>][edit]" value="1">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input perm-checkbox delete-checkbox" type="checkbox" name="perms[<?= $key ?>][delete]" value="1">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-cyan btn-xs toggle-all-row-btn" style="font-size: 0.75rem; padding: 2px 8px;">All</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/roles') ?>" class="btn btn-outline-secondary"><i class="far fa-times me-1"></i> Cancel</a>
                    <button type="submit" class="btn-cyan px-4"><i class="far fa-save me-1"></i> Create Role</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all for row helper
    const toggleBtns = document.querySelectorAll('.toggle-all-row-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('.module-row');
            const checkboxes = row.querySelectorAll('.perm-checkbox');
            
            // Check if all are checked currently
            let allChecked = true;
            checkboxes.forEach(cb => {
                if (!cb.checked) allChecked = false;
            });
            
            // Toggle
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
