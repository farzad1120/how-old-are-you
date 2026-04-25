<?php
/**
 * Main plugin orchestrator.
 *
 * @package HOAY
 */

namespace HOAY;

defined( 'ABSPATH' ) || exit;

/**
 * Singleton that wires every component together.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Whether `boot()` has already executed.
	 *
	 * @var bool
	 */
	private $booted = false;

	/**
	 * Get (and lazily create) the singleton.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor — use {@see instance()}.
	 */
	private function __construct() {}

	/**
	 * Wire WordPress hooks. Called once on `plugins_loaded`.
	 *
	 * @return void
	 */
	public function boot() {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		add_action( 'admin_init', array( $this, 'register_privacy_policy_content' ) );

		if ( is_admin() ) {
			( new Settings\SettingsPage() )->register();
		} else {
			( new Frontend\Gate() )->register();
		}

		( new Verification\AjaxHandler() )->register();
	}

	/**
	 * Register a suggested privacy-policy paragraph.
	 *
	 * Sites can include this paragraph on their privacy policy page via
	 * Tools → Privacy → How Old Are You.
	 *
	 * @return void
	 */
	public function register_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content  = '<p>' . esc_html__( 'This site uses an age-verification gate. To remember that you have confirmed your age, the plugin sets a single cookie in your browser the first time you pass the gate.', 'how-old-are-you' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'What is stored:', 'how-old-are-you' ) . '</strong> ' . esc_html__( 'a short, signed token containing the cookie\'s expiry timestamp and an HMAC of that timestamp keyed off the site\'s authentication salt. No personally identifiable information is stored — the date of birth you may have entered is used only to compute your age once and is then discarded.', 'how-old-are-you' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Where it is stored:', 'how-old-are-you' ) . '</strong> ' . esc_html__( 'in your browser only. Nothing is stored on the server about individual visitors.', 'how-old-are-you' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'How long it is kept:', 'how-old-are-you' ) . '</strong> ' . esc_html__( 'the cookie expires after the duration the site administrator has configured (30 days by default). You can clear it any time from your browser.', 'how-old-are-you' ) . '</p>';
		$content .= '<p><strong>' . esc_html__( 'Sharing:', 'how-old-are-you' ) . '</strong> ' . esc_html__( 'the cookie is set with HttpOnly and is never transmitted to any third party.', 'how-old-are-you' ) . '</p>';

		wp_add_privacy_policy_content(
			__( 'How Old Are You', 'how-old-are-you' ),
			wp_kses_post( $content )
		);
	}
}
