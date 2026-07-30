<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-question-circle me-2 text-cyan"></i> FAQ Manager</h4>
            <button type="button" class="btn btn-cyan btn-sm" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                <i class="far fa-plus me-1"></i> Add New Question
            </button>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Order</th>
                            <th>Category</th>
                            <th>Question</th>
                            <th>Answer Snippet</th>
                            <th>Status</th>
                            <th style="width: 150px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($faqs)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No FAQs found. Click "Add New Question" above.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($faqs as $faq): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= (int)$faq['sort_order'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary fw-medium px-2 py-1"><?= esc($faq['category']) ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block"><?= esc($faq['question']) ?></strong>
                                    </td>
                                    <td>
                                        <div class="text-muted small" style="max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= esc(strip_tags($faq['answer'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($faq['is_active']): ?>
                                            <a href="<?= base_url('admin/faqs/toggle/' . $faq['id']) ?>" class="badge bg-success text-decoration-none" title="Click to disable"><i class="far fa-check me-1"></i> Active</a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/faqs/toggle/' . $faq['id']) ?>" class="badge bg-danger text-decoration-none" title="Click to enable"><i class="far fa-times me-1"></i> Inactive</a>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn btn-outline-cyan btn-sm edit-faq-btn" data-url="<?= base_url('admin/faqs/edit_partial/' . $faq['id']) ?>" title="Edit Question">
                                            <i class="far fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('admin/faqs/delete/' . $faq['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this FAQ?');" title="Delete">
                                            <i class="far fa-trash-alt"></i>
                                        </a>
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

<!-- Add FAQ Modal (No tabindex="-1" to allow CKEditor dialog input focus) -->
<div class="modal fade" id="addFaqModal" aria-labelledby="addFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('admin/faqs/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-dark" id="addFaqModalLabel"><i class="far fa-plus-circle me-2 text-cyan"></i> Add New FAQ Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label font-weight-bold text-dark">Question <span class="text-danger">*</span></label>
                            <input type="text" name="question" class="form-control" placeholder="e.g. Do you support same day delivery?" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-dark">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select text-dark" required>
                                <option value="General" selected>General</option>
                                <option value="Delivery & Shipping">Delivery & Shipping</option>
                                <option value="Payments & Pricing">Payments & Pricing</option>
                                <option value="Orders & Tracking">Orders & Tracking</option>
                                <option value="Returns & Refunds">Returns & Refunds</option>
                                <option value="Customization & Gifts">Customization & Gifts</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold text-dark">Answer Content <span class="text-danger">*</span></label>
                        <textarea name="answer" id="add_faq_answer_editor" class="form-control" rows="4" placeholder="Detailed answer explanation..." required></textarea>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" value="1" checked>
                                <label class="form-check-label font-weight-bold text-dark ms-2" for="add_is_active">Publish Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-cyan px-4"><i class="far fa-save me-1"></i> Save FAQ Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Ensure Bootstrap focus trapping is disabled on Add Modal
    var addModalEl = document.getElementById('addFaqModal');
    if (addModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(addModalEl, { focus: false });
    }

    // Initialize CKEditor on Add Modal Answer textarea
    var addEditor = document.getElementById('add_faq_answer_editor');
    if (addEditor) {
        initAppCKEditor(addEditor);
    }

    // Modal Edit Handler
    $(document).on('click', '.edit-faq-btn', function() {
        var editUrl = $(this).data('url');
        $('#editModalBody').html('<div class="text-center p-4"><div class="spinner-border text-cyan" role="status"></div><p class="mt-2 text-muted">Loading FAQ form...</p></div>');
        
        var editModalEl = document.getElementById('editModal');
        if (editModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(editModalEl, { focus: false }).show();
        } else {
            $('#editModal').modal({ focus: false });
        }

        $.get(editUrl, function(html) {
            $('#editModalBody').html(html);
            var editEditor = document.getElementById('edit_faq_answer_editor');
            if (editEditor) {
                initAppCKEditor(editEditor);
            }
        }).fail(function() {
            $('#editModalBody').html('<div class="alert alert-danger mb-0">Failed to load edit form. Please try again.</div>');
        });
    });
});
</script>
<?= $this->endSection() ?>
