<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card-custom">
            <h4 class="mb-4 text-white"><i class="far fa-shopping-bag me-2 text-cyan"></i> Manage Orders</h4>
            <div class="table-responsive">
                <table id="orders-table" class="table table-custom">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Customer</th>
                            <th>Scheduled Delivery Date</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Placed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $ord): ?>
                                <?php
                                $status_badge = 'bg-info text-dark';
                                if ($ord['status'] === 'Delivered' || $ord['status'] === 'Completed') $status_badge = 'bg-success text-white';
                                elseif ($ord['status'] === 'Shipped') $status_badge = 'bg-info text-white';
                                elseif ($ord['status'] === 'Processing') $status_badge = 'bg-primary text-white';
                                elseif ($ord['status'] === 'Cancelled') $status_badge = 'bg-danger text-white';


                                $address = json_decode($ord['address_json'], true);
                                ?>
                                <tr>
                                    <td><strong class="text-cyan">#<?= esc($ord['order_number']) ?></strong></td>
                                    <td>
                                        <span class="text-white fw-bold"><?= esc($address['name'] ?? 'User') ?></span><br>
                                        <small class="text-muted"><?= esc($address['mobile'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?= date('d M Y', strtotime($ord['delivery_date'])) ?>
                                        </span>
                                    </td>
                                    <td>₹<?= number_format($ord['subtotal'], 2) ?></td>
                                    <td>-₹<?= number_format($ord['discount'], 2) ?></td>
                                    <td><strong class="text-white">₹<?= number_format($ord['total'], 2) ?></strong></td>
                                    <td><span class="badge badge-status <?= $status_badge ?>"><?= esc($ord['status']) ?></span></td>
                                    <td><?= date('d M Y h:i A', strtotime($ord['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center flex-nowrap">
                                            <a href="<?= base_url('admin/orders/view/' . $ord['id']) ?>" class="btn btn-outline-cyan btn-sm me-2 text-nowrap"><i class="far fa-eye"></i> View</a>
                                            <form action="<?= base_url('admin/orders/update-status') ?>" method="post" class="m-0 p-0">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                                <select name="status" class="form-select form-select-sm" style="min-width: 125px;" onchange="this.form.submit()">
                                                    <option value="Processing" <?= ($ord['status'] === 'Processing') ? 'selected' : '' ?>>Processing</option>
                                                    <option value="Shipped" <?= ($ord['status'] === 'Shipped') ? 'selected' : '' ?>>Shipped</option>
                                                    <option value="Delivered" <?= ($ord['status'] === 'Delivered') ? 'selected' : '' ?>>Delivered</option>
                                                    <option value="Cancelled" <?= ($ord['status'] === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </form>
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

<!-- DataTables CSS & Buttons -->
<style>
.dt-buttons .btn { background:#fff !important; color:#333 !important; border:1px solid #dee2e6 !important; margin-right:4px; border-radius:8px !important; font-weight:600; padding:5px 12px; font-size:.82rem; transition:all .2s; }
.dt-buttons .btn:hover { background:var(--primary-coral,#e76f51) !important; color:#fff !important; border-color:var(--primary-coral,#e76f51) !important; }
.dataTables_paginate .paginate_button.active .page-link { background:var(--primary-coral,#e76f51) !important; border-color:var(--primary-coral,#e76f51) !important; color:#fff !important; }
.dataTables_paginate .page-link { color:#333; border-radius:6px; margin:0 2px; }
</style>
<script>
$(document).ready(function() {
    $('#orders-table').DataTable({
        dom: "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            { extend:'csv',   text:'<i class="far fa-file-csv me-1"></i> CSV',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4,5,6,7]} },
            { extend:'excel', text:'<i class="far fa-file-excel me-1"></i> Excel', className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4,5,6,7]} },
            { extend:'pdf',   text:'<i class="far fa-file-pdf me-1"></i> PDF',   className:'btn btn-outline-secondary btn-sm', exportOptions:{columns:[0,1,2,3,4,5,6,7]} }
        ],
        order: [[7, 'desc']], // Latest orders first
        pageLength: 15,
        lengthMenu: [10,15,25,50,100],
        language: { search:'Search:', lengthMenu:'_MENU_ per page' }
    });
});
</script>
<?= $this->endSection() ?>

