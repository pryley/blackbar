<?php defined( 'WPINC' ) || die; ?>

<table>
	<tbody>
	<?php foreach( $profiler->getMeasure() as $timer ) : ?>
		<tr>
			<td><?= $profiler->getNameString( $timer ); ?></td>
			<td class="glbb-number"><?= $profiler->getTimeString( $timer ); ?></td>
			<td><?= $profiler->getMemoryString( $timer ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
