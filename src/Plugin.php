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

		add_action( 'init', array( $this, 'load_textdomain' ) );

		if ( is_admin() ) {
			( new Settings\SettingsPage() )->register();
		} else {
			( new Frontend\Gate() )->register();
		}

		( new Verification\AjaxHandler() )->register();
	}

	/**
	 * Load translations from `languages/`.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'how-old-are-you',
			false,
			dirname( HOAY_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
