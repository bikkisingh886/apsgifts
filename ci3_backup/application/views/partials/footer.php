<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <!-- Footer Area -->
    <footer class="footer-area bg-dark text-white pt-5 mt-5">
        <div class="container pb-4">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h4 class="text-white fw-bold mb-3"><i class="far fa-gift"></i> GiftShop</h4>
                        <p class="text-white-50">Your premier destination for high-quality gifts, fresh flowers, delicious cakes, and customized items. Making every occasion memorable.</p>
                        <div class="footer-contact mt-3 text-white-50">
                            <p><i class="far fa-map-marker-alt me-2 text-primary"></i> Patna, Bihar, India</p>
                            <p><i class="far fa-phone me-2 text-primary"></i> +91 98765 43210</p>
                            <p><i class="far fa-envelope me-2 text-primary"></i> support@giftshop.in</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5 class="text-white mb-3">Quick Links</h5>
                        <ul class="list-unstyled text-white-50 footer-links">
                            <li class="mb-2"><a href="<?= base_url() ?>" class="text-white-50 text-decoration-none">Home</a></li>
                            <li class="mb-2"><a href="<?= base_url('cart') ?>" class="text-white-50 text-decoration-none">My Cart</a></li>
                            <li class="mb-2"><a href="<?= base_url('wishlist') ?>" class="text-white-50 text-decoration-none">My Wishlist</a></li>
                            <li class="mb-2"><a href="<?= base_url('orders') ?>" class="text-white-50 text-decoration-none">Track Order</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="text-white mb-3">Customer Service</h5>
                        <ul class="list-unstyled text-white-50">
                            <li class="mb-2">Payment Option: COD / Manual UPI</li>
                            <li class="mb-2">Same Day Delivery (Express)</li>
                            <li class="mb-2">Courier Delivery (7 working days)</li>
                            <li class="mb-2">Secure Transactions (CSRF Enabled)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="text-white mb-3">Delivery Options</h5>
                        <div class="d-flex flex-column text-white-50">
                            <span class="badge badge-express p-2 mb-2 text-start"><i class="far fa-bolt"></i> Express: Today / Tomorrow / Schedule</span>
                            <span class="badge badge-courier p-2 text-start"><i class="far fa-truck"></i> Courier: Est. 5-7 working days</span>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="border-secondary mt-5 mb-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0 text-white-50">&copy; <?= date('Y') ?> GiftShop. Patna, Bihar. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-social">
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" class="text-white-50 me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Area End -->

    <!-- Scroll Top Button -->
    <a href="#" id="scroll-top" class="btn btn-primary rounded-circle position-fixed bottom-0 end-0 m-4 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; z-index: 999;"><i class="far fa-arrow-up"></i></a>

    <!-- JS Scripts -->
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/modernizr.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/imagesloaded.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.magnific-popup.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/isotope.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.appear.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.easing.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/owl.carousel.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/counter-up.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery-ui.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.nice-select.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/countdown.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/wow.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <!-- CSRF and AJAX Settings -->
    <script>
        var csrf_name = '<?= $this->security->get_csrf_token_name() ?>';
        var csrf_hash = '<?= $this->security->get_csrf_hash() ?>';

        // Integrate CSRF Token in jQuery AJAX
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (settings.type === 'POST') {
                    if (typeof settings.data === 'string') {
                        if (settings.data.indexOf(csrf_name) === -1) {
                            settings.data += '&' + csrf_name + '=' + csrf_hash;
                        }
                    } else if (settings.data instanceof FormData) {
                        if (!settings.data.has(csrf_name)) {
                            settings.data.append(csrf_name, csrf_hash);
                        }
                    } else {
                        settings.data = settings.data || {};
                        settings.data[csrf_name] = csrf_hash;
                    }
                }
            }
        });
        
        // Handle CSRF regeneration after requests if CodeIgniter changes the hash
        $(document).ajaxComplete(function(event, xhr, settings) {
            // Read updated CSRF token from cookie if available, or keep using standard
            // In CI3, if csrf_regenerate is TRUE, we can get it from headers or a meta tag if sent back.
            // Our app uses standard session cookie so this will be fine.
        });
    </script>
</body>
</html>
