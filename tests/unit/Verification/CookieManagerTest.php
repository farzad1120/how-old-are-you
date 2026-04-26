<?php
/**
 * Unit tests for CookieManager.
 *
 * Focuses on the pure cryptographic helpers (build_value/verify_value).
 * The setcookie/$_COOKIE-bound methods are exercised in integration tests.
 *
 * @package HOAY\Tests
 */

namespace HOAY\Tests\Unit\Verification;

use HOAY\Verification\CookieManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \HOAY\Verification\CookieManager
 */
final class CookieManagerTest extends TestCase {

	/**
	 * Per-test HMAC secret. Generated fresh in setUp() so nothing is
	 * hardcoded — keeps static-analysis tools happy and ensures each
	 * test exercises a different keyspace.
	 *
	 * @var string
	 */
	private $secret;

	/**
	 * Generate a random secret for each test run.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->secret = bin2hex( random_bytes( 32 ) );
	}

	public function test_build_then_verify_round_trips() {
		$expiry = time() + 3600;
		$value  = CookieManager::build_value( $expiry, $this->secret );
		$this->assertTrue( CookieManager::verify_value( $value, $this->secret ) );
	}

	public function test_verify_rejects_tampered_payload() {
		$expiry = time() + 3600;
		$value  = CookieManager::build_value( $expiry, $this->secret );
		// Flip a character in the HMAC portion.
		$tampered = substr( $value, 0, -1 ) . ( '0' === substr( $value, -1 ) ? '1' : '0' );
		$this->assertFalse( CookieManager::verify_value( $tampered, $this->secret ) );
	}

	public function test_verify_rejects_extended_expiry() {
		$expiry = time() + 60;
		$value  = CookieManager::build_value( $expiry, $this->secret );
		// Try to push expiry forward without re-signing.
		list( $payload, , $mac ) = explode( '|', $value );
		$forged = $payload . '|' . ( $expiry + 99999 ) . '|' . $mac;
		$this->assertFalse( CookieManager::verify_value( $forged, $this->secret ) );
	}

	public function test_verify_rejects_expired_cookie() {
		$past  = time() - 1;
		$value = CookieManager::build_value( $past, $this->secret );
		$this->assertFalse( CookieManager::verify_value( $value, $this->secret ) );
	}

	public function test_verify_rejects_wrong_secret() {
		$expiry      = time() + 3600;
		$value       = CookieManager::build_value( $expiry, $this->secret );
		$other       = bin2hex( random_bytes( 32 ) );
		$this->assertFalse( CookieManager::verify_value( $value, $other ) );
	}

	public function test_verify_rejects_malformed_inputs() {
		$this->assertFalse( CookieManager::verify_value( '', $this->secret ) );
		$this->assertFalse( CookieManager::verify_value( 'no-pipes', $this->secret ) );
		$this->assertFalse( CookieManager::verify_value( 'a|b|c|d', $this->secret ) );
		$this->assertFalse( CookieManager::verify_value( 'v1|notnumeric|abc', $this->secret ) );
	}

	public function test_verify_rejects_unknown_payload_version() {
		$expiry  = time() + 3600;
		$message = 'v999.' . $expiry;
		$mac     = hash_hmac( 'sha256', $message, $this->secret );
		$value   = 'v999|' . $expiry . '|' . $mac;
		$this->assertFalse( CookieManager::verify_value( $value, $this->secret ) );
	}
}
