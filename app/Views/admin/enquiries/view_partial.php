<div class="modal-header bg-light">
    <h5 class="modal-title font-weight-bold text-dark"><i class="far fa-envelope-open me-2 text-cyan"></i> Customer Enquiry Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="small text-muted text-uppercase font-weight-bold d-block">Sender Name</label>
            <h5 class="text-dark font-weight-bold mb-0"><?= esc($enquiry['name']) ?></h5>
        </div>
        <div class="col-md-6 text-md-end">
            <label class="small text-muted text-uppercase font-weight-bold d-block">Submitted On</label>
            <span class="text-dark fw-medium"><?= date('d M Y, h:i A', strtotime($enquiry['created_at'])) ?></span>
        </div>
    </div>

    <div class="row mb-3 p-3 bg-light rounded border">
        <div class="col-md-6 mb-2 mb-md-0">
            <small class="text-muted d-block">Email Address:</small>
            <a href="mailto:<?= esc($enquiry['email']) ?>" class="fw-bold text-cyan text-decoration-none"><i class="far fa-envelope me-1"></i> <?= esc($enquiry['email']) ?></a>
        </div>
        <div class="col-md-6">
            <small class="text-muted d-block">Phone Number:</small>
            <?php if (!empty($enquiry['phone'])): ?>
                <a href="tel:<?= esc($enquiry['phone']) ?>" class="fw-bold text-dark text-decoration-none"><i class="far fa-phone me-1"></i> <?= esc($enquiry['phone']) ?></a>
            <?php else: ?>
                <span class="text-muted">Not provided</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="mb-3">
        <label class="small text-muted text-uppercase font-weight-bold d-block">Subject</label>
        <h6 class="text-dark fw-bold border-bottom pb-2"><?= esc($enquiry['subject']) ?></h6>
    </div>

    <div class="mb-4">
        <label class="small text-muted text-uppercase font-weight-bold d-block mb-2">Message Body</label>
        <div class="p-3 bg-light rounded border text-dark" style="white-space: pre-line; line-height: 1.6; font-size: 0.95rem;">
            <?= esc($enquiry['message']) ?>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <form action="<?= base_url('admin/enquiries/update_status/' . $enquiry['id']) ?>" method="post" class="d-flex align-items-center">
            <?= csrf_field() ?>
            <label class="small text-muted me-2 font-weight-bold">Status:</label>
            <select name="status" class="form-select form-select-sm text-dark me-2" style="width: auto;">
                <option value="unread" <?= $enquiry['status'] === 'unread' ? 'selected' : '' ?>>Unread</option>
                <option value="read" <?= $enquiry['status'] === 'read' ? 'selected' : '' ?>>Read</option>
                <option value="replied" <?= $enquiry['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
            </select>
            <button type="submit" class="btn btn-cyan btn-sm">Update Status</button>
        </form>

        <a href="mailto:<?= esc($enquiry['email']) ?>?subject=Re: <?= urlencode($enquiry['subject']) ?>" class="btn btn-outline-primary btn-sm">
            <i class="far fa-paper-plane me-1"></i> Reply via Email
        </a>
    </div>
</div>
