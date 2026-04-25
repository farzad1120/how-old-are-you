<?php
/**
 * Unit tests for AgeCalculator.
 *
 * @package HOAY\Tests
 */

namespace HOAY\Tests\Unit\Verification;

use DateTimeImmutable;
use DateTimeZone;
use HOAY\Verification\AgeCalculator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \HOAY\Verification\AgeCalculator
 */
final class AgeCalculatorTest extends TestCase {

	/**
	 * Build a Y-m-d string for "$years_ago, $month_offset months ago".
	 *
	 * @param int $years_ago        Years before today.
	 * @param int $extra_days_after Days to add after that anchor.
	 * @return string
	 */
	private function ymd_offset( $years_ago, $extra_days_after = 0 ) {
		$tz   = new DateTimeZone( 'UTC' );
		$base = ( new DateTimeImmutable( 'now', $tz ) )
			->modify( sprintf( '-%d years', $years_ago ) )
			->modify( sprintf( '%+d days', $extra_days_after ) );
		return $base->format( 'Y-m-d' );
	}

	public function test_age_thirty_years_ago_is_thirty() {
		$dob = $this->ymd_offset( 30, -1 ); // birthday was yesterday → already 30.
		$this->assertSame( 30, AgeCalculator::age_from_dob( $dob ) );
	}

	public function test_birthday_tomorrow_still_returns_previous_age() {
		$dob = $this->ymd_offset( 30, 1 ); // birthday tomorrow → still 29.
		$this->assertSame( 29, AgeCalculator::age_from_dob( $dob ) );
	}

	public function test_future_dob_returns_null() {
		$dob = ( new DateTimeImmutable( '+1 day' ) )->format( 'Y-m-d' );
		$this->assertNull( AgeCalculator::age_from_dob( $dob ) );
	}

	public function test_invalid_format_returns_null() {
		$this->assertNull( AgeCalculator::age_from_dob( 'not-a-date' ) );
		$this->assertNull( AgeCalculator::age_from_dob( '2020-13-01' ) );
		$this->assertNull( AgeCalculator::age_from_dob( '2020-02-30' ) );
		$this->assertNull( AgeCalculator::age_from_dob( '01-01-2000' ) );
		$this->assertNull( AgeCalculator::age_from_dob( '' ) );
	}

	public function test_implausibly_old_returns_null() {
		$this->assertNull( AgeCalculator::age_from_dob( '1800-01-01' ) );
	}

	public function test_is_of_age_passes_when_above_threshold() {
		$dob = $this->ymd_offset( 21, -1 );
		$this->assertTrue( AgeCalculator::is_of_age( $dob, 18 ) );
		$this->assertTrue( AgeCalculator::is_of_age( $dob, 21 ) );
	}

	public function test_is_of_age_fails_when_below_threshold() {
		$dob = $this->ymd_offset( 17, -1 );
		$this->assertFalse( AgeCalculator::is_of_age( $dob, 18 ) );
	}

	public function test_is_of_age_fails_when_dob_invalid() {
		$this->assertFalse( AgeCalculator::is_of_age( 'garbage', 18 ) );
	}

	public function test_timezone_affects_birthday_boundary() {
		// DOB exactly 18 years ago at UTC midnight.
		$tz_utc = new DateTimeZone( 'UTC' );
		$dob    = ( new DateTimeImmutable( 'now', $tz_utc ) )
			->modify( '-18 years' )
			->format( 'Y-m-d' );

		$this->assertGreaterThanOrEqual( 18, AgeCalculator::age_from_dob( $dob, $tz_utc ) );
	}
}
