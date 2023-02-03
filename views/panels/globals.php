<?php defined('WPINC') || die; ?>

<?php if ($globals->hasEntries()) : ?>
    <table class="glbb-globals-table">
        <tbody>
            <?php foreach ($globals->entries() as $entry) : ?>
                <tr>
                    <td class="glbb-small"><?= esc_html($entry['name']); ?></td>
                    <td><pre><code class="language-php"><?= esc_html($entry['value']); ?></code></pre></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
