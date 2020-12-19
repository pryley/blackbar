<?php defined('WPINC') || die; ?>

<table>
	<tbody>
		<?php if (empty($actions->getMeasure())) : ?>
		<tr>
			<td><?= __('No entries found.', 'blackbar'); ?></td>
		</tr>
		<?php else : ?>
		<?php foreach ($actions->getMeasure() as $hook => $flow) : ?>
		<tr>
			<td class="glbb-smalls"><?php 
			// glsr_log($flow); 
			?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

