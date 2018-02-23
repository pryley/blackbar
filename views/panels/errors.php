<?php defined( 'WPINC' ) || die; ?>

<table>
	<tbody>
		<?php if( empty( $errors )) : ?>
		<tr>
			<td><?= __( 'No errors found.', 'blackbar' ); ?></td>
		</tr>
		<?php else : ?>
		<?php foreach( $errors as $error ) : ?>
		<tr>
			<td><?= $error['name']; ?></td>
			<td><?= $error['message']; ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
