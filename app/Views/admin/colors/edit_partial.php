<form action="<?= base_url('admin/colors/edit/' . $color['id']) ?>" method="post">
    <?= csrf_field() ?>
    
    <div class="row text-dark">
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Color Name *</label>
            <input type="text" name="name" class="form-control" value="<?= esc($color['name']) ?>" placeholder="e.g. Red" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Color HEX/Code (Optional)</label>
            <input type="text" name="color_code" class="form-control" value="<?= esc($color['color_code']) ?>" placeholder="e.g. #FF0000">
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label font-weight-bold">Status</label>
            <select name="is_active" class="form-select">
                <option value="1" <?= $color['is_active'] == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= $color['is_active'] == 0 ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-3 border-top pt-3">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
