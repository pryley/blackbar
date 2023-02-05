<?php defined('WPINC') || die; ?>

<?php if (!defined('SAVEQUERIES') || !SAVEQUERIES) : ?>
    <table>
        <tbody>
            <tr>
                <td><a href="https://wordpress.org/documentation/article/debugging-in-wordpress/#savequeries">SAVEQUERIES</a> <?= _x('must be enabled to view SQL queries', 'SAVEQUERIES', 'blackbar'); ?>.</td>
            </tr>
        </tbody>
    </table>
<?php else : ?>
    <form method="get" class="glbb-queries-filter">
        <input class="glbb-input" type="text" name="glbb_query_filter" id="glbb_query_filter" placeholder="<?= __('Find Queries Containing', 'blackbar'); ?>">
        <input class="glbb-input glbb-input-small" type="text" name="glbb_query_min_time" id="glbb_query_min_time" placeholder="<?= __('Minimum Execution Time', 'blackbar'); ?>">
        <select class="glbb-input" name="glbb_query_sort" id="glbb_query_sort">
            <option value><?= __('Sort by execution order', 'blackbar'); ?></option>
            <option value="time"><?= __('Sort by execution time', 'blackbar'); ?></option>
        </select>
    </form>
    <table class="glbb-queries-table">
        <tbody>
            <?php foreach ($queries->entries() as $index => $entry) : ?>
                <tr data-index="<?= esc_attr($index); ?>" data-time="<?= esc_attr($entry['time']); ?>">
                    <td class="glbb-small"><?= esc_html($entry['time']); ?></td>
                    <td><pre><code class="language-sql"><?= esc_html($entry['sql']); ?></code></pre></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
