<?php
/**
 * Plugin Name:       How Old Are You
 * Plugin URI:        https://github.com/farzad1120/how-old-are-you
 * Description:       Block under-age visitors from the public frontend with a customizable age-verification gate. Supports date-of-birth or simple confirmation modes, signed cookies, and full theming.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Farzad Zarasvand
 * Author URI:        https://github.com/farzad1120
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:       how-old-are-you
 * Domain Path:       /languages
 *
 * @package HOAY
 */

defined( 'ABSPATH' ) || exit;

define( 'HOAY_VERSION', '1.0.0' );
define( 'HOAY_PLUGIN_FILE', __FILE__ );
define( 'HOAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HOAY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HOAY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

$hoay_autoload = HOAY_PLUGIN_DIR . 'vendor/autoload.php';
if ( is_readable( $hoay_autoload ) ) {
	require_once $hoay_autoload;
} else {
	require_once HOAY_PLUGIN_DIR . 'src/Autoloader.php';
	\HOAY\Autoloader::register();
}

register_activation_hook( __FILE__, array( '\HOAY\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\HOAY\Deactivator', 'deactivate' ) );

add_action(
	'plugins_loaded',
	static function () {
		\HOAY\Plugin::instance()->boot();
	}
);
