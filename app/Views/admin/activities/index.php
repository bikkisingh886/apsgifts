<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-history me-2 text-cyan"></i> Employee Activity Logs</h4>
            <span class="text-muted small">Showing last 200 activity records</span>
        </div>

        <!-- Filter panel -->
        <div class="card-custom mb-4">
            <form action="<?= base_url('admin/activities') ?>" method="get">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Employee</label>
                        <select name="employee_id" class="form-select text-dark">
                            <option value="">-- All Employees --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id'] ?>" <?= $selected_employee == $emp['id'] ? 'selected' : '' ?>><?= esc($emp['name']) ?> (<?= esc($emp['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter by Module</label>
                        <select name="module" class="form-select text-dark">
                            <option value="">-- All Modules --</option>
                            <?php foreach ($modules as $key => $name): ?>
                                <option value="<?= $key ?>" <?= $selected_module == $key ? 'selected' : '' ?>><?= esc($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-cyan btn-sm px-4"><i class="far fa-filter me-1"></i> Apply Filters</button>
                        <a href="<?= base_url('admin/activities') ?>" class="btn btn-outline-secondary btn-sm px-3">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Logs list -->
        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width: 180px;">Timestamp</th>
                            <th>Employee</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>Browser User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No activity records found matching filters.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="text-muted"><?= date('d M Y, h:i A', strtotime($log['created_at'])) ?></td>
                                    <td>
                                        <strong class="text-white"><?= esc($log['employee_name']) ?></strong>
                                        <div class="small text-muted"><?= esc($log['employee_email']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($log['module']) ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $action = strtolower($log['action']);
                                        $badgeClass = 'bg-secondary';
                                        if ($action === 'create') $badgeClass = 'bg-success text-white';
                                        elseif ($action === 'edit') $badgeClass = 'bg-warning text-dark';
                                        elseif ($action === 'delete') $badgeClass = 'bg-danger text-white';
                                        elseif ($action === 'login') $badgeClass = 'bg-info text-dark';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= strtoupper($log['action']) ?></span>
                                    </td>
                                    <td class="text-light"><?= esc($log['details']) ?></td>
                                    <td><code><?= esc($log['ip_address']) ?></code></td>
                                    <td class="small text-muted text-wrap" style="max-width: 250px; font-size: 0.75rem;"><?= esc($log['user_agent']) ?></td>
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
