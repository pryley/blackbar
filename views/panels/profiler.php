<?php defined('WPINC') || die; ?>

<table>
    <?php if (!$profiler->hasEntries()) : ?>
        <tbody>
            <tr>
                <td><?= __('No entries found.', 'blackbar'); ?></td>
            </tr>
        </tbody>
    <?php else : ?>
        <thead>
            <tr>
                <th>Message</th>
                <th style="text-align: right;">Time</th>
                <th style="text-align: right;">Memory</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($profiler->entries() as $entry) : ?>
                <tr>
                    <td><?= esc_html($entry['name']); ?></td>
                    <td style="text-align: right;"><?= esc_html(sprintf('%.2f', $entry['time'])); ?> ms</td>
                    <td style="text-align: right;"><?= esc_html($entry['memory']); ?> KB</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    <?php endif; ?>
</table>
