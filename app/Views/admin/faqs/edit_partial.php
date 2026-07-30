<form action="<?= base_url('admin/faqs/update/' . $faq['id']) ?>" method="post">
    <?= csrf_field() ?>
    <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark"><i class="far fa-edit me-2 text-cyan"></i> Edit FAQ Question</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-8 mb-3">
                <label class="form-label font-weight-bold text-dark">Question <span class="text-danger">*</span></label>
                <input type="text" name="question" class="form-control" value="<?= esc($faq['question']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label font-weight-bold text-dark">Category <span class="text-danger">*</span></label>
                <?php
                $optCategories = [
                    'General',
                    'Delivery & Shipping',
                    'Payments & Pricing',
                    'Orders & Tracking',
                    'Returns & Refunds',
                    'Customization & Gifts',
                    'Delivery',
                    'Payments',
                    'Orders',
                    'Returns'
                ];
                $currentCat = $faq['category'];
                if (!in_array($currentCat, $optCategories)) {
                    $optCategories[] = $currentCat;
                }
                $optCategories = array_unique($optCategories);
                ?>
                <select name="category" class="form-select text-dark" required>
                    <?php foreach ($optCategories as $c): ?>
                        <option value="<?= esc($c) ?>" <?= ($currentCat === $c) ? 'selected' : '' ?>><?= esc($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label font-weight-bold text-dark">Answer Content <span class="text-danger">*</span></label>
            <textarea name="answer" id="edit_faq_answer_editor" class="form-control" rows="4" required><?= esc($faq['answer']) ?></textarea>
        </div>

        <div class="row align-items-center">
            <div class="col-md-6 mb-3">
                <label class="form-label font-weight-bold text-dark">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="<?= (int)$faq['sort_order'] ?>" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1" <?= $faq['is_active'] ? 'checked' : '' ?>>
                    <label class="form-check-label font-weight-bold text-dark ms-2" for="edit_is_active">Publish Active</label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-cyan px-4"><i class="far fa-save me-1"></i> Update Changes</button>
    </div>
</form>
