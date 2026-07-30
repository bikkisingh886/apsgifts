<?php
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . '/../public/');
require __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
CodeIgniter\Boot::bootConsole($paths);

session();

use Config\Services;

$request = new \CodeIgniter\HTTP\IncomingRequest(
    new \Config\App(),
    new \CodeIgniter\HTTP\URI('http://localhost/gift/public/'),
    'php://input',
    new \CodeIgniter\HTTP\UserAgent()
);
Services::injectMock('request', $request);

$categoryController = new \App\Controllers\Category();
$categoryController->initController($request, Services::response(), Services::logger());

function test_url($segments, $expected404) {
    global $categoryController;
    echo "Testing URL path: /" . implode('/', $segments) . "\n";
    try {
        // Capture view output to prevent dumping html in CLI
        ob_start();
        $categoryController->index(...$segments);
        ob_end_clean();
        echo "-> Result: LOADED SUCCESSFULLY\n";
        if ($expected404) {
            echo "-> FAILURE: Expected a 404 but page loaded!\n\n";
        } else {
            echo "-> SUCCESS: Page loaded correctly as expected.\n\n";
        }
    } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
        if (ob_get_level() > 0) ob_end_clean();
        echo "-> Result: 404 ERROR (" . $e->getMessage() . ")\n";
        if ($expected404) {
            echo "-> SUCCESS: Correctly blocked with a 404.\n\n";
        } else {
            echo "-> FAILURE: Expected page to load but got 404!\n\n";
        }
    } catch (\Throwable $e) {
        if (ob_get_level() > 0) ob_end_clean();
        echo "-> Result: EXCEPTION (" . get_class($e) . ": " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . ")\n";
        echo "-> FAILURE: Unexpected error occurred.\n\n";
    }
}

echo "==================================================\n";
echo "VERIFYING PATH RELATIONSHIP VALIDATION & 404 STATUS\n";
echo "==================================================\n";

// 1. /anniversary/delhi/cakes (Valid relationship chain: Anniversary -> Delhi -> Cakes)
// Should load successfully
test_url(['anniversary', 'delhi', 'cakes'], false);

// 2. /chocolates/personalised-gifts (Invalid relationship: Chocolates has no link to Personalised Gifts)
// Should throw 404
test_url(['chocolates', 'personalised-gifts'], true);

// 3. /gifts/for-her (Valid relationship: Gifts -> For Her)
// Should load successfully
test_url(['gifts', 'for-her'], false);

// 4. /anniversary/personalised-gifts/delhi (Valid: Anniversary -> Personalised Gifts -> Delhi)
test_url(['anniversary', 'personalised-gifts', 'delhi'], false);

// 5. /personalised-gifts/anniversary/delhi (Invalid: Anniversary does not have parent Personalised Gifts)
// Should throw 404
test_url(['personalised-gifts', 'anniversary', 'delhi'], true);

echo "==================================================\n";
