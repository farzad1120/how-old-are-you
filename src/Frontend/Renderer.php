<?php
/**
 * Frontend overlay renderer.
 *
 * @package HOAY
 */

namespace HOAY\Frontend;

use HOAY\Settings\Options;
use HOAY\Support\Template;

defined( 'ABSPATH' ) || exit;

/**
 * Streams the verification overlay as a complete HTML document and exits.
 *
 * Rendered as a standalone document (not injected into the theme) so the
 * page body never reaches the browser — preventing visitors from peeking
 * at content via "view source".
 */
final class Renderer {

	/**
	 * Output the overlay document.
	 *
	 * @return void
	 */
	public function render() {
		status_header( 200 );
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );

		$options   = Options::all();
		$min_age   = (int) $options['minimum_age'];
		$mode      = (string) $options['verification_mode'];
		$logo_url  = $options['logo_attachment_id']
			? (string) wp_get_attachment_image_url( (int) $options['logo_attachment_id'], 'medium' )
			: '';
		$nonce     = wp_create_nonce( 'hoay_verify' );
		$ajax_url  = admin_url( 'admin-ajax.php' );
		$max_dob   = current_time( 'Y-m-d' );
		$min_dob   = (string) gmdate( 'Y-m-d', strtotime( '-120 years' ) );
		$assets    = HOAY_PLUGIN_URL . 'assets/';
		$css_ver   = HOAY_VERSION;

		Template::render(
			'modal.php',
			array(
				'options'   => $options,
				'min_age'   => $min_age,
				'mode'      => $mode,
				'logo_url'  => $logo_url,
				'nonce'     => $nonce,
				'ajax_url'  => $ajax_url,
				'max_dob'   => $max_dob,
				'min_dob'   => $min_dob,
				'assets'    => $assets,
				'css_ver'   => $css_ver,
				'site_url'  => home_url( '/' ),
				'site_name' => get_bloginfo( 'name' ),
				'lang'      => get_bloginfo( 'language' ),
			)
		);
	}

	/**
	 * Render the merged inline CSS variables for the overlay.
	 *
	 * Public static so the template can call it without a Renderer instance.
	 *
	 * @param array<string,mixed> $options Settings.
	 * @return string CSS rule body for `.hoay-overlay`.
	 */
	public static function css_variables( array $options ) {
		$vars = array(
			'--hoay-bg'      => (string) $options['background_color'],
			'--hoay-opacity' => (string) $options['overlay_opacity'],
			'--hoay-panel'   => (string) $options['panel_color'],
			'--hoay-text'    => (string) $options['text_color'],
			'--hoay-accent'  => (string) $options['accent_color'],
		);

		$out = '';
		foreach ( $vars as $name => $value ) {
			$out .= $name . ': ' . $value . ';';
		}
		return $out;
	}
}
