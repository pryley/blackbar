<?php defined( 'WPINC' ) || die; ?>

<table>
	<tbody>
		<?php if( empty( $entries )) : ?>
		<tr>
			<td><?= __( 'No entries found.', 'blackbar' ); ?></td>
		</tr>
		<?php else : ?>
		<?php foreach( $entries as $entry ) : ?>
		<tr>
			<td class="glbb-small"><?= $entry['name']; ?></td>
			<td><pre><?= $entry['message']; ?></pre></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
