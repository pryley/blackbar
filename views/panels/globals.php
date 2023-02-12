<?php defined('WPINC') || exit; ?>

<?php if ($module->hasEntries()) : ?>
    <table class="glbb-grid">
        <tbody>
            <?php foreach ($module->entries() as $entry) : ?>
                <tr class="glbb-row-collapsed">
                    <td><div class="glbb-row-toggle dashicons-before dashicons-arrow-right"><?= esc_html($entry['name']); ?></div></td>
                    <td class="glbb-row-details">
                        <pre><code><?= esc_html($entry['value']); ?></code></pre></td>
                    </tr>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
