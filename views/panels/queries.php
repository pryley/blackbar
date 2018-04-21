<?php defined( 'WPINC' ) || die; ?>

<?php if( !SAVEQUERIES ) : ?>
<table>
	<tbody>
		<tr>
			<td><a href="https://codex.wordpress.org/Debugging_in_WordPress#SAVEQUERIES">SAVEQUERIES</a> <?= __( 'must be enabled to view SQL queries', 'blackbar' ); ?>.</td>
		</tr>
	</tbody>
</table>
<?php else : ?>
<form method="get" class="glbb-queries-filter">
	<input type="text" name="glbb_query_filter" id="glbb_query_filter" placeholder="<?= __( 'Find queries containing', 'blackbar' ); ?>">
	<input type="text" name="glbb_query_min_time" id="glbb_query_min_time" placeholder="<?= __( 'Minimum Execution Time', 'blackbar' ); ?>">
</form>
<table class="glbb-queries-table">
	<tbody>
		<?php foreach( $queries as $query ) : ?>
		<tr>
			<td class="glbb-small"><?= $query['ms']; ?></td>
			<td><pre><code class="sql"><?= $query['sql']; ?></code></pre></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
