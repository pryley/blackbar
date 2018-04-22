<?php defined( 'WPINC' ) || die; ?>

<div id="glbb-debug-bar">
	<a href="#" class="glbb-toggle glbb-off"><?= __( 'Toggle', 'blackbar' ); ?></a>
	<a href="javascript:Blackbar.switchPanel('glbb-globals')" class="glbb-globals"><?= __( 'Globals', 'blackbar' ); ?></a>
	<a href="javascript:Blackbar.switchPanel('glbb-profiler')" class="glbb-profiler"><?= $profilerLabel; ?></a>
	<a href="javascript:Blackbar.switchPanel('glbb-queries')" class="glbb-queries"><?= $queriesLabel; ?></a>
	<?php if( !is_admin() ) : ?>
	<a href="javascript:Blackbar.switchPanel('glbb-templates')" class="glbb-templates"><?= __( 'Templates', 'blackbar' ); ?></a>
	<?php endif; ?>
	<a href="javascript:Blackbar.switchPanel('glbb-errors')" class="glbb-errors"><?= $errorsLabel; ?></a>
	<a href="javascript:Blackbar.close()" class="glbb-close"><?= __( 'Close', 'blackbar' ); ?></a>
	<div id="glbb-globals" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/globals' ); ?>
	</div>
	<div id="glbb-profiler" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/profiler', array( 'profiler' => $profiler )); ?>
	</div>
	<div id="glbb-queries" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/queries', array( 'queries' => $queries )); ?>
	</div>
	<?php if( !is_admin() ) : ?>
	<div id="glbb-templates" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/templates', array( 'templates' => $templates )); ?>
	</div>
	<?php endif; ?>
	<div id="glbb-errors" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/errors', array( 'errors' => $errors )); ?>
	</div>
</div>
