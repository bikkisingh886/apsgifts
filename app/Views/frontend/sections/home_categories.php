<?php
$db = \Config\Database::connect();
$categoryIds = [];
$title = 'Shop by Category';
$subtitle = 'Featured Categories';

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    $categoryIds = $content['category_ids'] ?? [];
    $title = $content['title'] ?? $title;
    $subtitle = $content['subtitle'] ?? $subtitle;
}

$categories = [];
if (!empty($categoryIds)) {
    $categories = $db->table('categories c')
        ->select('c.id, c.name, c.slug, c.image_path')
        ->whereIn('c.id', $categoryIds)
        ->where('c.is_active', 1)
        ->get()
        ->getResultArray();
}
?>

<!-- Home Categories Grid (App-like Shortcuts) -->
<div class="home-categories-section pt-4 bg-white">
    <div class="container">
        <div class="d-flex justify-content-start align-items-center gap-4 py-2 px-1 overflow-auto hide-scrollbar scroll-pan">
            <?php if (empty($categories)): ?>
                <div class="w-100 text-center text-muted py-3">No categories selected. Go to Admin Homepage Manager to select categories.</div>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                    <?php 
                    $imageUrl = $cat['image_path'] ? base_url($cat['image_path']) : base_url('assets/img/product/default.png');
                    ?>
                    <a href="<?= get_category_url($cat) ?>" class="category-item-shortcut text-center text-decoration-none d-flex flex-column align-items-center">
                        <div class="category-img-box mb-2 shadow-sm d-flex align-items-center justify-content-center bg-white">
                            <img src="<?= $imageUrl ?>" alt="<?= esc($cat['name']) ?>" class="img-fluid rounded-3">
                        </div>
                        <span class="category-title-label text-dark fw-bold" style="font-size: 0.82rem; max-width: 120px; display: block; word-wrap: break-word; line-height: 1.2;"><?= esc($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* App-like Category Shortcuts Section */
.category-item-shortcut {
    flex: 0 0 auto;
    width: 150px;
    transition: transform 0.2s ease-in-out;
}
.category-item-shortcut:hover {
    transform: translateY(-4px);
}
.category-img-box {
    width: 150px;
    height: 150px;
    border-radius: 20px;
    border: 1px solid #f0f0f0;
    overflow: hidden;
    background-color: #ffffff;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.category-item-shortcut:hover .category-img-box {
    box-shadow: 0 8px 20px rgba(231, 111, 81, 0.15) !important;
    border-color: #e76f51;
}
.category-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    padding: 2px;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
.scroll-pan {
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
</style>
