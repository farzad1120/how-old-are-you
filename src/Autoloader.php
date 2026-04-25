<?php
/**
 * PSR-4 fallback autoloader.
 *
 * Used only when Composer's autoloader is not available (e.g. when the plugin
 * is installed as a release zip rather than via Composer). Maps the `HOAY\`
 * namespace to the `src/` directory.
 *
 * @package HOAY
 */

namespace HOAY;

defined( 'ABSPATH' ) || exit;

/**
 * Minimal PSR-4 autoloader for the HOAY namespace.
 */
final class Autoloader {

	/**
	 * Register the autoloader.
	 *
	 * @return void
	 */
	public static function register() {
		spl_autoload_register( array( __CLASS__, 'load' ) );
	}

	/**
	 * Resolve a fully-qualified class name to a file inside `src/`.
	 *
	 * @param string $class_name Fully-qualified class name.
	 * @return void
	 */
	public static function load( $class_name ) {
		$prefix = __NAMESPACE__ . '\\';
		if ( 0 !== strpos( $class_name, $prefix ) ) {
			return;
		}

		$relative = substr( $class_name, strlen( $prefix ) );
		$path     = HOAY_PLUGIN_DIR . 'src/' . str_replace( '\\', DIRECTORY_SEPARATOR, $relative ) . '.php';

		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}
}
