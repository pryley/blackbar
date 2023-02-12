<?php defined('WPINC') || exit; ?>

<?php if (!$module->hasEntries()) : ?>
    <table>
        <tbody>
            <tr>
                <td><?= esc_html__('Please deactivate the Debug Bar Slow Actions plugin.', 'blackbar'); ?></td>
            </tr>
        </tbody>
    </table>
<?php else : ?>
    <form>
        <input type="text" id="glbb_hooks_callback" placeholder="<?= esc_attr__('Find callbacks containing', 'blackbar'); ?>">
        <input type="text" id="glbb_hooks_min_time" placeholder="<?= esc_attr__('Minimum total time', 'blackbar'); ?>">
        <select id="glbb_hooks_sort_by">
            <option value><?= esc_html__('Sort by total time', 'blackbar'); ?></option>
            <option value="order"><?= esc_html__('Sort by execution order', 'blackbar'); ?></option>
        </select>
    </form>
    <table class="glbb-grid">
        <thead>
            <tr>
                <th><?= esc_html__('Action or Filter (Slowest 50)', 'blackbar'); ?></th>
                <th><?= esc_html__('Callbacks', 'blackbar'); ?></th>
                <th><?= esc_html__('Calls', 'blackbar'); ?></th>
                <th><?= esc_html__('Per Call', 'blackbar'); ?></th>
                <th><?= esc_html__('Total', 'blackbar'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($module->entries() as $hook => $data) : ?>
                <tr class="glbb-row-collapsed" data-index="<?= esc_attr($data['index']); ?>" data-time="<?= esc_attr($data['total']); ?>">
                    <td><div class="glbb-row-toggle dashicons-before dashicons-arrow-right"><?= esc_html($hook); ?></div></td>
                    <td data-callbacks="<?= esc_attr($data['callbacks_count']); ?>"><?= esc_html($data['callbacks_count']); ?></td>
                    <td data-calls="<?= esc_attr($data['count']); ?>"><?= esc_html($data['count']); ?></td>
                    <td data-percall="<?= esc_attr($data['per_call']); ?>"><?= esc_html($data['per_call']); ?></td>
                    <td data-total="<?= esc_attr($data['total']); ?>"><?= esc_html($data['total_formatted']); ?></td>
                    <td class="glbb-row-details">
                        <ol>
                            <?php foreach ($data['callbacks'] as $priority => $callbacks) : ?>
                                <?php foreach ($callbacks as $callback) : ?>
                                    <li value="<?= esc_attr($priority); ?>"><?= esc_html($callback); ?></li>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </ol>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
