<?php defined('WPINC') || die; ?>

<table>
    <tbody>
        <?php if (!$console->hasEntries()) : ?>
            <tr>
                <td><?= __('No entries found.', 'blackbar'); ?></td>
            </tr>
        <?php else : ?>
            <?php foreach ($console->entries() as $entry) : ?>
                <tr>
                    <td class="glbb-small">
                        <span class="glbb-info glbb-<?= $entry['errname']; ?>">
                            <?= esc_html($entry['name']); ?>
                        </span>
                    </td>
                    <td><?= esc_html($entry['message']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
