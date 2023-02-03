<?php defined('WPINC') || die; ?>

<div id="glbb-debug-bar">
    <a href="#" class="glbb-toggle glbb-off">
        <?= __('Toggle', 'blackbar'); ?>
    </a>
    <?php foreach ($modules as $module) : ?>
        <?php if (!$module->isVisible()) continue; ?>
        <a href="javascript:Blackbar.switchPanel('<?= $module->id(); ?>')" class="<?= $module->id(); ?>">
            <?= $module->label(); ?>
        </a>
        <div id="<?= $module->id(); ?>" class="glbb-debug-panel">
            <?php $module->render(); ?>
        </div>
    <?php endforeach; ?>
    <a href="javascript:Blackbar.close()" class="glbb-close">
        <?= __('Close', 'blackbar'); ?>
    </a>
</div>
