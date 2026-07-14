<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card-custom mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-dark fw-bold mb-0">
                    <i class="far fa-home me-2 text-cyan"></i> Homepage Sections Manager
                </h4>
                <button type="button" id="save-order-btn" class="btn btn-cyan text-white px-4 py-2" style="background-color: #00bcd4; border: none; display: none;">
                    <i class="far fa-save me-1"></i> Save Sections Order
                </button>
            </div>
            <p class="text-muted">
                Manage the layout of your homepage. Drag rows to re-order the sections, click the status badges to enable/disable them, or click "Edit" to modify their content dynamically.
            </p>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive mt-4">
                <table class="table table-custom align-middle" id="sections-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Drag</th>
                            <th>Section Key</th>
                            <th>Display Title</th>
                            <th>Subtitle</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-sections">
                        <?php foreach ($sections as $sec): ?>
                            <tr data-id="<?= $sec['id'] ?>" draggable="true" class="section-row">
                                <td>
                                    <span class="drag-handle" style="cursor: move; font-size: 1.2rem; color: #bbb;">
                                        <i class="fas fa-grip-vertical"></i>
                                    </span>
                                </td>
                                <td><code class="text-cyan fw-bold"><?= esc($sec['section_key']) ?></code></td>
                                <td><strong class="text-dark"><?= esc($sec['title']) ?></strong></td>
                                <td><span class="text-muted"><?= esc($sec['subtitle'] ?: '-') ?></span></td>
                                <td><span class="badge bg-light text-dark font-monospace"><?= (int)$sec['sort_order'] ?></span></td>
                                <td>
                                    <a href="<?= base_url('admin/homepage/toggle/' . $sec['id']) ?>" class="badge badge-status <?= $sec['is_active'] ? 'bg-success text-white' : 'bg-danger text-white' ?>" style="text-decoration: none;">
                                        <?= $sec['is_active'] ? 'Active' : 'Inactive' ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/homepage/edit/' . $sec['id']) ?>" class="btn btn-outline-cyan btn-sm"><i class="far fa-edit"></i> Edit Content</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.section-row.dragging {
    opacity: 0.5;
    background-color: #f1f9fa;
    border: 2px dashed #00bcd4;
}
.section-row {
    transition: background-color 0.2s;
}
.section-row:hover {
    background-color: #fafafa;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const tbody = document.getElementById('sortable-sections');
    const saveBtn = document.getElementById('save-order-btn');
    let dragSrcEl = null;

    function handleDragStart(e) {
        this.classList.add('dragging');
        dragSrcEl = this;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDragEnter(e) {
        this.classList.add('over');
    }

    function handleDragLeave(e) {
        this.classList.remove('over');
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        
        if (dragSrcEl !== this) {
            // Swap row data-ids and row elements
            const allRows = Array.from(tbody.querySelectorAll('.section-row'));
            const srcIndex = allRows.indexOf(dragSrcEl);
            const targetIndex = allRows.indexOf(this);
            
            if (srcIndex < targetIndex) {
                tbody.insertBefore(dragSrcEl, this.nextSibling);
            } else {
                tbody.insertBefore(dragSrcEl, this);
            }
            saveBtn.style.display = 'block';
        }
        return false;
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        const rows = tbody.querySelectorAll('.section-row');
        rows.forEach(function (row) {
            row.classList.remove('over');
        });
    }

    function addDnDHandlers(row) {
        row.addEventListener('dragstart', handleDragStart, false);
        row.addEventListener('dragenter', handleDragEnter, false);
        row.addEventListener('dragover', handleDragOver, false);
        row.addEventListener('dragleave', handleDragLeave, false);
        row.addEventListener('drop', handleDrop, false);
        row.addEventListener('dragend', handleDragEnd, false);
    }

    const rows = tbody.querySelectorAll('.section-row');
    rows.forEach(function(row) {
        addDnDHandlers(row);
    });

    saveBtn.addEventListener('click', function() {
        const sortedIds = [];
        const rows = tbody.querySelectorAll('.section-row');
        rows.forEach(row => {
            sortedIds.push(row.getAttribute('data-id'));
        });

        // Send AJAX request
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
        
        const formData = new FormData();
        sortedIds.forEach(id => {
            formData.append('ids[]', id);
        });
        
        // CSRF Token
        const csrfName = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        formData.append(csrfName, csrfHash);

        fetch('<?= base_url("admin/homepage/update-order") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error saving order: ' + (data.error || 'Unknown error'));
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="far fa-save me-1"></i> Save Sections Order';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Connection error occurred while saving.');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="far fa-save me-1"></i> Save Sections Order';
        });
    });
});
</script>
<?= $this->endSection() ?>
