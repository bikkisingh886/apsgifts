<?php
define('FCPATH', dirname(__DIR__) . '/public/');
chdir(FCPATH);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

// Bootstrap CI4 without running the output
$app = \CodeIgniter\Boot::bootWeb($paths);

// Instantiate Category Controller
$controller = new \App\Controllers\Category();
$controller->initController(
    \Config\Services::request(),
    \Config\Services::response(),
    \Config\Services::logger()
);

echo "Testing Category Controller Routing and Execution:\n\n";

// Test 1: Category "flowers" (should load flowers category)
try {
    echo "1. Testing flowers category... ";
    ob_start();
    $controller->index('flowers');
    ob_end_clean();
    echo "Success (loaded successfully)\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Test 2: Category "flowers" and City "delhi" (should set session and update name)
try {
    echo "2. Testing flowers/delhi... ";
    ob_start();
    $controller->index('flowers', 'delhi');
    ob_end_clean();
    echo "Success\n";
    echo "   Session City: " . session('selected_city_name') . " (ID: " . session('selected_city_id') . ")\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}

// Test 3: Bare city "delhi" (should throw 404 PageNotFoundException)
try {
    echo "3. Testing bare delhi... ";
    ob_start();
    $controller->index('delhi');
    ob_end_clean();
    echo "Failed (expected 404 PageNotFoundException, but it executed successfully)\n";
} catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
    echo "Success (Threw expected 404 PageNotFoundException: " . $e->getMessage() . ")\n";
} catch (\Exception $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
