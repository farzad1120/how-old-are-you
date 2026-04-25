<?php
/**
 * Integration smoke test for the frontend gate.
 *
 * Runs only when WP_TESTS_DIR is set, so CI without the WP test suite
 * skips it cleanly. Mirrors the manual end-to-end check from step 10:
 * an unverified request gets the overlay; a request with a valid signed
 * cookie passes through.
 *
 * @package HOAY\Tests
 */

namespace HOAY\Tests\Integration;

use HOAY\Verification\CookieManager;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
final class GateTest extends TestCase {

	protected function setUp(): void {
		if ( ! getenv( 'WP_TESTS_DIR' ) || ! function_exists( 'wp_create_nonce' ) ) {
			$this->markTestSkipped( 'WP test suite not loaded; set WP_TESTS_DIR and bootstrap WP-PHPUnit.' );
		}
	}

	public function test_unverified_visitor_sees_overlay() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI']    = '/';
		unset( $_COOKIE['hoay_verified'] );

		ob_start();
		try {
			do_action( 'template_redirect' );
		} catch ( \Throwable $e ) { // exit() inside Gate is hard to catch in phpunit.
			// Expected — Gate calls exit() after streaming.
		}
		$body = ob_get_clean();

		$this->assertStringContainsString( 'hoay-overlay', (string) $body );
		$this->assertStringContainsString( 'data-mode="', (string) $body );
	}

	public function test_verified_cookie_bypasses_gate() {
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI']    = '/';

		$expiry = time() + HOUR_IN_SECONDS;
		$_COOKIE['hoay_verified'] = CookieManager::build_value( $expiry, CookieManager::secret() );

		ob_start();
		do_action( 'template_redirect' );
		$body = ob_get_clean();

		$this->assertStringNotContainsString( 'hoay-overlay', (string) $body );
	}
}
