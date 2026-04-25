<?php
/**
 * Plugin uninstall handler.
 *
 * Fires when the user deletes the plugin from the WordPress admin. Removes
 * every database row this plugin owns so the site is left clean.
 *
 * @package HOAY
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'hoay_settings' );
delete_option( 'hoay_version' );

// Multisite: also clean per-site options on every blog in the network.
if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields'                 => 'ids',
			'number'                 => 0,
			'update_site_cache'      => false,
			'update_site_meta_cache' => false,
		)
	);

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		delete_option( 'hoay_settings' );
		delete_option( 'hoay_version' );
		restore_current_blog();
	}
}
