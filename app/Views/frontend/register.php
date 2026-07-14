<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="site-breadcrumb">
        <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
        <div class="container">
            <div class="site-breadcrumb-wrap">
                <h4 class="breadcrumb-title">Register</h4>
                <ul class="breadcrumb-menu">
                    <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                    <li class="active">Register</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- register area -->
    <div class="login-area py-100">
        <div class="container">
            <div class="col-md-5 mx-auto">
                <div class="login-form">
                    <div class="login-header">
                        <?php if ($logo = get_setting('company_logo')): ?>
                            <img src="<?= base_url($logo) ?>" alt="<?= esc(get_setting('company_name', 'OyeGifts')) ?>" style="max-height: 45px; object-fit: contain;">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/logo/logo.png') ?>" alt="">
                        <?php endif; ?>
                        <p>Create your free <?= esc(get_setting('company_name', 'GiftShop')) ?> account</p>
                    </div>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger" style="padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success" style="padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px;">
                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('register') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" placeholder="Your Mobile Number" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Your Password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label>Captcha Verification</label>
                            <div class="d-flex align-items-center gap-2 mb-2" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <img id="captcha-img" src="<?= base_url('captcha') ?>" alt="Captcha" style="border: 1px solid #ced4da; border-radius: 4px; height: 45px; width: 150px; display: block;">
                                <button type="button" id="captcha-reload" class="btn btn-outline-secondary btn-sm" style="height: 45px; padding: 0 15px; background: none; border: 1px solid #ced4da; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;"><i class="far fa-sync-alt"></i></button>
                            </div>
                            <input type="text" name="captcha" class="form-control" placeholder="Enter Captcha Code" required autocomplete="off" style="text-transform: uppercase;">
                        </div>

                        <div class="form-check form-group">
                            <input class="form-check-input" type="checkbox" value="1" id="agree" required>
                            <label class="form-check-label" for="agree">
                               I agree with the <a href="<?= base_url('terms') ?>">Terms of Service</a> & <a href="<?= base_url('privacy') ?>">Privacy Policy</a>.
                            </label>
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="submit" class="theme-btn"><i class="far fa-paper-plane"></i> Register</button>
                        </div>
                    </form>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var captchaImg = document.getElementById('captcha-img');
                        var captchaReload = document.getElementById('captcha-reload');
                        if (captchaReload && captchaImg) {
                            captchaReload.addEventListener('click', function() {
                                captchaImg.src = '<?= base_url('captcha') ?>?' + Date.now();
                            });
                        }
                    });
                    </script>
                    
                    <div class="login-footer">
                        <p>Already have an account? <a href="<?= base_url('login') ?>">Login.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- register area end -->

</main>
<?= $this->endSection() ?>
