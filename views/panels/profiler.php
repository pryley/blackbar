<?php defined('WPINC') || die; ?>

<table>
    <tbody>
        <?php if (!$profiler->hasEntries()) : ?>
            <tr>
                <td><?= __('No entries found.', 'blackbar'); ?></td>
            </tr>
        <?php else : ?>
            <?php foreach ($profiler->entries() as $entry) : ?>
                <tr>
                    <td><?= esc_html($entry['name']); ?></td>
                    <td class="glbb-medium"><?= esc_html($entry['time']); ?></td>
                    <td class="glbb-medium"><?= esc_html($entry['memory']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
