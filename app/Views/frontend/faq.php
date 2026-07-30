<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
<main class="main">

    <!-- breadcrumb -->
    <div class="site-breadcrumb">
        <div class="site-breadcrumb-bg" style="background: url(<?= base_url('assets/img/breadcrumb/01.jpg') ?>)"></div>
        <div class="container">
            <div class="site-breadcrumb-wrap">
                <h4 class="breadcrumb-title"><?= esc($page_title ?? "Frequently Asked Questions") ?></h4>
                <ul class="breadcrumb-menu">
                    <li><a href="<?= base_url() ?>"><i class="far fa-home"></i> Home</a></li>
                    <li class="active">FAQ</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- breadcrumb end -->

    <!-- faq area -->
    <div class="faq-area py-100 bg-white">
        <div class="container">
            <div class="row">
                <!-- Left Sidebar: Category Filters -->
                <div class="col-lg-3 mb-4">
                    <div class="list-group shadow-sm border-0 rounded-3">
                        <a href="<?= base_url('faq') ?>" class="list-group-item list-group-item-action <?= ($selectedCategory === 'all') ? 'active' : '' ?>">
                            <i class="far fa-th-large me-2"></i> All Questions
                        </a>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $catName): ?>
                                <a href="<?= base_url('faq?category=' . urlencode($catName)) ?>" class="list-group-item list-group-item-action <?= ($selectedCategory === $catName) ? 'active' : '' ?>">
                                    <i class="far fa-angle-right me-2"></i> <?= esc($catName) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Side: FAQ Accordion List -->
                <div class="col-lg-9">
                    <?php if (empty($faqs)): ?>
                        <div class="alert alert-info py-4 text-center rounded-3">
                            <i class="far fa-info-circle me-2"></i> No questions found in this category.
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="faqAccordion">
                            <?php foreach ($faqs as $index => $faq): ?>
                                <div class="accordion-item mb-3 border rounded-3 shadow-sm bg-white overflow-hidden" style="border-color: #eef1f4 !important;">
                                    <h2 class="accordion-header" id="headingFaq<?= $faq['id'] ?>">
                                        <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?> fw-bold py-3 px-4 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq<?= $faq['id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapseFaq<?= $faq['id'] ?>" style="font-size: 1rem; color: #2d3748; box-shadow: none;">
                                            <span><i class="far fa-question me-2 text-coral" style="color: #e76f51;"></i></span> <?= esc($faq['question']) ?>
                                        </button>
                                    </h2>
                                    <div id="collapseFaq<?= $faq['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="headingFaq<?= $faq['id'] ?>" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body py-3 px-4 border-top text-secondary" style="line-height: 1.6; font-size: 0.95rem; background-color: #fafbfc;">
                                            <?= html_entity_decode(htmlspecialchars_decode($faq['answer'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- faq area end -->

</main>
<?= $this->endSection() ?>
