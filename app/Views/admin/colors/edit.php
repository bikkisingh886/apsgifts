<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card-custom">
            <h4 class="mb-4 text-dark fw-bold"><i class="far fa-edit me-2 text-cyan"></i> Edit Product Color</h4>
            
            <form action="<?= base_url('admin/colors/edit/' . $color['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Color Name *</label>
                    <input type="text" name="name" class="form-control" value="<?= esc($color['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Color HEX/Code (Optional)</label>
                    <input type="text" name="color_code" class="form-control" value="<?= esc($color['color_code']) ?>" placeholder="e.g. #FF0000">
                </div>
                
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= $color['is_active'] == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $color['is_active'] == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <button type="submit" class="btn-cyan px-4 py-2">Save Changes</button>
                    <a href="<?= base_url('admin/colors') ?>" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
