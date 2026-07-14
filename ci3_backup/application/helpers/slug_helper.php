<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('generate_slug')) {
    /**
     * Generate an SEO-friendly URL slug from a string.
     *
     * @param string $string
     * @return string
     */
    function generate_slug($string) {
        // Convert to lowercase
        $slug = strtolower($string);
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        // Clean up multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        // Trim hyphens from beginning and end
        $slug = trim($slug, '-');
        return $slug;
    }
}
