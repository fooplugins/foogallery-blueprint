<?php
/**
 * Plugin Name:       FooGallery Demo Plugin
 * Description:       Sets everything up to get FooGallery working in Playground.
 * Version:           0.0.2
 * Requires at least: 6.5
 */

defined( 'ABSPATH' ) || exit;

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

//Hides the default Welcome Panel
remove_action( 'welcome_panel', 'wp_welcome_panel' );

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

add_action( 'init', 'foogallery_demo_add_dashboard_widget' );
add_action( 'welcome_panel', 'foogallery_demo_render_dashboard_widget' );

function foogallery_demo_add_dashboard_widget() {
    //Remove the default dashboard widgets
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_activity', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );

	// wp_add_dashboard_widget(
	// 	'foogallery_playground_dashboard_widget',
	// 	__( 'FooGallery Playground', 'foogallery-demo' ),
	// 	'foogallery_demo_render_dashboard_widget'
	// );
}

function foogallery_demo_render_dashboard_widget() {
	$new_gallery_url   = admin_url( 'post-new.php?post_type=foogallery' );
	$view_galleries_url = admin_url( 'edit.php?post_type=foogallery' );
	$demo_galleries_url = '/';
	?>
	<div class="welcome-panel-content">
		<p class="about-description">
			<a href="<?php echo esc_url( 'https://fooplugins.com/foogallery-wordpress-gallery-plugin/' ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Learn more about FooGallery', 'foogallery-demo' ); ?>
			</a>
		</p>
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column">
				<h3><?php esc_html_e( 'Demo Galleries', 'foogallery-demo' ); ?></h3>
				<p><?php esc_html_e( 'Explore pre-built galleries to see FooGallery in action.', 'foogallery-demo' ); ?></p>
				<a class="welcome-icon welcome-view-site" href="<?php echo esc_url( $demo_galleries_url ); ?>">
					<?php esc_html_e( 'Browse Demo Galleries', 'foogallery-demo' ); ?>
				</a>
			</div>
			<div class="welcome-panel-column">
				<h3><?php esc_html_e( 'Add New Gallery', 'foogallery-demo' ); ?></h3>
				<p><?php esc_html_e( 'Start building a new FooGallery.', 'foogallery-demo' ); ?></p>
				<a class="welcome-icon welcome-add-page" href="<?php echo esc_url( $new_gallery_url ); ?>">
					<?php esc_html_e( 'Create Gallery', 'foogallery-demo' ); ?>
				</a>
			</div>
			<div class="welcome-panel-column">
				<h3><?php esc_html_e( 'View Galleries', 'foogallery-demo' ); ?></h3>
				<p><?php esc_html_e( 'Manage all of your existing FooGalleries.', 'foogallery-demo' ); ?></p>
				<a class="welcome-icon welcome-edit-page" href="<?php echo esc_url( $view_galleries_url ); ?>">
					<?php esc_html_e( 'Manage Galleries', 'foogallery-demo' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
}

// add_action( 'init', function() { 
//     foogallery_create_demo_content(); 
// } );
