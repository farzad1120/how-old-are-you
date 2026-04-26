<?php
/**
 * Signed verification-cookie manager.
 *
 * @package HOAY
 */

namespace HOAY\Verification;

defined( 'ABSPATH' ) || exit;

/**
 * Reads, writes, and validates the age-verification cookie.
 *
 * Cookie value is `payload|expiry|hmac` where:
 *   - `payload` is a short token identifying the verification (`v1`)
 *   - `expiry`  is a unix timestamp
 *   - `hmac`    is `hash_hmac('sha256', payload.expiry, secret)`
 *
 * The HMAC prevents a visitor from forging or extending a cookie via
 * browser dev tools — a tampered cookie fails verification on the next
 * request and the gate re-renders.
 */
final class CookieManager {

	/**
	 * Current cookie payload version. Bump if the format changes.
	 */
	const PAYLOAD = 'v1';

	/**
	 * Build the signed cookie value for a given expiry timestamp.
	 *
	 * @param int    $expiry Unix timestamp at which the cookie expires.
	 * @param string $secret HMAC secret (use {@see secret()} in WP context).
	 * @return string
	 */
	public static function build_value( $expiry, $secret ) {
		$expiry  = (int) $expiry;
		$message = self::PAYLOAD . '.' . $expiry;
		$mac     = hash_hmac( 'sha256', $message, (string) $secret );
		return self::PAYLOAD . '|' . $expiry . '|' . $mac;
	}

	/**
	 * Verify a raw cookie value against the current secret and clock.
	 *
	 * @param string $raw    The cookie value as received from the browser.
	 * @param string $secret HMAC secret.
	 * @param int    $now    Current timestamp (defaults to time()).
	 * @return bool True if the value is intact and unexpired.
	 */
	public static function verify_value( $raw, $secret, $now = 0 ) {
		if ( ! is_string( $raw ) || '' === $raw ) {
			return false;
		}

		$parts = explode( '|', $raw );
		if ( 3 !== count( $parts ) ) {
			return false;
		}

		list( $payload, $expiry, $mac ) = $parts;

		if ( self::PAYLOAD !== $payload ) {
			return false;
		}

		if ( ! ctype_digit( $expiry ) ) {
			return false;
		}

		$expiry_int = (int) $expiry;
		$now        = $now > 0 ? (int) $now : time();
		if ( $expiry_int <= $now ) {
			return false;
		}

		$expected = hash_hmac( 'sha256', $payload . '.' . $expiry_int, (string) $secret );
		return hash_equals( $expected, (string) $mac );
	}

	/**
	 * Set the verification cookie via WordPress's setcookie wrapper.
	 *
	 * @param string $cookie_name      Cookie name from settings.
	 * @param int    $lifetime_seconds Lifetime in seconds from now.
	 * @param string $same_site        `Lax`, `Strict`, or `None`.
	 * @return bool
	 */
	public static function set_verified( $cookie_name, $lifetime_seconds, $same_site = 'Lax' ) {
		if ( headers_sent() ) {
			return false;
		}

		$expiry = time() + (int) $lifetime_seconds;
		$value  = self::build_value( $expiry, self::secret() );

		$args = array(
			'expires'  => $expiry,
			'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
			'domain'   => defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
			'secure'   => self::is_secure_request(),
			'httponly' => true,
			'samesite' => self::normalise_same_site( $same_site ),
		);

		/**
		 * Filter cookie args before setcookie.
		 *
		 * @param array  $args        setcookie options.
		 * @param string $cookie_name Cookie name.
		 */
		if ( function_exists( 'apply_filters' ) ) {
			$args = apply_filters( 'hoay_cookie_args', $args, $cookie_name );
		}

		$ok = setcookie( $cookie_name, $value, $args );
		if ( $ok ) {
			$_COOKIE[ $cookie_name ] = $value;
		}
		return (bool) $ok;
	}

	/**
	 * Read the cookie from `$_COOKIE` and return whether it's currently valid.
	 *
	 * @param string $cookie_name Cookie name from settings.
	 * @return bool
	 */
	public static function is_verified( $cookie_name ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$raw = sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) );
		return self::verify_value( $raw, self::secret() );
	}

	/**
	 * Delete the verification cookie.
	 *
	 * @param string $cookie_name Cookie name from settings.
	 * @return void
	 */
	public static function clear( $cookie_name ) {
		if ( headers_sent() ) {
			return;
		}
		$args = array(
			'expires'  => time() - 3600,
			'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
			'domain'   => defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '',
			'secure'   => self::is_secure_request(),
			'httponly' => true,
			'samesite' => 'Lax',
		);
		setcookie( $cookie_name, '', $args );
		unset( $_COOKIE[ $cookie_name ] );
	}

	/**
	 * Resolve the HMAC secret from WordPress salts.
	 *
	 * Falls back to a static string in non-WP contexts (tests).
	 *
	 * @return string
	 */
	public static function secret() {
		if ( function_exists( 'wp_salt' ) ) {
			return wp_salt( 'auth' ) . '|hoay';
		}
		return 'hoay-test-secret';
	}

	/**
	 * Whether the verification cookie should carry the Secure flag.
	 *
	 * In a normal WordPress context this mirrors `is_ssl()` — the cookie is
	 * marked Secure on HTTPS requests and not on plain HTTP, which matches
	 * what every browser will accept. The defensive `! function_exists`
	 * branch defaults to `true` so the flag is never silently dropped if
	 * the function is unavailable (e.g. unit-test bootstrap without WP).
	 *
	 * @return bool
	 */
	private static function is_secure_request() {
		if ( ! function_exists( 'is_ssl' ) ) {
			return true;
		}
		return (bool) is_ssl();
	}

	/**
	 * Coerce the SameSite attribute to a valid value.
	 *
	 * @param string $value Raw config value.
	 * @return string
	 */
	private static function normalise_same_site( $value ) {
		$allowed = array( 'Lax', 'Strict', 'None' );
		return in_array( (string) $value, $allowed, true ) ? (string) $value : 'Lax';
	}
}
