<?php
/**
 * PHPUnit bootstrap.
 *
 * Used for unit tests that exercise pure PHP without booting WordPress.
 * Stubs the few WordPress concepts the source files reference at top-level
 * (the `ABSPATH` constant and a no-op `__()` translation helper) so the
 * production files can be loaded by the autoloader without exiting.
 *
 * Integration tests that need a real WordPress should use the official
 * WP-PHPUnit test suite via the `WP_TESTS_DIR` environment variable.
 *
 * @package HOAY\Tests
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

if ( ! function_exists( '__' ) ) {
	/**
	 * Pass-through translation stub.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Text domain (ignored in tests).
	 * @return string
	 */
	function __( $text, $domain = 'default' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames
		unset( $domain );
		return $text;
	}
}

require_once dirname( __DIR__ ) . '/vendor/autoload.php';
