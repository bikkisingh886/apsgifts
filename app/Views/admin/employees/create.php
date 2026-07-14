<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-user-plus me-2 text-cyan"></i> Add New Employee</h4>
            <a href="<?= base_url('admin/employees') ?>" class="btn btn-outline-secondary btn-sm"><i class="far fa-arrow-left me-1"></i> Back to Employees</a>
        </div>

        <div class="card-custom">
            <form action="<?= base_url('admin/employees/create') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control text-dark" placeholder="e.g. Rahul Kumar" value="<?= old('name') ?>" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control text-dark" placeholder="e.g. rahul@giftshop.in" value="<?= old('email') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile Number *</label>
                        <input type="text" name="mobile" class="form-control text-dark" placeholder="e.g. 9876543210" value="<?= old('mobile') ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-control text-dark" placeholder="Password (min 6 characters)" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Role *</label>
                        <select name="role_id" class="form-select text-dark" required>
                            <option value="">-- Choose Role --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>><?= esc($role['name']) ?> (<?= esc($role['description'] ?: 'No desc') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Account Status</label>
                    <select name="is_active" class="form-select text-dark">
                        <option value="1" <?= old('is_active', '1') == '1' ? 'selected' : '' ?>>Active (Can Login)</option>
                        <option value="0" <?= old('is_active') == '0' ? 'selected' : '' ?>>Inactive (Blocked)</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= base_url('admin/employees') ?>" class="btn btn-outline-secondary"><i class="far fa-times me-1"></i> Cancel</a>
                    <button type="submit" class="btn-cyan px-4"><i class="far fa-save me-1"></i> Save Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
