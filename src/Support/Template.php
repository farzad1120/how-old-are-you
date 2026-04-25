<?php
/**
 * Tiny view loader.
 *
 * @package HOAY
 */

namespace HOAY\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Load PHP templates from the plugin's `templates/` directory, with the
 * ability for themes to override them by placing a copy at
 * `<theme>/how-old-are-you/<relative>`.
 */
final class Template {

	/**
	 * Render a template, passing `$vars` as named locals.
	 *
	 * @param string               $relative Path under the templates dir, e.g. `modal.php`.
	 * @param array<string, mixed> $vars     Variables exposed inside the template.
	 * @return void
	 */
	public static function render( $relative, array $vars = array() ) {
		$file = self::locate( $relative );
		if ( null === $file ) {
			return;
		}

		// Expose vars as $key locals inside the template.
		extract( $vars, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		include $file;
	}

	/**
	 * Resolve a template path, preferring a theme override.
	 *
	 * @param string $relative Relative template path.
	 * @return string|null Absolute filesystem path, or null if not found.
	 */
	public static function locate( $relative ) {
		$relative = ltrim( (string) $relative, '/\\' );

		if ( function_exists( 'locate_template' ) ) {
			$found = locate_template( 'how-old-are-you/' . $relative );
			if ( '' !== $found ) {
				return $found;
			}
		}

		$path = HOAY_PLUGIN_DIR . 'templates/' . $relative;
		return is_readable( $path ) ? $path : null;
	}
}
