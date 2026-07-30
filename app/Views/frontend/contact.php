<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="site-breadcrumb">
        <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
        <div class="container">
            <div class="site-breadcrumb-wrap">
                <h4 class="breadcrumb-title">Contact Us</h4>
                <ul class="breadcrumb-menu">
                    <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                    <li class="active">Contact Us</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- contact area -->
    <div class="contact-area pt-100 pb-80">
        <div class="container">
            <div class="contact-wrapper">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="contact-content">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="contact-info">
                                        <div class="contact-info-icon">
                                            <i class="fal fa-map-location-dot"></i>
                                        </div>
                                        <div class="contact-info-content">
                                            <h5>Office Address</h5>
                                            <p><?= esc(get_setting('company_address', 'Main Office, New Delhi, India')) ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="contact-info">
                                        <div class="contact-info-icon">
                                            <i class="fal fa-headset"></i>
                                        </div>
                                        <div class="contact-info-content">
                                            <h5>Call Us</h5>
                                            <p><a href="tel:<?= esc(get_setting('company_phone', '+91 8882570131')) ?>" class="text-secondary"><?= esc(get_setting('company_phone', '+91 8882570131')) ?></a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="contact-info">
                                        <div class="contact-info-icon">
                                            <i class="fal fa-envelopes"></i>
                                        </div>
                                        <div class="contact-info-content">
                                            <h5>Email Us</h5>
                                            <p><a href="mailto:<?= esc(get_setting('company_email', 'info@giftshop.in')) ?>" class="text-secondary"><?= esc(get_setting('company_email', 'info@giftshop.in')) ?></a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="contact-info">
                                        <div class="contact-info-icon">
                                            <i class="fal fa-alarm-clock"></i>
                                        </div>
                                        <div class="contact-info-content">
                                            <h5>Working Hours</h5>
                                            <p><?= esc(get_setting('company_working_hours', 'Mon-Sun (9.00AM - 9.00PM)')) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contact-form">
                            <div class="contact-form-header">
                                <h2>Get In Touch</h2>
                                <p>Have questions about your order or need assistance selecting the perfect gift? We are here to help!</p>
                            </div>

                            <?php if (session()->getFlashdata('success_message')): ?>
                                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                    <i class="far fa-check-circle me-2"></i> <?= session()->getFlashdata('success_message') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <div id="contact-alert-container"></div>

                            <form method="post" action="<?= base_url('contact-us') ?>" id="contact-form">
                                <?= csrf_field() ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label text-dark fw-bold small">Your Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" id="contact_name" placeholder="John Doe" required value="<?= old('name') ?>">
                                            <div class="invalid-feedback" id="err_name"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label text-dark fw-bold small">Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" id="contact_email" placeholder="john@example.com" required value="<?= old('email') ?>">
                                            <div class="invalid-feedback" id="err_email"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label text-dark fw-bold small">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" id="contact_phone" placeholder="+91 9876543210" value="<?= old('phone') ?>">
                                            <div class="invalid-feedback" id="err_phone"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label text-dark fw-bold small">Subject <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="subject" id="contact_subject" placeholder="Order Enquiry / Custom Gift Box" required value="<?= old('subject') ?>">
                                            <div class="invalid-feedback" id="err_subject"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label text-dark fw-bold small">Your Message <span class="text-danger">*</span></label>
                                    <textarea name="message" id="contact_message" cols="30" rows="4" class="form-control" placeholder="Tell us how we can help you..." required><?= old('message') ?></textarea>
                                    <div class="invalid-feedback" id="err_message"></div>
                                </div>
                                <button type="submit" class="theme-btn" id="contact-submit-btn">
                                    <span class="btn-text">Send Message</span> <i class="far fa-paper-plane ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end contact area -->

</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const contactForm = document.getElementById('contact-form');
    const alertContainer = document.getElementById('contact-alert-container');
    const submitBtn = document.getElementById('contact-submit-btn');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous error styles
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            alertContainer.innerHTML = '';

            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Sending...';
            submitBtn.disabled = true;

            const formData = new FormData(contactForm);

            fetch(contactForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (data.success) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="far fa-check-circle me-2"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    contactForm.reset();
                } else if (data.errors) {
                    let errList = '';
                    for (let key in data.errors) {
                        const inputEl = document.getElementById('contact_' + key);
                        const errEl = document.getElementById('err_' + key);
                        if (inputEl) inputEl.classList.add('is-invalid');
                        if (errEl) errEl.innerText = data.errors[key];
                        errList += `<li>${data.errors[key]}</li>`;
                    }
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <strong><i class="far fa-exclamation-triangle me-2"></i> Please correct the errors below:</strong>
                            <ul class="mb-0 mt-1 ps-3">${errList}</ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="far fa-exclamation-circle me-2"></i> An unexpected error occurred. Please try again later.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
