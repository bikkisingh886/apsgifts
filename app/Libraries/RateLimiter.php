<?php

namespace App\Libraries;

use Config\Database;

class RateLimiter
{
    protected $db;
    protected $table = 'rate_limit_logs';

    public function __construct()
    {
        $this->db = Database::connect();
        $this->ensureTableExists();
    }

    /**
     * Create rate_limit_logs table if it doesn't exist.
     */
    protected function ensureTableExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . $this->table . "` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `identifier` VARCHAR(255) NOT NULL,
            `endpoint` VARCHAR(255) NOT NULL,
            `timestamp` BIGINT NOT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            INDEX `idx_identifier_timestamp` (`identifier`, `timestamp`),
            INDEX `idx_timestamp` (`timestamp`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $this->db->query($sql);
    }

    /**
     * Check if request should be rate limited under sliding window log.
     *
     * @param string $identifier Unique identifier for the client (e.g. "ip:192.168.1.1" or "email:test@email.com")
     * @param string $endpoint The endpoint/action (e.g. "/login")
     * @param int $maxAttempts Max allowed attempts within the window
     * @param int $windowSeconds Window size in seconds
     * @param string $ipAddress Client IP address
     * @return bool True if rate limit is exceeded (should block), false if allowed.
     */
    public function isRateLimited(string $identifier, string $endpoint, int $maxAttempts, int $windowSeconds, string $ipAddress): bool
    {
        $currentTime = time();
        $windowStart = $currentTime - $windowSeconds;

        // Prune expired logs first to keep DB clean
        $this->pruneExpired($windowStart);

        // Count how many hits exist in the current window
        $builder = $this->db->table($this->table);
        $count = $builder->where('identifier', $identifier)
                         ->where('endpoint', $endpoint)
                         ->where('timestamp >=', $windowStart)
                         ->countAllResults();

        if ($count >= $maxAttempts) {
            return true; // Exceeded limit
        }

        // Record the current hit
        $builder->insert([
            'identifier' => $identifier,
            'endpoint'   => $endpoint,
            'timestamp'  => $currentTime,
            'ip_address' => $ipAddress,
        ]);

        return false;
    }

    /**
     * Prune expired logs.
     */
    protected function pruneExpired(int $thresholdTime)
    {
        $this->db->table($this->table)
                 ->where('timestamp <', $thresholdTime)
                 ->delete();
    }
}
