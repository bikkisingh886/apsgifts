<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="m-0 text-white"><i class="far fa-box-open me-2 text-cyan"></i> Products Catalog</h4>
                <div class="d-flex gap-2 align-items-center">
                    <!-- Bulk Delete Bar (hidden until rows selected) -->
                    <div id="bulk-action-bar" class="d-none align-items-center gap-2">
                        <span id="selected-count" class="text-white fw-bold small"></span>
                        <button id="bulk-delete-btn" class="btn btn-danger btn-sm px-3">
                            <i class="far fa-trash-alt me-1"></i> Delete Selected
                        </button>
                    </div>
                    <a href="<?= base_url('admin/products/create') ?>" class="btn-cyan"><i class="far fa-plus-circle me-1"></i> Add New Product</a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="products-table" class="table table-custom">
                    <thead>
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" id="select-all-products" class="form-check-input" title="Select All">
                            </th>
                            <th>Image</th>
                            <th>SKU &amp; Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Delivery Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">No products found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $prod): ?>
                                <?php
                                $imageUrl = $prod['image_path'] ? base_url($prod['image_path']) : base_url('assets/img/product/default.png');
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input product-checkbox" value="<?= $prod['id'] ?>">
                                    </td>
                                    <td>
                                        <img src="<?= $imageUrl ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
                                    </td>
                                    <td>
                                        <strong class="text-white"><?= esc($prod['name']) ?></strong><br>
                                        <small class="text-cyan">SKU: <?= esc($prod['sku']) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $catNames = array_column($prod['categories'] ?? [], 'name');
                                        echo esc(implode(', ', $catNames));
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($prod['offer_value'] > 0): ?>
                                            <?php
                                            $orig = (float)$prod['price'];
                                            $disc = $prod['offer_type'] === 'percent' ? $orig * (1 - $prod['offer_value']/100) : $orig - $prod['offer_value'];
                                            ?>
                                            <strong class="text-cyan">₹<?= number_format($disc, 2) ?></strong><br>
                                            <del class="text-muted small">₹<?= number_format($orig, 2) ?></del>
                                        <?php else: ?>
                                            <strong class="text-white">₹<?= number_format($prod['price'], 2) ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($prod['is_active']): ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $prod['delivery_type'] === 'Express' ? 'bg-primary' : 'bg-info text-dark' ?>">
                                            <?= esc($prod['delivery_type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/products/toggle/' . $prod['id']) ?>" class="badge badge-status <?= $prod['is_active'] ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                                            <?= $prod['is_active'] ? 'Active' : 'Inactive' ?>
                                        </a>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <a href="<?= base_url('admin/products/edit/' . $prod['id']) ?>" class="btn btn-outline-cyan btn-sm me-2 btn-edit-popup"><i class="far fa-edit"></i> Edit</a>
                                        <a href="<?= base_url('admin/products/delete/' . $prod['id']) ?>" class="btn btn-outline-danger btn-sm btn-single-delete" data-name="<?= esc($prod['name']) ?>"><i class="far fa-trash-alt"></i> Delete</a>
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

<style>
    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
    }
    div.dataTables_wrapper div.dataTables_filter input {
        border-radius: 8px;
        padding: 5px 10px;
        border: 1px solid var(--border-color);
        font-family: inherit;
    }
    .dt-buttons .btn {
        background-color: #ffffff !important;
        color: var(--text-dark) !important;
        border: 1px solid var(--border-color) !important;
        margin-right: 5px;
        border-radius: 8px !important;
        font-weight: 600;
        padding: 6px 14px;
        font-size: 0.85rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }
    .dt-buttons .btn:hover {
        background-color: var(--primary-coral) !important;
        color: #ffffff !important;
        border-color: var(--primary-coral) !important;
    }
    .dataTables_paginate .paginate_button.active .page-link {
        background-color: var(--primary-coral) !important;
        border-color: var(--primary-coral) !important;
        color: white !important;
    }
    .dataTables_paginate .page-link {
        color: var(--text-dark);
        border-radius: 6px;
        margin: 0 2px;
    }
    /* Checkbox column not sortable visually */
    #products-table thead th:first-child::after,
    #products-table thead th:first-child::before { display: none !important; }
</style>

<script>
$(document).ready(function() {
    var table = $('#products-table').DataTable({
        "dom": "<'row mb-3'<'col-md-4'l><'col-md-4 text-center'B><'col-md-4'f>>" +
               "<'row'<'col-12'tr>>" +
               "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        "buttons": [
            {
                extend: 'csv',
                text: '<i class="far fa-file-csv me-1"></i> CSV',
                className: 'btn btn-outline-secondary btn-sm',
                exportOptions: { columns: [ 2, 3, 4, 5, 6, 7 ] }
            },
            {
                extend: 'excel',
                text: '<i class="far fa-file-excel me-1"></i> Excel',
                className: 'btn btn-outline-secondary btn-sm',
                exportOptions: { columns: [ 2, 3, 4, 5, 6, 7 ] }
            },
            {
                extend: 'pdf',
                text: '<i class="far fa-file-pdf me-1"></i> PDF',
                className: 'btn btn-outline-secondary btn-sm',
                exportOptions: { columns: [ 2, 3, 4, 5, 6, 7 ] }
            }
        ],
        "pageLength": 15,
        "lengthMenu": [10, 15, 25, 50, 100],
        "language": {
            "search": "Search:",
            "lengthMenu": "_MENU_ per page"
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 1, 8] } // Checkbox, Image, Actions not sortable
        ]
    });

    // =====================
    // Select All / Deselect
    // =====================
    $('#select-all-products').on('change', function() {
        // Only check visible rows (respects pagination)
        table.rows({ page: 'current' }).nodes().to$().find('.product-checkbox').prop('checked', this.checked);
        updateBulkBar();
    });

    $(document).on('change', '.product-checkbox', function() {
        updateBulkBar();
        // Uncheck select-all if any unchecked
        var total = table.rows({ page: 'current' }).nodes().to$().find('.product-checkbox').length;
        var checked = table.rows({ page: 'current' }).nodes().to$().find('.product-checkbox:checked').length;
        $('#select-all-products').prop('checked', total === checked && total > 0);
    });

    function updateBulkBar() {
        var count = $('.product-checkbox:checked').length;
        if (count > 0) {
            $('#bulk-action-bar').removeClass('d-none').addClass('d-flex');
            $('#selected-count').text(count + ' selected');
        } else {
            $('#bulk-action-bar').removeClass('d-flex').addClass('d-none');
            $('#select-all-products').prop('checked', false);
        }
    }

    // Reset checkboxes on page change
    table.on('page.dt', function() {
        $('#select-all-products').prop('checked', false);
        updateBulkBar();
    });

    // =====================
    // Bulk Delete
    // =====================
    $('#bulk-delete-btn').on('click', function() {
        var ids = [];
        table.rows().nodes().to$().find('.product-checkbox:checked').each(function() {
            ids.push($(this).val());
        });
        if (ids.length === 0) return;

        Swal.fire({
            title: 'Delete ' + ids.length + ' Product' + (ids.length > 1 ? 's' : '') + '?',
            html: 'This will permanently delete <strong>' + ids.length + '</strong> selected product' + (ids.length > 1 ? 's' : '') + ' and all their images. This action <strong>cannot be undone</strong>.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Yes, Delete All',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: '<?= base_url('admin/products/bulk-delete') ?>',
                    type: 'POST',
                    data: { ids: ids, '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 2000, showConfirmButton: false })
                                .then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Server error. Please try again.' });
                    }
                });
            }
        });
    });

    // =====================
    // Single Delete via SweetAlert
    // =====================
    $(document).on('click', '.btn-single-delete', function(e) {
        e.preventDefault();
        var url  = $(this).attr('href');
        var name = $(this).data('name');

        Swal.fire({
            title: 'Delete "' + name + '"?',
            text: 'This product and all its images will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
