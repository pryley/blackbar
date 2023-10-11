<?php defined('WPINC') || exit; ?>

<?php if (!$module->hasEntries()) : ?>
    <div>
        <table>
            <tbody>
                <tr>
                    <td><?= esc_html__('No entries found.', 'blackbar'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div>
        <table class="glbb-grid">
            <tbody>
                <?php foreach ($module->entries() as $entry) : ?>
                    <tr data-errname="<?= esc_attr($entry['errname']); ?>">
                        <td>
                            <span class="glbb-info glbb-<?= $entry['errname']; ?>"><?= esc_html($entry['name']); ?></span>
                        </td>
                        <td>
                            <pre><code class="language-text"><?= esc_html($entry['message']); ?></code></pre>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <form>
        <label><input type="checkbox" value="debug"> <?= esc_html__('Debug', 'blackbar'); ?></label>
        <label><input type="checkbox" value="info"> <?= esc_html__('Info', 'blackbar'); ?></label>
        <label><input type="checkbox" value="deprecated"> <?= esc_html__('Deprecated', 'blackbar'); ?></label>
        <label><input type="checkbox" value="notice"> <?= esc_html__('Notice', 'blackbar'); ?></label>
        <label><input type="checkbox" value="strict"> <?= esc_html__('Strict', 'blackbar'); ?></label>
        <label><input type="checkbox" value="warning"> <?= esc_html__('Warning', 'blackbar'); ?></label>
        <label><input type="checkbox" value="error"> <?= esc_html__('Error', 'blackbar'); ?></label>
        <label><input type="checkbox" value="unknown"> <?= esc_html__('Unknown', 'blackbar'); ?></label>
    </form>
<?php endif; ?>
