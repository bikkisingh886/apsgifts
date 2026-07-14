<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- Minimalist Breadcrumb -->
    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size: 0.85rem; padding: 0; margin-bottom: 0; background: none;">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-muted" style="text-decoration: none;"><i class="far fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-weight: 500;">Profile Settings</li>
            </ol>
        </nav>
    </div>

    <!-- settings area -->
    <div class="user-area py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <?= $this->include('frontend/user/sidebar_partial') ?>
                </div>
                <div class="col-lg-9">
                    <div class="user-wrapper">
                        <div class="user-card p-4 border rounded bg-white shadow-sm">
                            <h4 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="far fa-user-cog text-primary me-2"></i> Profile Settings</h4>
                            
                            <?php if (session()->getFlashdata('success')): ?>
                                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                            <?php endif; ?>
                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                            <?php endif; ?>

                            <form action="<?= base_url('user/settings/update') ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="row g-3">
                                    <div class="col-md-12 text-center mb-4">
                                        <div class="profile-photo-container d-inline-block position-relative">
                                            <img src="<?= !empty($user['profile_photo']) ? base_url($user['profile_photo']) : base_url('assets/img/account/02.jpg') ?>" alt="Profile Photo" class="img-thumbnail rounded-circle" style="width: 130px; height: 130px; object-fit: cover;">
                                            <div class="mt-2">
                                                <label class="form-label small fw-bold text-dark">Upload Profile Photo</label>
                                                <input type="file" name="profile_photo" class="form-control form-control-sm" accept="image/*">
                                                <small class="text-muted d-block mt-1">Accepts JPG, PNG formats. Max 2MB.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold text-dark">Full Name *</label>
                                            <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold text-dark">Email Address *</label>
                                            <input type="email" class="form-control" value="<?= esc($user['email']) ?>" readonly disabled>
                                            <small class="text-muted">Email cannot be changed.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold text-dark">Mobile Number *</label>
                                            <input type="text" name="mobile" class="form-control" value="<?= esc($user['mobile']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold text-dark">New Password</label>
                                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="theme-btn py-3 px-4">Update Profile Details</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
