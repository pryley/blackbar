<?php defined('WPINC') || die; ?>

<?php if ($templates->hasEntries()) : ?>
    <table class="glbb-templates-table">
        <tbody>
            <?php foreach ($templates->entries() as $index => $template) : ?>
                <tr>
                    <td class="glbb-small"><?= $index; ?></td>
                    <td><pre><code class="nohighlight"><?= esc_html($template); ?></code></pre></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
