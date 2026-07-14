<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\SvgCaptcha;

class Auth extends BaseController
{
    protected $userModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->userModel = new UserModel();
    }

    /**
     * Display login page and process login.
     */
    public function login()
    {
        if ($this->authLib->isLoggedIn()) {
            return redirect()->to(base_url());
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $captchaInput = $this->request->getPost('captcha');

            if (empty($email) || empty($password) || empty($captchaInput)) {
                $this->session->setFlashdata('error', 'Please fill in all fields including the Captcha.');
                return redirect()->to(base_url('login'));
            }

            if (strlen($email) > 150 || strlen($password) > 72 || strlen($captchaInput) > 10) {
                $this->session->setFlashdata('error', 'Invalid input length.');
                return redirect()->to(base_url('login'));
            }

            // Verify captcha code
            $sessionCaptcha = $this->session->get('login_captcha');
            if (empty($sessionCaptcha) || strcasecmp($captchaInput, $sessionCaptcha) !== 0) {
                $this->session->remove('login_captcha');
                $this->session->setFlashdata('error', 'Invalid Captcha code. Please try again.');
                return redirect()->to(base_url('login'));
            }
            
            $this->session->remove('login_captcha');

            // Get user
            $user = $this->userModel->getByEmail($email);

            if ($user && $this->authLib->verifyPassword($password, $user['password'])) {
                if ($this->authLib->login($user)) {
                    $this->session->setFlashdata('success', 'Welcome back, ' . esc($user['name']) . '!');
                    
                    if ($this->authLib->isAdmin()) {
                        return redirect()->to(base_url('admin/dashboard'));
                    }
                    
                    return redirect()->to(base_url());
                } else {
                    $this->session->setFlashdata('error', 'Your account is inactive.');
                }
            } else {
                $this->session->setFlashdata('error', 'Invalid email or password.');
            }
            return redirect()->to(base_url('login'));
        }

        $data['meta_title'] = 'Login | GiftShop';
        $data['meta_desc'] = 'Login to your GiftShop account to place orders and check order status.';

        return view('frontend/login', $data);
    }

    /**
     * Display register page and process registration.
     */
    public function register()
    {
        if ($this->authLib->isLoggedIn()) {
            return redirect()->to(base_url());
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $mobile = $this->request->getPost('mobile');
            $password = $this->request->getPost('password');
            $confirm_password = $this->request->getPost('confirm_password');
            $captchaInput = $this->request->getPost('captcha');

            // Validation
            if (empty($name) || empty($email) || empty($mobile) || empty($password) || empty($captchaInput)) {
                $this->session->setFlashdata('error', 'All fields including Captcha are required.');
                return redirect()->to(base_url('register'));
            }

            if (strlen($name) > 100 || strlen($email) > 150 || strlen($mobile) > 15 || strlen($password) > 72 || strlen($captchaInput) > 10) {
                $this->session->setFlashdata('error', 'Invalid input length.');
                return redirect()->to(base_url('register'));
            }

            // Verify captcha code
            $sessionCaptcha = $this->session->get('login_captcha');
            if (empty($sessionCaptcha) || strcasecmp($captchaInput, $sessionCaptcha) !== 0) {
                $this->session->remove('login_captcha');
                $this->session->setFlashdata('error', 'Invalid Captcha code. Please try again.');
                return redirect()->to(base_url('register'));
            }
            
            $this->session->remove('login_captcha');

            if ($password !== $confirm_password) {
                $this->session->setFlashdata('error', 'Passwords do not match.');
                return redirect()->to(base_url('register'));
            }

            // Check if email already exists
            if ($this->userModel->getByEmail($email)) {
                $this->session->setFlashdata('error', 'Email is already registered.');
                return redirect()->to(base_url('register'));
            }

            // Insert user
            $userData = [
                'name'      => $name,
                'email'     => $email,
                'mobile'    => $mobile,
                'password'  => $this->authLib->hashPassword($password),
                'is_active' => 1
            ];

            if ($this->userModel->insert($userData)) {
                $this->session->setFlashdata('success', 'Registration successful! Please login.');
                return redirect()->to(base_url('login'));
            } else {
                $this->session->setFlashdata('error', 'Registration failed. Try again.');
                return redirect()->to(base_url('register'));
            }
        }

        $data['meta_title'] = 'Register | GiftShop';
        $data['meta_desc'] = 'Register for a new GiftShop account to order online.';

        return view('frontend/register', $data);
    }

    /**
     * Log the user out.
     */
    public function logout()
    {
        $this->authLib->logout();
        $this->session->setFlashdata('success', 'Logged out successfully.');
        return redirect()->to(base_url('login'));
    }

    /**
     * Forgot password page.
     */
    public function forgot_password()
    {
        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $email = $this->request->getPost('email');
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->session->setFlashdata('error', 'Please enter a valid email address.');
                return redirect()->to(base_url('forgot-password'));
            }

            // Ensure DB columns exist
            $db = \Config\Database::connect();
            if (!$db->fieldExists('reset_token', 'users')) {
                $db->query("ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(255) NULL");
            }
            if (!$db->fieldExists('reset_expires_at', 'users')) {
                $db->query("ALTER TABLE `users` ADD COLUMN `reset_expires_at` DATETIME NULL");
            }

            $user = $this->userModel->getByEmail($email);
            if ($user) {
                // Generate token
                $token = bin2hex(random_bytes(32));
                // Expire in 1 hour
                $expiry = date('Y-m-d H:i:s', time() + 3600);

                // Update user
                $this->userModel->update($user['id'], [
                    'reset_token' => $token,
                    'reset_expires_at' => $expiry
                ]);

                // Construct reset link
                $resetLink = base_url('reset-password/' . $token);

                // In development, show/flash the link so it can be clicked without SMTP configured
                if (ENVIRONMENT === 'development') {
                    $this->session->setFlashdata('success', 'Password reset instructions simulated! Click this link: <a href="' . $resetLink . '">' . $resetLink . '</a>');
                } else {
                    // Send standard email
                    $emailService = \Config\Services::email();
                    $emailService->setTo($email);
                    $emailService->setSubject('Password Reset Request | ' . get_setting('company_name', 'GiftShop'));
                    $emailService->setMessage("Hello " . esc($user['name']) . ",\r\n\r\nYou requested a password reset. Click the link below to set a new password (valid for 1 hour):\r\n" . $resetLink . "\r\n\r\nIf you did not request this, please ignore this email.");
                    
                    if ($emailService->send()) {
                        $this->session->setFlashdata('success', 'Password reset instructions have been sent to your email.');
                    } else {
                        log_message('error', 'Failed to send reset email: ' . $emailService->printDebugger(['headers']));
                        $this->session->setFlashdata('success', 'Password reset instructions sent if email exists (check logs).');
                    }
                }
            } else {
                $this->session->setFlashdata('success', 'Password reset instructions have been sent if the email exists.');
            }

            return redirect()->to(base_url('forgot-password'));
        }

        $data['meta_title'] = 'Forgot Password | GiftShop';
        $data['meta_desc'] = 'Recover your password.';

        return view('frontend/forgot_password', $data);
    }

    /**
     * Reset password page.
     */
    public function reset_password($token = null)
    {
        if (empty($token)) {
            $this->session->setFlashdata('error', 'Invalid password reset token.');
            return redirect()->to(base_url('login'));
        }

        // Find user by token
        $user = $this->userModel->where('reset_token', $token)
                               ->where('reset_expires_at >=', date('Y-m-d H:i:s'))
                               ->first();

        if (!$user) {
            $this->session->setFlashdata('error', 'Password reset token has expired or is invalid.');
            return redirect()->to(base_url('forgot-password'));
        }

        if (strcasecmp($this->request->getMethod(), 'post') === 0) {
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password');

            if (empty($password) || empty($confirmPassword)) {
                $this->session->setFlashdata('error', 'Please fill in all fields.');
                return redirect()->to(current_url());
            }

            if (strlen($password) < 6 || strlen($password) > 72) {
                $this->session->setFlashdata('error', 'Password must be between 6 and 72 characters.');
                return redirect()->to(current_url());
            }

            if ($password !== $confirmPassword) {
                $this->session->setFlashdata('error', 'Passwords do not match.');
                return redirect()->to(current_url());
            }

            // Update user password and clear token columns
            $updated = $this->userModel->update($user['id'], [
                'password' => $this->authLib->hashPassword($password),
                'reset_token' => null,
                'reset_expires_at' => null
            ]);

            if ($updated) {
                $this->session->setFlashdata('success', 'Your password has been successfully reset. Please login.');
                return redirect()->to(base_url('login'));
            } else {
                $this->session->setFlashdata('error', 'Failed to update password. Try again.');
                return redirect()->to(current_url());
            }
        }

        $data['token'] = $token;
        $data['meta_title'] = 'Reset Password | GiftShop';
        $data['meta_desc'] = 'Enter your new password.';

        return view('frontend/reset_password', $data);
    }

    /**
     * Generate dynamic SVG captcha.
     */
    public function captcha()
    {
        $code = SvgCaptcha::generateCode(5);
        $this->session->set('login_captcha', $code);
        
        $svg = SvgCaptcha::generateSvg($code, 150, 45);
        
        return $this->response
            ->setHeader('Content-Type', 'image/svg+xml')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody($svg);
    }
}
