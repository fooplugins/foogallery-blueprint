<?php
/**
 * Plugin Name:       FooGallery Demo Plugin
 * Description:       Sets everything up to get FooGallery working in Playground.
 * Version:           0.0.1
 * Requires at least: 6.5
 */

defined( 'ABSPATH' ) || exit;

// Make sure we do not need to optin.
define( 'FOOPLUGINS_FREEMIUS_ANONYMOUS', true );
define( 'WP_FS__DEV_MODE', true );

if ( !is_admin() ) return;

// Add a basic settings page
add_action( 'admin_menu', function() {
	add_options_page(
		'FooGallery Demo Settings',
		'FooGallery Demo',
		'manage_options',
		'foogallery-demo-settings',
		'foogallery_demo_render_settings_page'
	);
} );

function foogallery_demo_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$constants = [
		'FOOPLUGINS_FREEMIUS_ANONYMOUS',
		'WP_DEBUG',
		'WP_FS__DEV_MODE',
		'WP_PLAYGROUND',
	];

	echo '<div class="wrap">';
	echo '<h1>FooGallery Demo Settings</h1>';
	echo '<table class="widefat striped">';
	echo '<thead><tr><th>Constant</th><th>Value</th></tr></thead><tbody>';

	foreach ( $constants as $constant ) {
		$value = defined( $constant ) ? constant( $constant ) : '<em>Not defined</em>';
		if ( is_bool( $value ) ) {
			$value = $value ? 'true' : 'false';
		}
		echo '<tr><td>' . esc_html( $constant ) . '</td><td><code>' . esc_html( (string) $value ) . '</code></td></tr>';
	}

	echo '</tbody></table>';
	echo '</div>';
}

// add_action( 'init', function() { 
//     foogallery_create_demo_content(); 
// } );
