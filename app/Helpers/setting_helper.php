<?php

use App\Models\SettingModel;

if (!function_exists('get_setting')) {
    /**
     * Get value of a site setting key with a static cache.
     */
    function get_setting(string $key, string $default = ''): string {
        static $settings = null;
        if ($settings === null) {
            try {
                $model = new SettingModel();
                $settings = [];
                
                $db = \Config\Database::connect();
                if ($db->tableExists('settings')) {
                    foreach ($model->findAll() as $row) {
                        $settings[$row['key']] = $row['value'] ?? '';
                    }
                }
            } catch (\Exception $e) {
                return $default;
            }
        }
        return $settings[$key] ?? $default;
    }
}

if (!function_exists('get_category_url')) {
    /**
     * Get clean 3-level Category URL dynamically by traversing parents.
     * Returns base_url('slug1/slug2/slug3')
     */
    function get_category_url($category): string {
        if (empty($category)) {
            return base_url();
        }
        
        $current = $category;
        
        // If numeric ID is passed, retrieve the category first
        if (is_numeric($category)) {
            $categoryModel = new \App\Models\CategoryModel();
            $current = $categoryModel->find($category);
        } elseif (is_string($category)) {
            $categoryModel = new \App\Models\CategoryModel();
            $current = $categoryModel->getBySlug($category);
        }
        
        if (is_array($current) && isset($current['id'])) {
            if (!isset($current['parent_id']) || !isset($current['slug'])) {
                $categoryModel = new \App\Models\CategoryModel();
                $current = $categoryModel->find($current['id']);
            }
        }
        
        if (empty($current) || !is_array($current)) {
            return base_url();
        }
        
        $slugs = [$current['slug']];
        $parentId = $current['parent_id'] ?? null;
        
        $db = \Config\Database::connect();
        $levels = 0;
        
        // Traverse up to a maximum of 3 levels to build path and prevent infinite loop
        while ($parentId && $parentId > 0 && $levels < 3) {
            $parent = $db->table('categories')->where('id', $parentId)->get()->getRowArray();
            if ($parent) {
                array_unshift($slugs, $parent['slug']);
                $parentId = $parent['parent_id'] ?? null;
                $levels++;
            } else {
                break;
            }
        }
        
        return base_url(implode('/', $slugs));
    }
}

if (!function_exists('get_clean_canonical_url')) {
    /**
     * Get clean Canonical / OG URL without index.php,
     * enforcing https://www.apsgifts.com for live domain.
     */
    function get_clean_canonical_url(?string $custom_url = null): string {
        if (!empty($custom_url)) {
            $url = $custom_url;
        } else {
            $request = \Config\Services::request();
            $path = ltrim($request->getPath(), '/');
            $url = base_url($path);
        }

        // 1. Remove index.php from URL
        $url = str_replace(['/index.php/', '/index.php', 'index.php/'], ['/', '', ''], $url);

        // 2. Parse URL components
        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) {
            return $url;
        }

        $host = $parts['host'];
        $scheme = $parts['scheme'] ?? 'https';

        // Check if running on local development environment
        $is_local = (
            $host === 'localhost' || 
            $host === '127.0.0.1' || 
            filter_var($host, FILTER_VALIDATE_IP) !== false ||
            str_contains($host, '.local')
        );

        if (!$is_local) {
            $scheme = 'https';
            if ($host === 'apsgifts.com' || $host === 'www.apsgifts.com') {
                $host = 'www.apsgifts.com';
            } elseif (!str_starts_with($host, 'www.')) {
                $host = 'www.' . $host;
            }
        }

        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        // Prevent double slashes or trailing slash on non-root paths
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return "{$scheme}://{$host}{$port}" . ($path[0] === '/' ? $path : '/' . $path) . $query;
    }
}


