<?php
$title = 'Frequently Asked Questions';
$subtitle = 'FAQ';
$faqs = [
    [
        'question' => 'Can I get my gift delivered today?',
        'answer' => 'Yes, we offer same-day express delivery for cake and flower categories in supported cities.'
    ],
    [
        'question' => 'Can I add a custom message or note with my gift?',
        'answer' => 'Absolutely! During checkout, you can add a personalized message that will be printed on a card.'
    ]
];

if (!empty($section['content_json'])) {
    $content = json_decode($section['content_json'], true);
    if (isset($content[0])) {
        $faqs = $content;
    } else {
        $title = $content['title'] ?? $title;
        $subtitle = $content['subtitle'] ?? $subtitle;
        $faqs = $content['faqs'] ?? $faqs;
    }
}
?>
<!-- FAQ Section -->
<div class="faq-area py-70 pb-5 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto text-center">
                <div class="site-heading text-center">
                    <span class="site-title-tagline justify-content-center" style="color: #e76f51;">
                        <i class="fas fa-question-circle me-2"></i> <?= esc($subtitle) ?>
                    </span>
                    <h2 class="site-title" style="font-size: 2.3rem; font-weight: 800; color: #2d3748;"><?= esc($title) ?></h2>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <!-- Left Column: Even Indexes -->
            <div class="col-lg-6">
                <div class="accordion" id="accordionFaqHomeLeft">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <?php if ($index % 2 !== 0) continue; ?>
                        <div class="accordion-item mb-3 border rounded-3 shadow-sm bg-white" style="overflow: hidden; border-color: #eef1f4 !important;">
                            <h2 class="accordion-header" id="headingHomeFaq<?= $index ?>">
                                <button class="accordion-button collapsed fw-bold py-3 px-4 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHomeFaq<?= $index ?>" aria-expanded="false" aria-controls="collapseHomeFaq<?= $index ?>" style="font-size: 1rem; color: #2d3748; box-shadow: none;">
                                    <?= esc($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="collapseHomeFaq<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="headingHomeFaq<?= $index ?>" data-bs-parent="#accordionFaqHomeLeft">
                                <div class="accordion-body py-3 px-4 border-top text-secondary" style="line-height: 1.6; font-size: 0.95rem; background-color: #fafbfc;">
                                    <?= esc($faq['answer']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Right Column: Odd Indexes -->
            <div class="col-lg-6">
                <div class="accordion" id="accordionFaqHomeRight">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <?php if ($index % 2 === 0) continue; ?>
                        <div class="accordion-item mb-3 border rounded-3 shadow-sm bg-white" style="overflow: hidden; border-color: #eef1f4 !important;">
                            <h2 class="accordion-header" id="headingHomeFaq<?= $index ?>">
                                <button class="accordion-button collapsed fw-bold py-3 px-4 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHomeFaq<?= $index ?>" aria-expanded="false" aria-controls="collapseHomeFaq<?= $index ?>" style="font-size: 1rem; color: #2d3748; box-shadow: none;">
                                    <?= esc($faq['question']) ?>
                                </button>
                            </h2>
                            <div id="collapseHomeFaq<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="headingHomeFaq<?= $index ?>" data-bs-parent="#accordionFaqHomeRight">
                                <div class="accordion-body py-3 px-4 border-top text-secondary" style="line-height: 1.6; font-size: 0.95rem; background-color: #fafbfc;">
                                    <?= esc($faq['answer']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FAQ Section End -->

<style>
/* Custom Premium FAQ style overlays */
.accordion-button:not(.collapsed) {
    background-color: #fdf0eb !important;
    color: #e76f51 !important;
}
.accordion-button::after {
    transition: transform 0.2s;
}
.accordion-button:focus {
    box-shadow: none !important;
    border-color: rgba(231, 111, 81, 0.2) !important;
}
</style>
