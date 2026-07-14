<?php
$content = json_decode($section['content_json'] ?? '{}', true);
$html = $content['html'] ?? '';

if (!empty($html)):
?>
<section class="custom-text-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="custom-text-content text-dark">
                    <?= $html ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
