<?php defined('WPINC') || exit; ?>

<div id="glbb">
    <?php foreach ($modules as $module) : /* Ensure that the console entries are loaded last */ ?>
        <?php if (!$module->isVisible() || 'glbb-console' === $module->id()) continue; ?>
        <div id="<?= $module->id(); ?>" class="glbb-panel glbb-hidden">
            <?php $module->render(); ?>
        </div>
    <?php endforeach; ?>
    <?php foreach ($modules as $module) : /* Ensure that the console entries are loaded last */ ?>
        <?php if (!$module->isVisible() || 'glbb-console' !== $module->id()) continue; ?>
        <div id="<?= $module->id(); ?>" class="glbb-panel glbb-hidden">
            <?php $module->render(); ?>
        </div>
    <?php endforeach; ?>
    <div class="glbb-panel-links">
        <a href="#" class="dashicons-before glbb-toggle">
            <span class="screen-reader-text">
                <?= esc_html__('Toggle', 'blackbar'); ?>
            </span>
        </a>
        <?php foreach ($modules as $module) : ?>
            <?php if (!$module->isVisible()) continue; ?>
            <a href="#" data-panel="<?= esc_attr($module->id()); ?>" data-info="<?= esc_attr($module->info()); ?>" class="dashicons-before <?= $module->classes(); ?>">
                <span><?= $module->label(); ?></span>
            </a>
        <?php endforeach; ?>
        <div>
            <a href="#" class="dashicons-before glbb-close">
                <span class="screen-reader-text">
                    <?= esc_html__('Close', 'blackbar'); ?>
                </span>
            </a>
        </div>
    </div>
</div>
