<?= $this->extend('frontend/layout') ?>

<?= $this->section('content') ?>
        <!-- Dynamic Homepage Sections -->
        <?php foreach ($sections as $section): ?>
            <?= view('frontend/sections/' . $section['section_key'], ['section' => $section]) ?>
        <?php endforeach; ?>

<?= $this->endSection() ?>