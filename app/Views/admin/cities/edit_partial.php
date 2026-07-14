<form action="<?= base_url('admin/cities/edit/' . $city['id']) ?>" method="post">
    <?= csrf_field() ?>
    
    <div class="row text-dark">
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">City Name *</label>
            <input type="text" name="name" id="modal-city-name-input" class="form-control" value="<?= esc($city['name']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">URL Slug</label>
            <input type="text" name="slug" id="modal-city-slug-input" class="form-control" value="<?= esc($city['slug']) ?>" required>
            <div id="modal-city-slug-feedback" class="mt-1" style="display:none;"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Popular Status</label>
            <select name="is_popular" class="form-select">
                <option value="0" <?= $city['is_popular'] == 0 ? 'selected' : '' ?>>No</option>
                <option value="1" <?= $city['is_popular'] == 1 ? 'selected' : '' ?>>Yes (Show at Top)</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label font-weight-bold">Status</label>
            <select name="is_active" class="form-select">
                <option value="1" <?= $city['is_active'] == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= $city['is_active'] == 0 ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-3 border-top pt-3">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>

<script>
(function() {
    const CITY_EDIT_ID = <?= (int)$city['id'] ?>;

    const nameInput = document.getElementById('modal-city-name-input');
    const slugInput = document.getElementById('modal-city-slug-input');
    const slugFeedback = document.getElementById('modal-city-slug-feedback');
    let autoSlug = false;
    let slugCheckTimer = null;

    function checkModalCitySlug(slug) {
        if (!slug || slug.length < 2) { slugFeedback.style.display = 'none'; return; }
        clearTimeout(slugCheckTimer);
        slugCheckTimer = setTimeout(function() {
            var url = '<?= base_url('admin/cities/check-slug') ?>?slug=' + encodeURIComponent(slug) + '&id=' + CITY_EDIT_ID;
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    slugFeedback.style.display = 'block';
                    if (data.available) {
                        slugFeedback.innerHTML = '<small class="text-success"><i class="far fa-check-circle me-1"></i>Slug is available</small>';
                        slugInput.classList.remove('is-invalid'); slugInput.classList.add('is-valid');
                    } else {
                        slugFeedback.innerHTML = '<small class="text-danger"><i class="far fa-exclamation-circle me-1"></i>Slug <strong>"' + data.slug + '"</strong> is already in use! Please change it.</small>';
                        slugInput.classList.remove('is-valid'); slugInput.classList.add('is-invalid');
                    }
                });
        }, 450);
    }

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (autoSlug) {
                let slug = this.value.toLowerCase()
                                     .replace(/[^a-z0-9\s-]/g, '')
                                     .replace(/\s+/g, '-')
                                     .replace(/-+/g, '-');
                slugInput.value = slug;
                checkModalCitySlug(slug);
            }
        });

        slugInput.addEventListener('input', function() {
            autoSlug = (this.value === "");
            checkModalCitySlug(this.value);
        });
    }
})();
</script>
