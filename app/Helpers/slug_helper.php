<?php

if (!function_exists('generate_slug')) {
    /**
     * Generate an SEO-friendly URL slug from a string.
     */
    function generate_slug(string $string): string {
        $slug = strtolower($string);
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
}
