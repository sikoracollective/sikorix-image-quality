<?php
/**
 * Admin settings page for Sikorix Image Upload Manager.
 *
 * @package SikorixImageUploadManager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared args for registered integer settings.
 *
 * @param string $sanitize_callback Sanitize callback name.
 * @param int    $default           Default value.
 * @return array<string, mixed>
 */
function sikorix_ium_get_setting_args( $sanitize_callback, $default ) {
	return array(
		'type'              => 'integer',
		'sanitize_callback' => $sanitize_callback,
		'default'           => $default,
		'capability'        => 'manage_options',
		'show_in_rest'      => false,
	);
}

/**
 * Register settings, sections, and fields.
 */
function sikorix_ium_register_admin_settings() {
	register_setting(
		'sikorix_ium_settings_group',
		SIKORIX_IUM_OPTION_JPEG_QUALITY,
		sikorix_ium_get_setting_args( 'sikorix_ium_sanitize_quality', SIKORIX_IUM_DEFAULT_QUALITY )
	);

	register_setting(
		'sikorix_ium_settings_group',
		SIKORIX_IUM_OPTION_JPEG_PRIORITY,
		sikorix_ium_get_setting_args( 'sikorix_ium_sanitize_filter_priority', SIKORIX_IUM_DEFAULT_FILTER_PRIORITY )
	);

	register_setting(
		'sikorix_ium_settings_group',
		SIKORIX_IUM_OPTION_EDITOR_QUALITY,
		sikorix_ium_get_setting_args( 'sikorix_ium_sanitize_quality', SIKORIX_IUM_DEFAULT_QUALITY )
	);

	register_setting(
		'sikorix_ium_settings_group',
		SIKORIX_IUM_OPTION_EDITOR_PRIORITY,
		sikorix_ium_get_setting_args( 'sikorix_ium_sanitize_filter_priority', SIKORIX_IUM_DEFAULT_FILTER_PRIORITY )
	);

	add_settings_section(
		'sikorix_ium_jpeg_section',
		__( 'JPEG Quality', 'sikorix-image-upload-manager' ),
		'sikorix_ium_render_jpeg_section_intro',
		'sikorix-image-upload-manager'
	);

	add_settings_section(
		'sikorix_ium_editor_section',
		__( 'WordPress Editor Quality', 'sikorix-image-upload-manager' ),
		'sikorix_ium_render_editor_section_intro',
		'sikorix-image-upload-manager'
	);

	add_settings_field(
		'sikorix_ium_jpeg_controls',
		'',
		'sikorix_ium_render_jpeg_controls',
		'sikorix-image-upload-manager',
		'sikorix_ium_jpeg_section'
	);

	add_settings_field(
		'sikorix_ium_editor_controls',
		'',
		'sikorix_ium_render_editor_controls',
		'sikorix-image-upload-manager',
		'sikorix_ium_editor_section'
	);
}
add_action( 'admin_init', 'sikorix_ium_register_admin_settings' );

/**
 * Add the plugin settings page under Settings.
 */
function sikorix_ium_add_settings_page() {
	add_options_page(
		__( 'Sikorix Image Upload Manager', 'sikorix-image-upload-manager' ),
		__( 'Sikorix Image Upload Manager', 'sikorix-image-upload-manager' ),
		'manage_options',
		'sikorix-image-upload-manager',
		'sikorix_ium_render_settings_page'
	);
}
add_action( 'admin_menu', 'sikorix_ium_add_settings_page' );

/**
 * Enqueue inline admin assets on the settings screen only.
 *
 * @param string $hook_suffix Current admin page hook suffix.
 */
function sikorix_ium_enqueue_admin_assets( $hook_suffix ) {
	if ( 'settings_page_sikorix-image-upload-manager' !== $hook_suffix ) {
		return;
	}

	wp_register_script( 'sikorix-ium-admin-settings', false, array(), SIKORIX_IUM_PLUGIN_VERSION, true );
	wp_enqueue_script( 'sikorix-ium-admin-settings' );
	wp_add_inline_script( 'sikorix-ium-admin-settings', sikorix_ium_get_admin_settings_script() );

	wp_register_style( 'sikorix-ium-admin-settings', false, array(), SIKORIX_IUM_PLUGIN_VERSION );
	wp_enqueue_style( 'sikorix-ium-admin-settings' );
	wp_add_inline_style( 'sikorix-ium-admin-settings', sikorix_ium_get_admin_settings_styles() );
}
add_action( 'admin_enqueue_scripts', 'sikorix_ium_enqueue_admin_assets' );

/**
 * Inline CSS for the settings page layout.
 *
 * @return string
 */
function sikorix_ium_get_admin_settings_styles() {
	return '
		.sikorix-ium-page-header {
			display: flex;
			align-items: baseline;
			gap: 12px;
			margin-bottom: 0;
		}
		.sikorix-ium-page-header__title,
		.wrap h1.sikorix-ium-page-header__title {
			font-weight: 700;
			font-size: 23px;
			line-height: 1.3;
			margin: 0;
			padding: 0;
		}
		.sikorix-ium-page-header__version {
			font-size: 14px;
			line-height: 1.4;
		}
		body.settings_page_sikorix-image-upload-manager .wrap > .notice {
			margin: 8px 0 12px;
		}
		body.settings_page_sikorix-image-upload-manager .wrap > form {
			margin-top: 12px;
		}
		.sikorix-ium-control-label {
			display: block;
			font-weight: 600;
			margin: 16px 0 8px;
		}
		.sikorix-ium-control-row {
			display: flex;
			align-items: center;
			flex-wrap: wrap;
			gap: 12px;
			margin-bottom: 6px;
		}
		.sikorix-ium-control-row input[type="range"] {
			width: min(320px, 100%);
			max-width: 100%;
		}
		.sikorix-ium-default-note {
			margin: 0 0 8px;
			color: #646970;
		}
		.sikorix-ium-file-types {
			color: #646970;
			font-size: 13px;
		}
		.sikorix-ium-developed-by {
			margin: 12px 12px 12px 0;
			font-size: 14px;
			line-height: 1.4;
		}
		#wpfooter .sikorix-ium-developed-by {
			margin: 12px 12px 12px 0;
			padding-left: 0;
		}
	';
}

/**
 * Inline JavaScript that keeps sliders and number fields in sync.
 *
 * @return string
 */
function sikorix_ium_get_admin_settings_script() {
	return <<<'JS'
( function () {
	'use strict';

	/**
	 * Keep every slider and matching number input in a control row in sync.
	 *
	 * @param {HTMLElement} row Control row containing range and number inputs.
	 */
	function bindSyncedRow( row ) {
		var slider = row.querySelector( 'input[type="range"]' );
		var input = row.querySelector( 'input[type="number"]' );

		if ( ! slider || ! input ) {
			return;
		}

		var min = parseInt( input.min, 10 );
		var max = parseInt( input.max, 10 );

		if ( Number.isNaN( min ) ) {
			min = parseInt( slider.min, 10 ) || 1;
		}
		if ( Number.isNaN( max ) ) {
			max = parseInt( slider.max, 10 ) || 100;
		}

		function clamp( value ) {
			var n = parseInt( value, 10 );
			if ( Number.isNaN( n ) ) {
				n = min;
			}
			return Math.min( max, Math.max( min, n ) );
		}

		function syncFromSlider() {
			input.value = String( clamp( slider.value ) );
		}

		function syncFromInput( force ) {
			if ( ! force && '' === input.value ) {
				return;
			}
			var value = clamp( input.value );
			input.value = String( value );
			slider.value = String( value );
		}

		slider.addEventListener( 'input', syncFromSlider );
		slider.addEventListener( 'change', syncFromSlider );
		input.addEventListener( 'input', function () {
			syncFromInput( false );
		} );
		input.addEventListener( 'change', function () {
			syncFromInput( true );
		} );
		input.addEventListener( 'blur', function () {
			syncFromInput( true );
		} );

		syncFromInput( true );
	}

	function init() {
		document.querySelectorAll( '.sikorix-ium-control-row' ).forEach( bindSyncedRow );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
JS;
}

/**
 * Short intro under the JPEG section heading.
 */
function sikorix_ium_render_jpeg_section_intro() {
	echo '<p class="description">';
	esc_html_e(
		'Controls the jpeg_quality filter used when WordPress generates JPEG images.',
		'sikorix-image-upload-manager'
	);
	echo '</p>';
}

/**
 * Short intro under the editor section heading.
 */
function sikorix_ium_render_editor_section_intro() {
	echo '<p class="description">';
	esc_html_e(
		'Controls the wp_editor_set_quality filter used when the WordPress image editor saves images.',
		'sikorix-image-upload-manager'
	);
	echo '</p>';
}

/**
 * Render a synced slider and number field row.
 *
 * @param string $label       Visible label before the slider.
 * @param string $slider_id   HTML id for the range input.
 * @param string $input_id    HTML id for the number input.
 * @param string $option_name Option name used as the number input name attribute.
 * @param int    $value       Current value.
 * @param int    $min         Minimum allowed value.
 * @param int    $max         Maximum allowed value.
 * @param string $trailing    Optional plain text shown after the number field.
 */
function sikorix_ium_render_slider_number_row( $label, $slider_id, $input_id, $option_name, $value, $min, $max, $trailing = '' ) {
	?>
	<label class="sikorix-ium-control-label" for="<?php echo esc_attr( $input_id ); ?>">
		<?php echo esc_html( $label ); ?>
	</label>
	<div class="sikorix-ium-control-row">
		<label for="<?php echo esc_attr( $slider_id ); ?>" class="screen-reader-text">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: control name, e.g. JPEG Quality. */
					__( '%s slider', 'sikorix-image-upload-manager' ),
					$label
				)
			);
			?>
		</label>
		<input
			type="range"
			id="<?php echo esc_attr( $slider_id ); ?>"
			min="<?php echo esc_attr( (string) $min ); ?>"
			max="<?php echo esc_attr( (string) $max ); ?>"
			step="1"
			value="<?php echo esc_attr( (string) $value ); ?>"
		/>
		<input
			type="number"
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $option_name ); ?>"
			min="<?php echo esc_attr( (string) $min ); ?>"
			max="<?php echo esc_attr( (string) $max ); ?>"
			step="1"
			value="<?php echo esc_attr( (string) $value ); ?>"
			class="small-text"
		/>
		<?php if ( '' !== $trailing ) : ?>
			<span class="sikorix-ium-file-types"><?php echo esc_html( $trailing ); ?></span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render JPEG quality and filter priority controls.
 */
function sikorix_ium_render_jpeg_controls() {
	$quality  = sikorix_ium_get_jpeg_quality();
	$priority = sikorix_ium_get_jpeg_filter_priority();

	$file_types = __( 'Affects JPEG (.jpg, .jpeg) images', 'sikorix-image-upload-manager' );

	sikorix_ium_render_slider_number_row(
		__( 'JPEG Quality', 'sikorix-image-upload-manager' ),
		'sikorix-ium-jpeg-quality-slider',
		'sikorix-ium-jpeg-quality-input',
		SIKORIX_IUM_OPTION_JPEG_QUALITY,
		$quality,
		SIKORIX_IUM_MIN_QUALITY,
		SIKORIX_IUM_MAX_QUALITY,
		$file_types
	);
	?>
	<p class="sikorix-ium-default-note">
		<?php
		printf(
			/* translators: %d: WordPress default JPEG quality. */
			esc_html__( 'WordPress default image quality: %d', 'sikorix-image-upload-manager' ),
			absint( SIKORIX_IUM_WP_DEFAULT_QUALITY )
		);
		?>
	</p>
	<?php
	sikorix_ium_render_slider_number_row(
		__( 'JPEG Filter Priority', 'sikorix-image-upload-manager' ),
		'sikorix-ium-jpeg-priority-slider',
		'sikorix-ium-jpeg-priority-input',
		SIKORIX_IUM_OPTION_JPEG_PRIORITY,
		$priority,
		SIKORIX_IUM_MIN_PRIORITY,
		SIKORIX_IUM_MAX_PRIORITY
	);
	?>
	<p class="sikorix-ium-default-note">
		<?php
		printf(
			/* translators: %d: WordPress default filter priority. */
			esc_html__( 'WordPress default filter priority: %d', 'sikorix-image-upload-manager' ),
			absint( SIKORIX_IUM_WP_DEFAULT_FILTER_PRIORITY )
		);
		?>
	</p>
	<?php
}

/**
 * Render WordPress editor quality and filter priority controls.
 */
function sikorix_ium_render_editor_controls() {
	$quality  = sikorix_ium_get_editor_quality();
	$priority = sikorix_ium_get_editor_filter_priority();

	$file_types = __( 'Affects JPEG, WebP, AVIF, and PNG images', 'sikorix-image-upload-manager' );

	sikorix_ium_render_slider_number_row(
		__( 'WordPress Editor Quality', 'sikorix-image-upload-manager' ),
		'sikorix-ium-editor-quality-slider',
		'sikorix-ium-editor-quality-input',
		SIKORIX_IUM_OPTION_EDITOR_QUALITY,
		$quality,
		SIKORIX_IUM_MIN_QUALITY,
		SIKORIX_IUM_MAX_QUALITY,
		$file_types
	);
	?>
	<p class="sikorix-ium-default-note">
		<?php
		printf(
			/* translators: %d: WordPress default editor quality. */
			esc_html__( 'WordPress default image quality: %d', 'sikorix-image-upload-manager' ),
			absint( SIKORIX_IUM_WP_DEFAULT_QUALITY )
		);
		?>
	</p>
	<?php
	sikorix_ium_render_slider_number_row(
		__( 'WordPress Editor Filter Priority', 'sikorix-image-upload-manager' ),
		'sikorix-ium-editor-priority-slider',
		'sikorix-ium-editor-priority-input',
		SIKORIX_IUM_OPTION_EDITOR_PRIORITY,
		$priority,
		SIKORIX_IUM_MIN_PRIORITY,
		SIKORIX_IUM_MAX_PRIORITY
	);
	?>
	<p class="sikorix-ium-default-note">
		<?php
		printf(
			/* translators: %d: WordPress default filter priority. */
			esc_html__( 'WordPress default filter priority: %d', 'sikorix-image-upload-manager' ),
			absint( SIKORIX_IUM_WP_DEFAULT_FILTER_PRIORITY )
		);
		?>
	</p>
	<?php
}

/**
 * Whether the current admin screen is this plugin's settings page.
 *
 * @return bool
 */
function sikorix_ium_is_settings_screen() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	$screen = get_current_screen();

	return ( $screen && 'settings_page_sikorix-image-upload-manager' === $screen->id );
}

/**
 * Print the developer credit on its own line above the WordPress footer text.
 */
function sikorix_ium_render_admin_footer_credit() {
	if ( ! sikorix_ium_is_settings_screen() ) {
		return;
	}
	?>
	<div class="sikorix-ium-developed-by">
		<?php esc_html_e( 'Developed by', 'sikorix-image-upload-manager' ); ?>
		<a
			href="<?php echo esc_url( 'https://sikorix.com/' ); ?>"
			target="_blank"
			rel="noopener noreferrer"
		>
			<?php esc_html_e( 'Sikorix', 'sikorix-image-upload-manager' ); ?>
		</a>
	</div>
	<?php
}
add_action( 'in_admin_footer', 'sikorix_ium_render_admin_footer_credit' );

/**
 * Render the full settings page.
 */
function sikorix_ium_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<div class="sikorix-ium-page-header">
			<h1 class="sikorix-ium-page-header__title"><?php esc_html_e( 'Sikorix Image Upload Manager', 'sikorix-image-upload-manager' ); ?></h1>
			<span class="sikorix-ium-page-header__version"><?php echo esc_html( SIKORIX_IUM_PLUGIN_VERSION ); ?></span>
		</div>
		<hr class="wp-header-end" />

		<form action="options.php" method="post">
			<?php
			settings_fields( 'sikorix_ium_settings_group' );
			do_settings_sections( 'sikorix-image-upload-manager' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
