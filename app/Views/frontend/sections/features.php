<?php
$features = [];
if (!empty($section['content_json'])) {
    $features = json_decode($section['content_json'], true);
}
?>
<!-- feature area -->
<div class="feature-area py-80">
    <div class="container wow fadeInUp" data-wow-delay=".25s">
        <div class="feature-wrap">
            <div class="row g-0 mobile-horizontal-scroll">
                <?php foreach ($features as $feat): ?>
                    <?php
                    // Translate icons from fontawesome categories
                    $iconClass = 'fal fa-truck';
                    if (strpos($feat['icon'], 'delivery') !== false || strpos($feat['icon'], 'truck') !== false) $iconClass = 'fal fa-truck';
                    elseif (strpos($feat['icon'], 'sync') !== false || strpos($feat['icon'], 'refund') !== false) $iconClass = 'fal fa-sync';
                    elseif (strpos($feat['icon'], 'wallet') !== false || strpos($feat['icon'], 'credit') !== false) $iconClass = 'fal fa-wallet';
                    elseif (strpos($feat['icon'], 'support') !== false || strpos($feat['icon'], 'headset') !== false) $iconClass = 'fal fa-headset';
                    else $iconClass = esc($feat['icon']);
                    ?>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="<?= $iconClass ?>"></i>
                            </div>
                            <div class="feature-content">
                                <h4><?= esc($feat['title']) ?></h4>
                                <p><?= esc($feat['description']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!-- feature area end -->
