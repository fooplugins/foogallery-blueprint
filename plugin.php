<?php
/**
 * Plugin Name:       FooGallery Demo Plugin
 * Description:       Sets everything up to get FooGallery working in Playground.
 * Version:           0.0.1
 * Requires at least: 6.5
 */

defined( 'ABSPATH' ) || exit;

// Make sure we do not need to optin.
define( 'WP_FS__DEV_MODE', true );
define( 'WP_FS__SKIP_EMAIL_ACTIVATION', true );

// add_action( 'init', function() { 
//     foogallery_create_demo_content(); 
// } );