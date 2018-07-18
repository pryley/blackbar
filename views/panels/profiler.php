<?php defined( 'WPINC' ) || die; ?>

<table>
	<tbody>
		<?php if( empty( $profiler->getMeasure() )) : ?>
		<tr>
			<td><?= __( 'No entries found.', 'blackbar' ); ?></td>
		</tr>
		<?php else : ?>
		<?php foreach( $profiler->getMeasure() as $timer ) : ?>
		<tr>
			<td><?= $profiler->getNameString( $timer ); ?></td>
			<td class="glbb-medium"><?= $profiler->getTimeString( $timer ); ?></td>
			<td class="glbb-medium"><?= $profiler->getMemoryString( $timer ); ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
