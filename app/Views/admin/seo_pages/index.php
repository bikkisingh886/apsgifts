<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-dark fw-bold mb-0"><i class="far fa-search me-2 text-cyan"></i> Static Pages SEO Manager</h4>
        </div>

        <div class="card-custom">
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Page Name</th>
                            <th>Page Key / URL</th>
                            <th>Meta Title</th>
                            <th>Meta Description</th>
                            <th style="width: 120px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pages)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No pages found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pages as $page): ?>
                                <tr>
                                    <td>
                                        <strong class="text-dark"><?= esc($page['page_name']) ?></strong>
                                    </td>
                                     <td>
                                         <code class="text-cyan"><?= esc($page['page_key']) ?></code>
                                         <?php 
                                            $urlMap = [
                                                'home' => '',
                                                'shop' => 'shop',
                                                'about' => 'about-us',
                                                'privacy' => 'privacy-policy',
                                                'terms' => 'terms-of-service',
                                                'cancellation' => 'cancellation-policy',
                                                'shipping' => 'shipping-policy',
                                                'contact' => 'contact-us',
                                                'faq' => 'faq'
                                            ];
                                            $path = $urlMap[$page['page_key']] ?? $page['page_key'];
                                         ?>
                                         <div class="small text-muted">URL: <a href="<?= base_url($path) ?>" target="_blank" class="text-muted text-decoration-underline"><?= base_url($path) ?></a></div>
                                     </td>
                                    <td>
                                        <div class="text-dark" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= esc($page['meta_title'] ?: 'Not set') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= esc($page['meta_desc'] ?: 'Not set') ?>
                                        </div>
                                    </td>
                                    <!-- <td class="small text-muted" style="line-height: 1.4;">
                                        <?php /*if (!empty($page['creator_name'])): ?>
                                            <div>Created by: <span class="text-cyan"><?= esc($page['creator_name']) ?></span></div>
                                        <?php endif; ?>
                                        <?php if ($page['updated_at']): ?>
                                            <div>Updated at: <?= date('d M Y, h:i A', strtotime($page['updated_at'])) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($page['updater_name'])): ?>
                                            <div class="mt-1">Updated by: <span class="text-cyan"><?= esc($page['updater_name']) ?></span></div>
                                        <?php endif;*/ ?>
                                    </td> -->
                                    <td style="text-align: right;">
                                        <a href="<?= base_url('admin/seo-pages/edit/' . $page['id']) ?>" class="btn btn-cyan btn-sm" title="Edit SEO Details"><i class="far fa-cog me-1"></i> Configure</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
