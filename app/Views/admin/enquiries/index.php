<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="text-dark fw-bold mb-1"><i class="far fa-envelope-open-text me-2 text-cyan"></i> Customer Enquiries</h4>
                <p class="text-muted small mb-0">View and respond to customer messages submitted via the Contact Us page.</p>
            </div>
            <?php if ($unreadCount > 0): ?>
                <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill"><i class="far fa-bell me-1"></i> <?= $unreadCount ?> Unread Enquiries</span>
            <?php endif; ?>
        </div>

        <!-- Filter & Search Bar -->
        <div class="card-custom mb-4 p-3">
            <form method="get" action="<?= base_url('admin/enquiries') ?>" class="row g-2 align-items-center">
                <div class="col-md-7 col-12">
                    <div class="btn-group w-100" role="group">
                        <a href="<?= base_url('admin/enquiries') ?>" class="btn btn-sm <?= $selectedStatus === 'all' ? 'btn-cyan' : 'btn-outline-secondary' ?>">All (<?= $totalCount ?>)</a>
                        <a href="<?= base_url('admin/enquiries?status=unread') ?>" class="btn btn-sm <?= $selectedStatus === 'unread' ? 'btn-cyan' : 'btn-outline-secondary' ?>">Unread (<?= $unreadCount ?>)</a>
                        <a href="<?= base_url('admin/enquiries?status=read') ?>" class="btn btn-sm <?= $selectedStatus === 'read' ? 'btn-cyan' : 'btn-outline-secondary' ?>">Read</a>
                        <a href="<?= base_url('admin/enquiries?status=replied') ?>" class="btn btn-sm <?= $selectedStatus === 'replied' ? 'btn-cyan' : 'btn-outline-secondary' ?>">Replied</a>
                    </div>
                </div>
                <div class="col-md-5 col-12">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, phone, subject..." value="<?= esc($search) ?>">
                        <button class="btn btn-cyan btn-sm" type="submit"><i class="far fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Enquiries List Table -->
        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sender Name</th>
                            <th>Email & Phone</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th style="width: 130px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($enquiries)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No enquiries found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($enquiries as $enq): ?>
                                <tr class="<?= $enq['status'] === 'unread' ? 'fw-bold bg-light-subtle' : '' ?>">
                                    <td>
                                        <div class="small text-dark"><?= date('d M Y', strtotime($enq['created_at'])) ?></div>
                                        <div class="small text-muted" style="font-size: 0.78rem;"><?= date('h:i A', strtotime($enq['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold"><?= esc($enq['name']) ?></span>
                                    </td>
                                    <td>
                                        <div><a href="mailto:<?= esc($enq['email']) ?>" class="text-cyan text-decoration-none small"><i class="far fa-envelope me-1"></i><?= esc($enq['email']) ?></a></div>
                                        <?php if (!empty($enq['phone'])): ?>
                                            <div><a href="tel:<?= esc($enq['phone']) ?>" class="text-secondary text-decoration-none small"><i class="far fa-phone me-1"></i><?= esc($enq['phone']) ?></a></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-dark" style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= esc($enq['subject']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($enq['status'] === 'unread'): ?>
                                            <span class="badge bg-danger"><i class="far fa-circle me-1"></i> Unread</span>
                                        <?php elseif ($enq['status'] === 'read'): ?>
                                            <span class="badge bg-info text-dark"><i class="far fa-eye me-1"></i> Read</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><i class="far fa-check-double me-1"></i> Replied</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn btn-outline-cyan btn-sm view-enquiry-btn" data-url="<?= base_url('admin/enquiries/view_partial/' . $enq['id']) ?>" title="View Message">
                                            <i class="far fa-eye"></i>
                                        </button>
                                        <a href="<?= base_url('admin/enquiries/delete/' . $enq['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this enquiry?');" title="Delete">
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    $(document).on('click', '.view-enquiry-btn', function() {
        var viewUrl = $(this).data('url');
        $('#editModalBody').html('<div class="text-center p-4"><div class="spinner-border text-cyan" role="status"></div><p class="mt-2 text-muted">Loading message details...</p></div>');
        $('#editModal').modal('show');

        $.get(viewUrl, function(html) {
            $('#editModalBody').html(html);
        }).fail(function() {
            $('#editModalBody').html('<div class="alert alert-danger mb-0">Failed to load message. Please try again.</div>');
        });
    });
});
</script>
<?= $this->endSection() ?>
