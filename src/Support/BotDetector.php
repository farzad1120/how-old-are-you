<?php
/**
 * Pure user-agent → bot matcher.
 *
 * @package HOAY
 */

namespace HOAY\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Decides whether a request comes from a search engine, AI crawler,
 * archiver, or social-media link unfurler.
 *
 * Stateless and side-effect free so it can be unit-tested without WordPress.
 */
final class BotDetector {

	/**
	 * Built-in default UA tokens.
	 *
	 * Match is case-insensitive substring, so a token like `Googlebot`
	 * also matches `Googlebot-Image`, `Mediapartners-Google`, etc.
	 *
	 * @var string[]
	 */
	const DEFAULT_TOKENS = array(
		// Search engines.
		'Googlebot',
		'Bingbot',
		'Slurp',
		'DuckDuckBot',
		'Baiduspider',
		'YandexBot',
		'Sogou',
		'Exabot',
		'Applebot',
		'PetalBot',
		'SeznamBot',
		'ia_archiver',
		// Social previews / unfurlers.
		'facebookexternalhit',
		'Facebot',
		'Twitterbot',
		'LinkedInBot',
		'Pinterestbot',
		'TelegramBot',
		'Discordbot',
		'Slackbot',
		'WhatsApp',
		'SkypeUriPreview',
		'redditbot',
		// Site archivers / search assistants.
		'archive.org_bot',
		'Wayback',
		// Ads / structured data validators.
		'Mediapartners-Google',
		'AdsBot-Google',
		'Google-Site-Verification',
	);

	/**
	 * Decide whether a UA string matches any of the configured tokens.
	 *
	 * @param string|null   $user_agent Raw `User-Agent` header value.
	 * @param string[]|null $tokens     Tokens to match. Falsy → built-in defaults.
	 * @return bool
	 */
	public static function is_bot( $user_agent, $tokens = null ) {
		if ( ! is_string( $user_agent ) || '' === $user_agent ) {
			return false;
		}

		$tokens = self::normalize_tokens( $tokens );
		if ( empty( $tokens ) ) {
			return false;
		}

		foreach ( $tokens as $token ) {
			if ( false !== stripos( $user_agent, $token ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Resolve the effective token list.
	 *
	 * Accepts:
	 *   - null         → built-in defaults
	 *   - string       → split on newlines (admin textarea convention)
	 *   - string[]     → used as-is, trimmed and de-duped
	 * Empty result falls back to built-in defaults.
	 *
	 * @param mixed $tokens Raw token input.
	 * @return string[]
	 */
	public static function normalize_tokens( $tokens ) {
		if ( null === $tokens ) {
			return self::DEFAULT_TOKENS;
		}

		if ( is_string( $tokens ) ) {
			$tokens = preg_split( '/\r\n|\r|\n/', $tokens );
		}

		if ( ! is_array( $tokens ) ) {
			return self::DEFAULT_TOKENS;
		}

		$out = array();
		foreach ( $tokens as $token ) {
			$token = trim( (string) $token );
			if ( '' !== $token ) {
				$out[] = $token;
			}
		}

		if ( empty( $out ) ) {
			return self::DEFAULT_TOKENS;
		}

		return array_values( array_unique( $out ) );
	}
}
