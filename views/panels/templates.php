<?php defined('WPINC') || die; ?>

<?php if ($templates->hasEntries()) : ?>
    <table class="glbb-templates-table">
        <tbody>
            <tr>
                <td>
                    <ol>
                        <?php foreach ($templates->entries() as $index => $template) : ?>
                            <li><?= esc_html($template); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>
