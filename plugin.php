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
    remove_meta_box( 'dashboard_activity', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
}

function foogallery_playground_render_dashboard_widget() {
	$new_gallery_url       = admin_url( 'post-new.php?post_type=foogallery' );
	$list_galleries_url    = admin_url( 'edit.php?post_type=foogallery' );
	$demo_galleries_url     = '/';
	$demo_created           = (int) get_option( 'foogallery_playground_demo_content_created' ) > 0;
	$nonce                  = wp_create_nonce( 'foogallery_playground_create_demo' );
	$status_text            = '';
	$spinner_class          = $demo_created ? '' : ' is-active';
	$aria_busy              = $demo_created ? 'false' : 'true';
	$status_hidden_attr     = ' hidden';
	$link_hidden_attr       = $demo_created ? '' : ' hidden';
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

		.foogallery-playground-demo-status-wrap {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-top: 12px;
		}

		.foogallery-playground-demo-status-wrap .spinner {
			float: none;
			margin: 0;
		}

		.foogallery-playground-demo-status {
			margin: 0;
		}

		.foogallery-playground-demo-status.foogallery-playground-status-progress {
			color: #2271b1;
		}

		.foogallery-playground-demo-status.foogallery-playground-status-success {
			color: #0a9544;
		}

		.foogallery-playground-demo-status.foogallery-playground-status-error {
			color: #d63638;
		}

		.foogallery-playground-panel-content .foogallery-playground-panel-column-container {
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
				<a href="<?php echo esc_url( 'https://fooplugins.com/foogallery-wordpress-gallery-plugin/' ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Learn more about FooGallery', 'foogallery-demo' ); ?>
				</a>
			</p>
		</div>
		<div class="foogallery-playground-panel-column-container">
			<div class="foogallery-playground-panel-column">
				<div class="foogallery-playground-panel-column-content" id="foogallery-playground-demo-column">
					<h3><?php esc_html_e( 'Demo Galleries', 'foogallery-playground' ); ?></h3>
					<p><?php esc_html_e( 'Explore pre-built demo galleries in the backend.', 'foogallery-playground' ); ?></p>
						<div class="foogallery-playground-demo-status-wrap" id="foogallery-playground-demo-status-wrap" data-created="<?php echo $demo_created ? '1' : '0'; ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" aria-live="polite" aria-busy="<?php echo esc_attr( $aria_busy ); ?>">
							<span class="spinner<?php echo $spinner_class; ?>" id="foogallery-playground-demo-spinner"></span>
							<p class="description foogallery-playground-demo-status" id="foogallery-playground-demo-status" role="status"<?php echo $status_hidden_attr; ?>><?php echo esc_html( $status_text ); ?></p>
						</div>
						<a class="welcome-icon welcome-edit-page" id="foogallery-playground-demo-link"<?php echo $link_hidden_attr; ?> href="<?php echo esc_url( $list_galleries_url ); ?>">
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
	<script>
		jQuery(function($) {
			const $statusWrap = $('#foogallery-playground-demo-status-wrap');
			if (!$statusWrap.length) {
				return;
			}

			const $spinner = $('#foogallery-playground-demo-spinner');
			const $status = $('#foogallery-playground-demo-status');
			const $link = $('#foogallery-playground-demo-link');
			const nonce = $statusWrap.data('nonce');
			let created = Number($statusWrap.data('created')) === 1;

			function setStatus(message, type) {
				$status.removeClass('foogallery-playground-status-success foogallery-playground-status-error foogallery-playground-status-progress');
				if (!message) {
					$status.attr('hidden', 'hidden').text('');
					return;
				}

				$status.removeAttr('hidden');

				if (type === 'success') {
					$status.addClass('foogallery-playground-status-success');
				} else if (type === 'error') {
					$status.addClass('foogallery-playground-status-error');
				} else if (type === 'progress') {
					$status.addClass('foogallery-playground-status-progress');
				}

				$status.text(message);
			}

			function markCreated() {
				created = true;
				$statusWrap.data('created', 1);
				$statusWrap.attr('data-created', '1');
				setStatus('', '');
				$link.removeAttr('hidden');
			}

			function triggerDemoCreation() {
				if (!nonce) {
					$spinner.removeClass('is-active');
					$statusWrap.attr('aria-busy', 'false');
					return;
				}

				$statusWrap.attr('aria-busy', 'true');
				$spinner.addClass('is-active');
				setStatus('Creating demo content...', 'progress');

				$.post(ajaxurl, {
					action: 'foogallery_playground_create_demo_content',
					_ajax_nonce: nonce
				}).done(function(response) {
					if (response && response.success) {
						markCreated();
						return;
					}

					if (response && response.data && response.data.already_created) {
						markCreated();
						return;
					}

					const errorMessage = response && response.data && response.data.message ? response.data.message : '<?php echo esc_js( __( 'Unable to create demo content.', 'foogallery-playground' ) ); ?>';
					setStatus(errorMessage, 'error');
				}).fail(function() {
					setStatus('<?php echo esc_js( __( 'An unexpected error occurred. Please try again.', 'foogallery-playground' ) ); ?>', 'error');
				}).always(function() {
					$spinner.removeClass('is-active');
					$statusWrap.attr('aria-busy', 'false');
				});
			}

			if (!created) {
				triggerDemoCreation();
			} else {
				$statusWrap.attr('aria-busy', 'false');
				$spinner.removeClass('is-active');
				$link.removeAttr('hidden');
			}
		});
	</script>
	<?php
}

function foogallery_playground_maybe_create_demo_content() {
	$existing_timestamp = (int) get_option( 'foogallery_playground_demo_content_created' );

	if ( $existing_timestamp ) {
		return new WP_Error(
			'foogallery_playground_demo_already_created',
			__( 'Demo content has already been created.', 'foogallery-playground' ),
			[ 'created_at' => $existing_timestamp ]
		);
	}

	if ( ! function_exists( 'foogallery_create_demo_content' ) ) {
		return new WP_Error(
			'foogallery_playground_demo_unavailable',
			__( 'Demo content importer is unavailable.', 'foogallery-playground' )
		);
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error(
			'foogallery_playground_demo_capability',
			__( 'You do not have permission to create demo content.', 'foogallery-playground' )
		);
	}

	$created = foogallery_create_demo_content();

	if ( false === $created ) {
		return new WP_Error(
			'foogallery_playground_demo_failed',
			__( 'Demo content could not be created.', 'foogallery-playground' )
		);
	}

	$timestamp = current_time( 'timestamp' );

	update_option( 'foogallery_playground_demo_content_created', $timestamp );

	return [
		'created'    => $created,
		'created_at' => $timestamp,
	];
}

add_action( 'wp_ajax_foogallery_playground_create_demo_content', 'foogallery_playground_ajax_create_demo_content' );

function foogallery_playground_ajax_create_demo_content() {
	check_ajax_referer( 'foogallery_playground_create_demo' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error(
			[
				'message' => __( 'You are not allowed to create demo content.', 'foogallery-playground' ),
			],
			403
		);
	}

	$result = foogallery_playground_maybe_create_demo_content();

	if ( is_wp_error( $result ) ) {
		$data = [
			'message' => $result->get_error_message(),
		];

		if ( 'foogallery_playground_demo_already_created' === $result->get_error_code() ) {
			$error_data = $result->get_error_data();
			if ( isset( $error_data['created_at'] ) ) {
				$created_at      = (int) $error_data['created_at'];
				$data['message'] = sprintf(
					__( 'Demo content was already created on %s.', 'foogallery-playground' ),
					date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $created_at )
				);
				$data['created_at'] = $created_at;
			}
			$data['already_created'] = true;
		}

		wp_send_json_error( $data );
	}

	$created_at = isset( $result['created_at'] ) ? (int) $result['created_at'] : 0;

	wp_send_json_success(
		[
			'message'     => sprintf(
				__( 'Demo content created on %s.', 'foogallery-playground' ),
				date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $created_at )
			),
			'created_at' => $created_at,
		]
	);
}
