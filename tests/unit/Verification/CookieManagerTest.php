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

	const SECRET = 'unit-test-secret';

	public function test_build_then_verify_round_trips() {
		$expiry = time() + 3600;
		$value  = CookieManager::build_value( $expiry, self::SECRET );
		$this->assertTrue( CookieManager::verify_value( $value, self::SECRET ) );
	}

	public function test_verify_rejects_tampered_payload() {
		$expiry = time() + 3600;
		$value  = CookieManager::build_value( $expiry, self::SECRET );
		// Flip a character in the HMAC portion.
		$tampered = substr( $value, 0, -1 ) . ( '0' === substr( $value, -1 ) ? '1' : '0' );
		$this->assertFalse( CookieManager::verify_value( $tampered, self::SECRET ) );
	}

	public function test_verify_rejects_extended_expiry() {
		$expiry = time() + 60;
		$value  = CookieManager::build_value( $expiry, self::SECRET );
		// Try to push expiry forward without re-signing.
		list( $payload, , $mac ) = explode( '|', $value );
		$forged = $payload . '|' . ( $expiry + 99999 ) . '|' . $mac;
		$this->assertFalse( CookieManager::verify_value( $forged, self::SECRET ) );
	}

	public function test_verify_rejects_expired_cookie() {
		$past  = time() - 1;
		$value = CookieManager::build_value( $past, self::SECRET );
		$this->assertFalse( CookieManager::verify_value( $value, self::SECRET ) );
	}

	public function test_verify_rejects_wrong_secret() {
		$expiry = time() + 3600;
		$value  = CookieManager::build_value( $expiry, self::SECRET );
		$this->assertFalse( CookieManager::verify_value( $value, 'different-secret' ) );
	}

	public function test_verify_rejects_malformed_inputs() {
		$this->assertFalse( CookieManager::verify_value( '', self::SECRET ) );
		$this->assertFalse( CookieManager::verify_value( 'no-pipes', self::SECRET ) );
		$this->assertFalse( CookieManager::verify_value( 'a|b|c|d', self::SECRET ) );
		$this->assertFalse( CookieManager::verify_value( 'v1|notnumeric|abc', self::SECRET ) );
	}

	public function test_verify_rejects_unknown_payload_version() {
		$expiry  = time() + 3600;
		$message = 'v999.' . $expiry;
		$mac     = hash_hmac( 'sha256', $message, self::SECRET );
		$value   = 'v999|' . $expiry . '|' . $mac;
		$this->assertFalse( CookieManager::verify_value( $value, self::SECRET ) );
	}
}
