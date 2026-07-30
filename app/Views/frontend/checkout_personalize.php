<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="site-breadcrumb">
        <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
        <div class="container">
            <div class="site-breadcrumb-wrap">
                <h4 class="breadcrumb-title">Personalize Your Order</h4>
                <ul class="breadcrumb-menu">
                    <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                    <li><a href="<?= base_url('checkout') ?>">Checkout</a></li>
                    <li class="active">Personalization</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- Personalize items section -->
    <div class="shop-checkout-complete py-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
                        
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success mb-4"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger mb-4"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <div class="text-center mb-5">
                            <span class="d-inline-block rounded-circle bg-light p-3 text-primary mb-3" style="font-size: 2rem; color: #e76f51 !important;">
                                <i class="fas fa-magic"></i>
                            </span>
                            <h3 class="fw-bold text-dark mb-2">Personalize Your Products</h3>
                            <p class="text-muted">Please provide the customization details below to complete your order placement.</p>
                        </div>

                        <?php foreach ($items as $index => $item): ?>
                            <?php 
                            $custText = $item['customization_text'] ?? '';
                            $custImage = $item['customization_image'] ?? '';
                            $itemImg = !empty($item['image']) ? base_url($item['image']) : base_url('assets/img/product/default.png');
                            ?>
                            <div class="card mb-4 border rounded shadow-sm" style="overflow: hidden;">
                                <div class="card-header bg-light d-flex align-items-center py-3 px-4 border-bottom">
                                    <span class="badge bg-primary me-3 text-white" style="font-size: 0.85rem; padding: 6px 12px; background-color: #e76f51 !important;">Item #<?= $index + 1 ?></span>
                                    <h5 class="m-0 fw-bold text-dark"><?= esc($item['name']) ?></h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4 align-items-center">
                                        <!-- Product Image -->
                                        <div class="col-md-3 text-center">
                                            <img src="<?= $itemImg ?>" alt="<?= esc($item['name']) ?>" class="img-thumbnail rounded" style="max-height: 120px; object-fit: contain;">
                                        </div>
                                        
                                        <!-- Customization inputs -->
                                        <div class="col-md-9 border-start ps-md-4">
                                            <!-- Existing configuration details if any -->
                                            <?php if (!empty($custText) || !empty($custImage)): ?>
                                                <div class="mb-3 p-3 bg-light rounded border border-success">
                                                    <span class="d-block fw-bold text-success small mb-2"><i class="fas fa-check-circle me-1"></i> Saved Details:</span>
                                                    <?php if (!empty($custText)): ?>
                                                        <div class="text-dark small"><strong>Custom Message/Name:</strong> <?= esc($custText) ?></div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($custImage)): ?>
                                                        <div class="mt-2 text-dark small">
                                                            <strong>Custom Uploaded Photo:</strong><br>
                                                            <img src="<?= base_url($custImage) ?>" alt="Custom Image" class="img-thumbnail mt-1" style="max-height: 80px; object-fit: contain;">
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>

                                            <form action="<?= base_url('checkout/personalize/submit/' . $item['id']) ?>" method="post" enctype="multipart/form-data">
                                                <?= csrf_field() ?>
                                                <div class="row g-3">
                                                    <?php if (in_array($item['customization_type'], ['text', 'both'])): ?>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold mb-1">Enter Name or Message <span class="text-danger">*</span></label>
                                                            <input type="text" name="customization_text" class="form-control" placeholder="Type text to print on product..." value="<?= esc($custText) ?>" required style="border-radius: 8px;">
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (in_array($item['customization_type'], ['image', 'both'])): ?>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold mb-1">Upload Photo <span class="text-danger">*</span></label>
                                                            <input type="file" name="customization_image" class="form-control" accept="image/*" <?= empty($custImage) ? 'required' : '' ?> style="border-radius: 8px;">
                                                            <small class="text-muted d-block mt-1">Recommended high-quality JPG/PNG format.</small>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="col-12 text-end mt-3">
                                                        <button type="submit" class="theme-btn py-2 px-4 text-center" style="font-size: 0.9rem; border-radius: 8px;"><i class="fas fa-save me-1"></i> Save Personalization</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="border-top pt-4 text-center mt-5">
                            <p class="text-muted small mb-4">Please make sure to click "Save Personalization" for all products before completing your order.</p>
                            <a href="<?= base_url('checkout/personalize/complete') ?>" class="theme-btn py-3 px-5 text-white" style="font-size: 1.1rem; font-weight: bold; border-radius: 50px; background-color: #2b9348 !important; border-color: #2b9348 !important;">
                                Complete Order & Confirm <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<?= $this->endSection() ?>
