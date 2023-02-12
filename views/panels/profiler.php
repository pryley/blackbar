<?php defined('WPINC') || exit; ?>

<table class="glbb-grid">
    <thead>
        <tr>
            <th><?= esc_html__('Timer Name', 'blackbar'); ?></th>
            <th><?= esc_html__('Memory Used', 'blackbar'); ?></th>
            <th><?= esc_html__('Total Time', 'blackbar'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($module->entries() as $entry) : ?>
            <tr>
                <td><?= esc_html($entry['name']); ?></td>
                <td><?= (string) size_format($entry['memory'], 2); ?></td>
                <td><?= esc_html($entry['time']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
