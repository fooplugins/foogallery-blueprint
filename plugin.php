<?php
/**
 * Plugin Name:       FooGallery Playground Plugin
 * Description:       Sets everything up to get FooGallery working in Playground.
 * Version:           0.0.3
 * Requires at least: 6.5
 */

defined( 'ABSPATH' ) || exit;

if ( !is_admin() ) return;

// Add a basic settings page
add_action( 'admin_menu', function() {
	add_options_page(
		'FooGallery Playground Settings',
		'FooGallery Playground',
		'manage_options',
		'foogallery-playground-settings',
		'foogallery_playground_render_settings_page'
	);
} );

function foogallery_playground_render_settings_page() {
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
	echo '<h1>FooGallery Playground Settings</h1>';
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

add_action( 'admin_init', function() {
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
    add_action( 'wp_dashboard_setup', 'foogallery_playground_add_dashboard_widget' );
    add_action( 'welcome_panel', 'foogallery_playground_render_dashboard_widget' );
} );

function foogallery_playground_add_dashboard_widget() {
    //Remove the default dashboard widgets
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
}

function foogallery_playground_render_dashboard_widget() {
	$new_gallery_url   = admin_url( 'post-new.php?post_type=foogallery' );
	$list_galleries_url = admin_url( 'edit.php?post_type=foogallery' );
	$demo_galleries_url = '/';
	?>
    <style>
        .welcome-panel-close {
            display: none;
        }
		.foogallery-playground-panel-content {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
		}

		.foogallery-playground-panel-content .foogallery-playground-panel-header {
			box-sizing: border-box;
			margin: 0;
			width: 100%;
			padding: 24px 48px;
		}

		.foogallery-playground-panel-content .foogallery-playground-panel-header h2 {
			color: #7e4eff;
		}

		.foogallery-playground-panel-content .foogallery-playground-panel-column-container{
			box-sizing: border-box;
			width: 100%;
			clear: both;
			display: grid;
			z-index: 1;
			padding: 48px;
			grid-template-columns: repeat(3, 1fr);
			gap: 32px;
			align-self: flex-end;
			background: #fff;
		}
    </style>
	<div class="foogallery-playground-panel-content">
		<div class="foogallery-playground-panel-header">
			<h2><?php _e( 'FooGallery Playground' ); ?></h2>
			<p>
				<a href="<?php echo esc_url( 'https://fooplugins.com/foogallery-wordpress-gallery-plugin/' ); ?>" target="_blank">
					<?php esc_html_e( 'Learn more about FooGallery', 'foogallery-demo' ); ?>
				</a>
			</p>
		</div>
		<div class="foogallery-playground-panel-column-container">
			<div class="foogallery-playground-panel-column">
				<div class="foogallery-playground-panel-column-content">
					<h3><?php esc_html_e( 'Demo Galleries', 'foogallery-playground' ); ?></h3>
					<p><?php esc_html_e( 'Explore pre-built demo galleries in the backend.', 'foogallery-playground' ); ?></p>
					<a class="welcome-icon welcome-edit-page" href="<?php echo esc_url( $list_galleries_url ); ?>">
						<?php esc_html_e( 'Browse Demo Galleries', 'foogallery-playground' ); ?>
					</a>
				</div>
			</div>
			<div class="foogallery-playground-panel-column">
				<div class="foogallery-playground-panel-column-content">
					<h3><?php esc_html_e( 'View Galleries (Frontend)', 'foogallery-playground' ); ?></h3>
					<p><?php esc_html_e( 'See FooGallery in action on the frontend!', 'foogallery-playground' ); ?></p>
					<a class="welcome-icon welcome-view-site" href="<?php echo esc_url( $demo_galleries_url ); ?>">
						<?php esc_html_e( 'View Galleries', 'foogallery-playground' ); ?>
					</a>
				</div>
			</div>
			<div class="foogallery-playground-panel-column">
				<div class="foogallery-playground-panel-column-content">
					<h3><?php esc_html_e( 'Add New Gallery', 'foogallery-playground' ); ?></h3>
					<p><?php esc_html_e( 'Start building a new gallery.', 'foogallery-playground' ); ?></p>
					<a class="welcome-icon welcome-add-page" href="<?php echo esc_url( $new_gallery_url ); ?>">
						<?php esc_html_e( 'Create Gallery', 'foogallery-playground' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php
}

// add_action( 'init', function() { 
//     foogallery_create_demo_content(); 
// } );
