<?php
/**
 * Frontend gate — decides whether to show the verification overlay.
 *
 * @package HOAY
 */

namespace HOAY\Frontend;

use HOAY\Settings\Options;
use HOAY\Verification\CookieManager;

defined( 'ABSPATH' ) || exit;

/**
 * Hooked on `template_redirect`, intercepts unverified front-end GETs and
 * streams the verification overlay instead of the page body.
 */
final class Gate {

	/**
	 * Wire WordPress hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'template_redirect', array( $this, 'maybe_render' ), 0 );
	}

	/**
	 * Decide whether to render and, if so, render+exit.
	 *
	 * @return void
	 */
	public function maybe_render() {
		if ( ! $this->should_gate() ) {
			return;
		}

		nocache_headers();
		( new Renderer() )->render();
		exit;
	}

	/**
	 * Whether the gate should be shown for this request.
	 *
	 * @return bool
	 */
	public function should_gate() {
		if ( ! Options::get( 'enabled' ) ) {
			return false;
		}

		if ( $this->is_exempt_request() ) {
			return false;
		}

		if ( CookieManager::is_verified( (string) Options::get( 'cookie_name' ) ) ) {
			return false;
		}

		/**
		 * Final say on whether the gate renders for this request.
		 *
		 * @param bool $should True to render the gate.
		 */
		return (bool) apply_filters( 'hoay_should_gate', true );
	}

	/**
	 * Endpoints and request shapes that always bypass the gate.
	 *
	 * @return bool
	 */
	private function is_exempt_request() {
		if ( is_admin() ) {
			return true;
		}
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return true;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return true;
		}
		if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'GET' !== strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) ) {
			return true;
		}
		if ( $this->is_login_or_register() ) {
			return true;
		}
		if ( is_robots() || is_feed() || is_trackback() ) {
			return true;
		}
		if ( $this->path_is_excluded() ) {
			return true;
		}

		return false;
	}

	/**
	 * Detect WP login / register / lost-password screens.
	 *
	 * @return bool
	 */
	private function is_login_or_register() {
		$pagenow = isset( $GLOBALS['pagenow'] ) ? (string) $GLOBALS['pagenow'] : '';
		return in_array( $pagenow, array( 'wp-login.php', 'wp-register.php' ), true );
	}

	/**
	 * Whether the current request URI matches an admin-configured excluded prefix.
	 *
	 * @return bool
	 */
	private function path_is_excluded() {
		$paths = array_filter(
			array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) Options::get( 'excluded_paths' ) ) )
		);

		/**
		 * Filter the list of excluded path prefixes.
		 *
		 * @param string[] $paths Path prefixes that bypass the gate.
		 */
		$paths = (array) apply_filters( 'hoay_excluded_paths', $paths );

		if ( empty( $paths ) ) {
			return false;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$path        = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
		if ( '' === $path ) {
			$path = '/';
		}

		foreach ( $paths as $prefix ) {
			if ( '' === $prefix ) {
				continue;
			}
			if ( 0 === strpos( $path, $prefix ) ) {
				return true;
			}
		}
		return false;
	}
}
