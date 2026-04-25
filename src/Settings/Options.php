<?php
/**
 * Typed accessor for plugin options.
 *
 * @package HOAY
 */

namespace HOAY\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Loads, validates, and exposes the plugin's settings.
 *
 * All settings live in a single wp_option named `hoay_settings` (an
 * associative array) to avoid polluting the options table.
 */
final class Options {

	/**
	 * Option key in `wp_options`.
	 */
	const OPTION_KEY = 'hoay_settings';

	/**
	 * In-memory cache of the merged option array.
	 *
	 * @var array<string,mixed>|null
	 */
	private static $cache = null;

	/**
	 * Get the canonical defaults for every setting.
	 *
	 * Translation calls here are intentional: defaults are seeded into the DB
	 * on activation, and re-rendered via Options::get() on each page load,
	 * so end users will always see strings in their site language.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		return array(
			'enabled'                  => true,
			'minimum_age'              => 18,
			'verification_mode'        => 'dob',
			'cookie_name'              => 'hoay_verified',
			'cookie_lifetime_days'     => 30,
			'cookie_same_site'         => 'Lax',
			'heading_text'             => __( 'Please verify your age', 'how-old-are-you' ),
			'body_text'                => __( 'You must be of legal age to enter this site.', 'how-old-are-you' ),
			'dob_label'                => __( 'Date of birth', 'how-old-are-you' ),
			'confirm_yes_label'        => __( 'I am {age} or older', 'how-old-are-you' ),
			'confirm_no_label'         => __( 'I am under {age}', 'how-old-are-you' ),
			'submit_label'             => __( 'Enter site', 'how-old-are-you' ),
			'rejection_heading'        => __( 'Sorry, you can\'t enter', 'how-old-are-you' ),
			'rejection_body'           => __( 'You are not old enough to view this site.', 'how-old-are-you' ),
			'logo_attachment_id'       => 0,
			'logo_max_width_px'        => 160,
			'background_color'         => '#0b0b0b',
			'background_image_id'      => 0,
			'background_image_size'    => 'cover',
			'overlay_opacity'          => 0.92,
			'backdrop_blur_px'         => 0,
			'panel_color'              => '#ffffff',
			'panel_width_px'           => 440,
			'panel_padding_px'         => 36,
			'panel_radius_px'          => 12,
			'text_color'               => '#111111',
			'text_align'               => 'center',
			'accent_color'             => '#c7a008',
			'font_family'              => '',
			'font_size_base_px'        => 16,
			'heading_size_px'          => 22,
			'button_radius_px'         => 8,
			'input_radius_px'          => 8,
			'dob_input_style'          => 'native',
			'custom_css'               => '',
			'excluded_paths'           => '',
			// SEO — search engines + social previews.
			'seo_bot_bypass'           => true,
			'seo_bot_user_agents'      => '',
			'seo_robots_meta'          => 'noindex,nofollow',
			'seo_canonical_to_request' => true,
			'seo_inherit_open_graph'   => true,
			'seo_meta_description'     => '',
			'seo_og_image_id'          => 0,
		);
	}

	/**
	 * Get the full settings array, merged over defaults.
	 *
	 * @return array<string,mixed>
	 */
	public static function all() {
		if ( null !== self::$cache ) {
			return self::$cache;
		}

		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		self::$cache = array_merge( self::defaults(), $stored );
		return self::$cache;
	}

	/**
	 * Get a single setting value.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $fallback Value returned if the key is absent (defaults to null).
	 * @return mixed
	 */
	public static function get( $key, $fallback = null ) {
		$all = self::all();
		return array_key_exists( $key, $all ) ? $all[ $key ] : $fallback;
	}

	/**
	 * Persist the full settings array.
	 *
	 * Caller is responsible for sanitising values before passing them in;
	 * this method only handles storage and cache invalidation.
	 *
	 * @param array<string,mixed> $values Already-sanitised settings.
	 * @return bool True on success.
	 */
	public static function update( array $values ) {
		self::$cache = null;
		return update_option( self::OPTION_KEY, $values );
	}

	/**
	 * Drop the in-memory cache. Useful in tests.
	 *
	 * @return void
	 */
	public static function flush_cache() {
		self::$cache = null;
	}

	/**
	 * Replace `{age}` placeholders in any string with the configured minimum.
	 *
	 * @param string $text Source text, possibly containing `{age}`.
	 * @return string
	 */
	public static function interpolate_age( $text ) {
		return str_replace( '{age}', (string) self::get( 'minimum_age' ), (string) $text );
	}
}
