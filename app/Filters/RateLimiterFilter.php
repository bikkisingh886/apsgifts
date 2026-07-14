<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\RateLimiter;

class RateLimiterFilter implements FilterInterface
{
    /**
     * Apply rate limiting checks before controller execution.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $rateLimiter = new RateLimiter();
        
        $ip = $request->getIPAddress();
        $session = \Config\Services::session();
        $sessionId = $session->session_id ?? 'no_session';
        $path = $request->getPath();

        // 1. IP Rate Limiting (General IP Abuse protection)
        // Limit: 60 requests per 60 seconds
        $ipIdentifier = 'ip:' . $ip;
        if ($rateLimiter->isRateLimited($ipIdentifier, $path, 60, 60, $ip)) {
            return $this->buildBlockResponse('Too many requests from this IP. Please wait a minute.');
        }

        // 2. Session Rate Limiting (Session ID hijacking/automation protection)
        // Limit: 30 requests per 60 seconds
        if ($sessionId !== 'no_session') {
            $sessionIdentifier = 'sess:' . $sessionId;
            if ($rateLimiter->isRateLimited($sessionIdentifier, $path, 30, 60, $ip)) {
                return $this->buildBlockResponse('Too many requests from this session. Please wait.');
            }
        }

        // 3. Identity Rate Limiting (Account targeting protection)
        // Limit: 5 requests per 120 seconds per email address
        // Check if there is an email input in the POST body
        if (strcasecmp($request->getMethod(), 'post') === 0) {
            $email = $request->getPost('email');
            if (!empty($email) && is_string($email) && strlen($email) <= 150) {
                // Sanitize and lowercase key to prevent case bypass
                $emailKey = strtolower(trim($email));
                $emailIdentifier = 'email:' . md5($emailKey); // use md5 to avoid storing plain emails in rate logs for compliance
                if ($rateLimiter->isRateLimited($emailIdentifier, $path, 5, 120, $ip)) {
                    return $this->buildBlockResponse('Too many login/verification attempts for this account. Please try again in 2 minutes.');
                }
            }
        }

        return null;
    }

    /**
     * Build HTTP 429 Too Many Requests response.
     */
    protected function buildBlockResponse(string $message)
    {
        $response = \Config\Services::response();
        $response->setStatusCode(429);
        $response->setHeader('Retry-After', '60');
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8');
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 Too Many Requests | GiftShop</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8f9fa; color: #495057; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .container { text-align: center; max-width: 500px; padding: 40px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border-top: 4px solid #dc3545; }
        h1 { color: #dc3545; font-size: 2.2rem; margin-bottom: 20px; }
        p { font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px; }
        .btn { display: inline-block; padding: 12px 24px; color: #fff; background-color: #007bff; border-radius: 4px; text-decoration: none; font-weight: bold; transition: background-color 0.2s; }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Too Many Requests</h1>
        <p>' . esc($message) . '</p>
        <a href="javascript:location.reload();" class="btn">Try Again</a>
    </div>
</body>
</html>';

        $response->setBody($html);
        return $response;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after execution
        return null;
    }
}
