<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<!-- Include Select2 CSS in this view -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 42px !important;
    border: 1px solid #ced4da !important;
    border-radius: 6px !important;
    padding: 6px 12px !important;
    background-color: #ffffff !important;
    color: #212529 !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #212529 !important;
    line-height: 28px !important;
    padding-left: 0 !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px !important;
}
.select2-dropdown {
    background-color: #ffffff !important;
    border-color: #ced4da !important;
    color: #212529 !important;
}
.select2-search__field {
    background-color: #ffffff !important;
    color: #212529 !important;
    border-color: #ced4da !important;
}
.select2-results__option {
    color: #212529 !important;
}
.select2-results__option--highlighted[aria-selected] {
    background-color: #e76f51 !important;
    color: #ffffff !important;
}
</style>

<div class="row">
    <!-- Left Column: Add New Review -->
    <div class="col-lg-4 mb-4">
        <div class="card-custom shadow-sm bg-white p-4 border rounded">
            <h4 class="mb-4 text-dark fw-bold border-bottom pb-3"><i class="far fa-plus-circle me-2 text-cyan"></i> Add Custom Review</h4>
            
            <form action="<?= base_url('admin/reviews/create') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Product *</label>
                    <select name="product_id" id="product_search" class="form-control" style="width: 100%;" required>
                        <option value="">Search product...</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="customer_name" class="form-label fw-bold">Customer Name *</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="e.g. Rahul Sharma" required>
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label fw-bold">Rating *</label>
                    <select class="form-select" id="rating" name="rating" required>
                        <option value="5" selected>5 Stars (Excellent)</option>
                        <option value="4">4 Stars (Good)</option>
                        <option value="3">3 Stars (Average)</option>
                        <option value="2">2 Stars (Poor)</option>
                        <option value="1">1 Star (Terrible)</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="review_text" class="form-label fw-bold">Review Description *</label>
                    <textarea class="form-control" id="review_text" name="review_text" rows="4" placeholder="Write review comments..." required></textarea>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-cyan w-100 py-2"><i class="far fa-save me-1"></i> Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Reviews Moderation -->
    <div class="col-lg-8">
        <div class="card-custom shadow-sm bg-white p-4 border rounded mb-4">
            <h4 class="mb-4 text-dark fw-bold border-bottom pb-3"><i class="far fa-comments me-2 text-cyan"></i> Reviews Moderation</h4>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Reviewer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reviews)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No reviews found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reviews as $r): ?>
                                <tr>
                                    <td>
                                        <div class="small fw-bold text-dark text-truncate-2" style="max-width: 150px;">
                                            <?= esc($r['product_name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small"><?= esc($r['reviewer_name'] ?? 'N/A') ?></div>
                                        <span class="text-muted small d-block"><?= esc($r['reviewer_email'] ?? 'Manual admin entry') ?></span>
                                    </td>
                                    <td>
                                        <div class="text-nowrap">
                                            <?php for ($i = 0; $i < $r['rating']; $i++): ?>
                                                <i class="fas fa-star text-warning small"></i>
                                            <?php endfor; ?>
                                            <?php for ($i = 0; $i < (5 - $r['rating']); $i++): ?>
                                                <i class="far fa-star text-warning small"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted text-truncate-2" style="max-width: 200px;" title="<?= esc($r['review_text']) ?>">
                                            <?= esc($r['review_text']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($r['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($r['status'] === 'pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= esc($r['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <?php if ($r['status'] !== 'approved'): ?>
                                                <a href="<?= base_url('admin/reviews/approve/' . $r['id']) ?>" class="btn btn-sm btn-outline-success" title="Approve"><i class="far fa-check"></i></a>
                                            <?php else: ?>
                                                <a href="<?= base_url('admin/reviews/disapprove/' . $r['id']) ?>" class="btn btn-sm btn-outline-warning" title="Unapprove"><i class="far fa-undo"></i></a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('admin/reviews/delete/' . $r['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this review?');" title="Delete"><i class="far fa-trash-alt"></i></a>
                                        </div>
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

<!-- Select2 JavaScript dependency -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#product_search').select2({
        dropdownParent: $('.card-custom'),
        ajax: {
            url: '<?= base_url('admin/reviews/search-products') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        placeholder: 'Search product...',
        minimumInputLength: 1
    });
});
</script>
<?= $this->endSection() ?>
