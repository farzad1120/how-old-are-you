<?php
/**
 * Pure age calculation utilities.
 *
 * @package HOAY
 */

namespace HOAY\Verification;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Calculate ages from dates of birth.
 *
 * Stateless and side-effect free so it can be unit-tested without WordPress.
 */
final class AgeCalculator {

	/**
	 * Maximum plausible age, used as an upper bound when validating DOBs.
	 */
	const MAX_AGE = 120;

	/**
	 * Compute integer age in completed years from a Y-m-d date string.
	 *
	 * Comparison happens in the supplied timezone so a visitor whose birthday
	 * is "today" is never off by one due to UTC drift.
	 *
	 * @param string            $dob_ymd  Date of birth, ISO format `YYYY-MM-DD`.
	 * @param DateTimeZone|null $timezone Site timezone; defaults to UTC.
	 * @return int|null Age in years, or null if the input is invalid.
	 */
	public static function age_from_dob( $dob_ymd, ?DateTimeZone $timezone = null ) {
		if ( ! is_string( $dob_ymd ) || '' === $dob_ymd ) {
			return null;
		}

		if ( null === $timezone ) {
			$timezone = new DateTimeZone( 'UTC' );
		}

		try {
			$dob = DateTimeImmutable::createFromFormat( '!Y-m-d', $dob_ymd, $timezone );
		} catch ( Exception $e ) {
			return null;
		}

		if ( ! $dob instanceof DateTimeImmutable ) {
			return null;
		}

		// Reject malformed inputs that PHP silently coerces (e.g. "2020-13-40").
		if ( $dob->format( 'Y-m-d' ) !== $dob_ymd ) {
			return null;
		}

		$now = new DateTimeImmutable( 'now', $timezone );
		if ( $dob > $now ) {
			return null;
		}

		$age = (int) $now->diff( $dob )->y;
		if ( $age < 0 || $age > self::MAX_AGE ) {
			return null;
		}

		return $age;
	}

	/**
	 * Convenience wrapper: is this DOB at least `$minimum` years old?
	 *
	 * @param string            $dob_ymd  Date of birth, `YYYY-MM-DD`.
	 * @param int               $minimum  Minimum required age.
	 * @param DateTimeZone|null $timezone Site timezone; defaults to UTC.
	 * @return bool
	 */
	public static function is_of_age( $dob_ymd, $minimum, ?DateTimeZone $timezone = null ) {
		$age = self::age_from_dob( $dob_ymd, $timezone );
		return null !== $age && $age >= (int) $minimum;
	}
}
