<?php defined('WPINC') || die; ?>

<?php if (!$actions->hasEntries()) : ?>
    <table>
        <tbody>
            <tr>
                <td><?= __('Please deactivate the Debug Bar Slow Actions plugin.', 'blackbar'); ?></td>
            </tr>
        </tbody>
    </table>
<?php else : ?>
    <form method="get" class="glbb-actions-filter">
        <input class="glbb-input" type="text" name="glbb_actions_callback" id="glbb_actions_callback" placeholder="<?= __('Find Callbacks Containing', 'blackbar'); ?>">
        <input class="glbb-input glbb-input-small" type="text" name="glbb_actions_min_time" id="glbb_actions_min_time" placeholder="<?= __('Minimum Total Time', 'blackbar'); ?>">
    </form>
    <table style="top:-10px;">
        <thead>
            <tr>
                <th>Action or Filter (Slowest 50)</th>
                <th style="text-align: right;">Callbacks</th>
                <th style="text-align: right;">Calls</th>
                <th style="text-align: right;">Per Call</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($actions->entries() as $action => $data) : ?>
                <tr class="glbb-row-collapsed">
                    <th><div class="glbb-row-toggle dashicons-before dashicons-arrow-right"><?= esc_html($action); ?></div></th>
                    <th><div data-callbacks="<?= esc_attr($data['callbacks_count']); ?>"><?= esc_html($data['callbacks_count']); ?></div></th>
                    <th><div data-calls="<?= esc_attr($data['count']); ?>"><?= esc_html($data['count']); ?></div></th>
                    <th><div data-percall="<?= esc_attr(sprintf('%.2f', $data['total'] / $data['count'])); ?>"><?= esc_html(sprintf('%.2f ms', $data['total'] / $data['count'])); ?></div></th>
                    <th><div data-total="<?= esc_attr($data['total']); ?>"><?= esc_html(sprintf('%.2f ms', $data['total'])); ?></div></th>
                </tr>
                <tr class="glbb-row">
                    <td colspan="5">
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
