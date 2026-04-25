<?php
/**
 * Unit tests for Renderer::css_variables.
 *
 * Each test exercises one admin-configurable theming setting and asserts
 * that the corresponding CSS custom property is present in the output
 * with the right value and **without** any HTML entity encoding (the
 * historic bug — CSS inside `<style>` is raw text, so entity-encoding
 * `"` to `&quot;` breaks bg-image URLs and quoted font stacks).
 *
 * @package HOAY\Tests
 */

namespace HOAY\Tests\Unit\Frontend;

use Brain\Monkey;
use HOAY\Frontend\Renderer;
use HOAY\Settings\Options;
use PHPUnit\Framework\TestCase;

/**
 * @covers \HOAY\Frontend\Renderer::css_variables
 */
final class RendererCssVariablesTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		Monkey\Functions\stubs(
			array(
				'wp_get_attachment_image_url' => static function ( $id, $size = '' ) {
					return 'https://example.test/wp-content/uploads/cover.jpg';
				},
				'esc_url_raw'                 => static function ( $url ) {
					return $url;
				},
			)
		);
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Build a settings array starting from defaults and overriding a few keys.
	 *
	 * @param array<string,mixed> $overrides Keys to override.
	 * @return array<string,mixed>
	 */
	private function options( array $overrides = array() ) {
		return array_merge( Options::defaults(), $overrides );
	}

	public function test_background_color_appears_verbatim() {
		$css = Renderer::css_variables( $this->options( array( 'background_color' => '#abcdef' ) ) );
		$this->assertStringContainsString( '--hoay-bg: #abcdef;', $css );
	}

	public function test_background_image_url_uses_real_double_quotes_not_entities() {
		$css = Renderer::css_variables( $this->options( array( 'background_image_id' => 42 ) ) );
		$this->assertStringContainsString( '--hoay-bg-image: url("https://example.test/wp-content/uploads/cover.jpg");', $css );
		$this->assertStringNotContainsString( '&quot;', $css );
	}

	public function test_background_image_none_when_id_zero() {
		$css = Renderer::css_variables( $this->options() );
		$this->assertStringContainsString( '--hoay-bg-image: none;', $css );
	}

	public function test_background_image_size_enum_propagates() {
		$css = Renderer::css_variables( $this->options( array( 'background_image_size' => 'contain' ) ) );
		$this->assertStringContainsString( '--hoay-bg-size: contain;', $css );
	}

	public function test_overlay_opacity_propagates_as_decimal_string() {
		$css = Renderer::css_variables( $this->options( array( 'overlay_opacity' => 0.42 ) ) );
		$this->assertMatchesRegularExpression( '/--hoay-opacity: 0?\.42;/', $css );
	}

	public function test_backdrop_blur_emits_px_unit() {
		$css = Renderer::css_variables( $this->options( array( 'backdrop_blur_px' => 14 ) ) );
		$this->assertStringContainsString( '--hoay-blur: 14px;', $css );
	}

	public function test_panel_color_propagates() {
		$css = Renderer::css_variables( $this->options( array( 'panel_color' => '#222222' ) ) );
		$this->assertStringContainsString( '--hoay-panel: #222222;', $css );
	}

	public function test_panel_width_padding_radius_emit_px() {
		$css = Renderer::css_variables(
			$this->options(
				array(
					'panel_width_px'   => 520,
					'panel_padding_px' => 48,
					'panel_radius_px'  => 24,
				)
			)
		);
		$this->assertStringContainsString( '--hoay-panel-width: 520px;', $css );
		$this->assertStringContainsString( '--hoay-panel-padding: 48px;', $css );
		$this->assertStringContainsString( '--hoay-panel-radius: 24px;', $css );
	}

	public function test_text_color_and_alignment_propagate() {
		$css = Renderer::css_variables(
			$this->options(
				array(
					'text_color' => '#333333',
					'text_align' => 'left',
				)
			)
		);
		$this->assertStringContainsString( '--hoay-text: #333333;', $css );
		$this->assertStringContainsString( '--hoay-text-align: left;', $css );
	}

	public function test_accent_color_propagates() {
		$css = Renderer::css_variables( $this->options( array( 'accent_color' => '#ff8800' ) ) );
		$this->assertStringContainsString( '--hoay-accent: #ff8800;', $css );
	}

	public function test_custom_font_family_is_emitted_with_real_quotes() {
		$css = Renderer::css_variables( $this->options( array( 'font_family' => '"Inter", system-ui, sans-serif' ) ) );
		$this->assertStringContainsString( '--hoay-font: "Inter", system-ui, sans-serif;', $css );
		$this->assertStringNotContainsString( '&quot;', $css );
	}

	public function test_default_font_stack_is_emitted_when_setting_empty() {
		$css = Renderer::css_variables( $this->options( array( 'font_family' => '' ) ) );
		$this->assertStringContainsString( '"Segoe UI"', $css );
		$this->assertStringContainsString( '"Helvetica Neue"', $css );
		$this->assertStringNotContainsString( '&quot;', $css );
	}

	public function test_font_sizes_emit_px() {
		$css = Renderer::css_variables(
			$this->options(
				array(
					'font_size_base_px' => 18,
					'heading_size_px'   => 32,
				)
			)
		);
		$this->assertStringContainsString( '--hoay-font-size: 18px;', $css );
		$this->assertStringContainsString( '--hoay-heading-size: 32px;', $css );
	}

	public function test_button_and_input_radii_emit_px() {
		$css = Renderer::css_variables(
			$this->options(
				array(
					'button_radius_px' => 4,
					'input_radius_px'  => 0,
				)
			)
		);
		$this->assertStringContainsString( '--hoay-button-radius: 4px;', $css );
		$this->assertStringContainsString( '--hoay-input-radius: 0px;', $css );
	}

	public function test_logo_max_width_emits_px() {
		$css = Renderer::css_variables( $this->options( array( 'logo_max_width_px' => 220 ) ) );
		$this->assertStringContainsString( '--hoay-logo-max: 220px;', $css );
	}

	public function test_output_contains_no_html_entities_for_any_default_settings() {
		$css = Renderer::css_variables( $this->options() );
		$this->assertStringNotContainsString( '&quot;', $css );
		$this->assertStringNotContainsString( '&#039;', $css );
		$this->assertStringNotContainsString( '&amp;', $css );
		$this->assertStringNotContainsString( '&lt;', $css );
		$this->assertStringNotContainsString( '&gt;', $css );
	}

	public function test_output_never_contains_closing_style_tag() {
		// Even if a malicious value made it in, the template's str_replace
		// scrub would catch it. Verify css_variables itself doesn't produce one.
		$css = Renderer::css_variables(
			$this->options(
				array(
					'background_color' => '#000000',
					'panel_color'      => '#ffffff',
					'font_family'      => '"Helvetica", sans-serif',
				)
			)
		);
		$this->assertStringNotContainsString( '</style', $css );
	}
}
