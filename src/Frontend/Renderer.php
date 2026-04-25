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

		$options  = Options::all();
		$min_age  = (int) $options['minimum_age'];
		$mode     = (string) $options['verification_mode'];
		$logo_url = $options['logo_attachment_id']
			? (string) wp_get_attachment_image_url( (int) $options['logo_attachment_id'], 'medium' )
			: '';
		$nonce    = wp_create_nonce( 'hoay_verify' );
		$ajax_url = admin_url( 'admin-ajax.php' );
		$max_dob  = current_time( 'Y-m-d' );
		$min_dob  = (string) gmdate( 'Y-m-d', strtotime( '-120 years' ) );
		$assets   = HOAY_PLUGIN_URL . 'assets/';
		$css_ver  = HOAY_VERSION;

		$lang        = get_bloginfo( 'language' );
		$lang_short  = strtolower( substr( (string) $lang, 0, 2 ) );
		$date_format = (string) get_option( 'date_format', 'F j, Y' );
		$format_hint = self::format_hint_for( $date_format );
		$months      = self::localized_months();
		$year_max    = (int) current_time( 'Y' );
		$year_min    = $year_max - 120;

		Template::render(
			'modal.php',
			array(
				'options'     => $options,
				'min_age'     => $min_age,
				'mode'        => $mode,
				'logo_url'    => $logo_url,
				'nonce'       => $nonce,
				'ajax_url'    => $ajax_url,
				'max_dob'     => $max_dob,
				'min_dob'     => $min_dob,
				'assets'      => $assets,
				'css_ver'     => $css_ver,
				'site_url'    => home_url( '/' ),
				'site_name'   => get_bloginfo( 'name' ),
				'lang'        => $lang,
				'lang_short'  => $lang_short,
				'format_hint' => $format_hint,
				'months'      => $months,
				'year_max'    => $year_max,
				'year_min'    => $year_min,
			)
		);
	}

	/**
	 * Convert a WordPress date format string into a human-readable hint.
	 *
	 * Maps PHP date tokens to letters that are conventional placeholders
	 * (Y → YYYY, m → MM, d → DD, F → localized "Month") and leaves other
	 * separator characters intact.
	 *
	 * @param string $format WordPress `date_format` option value.
	 * @return string
	 */
	public static function format_hint_for( $format ) {
		$map = array(
			'Y' => 'YYYY',
			'y' => 'YY',
			'n' => 'M',
			'm' => 'MM',
			'F' => __( 'Month', 'how-old-are-you' ),
			'M' => __( 'Mon', 'how-old-are-you' ),
			'j' => 'D',
			'd' => 'DD',
			'l' => '',
			'D' => '',
		);
		$out = '';
		$len = strlen( (string) $format );
		for ( $i = 0; $i < $len; $i++ ) {
			$ch   = $format[ $i ];
			$out .= isset( $map[ $ch ] ) ? $map[ $ch ] : $ch;
		}
		return trim( $out );
	}

	/**
	 * Get an array of site-localized month names indexed 1–12.
	 *
	 * Uses date_i18n() so month names follow the active WordPress locale,
	 * regardless of the visitor's browser language.
	 *
	 * @return array<int,string>
	 */
	public static function localized_months() {
		$months = array();
		for ( $m = 1; $m <= 12; $m++ ) {
			$ts = mktime( 0, 0, 0, $m, 1, 2000 );
			if ( false === $ts ) {
				continue;
			}
			$months[ $m ] = (string) date_i18n( 'F', $ts );
		}
		return $months;
	}

	/**
	 * Render the merged inline CSS variables for the overlay.
	 *
	 * Public static so the template can call it without a Renderer instance.
	 * Every value defined here is also documented next to the Custom CSS
	 * field on the settings page.
	 *
	 * @param array<string,mixed> $options Settings.
	 * @return string CSS rule body for `.hoay-overlay`.
	 */
	public static function css_variables( array $options ) {
		$bg_image_url = ! empty( $options['background_image_id'] )
			? (string) wp_get_attachment_image_url( (int) $options['background_image_id'], 'full' )
			: '';

		$font_family = (string) $options['font_family'];
		if ( '' === $font_family ) {
			$font_family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif';
		}

		$vars = array(
			'--hoay-bg'            => (string) $options['background_color'],
			'--hoay-bg-image'      => '' !== $bg_image_url ? 'url("' . esc_url_raw( $bg_image_url ) . '")' : 'none',
			'--hoay-bg-size'       => (string) $options['background_image_size'],
			'--hoay-opacity'       => (string) $options['overlay_opacity'],
			'--hoay-blur'          => (int) $options['backdrop_blur_px'] . 'px',
			'--hoay-panel'         => (string) $options['panel_color'],
			'--hoay-panel-width'   => (int) $options['panel_width_px'] . 'px',
			'--hoay-panel-padding' => (int) $options['panel_padding_px'] . 'px',
			'--hoay-panel-radius'  => (int) $options['panel_radius_px'] . 'px',
			'--hoay-text'          => (string) $options['text_color'],
			'--hoay-text-align'    => (string) $options['text_align'],
			'--hoay-accent'        => (string) $options['accent_color'],
			'--hoay-font'          => $font_family,
			'--hoay-font-size'     => (int) $options['font_size_base_px'] . 'px',
			'--hoay-heading-size'  => (int) $options['heading_size_px'] . 'px',
			'--hoay-button-radius' => (int) $options['button_radius_px'] . 'px',
			'--hoay-input-radius'  => (int) $options['input_radius_px'] . 'px',
			'--hoay-logo-max'      => (int) $options['logo_max_width_px'] . 'px',
		);

		$out = '';
		foreach ( $vars as $name => $value ) {
			$out .= $name . ': ' . $value . ';';
		}
		return $out;
	}
}
