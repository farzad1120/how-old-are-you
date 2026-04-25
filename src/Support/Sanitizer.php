<?php
/**
 * Centralised input sanitisation helpers.
 *
 * @package HOAY
 */

namespace HOAY\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Type-aware sanitisers used by the settings page and AJAX handler.
 *
 * Wraps WordPress core functions so call sites stay compact and unit
 * tests can stub one place. Falls back to safe no-op behaviour if
 * called outside WordPress (e.g. in unit tests).
 */
final class Sanitizer {

	/**
	 * Allowed values for `cookie_same_site`.
	 *
	 * @var string[]
	 */
	const SAME_SITE_VALUES = array( 'Lax', 'Strict', 'None' );

	/**
	 * Allowed values for `verification_mode`.
	 *
	 * @var string[]
	 */
	const VERIFICATION_MODES = array( 'dob', 'confirm' );

	/**
	 * Coerce any input to a boolean.
	 *
	 * Treats `'1'`, `1`, `true`, `'true'`, `'on'`, `'yes'` as true and
	 * everything else as false.
	 *
	 * @param mixed $value Raw input.
	 * @return bool
	 */
	public static function bool( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_string( $value ) ) {
			return in_array( strtolower( $value ), array( '1', 'true', 'on', 'yes' ), true );
		}
		return (bool) $value;
	}

	/**
	 * Sanitize an integer within a min/max range (inclusive).
	 *
	 * @param mixed $value Raw input.
	 * @param int   $min   Minimum allowed.
	 * @param int   $max   Maximum allowed.
	 * @return int
	 */
	public static function int_range( $value, $min, $max ) {
		$int = (int) $value;
		if ( $int < $min ) {
			return (int) $min;
		}
		if ( $int > $max ) {
			return (int) $max;
		}
		return $int;
	}

	/**
	 * Sanitize a float within a min/max range (inclusive).
	 *
	 * @param mixed $value Raw input.
	 * @param float $min   Minimum allowed.
	 * @param float $max   Maximum allowed.
	 * @return float
	 */
	public static function float_range( $value, $min, $max ) {
		$float = (float) $value;
		if ( $float < $min ) {
			return (float) $min;
		}
		if ( $float > $max ) {
			return (float) $max;
		}
		return $float;
	}

	/**
	 * Restrict a value to a fixed set of choices.
	 *
	 * @param mixed    $value    Raw input.
	 * @param string[] $allowed  Allowed string values.
	 * @param string   $fallback Returned when `$value` is not in `$allowed`.
	 * @return string
	 */
	public static function enum( $value, array $allowed, $fallback ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		return in_array( $value, $allowed, true ) ? $value : (string) $fallback;
	}

	/**
	 * Sanitize a single-line text field.
	 *
	 * @param mixed $value Raw input.
	 * @return string
	 */
	public static function text( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		if ( function_exists( 'sanitize_text_field' ) ) {
			return sanitize_text_field( $value );
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- WP function not available in unit-test fallback path.
		return trim( strip_tags( $value ) );
	}

	/**
	 * Sanitize a multi-line free-text block (no HTML).
	 *
	 * @param mixed $value Raw input.
	 * @return string
	 */
	public static function textarea( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		if ( function_exists( 'sanitize_textarea_field' ) ) {
			return sanitize_textarea_field( $value );
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- WP function not available in unit-test fallback path.
		return trim( strip_tags( $value ) );
	}

	/**
	 * Sanitize a hex color (`#rrggbb` or `#rgb`); returns empty string on failure.
	 *
	 * @param mixed $value Raw input.
	 * @return string
	 */
	public static function hex_color( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		if ( function_exists( 'sanitize_hex_color' ) ) {
			$result = sanitize_hex_color( $value );
			return is_string( $result ) ? $result : '';
		}
		return preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $value ) ? $value : '';
	}

	/**
	 * Sanitize a slug-like identifier (alphanumerics, dashes, underscores).
	 *
	 * @param mixed $value Raw input.
	 * @return string
	 */
	public static function slug( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		if ( function_exists( 'sanitize_key' ) ) {
			return sanitize_key( $value );
		}
		return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', $value ) );
	}

	/**
	 * Sanitize a small CSS snippet for inline injection.
	 *
	 * Strips `<` and `>` to prevent breaking out of a `<style>` tag, and
	 * removes obvious script-like sequences. Not a full CSS parser — admins
	 * supplying custom CSS are expected to be trusted.
	 *
	 * @param mixed $value Raw input.
	 * @return string
	 */
	public static function css( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		$value = str_replace( array( '<', '>' ), '', $value );
		$value = preg_replace( '/(?:javascript|expression|behaviou?r|@import)\s*[:(]/i', '', $value );
		return is_string( $value ) ? trim( $value ) : '';
	}

	/**
	 * Sanitize an excluded-paths blob (one path per line).
	 *
	 * @param mixed $value Raw input.
	 * @return string Newline-joined list of cleaned, leading-slash-prefixed paths.
	 */
	public static function path_list( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		$lines = preg_split( '/\r\n|\r|\n/', $value );
		$out   = array();
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}
			$line = preg_replace( '#[^A-Za-z0-9_\-/\.]#', '', $line );
			if ( '' === $line ) {
				continue;
			}
			if ( '/' !== $line[0] ) {
				$line = '/' . $line;
			}
			$out[] = $line;
		}
		return implode( "\n", $out );
	}

	/**
	 * Sanitize an attachment ID.
	 *
	 * @param mixed $value Raw input.
	 * @return int Non-negative integer.
	 */
	public static function attachment_id( $value ) {
		$id = (int) $value;
		return $id > 0 ? $id : 0;
	}
}
