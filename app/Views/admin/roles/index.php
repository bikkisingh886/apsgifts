<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-shield-alt me-2 text-cyan"></i> Roles & Permissions</h4>
            <a href="<?= base_url('admin/roles/create') ?>" class="btn btn-cyan btn-sm"><i class="far fa-plus me-1"></i> Add New Role</a>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Employees Assigned</th>
                            <th>Date Created</th>
                            <th style="width: 150px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($roles)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No roles found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($roles as $index => $role): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong class="text-white"><?= esc($role['name']) ?></strong>
                                        <?php if (in_array($role['name'], ['Admin', 'Manager'])): ?>
                                            <span class="badge bg-secondary ms-1" style="font-size: 0.7rem;">System Default</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= esc($role['description'] ?: 'No description provided') ?></td>
                                    <td>
                                        <span class="badge bg-cyan text-dark fw-bold"><?= $role['employee_count'] ?> Employee(s)</span>
                                    </td>
                                    <td><?= date('d M Y, h:i A', strtotime($role['created_at'])) ?></td>
                                    <td style="text-align: right;">
                                        <?php if ($role['name'] !== 'Admin'): ?>
                                            <a href="<?= base_url('admin/roles/edit/' . $role['id']) ?>" class="btn btn-outline-warning btn-sm me-1" title="Edit Permissions"><i class="far fa-edit"></i> Edit</a>
                                            <?php if ($role['name'] !== 'Manager'): ?>
                                                <a href="<?= base_url('admin/roles/delete/' . $role['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this role? All associated permissions will be permanently removed.');" title="Delete Role"><i class="far fa-trash"></i></a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">System Locked</span>
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
<?= $this->endSection() ?>
