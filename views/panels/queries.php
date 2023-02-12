<?php defined('WPINC') || exit; ?>

<?php if (!defined('SAVEQUERIES') || !SAVEQUERIES) : ?>
    <table>
        <tbody>
            <tr>
                <td><a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#savequeries" target="_blank">SAVEQUERIES</a> <?= esc_html_x('must be enabled to view SQL queries', 'SAVEQUERIES', 'blackbar'); ?>.</td>
            </tr>
        </tbody>
    </table>
<?php else : ?>
    <form>
        <input type="text" id="glbb_queries_sql" placeholder="<?= esc_attr__('Find SQL containing', 'blackbar'); ?>">
        <input type="text" id="glbb_queries_min_time" placeholder="<?= esc_attr__('Minimum execution time', 'blackbar'); ?>">
        <select id="glbb_queries_sort_by">
            <option value><?= esc_html__('Sort by execution time', 'blackbar'); ?></option>
            <option value="order"><?= esc_html__('Sort by execution order', 'blackbar'); ?></option>
        </select>
    </form>
    <table class="glbb-grid">
        <tbody>
            <?php foreach ($module->entries() as $entry) : ?>
                <tr class="glbb-row-collapsed" data-index="<?= esc_attr($entry['index']); ?>" data-time="<?= esc_attr($entry['time']); ?>">
                    <td data-time="<?= esc_attr($entry['time_formatted']); ?>"><div class="glbb-row-toggle dashicons-before dashicons-arrow-right"><?= esc_html($entry['time_formatted']); ?></div></td>
                    <td data-sql><pre><code class="language-sql"><?= esc_html($entry['sql']); ?></code></pre></td>
                    <td class="glbb-row-details">
                        <ol>
                            <?php foreach ($entry['trace'] as $index => $line) : ?>
                                <li value="<?= esc_attr($index + 1); ?>"><?= esc_html($line); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
