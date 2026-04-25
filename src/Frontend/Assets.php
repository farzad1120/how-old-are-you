<?php
/**
 * Asset URL helpers for the frontend overlay.
 *
 * Asset enqueueing is intentionally not done via wp_enqueue_*: the modal
 * is rendered as a standalone document by Renderer, so there is no
 * `wp_head`/`wp_footer` cycle to enqueue into. This class exists as a
 * single source of truth for asset URLs and versions, used by the
 * template directly.
 *
 * @package HOAY
 */

namespace HOAY\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Provides versioned asset URLs.
 */
final class Assets {

	/**
	 * URL to a file under the plugin's `assets/` directory, with version query.
	 *
	 * @param string $relative Path under assets/, e.g. `css/frontend.css`.
	 * @return string
	 */
	public static function url( $relative ) {
		return HOAY_PLUGIN_URL . 'assets/' . ltrim( (string) $relative, '/' ) . '?ver=' . rawurlencode( HOAY_VERSION );
	}
}
