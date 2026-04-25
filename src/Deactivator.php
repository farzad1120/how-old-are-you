<?php
/**
 * Deactivation hook handler.
 *
 * @package HOAY
 */

namespace HOAY;

defined( 'ABSPATH' ) || exit;

/**
 * Runs when the plugin is deactivated.
 *
 * Settings survive deactivation by design — users frequently deactivate
 * temporarily and expect to find their configuration intact. Permanent
 * cleanup happens in `uninstall.php`.
 */
final class Deactivator {

	/**
	 * Currently a no-op; kept for symmetry and future use.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Intentionally empty.
	}
}
