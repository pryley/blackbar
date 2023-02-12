<?php defined('WPINC') || exit; ?>

<table class="glbb-grid">
    <tbody>
        <?php foreach ($module->entries() as $index => $template) : ?>
            <tr>
                <td>
                    <ol>
                        <li value="<?= esc_attr($index + 1); ?>"><?= esc_html($template); ?></li>
                    </ol>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
