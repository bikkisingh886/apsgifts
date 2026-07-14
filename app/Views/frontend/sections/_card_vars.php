<?php
// ─── Shared card rendering helper ────────────────────────────────────────────
// Usage: include this file with $product defined in scope.
// Returns variables: $imageUrl, $hasDiscount, $badgeText, $discountPrice,
//                    $originalPrice, $iconHtml, $badgeBg, $descText

$imageUrl      = !empty($product['image_path']) ? base_url($product['image_path']) : base_url('assets/img/product/default.png');
$originalPrice = (float)($product['price'] ?? 0);
$discountPrice = $originalPrice;
$hasDiscount   = false;
$badgeText     = '';

if (!empty($product['offer_value']) && $product['offer_value'] > 0) {
    $hasDiscount = true;
    if (($product['offer_type'] ?? '') === 'percent') {
        $discountPrice = $originalPrice * (1 - $product['offer_value'] / 100);
        $badgeText     = '-' . (int)$product['offer_value'] . '%';
    } else {
        $discountPrice = $originalPrice - $product['offer_value'];
        $badgeText     = '-₹' . (int)$product['offer_value'];
    }
}

// Emoji + pastel background based on product name keywords
$iconHtml = '🎁';
$badgeBg  = '#eef3ff';
$n = strtolower($product['name'] ?? '');
if (strpos($n, 'teddy') !== false || strpos($n, 'bear') !== false || strpos($n, 'soft toy') !== false) {
    $iconHtml = '🐻'; $badgeBg = '#fff3e0';
} elseif (strpos($n, 'cake') !== false || strpos($n, 'pastry') !== false) {
    $iconHtml = '🎂'; $badgeBg = '#ffeef2';
} elseif (strpos($n, 'rose') !== false || strpos($n, 'flower') !== false || strpos($n, 'bouquet') !== false || strpos($n, 'lily') !== false || strpos($n, 'gerbera') !== false) {
    $iconHtml = '🌸'; $badgeBg = '#fff0f5';
} elseif (strpos($n, 'chocolate') !== false || strpos($n, 'choco') !== false || strpos($n, 'truffle') !== false) {
    $iconHtml = '🍫'; $badgeBg = '#faf0e6';
} elseif (strpos($n, 'cushion') !== false || strpos($n, 'pillow') !== false) {
    $iconHtml = '🛋️'; $badgeBg = '#fff8dc';
} elseif (strpos($n, 'mug') !== false || strpos($n, 'cup') !== false) {
    $iconHtml = '☕'; $badgeBg = '#e6fafc';
} elseif (strpos($n, 'bottle') !== false) {
    $iconHtml = '🥤'; $badgeBg = '#e6fafc';
} elseif (strpos($n, 'plant') !== false || strpos($n, 'bamboo') !== false || strpos($n, 'succulent') !== false) {
    $iconHtml = '🌱'; $badgeBg = '#f0fff0';
} elseif (strpos($n, 'jewel') !== false || strpos($n, 'necklace') !== false || strpos($n, 'ring') !== false || strpos($n, 'bracelet') !== false) {
    $iconHtml = '💍'; $badgeBg = '#f5f0ff';
} elseif (strpos($n, 'photo') !== false || strpos($n, 'frame') !== false || strpos($n, 'canvas') !== false) {
    $iconHtml = '🖼️'; $badgeBg = '#f0f4ff';
} elseif (strpos($n, 'candle') !== false || strpos($n, 'diffuser') !== false) {
    $iconHtml = '🕯️'; $badgeBg = '#fff9e6';
} elseif (strpos($n, 'hamper') !== false || strpos($n, 'basket') !== false || strpos($n, 'combo') !== false || strpos($n, 'box') !== false) {
    $iconHtml = '🧺'; $badgeBg = '#fef9ec';
}

$descText = !empty($product['description'])
    ? strip_tags($product['description'])
    : 'Send this beautiful ' . ($product['name'] ?? '') . ' online.';
