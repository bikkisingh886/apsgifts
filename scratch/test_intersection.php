<?php
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . '/../public/');
require __DIR__ . '/../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';
CodeIgniter\Boot::bootConsole($paths);

session();

$db = \Config\Database::connect();

$slugSegments = ['gifts', 'for-her'];

$builder = $db->table('products products')
             ->select('products.id, products.name, products.slug, products.price')
             ->join('offers o', 'o.id = products.offer_id AND o.is_active = 1', 'left')
             ->join('product_images pi', 'pi.product_id = products.id AND pi.is_primary = 1', 'left')
             ->where('products.is_active', 1)
             ->where('products.hide_from_frontend', 0)
             ->groupBy('products.id');

foreach ($slugSegments as $index => $slug) {
    $alias_pc = "pc_" . $index;
    $alias_c = "c_" . $index;
    $builder->join("product_categories $alias_pc", "$alias_pc.product_id = products.id")
            ->join("categories $alias_c", "$alias_c.id = $alias_pc.category_id")
            ->where("$alias_c.slug", $slug);
}

$products = $builder->get()->getResultArray();
echo "Products count for ['gifts', 'for-her'] with direct matching: " . count($products) . "\n";
echo "Product list: " . json_encode(array_column($products, 'name')) . "\n";
