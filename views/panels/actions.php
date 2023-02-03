<?php defined('WPINC') || die; ?>

<table>
    <thead>
        <tr>
            <th>Action or Filter</th>
            <th style="text-align: right;">Callbacks</th>
            <th style="text-align: right;">Calls</th>
            <th style="text-align: right;">Per Call</th>
            <th style="text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($actions->entries() as $action => $data) : ?>
            <tr class="glbb-td-bg">
                <th><strong><?= $action; ?></strong></th>
                <th style="text-align: right;"><?= $data['callbacks_count']; ?></th>
                <th style="text-align: right;"><?= $data['count']; ?></th>
                <th style="text-align: right;"><?= sprintf('%.2fms', $data['total'] / $data['count']); ?></th>
                <th style="text-align: right;"><?= sprintf('%.2fms', $data['total']); ?></th>
            </tr>
            <tr>
                <td colspan="5">
                    <ol>
                        <?php foreach ($data['callbacks'] as $priority => $callbacks) : ?>
                            <?php foreach ($callbacks as $callback) : ?>
                                <li value="<?= $priority; ?>"><?= $callback; ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ol>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
