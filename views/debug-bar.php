<?php defined( 'WPINC' ) || die; ?>

<div id="glbb-debug-bar">
	<a href="#" class="glbb-toggle glbb-off"><?= __( 'Toggle', 'blackbar' ); ?></a>

	<a href="javascript:Blackbar.switchPanel('glbb-console')" class="glbb-console"><?= $consoleLabel; ?></a>
	<div id="glbb-console" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/console', array( 'entries' => $consoleEntries )); ?>
	</div>

	<a href="javascript:Blackbar.switchPanel('glbb-profiler')" class="glbb-profiler"><?= $profilerLabel; ?></a>
	<div id="glbb-profiler" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/profiler', array( 'profiler' => $profiler )); ?>
	</div>

	<a href="javascript:Blackbar.switchPanel('glbb-queries')" class="glbb-queries"><?= $queriesLabel; ?></a>
	<div id="glbb-queries" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/queries', array( 'queries' => $queries )); ?>
	</div>

	<?php if( !is_admin() ) : ?>
	<a href="javascript:Blackbar.switchPanel('glbb-templates')" class="glbb-templates"><?= __( 'Templates', 'blackbar' ); ?></a>
	<div id="glbb-templates" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/templates', array( 'templates' => $templates )); ?>
	</div>
	<?php endif; ?>

	<a href="javascript:Blackbar.switchPanel('glbb-globals')" class="glbb-globals"><?= __( 'Globals', 'blackbar' ); ?></a>
	<div id="glbb-globals" class="glbb-debug-panel">
		<?php $blackbar->render( 'panels/globals' ); ?>
	</div>

	<a href="javascript:Blackbar.close()" class="glbb-close"><?= __( 'Close', 'blackbar' ); ?></a>
</div>
