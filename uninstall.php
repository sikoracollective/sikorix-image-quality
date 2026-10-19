<?php
/**
 * Remove plugin options on uninstall.
 *
 * @package SikorixImageUploadManager
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$sikorix_ium_option_keys = array(
	'sikorix_jpeg_quality',
	'sikorix_jpeg_filter_priority',
	'sikorix_editor_quality',
	'sikorix_editor_filter_priority',
);

foreach ( $sikorix_ium_option_keys as $sikorix_ium_option_key ) {
	delete_option( $sikorix_ium_option_key );
}
