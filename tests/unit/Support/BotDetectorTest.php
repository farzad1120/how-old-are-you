<?php
/**
 * Unit tests for BotDetector.
 *
 * @package HOAY\Tests
 */

namespace HOAY\Tests\Unit\Support;

use HOAY\Support\BotDetector;
use PHPUnit\Framework\TestCase;

/**
 * @covers \HOAY\Support\BotDetector
 */
final class BotDetectorTest extends TestCase {

	public function test_matches_googlebot_with_default_tokens() {
		$ua = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
		$this->assertTrue( BotDetector::is_bot( $ua ) );
	}

	public function test_matches_facebook_unfurler() {
		$ua = 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)';
		$this->assertTrue( BotDetector::is_bot( $ua ) );
	}

	public function test_matches_twitterbot() {
		$this->assertTrue( BotDetector::is_bot( 'Twitterbot/1.0' ) );
	}

	public function test_does_not_match_regular_browser() {
		$ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15';
		$this->assertFalse( BotDetector::is_bot( $ua ) );
	}

	public function test_match_is_case_insensitive() {
		$this->assertTrue( BotDetector::is_bot( 'compatible; googlebot/2.1' ) );
		$this->assertTrue( BotDetector::is_bot( 'BINGBOT/2.0' ) );
	}

	public function test_returns_false_for_empty_or_non_string_ua() {
		$this->assertFalse( BotDetector::is_bot( '' ) );
		$this->assertFalse( BotDetector::is_bot( null ) );
		$this->assertFalse( BotDetector::is_bot( array() ) );
	}

	public function test_custom_tokens_override_defaults() {
		$ua = 'Googlebot/2.1';
		// Custom list with no Google in it → no match even though the default list would.
		$this->assertFalse( BotDetector::is_bot( $ua, array( 'CustomBot' ) ) );
		$this->assertTrue( BotDetector::is_bot( 'CustomBot/1.0', array( 'CustomBot' ) ) );
	}

	public function test_string_tokens_are_split_on_newlines() {
		$tokens = "Googlebot\n  Bingbot  \n\nTwitterbot";
		$this->assertTrue( BotDetector::is_bot( 'Bingbot/2.0', $tokens ) );
		$this->assertTrue( BotDetector::is_bot( 'Twitterbot/1.0', $tokens ) );
		$this->assertFalse( BotDetector::is_bot( 'AdsBot-Google/2.1', $tokens ) );
	}

	public function test_empty_token_list_falls_back_to_defaults() {
		$ua = 'Mozilla/5.0 (compatible; Googlebot/2.1)';
		$this->assertTrue( BotDetector::is_bot( $ua, '' ) );
		$this->assertTrue( BotDetector::is_bot( $ua, "\n  \n" ) );
		$this->assertTrue( BotDetector::is_bot( $ua, array() ) );
	}

	public function test_normalize_tokens_dedupes_and_trims() {
		$result = BotDetector::normalize_tokens( "Googlebot\nGooglebot\n  Bingbot  " );
		$this->assertSame( array( 'Googlebot', 'Bingbot' ), $result );
	}
}
