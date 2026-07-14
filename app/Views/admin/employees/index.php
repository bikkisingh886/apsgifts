<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-users-cog me-2 text-cyan"></i> Employees Management</h4>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/roles') ?>" class="btn btn-outline-cyan btn-sm"><i class="far fa-shield-alt me-1"></i> Manage Roles</a>
                <a href="<?= base_url('admin/employees/create') ?>" class="btn btn-cyan btn-sm"><i class="far fa-plus me-1"></i> Add Employee</a>
            </div>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th style="width: 150px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No employees found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td>
                                        <strong class="text-white"><?= esc($employee['name']) ?></strong>
                                    </td>
                                    <td><?= esc($employee['email']) ?></td>
                                    <td><?= esc($employee['mobile']) ?></td>
                                    <td>
                                        <span class="badge bg-cyan text-dark fw-bold"><?= esc($employee['role_name']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($employee['email'] !== 'admin@giftshop.in'): ?>
                                            <a href="<?= base_url('admin/employees/toggle/' . $employee['id']) ?>" class="badge <?= $employee['is_active'] ? 'bg-success' : 'bg-danger' ?> text-decoration-none">
                                                <?= $employee['is_active'] ? 'Active' : 'Inactive' ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <!--<td class="small text-muted" style="line-height: 1.4;">
                                        <?php /*if (!empty($employee['creator_name'])): ?>
                                            <div>Created by: <span class="text-cyan"><?= esc($employee['creator_name']) ?></span></div>
                                        <?php endif; ?>
                                        <div>Created at: <?= date('d M Y, h:i A', strtotime($employee['created_at'])) ?></div>
                                        <?php if (!empty($employee['updater_name'])): ?>
                                            <div class="mt-1">Updated by: <span class="text-cyan"><?= esc($employee['updater_name']) ?></span></div>
                                        <?php endif;*/ ?>
                                    </td>-->
                                    <td style="text-align: right;">
                                        <?php if ($employee['email'] !== 'admin@giftshop.in'): ?>
                                            <a href="<?= base_url('admin/employees/edit/' . $employee['id']) ?>" class="btn btn-outline-warning btn-sm me-1" title="Edit Employee"><i class="far fa-edit"></i> Edit</a>
                                            <a href="<?= base_url('admin/employees/delete/' . $employee['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee account? This action will also wipe their activity logs.');" title="Delete Employee"><i class="far fa-trash"></i></a>
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
