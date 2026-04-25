<?php
/**
 * Unit tests for Sanitizer.
 *
 * @package HOAY\Tests
 */

namespace HOAY\Tests\Unit\Support;

use HOAY\Support\Sanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \HOAY\Support\Sanitizer
 */
final class SanitizerTest extends TestCase {

	public function test_bool_recognises_truthy_strings() {
		$this->assertTrue( Sanitizer::bool( '1' ) );
		$this->assertTrue( Sanitizer::bool( 'on' ) );
		$this->assertTrue( Sanitizer::bool( 'yes' ) );
		$this->assertTrue( Sanitizer::bool( 'true' ) );
		$this->assertTrue( Sanitizer::bool( true ) );
	}

	public function test_bool_returns_false_for_falsy_values() {
		$this->assertFalse( Sanitizer::bool( '0' ) );
		$this->assertFalse( Sanitizer::bool( '' ) );
		$this->assertFalse( Sanitizer::bool( 'random' ) );
		$this->assertFalse( Sanitizer::bool( null ) );
	}

	public function test_int_range_clamps_below_and_above() {
		$this->assertSame( 1, Sanitizer::int_range( 0, 1, 100 ) );
		$this->assertSame( 100, Sanitizer::int_range( 999, 1, 100 ) );
		$this->assertSame( 42, Sanitizer::int_range( '42', 1, 100 ) );
	}

	public function test_float_range_clamps_below_and_above() {
		$this->assertSame( 0.0, Sanitizer::float_range( -1, 0.0, 1.0 ) );
		$this->assertSame( 1.0, Sanitizer::float_range( 5, 0.0, 1.0 ) );
		$this->assertEqualsWithDelta( 0.5, Sanitizer::float_range( '0.5', 0.0, 1.0 ), 0.0001 );
	}

	public function test_enum_returns_fallback_for_unknown() {
		$this->assertSame( 'dob', Sanitizer::enum( 'invalid', Sanitizer::VERIFICATION_MODES, 'dob' ) );
		$this->assertSame( 'confirm', Sanitizer::enum( 'confirm', Sanitizer::VERIFICATION_MODES, 'dob' ) );
	}

	public function test_hex_color_accepts_3_and_6_digit_hex() {
		$this->assertSame( '#abc', Sanitizer::hex_color( '#abc' ) );
		$this->assertSame( '#aabbcc', Sanitizer::hex_color( '#aabbcc' ) );
		$this->assertSame( '', Sanitizer::hex_color( 'red' ) );
		$this->assertSame( '', Sanitizer::hex_color( '#zzz' ) );
	}

	public function test_slug_strips_invalid_chars() {
		$this->assertSame( 'hoay_verified', Sanitizer::slug( 'HOAY_verified' ) );
		$this->assertSame( 'foo-bar_1', Sanitizer::slug( 'Foo-Bar_1!' ) );
	}

	public function test_css_strips_angle_brackets_and_dangerous_keywords() {
		$out = Sanitizer::css( "<script>alert(1)</script> color: javascript:foo;" );
		$this->assertStringNotContainsString( '<', $out );
		$this->assertStringNotContainsString( '>', $out );
		$this->assertStringNotContainsString( 'javascript:', $out );
	}

	public function test_path_list_normalises_lines_and_adds_leading_slash() {
		$input  = "about\n/contact\n   \n/privacy?x=1";
		$output = Sanitizer::path_list( $input );
		$lines  = explode( "\n", $output );
		$this->assertSame( array( '/about', '/contact', '/privacyx1' ), $lines );
	}

	public function test_attachment_id_returns_zero_for_negative_or_garbage() {
		$this->assertSame( 0, Sanitizer::attachment_id( -1 ) );
		$this->assertSame( 0, Sanitizer::attachment_id( 'abc' ) );
		$this->assertSame( 42, Sanitizer::attachment_id( '42' ) );
	}

	public function test_text_strips_html_when_wp_unavailable() {
		$this->assertSame( 'hello', Sanitizer::text( '  <b>hello</b>  ' ) );
	}
}
