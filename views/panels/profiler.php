<?php defined( 'WPINC' ) || die; ?>

<table>
	<tbody>
	<?php foreach( $profiler->getMeasure() as $timer ) : ?>
		<tr>
			<td><?= $profiler->getNameString( $timer ); ?></td>
			<td class="glbb-medium"><?= $profiler->getTimeString( $timer ); ?></td>
			<td class="glbb-medium"><?= $profiler->getMemoryString( $timer ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
