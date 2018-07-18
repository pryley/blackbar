<?php

defined( 'WPINC' ) || die;

/**
 * @return array
 * @see https://docs.gravityforms.com/gform_noconflict_scripts/
 */
add_filter( 'gform_noconflict_scripts', function( array $scripts ) {
	$scripts[] = 'blackbar';
	return $scripts;
});

/**
 * @return array
 * @see https://docs.gravityforms.com/gform_noconflict_styles/
 */
add_filter( 'gform_noconflict_styles', function( array $styles ) {
	$styles[] = 'blackbar';
	$styles[] = 'blackbar-syntax';
	return $styles;
});
