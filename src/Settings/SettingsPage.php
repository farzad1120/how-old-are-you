<?php
/**
 * Admin settings page (Settings API).
 *
 * @package HOAY
 */

namespace HOAY\Settings;

use HOAY\Support\Sanitizer;
use HOAY\Support\Template;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the settings group, fields, and admin menu entry.
 */
final class SettingsPage {

	/**
	 * Settings group name (used in `settings_fields()`).
	 */
	const GROUP = 'hoay_settings_group';

	/**
	 * Page slug under `options-general.php?page=...`.
	 */
	const PAGE_SLUG = 'how-old-are-you';

	/**
	 * Wire WordPress hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add a Settings submenu entry.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_options_page(
			__( 'Age Verification', 'how-old-are-you' ),
			__( 'Age Verification', 'how-old-are-you' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render' )
		);
	}

	/**
	 * Register the single option key and tell core how to sanitise it.
	 *
	 * @return void
	 */
	public function register_setting() {
		register_setting(
			self::GROUP,
			Options::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => Options::defaults(),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Enqueue admin CSS/JS only on this settings page.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();

		wp_enqueue_style(
			'hoay-admin',
			HOAY_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			HOAY_VERSION
		);

		wp_enqueue_script(
			'hoay-admin',
			HOAY_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			HOAY_VERSION,
			true
		);

		wp_localize_script(
			'hoay-admin',
			'HOAY_ADMIN',
			array(
				'mediaTitle'  => __( 'Choose a verification screen logo', 'how-old-are-you' ),
				'mediaButton' => __( 'Use this image', 'how-old-are-you' ),
				'removeLabel' => __( 'Remove', 'how-old-are-you' ),
			)
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		Template::render(
			'admin/settings-page.php',
			array(
				'options'   => Options::all(),
				'group'     => self::GROUP,
				'page_slug' => self::PAGE_SLUG,
			)
		);
	}

	/**
	 * Sanitise the entire submitted settings array.
	 *
	 * @param array<string,mixed>|mixed $input Raw POST data for the option.
	 * @return array<string,mixed>
	 */
	public function sanitize( $input ) {
		$defaults = Options::defaults();
		if ( ! is_array( $input ) ) {
			$input = array();
		}

		$out = array();

		$out['enabled']           = Sanitizer::bool( isset( $input['enabled'] ) ? $input['enabled'] : false );
		$out['minimum_age']       = Sanitizer::int_range( isset( $input['minimum_age'] ) ? $input['minimum_age'] : $defaults['minimum_age'], 1, 120 );
		$out['verification_mode'] = Sanitizer::enum( isset( $input['verification_mode'] ) ? $input['verification_mode'] : $defaults['verification_mode'], Sanitizer::VERIFICATION_MODES, 'dob' );
		$out['cookie_name']       = Sanitizer::slug( isset( $input['cookie_name'] ) ? $input['cookie_name'] : $defaults['cookie_name'] );
		if ( '' === $out['cookie_name'] ) {
			$out['cookie_name'] = $defaults['cookie_name'];
		}
		$out['cookie_lifetime_days'] = Sanitizer::int_range( isset( $input['cookie_lifetime_days'] ) ? $input['cookie_lifetime_days'] : $defaults['cookie_lifetime_days'], 1, 365 );
		$out['cookie_same_site']     = Sanitizer::enum( isset( $input['cookie_same_site'] ) ? $input['cookie_same_site'] : $defaults['cookie_same_site'], Sanitizer::SAME_SITE_VALUES, 'Lax' );

		$out['heading_text']      = Sanitizer::text( isset( $input['heading_text'] ) ? $input['heading_text'] : $defaults['heading_text'] );
		$out['body_text']         = Sanitizer::textarea( isset( $input['body_text'] ) ? $input['body_text'] : $defaults['body_text'] );
		$out['dob_label']         = Sanitizer::text( isset( $input['dob_label'] ) ? $input['dob_label'] : $defaults['dob_label'] );
		$out['confirm_yes_label'] = Sanitizer::text( isset( $input['confirm_yes_label'] ) ? $input['confirm_yes_label'] : $defaults['confirm_yes_label'] );
		$out['confirm_no_label']  = Sanitizer::text( isset( $input['confirm_no_label'] ) ? $input['confirm_no_label'] : $defaults['confirm_no_label'] );
		$out['submit_label']      = Sanitizer::text( isset( $input['submit_label'] ) ? $input['submit_label'] : $defaults['submit_label'] );
		$out['rejection_heading'] = Sanitizer::text( isset( $input['rejection_heading'] ) ? $input['rejection_heading'] : $defaults['rejection_heading'] );
		$out['rejection_body']    = Sanitizer::textarea( isset( $input['rejection_body'] ) ? $input['rejection_body'] : $defaults['rejection_body'] );

		$out['logo_attachment_id'] = Sanitizer::attachment_id( isset( $input['logo_attachment_id'] ) ? $input['logo_attachment_id'] : 0 );
		$out['logo_max_width_px']  = Sanitizer::int_range( isset( $input['logo_max_width_px'] ) ? $input['logo_max_width_px'] : $defaults['logo_max_width_px'], 40, 400 );

		$out['background_color'] = Sanitizer::hex_color( isset( $input['background_color'] ) ? $input['background_color'] : $defaults['background_color'] );
		if ( '' === $out['background_color'] ) {
			$out['background_color'] = $defaults['background_color'];
		}
		$out['background_image_id']   = Sanitizer::attachment_id( isset( $input['background_image_id'] ) ? $input['background_image_id'] : 0 );
		$out['background_image_size'] = Sanitizer::enum( isset( $input['background_image_size'] ) ? $input['background_image_size'] : $defaults['background_image_size'], Sanitizer::BACKGROUND_SIZES, 'cover' );

		$out['overlay_opacity']  = Sanitizer::float_range( isset( $input['overlay_opacity'] ) ? $input['overlay_opacity'] : $defaults['overlay_opacity'], 0.0, 1.0 );
		$out['backdrop_blur_px'] = Sanitizer::int_range( isset( $input['backdrop_blur_px'] ) ? $input['backdrop_blur_px'] : $defaults['backdrop_blur_px'], 0, 32 );

		$out['panel_color'] = Sanitizer::hex_color( isset( $input['panel_color'] ) ? $input['panel_color'] : $defaults['panel_color'] );
		if ( '' === $out['panel_color'] ) {
			$out['panel_color'] = $defaults['panel_color'];
		}
		$out['panel_width_px']   = Sanitizer::int_range( isset( $input['panel_width_px'] ) ? $input['panel_width_px'] : $defaults['panel_width_px'], 320, 720 );
		$out['panel_padding_px'] = Sanitizer::int_range( isset( $input['panel_padding_px'] ) ? $input['panel_padding_px'] : $defaults['panel_padding_px'], 16, 64 );
		$out['panel_radius_px']  = Sanitizer::int_range( isset( $input['panel_radius_px'] ) ? $input['panel_radius_px'] : $defaults['panel_radius_px'], 0, 32 );

		$out['text_color'] = Sanitizer::hex_color( isset( $input['text_color'] ) ? $input['text_color'] : $defaults['text_color'] );
		if ( '' === $out['text_color'] ) {
			$out['text_color'] = $defaults['text_color'];
		}
		$out['text_align'] = Sanitizer::enum( isset( $input['text_align'] ) ? $input['text_align'] : $defaults['text_align'], Sanitizer::TEXT_ALIGNMENTS, 'center' );

		$out['accent_color'] = Sanitizer::hex_color( isset( $input['accent_color'] ) ? $input['accent_color'] : $defaults['accent_color'] );
		if ( '' === $out['accent_color'] ) {
			$out['accent_color'] = $defaults['accent_color'];
		}

		$out['font_family']       = Sanitizer::font_family( isset( $input['font_family'] ) ? $input['font_family'] : '' );
		$out['font_size_base_px'] = Sanitizer::int_range( isset( $input['font_size_base_px'] ) ? $input['font_size_base_px'] : $defaults['font_size_base_px'], 12, 24 );
		$out['heading_size_px']   = Sanitizer::int_range( isset( $input['heading_size_px'] ) ? $input['heading_size_px'] : $defaults['heading_size_px'], 16, 48 );

		$out['button_radius_px'] = Sanitizer::int_range( isset( $input['button_radius_px'] ) ? $input['button_radius_px'] : $defaults['button_radius_px'], 0, 32 );
		$out['input_radius_px']  = Sanitizer::int_range( isset( $input['input_radius_px'] ) ? $input['input_radius_px'] : $defaults['input_radius_px'], 0, 32 );

		$out['dob_input_style'] = Sanitizer::enum( isset( $input['dob_input_style'] ) ? $input['dob_input_style'] : $defaults['dob_input_style'], Sanitizer::DOB_INPUT_STYLES, 'native' );

		$out['custom_css']     = Sanitizer::css( isset( $input['custom_css'] ) ? $input['custom_css'] : '' );
		$out['excluded_paths'] = Sanitizer::path_list( isset( $input['excluded_paths'] ) ? $input['excluded_paths'] : '' );

		$out['seo_bot_bypass']           = Sanitizer::bool( isset( $input['seo_bot_bypass'] ) ? $input['seo_bot_bypass'] : false );
		$out['seo_bot_user_agents']      = Sanitizer::user_agent_list( isset( $input['seo_bot_user_agents'] ) ? $input['seo_bot_user_agents'] : '' );
		$out['seo_robots_meta']          = Sanitizer::robots_meta( isset( $input['seo_robots_meta'] ) ? $input['seo_robots_meta'] : $defaults['seo_robots_meta'] );
		if ( '' === $out['seo_robots_meta'] ) {
			$out['seo_robots_meta'] = $defaults['seo_robots_meta'];
		}
		$out['seo_canonical_to_request'] = Sanitizer::bool( isset( $input['seo_canonical_to_request'] ) ? $input['seo_canonical_to_request'] : false );
		$out['seo_inherit_open_graph']   = Sanitizer::bool( isset( $input['seo_inherit_open_graph'] ) ? $input['seo_inherit_open_graph'] : false );
		$out['seo_meta_description']     = Sanitizer::textarea( isset( $input['seo_meta_description'] ) ? $input['seo_meta_description'] : '' );
		$out['seo_og_image_id']          = Sanitizer::attachment_id( isset( $input['seo_og_image_id'] ) ? $input['seo_og_image_id'] : 0 );

		return $out;
	}
}
