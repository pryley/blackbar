<?php defined( 'WPINC' ) || die; ?>

<table>
	<tbody>
		<?php foreach( $errors as $error ) : ?>
		<tr>
			<td><?= $error['name']; ?></td>
			<td><?= $error['message']; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
