<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

        <!-- breadcrumb -->
        <div class="site-breadcrumb">
            <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
            <div class="container">
                <div class="site-breadcrumb-wrap">
                    <h4 class="breadcrumb-title">Reset Password</h4>
                    <ul class="breadcrumb-menu">
                        <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                        <li class="active">Reset Password</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- breadcrumb end -->


        <!-- reset password -->
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
                            <p>Enter your new secure password</p>
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

                        <form action="<?= base_url('reset-password/' . esc($token)) ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="New Password" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required minlength="6">
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="submit" class="theme-btn"><i class="far fa-key"></i> Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- reset password end -->

    </main>
<?= $this->endSection() ?>
