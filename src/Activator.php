<?php
/**
 * Activation hook handler.
 *
 * @package HOAY
 */

namespace HOAY;

defined( 'ABSPATH' ) || exit;

/**
 * Runs once when the plugin is activated.
 */
final class Activator {

	/**
	 * Seed default options if they don't exist yet, and stamp the install
	 * version so future migrations have a baseline.
	 *
	 * @return void
	 */
	public static function activate() {
		if ( false === get_option( 'hoay_settings', false ) ) {
			add_option( 'hoay_settings', Settings\Options::defaults() );
		}

		update_option( 'hoay_version', HOAY_VERSION, false );
	}
}
