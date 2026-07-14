<?php
// Compute variables using the shared helper
include __DIR__ . '/_card_vars.php';
?>
<div class="custom-card-item">

    <!-- Image -->
    <div class="card-img-wrap">
        <?php if ($hasDiscount): ?>
            <span class="card-discount-badge"><?= $badgeText ?></span>
        <?php endif; ?>
        <a href="<?= base_url('product/' . $product['slug']) ?>" class="d-block">
            <img src="<?= $imageUrl ?>" alt="<?= esc($product['name']) ?>">
        </a>
    </div>

    <!-- Body -->
    <div class="card-body-content">
        <!-- Emoji category badge sits here relative to the content start line -->
        <div class="card-icon-badge" style="background:<?= $badgeBg ?>;"><?= $iconHtml ?></div>

        <h4 class="card-title-text">
            <a href="<?= base_url('product/' . $product['slug']) ?>"><?= esc($product['name']) ?></a>
        </h4>
        <p class="card-desc-text"><?= esc(mb_strimwidth($descText, 0, 75, '...')) ?></p>

        <div class="card-footer-info">
            <!-- Price -->
            <div class="card-price-value">
                <?php if ($hasDiscount): ?>
                    <del>₹<?= number_format($originalPrice, 0) ?></del>
                    <span>₹<?= number_format($discountPrice, 0) ?></span>
                <?php else: ?>
                    <span>₹<?= number_format($originalPrice, 0) ?></span>
                <?php endif; ?>
            </div>

            <!-- Add to cart button (arrow) -->
            <form action="<?= base_url('cart/add') ?>" method="post" class="m-0">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="card-action-arrow" title="Add To Cart">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            </form>
        </div>
    </div>

</div>
